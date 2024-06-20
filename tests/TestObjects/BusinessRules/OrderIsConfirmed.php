<?php

namespace Tests\TestObjects\BusinessRules;

use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;
use Tests\TestObjects\Models\Order;

readonly class OrderIsConfirmed implements BusinessRulesInterface
{
    public function __construct(
        private Order $order,
    ) {}

    public function getFailureMessage(): string
    {
        return 'Order is not confirmed';
    }

    public function isSatisfied(): bool
    {
        return $this->order->status === 'confirmed';
    }
}
