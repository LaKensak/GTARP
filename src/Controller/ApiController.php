<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ApiController extends AbstractController
{
    #[Route('/online-count', name: 'api_online_count', methods: ['GET'])]
    public function getOnlineCount(UserRepository $userRepository): JsonResponse
    {
        $count = $userRepository->countOnlineUsers();

        return $this->json([
            'count' => $count,
        ]);
    }

    #[Route('/heartbeat', name: 'api_heartbeat', methods: ['POST'])]
    public function heartbeat(EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if ($user) {
            $user->setLastActivityAt(new \DateTimeImmutable());
            $entityManager->flush();
        }

        return $this->json(['status' => 'ok']);
    }

    #[Route('/villes', name: 'api_villes', methods: ['GET'])]
    public function searchVilles(): JsonResponse
    {
        return $this->json([
            'message' => 'Utilisez directement l\'API geo.api.gouv.fr côté client'
        ]);
    }
}
