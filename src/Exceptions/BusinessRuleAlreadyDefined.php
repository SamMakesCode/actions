<?php

namespace SamMakesCode\Actions\Exceptions;

use SamMakesCode\Actions\Actions\BaseAction;
use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;

class BusinessRuleAlreadyDefined extends \Exception
{
    private static string $messageTemplate = 'Business rule %s is already defined in action %s.';

    public function __construct(BaseAction $action, BusinessRulesInterface $businessRule)
    {
        parent::__construct(
            sprintf(
                self::$messageTemplate,
                $businessRule::class,
                $action::class,
            )
        );
    }
}
