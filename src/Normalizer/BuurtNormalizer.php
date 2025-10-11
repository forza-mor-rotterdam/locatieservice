<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Buurt;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BuurtNormalizer implements NormalizerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {
        //
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Buurt;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Buurt::class => true
        ];
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        assert($data instanceof Buurt);

        if (isset($context['style']) && $context['style'] === 'legacy') {
            return [
                'buurtnaam' => $data->getNaam(),
                'wijknaam' => $data->getWijk()->getNaam(),
                'plaatsnaam' => $data->getWoonplaats()->getNaam()
            ];
        }

        $output = [
            '_links' => [
                'self' => $this->urlGenerator->generate('app_apiv1_buurt_detail', ['uuid' => $data->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'uuid' => $data->getUuid(),
            'naam' => $data->getNaam(),
            'code' => $data->getCode(),
            'gemeente' => [
                'code' => $data->getGemeenteCode(),
                'naam' => $data->getGemeenteNaam(),
            ],
            'geo_center' => $data->getGeoCenter(),
            'woonplaats' => null,
            'wijk' => null,
        ];

        if ($data->getWoonplaats()) {
            $output['woonplaats'] = [
                '_links' => [
                    'self' => $this->urlGenerator->generate('app_apiv1_woonplaats_detail', ['uuid' => $data->getWoonplaats()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL)
                ],
                'uuid' => $data->getWoonplaats()->getUuid(),
                'naam' => $data->getWoonplaats()->getNaam(),
            ];
        }

        if ($data->getWijk()) {
            $output['wijk'] = [
                '_links' => [
                    'self' => $this->urlGenerator->generate('app_apiv1_wijk_detail', ['uuid' => $data->getWijk()->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL)
                ],
                'uuid' => $data->getWijk()->getUuid(),
                'naam' => $data->getWijk()->getNaam(),
            ];
        }

        return $output;
    }

}