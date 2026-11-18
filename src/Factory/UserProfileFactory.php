<?php

namespace App\Factory;

use App\Entity\User;
use App\Entity\UserProfile;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<UserProfile>
 */
final class UserProfileFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return UserProfile::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'marketingEmailOptIn' => false,
            'phone' => '+33123456789',
            'phoneCountryCode' => '+33',
            'updatedAt' => new \DateTimeImmutable(),
            'user' => UserFactory::new(),
        ];
    }

    public function forUser(User|Proxy $user): static
    {
        return $this->with([
            'user' => $user,
        ]);
    }

    public function optedIn(): static
    {
        return $this->with([
            'marketingEmailOptIn' => true,
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(UserProfile $userProfile): void {})
        ;
    }
}
