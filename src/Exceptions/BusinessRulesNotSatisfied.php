<?php

namespace SamMakesCode\Actions\Exceptions;

use SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;

class BusinessRulesNotSatisfied extends \Exception
{
    private static string $messageTemplate = 'The business rules (%s) are not satisfied.';

    /**
     * @param BusinessRulesInterface[] $failingRules
     */
    public function __construct(
        public readonly array $failingRules = [],
    ) {
        $message = sprintf(
            self::$messageTemplate,
            implode(
                ', ',
                array_map('get_class', $this->failingRules),
            ),
        );

        parent::__construct(
            $message,
        );
    }

    /**
     * @return string[]
     */
    public function getFailingRulesMessages(): array
    {
        $messages = [];
        foreach ($this->failingRules as $failingRule) {
            $messages[] = $failingRule->getFailureMessage();
        }
        return $messages;
    }
}
