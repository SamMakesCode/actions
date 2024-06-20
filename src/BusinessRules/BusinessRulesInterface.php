<?php

namespace SamMakesCode\Actions\BusinessRules;

interface BusinessRulesInterface
{
    public function getFailureMessage(): string;

    public function isSatisfied(): bool;
}
