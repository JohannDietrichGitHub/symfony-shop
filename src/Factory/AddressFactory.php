<?php

namespace App\Factory;

use App\Entity\Address;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Address>
 */
final class AddressFactory extends PersistentObjectFactory
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
        return Address::class;
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
            'addressLine1' => self::faker()->streetAddress(),
            'city' => self::faker()->city(),
            'countryCode' => 'FR',
            'isDefault' => false,
            'label' => 'Home',
            'postalCode' => self::faker()->postcode(),
            'recipientName' => self::faker()->name(),
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

    public function asDefault(): static
    {
        return $this->with([
            'isDefault' => true,
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Address $address): void {})
        ;
    }
}
