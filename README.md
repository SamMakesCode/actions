Actions
---

# Introduction

This library is designed to facilitate contained, repeatable actions you might use throughout your application and enforce business rules on those actions.

## Example use case

A hypothetical application might want to - for various reasons and in various places - cancel an order. In order to prevent duplication of logic, a developer might create something like a class called "CancelOrder" that defines that logic.

This library formalises that process and allows you to attach business logic to it. For example, if it's never possible to cancel an order that's been dispatched, you can define that logic within your action.

# Requirements

- [Composer](https://getcomposer.org/)
- PHP 8.2 or later

# Installation

```
composer require sammakescode/actions
```

# Usage

You can have a look at the `tests` directory for some examples, but here's a quick on your can have a look at.

## Example

### Define your business rules

```php
<?php

namespace App;

use \SamMakesCode\Actions\BusinessRules\BusinessRulesInterface;

class OrderMustNotBeDispatched implements BusinessRulesInterface
{
    public function __construct(
        private readonly Order $order,    
    ) {}
    
    public function isSatisfied(): bool
    {
        return !in_array(
            $this->order->status,
            [
                'dispatched',
                'delivered',
                'complete',
            ]
        );
    }
    
    public function getFailureMessage(): string
    {
        return 'Cannot perform action because the order has been dispatched!';
    }
}
```

### Define your action

```php
<?php

namespace App;

use \SamMakesCode\Actions\Actions\BaseAction;

class CancelOrder extends BaseAction
{
    public function __construct(
        private readonly Order $order,
    ) {
        $this->registerBusinessRules([
            new OrderMustNotBeDispatched($this->order),
        ]);
    }
    
    public function handle()
    {
        $this->order->cancel();
    }
}
```

### Perform your action

```php
<?php

$order = new \App\Order;
$order->status = 'dispatched';

$action = new \App\CancelOrder($order);

// Throws \SamMakesCode\Actions\Exceptions\BusinessRulesNotSatisfied because dispatched orders can't be cancelled
$action->perform();
```

*Note: When the `perform()` method is called on an action, the `__call()` magic method intercepts the request and calls the `handle()` method after any business rules have been run.*

### Shorthand actions

Instead of manually instantiating and performing you action, you can also use the `::do()` shorthand.

```php
CancelOrder::do($order);
```

# Contributions

Contributions and issues are welcome. :)

# FAQ

## Isn't this just fancy conditional blocks?

Yes, but also no. The library allows you to wrap complex (or simple) sets of conditional statements into a block that can be reused in many places and standardises the exceptions that are thrown.
