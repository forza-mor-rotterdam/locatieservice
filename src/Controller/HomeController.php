<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\WoonplaatsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('')]
    public function __invoke(Request $request, WoonplaatsRepository $woonplaatsRepository)
    {
        return $this->render('home.html.twig', [
            'woonplaatsen' => $woonplaatsRepository->findAll()
        ]);
    }
}