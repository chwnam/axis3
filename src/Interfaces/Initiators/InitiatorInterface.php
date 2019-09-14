<?php

namespace Shoplic\Axis3\Interfaces\Initiators;

use Shoplic\Axis3\Interfaces\Objects\AxisObjectInterface;

/**
 * Interface InitiatorInterface
 *
 * 전수자 (initiator) 콤포넌트입니다.
 * 모든 액션/필터의 콜백을 주로 담당하며, 단순한 콜백은 클래스 내부에서 직접적으로 처리할 수 있습니다.
 *
 * @package Shoplic\Axis3\Interfaces\Initiators
 * @since   1.0.0
 */
interface InitiatorInterface extends AxisObjectInterface
{
    /**
     * 액션과 필터는 이 메소드에서 처리합니다.
     *
     * @return void
     */
    public function initHooks();
}
