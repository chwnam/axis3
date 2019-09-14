<?php

namespace Shoplic\Axis3\Interfaces\Starters\ClassFinders;

use Shoplic\Axis3\Interfaces\Starters\StarterInterface;

/**
 * Interface ClassFinderInterface
 *
 * 클래스 검색자(Class Finder) 인터페이스.
 * 개시자(Starter)에서 관리해야 할 콤포넌트를 찾아주는 역할을 합니다.
 *
 * @package Shoplic\Axis3\Interfaces\Starters\ClassFinders
 * @since   1.0.0
 */
interface ClassFinderInterface
{
    /**
     * 클래스 검색을 수행한다.
     *
     * @var array $foundClasses 찾은 클래스 목록. 키: 콘텍스트, 값: FQCN 목록.
     *                          여러 클래스 검색자가 연결되므로 입력을 레퍼런스로 전달, 직접 배열을 수정한다.
     *
     * @return void
     */
    public function find(array &$foundClasses);

    /**
     * 콤포넌트 접미사를 리턴
     * @return string
     */
    public function getComponentPostfix(): string;

    /**
     * 이 객체의 관심 콤포넌트 접두사를 설정
     *
     * @param string $postfix
     *
     * @return self
     */
    public function setComponentPostfix(string $postfix);

    /**
     * 개시자를 리턴
     *
     * @return StarterInterface
     */
    public function getStarter(): StarterInterface;

    /**
     * 이 객체가 속한 개시자 지정
     *
     * @param StarterInterface $starter
     *
     * @return self
     */
    public function setStarter(StarterInterface $starter);

    /**
     * 검색 판단 콜백 지정
     *
     * find() 메소드 안에서 현재 검색자가 검색을 해도 좋은지 아닌지를 판단한다.
     * 기본은 null 로 판단을 하지 않고 언제나 find() 를 완료한다.
     *
     * @param callable $findConditionCallback 조건을 판단할 요소. 객체에서 호출할 것이다.
     *                                        검색자를 인자로 받고, 불리언을 리턴해야 한다.
     *
     * @return self
     */
    public function setFindConditionCallback(callable $findConditionCallback);
}
