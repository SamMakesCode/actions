<?php

namespace Tests\TestObjects\Actions;

use SamMakesCode\Actions\Actions\BaseAction;
use Tests\TestObjects\BusinessRules\OrderIsNotDispatched;
use Tests\TestObjects\Models\Order;

class CancelOrderAction extends BaseAction
{
    public function __construct(
        private readonly Order $order,
    ) {
        $this->registerBusinessRule(
            new OrderIsNotDispatched($this->order),
        );
    }

    public function handle(): Order
    {
        $this->order->cancel();

        return $this->order;
    }
}
