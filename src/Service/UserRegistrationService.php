<?php

namespace App\Service;

Final class UserRegistrationService
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager
    ) {}

    public function registerUser(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );

        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
