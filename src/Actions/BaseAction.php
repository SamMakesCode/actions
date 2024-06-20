<?php

namespace SamMakesCode\Actions\Actions;

use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;
use SamMakesCode\Actions\Exceptions\BusinessRulesNotSatisfied;

/**
 * @method perform
 */
abstract class BaseAction
{
    private array $businessRules = [];
    private array $failingRules = [];

    public function __call(string $name, array $arguments)
    {
        if ($name === 'perform') {
            if ($this->hasBusinessRules()) {
                $this->evaluateBusinessRules();
            }
            return $this->handle();
        }
    }

    public function registerBusinessRules(
        array $businessRules,
    ): void {
        $this->businessRules += $businessRules;
    }

    public function registerBusinessRule(
        BusinessRulesInterface $businessRule,
    ): void {
        $this->businessRules[] = $businessRule;
    }

    private function evaluateBusinessRules(): void
    {
        foreach ($this->businessRules as $businessRule) {
            if (!$businessRule->isSatisfied()) {
                $this->failingRules[] = $businessRule;
            }
        }

        if (count($this->failingRules) > 0) {
            throw new BusinessRulesNotSatisfied($this->failingRules);
        }
    }

    private function hasBusinessRules(): bool
    {
        return count($this->businessRules) > 0;
    }
}
