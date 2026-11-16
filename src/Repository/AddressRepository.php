<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class AddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Address::class);
    }

    /**
     * @return list<Address>
     */
    public function findForUser(User $user): array
    {
        return $this->createQueryBuilder('address')
            ->andWhere('address.user = :user')
            ->setParameter('user', $user)
            ->orderBy('address.isDefault', 'DESC')
            ->addOrderBy('address.label', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneForUser(User $user, int $id): ?Address
    {
        return $this->createQueryBuilder('address')
            ->andWhere('address.id = :id')
            ->andWhere('address.user = :user')
            ->setParameter('id', $id)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
