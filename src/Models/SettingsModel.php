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

        add_filter('whitelist_options', [$this, 'callbackFilterWhitelistOptions'], 20);
    }

    public function activationSetup()
    {
        // autoload = 'no' 임에도 'yes'로 설정되어있는 옵션이 있다면 변경 처리한다.
        // 옵션 필드는 저장시 이전값과 다른 값이 들어오지 않으면 업데이트 쿼리가 실행되지 않고,
        // 따라서 autoload 변경 처리 액션도 동작하지 않는다.
        // 또한 개발이 진행 중 옵션의 'autoload' 옵션이 변경되었다면 변경된 설정을 쉽게 적용하기 위해 사용할 수 있다.
        $allOptionNames = [];
        foreach ($this->getAllOptionFields() as $field) {
            if (!$field->isAutoload()) {
                $allOptionNames[] = $field->getKey();
            }
        }
        if ($allOptionNames) {
            $wpdb = self::getWpdb();
            $pad  = implode(', ', array_pad([], count($allOptionNames), '%s'));
            $wpdb->query(
                $wpdb->prepare(
                    "UPDATE `{$wpdb->options}` SET `autoload` = 'no' WHERE `option_name` IN ({$pad}) AND `autoload` = 'yes'",
                    $allOptionNames
                )
            );
        }
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

    /**
     * 히든 필드에 대해 whitelist_options 목록에서 제거한다.
     * 단, 히든 필드의 옵션 그룹이 해당 모델과 같지 않으면 동작하지 않는다.
     *
     * 히든 필드는 겉으로 나오지 않고 프로그램 내부적으로만 처리되도록 약속된 필드이다.
     * Settings API 에 의해 옵션이 등록되면 옵션 폼 제출에 의한 업데이트 동작시 등록된 옵션에 대해 일괄적인
     * update_option() 호출이 일어난다. 폼에 전달되든 전달되지 않든 말이다.
     *
     * 그런데 히든 필드는 UI를 따로 만들지 않았기 때문에 값이 제대로 업데이트 될 리 없다.
     * 값도 엉뚱하게 변경되고, 원치 않은 notice 메시지도 마주하게 될 것이다.
     *
     * 그렇다고 옵션을 등록하지 않으면? 값 등록시 sanitize_callback 콜백을 지정하고, axis3는 이것을 중요하게 생각한다.
     * 즉 프로그래밍 상에서 옵션 업데이트시 제대로 sanitize_callback 이 일어나지 않아 그것도 부작용이 된다.
     *
     * 그래서 일괄 register_option() 함수를 통해 적용하되, 옵션 업데이트가 일어나기 전에만
     * whitelist options 에서 제거한다. 이렇게 하면 일괄적인 업데이트시 값의 오염을 막는다.
     *
     * @param array $whitelistOptions
     *
     * @return array
     *
     * @see /wp-admin/options.php
     * @see wp-admin/includes/admin-filters.php
     * @see option_update_filter()
     */
    public function callbackFilterWhitelistOptions($whitelistOptions)
    {
        if (isset($whitelistOptions[static::getOptionGroup()])) {
            $cleared = false;
            foreach ($this->getAllOptionFields() as $field) {
                /** @var OptionFieldModelInterface $field */
                if ($field->isHidden()) {
                    $pos = array_search($field->getKey(), $whitelistOptions[static::getOptionGroup()]);
                    if (false !== $pos) {
                        unset($whitelistOptions[static::getOptionGroup()][$pos]);
                        $cleared = true;
                    }
                }
            }
            if ($cleared) {
                $whitelistOptions[static::getOptionGroup()] = array_values($whitelistOptions[static::getOptionGroup()]);
            }
        }

        return $whitelistOptions;
    }

    protected function getOptionFieldArgs(callable $args)
    {
        $argument = parent::getOptionFieldArgs($args);

        if (empty($argument['group'])) {
            $argument['group'] = static::getOptionGroup();
        }

        return $argument;
    }
}
