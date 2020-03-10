<?php

namespace App\Repository;

use App\Entity\NewPcComMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method NewPcComMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewPcComMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewPcComMember[]    findAll()
 * @method NewPcComMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewPcComMemberRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, NewPcComMember::class);
    }

    public function getDataByRef($ref)
    {
        return $this->createQueryBuilder('n')
            ->select('n.tracking, n.rcp, n.bill_amt, n.cod_fee, n.transfer_amt, n.sd, n.dd, n.tfd')
            ->where('n.refno2 = :ref')
            ->setParameter('ref', $ref)
//            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

}
