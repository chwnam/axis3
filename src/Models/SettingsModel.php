<?php

namespace Shoplic\Axis3\Models;

use Shoplic\Axis3\Initiators\ModelRegistrationInitiator;
use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\SettingsModelInterface;
use Shoplic\Axis3\Models\FieldHolders\OptionFieldHolderModel;

/**
 * Class SettingsModel
 *
 * Settings API 와 직결된 옵션 필드를 정의.
 *
 * ModelRegistrationInitiator 와 연동하여 옵션 모델을 직접 코어에 등록해준다.
 *
 * 단, setOptionGroup() 메소드를 통해 반드시 그룹 이름을 지정해야 합니다.
 *
 * @package Shoplic\Axis3\Models
 * @see     ModelRegistrationInitiator
 */
abstract class SettingsModel extends OptionFieldHolderModel implements SettingsModelInterface
{
    /**
     * ModelRegistrationInitiator 에 의해 자동으로 불려집니다.
     */
    public function registerSettings()
    {
        /** @var OptionFieldModelInterface $optionField */
        foreach ($this->getAllOptionFields() as $optionField) {
            if (!$optionField->isAutoload()) {
                add_action("update_option_{$optionField->getKey()}", [$this, 'correctAutoloadField'], 1, 3);
            }
            if ($optionField->isContextual()) {
                add_filter("pre_update_option_{$optionField->getKey()}", [$this, 'preUpdateContextualOption'], 1, 3);
            }
        }
    }

    public function activationSetup()
    {
    }

    public function deactivationCleanup()
    {
    }

    /**
     * Settings API 로 등록된 옵션은 autoload 가 무조건 true 로 저장된다.
     * 옵션 저장시 autoload 아닌 필드는 교정하도록 한다.
     *
     * @callback
     * @action      update_option_{$option}
     *
     * @param mixed  $oldValue 이전에 저장된 값
     * @param mixed  $value    현재 update_option() 호출로 저장하는 값
     * @param string $option   옵션 이름
     *
     * @see         update_option()
     * @see         SettingsModel::registerSettings()
     */
    public function correctAutoloadField($oldValue, $value, $option)
    {
        $optionFieldModel = $this->claimOptionField($option);
        if ($optionFieldModel && !$optionFieldModel->isAutoload()) {
            $wpdb = static::getWpdb();
            $wpdb->update($wpdb->options, ['autoload' => 'no'], ['option_name' => $option]);
        }
    }

    /**
     * Settings API 로 등록한 옵션은 contextual 일 경우 context 보호를 위해
     * 값을 미리 처리해야 한다.
     *
     * 옵션이 contextual 한 경우, 필드에서는 {$option}_context 라는 숨겨진 변수를 통해
     * 현재 콘텍스트를 전달하도록 되어 있습니다.
     *
     * @callback
     * @filter      pre_update_option_{$option}
     *
     * @param mixed  $value    현재 저장하려는 옵션값
     * @param mixed  $oldValue 이전에 저장된 옵션값. 콘텍스트를 가지고 있어서 반드시 배열이어야 한다.
     * @param string $option   옵션 이름
     *
     * @return mixed
     * @see         update_option()
     */
    public function preUpdateContextualOption($value, $oldValue, $option)
    {
        $optionFieldModel = $this->claimOptionField($option);
        $context          = sanitize_key($_REQUEST["{$option}_context"] ?? '');

        if ($optionFieldModel && $optionFieldModel->isContextual() && $context) {
            $output = [];
            foreach ((array)$oldValue as $k => $v) {
                if ($k === $context) {
                    $output[$k] = $value;
                } else {
                    $output[$k] = $v;
                }
            }
            return $output;
        }

        return $oldValue;
    }

    protected function getOptionFieldArgs(callable $args)
    {
        $argument = parent::getOptionFieldArgs($args);

        if (empty($argument['group'])) {
            $argument['group'] = $this->getOptionGroup();
        }

        return $argument;
    }
}
