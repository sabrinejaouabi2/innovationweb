<?php

namespace App\Repository;

use App\Entity\Favouris;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @extends ServiceEntityRepository<Favouris>
 *
 * @method Favouris|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favouris|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favouris[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class favourisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favouris::class);
    }




    public function findAll(): array
    {
        return $this->createQueryBuilder('o')
            ->getQuery()
            ->getResult();
    }



}