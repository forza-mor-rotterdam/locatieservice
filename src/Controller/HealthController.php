<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ImportActionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HealthController extends AbstractController
{
    #[Route('/health')]
    public function health(Request $request, ImportActionRepository $importActionRepository): Response
    {
        $importActionRepository->findOneBy(['id' => 0]);

        return new Response('DB connection: OK');
    }

    #[Route('/healthz')]
    public function healthz(Request $request): Response
    {
        return new Response('OK');
    }
}