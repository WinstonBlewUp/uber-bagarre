<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\Form\AnnonceFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/annonce', name: 'annonce_')]
class AnnonceController extends AbstractController
{
    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annonce = new Annonce();
        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonce->setCreatedBy($this->getUser());
            $annonce->setDate(new \DateTimeImmutable('now'));
            $entityManager->persist($annonce);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('annonce/new.html.twig', [
            'annonceForm' => $form->createView(),
        ]);
    }

    #[Route('/mes-annonces', name: 'mes_annonces')]
    public function userAnnonces(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $annonces = $entityManager->getRepository(Annonce::class)->findBy(['createdBy' => $user], ['date' => 'DESC']);

        $annoncesAcceptees = [];
        if (in_array('ROLE_BAGARREUR', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) {
            $annoncesAcceptees = $entityManager->getRepository(Annonce::class)->createQueryBuilder('a')
                ->join('a.participants', 'p')
                ->where('p.id = :userId')
                ->setParameter('userId', $user->getId())
                ->orderBy('a.date', 'DESC')
                ->getQuery()
                ->getResult();
        }

        return $this->render('annonce/mine.html.twig', [
            'annonces' => $annonces,
            'annoncesAcceptees' => $annoncesAcceptees,
        ]);
    }


    #[Route('/edit/{id}', name: 'edit')]
    public function editAnnonce(Request $request, EntityManagerInterface $entityManager, Annonce $annonce): Response
    {
        $this->denyAccessUnlessGranted('annonce_edit', $annonce);

        $form = $this->createForm(AnnonceFormType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('annonce_mes_annonces');
        }

        return $this->render('annonce/edit.html.twig', [
            'annonceForm' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function deleteAnnonce(EntityManagerInterface $entityManager, Annonce $annonce): Response
    {
        $this->denyAccessUnlessGranted('annonce_delete', $annonce);

        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->redirectToRoute('annonce_mes_annonces');
    }


    #[Route('/join/{id}', name: 'join')]
    public function joinAnnonce(Annonce $annonce, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        if ($user === $annonce->getCreatedBy()) {
            return $this->redirectToRoute('annonce_list');
        }

        if (!$annonce->getParticipants()->contains($user)) {
            $annonce->addParticipant($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_list');
    }

    #[Route('/validate/{annonceId}/{userId}', name: 'validate')]
    public function validateParticipant(int $annonceId, int $userId, EntityManagerInterface $entityManager): Response
    {
        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$annonce || !$user) {
            throw $this->createNotFoundException();
        }

        $this->denyAccessUnlessGranted('annonce_validate', $annonce);

        if ($annonce->getParticipants()->contains($user)) {
            $annonce->removeParticipant($user);
        } else {
            $annonce->addParticipant($user);
        }

        $entityManager->flush();

        return $this->redirectToRoute('annonce_mes_annonces');
    }



    #[Route('/bagarres', name: 'list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $annonces = $entityManager->getRepository(Annonce::class)->findBy([], ['date' => 'DESC']);

        return $this->render('annonce/list.html.twig', [
            'annonces' => $annonces,
        ]);
    }
}


