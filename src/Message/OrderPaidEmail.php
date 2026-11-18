<?php

namespace App\Message;

final readonly class OrderPaidEmail
{
    public function __construct(
        public int $orderId,
    ) {
    }
}
