<?php

namespace App\Repository;

use App\Entity\TestMemberCodAll;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestMemberCodAll|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestMemberCodAll|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestMemberCodAll[]    findAll()
 * @method TestMemberCodAll[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestMemberCodAllRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestMemberCodAll::class);
    }


    public function getStatusByPhone($sender_phone)
    {
        return $this->createQueryBuilder('t')
            ->select('t.status, t.invAmt, t.codAmt')
            ->where('t.tel = :sender_phone')
            ->setParameter('sender_phone', $sender_phone)
            ->getQuery()
            ->getResult();
    }
}
