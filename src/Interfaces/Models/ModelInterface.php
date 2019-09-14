<?php

namespace Shoplic\Axis3\Interfaces\Models;

use Shoplic\Axis3\Interfaces\Objects\AxisObjectInterface;
use wpdb;

/**
 * Interface ModelInterface
 *
 * 모델 콤포넌트는 커스텀 포스트, 택소노미, 역할/권한, 커스텀 필드 등 플러그인에서 취급하는
 * 데이터와 밀접한 관련을 가지는 콬포넌트입니다.
 *
 * @package Shoplic\Axis3\Interfaces\Models
 * @since   1.0.0
 */
interface ModelInterface extends AxisObjectInterface
{
    /**
     * 워드프레스 DB 객체를 리턴.
     *
     * @return wpdb;
     */
    public static function getWpdb();
}
