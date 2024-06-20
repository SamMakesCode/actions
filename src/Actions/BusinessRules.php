<?php

namespace SamMakesCode\Actions\Actions;

use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;
use SamMakesCode\Actions\Exceptions\BusinessRuleAlreadyDefined;
use SamMakesCode\Actions\Exceptions\BusinessRulesNotSatisfied;

trait BusinessRules
{
    /** @var BusinessRulesInterface[] $businessRules  */
    protected array $businessRules = [];

    protected array $failingRules = [];

    public function registerBusinessRule(BusinessRulesInterface $businessRule): void
    {
        if ($this->isAlreadyDefined($businessRule)) {
            throw new BusinessRuleAlreadyDefined(
                $this,
                $businessRule,
            );
        }

        $this->businessRules[] = $businessRule;
    }

    /**
     * @param BusinessRulesInterface[] $businessRules
     * @return void
     */
    public function registerBusinessRules(array $businessRules): void
    {
        foreach ($businessRules as $businessRule) {
            $this->registerBusinessRule($businessRule);
        }
    }

    private function isAlreadyDefined(BusinessRulesInterface $businessRule): bool
    {
        foreach ($this->businessRules as $existingBusinessRules) {
            if ($businessRule::class === $existingBusinessRules::class) {
                return true;
            }
        }

        return false;
    }

    public function evaluateBusinessRules(): void
    {
        if (count($this->businessRules) > 0) {
            foreach ($this->businessRules as $businessRule) {
                $this->failingRules[] = $businessRule;
            }
        }

        if (count($this->failingRules) > 0) {
            throw new BusinessRulesNotSatisfied($this->failingRules);
        }
    }

    /**
     * @return BusinessRulesInterface[]
     */
    public function getFailingRules(): array
    {
        return $this->failingRules;
    }
}
