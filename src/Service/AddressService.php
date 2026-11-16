<?php

namespace App\Service;

use App\Entity\Address;
use App\Entity\User;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;

final class AddressService
{
    public function __construct(
        private AddressRepository $addressRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return list<Address>
     */
    public function findForUser(User $user): array
    {
        return $this->addressRepository->findForUser($user);
    }

    public function findOneForUser(User $user, int $id): ?Address
    {
        return $this->addressRepository->findOneForUser($user, $id);
    }

    public function save(Address $address, User $user): void
    {
        $address->setUser($user);

        if (!$address->getId() || $address->isDefault()) {
            $this->clearDefaultExcept($user, $address);
        }

        if ($this->addressRepository->count(['user' => $user]) === 0) {
            $address->setDefault(true);
        }

        $address->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        $addresses = $this->addressRepository->findForUser($user);
        if ($addresses !== [] && !array_filter($addresses, static fn (Address $item): bool => $item->isDefault())) {
            $addresses[0]->setDefault(true);
            $this->entityManager->flush();
        }
    }

    public function delete(Address $address): void
    {
        $user = $address->getUser();
        $wasDefault = $address->isDefault();
        $this->entityManager->remove($address);
        $this->entityManager->flush();

        if ($wasDefault && $user instanceof User) {
            $remaining = $this->addressRepository->findForUser($user);
            if ($remaining !== []) {
                $remaining[0]->setDefault(true);
                $this->entityManager->flush();
            }
        }
    }

    private function clearDefaultExcept(User $user, Address $except): void
    {
        foreach ($this->addressRepository->findForUser($user) as $address) {
            if ($address !== $except) {
                $address->setDefault(false);
            }
        }
    }
}
