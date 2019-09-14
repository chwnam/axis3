<?php

namespace Shoplic\Axis3\Interfaces\Objects;

use Shoplic\Axis3\Interfaces\Aspects\AspectInterface;
use Shoplic\Axis3\Interfaces\Models\ModelInterface;
use Shoplic\Axis3\Interfaces\Views\ViewInterface;
use Shoplic\Axis3\Interfaces\Starters\StarterInterface;

/**
 * Interface AxisObjectInterface
 *
 * 콤포넌트의 기본이 되는 인터페이스를 정의합니다.
 *
 * @package Shoplic\Axis3\Interfaces\Objects
 * @since   1.0.0
 */
interface AxisObjectInterface
{
    /**
     * 객체가 최초로 생성될 때 한 번 불립니다.
     *
     * @param array $args
     *
     * @return mixed
     */
    public function setup($args = []);

    /**
     * 이 오브젝트가 속한 개시자를 가져옵니다.
     *
     * @return StarterInterface
     */
    public function getStarter(): StarterInterface;

    /**
     * 이 오브젝트를 소유할 개시자를 지정합니다.
     *
     * @param StarterInterface $starter
     *
     * @return self
     */
    public function setStarter(StarterInterface $starter);

    /**
     * 이 오브젝트가 속한 개시자의 애즈펙트 오브젝트를 가져옵니다.
     * 개시자의 claimObject() 메소드를 감싼 메소드입니다.
     *
     * @param string $fqcn
     * @param array  $setupArgs
     * @param bool   $reuse
     *
     * @return AspectInterface|object|null
     * @see    StarterInterface::claimObject()
     */
    public function claimAspect(string $fqcn, array $setupArgs = [], bool $reuse = true);

    /**
     * 이 오브젝트가 속한 개시자의 모델 오브젝트를 가져옵니다.
     * 개시자의 claimObject() 메소드를 감싼 메소드입니다.
     *
     * @param string $fqcn
     * @param array  $setupArgs
     * @param bool   $reuse
     *
     * @return ModelInterface|object|null
     * @see    StarterInterface::claimObject()
     */
    public function claimModel(string $fqcn, array $setupArgs = [], bool $reuse = true);

    /**
     * 이 오브젝트가 속한 개시자의 뷰 오브젝트를 가져옵니다.
     * 개시자의 claimObject() 메소드를 감싼 메소드입니다.
     *
     * @param string $fqcn
     * @param array  $setupArgs
     * @param bool   $reuse
     *
     * @return ViewInterface|object|null
     * @see    StarterInterface::claimObject()
     */
    public function claimView(string $fqcn, array $setupArgs = [], bool $reuse = true);
}
