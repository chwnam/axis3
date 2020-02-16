<?php

namespace Shoplic\Axis3\Initiators;

/**
 * Class SimpleInitiator
 *
 * 간단한 전수자. 작성할 때 기본 콘텍스트에 둔다.
 *
 * 클래스 내부에서 콘텍스트에 따라 적절히 액션과 필터를 넣어주도록 설계되었고,
 * 크키가 작은 플러그인의 경우 간단하게 한 클래스에서 다양한 처리를 하도록 의도되었다.
 *
 * 단, 한 클래스 안에 모든 액션, 필터가 어우러지면 플러그인의 유지 보수가 어려워진다.
 * 그러므로 액션/필터의 개수가 5~6개 이상 넘어가면 다른 전수자를 사용하는 것을 고려하자.
 *
 * @package Shoplic\Axis3\Initiators
 * @since   1.0.0
 */
class SimpleInitiator extends BaseInitiator
{
    public function initHooks()
    {
        $this->contextHandlerGeneric();

        $context = $this->getStarter()->getCurrentRequestContext();
        if ('Front' === $context && (defined('DOING_AJAX') && DOING_AJAX)) {
            $contexts = ['Front', 'Ajax'];
        } else {
            $contexts = [$context];
        }

        foreach ($contexts as $context) {
            $methodName = 'contextHandler' . ucfirst($context);
            if (method_exists($this, $methodName)) {
                /**
                 * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::contextHandlerAdmin()
                 * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::contextHandlerAjax()
                 * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::contextHandlerAutosave()
                 * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::contextHandlerCron()
                 * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::contextHandlerFront()
                 * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::contextHandlerFrontNoAjax()
                 *
                 * 추가적으로 콘텍스트를 등록했는가? contextHandler{$context} 메소드를 만들면 된다.
                 */
                call_user_func([$this, $methodName]);
            }
        }
    }

    /**
     * 기본 콘텍스트.
     *
     * 상속받는 클래스는 이 메소드를 오버라이드한다.
     * 이 메소드에서 액션/필터를 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     */
    protected function contextHandlerGeneric()
    {
    }

    /**
     * 관리자 화면 콘텍스트.
     *
     * 관리자 화면에서만 동작할 액션/필터를 여기에서 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     */
    protected function contextHandlerAdmin()
    {
    }

    /**
     * AJAX 콘텍스트.
     *
     * AJAX 동작 중일 때만 동작할 액션/필터를 여기에서 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     *
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     */
    protected function contextHandlerAjax()
    {
    }

    /**
     * 자동 저장 콘텍스트
     *
     * 자동 저장 중일 때만 동작할 액션/필터를 여기에서 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     */
    protected function contextHandlerAutosave()
    {
    }

    /**
     * 크론 콘텍스트
     *
     * 크론 동작 중일 때만 동작할 액션/필터를 여기에서 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     */
    protected function contextHandlerCron()
    {
    }

    /**
     * 전면 페이지 콘텍스트
     *
     * 전면 페이지 동작 중일 때만 동작할 액션/필터를 여기에서 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     */
    protected function contextHandlerFront()
    {
    }

    /**
     * 전면 페이지 (AJAX 제외) 콘텍스트
     *
     * AJAX 처리 또한 Front 콘텍스트라고 해석한다. AJAX 까지 제외한 완전 프론트 레이어에서만 일어나야 할
     * 액션/필터를 여기에서 등록한다.
     *
     * @return void
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addAction()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addFilter()
     * @uses \Shoplic\Axis3\Initiators\SimpleInitiator::addShortcode()
     */
    protected function contextHandlerFrontNoAjax()
    {
    }

    /**
     * @param string                $tag
     * @param array|string|callable $callback
     * @param int                   $priority
     * @param int                   $acceptedArgs
     *
     * @return $this
     * @see    BaseInitiator::formatArgument() $callback 인자로 허용되는 값은 이 메소드를 참고
     */
    protected function addAction(string $tag, $callback, int $priority = 10, int $acceptedArgs = 1)
    {
        $formatted = $this->formatArgument($callback);
        if ($formatted) {
            add_action($tag, $formatted, $priority, $acceptedArgs);
        }

        return $this;
    }

    /**
     * @param string                $tag
     * @param array|string|callable $callback
     * @param int                   $priority
     * @param int                   $acceptedArgs
     *
     * @return $this
     * @see    BaseInitiator::formatArgument() $callback 인자로 허용되는 값은 이 메소드를 참고
     */
    protected function addFilter(string $tag, $callback, int $priority = 10, int $acceptedArgs = 1)
    {
        $formatted = $this->formatArgument($callback);
        if ($formatted) {
            add_filter($tag, $formatted, $priority, $acceptedArgs);
        }

        return $this;
    }

    /**
     * @param string                $tag
     * @param array|string|callable $callback
     *
     * @return $this
     * @see    BaseInitiator::formatArgument() $callback 인자로 허용되는 값은 이 메소드를 참고
     */
    protected function addShortcode(string $tag, $callback)
    {
        $formatted = $this->formatArgument($callback);
        if ($formatted) {
            add_shortcode($tag, $formatted);
        }

        return $this;
    }

    /**
     * 활성화 콜백 등록
     *
     * @param $callback
     *
     * @return $this
     *
     * @see BaseInitiator::formatArgument()
     */
    protected function registerActivationHook($callback)
    {
        $formatted = $this->formatArgument($callback);

        if ($formatted) {
            register_activation_hook($this->getStarter()->getMainFile(), $formatted);
        }

        return $this;
    }

    /**
     * 비활성화 콜백 등록
     *
     * @param string|array|$callback 콜백 함수.
     *
     * @return self
     *
     * @see BaseInitiator::formatArgument()
     */
    protected function registerDeactivationHook($callback)
    {
        $formatted = $this->formatArgument($callback);

        if ($formatted) {
            register_deactivation_hook($this->getStarter()->getMainFile(), $formatted);
        }

        return $this;
    }
}
