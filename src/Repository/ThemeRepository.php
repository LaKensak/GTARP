<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Repository pour l'entité Theme
 * Gère les requêtes liées aux thèmes du forum
 */
class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    /**
     * Récupère les thèmes avec pagination (5 thèmes par page selon le cahier des charges)
     * Les thèmes sont triés par date de dernière discussion
     */
    public function findAllPaginated(PaginatorInterface $paginator, int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.discussions', 'd')
            ->addSelect('d')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery();

        // Pagination avec 5 thèmes par page
        return $paginator->paginate(
            $query,
            $page,
            5 // max 5 thèmes par page
        );
    }

    /**
     * Récupère un thème avec ses discussions pré-chargées
     */
    public function findWithDiscussions(int $id): ?Theme
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.discussions', 'd')
            ->addSelect('d')
            ->leftJoin('d.auteur', 'a')
            ->addSelect('a')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
