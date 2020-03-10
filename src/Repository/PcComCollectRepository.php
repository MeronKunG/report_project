<?php

namespace App\Repository;

use App\Entity\PcComCollect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PcComCollect|null find($id, $lockMode = null, $lockVersion = null)
 * @method PcComCollect|null findOneBy(array $criteria, array $orderBy = null)
 * @method PcComCollect[]    findAll()
 * @method PcComCollect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PcComCollectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PcComCollect::class);
    }

//    public function getDataByMemberIdAndRef($memberId, $ref)
//    {
//        return $this->createQueryBuilder('a')
//            ->select('y.phoneregis AS phoneregis, mbd.mailcode AS tracking, mb.ordername, mb.orderphoneno, a.billAmt AS bill_amt, a.codFee AS cod_fee,
//            a.transferAmt AS transfer_amt, cast(mbd.sendmaildate AS date) AS sd, cast(a.paymentD AS date) AS dd, cp.tfd AS tfd, cp.refno2 AS refno2, a.pcMemId AS pid')
//            ->leftJoin('App\Entity\PcComApproved', 'cp', 'WITH', 'a.invoice = cp.invoice')
//            ->leftJoin('App\Entity\PBudget', 'z', 'WITH', 'a.takeorderby = cp.takeorderby')
//            ->leftJoin('App\Entity\MerchantConfig', 'x', 'WITH', 'a.takeorderby = x.takeorderby')
//            ->leftJoin('App\Entity\ParcelMember', 'y', 'WITH', 'a.pcMemId = y.memberId')
//            ->leftJoin('App\Entity\MerchantBillingDelivery', 'mbd', 'WITH', 'a.takeorderby = mbd.takeorderby AND a.invoice = mbd.paymentInvoice')
//            ->leftJoin('App\Entity\MerchantBilling', 'mb', 'WITH', 'a.takeorderby = mbd.takeorderby AND a.invoice = mb.paymentInvoice')
//            ->where('y.memberId = :memberId AND cp.refno2 = :ref')
//            ->setParameter('memberId', $memberId)
//            ->setParameter('ref', $ref)
//            ->getQuery()
//            ->getResult();
//    }

}
