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
    name: 'app:import:morcore',
    description: 'Import wijken/buurten from MorCore',
)]
class ImportMorCoreCommand extends Command
{
    public function __construct(
        private HttpClientInterface $morCoreClient,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
        private WoonplaatsRepository $woonplaatsRepository,
        private WijkRepository $wijkRepository,
        private BuurtRepository $buurtRepository,
        private string $morCoreUsername,
        private string $morCorePassword,
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
        $importAction = new ImportAction('mor-core');
        $this->em->persist($importAction);
        $this->em->flush();

        // exchange credentials for token
        $token = $this->morCoreClient->request('POST', '/api-token-auth/', [
            'body' => [
                'username' => $this->morCoreUsername,
                'password' => $this->morCorePassword,
            ]
        ])->toArray()['token'];

        // download all buurten
        $morCoreBuurten = $this->morCoreClient->request('GET', '/api/v1/locatie/buurten', [
            'headers' => [
                'Authorization' => 'Token ' . $token
            ]
        ])->toArray();

        // load all objects from database
        $woonplaatsen = $this->woonplaatsRepository->findAllAsLookupArray();
        $wijken = $this->wijkRepository->findAllAsLookupArray();
        $buurten = $this->buurtRepository->findAllAsLookupArray();

        // start transaction
        $this->em->beginTransaction();

        // set pdok status to false for all objects
        foreach ($woonplaatsen as $woonplaats) {
            assert($woonplaats instanceof Woonplaats);
            $woonplaats->setInMorCore(false);
        }
        foreach ($wijken as $wijk) {
            assert($wijk instanceof Wijk);
            $wijk->setInMorCore(false);
        }
        foreach ($buurten as $buurt) {
            assert($buurt instanceof Buurt);
            $buurt->setInMorCore(false);
        }

        foreach($morCoreBuurten as $morCoreBuurt) {
            // handle woonplaats
            $woonplaatsMatchNaam = TopografischeEenheid::createMatchableString($morCoreBuurt['plaatsnaam']);

            $woonplaats = null;
            if (isset($woonplaatsen[$woonplaatsMatchNaam])) {
                $woonplaats = $woonplaatsen[$woonplaatsMatchNaam];
            } else {
                $woonplaats = new Woonplaats();
                $this->em->persist($woonplaats);
                $woonplaatsen[$woonplaatsMatchNaam] = $woonplaats;
            }
            assert($woonplaats instanceof Woonplaats);

            $woonplaats->setInMorCore(true);
            $woonplaats->setLastSeenInMorCore(new \DateTimeImmutable());
            $woonplaats->setNaam($morCoreBuurt['plaatsnaam']);

            // handle wijk
            $wijkMatchNaam = TopografischeEenheid::createMatchableString($morCoreBuurt['wijknaam']);

            $wijk = null;
            if (isset($wijken[$wijkMatchNaam])) {
                $wijk = $wijken[$wijkMatchNaam];
            } else {
                $wijk = new Wijk();
                $this->em->persist($wijk);
                $wijken[$wijkMatchNaam] = $wijk;
            }
            assert($wijk instanceof Wijk);

            $wijk->setInMorCore(true);
            $wijk->setLastSeenInMorCore(new \DateTimeImmutable());
            $wijk->setNaam($morCoreBuurt['wijknaam']);
            $wijk->setWoonplaats($woonplaats);

            // handle buurt
            $buurtMatchNaam = TopografischeEenheid::createMatchableString($morCoreBuurt['buurtnaam']);

            $buurt = null;
            if (isset($buurten[$buurtMatchNaam])) {
                $buurt = $buurten[$buurtMatchNaam];
            } else {
                $buurt = new Buurt();
                $this->em->persist($buurt);
                $buurten[$buurtMatchNaam] = $buurt;
            }
            assert($buurt instanceof Buurt);

            $buurt->setInMorCore(true);
            $buurt->setLastSeenInMorCore(new \DateTimeImmutable());
            $buurt->setNaam($morCoreBuurt['buurtnaam']);
            $buurt->setWoonplaats($woonplaats);
            $buurt->setWijk($wijk);
        }

        $importAction->setFinish(new \DateTimeImmutable());
        $importAction->setSuccess(true);

        $this->em->flush();

        $this->em->commit();

        return Command::SUCCESS;
    }

}
