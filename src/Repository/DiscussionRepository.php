<?php

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    public function findByThemePaginated(Theme $theme, PaginatorInterface $paginator, int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('d')
            ->where('d.theme = :theme')
            ->setParameter('theme', $theme)
            ->leftJoin('d.auteur', 'a')
            ->addSelect('a')
            ->orderBy('d.createdAt', 'ASC') // ordre
            ->getQuery();

        return $paginator->paginate(
            $query,
            $page,
            10 // limit
        );
    }

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
