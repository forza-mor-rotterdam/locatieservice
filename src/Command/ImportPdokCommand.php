<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Buurt;
use App\Entity\ImportAction;
use App\Entity\TopografischeEenheid;
use App\Entity\Wijk;
use App\Entity\Woonplaats;
use App\Repository\BuurtRepository;
use App\Repository\WijkRepository;
use App\Repository\WoonplaatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:import:pdok',
    description: 'Import woonplaatsen/wijken/buurten from PDOK',
)]
class ImportPdokCommand extends Command
{
    public function __construct(
        private HttpClientInterface $pdokClient,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
        private WoonplaatsRepository $woonplaatsRepository,
        private WijkRepository $wijkRepository,
        private BuurtRepository $buurtRepository
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $importAction = new ImportAction('pdok');
        $this->em->persist($importAction);
        $this->em->flush();

        $pageSize = 20;

        $pdokWoonplaatsen = [];
        $pdokWijken = [];
        $pdokBuurten = [];

        // download woonplaatsen
        foreach ($this->loopResultPages('GET', '/bzk/locatieserver/search/v3_1/free', [
            'query' => [
                'fq' => 'bron:BAG AND type:woonplaats AND gemeentenaam:Rotterdam',
                'wt' => 'json'
            ]
        ], $pageSize, 5) as $doc) {
            $output->writeln('Pdok found woonplaats: ' . $doc['woonplaatsnaam'] . ' (' . $doc['identificatie'] . ')');
            $pdokWoonplaatsen[$doc['identificatie']] = [
                'code' => $doc['identificatie'],
                'naam' => $doc['woonplaatsnaam'],
                'geo_center' => $doc['centroide_ll'],
                'gemeente_code' => $doc['gemeentecode'],
                'gemeente_naam' => $doc['gemeentenaam']
            ];
        }

        // download wijken
        foreach ($this->loopResultPages('GET', '/bzk/locatieserver/search/v3_1/free', [
            'query' => [
                'fq' => 'bron:CBS AND type:wijk AND gemeentenaam:Rotterdam',
                'wt' => 'json'
            ]
        ], $pageSize, 5) as $doc) {
            $output->writeln('Pdok found wijk: ' . $doc['wijknaam'] . ' (' . $doc['identificatie'] . ')');
            $pdokWijken[$doc['identificatie']] = [
                'code' => $doc['identificatie'],
                'naam' => $doc['wijknaam'],
                'geo_center' => $doc['centroide_ll'],
                'gemeente_code' => $doc['gemeentecode'],
                'gemeente_naam' => $doc['gemeentenaam']
            ];

            // zoek woonplaats bij wijk (deze data is niet in bron CBS beschikbaar)
            preg_match('/^POINT\((?P<x>\d+\.?\d*) (?P<y>\d+\.?\d*)\)$/', $doc['centroide_ll'], $coords);
            $woonplaats = $this->pdokClient->request('GET', '/bzk/locatieserver/search/v3_1/reverse', [
                'query' => [
                    'lat' => $coords['y'],
                    'lon' => $coords['x'],
                    'type' => 'woonplaats',
                    'fl' => 'woonplaatscode woonplaatsnaam gemeentecode',
                    'start' => 0,
                    'rows' => 1
                ]
            ])->toArray()['response']['docs'][0];

            if ($woonplaats['gemeentecode'] === $doc['gemeentecode']) {
                $pdokWijken[$doc['identificatie']]['woonplaats_code'] = $woonplaats['woonplaatscode'];
                $pdokWijken[$doc['identificatie']]['woonplaats_naam'] = $woonplaats['woonplaatsnaam'];
            }
        }

        // download buurten
        foreach ($this->loopResultPages('GET', '/bzk/locatieserver/search/v3_1/free', [
            'query' => [
                'fq' => 'bron:CBS AND type:buurt AND gemeentenaam:Rotterdam',
                'wt' => 'json'
            ]
        ], $pageSize, 5) as $doc) {
            $output->writeln('Pdok found buurt: ' . $doc['buurtnaam'] . ' (' . $doc['identificatie'] . ')');
            $pdokBuurten[$doc['identificatie']] = [
                'code' => $doc['identificatie'],
                'naam' => $doc['buurtnaam'],
                'geo_center' => $doc['centroide_ll'],
                'gemeente_code' => $doc['gemeentecode'],
                'gemeente_naam' => $doc['gemeentenaam'],
                'wijk_code' => $doc['wijkcode'],
                'wijk_naam' => $doc['wijknaam']
            ];
        }

        // load all objects from database
        $woonplaatsen = $this->woonplaatsRepository->findAllAsLookupArray();
        $wijken = $this->wijkRepository->findAllAsLookupArray();
        $buurten = $this->buurtRepository->findAllAsLookupArray();

        $this->em->beginTransaction();

        // set pdok status to false for all objects
        foreach ($woonplaatsen as $woonplaats) {
            assert($woonplaats instanceof Woonplaats);
            $woonplaats->setInPdok(false);
        }
        foreach ($wijken as $wijk) {
            assert($wijk instanceof Wijk);
            $wijk->setInPdok(false);
        }
        foreach ($buurten as $buurt) {
            assert($buurt instanceof Buurt);
            $buurt->setInPdok(false);
        }

        // update all woonplaatsen
        foreach ($pdokWoonplaatsen as $pdokWoonplaats) {
            $matchableNaam = TopografischeEenheid::createMatchableString($pdokWoonplaats['naam']);

            $woonplaats = null;
            if (isset($woonplaatsen[$matchableNaam])) {
                $woonplaats = $woonplaatsen[$matchableNaam];
            } else {
                $woonplaats = new Woonplaats();
                $this->em->persist($woonplaats);
                $woonplaatsen[$matchableNaam] = $woonplaats;
            }
            assert($woonplaats instanceof Woonplaats);

            $woonplaats->setCode($pdokWoonplaats['code']);
            $woonplaats->setGemeenteCode($pdokWoonplaats['gemeente_code']);
            $woonplaats->setGemeenteNaam($pdokWoonplaats['gemeente_naam']);
            $woonplaats->setGeoCenter($pdokWoonplaats['geo_center']);
            $woonplaats->setInPdok(true);
            $woonplaats->setLastSeenInPdok(new \DateTimeImmutable());
            $woonplaats->setNaam($pdokWoonplaats['naam']);
        }

        // update all wijken
        foreach ($pdokWijken as $pdokWijk) {
            $matchableNaam = TopografischeEenheid::createMatchableString($pdokWijk['naam']);

            $wijk = null;
            if (isset($wijken[$matchableNaam])) {
                $wijk = $wijken[$matchableNaam];
            } else {
                $wijk = new Wijk();
                $this->em->persist($wijk);
                $wijken[$matchableNaam] = $wijk;
            }
            assert($wijk instanceof Wijk);

            $wijk->setCode($pdokWijk['code']);
            $wijk->setGemeenteCode($pdokWijk['gemeente_code']);
            $wijk->setGemeenteNaam($pdokWijk['gemeente_naam']);
            $wijk->setGeoCenter($pdokWijk['geo_center']);
            $wijk->setInPdok(true);
            $wijk->setLastSeenInPdok(new \DateTimeImmutable());
            $wijk->setNaam($pdokWijk['naam']);

            $matchableNaamWoonplaats = TopografischeEenheid::createMatchableString($pdokWijk['woonplaats_naam']);
            if (isset($woonplaatsen[$matchableNaamWoonplaats])) {
                $woonplaats = $woonplaatsen[$matchableNaamWoonplaats];
                assert($woonplaats instanceof Woonplaats);

                $wijk->setWoonplaats($woonplaats);
            }
        }

        // update all buurten
        foreach ($pdokBuurten as $pdokBuurt) {
            $matchableNaam = TopografischeEenheid::createMatchableString($pdokBuurt['naam']);

            $buurt = null;
            if (isset($buurten[$matchableNaam])) {
                $buurt = $buurten[$matchableNaam];
            } else {
                $buurt = new Buurt();
                $this->em->persist($buurt);
                $buurten[$matchableNaam] = $buurt;
            }
            assert($buurt instanceof Buurt);

            $buurt->setCode($pdokBuurt['code']);
            $buurt->setGemeenteCode($pdokBuurt['gemeente_code']);
            $buurt->setGemeenteNaam($pdokBuurt['gemeente_naam']);
            $buurt->setGeoCenter($pdokBuurt['geo_center']);
            $buurt->setInPdok(true);
            $buurt->setLastSeenInPdok(new \DateTimeImmutable());
            $buurt->setNaam($pdokBuurt['naam']);

            $matchableNaamWijk = TopografischeEenheid::createMatchableString($pdokBuurt['wijk_naam']);
            if (isset($wijken[$matchableNaamWijk])) {
                $wijk = $wijken[$matchableNaamWijk];
                assert($wijk instanceof Wijk);

                $buurt->setWijk($wijk);
                $buurt->setWoonplaats($wijk->getWoonplaats());
            }
        }

        $importAction->setFinish(new \DateTimeImmutable());
        $importAction->setSuccess(true);

        $this->em->flush();

        $this->em->commit();

        return Command::SUCCESS;
    }

    protected function loopResultPages(string $method, string $url, array $options, int $pageSize = 10, int $limitPageCount = 100): array
    {
        $docs = [];
        if (isset($options['query']) === false) {
            $options['query'] = [];
        }

        $i = 0;
        do {
            // set limit and offset
            $options['query']['start'] = $i * $pageSize;
            $options['query']['rows'] = $pageSize;

            // make the call
            $results = $this->pdokClient->request($method, $url, $options)->toArray();

            // add all docs from this response to the returing array
            foreach ($results['response']['docs'] as $doc) {
                $docs[] = $doc;
            }

            // break the loop when no results are given
            if (count($results['response']['docs']) === 0) {
                break;
            }

            // time for the next page, make sure we don't create an infinitif loop
            $i++;
            if ($i > $limitPageCount) {
                throw new \RuntimeException('Loop is more than ' . $limitPageCount . ', operation canceld.');
            }
        } while (true);

        return $docs;
    }
}
