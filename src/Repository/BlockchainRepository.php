<?php

namespace App\Repository;

use App\Entity\Blockchain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Blockchain|null find($id, $lockMode = null, $lockVersion = null)
 * @method Blockchain|null findOneBy(array $criteria, array $orderBy = null)
 * @method Blockchain[]    findAll()
 * @method Blockchain[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlockchainRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Blockchain::class);
    }

    /**
     * @return Blockchain|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastEntity() : ?Blockchain {
        return $this->createQueryBuilder('e')
                    ->orderBy('e.id', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
