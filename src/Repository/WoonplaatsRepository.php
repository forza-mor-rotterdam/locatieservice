<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Woonplaats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Woonplaats>
 */
class WoonplaatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Woonplaats::class);
    }

    public function findAllAsLookupArray(): array
    {
        return $this
            ->createQueryBuilder('woonplaats', 'woonplaats.matchNaam')
            ->select('woonplaats')
            ->addOrderBy('woonplaats.matchNaam', 'ASC')
            ->getQuery()
            ->execute()
        ;
    }
}
