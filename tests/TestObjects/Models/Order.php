<?php

namespace Tests\TestObjects\Models;

class Order
{
    public function __construct(
        public string $status,
        public readonly ?Payment $payment = null,
    ) {}

    public function cancel(): void
    {
        $this->status = 'cancelled';
    }

    public function dispatch(): void
    {
        $this->status = 'dispatched';
    }
}
