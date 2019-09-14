<?php

namespace Shoplic\Axis3\Models;

use Shoplic\Axis3\Interfaces\Models\ModelInterface;
use Shoplic\Axis3\Objects\AxisObject;

/**
 * Class BaseModel
 *
 * 기본 모델 클래스
 *
 * @package Shoplic\Axis3\Models
 * @since   1.0.0
 */
class BaseModel extends AxisObject implements ModelInterface
{
    public function setup($args = [])
    {
    }

    public static function getWpdb()
    {
        global $wpdb;
        return $wpdb;
    }
}
