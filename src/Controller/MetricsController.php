<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\BuurtRepository;
use App\Repository\ImportActionRepository;
use App\Repository\WijkRepository;
use App\Repository\WoonplaatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

class MetricsController extends AbstractController
{
    #[Route('/app/metrics')]
    public function __invoke(Request $request, WoonplaatsRepository $woonplaatsRepository, WijkRepository $wijkRepository, BuurtRepository $buurtRepository, ImportActionRepository $importActionRepository): Response
    {
        $metrics = [];

        $metrics['aantal_woonplaats'][] = ['labels' => ['found_in_pdok' => false], 'value' => $woonplaatsRepository->count(['inPdok' => false])];
        $metrics['aantal_woonplaats'][] = ['labels' => ['found_in_pdok' => true], 'value' => $woonplaatsRepository->count(['inPdok' => true])];
        $metrics['aantal_woonplaats'][] = ['labels' => ['found_in_morcore' => false], 'value' => $woonplaatsRepository->count(['inMorCore' => false])];
        $metrics['aantal_woonplaats'][] = ['labels' => ['found_in_morcore' => true], 'value' => $woonplaatsRepository->count(['inMorCore' => true])];

        $metrics['aantal_wijk'][] = ['labels' => ['found_in_pdok' => false], 'value' => $wijkRepository->count(['inPdok' => false])];
        $metrics['aantal_wijk'][] = ['labels' => ['found_in_pdok' => true], 'value' => $wijkRepository->count(['inPdok' => true])];
        $metrics['aantal_wijk'][] = ['labels' => ['found_in_morcore' => false], 'value' => $wijkRepository->count(['inMorCore' => false])];
        $metrics['aantal_wijk'][] = ['labels' => ['found_in_morcore' => true], 'value' => $wijkRepository->count(['inMorCore' => true])];

        $metrics['aantal_buurt'][] = ['labels' => ['found_in_pdok' => false], 'value' => $buurtRepository->count(['inPdok' => false])];
        $metrics['aantal_buurt'][] = ['labels' => ['found_in_pdok' => true], 'value' => $buurtRepository->count(['inPdok' => true])];
        $metrics['aantal_buurt'][] = ['labels' => ['found_in_morcore' => false], 'value' => $buurtRepository->count(['inMorCore' => false])];
        $metrics['aantal_buurt'][] = ['labels' => ['found_in_morcore' => true], 'value' => $buurtRepository->count(['inMorCore' => true])];

        foreach (['pdok', 'mor-core'] as $type) {
            if ($value = $importActionRepository->findOneBy(['type' => $type, 'success' => true], ['start' => 'DESC'])?->getStart()->getTimestamp()) {
                $metrics['time_since_last_import'][] = ['labels' => ['type' => $type, 'success' => true], 'value' => time() - $value];
            }
        }

        return new StreamedResponse(function () use ($metrics) {
            foreach ($metrics as $metricName => $metricSet) {
                foreach ($metricSet as $data) {
                    echo $metricName;
                    echo '{';
                    $labels = [];
                    foreach ($data['labels'] as $name => $value) {
                        if (is_bool($value)) {
                            $value = $value ? 'true' : 'false';
                        }
                        $labels[] = $name . '="' . addslashes($value) . '"';
                    }
                    echo implode(', ', $labels);
                    echo '}';
                    echo ' ';
                    echo $data['value'];
                    echo "\n";
                }
            }
        }, Response::HTTP_OK, [
            'Content-Type' => 'text/plain'
        ]);
    }
}