<?php

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Repository pour l'entité Discussion
 * Gère les requêtes liées aux discussions du forum
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    /**
     * Récupère les discussions d'un thème avec pagination
     * 10 discussions par page selon le cahier des charges
     * Triées par ordre chronologique (les plus anciennes en premier)
     */
    public function findByThemePaginated(Theme $theme, PaginatorInterface $paginator, int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('d')
            ->where('d.theme = :theme')
            ->setParameter('theme', $theme)
            ->leftJoin('d.auteur', 'a')
            ->addSelect('a')
            ->orderBy('d.createdAt', 'ASC') // Ordre chronologique
            ->getQuery();

        // Pagination avec 10 discussions par page
        return $paginator->paginate(
            $query,
            $page,
            10 // max 10 discussions par page
        );
    }

    /**
     * Compte le nombre de discussions par thème
     */
    public function countByTheme(Theme $theme): int
    {
        return $this->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->where('d.theme = :theme')
            ->setParameter('theme', $theme)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
