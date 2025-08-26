<?php
namespace App\Repository;
use App\Entity\ServiceRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class ServiceRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    { parent::__construct($registry, ServiceRecord::class); }
}
