<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use SamMakesCode\Actions\Exceptions\BusinessRulesNotSatisfied;
use Tests\TestObjects\Actions\CancelOrderAction;
use Tests\TestObjects\Actions\DispatchOrderAction;
use Tests\TestObjects\Actions\ReverseStringAction;
use Tests\TestObjects\BusinessRules\OrderIsConfirmed;
use Tests\TestObjects\Models\Order;
use Tests\TestObjects\Models\Payment;

class ActionsTest extends TestCase
{
    /**
     * This test will simply check whether an action will perform its function
     * without any business rules applied
     */
    public function testWhenNoRulesAreSpecified()
    {
        // First, let's create a random test string
        $testString = str_pad(
            (string)rand(1, 1000000000),
            10,
            '0',
            STR_PAD_LEFT,
        );

        // Now let's instantiate the action
        $action = new ReverseStringAction(
            $testString,
        );

        // Get the response from performing the action
        $response = $action->perform();

        // Check that the action has been performed correctly
        $this->assertNotNull($response);
        $this->assertEquals(strrev($testString), $response);
    }

    /**
     * This test will check that an action with one satisfied business rule will
     * perform its task correctly.
     */
    public function testWhenOneRuleIsSatisfied()
    {
        // Let's say we have a pending order...
        $order = new Order('pending');

        /*
         * ...and the user wants to cancel it.
         *
         * Note: that the cancel order action has a business rule that requires
         * that the order hasn't already been dispatched.
         */
        $cancelOrder = new CancelOrderAction(
            $order,
        );

        // Perform the action...
        $order = $cancelOrder->perform();

        // Do we get a cancelled order back?
        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals('cancelled', $order->status);
    }

    public function testWhenOneRuleIsUnsatisfied()
    {
        // Let's say we have an order that has left the warehouse
        $order = new Order('dispatched');

        /*
         * And the user tries to cancel it (maybe the UI allows for that)
         *
         * Note: that the cancel order action has a business rule that requires
         * that the order hasn't already been dispatched.
         */
        $cancelOrder = new CancelOrderAction(
            $order,
        );

        // Perform the action
        try {
            $cancelOrder->perform();

            // Let's just check it failed...
            $this->fail('We didn\'t receive the expected exception');
        } catch (\Exception $exception) {
            // Let's check we get one single failing rule
            $this->assertCount(1, $exception->failingRules);
            // And that the exception
            $this->assertInstanceOf(BusinessRulesNotSatisfied::class, $exception);
        }
    }

    public function testWhenManyRulesAreSatisfied()
    {
        // Let's say an order has been placed and paid for...
        $order = new Order(
            'confirmed',
            new Payment(true),
        );

        // And some job in our system wants to dispatch it
        $dispatchOrder = new DispatchOrderAction($order);

        // Perform the action
        $order = $dispatchOrder->perform();

        // First let's check we get an order back
        $this->assertInstanceOf(Order::class, $order);
        // Then, let's check that the order status is now "dispatched"
        $this->assertEquals('dispatched', $order->status);
    }

    public function testWhenOneOfManyRulesIsUnsatisfied()
    {
        /*
         * Let's say we have an order that is paid for, but for some reason it
         * wasn't automatically marked as "confirmed". Maybe it's a bug, maybe
         * confirming orders is a manual process to prevent fraud...
         */
        $order = new Order('pending', new Payment(true));

        /*
         * And some badly figured job that only looks for settled payments comes
         * along and decides this order is ready to dispatch...
         */
        $dispatchOrder = new DispatchOrderAction($order);

        try {
            // Let's perform the job...
            $dispatchOrder->perform();

            // Let's just check it failed...
            $this->fail('We didn\'t receive the expected exception');
        } catch (\Exception $exception) {
            /** @var BusinessRulesNotSatisfied $exception */
            // First let's check we get the right exception back
            $this->assertInstanceOf(BusinessRulesNotSatisfied::class, $exception);
            // And then let's check that we're only receiving the failing rule
            $this->assertCount(1, $exception->failingRules);
            $this->assertInstanceOf(OrderIsConfirmed::class, $exception->failingRules[0]);
            // Finally, let's check we can get an array of error messages back
            $this->assertCount(1, $exception->getFailingRulesMessages());
        }
    }

    public function testWhenAllRulesAreUnsatisfied()
    {
        /*
         * Let's say some very naughty developer has access to the production
         * database and that they've inserted an order manually, and wrong.
         */
        $order = new Order('invalid_status');

        /*
         * And some badly configured job comes along and decides it's ready to
         * dispatch
         */
        $dispatchOrder = new DispatchOrderAction($order);

        try {
            // Perform the action
            $dispatchOrder->perform();

            // Let's just check it failed...
            $this->fail('We didn\'t receive the expected exception');
        } catch (\Exception $exception) {
            /** @var BusinessRulesNotSatisfied $exception */
            // First let's check we get the right exception back
            $this->assertInstanceOf(BusinessRulesNotSatisfied::class, $exception);
            // And then let's check that we're getting the right number of rules
            $this->assertCount(2, $exception->failingRules);
            // Finally, let's check we can get an array of error messages back
            $this->assertCount(2, $exception->getFailingRulesMessages());
        }
    }

    public function testStaticDoPerformsAction()
    {
        // First, let's create a random test string
        $testString = str_pad(
            (string)rand(1, 1000000000),
            10,
            '0',
            STR_PAD_LEFT,
        );

        // Now let's do the action without the manual instantiation
        $response = ReverseStringAction::do($testString);

        // Check that the action has been performed correctly
        $this->assertNotNull($response);
        $this->assertEquals(strrev($testString), $response);
    }
}
