<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur pour les endpoints AJAX
 * Utilisé pour le compteur de connectés et autres fonctions asynchrones
 */
#[Route('/api')]
class ApiController extends AbstractController
{
    /**
     * Retourne le nombre d'utilisateurs connectés
     * Appelé toutes les 15 secondes via AJAX (selon le cahier des charges)
     */
    #[Route('/online-count', name: 'api_online_count', methods: ['GET'])]
    public function getOnlineCount(UserRepository $userRepository): JsonResponse
    {
        $count = $userRepository->countOnlineUsers();

        return $this->json([
            'count' => $count,
        ]);
    }

    /**
     * Met à jour l'activité de l'utilisateur connecté
     * Appelé périodiquement pour maintenir le statut "connecté"
     */
    #[Route('/heartbeat', name: 'api_heartbeat', methods: ['POST'])]
    public function heartbeat(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if ($user) {
            // Mise à jour de la dernière activité
            $user->setLastActivityAt(new \DateTimeImmutable());
            $entityManager->flush();
        }

        return $this->json(['status' => 'ok']);
    }

    /**
     * Auto-complétion des villes (pour le formulaire d'inscription)
     * Utilise l'API geo.api.gouv.fr
     */
    #[Route('/villes', name: 'api_villes', methods: ['GET'])]
    public function searchVilles(): JsonResponse
    {
        // Note: Cette route est juste un proxy si nécessaire
        // L'auto-complétion peut aussi être faite côté client directement
        return $this->json([
            'message' => 'Utilisez directement l\'API geo.api.gouv.fr côté client'
        ]);
    }
}
