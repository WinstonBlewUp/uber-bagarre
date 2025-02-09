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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\BecomeFighterFormType;
use Symfony\Bundle\SecurityBundle\Security;


#[Route('/profil', name: 'profile_')]
class ProfileController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
            if ($user->getCity()) {
                $geoData = $geoService->getCoordinates($user->getCity());
                if ($geoData) {
                    $user->setLatitude($geoData['lat']);
                    $user->setLongitude($geoData['lng']);
                }
            }
    
            $entityManager->flush();
            return $this->redirectToRoute('profile_view');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/become-fighter', name: 'become_fighter')]
    public function becomeFighter(Request $request, MailerInterface $mailer, EntityManagerInterface $entityManager, Security $security): Response
    {
        $user = $security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(BecomeFighterFormType::class, null, [
            'user' => $user, // Passe l'utilisateur ici
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motivation = $form->get('motivation')->getData();

            $emailUser = (new Email())
                ->from('no-reply@uberbagarre.com')
                ->to($user->getEmail())
                ->subject('Demande de passage en bagarreur en cours')
                ->text('Votre demande pour devenir bagarreur a bien été prise en compte. Un administrateur la validera prochainement.');

            $mailer->send($emailUser);

            $emailAdmin = (new Email())
                ->from('no-reply@uberbagarre.com')
                ->to('playlist.robin@gmail.com')
                ->subject('Nouvelle demande de bagarreur')
                ->text("L'utilisateur {$user->getUsername()} a demandé à devenir bagarreur. Motif : $motivation");

            $mailer->send($emailAdmin);

            $this->addFlash('success', 'Votre demande a été envoyée. Un administrateur la validera sous peu.');

            return $this->redirectToRoute('profile_view', ['id' => $user->getId()]);
        }

        return $this->render('profile/become_fighter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        

}
