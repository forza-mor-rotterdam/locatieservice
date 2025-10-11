<?php

declare(strict_types=1);

namespace App\Controller\ApiV1;

use App\Entity\Woonplaats;
use App\Repository\WoonplaatsRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/v1/woonplaats')]
class WoonplaatsController extends AbstractController
{
    #[Route('')]
    public function index(Request $request, WoonplaatsRepository $woonplaatsRepository, NormalizerInterface $normalizer): Response
    {
        $woonplaatsen = $woonplaatsRepository->findAll();

        return new JsonResponse([
            '_links' => [],
            'count' => count($woonplaatsen),
            'results' => $normalizer->normalize($woonplaatsen, 'json', [])
        ]);
    }

    #[Route('/{uuid}')]
    public function detail(Request $request, #[MapEntity(id: 'uuid')] Woonplaats $woonplaats, NormalizerInterface $normalizer): Response
    {
        return new JsonResponse(
            $normalizer->normalize($woonplaats, 'json', [])
        );
    }
}