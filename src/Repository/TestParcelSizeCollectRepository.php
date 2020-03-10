<?php

namespace App\Repository;

use App\Entity\TestParcelSizeCollect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestParcelSizeCollect|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestParcelSizeCollect|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestParcelSizeCollect[]    findAll()
 * @method TestParcelSizeCollect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestParcelSizeCollectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestParcelSizeCollect::class);
    }

}
