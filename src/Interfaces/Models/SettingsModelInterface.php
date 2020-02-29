<?php

namespace Shoplic\Axis3\Interfaces\Models;

use Shoplic\Axis3\Interfaces\Models\FieldHolders\OptionFieldHolderInterface;

interface SettingsModelInterface extends OptionFieldHolderInterface
{
    /**
     * 이 세팅의 옵션 그룹을 리턴
     *
     * @return string
     */
    public static function getOptionGroup(): string;

    /**
     * 이 클래스에 있는 옵션 필드를 등록
     *
     * @return void
     */
    public function registerSettings();

    /**
     * 플러그인 활성화시 불리는 콜백.
     *
     * @return void
     */
    public function activationSetup();

    /**
     * 플러그인 비활성화시 불리는 콜백.
     *
     * @return void
     */
    public function deactivationCleanup();
}
