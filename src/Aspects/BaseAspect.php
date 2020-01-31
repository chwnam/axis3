<?php

namespace Shoplic\Axis3\Aspects;

use Shoplic\Axis3\Interfaces\Aspects\AspectInterface;
use Shoplic\Axis3\Objects\AxisObject;

/**
 * Class BaseAspect
 *
 * 기본 애즈펙트
 *
 * @package Shoplic\Axis3\Aspects
 */
class BaseAspect extends AxisObject implements AspectInterface
{
    public function setup($args = array())
    {
    }
}
