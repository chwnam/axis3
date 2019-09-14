<?php

namespace Shoplic\Axis3\Models\FieldModels;

use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;

class OptionFieldModel extends BaseFieldModel implements OptionFieldModelInterface
{
    private $context = null;

    public function __construct($key, $args = [])
    {
        $args['_fieldType'] = 'option';

        parent::__construct($key, $args);
    }

    public function isAutoload(): bool
    {
        return $this->args['autoload'];
    }

    public function isContextual(): bool
    {
        return $this->args['contextual'];
    }

    public function getGroup(): string
    {
        return $this->args['group'];
    }

    public function retrieve($context = null)
    {
        if ($this->isContextual()) {
            $default = $this->getDefault();
            $value   = (array)get_option($this->getKey(), $default);
            $context = sanitize_key($context);
            if (isset($value[$context])) {
                return $this->import($value[$context]);
            } elseif (isset($default[$context])) {
                return $this->import($default[$context]);
            } else {
                return $default;
            }
        } else {
            return $this->import(get_option($this->getKey(), $this->getDefault()));
        }
    }

    public function save($value, $context = null)
    {
        /**
         * @see OptionFieldModel::defaultSanitizeCallback() 콜백에 현재 콘텍스트를 알리기 이해 콘텍스트 저장
         */
        if ($this->isContextual() && $context) {
            $this->context = sanitize_key($context);
        }

        // update_option() 함수 내부에서 sanitize 용도로 defaultSanitizeCallback() 메소드를 호출할 것임.
        return update_option($this->getKey(), $value, $this->isAutoload());
    }

    /**
     * 기본으로 셋업되는 sanitize_option() 안의 'sanitize_option_{$option}' 필터의 콜백
     *
     * @callback
     * @filter      sanitize_option_{$option}
     *
     * @param mixed $value 전달된 값. register_settings() 에서 인자 수를 1개로 두었다는 점을 참고.
     *
     * @return mixed
     * @see         sanitize_option()
     * @see         update_option()
     * @see         register_setting()  add_filter() 함수가 호출되는 지점은 이 곳
     * @used-by     OptionFieldModel::getSanitizeCallback()
     */
    public function defaultSanitizeCallback($value)
    {
        /** @see ValueTypeInterface::verify() 결과값 참조 */
        list($verified, $result) = $this->verify($this->sanitize($value));

        $verifiedValue = null;

        if ($verified) {
            /** @var mixed $result */
            $verifiedValue = $result;
        } else {
            /** @var string $result 검증에 실패한 경우 $result 는 실패 메시지 문자열. */
            if ($this->getValueType()->isStrict()) {
                $description = sprintf(
                    __('Option \'%s\' has failed verification', 'axis3'),
                    $this->getKey()
                );
                $this->dieValidationError($description, $result, $value);
            } else {
                $description = sprintf(
                    __('Option \'%s\' has failed verification and replaced with the default value', 'axis3'),
                    $this->getKey()
                );
                add_settings_error(
                    $this->getGroup(),
                    'warning-' . $this->getKey(),
                    $description,
                    'warning'
                );
                $verifiedValue = $this->getDefault(ValueTypeInterface::DEFAULT_CONTEXT_VERIFY);
            }
        }

        if ($this->isContextual()) {
            if (!is_array($value)) {
                $value = [];
            }
            $value[$this->context] = $this->export($verifiedValue);
        } else {
            $value = $this->export($verifiedValue);
        }

        /**
         * save() 메소드를 위해 저장한 콘텍스트 초기화.
         *
         * @see OptionFieldModel::save()
         */
        $this->context = null;

        return $value;
    }

    public function registerOptionField()
    {
        $registeredSettings = get_registered_settings();
        if (!isset($registeredSettings[$this->getKey()])) {
            register_setting(
                $this->getGroup(),
                $this->getKey(),
                [
                    'type'              => $this->getType(),
                    'group'             => $this->getGroup(),
                    'description'       => $this->getDescription(),
                    'sanitize_callback' => $this->getSanitizeCallback(),
                    'show_in_rest'      => $this->isShowInRest(),
                ]
            );
        }
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /** bool 옵션 오토로딩을 지원합니다. 기본 true. */
                'autoload'   => true,

                /** bool 문맥적 옵션을 지원합니다. 기본 false. */
                'contextual' => false,

                /** bool 옵션 그룹의 이름입니다. 옵션 API 사용을 위해 필요합니다. */
                'group'      => '',

                /** bool REST API 에 보일 지 결정합니다. 기본 false */
                'showInRest' => false,
            ]
        );
    }
}
