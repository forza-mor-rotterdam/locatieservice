<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wijk;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Wijk>
 */
class WijkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wijk::class);
    }

    public function findAllAsLookupArray(): array
    {
        return $this
            ->createQueryBuilder('wijk', 'wijk.matchNaam')
            ->select('wijk')
            ->addOrderBy('wijk.matchNaam', 'ASC')
            ->getQuery()
            ->execute()
        ;
    }
}
