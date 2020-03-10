<?php

namespace App\Repository;

use App\Entity\TestHybridOwner;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestHybridOwner|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestHybridOwner|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestHybridOwner[]    findAll()
 * @method TestHybridOwner[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestHybridOwnerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestHybridOwner::class);
    }
}
