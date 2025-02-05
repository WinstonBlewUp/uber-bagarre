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
        $fighters = $entityManager->getConnection()->fetchAllAssociative("
            SELECT * FROM \"user\" WHERE roles::jsonb @> '[\"ROLE_BAGARREUR\"]'::jsonb ORDER BY score DESC LIMIT 3
        ");


        return $this->render('home.html.twig', [
            'annonces' => $annonces,
            'fighters' => $fighters,
        ]);
    }
}
