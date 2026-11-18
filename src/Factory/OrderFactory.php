<?php

namespace App\Factory;

use App\Entity\Order;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\Proxy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Order>
 */
final class OrderFactory extends PersistentObjectFactory
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
        return Order::class;
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
            'currency' => 'eur',
            'status' => 'pending',
            'total' => 0,
            'user' => UserFactory::new(),
        ];
    }

    public function forUser(User|Proxy $user): static
    {
        return $this->with([
            'user' => $user,
        ]);
    }

    public function paid(): static
    {
        return $this
            ->with(['status' => 'paid'])
            ->afterInstantiate(static function (Order $order): void {
                $order->markAsPaid(new \DateTimeImmutable());
            })
        ;
    }

    public function paymentFailed(): static
    {
        return $this->with([
            'status' => 'payment_failed',
        ]);
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Order $order): void {})
        ;
    }
}
