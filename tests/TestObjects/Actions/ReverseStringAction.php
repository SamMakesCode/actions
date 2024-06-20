<?php

namespace Tests\TestObjects\Actions;

use SamMakesCode\Actions\Actions\BaseAction;

class ReverseStringAction extends BaseAction
{
    public function __construct(
        private readonly string $stringToReverse,
    ) {}

    public function handle(): string
    {
        return strrev($this->stringToReverse);
    }
}
