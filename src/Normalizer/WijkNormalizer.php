<?php

declare(strict_types=1);

namespace App\Normalizer;

use App\Entity\Wijk;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class WijkNormalizer implements NormalizerInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    )
    {
        //
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Wijk;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Wijk::class => true
        ];
    }

    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        assert($data instanceof Wijk);

        $output = [
            '_links' => [
                'self' => $this->urlGenerator->generate('app_apiv1_wijk_detail', ['uuid' => $data->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
                'buurten' => $this->urlGenerator->generate('app_apiv1_buurt_index', ['wijk' => $data->getUuid()], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            'uuid' => $data->getUuid(),
            'naam' => $data->getNaam(),
            'code' => $data->getCode(),
            'gemeente' => [
                'code' => $data->getGemeenteCode(),
                'naam' => $data->getGemeenteNaam(),
            ],
            'geo_center' => $data->getGeoCenter(),
            'woonplaats' => null
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

        return $output;
    }

}