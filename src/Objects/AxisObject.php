<?php

namespace Shoplic\Axis3\Objects;

use Shoplic\Axis3\Interfaces\Objects\AxisObjectInterface;
use Shoplic\Axis3\Interfaces\Starters\StarterInterface;

/**
 * Class AxisObject
 *
 * Axis 3에서 기본적으로 구현된 Axis 기본 콤포넌트를 위한 클래스
 *
 * @package Shoplic\Axis3\Objects
 * @since   1.0.0
 */
abstract class AxisObject implements AxisObjectInterface
{
    /** @var StarterInterface */
    private $starter;

    public function getStarter(): StarterInterface
    {
        return $this->starter;
    }

    public function setStarter(StarterInterface $starter)
    {
        $this->starter = $starter;

        return $this;
    }

    public function claimAspect(string $fqcn, array $setupArgs = [], bool $reuse = true)
    {
        return $this->getStarter()->claimObject('aspect', $fqcn, $setupArgs, $reuse);
    }

    public function claimModel(string $fqcn, array $setupArgs = [], bool $reuse = true)
    {
        return $this->getStarter()->claimObject('model', $fqcn, $setupArgs, $reuse);
    }

    public function claimView(string $fqcn, array $setupArgs = [], bool $reuse = true)
    {
        return $this->getStarter()->claimObject('view', $fqcn, $setupArgs, $reuse);
    }
}
