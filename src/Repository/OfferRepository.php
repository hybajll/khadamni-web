<?php

namespace App\Repository;

use App\Entity\Offer;
use App\Entity\Society;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Offer>
 */
class OfferRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Offer::class);
    }

    /**
     * Find all active offers
     */
    public function findAllActive()
    {
        return $this->createQueryBuilder('o')
            ->where('o.isActive = :active')
            ->andWhere('o.expirationDate IS NULL OR o.expirationDate >= :today')
            ->setParameter('active', true)
            ->setParameter('today', new \DateTimeImmutable())
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find active offers for a specific society
     */
    public function findActiveBySociety(Society $society)
    {
        return $this->createQueryBuilder('o')
            ->where('o.society = :society')
            ->andWhere('o.isActive = :active')
            ->andWhere('o.expirationDate IS NULL OR o.expirationDate >= :today')
            ->setParameter('society', $society)
            ->setParameter('active', true)
            ->setParameter('today', new \DateTimeImmutable())
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find offers by domain
     */
    public function findByDomain(string $domain)
    {
        return $this->createQueryBuilder('o')
            ->where('o.domain = :domain')
            ->andWhere('o.isActive = :active')
            ->andWhere('o.expirationDate IS NULL OR o.expirationDate >= :today')
            ->setParameter('domain', $domain)
            ->setParameter('active', true)
            ->setParameter('today', new \DateTimeImmutable())
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find offers by contract type
     */
    public function findByContractType(string $contractType)
    {
        return $this->createQueryBuilder('o')
            ->where('o.contractType = :contractType')
            ->andWhere('o.isActive = :active')
            ->andWhere('o.expirationDate IS NULL OR o.expirationDate >= :today')
            ->setParameter('contractType', $contractType)
            ->setParameter('active', true)
            ->setParameter('today', new \DateTimeImmutable())
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find offers by location
     */
    public function findByLocation(string $location)
    {
        return $this->createQueryBuilder('o')
            ->where('o.location = :location')
            ->andWhere('o.isActive = :active')
            ->andWhere('o.expirationDate IS NULL OR o.expirationDate >= :today')
            ->setParameter('location', $location)
            ->setParameter('active', true)
            ->setParameter('today', new \DateTimeImmutable())
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Search offers with filters
     */
    public function searchOffers(?string $keyword = null, ?string $domain = null, ?string $location = null)
    {
        $query = $this->createQueryBuilder('o')
            ->where('o.isActive = :active')
            ->andWhere('o.expirationDate IS NULL OR o.expirationDate >= :today')
            ->setParameter('active', true)
            ->setParameter('today', new \DateTimeImmutable());

        if ($keyword) {
            $query->andWhere('o.title LIKE :keyword OR o.description LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        if ($domain) {
            $query->andWhere('o.domain = :domain')
                ->setParameter('domain', $domain);
        }

        if ($location) {
            $query->andWhere('o.location = :location')
                ->setParameter('location', $location);
        }

        return $query->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
