<?php

declare(strict_types=1);

namespace App\Controller\ApiV1;

use App\Entity\Buurt;
use App\Repository\BuurtRepository;
use App\Repository\WijkRepository;
use App\Repository\WoonplaatsRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api/v1/buurt')]
class BuurtController extends AbstractController
{
    #[Route('')]
    public function index(Request $request, WoonplaatsRepository $woonplaatsRepository, WijkRepository $wijkRepository, BuurtRepository $buurtRepository, NormalizerInterface $normalizer): Response
    {
        $buurten = [];

        if ($request->query->has('wijk') && $request->query->has('woonplaats')) {
            return new JsonResponse(['state' => 'INPUT_ERROR', 'message' => 'Not both filter arguments can be given "wijk", "woonplaats"']);
        } elseif ($request->query->has('wijk')) {
            $wijk = $wijkRepository->find($request->query->get('wijk'));
            if ($wijk === null) {
                throw $this->createNotFoundException('Wijk not found');
            }
            $buurten = $buurtRepository->findBy(['wijk' => $wijk]);
        } elseif ($request->query->has('woonplaats')) {
            $woonplaats = $woonplaatsRepository->find($request->query->get('woonplaats'));
            if ($woonplaats === null) {
                throw $this->createNotFoundException('Woonplaats not found');
            }
            $buurten = $buurtRepository->findBy(['woonplaats' => $woonplaats]);
        } else {
            $buurten = $buurtRepository->findAll();
        }

        if ($request->query->get('format') === 'legacy') {
            return new JsonResponse($normalizer->normalize($buurten, 'json', [
                'style' => 'legacy'
            ]));
        }

        return new JsonResponse([
            '_links' => [],
            'count' => count($buurten),
            'results' => $normalizer->normalize($buurten, 'json', [])
        ]);
    }

    #[Route('/{uuid}')]
    public function detail(Request $request, #[MapEntity(id: 'uuid')] Buurt $buurt, NormalizerInterface $normalizer): Response
    {
        return new JsonResponse(
            $normalizer->normalize($buurt, 'json', [])
        );
    }
}