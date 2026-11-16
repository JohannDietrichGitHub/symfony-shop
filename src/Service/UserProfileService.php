<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface;

final class UserProfileService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getOrCreateFor(User $user): UserProfile
    {
        if ($user->getProfile() instanceof UserProfile) {
            return $user->getProfile();
        }

        $profile = new UserProfile();
        $user->setProfile($profile);
        $this->entityManager->persist($profile);

        return $profile;
    }

    public function save(UserProfile $profile): void
    {
        $profile->setUpdatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($profile);
        $this->entityManager->flush();
    }
}
