<?php

namespace App\Factory;

use App\Entity\Cart;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Cart>
 */
final class CartFactory extends PersistentObjectFactory
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
        return Cart::class;
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
            'createdAt' => new \DateTimeImmutable(),
            'customer' => UserFactory::new(),
            'updatedAt' => new \DateTimeImmutable(),
        ];
    }

    public function forCustomer(User|Proxy $user): static
    {
        return $this->with([
            'customer' => $user,
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Cart $cart): void {})
        ;
    }
}
