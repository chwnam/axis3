<?php

namespace Shoplic\Axis3\Starters\ClassFinders;

/**
 * Class SimpleListClassFinder
 *
 * 단순하게 입력한 FQCN 만 돌려주는 검색자.
 * 개시자에 명시적으로 특정 클래스만 넣어줄 때 유용하다.
 * 프로그래머가 명시적으로 목록을 넣어주기 때문에 콤포넌트의 접미사는 신경쓰지 않는다.
 *
 * @package Shoplic\Axis3\Starters\ClassFinders
 * @since   1.0.0
 */
class SimpleListClassFinder extends BaseClassFinder
{
    /** @var array 내부에 저장된 클래스 배열. 키: 실행 콘텍스트, 값: FQCN 배열 */
    private $classes = [];

    public function find(array &$foundClasses)
    {
        if ($this->checkFindCondition()) {
            foreach ($this->classes as $context => $classes) {
                if (!isset($foundClasses[$context])) {
                    $foundClasses[$context] = [];
                }
                $foundClasses[$context] = array_merge($foundClasses[$context], $classes);
            }
        }
    }

    /**
     * 클래스를 추가한다. 검색자 내부에서 FQCN 의 중복을 관리하지는 않는다.
     *
     * @param string $context
     * @param array  $classes
     *
     * @uses \Shoplic\Axis3\Starters\ClassFinders\BaseClassFinder::trimNamespace()
     */
    public function addClasses(string $context, array $classes)
    {
        if (!isset($this->classes[$context])) {
            $this->classes[$context] = [];
        }

        $this->classes[$context] = array_merge(
            $this->classes[$context],
            array_map([$this, 'trimNamespace'], $classes)
        );
    }
}
