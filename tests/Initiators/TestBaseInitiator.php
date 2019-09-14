<?php

namespace Shoplic\Axis3\Tests\Initiators;

use Shoplic\Axis3\Initiators\BaseInitiator;
use Shoplic\Axis3\Interfaces\Views\ViewInterface;
use Shoplic\Axis3\Objects\AxisObject;
use Shoplic\Axis3\Starters\Starter;
use WP_UnitTestCase;

class TestBaseInitiator extends WP_UnitTestCase
{
    public function testFormatArgument()
    {
        $method  = makeMethodAccessible(TemporaryBaseInitiatorInitiator::class, 'formatArgument');
        $starter = new Starter();

        $initiator = new TemporaryBaseInitiatorInitiator();
        $initiator->setStarter($starter);

        $view   = $initiator->claimView(TemporaryBaseInitiatorView::class, ['testValue' => 'success']);
        $copied = $initiator->claimView(TemporaryBaseInitiatorView::class, ['testView' => 'copied'], false);

        // 테스트 #1: 배열. 인스턴스와 메소드 이름
        $returned = $method->invoke($initiator, [$view, 'getTestValue']);
        // 검증 #1
        $this->assertIsArray($returned);
        $this->assertEquals(2, sizeof($returned));
        $this->assertEquals($view, $returned[0]);
        $this->assertNotEquals($copied, $view);
        $this->assertEquals('getTestValue', $returned[1]);
        $this->assertEquals('success', call_user_func($returned));

        // 테스트 #2: 배열. Axis 3 뷰의 FQCN, 그리고 메소드 이름
        $returned = $method->invoke($initiator, [TemporaryBaseInitiatorView::class, 'getTestValue']);
        // 검증 #2 뷰는 재활용됨.
        $this->assertIsArray($returned);
        $this->assertEquals(2, sizeof($returned));
        $this->assertEquals($view, $returned[0]);
        $this->assertNotEquals($copied, $view);
        $this->assertEquals('getTestValue', $returned[1]);
        $this->assertEquals('success', call_user_func($returned));

        // 테스트 #3: 부를 수 있는 것
        $returned = $method->invoke($initiator, '__return_empty_string');
        // 검증 #3
        $this->assertEquals('__return_empty_string', $returned);

        // 테스트 #4: 부를 수 없는 것은 NULL 리턴되는지 확인
        $returned = $method->invoke($initiator, null);
        // 검증 #4
        $this->assertNull($returned);
    }
}

/**
 * Class TemporaryBaseInitiatorInitiator
 *
 * 테스트를 위한 임시 구현체.
 *
 * @package Shoplic\Axis3\Tests\Initiators
 */
class TemporaryBaseInitiatorInitiator extends BaseInitiator
{
    public function initHooks()
    {
        // 아무 것도 하지 않음.
    }
}

/**
 * Class TemporaryBaseInitiatorView
 *
 * 테스트를 위한 임시 구현체.
 *
 * @package Shoplic\Axis3\Tests\Initiators
 */
class TemporaryBaseInitiatorView extends AxisObject implements ViewInterface
{
    private $testValue = null;

    public function setup($args = array())
    {
        $this->testValue = $args['testValue'] ?? null;
    }

    public function getTestValue()
    {
        return $this->testValue;
    }
}
