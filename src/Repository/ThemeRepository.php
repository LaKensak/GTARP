<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;

class ThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }

    public function findAllPaginated(PaginatorInterface $paginator, int $page): PaginationInterface
    {
        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.discussions', 'd')
            ->addSelect('d')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery();

        return $paginator->paginate(
            $query,
            $page,
            5 // limit
        );
    }

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
