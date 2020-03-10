<?php

namespace App\Repository;

use App\Entity\MemberCodTransfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MemberCodTransfer|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberCodTransfer|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberCodTransfer[]    findAll()
 * @method MemberCodTransfer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberCodTransferRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MemberCodTransfer::class);
    }

    public function getDataByPhoneRegister($phoneregis)
    {
        return $this->createQueryBuilder('t')
            ->select('t.tfd, t.cod_amt, t.ref')
            ->where('t.phoneregis = :phoneregis')
            ->setParameter('phoneregis', $phoneregis)
            ->orderBy('t.tfd', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
