<?php

namespace Shoplic\Axis3\Starters\ClassFinders;

use Shoplic\Axis3\Interfaces\Starters\ClassFinders\ClassFinderInterface;
use Shoplic\Axis3\Interfaces\Starters\StarterInterface;

/**
 * Class BaseClassFinder
 *
 * 기본 클래스 검색자
 *
 * @package Shoplic\Axis3\Starters\ClassFinders
 * @since   1.0.0
 */
abstract class BaseClassFinder implements ClassFinderInterface
{
    /**
     * @var callable|null find() 메소드 안에서 현재 검색자가 검색을 해도 좋은지 아닌지를 판단한다.
     *                    기본은 null 로 판단을 하지 않고 언제나 find() 를 완료한다.
     *                    callable 로 전달하는 콜백 함수는 검색자를 인자로 받고, 불리언을 리턴해야 한다.
     */
    protected $findConditionCallback = null;

    /**
     * @var string 파일이 어떤 콤포넌트인지는 파일의 마지막 접미로 구분한다.
     *             구분의 기준이 되는 접미를 기록한다.
     */
    private $postfix = '';

    /**
     * @var StarterInterface 검색자가 속한 개시자
     */
    private $starter = null;

    public function getComponentPostfix(): string
    {
        return $this->postfix;
    }

    public function setComponentPostfix(string $postfix)
    {
        $this->postfix = ucfirst($postfix);

        return $this;
    }

    public function getStarter(): StarterInterface
    {
        return $this->starter;
    }

    public function setStarter(StarterInterface $starter)
    {
        $this->starter = $starter;

        return $this;
    }

    public function setFindConditionCallback(callable $findConditionCallback)
    {
        $this->findConditionCallback = $findConditionCallback;

        return $this;
    }

    /**
     * find() 메소드 안에서 현재 검색자가 검색을 해도 좋은지 아닌지를 판단한다.
     *
     * @return bool
     */
    protected function checkFindCondition(): bool
    {
        return !$this->findConditionCallback || call_user_func($this->findConditionCallback, $this);
    }

    /**
     * FQCN 문자열의 가장 앞에 있는 백슬래시를 제거한다.
     *
     * @param string $class
     *
     * @return string
     */
    protected function trimNamespace(string $class): string
    {
        return ltrim($class, '\\');
    }
}
