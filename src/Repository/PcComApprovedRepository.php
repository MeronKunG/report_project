<?php

namespace App\Repository;

use App\Entity\PcComApproved;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PcComApproved|null find($id, $lockMode = null, $lockVersion = null)
 * @method PcComApproved|null findOneBy(array $criteria, array $orderBy = null)
 * @method PcComApproved[]    findAll()
 * @method PcComApproved[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PcComApprovedRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PcComApproved::class);
    }

    public function getDataByMemberIdAndRef($memberId, $ref)
    {
        return $this->createQueryBuilder('aa')
            ->select('aa.refno2, dd.mailcode AS tracking, cc.ordername, cc.orderphoneno, bb.billAmt, bb.codFee, bb.transferAmt, date(dd.sendmaildate) AS sd, date(dd.transactiondate) AS td , aa.tfd')
            ->leftJoin('App\Entity\PcComCollect', 'bb', 'WITH', 'aa.takeorderby = bb.takeorderby AND aa.invoice = bb.invoice')
            ->leftJoin('App\Entity\MerchantBilling', 'cc', 'WITH', 'aa.takeorderby = cc.takeorderby AND aa.invoice = cc.paymentInvoice')
            ->leftJoin('App\Entity\MerchantBillingDelivery', 'dd', 'WITH', 'aa.takeorderby = dd.takeorderby AND aa.invoice = dd.paymentInvoice')
            ->where('aa.memId = :memberId AND aa.refno2 = :ref')
            ->setParameter('memberId', $memberId)
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getResult();
    }

}
