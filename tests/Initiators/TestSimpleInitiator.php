<?php

namespace Shoplic\Axis3\Tests\Initiators;

use Shoplic\Axis3\Initiators\SimpleInitiator;
use Shoplic\Axis3\Starters\Starter;
use WP_UnitTestCase;

/**
 * Class TestSimpleInitiator
 *
 * SimpleInitiator 테스트
 *
 * @package Shoplic\Axis3\Tests\Initiators
 * @see     SimpleInitiator
 */
class TestSimpleInitiator extends WP_UnitTestCase
{
    private $starter;

    private $initiator;

    public function setUp()
    {
        $this->starter   = new Starter();
        $this->initiator = new SimpleInitiator();

        $this->starter->setMainFile(__FILE__);
        $this->initiator->setStarter($this->starter);
    }

    public function testAddAction()
    {
        $function = function () {
        };

        $ref = makeMethodAccessible(SimpleInitiator::class, 'addAction');
        $ref->invoke($this->initiator, 'my-action', $function, 20);

        $this->assertEquals(20, has_action('my-action', $function));
    }

    public function testAddFilter()
    {
        $function = function () {
        };

        $ref = makeMethodAccessible(SimpleInitiator::class, 'addFilter');
        $ref->invoke($this->initiator, 'my-filter', $function, 20);

        $this->assertEquals(20, has_action('my-filter', $function));
    }

    public function testAddShortcode()
    {
        $function = function () {
        };

        $ref = makeMethodAccessible(SimpleInitiator::class, 'addShortcode');
        $ref->invoke($this->initiator, 'my-shortcode', $function);

        global $shortcode_tags;

        $this->assertArrayHasKey('my-shortcode', $shortcode_tags);
        $this->assertEquals($shortcode_tags['my-shortcode'], $function);
    }

    public function testRegisterActivationHook()
    {
        $function = function () {
        };

        $ref = makeMethodAccessible(SimpleInitiator::class, 'registerActivationHook');
        $ref->invoke($this->initiator, $function);

        $this->assertEquals(has_action('activate_' . plugin_basename(__FILE__), $function), 10);
    }

    public function testRegisterDeactivationHook()
    {
        $function = function () {
        };

        $ref = makeMethodAccessible(SimpleInitiator::class, 'registerDeactivationHook');
        $ref->invoke($this->initiator, $function);

        $this->assertEquals(has_action('deactivate_' . plugin_basename(__FILE__), $function), 10);
    }
}
