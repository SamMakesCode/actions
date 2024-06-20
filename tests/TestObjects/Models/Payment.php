<?php

namespace Tests\TestObjects\Models;

class Payment
{
    public function __construct(
        public bool $is_settled = false,
    ) {}
}
