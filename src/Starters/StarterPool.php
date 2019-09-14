<?php

namespace Shoplic\Axis3\Starters;

use Shoplic\Axis3\Interfaces\Starters\StarterInterface;
use Exception;

/**
 * Class StarterPool
 *
 * 개시자 풀. 모든 개시자의 인스턴스는 이 곳에 보관됩니다.
 *
 * Axis 3 가장 외곽에 존재하는 요소입니다. 플러그인 밖에서 플러그인의 액션/필터를 피치 못할 커스텀 요구 사항에 의해
 * 수정해야만 하는 경우에 최후의 보루로써 제공하기 위한 목적을 가지고 있습니다.
 * 그러므로 최대한 가능하면 Axis 3를 이용해서 개발하는 플러그인 내부에서는 개시자 인스턴스와 개시자의 풀의 존재를
 * 의식하지 말고, 개시자는 서로 독립, 분리하여 서로 간섭하지 않도록 하세요.
 *
 * @package Shoplic\Axis3\Starters
 * @since   1.0.0
 */
class StarterPool
{
    /** @var self */
    private static $instance = null;

    /** @var StarterInterface[] */
    private $pool = [];

    private function __construct()
    {
    }

    /**
     * 풀 인스턴스 반환.
     *
     * @return self
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * 개시자를 추가합니다.
     *
     * @param StarterInterface $starter 개시자 인스턴스
     *
     * @throws Exception 개시자는 반드시 문자열로 된 슬러그가 등록되어야 합니다.
     *                   적절하지 않은 슬러그를 가진 경우 예외를 던집니다.
     */
    public function addStarter(StarterInterface $starter)
    {
        if (!$starter->getSlug() || !is_string($starter->getSlug())) {
            throw new Exception(__('A starter must have a string-typed slug.', 'axis3'));
        }

        $this->pool[$starter->getSlug()] = $starter;
    }

    /**
     * 개시자를 반환합니다.
     *
     * @param string $slug
     *
     * @return StarterInterface|null
     */
    public function getStarter(string $slug)
    {
        return $this->pool[$slug] ?? null;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function __wakeup()
    {
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function __sleep()
    {
    }
}
