<?php

namespace App\Repository;

use App\Entity\ParcelMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ParcelMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method ParcelMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ParcelMember[]    findAll()
 * @method ParcelMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParcelMemberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ParcelMember::class);
    }

    public function getMemberIdByPhoneNo($phone)
    {
        return $this->createQueryBuilder('p')
            ->select('p.memberId, p.firstname, p.lastname')
            ->where('p.phoneregis = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getResult();
    }

}
