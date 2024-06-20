<?php

namespace Tests\TestObjects\BusinessRules;

use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;
use Tests\TestObjects\Models\Order;

readonly class OrderIsNotDispatched implements BusinessRulesInterface
{
    public function __construct(
        private Order $order,
    ) {}

    public function getFailureMessage(): string
    {
        return 'The order is dispatched!';
    }

    public function isSatisfied(): bool
    {
        return $this->order->status !== 'dispatched';
    }
}
