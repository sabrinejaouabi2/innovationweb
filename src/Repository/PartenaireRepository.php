<?php

namespace App\Repository;

use App\Entity\Partenaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @extends ServiceEntityRepository<Partenaire>
 *
 * @method Partenaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partenaire|null findOneBy(array $criteria, array $orderBy = null)
 //* @method Partenaire[]    findAll()
 * @method Partenaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartenaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partenaire::class);
    }

    public function save(Partenaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Partenaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // Add other custom query methods here
    public function findPartenaireByName($filtre)
    {
        return $this->createQueryBuilder('e')
            ->where('e.filtre = :filtre')
            ->setParameter('filtre', $filtre)
            ->getQuery()
            ->getResult();
    }

    public function findByCategorieName($nom)
    {
        return $this->createQueryBuilder('o')
            ->join('o.IdCategorie', 'c')
            ->where('c.nom = :nom')
            ->setParameter('nom', $nom)
            ->getQuery()
            ->getResult();
    }

    public function searchNom($nom)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :ncl')
            ->setParameter('ncl', $nom.'%')
            ->getQuery()
            ->execute();
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder('o')
            ->getQuery()
            ->getResult();
    }

  

}