<?php

namespace App\Repository;

use App\Entity\TestAllParcel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TestAllParcel|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestAllParcel|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestAllParcel[]    findAll()
 * @method TestAllParcel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestAllParcelRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TestAllParcel::class);
    }

    public function getDataBySenderPhone($sender_phone)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone')
            ->setParameter('sender_phone', $sender_phone)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneForDownload($sender_phone, $startDate, $endDate, $status)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, cc.ordername,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct, cc.province')
            ->leftJoin('App\Entity\MerchantBilling', 'cc', 'WITH', 't.takeorderby = cc.takeorderby AND t.paymentInvoice = cc.paymentInvoice')
            ->where('t.senderPhone = :sender_phone')
            ->andWhere('(t.sendmaildate BETWEEN :startDate AND :endDate) AND t.statusNameTh IN (:status)')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->setParameter('status', $status)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAll($sender_phone)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.transportType =\'cod\' AND t.statuscode = \'105\'')
            ->setParameter('sender_phone', $sender_phone)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneFilterSize($sender_phone, $size)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.sizeName = :size AND t.statuscode <> \'101\'')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('size', $size)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneFilter($sender_phone, $filter)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.codReturn = :filter AND t.transportType =\'cod\' AND t.statuscode = \'105\'')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('filter', $filter)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getCountDataBySenderPhoneAll($sender_phone)
    {
        return $this->createQueryBuilder('t')
            ->select('t.codReturn, t.codAmt')
            ->where('t.senderPhone = :sender_phone AND t.transportType =\'cod\' AND t.statuscode = \'105\'')
            ->setParameter('sender_phone', $sender_phone)
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAndSearch($sender_phone, $value)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct, t.recipientPhone')
            ->where('t.senderPhone = :sender_phone')
            ->andWhere('t.mailcode LIKE :value OR t.recipientInfo LIKE :value OR t.recipientPhone LIKE :value')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('value', '%' .$value. '%')
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAndStatus($sender_phone, $status)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.statusNameTh = :status')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('status', $status)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAndSendDate($sender_phone, $send_date, $end_date)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND date(t.sendmaildate) BETWEEN :sendmaildate AND :endDate')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('sendmaildate', $send_date)
            ->setParameter('endDate', $end_date)
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getCountStatusBySenderPhone($sender_phone)
    {
        return $this->createQueryBuilder('t')
            ->select('t.statusNameTh')
            ->where('t.senderPhone = :sender_phone')
            ->setParameter('sender_phone', $sender_phone)
            ->getQuery()
            ->getResult();
    }


    public function getDataBySenderPhoneAndStatusAndSearch($sender_phone, $status, $value)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.statusNameTh = :status')
            ->andWhere('t.mailcode LIKE :value OR t.recipientInfo LIKE :value')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('status', $status)
            ->setParameter('value', '%' .$value. '%')
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAndSearchAll($sender_phone, $value)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.transportType =\'cod\' AND t.statuscode = \'105\'')
            ->andWhere('t.mailcode LIKE :value OR t.recipientInfo LIKE :value')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('value', '%' .$value. '%')
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAndSearchSize($sender_phone, $size, $value)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.sizeName = :size AND t.statuscode <> \'101\'')
            ->andWhere('t.mailcode LIKE :value OR t.recipientInfo LIKE :value')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('size', $size)
            ->setParameter('value', '%' .$value. '%')
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getDataBySenderPhoneAndSearchFilter($sender_phone, $value, $filter)
    {
        return $this->createQueryBuilder('t')
            ->select('t.sendmaildate, t.mailcode, t.trackingUrl, t.trackingUrl, t.trackingUrl, t.recipientInfo,
             t.transportType, t.codAmt, t.statusNameTh, t.transactiondate, t.sizeName, t.area,
              t.sizePrice, t.ffm, t.ffmProduct')
            ->where('t.senderPhone = :sender_phone AND t.codReturn = :filter AND t.transportType =\'cod\' AND t.statuscode = \'105\'')
            ->andWhere('t.mailcode LIKE :value OR t.recipientInfo LIKE :value')
            ->setParameter('sender_phone', $sender_phone)
            ->setParameter('filter', $filter)
            ->setParameter('value', '%' .$value. '%')
            ->orderBy('t.sendmaildate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
