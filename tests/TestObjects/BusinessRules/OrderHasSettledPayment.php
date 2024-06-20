<?php

namespace Tests\TestObjects\BusinessRules;

use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;
use Tests\TestObjects\Models\Payment;

readonly class OrderHasSettledPayment implements BusinessRulesInterface
{
    public function __construct(
        private ?Payment $payment,
    ) {}

    public function getFailureMessage(): string
    {
        return 'Order doesn\'t have payment';
    }

    public function isSatisfied(): bool
    {
        return $this->payment !== null && $this->payment->is_settled;
    }
}
