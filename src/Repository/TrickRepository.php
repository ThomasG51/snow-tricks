<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }


    /**
     * Return all tricks ten by ten
     *
     * @param $firstItem
     * @param $nbItems
     * @return int|mixed|string
     */
    public function find5By5($firstItem, $nbItems)
    {
        return $this->createQueryBuilder('trick')
                      ->orderBy('trick.createdAt', 'ASC')
                      ->setFirstResult($firstItem)
                      ->setMaxResults($nbItems)
                      ->getQuery()
                      ->getResult();
    }
}
