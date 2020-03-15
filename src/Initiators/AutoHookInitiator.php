<?php

namespace Shoplic\Axis3\Initiators;

use function Shoplic\Axis3\Functions\strStartsWith;

/**
 * Class AutoHookInitiator
 *
 * 자동 훅 전수자
 * Axis 3 프레임워크의 핵심 콤포넌트 요소입니다.
 * 워드프레스에서 사용되는 액션, 필터, 쇼트코드, 활성/비활성화 콜백에 대해 규칙에 맞춰
 * 메소드 이름을 만들면 훅이 자동으로 걸리는 시스템을 가지고 있습니다.
 *
 * @package Shoplic\Axis3\Initiators
 * @version 0.1.0
 */
class AutoHookInitiator extends BaseInitiator
{
    /** @var string[] 여기 메소드는 베이스 클래스에서 정의되어 있어서 고려하지 않아도 됩니다. */
    protected static $noHookMethodNames = [
        '__construct',
        'addHookDirective',
        'addReplacement',
        'claimAspect',
        'claimModel',
        'claimView',
        'fallbackDoNothing',
        'formatArgument',
        'getCallbackParams',
        'getMethods',
        'getStarter',
        'getViewCallback',
        'handleDirective',
        'handleDirectiveAllowNopriv',
        'initHooks',
        'replaceHook',
        'setStarter',
        'setup',
    ];

    /** @var array 훅 디렉티브. 키는 디렉티브 문자열. 값은 호출 가능한 메소드, 함수. */
    private $hookDirectives = [];

    /** @var array 훅 이름 치환 정보. */
    private $hookReplacements = [];

    public function __construct()
    {
        $this->hookDirectives = [
            'allow_nopriv' => [$this, 'handleDirectiveAllowNopriv'],
        ];
    }

    /**
     * 훅 등록
     *
     * @uses \Shoplic\Axis3\Initiators\AutoHookInitiator::getCallbackParams()
     */
    public function initHooks()
    {
        foreach ($this->getCallbackParams() as $function => $callbackParams) {
            foreach ($callbackParams as $callbackParam) {
                switch ($function) {
                    case 'add_action':
                        add_action(
                            $callbackParam['tag'],
                            $callbackParam['callback'],
                            $callbackParam['priority'],
                            $callbackParam['accepted_args']
                        );
                        break;

                    case 'add_filter':
                        add_filter(
                            $callbackParam['tag'],
                            $callbackParam['callback'],
                            $callbackParam['priority'],
                            $callbackParam['accepted_args']
                        );
                        break;

                    case 'add_shortcode':
                        add_shortcode($callbackParam['tag'], $callbackParam['callback']);
                        break;

                    case 'register_activation_hook':
                        register_activation_hook($this->getStarter()->getMainFile(), $callbackParam['callback']);
                        break;

                    case 'register_deactivation_hook':
                        register_deactivation_hook($this->getStarter()->getMainFile(), $callbackParam['callback']);
                        break;
                }
                $this->handleDirective($function, $callbackParam);
            }
        }
    }

    /**
     * 이 클래스에서 가져올 수 있는 메소드 목록을 리턴
     *
     * @return string[]
     */
    protected function getMethods()
    {
        return array_diff(get_class_methods($this), self::$noHookMethodNames);
    }

