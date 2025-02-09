<?php

namespace App\Repository;

use App\Entity\Annonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;

class AnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Annonce::class);
    }

    public function findOneWithCreator(int $id): ?Annonce
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.createdBy', 'u')
            ->addSelect('u')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNearbyAnnonces(float $latitude, float $longitude, float $radiusKm = 10): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT * FROM annonce
            WHERE ST_DWithin(
                ST_SetSRID(ST_MakePoint(longitude, latitude), 4326),
                ST_SetSRID(ST_MakePoint(:longitude, :latitude), 4326),
                :radius
            )
        ";

        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'latitude' => $latitude,
            'longitude' => $longitude,
            'radius' => $radiusKm * 1000,
        ]);

        return $result->fetchAllAssociative();
    }


    public function findRecentAnnonces(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findAnnoncesByUser(int $userId): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.createdBy = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('a.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
