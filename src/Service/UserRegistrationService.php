<?php

namespace App\Service;
use App\Entity\User;
use App\Entity\UserProfile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

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
        $user->setProfile(new UserProfile());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
