<?php

namespace Shoplic\Axis3\Initiators;

use Shoplic\Axis3\Interfaces\Initiators\InitiatorInterface;
use Shoplic\Axis3\Objects\AxisObject;

/**
 * Class BaseInitiator
 *
 * 기본 전수자입니다.
 *
 * @package Shoplic\Axis3\Initiators
 * @since   1.0.0
 */
abstract class BaseInitiator extends AxisObject implements InitiatorInterface
{
    public function setup($args = array())
    {
    }

    /**
     * 콜백 인자를 해석한다.
     *
     * 가능한 포맷:
     *   - 배열:
     *       - [ $obj, 'methodName' ]                       전형적인 콜백
     *       - [ SomeComponent::class, 'methodName' ]       Axis 3 오브젝트 구현체면 가능
     *    - 문자열, 객체:                                   호출가능하다고 판단하면 그대로 리턴
     *
     * @param array|string|callable $arg 호출 가능한 형태, 불정리할 콜백 인자. 포맷은 설명을 참조.
     *
     * @return array|callable|null 정리된 콜백
     *
     * @uses   \Shoplic\Axis3\Functions\classImplements()
     */
    protected function formatArgument($arg)
    {
        if (is_array($arg) && sizeof($arg) === 2 && is_string($arg[0])) {
            $arg = [$this->claimView($arg[0]), $arg[1]];
        }

        return $arg;
    }
}
