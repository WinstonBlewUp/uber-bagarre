<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $annonces = $entityManager->getRepository(Annonce::class)->findBy([], ['date' => 'DESC'], 3);
        $fighters = $entityManager->getRepository(User::class)->findTopFighters();

        $annoncesProches = [];
        if ($this->getUser()) {
            $user = $this->getUser();
            if ($user->getLatitude() !== null && $user->getLongitude() !== null) {
                $annoncesProches = $entityManager->getRepository(Annonce::class)
                    ->findNearbyAnnonces($user->getLatitude(), $user->getLongitude(), 10);
            }
        }

        return $this->render('home.html.twig', [
            'annonces' => $annonces,
            'fighters' => $fighters,
            'annoncesProches' => $annoncesProches,
        ]);
    }
}
