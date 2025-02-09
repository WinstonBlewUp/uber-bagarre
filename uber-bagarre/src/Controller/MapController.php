<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MapController extends AbstractController
{
    #[Route('/map', name: 'map_')]
    public function index(array $markers = []): Response
    {
        return $this->render('components/_map.html.twig', [
            'markers' => $markers
        ]);
    }
}
