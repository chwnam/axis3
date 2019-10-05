<?php

namespace Shoplic\Axis3\Models\FieldModels;

use Shoplic\Axis3\Interfaces\Models\FieldModels\FieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;
use Shoplic\Axis3\Models\BaseModel;

/**
 * Class BaseFieldModel
 *
 * 필드의 추상 구현.
 *
 * @package Shoplic\Axis3\Models\FieldModels
 */
abstract class BaseFieldModel extends BaseModel implements FieldModelInterface
{
    /** @var array */
    protected $args;

    /** @var string */
    private $key;

    public function __construct($key, $args = [])
    {
        $this->key  = sanitize_key($key);
        $this->args = wp_parse_args($args, static::getDefaultArgs());

        if (!$this->args['sanitizeCallback']) {
            /** @uses MetaFieldModel::defaultSanitizeCallback() */
            $this->args['sanitizeCallback'] = [$this, 'defaultSanitizeCallback'];
        }

        if (!did_action('plugins_loaded')) {
            add_action('plugins_loaded', [$this, 'earlyLoadFix']);
        }
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * 필드 타입을 리턴.
     * 기본적으로 필드 타입은 값 타입에서 설정한 타입을 따른다.
     *
     * @return string
     * @see ValueTypeInterface::getType()
     */
    public function getType(): string
    {
        return $this->getValueType()->getType();
    }

    public function getFieldType(): string
    {
        return $this->args['_fieldType'];
    }

    public function getLabel(): string
    {
        return $this->args['label'];
    }

    public function getShortLabel(): string
    {
        return $this->args['shortLabel'] ? $this->args['shortLabel'] : $this->args['label'];
    }

    public function getDescription(): string
    {
        return $this->args['description'];
    }

    public function getDefault(string $context = ValueTypeInterface::DEFAULT_CONTEXT_DEFAULT)
    {
        if (isset($this->args['default'])) {
            if (is_callable($this->args['default'])) {
                return call_user_func($this->args['default'], $context, $this);
            } else {
                return $this->args['default'];
            }
        } else {
            return $this->getValueType()->getDefault($context);
        }
    }

    public function isRequired(): bool
    {
        return $this->args['required'];
    }

    public function getRequiredMessage()
    {
        if (is_callable($this->args['requiredMessage'])) {
            return call_user_func($this->args['requiredMessage'], $this);
        } else {
            return $this->args['requiredMessage'];
        }
    }

    public function isShowInRest(): bool
    {
        return $this->args['showInRest'];
    }

    public function getValueType(): ValueTypeInterface
    {
        return $this->args['valueType'];
    }

    /**
     * @return callable
     * @uses   OptionFieldModel::defaultSanitizeCallback()
     */
    public function getSanitizeCallback(): callable
    {
        return $this->args['sanitizeCallback'];
    }

    public function sanitize($value)
    {
        $sanitized = $this->getValueType()->sanitize($value);

        if (isset($this->args['extraSanitize']) && is_callable($this->args['extraSanitize'])) {
            return call_user_func($this->args['extraSanitize'], $sanitized, $value, $this);
        } else {
            return $sanitized;
        }
    }

    public function verify($value): array
    {
        $verified = $this->getValueType()->verify($value);

        if (isset($this->args['extraVerify']) && is_callable($this->args['extraVerify'])) {
            return call_user_func($this->args['extraVerify'], $verified, $value, $this);
        } else {
            return $verified;
        }
    }

    public function import($value)
    {
        return $this->getValueType()->import($value);
    }

    public function export($value)
    {
        return $this->getValueType()->export($value);
    }

    /**
     * 필드 모델을 너무 일찍 초기화해서 사용하는 경우, 옵션 화면 등에서
     * 번역문이 제대로 출력되지 않는 문제를 수정한다.
     *
     * @callback
     * @action      plugins_loaded
     * @used-by     BaseFieldModel::__construct()
     */
    public function earlyLoadFix()
    {
        $starter = $this->getStarter();
        if ($starter) {
            $this->args['label']       = __($this->args['label'], $starter->getTextdomain());
            $this->args['shortLabel']  = __($this->args['shortLabel'], $starter->getTextdomain());
            $this->args['description'] = __($this->args['description'], $starter->getTextdomain());
        }
    }

    public static function getDefaultArgs(): array
    {
        return [
            /* string 필드 타입입니다. 내부적으로 사용됩니다. */
            '_fieldType'       => '',

            /* string 이 필드의 레이블입니다. */
            'label'            => '',

            /* string 리스트 테이블 같은 좁은 공간에서 쓸 수 있는 간략한 레이블입니다. */
            'shortLabel'       => '',

            /* string 이 필드를 설명합니다. */
            'description'      => '',

            /* bool 이 필드가 필수인지 표시합니다. */
            'required'         => false,

            /**
             * null|string|callable: 필수일 경우 필수 표시를 위해 나타나는 메시지입니다.
             *                       필수 메시지를 출력하는 방법은 필드 위젯의 구현마다 다를 수 있습니다.
             *                       콜백의 경우 이 인스턴스를 인자로 가집니다. 콜백은 반드시 문자열을 리턴해야 합니다.
             */
            'requiredMessage'  => null,

            /* ValueTypeInterface 값 타입을 명시합니다. 필수적입니다. */
            'valueType'        => null,

            /**
             * callable|null sanitize callback 을 오버라이드하려면 여기에 콜백 함수를 대입하세요
             *
             * @see OptionFieldModel::defaultSanitizeCallback()
             */
            'sanitizeCallback' => null,

            /* bool REST 에서 보이려면 true 로 설정하세요. */
            'showInRest'       => false,

            /**
             * callable: sanitize(), verify() 기본 메소드에서 이 파라미터에 정의된 콜백을 실행합니다.
             *           값 타입에서 별도로 한 번 더 값을 교졍/세정할 경우 지정할 수 있습니다.
             *
             * @see BaseFieldModel::sanitize()
             * @see BaseFieldModel::verify()
             */
            // 'extraSanitize' => null,
            // 'extraVerify'   => null,

            /**
             * mixed 값 타입에도 기본값이 있습니다. 그러나 지정해 주면 지정된 값을 기본값으로 사용합니다.
             *       콜백 함수를 대입할 수도 있습니다. 이 때 콜백 함수의 인자로 콘텍스트, 현재 객체가 입력됩니다.
             *       콜백 함수는 이 필드에서 사용할 기본값을 리턴해야 합니다.
             *
             *       콜백 함수 예시:
             *       'default' => function (string $context, ValueTypeInterface $valueType) {
             *                        if ($context === 'verify') {
             *                        } else {
             *                        }
             *                    },
             *
             *       중요: 값 타입에서 지정된 기본값을 사용하려면, 이 키를 지정하지 마세요
             *
             * @see BaseValueType::getDefault()
             */
            // default => null
        ];
    }

    protected function dieValidationError(string $description, string $errorMessage, $failedValue)
    {
        $title = __('Verification Error', 'axis3');

        wp_die(
            sprintf(
                '<h1>%s</h1><p>%s</p>' .
                '<ul><li>%s: %s</li>' .
                '<li>%s: %s</li></ul>' .
                '<h3>%s</h3><p>%s</p>',
                esc_html($title),
                esc_html($description),
                __('Key', 'axis3'),
                $this->getKey(),
                __('Value', 'axis3'),
                esc_html(print_r($failedValue, true)),
                __('Error message', 'axis3'),
                nl2br(esc_html($errorMessage))
            ),
            $title,
            [
                'response'  => 400,
                'back_link' => true,
                'code'      => 'error-' . $this->getKey(),
            ]
        );
    }
}
