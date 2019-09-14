<?php

namespace Shoplic\Axis3\Models\ValueTypes;

/**
 * Class DoubleType
 *
 * 부동소숫점 값 타입.
 *
 * PHP 에서는 double, float, real 값 타입의 구분이 없다.
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class DoubleType extends BaseValueType
{
    /** @var array sanitize 옵션 */
    private $sanitizeOptions = null;

    /** @var array verify 옵션 */
    private $verifyOptions = null;

    public function getType(): string
    {
        return 'float';
    }

    public function sanitize($value)
    {
        if (is_null($this->sanitizeOptions)) {
            $this->sanitizeOptions = 0;
            if ($this->args['allow_fraction']) {
                $this->sanitizeOptions |= FILTER_FLAG_ALLOW_FRACTION;
            }
            if ($this->args['allow_thousand']) {
                $this->sanitizeOptions |= FILTER_FLAG_ALLOW_THOUSAND;
            }
            if ($this->args['allow_scientific']) {
                $this->sanitizeOptions |= FILTER_FLAG_ALLOW_SCIENTIFIC;
            }
        }

        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, $this->sanitizeOptions);
    }

    public function verify($value): array
    {
        if (is_null($this->verifyOptions)) {
            $this->verifyOptions = [
                'options' => [],
                'flags'   => 0,
            ];

            if ($this->args['decimal']) {
                $this->verifyOptions['options']['decimal'] = $this->args['decimal'];
            }

            if ($this->args['allow_thousand']) {
                $this->verifyOptions['flags'] |= FILTER_FLAG_ALLOW_THOUSAND;
            }
        }

        if (empty($value) && $this->args['emptyAsZero']) {
            $value = 0.0;
        }

        $verified = filter_var($value, FILTER_VALIDATE_FLOAT, $this->verifyOptions);

        if (false === $verified) {
            return [false, __('Invalid floating point.', 'axis3')];
        }

        if ($this->args['min'] && $this->args['min'] > $verified) {
            return [
                false,
                sprintf(
                    __('The value %d is smaller then the minimum value %d.', 'axis3'),
                    $verified,
                    $this->args['min']
                ),
            ];
        }

        if ($this->args['max'] && $this->args['max'] < $verified) {
            return [
                false,
                sprintf(
                    __('The value %d is larger then the maximum value %d.', 'axis3'),
                    $verified,
                    $this->args['max']
                ),
            ];
        }

        return [true, floatval($verified)];
    }

    public function import($value)
    {
        return floatval($value);
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // bool: sanitize() 에서 소숫점을 보존할지, 삭제할지 결정한다.
                'allow_fraction'   => true,

                // bool: sanitize(), verify() 에서 천자리수 구분자를 허용할지 결정한다.
                'allow_thousand'   => false,

                // bool: sanitize() 에서 부동소숫점 표기법(예를 들면 0.13e+3)을 허용할지 결정한다.
                'allow_scientific' => false,

                // verify() 메소드에서만 사용한다. 소숫점 표시 기호를 설정한다. 기본은 '.'.
                'decimal'          => '.',

                // double|null: 설정시 min, max 검증을 거친다.
                'min'              => null,
                'max'              => null,

                // bool: empty 값, 0이 아닌 빈 값(false, null, 빈 문자열)은 0.0으로 처리한다. 기본 true.
                //       false 일 경우 0.0이 아닌 빈 값들은 verify()를 통과하지 못해 verification fail 처리된다.
                'emptyAsZero'      => true,

                'default' => 0.0,
            ]
        );
    }
}

class_alias(DoubleType::class, 'Shoplic\Axis3\Models\ValueTypes\FloatType');
class_alias(DoubleType::class, 'Shoplic\Axis3\Models\ValueTypes\RealType');
