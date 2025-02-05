<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/fighters', name: 'fighters_')]
class FighterController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(Connection $connection): Response
    {
        $fighters = $connection->fetchAllAssociative("
            SELECT * FROM \"user\" WHERE roles::jsonb @> '[\"ROLE_BAGARREUR\"]'::jsonb ORDER BY score DESC
        ");


        return $this->render('fighter/list.html.twig', [
            'fighters' => $fighters,
        ]);
    }
}
