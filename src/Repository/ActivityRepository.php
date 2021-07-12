<?php

namespace App\Repository;


use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Activity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Activity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Activity[]    findAll()
 * @method Activity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /**
     * @param $value
     * @return array Returns an Activity object
     * @throws NonUniqueResultException
     */
    public function findByBlockee($value): ?Activity
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.blockee = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    /**
     * @param $value
     * @return array Returns an Activity object
     * @throws NonUniqueResultException
     */
    public function findByBlocker($value): ?Activity
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.blocker = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }


}