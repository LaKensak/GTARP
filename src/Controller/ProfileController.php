<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur de gestion du profil utilisateur
 */
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    /**
     * Modification du profil utilisateur
     */
    #[Route('/profil', name: 'app_profile')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour !');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }

    /**
     * Liste des participants (accessible aux modérateurs uniquement)
     */
    #[Route('/participants', name: 'app_participants')]
    #[IsGranted('ROLE_MODERATOR')]
    public function listParticipants(UserRepository $userRepository): Response
    {
        $participants = $userRepository->findAllParticipants();

        return $this->render('profile/participants.html.twig', [
            'participants' => $participants,
        ]);
    }
}
