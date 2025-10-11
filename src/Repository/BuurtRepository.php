<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Buurt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Buurt>
 */
class BuurtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Buurt::class);
    }

    public function findAllAsLookupArray(): array
    {
        return $this
            ->createQueryBuilder('buurt', 'buurt.matchNaam')
            ->select('buurt')
            ->addOrderBy('buurt.matchNaam', 'ASC')
            ->getQuery()
            ->execute()
        ;
    }
}