    /**
     * 현재 클래스에 정의된 액션/필터/쇼트코드/활성화/비활성화 동작을 파악한다.
     *
     * @return array
     */
    public function getCallbackParams()
    {
        $patterns = [
            //                                                    p.       a.      h.      d.
            //                              1    2              3 4      5 6       7    8  9
            'action|filter'           => '/^(v_)?(action|filter)(_(\d+))?(_(\d+))?_(.+?)(__(.+))?$/',

            //                              1              2    3  4
            'shortcode'               => '/^(v_)?shortcode_(.+?)(__(.+))?$/',

            //                              1    2                        3  4
            'activation|deactivation' => '/^(v_)?(activation|deactivation)(__(.+))?$/',
        ];

        $output = [
            'add_action'                 => [],
            'add_filter'                 => [],
            'add_shortcode'              => [],
            'register_activation_hook'   => [],
            'register_deactivation_hook' => [],
        ];

        foreach ($this->getMethods() as $method) {
            if (2 >= strlen($method) || substr($method, 0, 1) === '_') {
                continue;
            }
            foreach ($patterns as $type => $pattern) {
                $match = [];
                if (preg_match($pattern, $method, $match)) {
                    switch ($type) {
                        case 'action|filter':
                            $output["add_{$match[2]}"][] = [
                                'tag'           => $this->replaceHook($match[7]),
                                'callback'      => $this->getViewCallback(($match[1] === 'v_'), $method),
                                'priority'      => $match[4] ? intval($match[4]) : 10,
                                'accepted_args' => $match[6] ? intval($match[6]) : 1,
                                'directive'     => $match[9] ?? '',
                            ];
                            break 2;

                        case 'shortcode':
                            $output["add_shortcode"][] = [
                                'tag'       => $this->replaceHook($match[2]),
                                'callback'  => $this->getViewCallback(($match[1] === 'v_'), $method),
                                'directive' => $match[4] ?? '',
                            ];
                            break 2;

                        case 'activation|deactivation':
                            $output["register_{$match[2]}_hook"][] = [
                                'callback'  => $this->getViewCallback(($match[1] === 'v_'), $method),
                                'directive' => $match[4] ?? '',
                            ];
                            break 2;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * 디렉티브를 추가한다.
     *
     * @param string   $directive 디렉티브 이름.
     * @param callable $callback  콜백. 인자는 2개로 하나는 이 디렉티브가 걸린 워드프레스 함수 이름.
     *                            나머지 하나는 분석된 파라미터 정보
     *
     * @see AutoHookInitiator::getCallbackParams()
     */
    protected function addHookDirective(string $directive, callable $callback)
    {
        $this->hookDirectives[$directive] = $callback;
    }

    /**
     * 메소드에 적은 훅 이름을 실제 훅 이름으로 변경하도록 미리 등록한다.
     *
     * @param string          $methodNamePart 메소드에 적힌 부분
     * @param string|callable $convertTo      실제 변경할 부분. 단순 문자열도 가능하고, 콜백함수도 가능하다.
     *                                        콜백 함수라면 메소드에 적힌 고정된 부분과 현재 객체를 받는 함수.
     *                                        이 함수는 반드시 문자열을 리턴해야 한다.
     *
     * @see AutoHookInitiator::replaceHook()
     */
    protected function addReplacement(string $methodNamePart, $convertTo)
    {
        $this->hookReplacements[$methodNamePart] = $convertTo;
    }

    /**
     * 메소드에 적힌 훅 이름을 실제 훅 이름으로 교체한다.
     *
     * 예시)
     *       public function action_post_post_id_sample_hook() { ... }
     *
     * 위 메소드의 액션 태그는 'post_post_id_sample_hook' 이다.
     * 한편 미리 아래처럼 교체 내용을 등록했다고 하자.
     *
     *       $this->addReplacement( 'post_post_id_sample_hook', "post_{$postId}_sample-hook" );
     *
     * 그러면 실제로는 add_action( "post_{$postId}_sample-hook", ... ); 로 코어에 등록된다.
     *
     * @param string $methodNamePart 메소드에 적힌 훅 이름
     *
     * @return string
     */
    protected function replaceHook(string $methodNamePart)
    {
        if (isset($this->hookReplacements[$methodNamePart])) {
            if (is_callable($this->hookReplacements[$methodNamePart])) {
                return call_user_func_array($this->hookReplacements[$methodNamePart], [$methodNamePart, &$this]);
            } else {
                return $this->hookReplacements[$methodNamePart];
            }
        }

        return $methodNamePart;
    }

    /**
     * 뷰 콜백을 리턴.
     *
     * @param bool   $isVCallback v 콜백인지 판단.
     * @param string $method      메소드 이름
     *
     * @return array
     * @uses   \Shoplic\Axis3\Initiators\AutoHookInitiator::fallbackDoNothing()
     */
    protected function getViewCallback(bool $isVCallback, string $method)
    {
        if ($isVCallback) {
            return $this->formatArgument(call_user_func([$this, $method]));
        } else {
            return [$this, $method];
        }
    }

    /**
     * 디렉티브 핸들러: allow_nopriv
     * wp_ajax_*    액션을 자동으로 wp_ajax_nopriv_* 에서도 동작하게 만든다.
     * admin_post_* 액션을 자동으로 admin_post_nopriv_* 에서도 동작하게 만든다.
     *
     * @param string $function
     * @param        $callbackParam
     */
    protected function handleDirectiveAllowNopriv(string $function, &$callbackParam)
    {
        if ($function === 'add_action') {
            if (strStartsWith($callbackParam['tag'], 'wp_ajax_')) {
                $lim = 1;
                $tag = str_replace('wp_ajax_', 'wp_ajax_nopriv_', $callbackParam['tag'], $lim);
                add_action(
                    $tag, $callbackParam['callback'],
                    $callbackParam['priority'],
                    $callbackParam['accepted_args']
                );
            } elseif (strStartsWith($callbackParam['tag'], 'admin_post_')) {
                $lim = 1;
                $tag = str_replace('admin_post_', 'admin_post_nopriv_', $callbackParam['tag'], $lim);
                add_action(
                    $tag, $callbackParam['callback'],
                    $callbackParam['priority'],
                    $callbackParam['accepted_args']
                );
            }
        }
    }

    /**
     * 디렉티브를 처리한다.
     *
     * @param string $function      어떤 함수를 불렀는지 나타냄. add_action(), add_filter(), ...
     * @param array  $callbackParam 함수 인자 정보
     *
     * @uses  \Shoplic\Axis3\Initiators\AutoHookInitiator::handleDirectiveAllowNopriv()
     */
    private function handleDirective(string $function, array &$callbackParam)
    {
        $directive = $callbackParam['directive'] ?? null;
        if ($directive) {
            $directiveMethod = $this->hookDirectives[$directive] ?? null;
            if ($directiveMethod && is_callable($directiveMethod)) {
                call_user_func_array($directiveMethod, [$function, &$callbackParam]);
            }
        }
    }
}
