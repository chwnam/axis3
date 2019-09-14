<?php

namespace Shoplic\Axis3\Models\ValueTypes;

/**
 * Class IntType
 *
 * 정수 값 타입
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @version 1.0.0
 */
class IntType extends BaseValueType
{
    /** @var array verify 옵션 */
    private $verifyOptions = null;

    public function getType(): string
    {
        return 'integer';
    }

    public function sanitize($value)
    {
        // filter_var( $value, FILTER_SANITIZE_NUMBER_INT ); 로는 16진수에 대응하기 어렵다.
        return preg_replace('/[^0-9A-F\-+]/i', '', $value);
    }

    public function verify($value): array
    {
        if (is_null($this->verifyOptions)) {
            $this->verifyOptions = [
                'options' => [],
                'flags'   => 0,
            ];

            if ($this->args['base'] == 8) {
                $this->verifyOptions['flags'] |= FILTER_FLAG_ALLOW_OCTAL;
            } elseif ($this->args['base'] == 16) {
                $this->verifyOptions['flags'] |= FILTER_FLAG_ALLOW_HEX;
            }
        }

        if (empty($value) && $this->args['emptyAsZero']) {
            $value = 0;
        }

        $verified = filter_var($value, FILTER_VALIDATE_INT, $this->verifyOptions);

        if (false === $verified) {
            return [false, __('Invalid integer value.', 'axis3')];
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

        return [true, intval($verified, $this->args['base'])];
    }

    public function import($value)
    {
        return intval($value, $this->args['base']);
    }

    public function export($value)
    {
        if ($this->args['base'] && $this->args['base'] != 10) {
            return (string)base_convert($value, 10, $this->args['base']);
        } else {
            return (string)$value;
        }
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // int: DB 에서 값을 가져올 때 취급할 진수. 기본은 null 로 10진법.
                //      8진수 체계면 verify() 메소드 호출 시 FILTER_FLAG_ALLOW_OCTAL 플래그가 추가되며
                //      역시 16진수 체계면 마찬가지로 FILTER_FLAG_ALLOW_HEX 플래그가 추가된다.
                //      값을 내보낼 때도 해당 진법에 맞게 값 출력이 된다.
                'base'        => null,

                // int: 값 검증시 최솟값. null 이면 제한하지 않는다.
                'min'         => null,

                // int: 값 검증시 최댓값. null 이면 제한하지 않는다.
                'max'         => null,

                // bool: empty 값, 0이 아닌 빈 값(false, null, 빈 문자열)은 0으로 처리한다. 기본 true.
                //       false 일 경우 0이 아닌 빈 값들은 verify()를 통과하지 못해 verification fail 처리된다.
                'emptyAsZero' => true,

                'default' => 0,
            ]
        );
    }
}
