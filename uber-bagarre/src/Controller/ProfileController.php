<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Annonce;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'profile_')]
class ProfileController extends AbstractController
{
    #[Route('/{id}', name: 'view', requirements: ['id' => '\d+'], defaults: ['id' => null])]
    public function viewProfile(?int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $id ? $entityManager->getRepository(User::class)->find($id) : $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException("Utilisateur introuvable.");
        }
        
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

        return $this->render('profile/view.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
        ]);
    }

    #[Route('/edit', name: 'edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('profile_view');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
