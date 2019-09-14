<?php

namespace Shoplic\Axis3\Tests\Initiators;

use Shoplic\Axis3\Initiators\AutoHookInitiator;
use Shoplic\Axis3\Starters\Starter;
use Shoplic\Axis3\Views\BaseView;
use WP_UnitTestCase;

class TestAutoHookInitiator extends WP_UnitTestCase
{
    private $starter;

    private $initiator;

    /** @var array v-callback */
    private $vCallback = [];

    /** @var array AutoHookInitiator 파리미터 분석의 정답지 */
    private $callbackParamSolutions = [];

    public function setUp()
    {
        $this->starter   = new Starter();
        $this->initiator = new TemporaryAutoHookInitiator();
        $this->initiator->setStarter($this->starter);
        $this->vCallback = [$this->initiator->claimView(TemporaryAutoHookInitiatorView::class), 'viewCallbackMethod'];

        $this->callbackParamSolutions = [
            // 12 actions
            'action_plain' => [
                'tag'           => 'plain',
                'callback'      => [$this->initiator, 'action_plain'],
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'action_20_plain_priority' => [
                'tag'           => 'plain_priority',
                'callback'      => [$this->initiator, 'action_20_plain_priority'],
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'action_20_2_plain_priority_args' => [
                'tag'           => 'plain_priority_args',
                'callback'      => [$this->initiator, 'action_20_2_plain_priority_args'],
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => '',
            ],

            'action_20_2_plain_priority_args_directive__directive' => [
                'tag'           => 'plain_priority_args_directive',
                'callback'      => [$this->initiator, 'action_20_2_plain_priority_args_directive__directive'],
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => 'directive',
            ],

            'action_20_plain_directive__directive' => [
                'tag'           => 'plain_directive',
                'callback'      => [$this->initiator, 'action_20_plain_directive__directive'],
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            'action_plain_directive__directive' => [
                'tag'           => 'plain_directive',
                'callback'      => [$this->initiator, 'action_plain_directive__directive'],
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            'v_action_v_callback' => [
                'tag'           => 'v_callback',
                'callback'      => $this->vCallback,
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'v_action_20_v_callback_priority' => [
                'tag'           => 'v_callback_priority',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'v_action_20_2_v_callback_priority_args' => [
                'tag'           => 'v_callback_priority_args',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => '',
            ],

            'v_action_20_2_v_callback_priority_args_directive__directive' => [
                'tag'           => 'v_callback_priority_args_directive',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => 'directive',
            ],

            'v_action_20_v_callback_directive__directive' => [
                'tag'           => 'v_callback_directive',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            'v_action_v_callback_directive__directive' => [
                'tag'           => 'v_callback_directive',
                'callback'      => $this->vCallback,
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            // 12 filters

            'filter_plain' => [
                'tag'           => 'plain',
                'callback'      => [$this->initiator, 'filter_plain'],
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'filter_20_plain_priority' => [
                'tag'           => 'plain_priority',
                'callback'      => [$this->initiator, 'filter_20_plain_priority'],
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'filter_20_2_plain_priority_args' => [
                'tag'           => 'plain_priority_args',
                'callback'      => [$this->initiator, 'filter_20_2_plain_priority_args'],
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => '',
            ],

            'filter_20_2_plain_priority_args_directive__directive' => [
                'tag'           => 'plain_priority_args_directive',
                'callback'      => [$this->initiator, 'filter_20_2_plain_priority_args_directive__directive'],
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => 'directive',
            ],

            'filter_20_plain_directive__directive' => [
                'tag'           => 'plain_directive',
                'callback'      => [$this->initiator, 'filter_20_plain_directive__directive'],
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            'filter_plain_directive__directive' => [
                'tag'           => 'plain_directive',
                'callback'      => [$this->initiator, 'filter_plain_directive__directive'],
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            'v_filter_v_callback' => [
                'tag'           => 'v_callback',
                'callback'      => $this->vCallback,
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'v_filter_20_v_callback_priority' => [
                'tag'           => 'v_callback_priority',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'v_filter_20_2_v_callback_priority_args' => [
                'tag'           => '',
                'callback'      => $this->vCallback,
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => '',
            ],

            'v_filter_20_2_v_callback_priority_args_directive__directive' => [
                'tag'           => 'v_callback_priority_args_directive',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 2,
                'directive'     => 'directive',
            ],

            'v_filter_20_v_callback_directive__directive' => [
                'tag'           => 'v_callback_directive',
                'callback'      => $this->vCallback,
                'priority'      => 20,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            'v_filter_v_callback_directive__directive' => [
                'tag'           => 'v_callback_directive',
                'callback'      => $this->vCallback,
                'priority'      => 10,
                'accepted_args' => 1,
                'directive'     => 'directive',
            ],

            // 4 shortcodes

            'shortcode_plain' => [
                'tag'       => 'plain',
                'callback'  => [$this->initiator, 'shortcode_plain'],
                'directive' => '',
            ],

            'shortcode_plain_directive__directive' => [
                'tag'       => 'plain_directive',
                'callback'  => [$this->initiator, 'shortcode_plain_directive__directive'],
                'directive' => 'directive',
            ],

            'v_shortcode_v_callback' => [
                'tag'       => 'v_callback',
                'callback'  => $this->vCallback,
                'directive' => '',
            ],

            'v_shortcode_v_callback_directive__directive' => [
                'tag'       => 'v_callback_directive',
                'callback'  => $this->vCallback,
                'directive' => 'directive',
            ],

            // 4 activation

            'activation' => [
                'callback'  => [$this->initiator, 'activation'],
                'directive' => '',
            ],

            'activation__directive' => [
                'callback'  => [$this->initiator, 'activation__directive'],
                'directive' => 'directive',
            ],

            'v_activation' => [
                'callback'  => $this->vCallback,
                'directive' => '',
            ],

            'v_activation__directive' => [
                'callback'  => $this->vCallback,
                'directive' => 'directive',
            ],

            // 4 deactivation

            'deactivation' => [
                'callback'  => [$this->initiator, 'deactivation'],
                'directive' => '',
            ],

            'deactivation__directive' => [
                'callback'  => [$this->initiator, 'deactivation__directive'],
                'directive' => 'directive',
            ],

            'v_deactivation' => [
                'callback'  => $this->vCallback,
                'directive' => '',
            ],

            'v_deactivation__directive' => [
                'callback'  => $this->vCallback,
                'directive' => 'directive',
            ],
        ];
    }

    /**
     * AutoHookInitiator::getMethods() 테스트
     *
     * @see AutoHookInitiator::getMethods()
     */
    public function testGetMethods()
    {
        $reflection  = makeMethodAccessible(TemporaryAutoHookInitiator::class, 'getMethods');
        $methods     = $reflection->invoke($this->initiator);
        $methodNames = array_keys($this->callbackParamSolutions);
        $this->assertEquals($methodNames, array_intersect($methods, $methodNames));
    }

    /**
     * AutoHookInitiator::getCallbackParams() 테스트
     *
     * @see AutoHookInitiator::getCallbackParams()
     */
    public function testGetCallbackParams()
    {
        $reflection = makeMethodAccessible(TemporaryAutoHookInitiator::class, 'getCallbackParams');
        $params     = $reflection->invoke($this->initiator);

        reset($this->callbackParamSolutions);

        # 검증: add_action
        $this->assertArrayHasKey('add_action', $params);
        $this->assertEquals(12, sizeof($params['add_action']));
        foreach ($params['add_action'] as $param) {
            $key = key($this->callbackParamSolutions);
            // error_log($key);
            $solution = current($this->callbackParamSolutions);
            $this->assertEquals($solution, $param);
            next($this->callbackParamSolutions);
        }

        # 검증: add_filter
        $this->assertArrayHasKey('add_filter', $params);
        $this->assertEquals(12, sizeof($params['add_filter']));
        foreach ($params['add_filter'] as $index => $param) {
            $key = key($this->callbackParamSolutions);
            // error_log($key);
            $solution = current($this->callbackParamSolutions);
            next($this->callbackParamSolutions);
        }

        # 검증: add_shortcode
        $this->assertArrayHasKey('add_shortcode', $params);
        $this->assertEquals(4, sizeof($params['add_shortcode']));
        foreach ($params['add_shortcode'] as $index => $param) {
            $key = key($this->callbackParamSolutions);
            // error_log($key);
            $solution = current($this->callbackParamSolutions);
            $this->assertEquals($solution, $param);
            next($this->callbackParamSolutions);
        }

        # 검증: register_activation_hook
        $this->assertArrayHasKey('register_activation_hook', $params);
        $this->assertEquals(4, sizeof($params['register_activation_hook']));
        foreach ($params['register_activation_hook'] as $index => $param) {
            $key = key($this->callbackParamSolutions);
            // error_log($key);
            $solution = current($this->callbackParamSolutions);
            $this->assertEquals($solution, $param);
            next($this->callbackParamSolutions);
        }

        # 검증: register_deactivation_hook
        $this->assertArrayHasKey('register_deactivation_hook', $params);
        $this->assertEquals(4, sizeof($params['register_deactivation_hook']));
        foreach ($params['register_deactivation_hook'] as $index => $param) {
            $key = key($this->callbackParamSolutions);
            // error_log($key);
            $solution = current($this->callbackParamSolutions);
            $this->assertEquals($solution, $param);
            next($this->callbackParamSolutions);
        }

        reset($this->callbackParamSolutions);
    }

    public function testAddHookDirectives()
    {
        $methodRef = makeMethodAccessible(AutoHookInitiator::class, 'addHookDirective');
        $propRef   = makePropertyAccessible(AutoHookInitiator::class, 'hookDirectives');

        $initiator = new AutoHookInitiator();
        $methodRef->invoke($initiator, 'test_directive', '__return_true');
        $directives = $propRef->getValue($initiator);

        $this->assertIsArray($directives);
        $this->assertArrayHasKey('test_directive', $directives);
        $this->assertEquals('__return_true', $directives['test_directive']);
    }

    public function testReplaceHook()
    {
        $testCase          = &$this;
        $addReplacementRef = makeMethodAccessible(AutoHookInitiator::class, 'addReplacement');
        $replaceHookRef    = makeMethodAccessible(AutoHookInitiator::class, 'replaceHook');
        $initiator         = new AutoHookInitiator();

        // 테스트 #1: 문자열로 테스트
        $addReplacementRef->invoke($initiator, 'post_post_id_name', 'post_388_name');
        $replaced = $replaceHookRef->invoke($initiator, 'post_post_id_name');
        // 검증 #1
        $this->assertEquals('post_388_name', $replaced);

        // 테스트 #2: 콜백으로 테스트
        $callbackInvoked = false;
        $addReplacementRef->invoke(
            $initiator,
            'post_post_id_name',
            function ($namePart, $instance) use ($initiator, $testCase, &$callbackInvoked) {
                $callbackInvoked = true;
                $testCase->assertEquals($initiator, $instance);
                $testCase->assertIsString('post_post_id_name', $namePart);
                return 'callback_replaced_this';
            }
        );
        $replaced = $replaceHookRef->invoke($initiator, 'post_post_id_name');
        $this->assertEquals('callback_replaced_this', $replaced);

        // 테스트 #3: 없는 문자열은 그대로 반환되어야 함
        $this->assertEquals('open_sesame', $replaceHookRef->invoke($initiator, 'open_sesame'));
        $this->assertTrue($callbackInvoked);
    }
}

class TemporaryAutoHookInitiator extends AutoHookInitiator
{
    // Action     ////////////////////////////////////////////////

    /** 평범한 액션 */
    public function action_plain()
    {
    }

    /** 평범한 액션, 우선순위 */
    public function action_20_plain_priority()
    {
    }

    /** 평범한 액션, 우선순위, 인자 수 */
    public function action_20_2_plain_priority_args()
    {
    }

    /** 평범한 액션, 우선순위, 인자 수, 디렉티브 */
    public function action_20_2_plain_priority_args_directive__directive()
    {
    }

    /** 평범한 액션, 우선순위, 디렉티브 */
    public function action_20_plain_directive__directive()
    {
    }

    /** 평범한 액션, 디렉티브 */
    public function action_plain_directive__directive()
    {
    }

    /** v-액션 */
    public function v_action_v_callback()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-액션, 우선순위 */
    public function v_action_20_v_callback_priority()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-액션, 우선순위, 인자 수 */
    public function v_action_20_2_v_callback_priority_args()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-액션, 우선순위, 인자 수, 디렉티브 */
    public function v_action_20_2_v_callback_priority_args_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-액션, 우선순위, 디렉티브 */
    public function v_action_20_v_callback_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-액션, 디렉티브 */
    public function v_action_v_callback_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }


    // Filter     ////////////////////////////////////////////////

    /** 평범한 필터 */
    public function filter_plain()
    {
    }

    /** 평범한 필터, 우선순위 */
    public function filter_20_plain_priority()
    {
    }

    /** 평범한 필터, 우선순위, 인자 수 */
    public function filter_20_2_plain_priority_args()
    {
    }

    /** 평범한 필터, 우선순위, 인자 수, 디렉티브 */
    public function filter_20_2_plain_priority_args_directive__directive()
    {
    }

    /** 평범한 필터, 우선순위, 디렉티브 */
    public function filter_20_plain_directive__directive()
    {
    }

    /** 평범한 필터, 디렉티브 */
    public function filter_plain_directive__directive()
    {
    }

    /** v-필터 */
    public function v_filter_v_callback()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-필터, 우선순위 */
    public function v_filter_20_v_callback_priority()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-필터, 우선순위, 인자 수 */
    public function v_filter_20_2_v_callback_priority_args()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-필터, 우선순위, 인자 수, 디렉티브 */
    public function v_filter_20_2_v_callback_priority_args_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-필터, 우선순위, 디렉티브 */
    public function v_filter_20_v_callback_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-필터, 디렉티브 */
    public function v_filter_v_callback_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }


    // Shortcode  ////////////////////////////////////////////////

    /** 평범한 쇼트코드 */
    public function shortcode_plain()
    {
    }

    /** 평범한 쇼트코드, 디렉티브 */
    public function shortcode_plain_directive__directive()
    {
    }

    /** v-쇼트코드 */
    public function v_shortcode_v_callback()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** v-쇼트코드, 디렉티브 */
    public function v_shortcode_v_callback_directive__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }


    // Activation ////////////////////////////////////////////////

    /** 평범한 활성화 훅 */
    public function activation()
    {
    }

    /** 평범한 활성화 훅과 디렉티브 */
    public function activation__directive()
    {
    }

    /** 활성화 v 콜백 */
    public function v_activation()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** 활성화 v 콜백과 디렉티브 */
    public function v_activation__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    // Deactivation ////////////////////////////////////////////////

    /** 평범한 비활성화 훅 */
    public function deactivation()
    {
    }

    /** 평범한 비활성화 훅과 디렉티브 */
    public function deactivation__directive()
    {
    }

    /** 비활성화 v 콜백 */
    public function v_deactivation()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }

    /** 비활성화 v 콜백과 디렉티브 */
    public function v_deactivation__directive()
    {
        return [TemporaryAutoHookInitiatorView::class, 'viewCallbackMethod'];
    }
}

/**
 * Class TemporaryAutoHookInitiatorView
 *
 * V-callback 처리를 위한 임시 뷰 클래스
 *
 * @package Shoplic\Axis3\Tests\Initiators
 */
class TemporaryAutoHookInitiatorView extends BaseView
{
    public function viewCallbackMethod()
    {
    }
}