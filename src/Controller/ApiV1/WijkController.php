<?php

declare(strict_types=1);

namespace App\Controller\ApiV1;

use App\Entity\Wijk;
use App\Repository\WijkRepository;
use App\Repository\WoonplaatsRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/v1/wijk')]
class WijkController extends AbstractController
{
    #[Route('')]
    public function index(Request $request, WoonplaatsRepository $woonplaatsRepository, WijkRepository $wijkRepository, NormalizerInterface $normalizer): Response
    {
        $wijken = [];

        if ($request->query->has('woonplaats')) {
            $woonplaats = $woonplaatsRepository->find($request->query->get('woonplaats'));
            if ($woonplaats === null) {
                throw $this->createNotFoundException('Woonplaats not found');
            }
            $wijken = $wijkRepository->findBy(['woonplaats' => $woonplaats]);
        } else {
            $wijken = $wijkRepository->findAll();
        }

        return new JsonResponse([
            '_links' => [],
            'count' => count($wijken),
            'results' => $normalizer->normalize($wijken, 'json', [])
        ]);
    }

    #[Route('/{uuid}')]
    public function detail(Request $request, #[MapEntity(id: 'uuid')] Wijk $wijk, NormalizerInterface $normalizer): Response
    {
        return new JsonResponse(
            $normalizer->normalize($wijk, 'json', [])
        );
    }
}