<?php

namespace App\Repository;

use App\Entity\MemberCodStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method MemberCodStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberCodStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberCodStat[]    findAll()
 * @method MemberCodStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberCodStatRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, MemberCodStat::class);
    }

}
