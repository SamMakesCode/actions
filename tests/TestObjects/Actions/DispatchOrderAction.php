<?php

namespace Tests\TestObjects\Actions;

use SamMakesCode\Actions\Actions\BaseAction;
use Tests\TestObjects\BusinessRules\OrderHasSettledPayment;
use Tests\TestObjects\BusinessRules\OrderIsConfirmed;
use Tests\TestObjects\Models\Order;

class DispatchOrderAction extends BaseAction
{
    public function __construct(
        private readonly Order $order,
    ) {
        $this->registerBusinessRules([
            new OrderHasSettledPayment($this->order?->payment),
            new OrderIsConfirmed($this->order),
        ]);
    }

    public function handle(): Order
    {
        $this->order->dispatch();

        return $this->order;
    }
}
