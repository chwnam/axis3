<?php

namespace Shoplic\Axis3\Models\ValueTypes;

use Shoplic\Axis3\Interfaces\Models\ValueObjects\ValueObjectInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;

/**
 * Class ArrayType
 *
 * 배열 값 타입 클래스
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class ArrayType extends BaseValueType
{
    /** @var ValueTypeInterface */
    protected $elementValueType = null;

    public function __construct(ValueTypeInterface $elementValueType, array $args = [])
    {
        parent::__construct($args);

        $this->elementValueType = $elementValueType;

        if ($this->args['associative'] && !$this->args['keySanitizer']) {
            $this->args['keySanitizer'] = 'sanitize_key';
        }
    }

    public function getType(): string
    {
        return 'array';
    }

    public function import($value)
    {
        if ($this->elementValueType instanceof ValueObjectType) {
            $output = [];
            /** @var ValueObjectInterface $item 임포트 해야 할 값들. */
            foreach ($value as $key => $item) {
                $output[$key] = $this->elementValueType->import($item);
            }
            return $output;
        }

        return parent::import($value);
    }

    public function export($value)
    {
        if ($this->elementValueType instanceof ValueObjectType) {
            $output = [];
            /** @var ValueObjectInterface $item 익스포트 해야 할 값들. */
            foreach ($value as $key => $item) {
                $output[$key] = $this->elementValueType->export($item);
            }
            return $output;
        }

        return parent::export($value);
    }

    public function sanitize($values)
    {
        $sanitized = [];

        foreach ((array)$values as $key => $value) {
            if ($this->args['associative']) {
                $k = call_user_func($this->args['keySanitizer'], $key);
                if ($this->args['keys'] && !isset($this->args['keys'][$k])) {
                    continue;
                } else {
                    $sanitized[$k] = $this->elementValueType->sanitize($value);
                }
            } else {
                $sanitized[] = $this->elementValueType->sanitize($value);
            }
        }

        return $sanitized;
    }

    public function verify($values): array
    {
        $verified = [];

        foreach ($values as $key => $value) {
            list($isValid, $result) = $this->elementValueType->verify($value);

            if ($isValid) {
                $verified[$key] = $result;
            } else {
                switch ($this->args['invalidElementPolicy']) {
                    case 'skip':
                        break;

                    case 'default':
                        $verified[$key] = $this->elementValueType->getDefault(
                            ValueTypeInterface::DEFAULT_CONTEXT_VERIFY
                        );
                        break;

                    case 'error':
                        return [
                            false,
                            sprintf(
                                __('The array element index \'%s\' occurred an error. Error message: %s', 'axis3'),
                                $key,
                                $result
                            ),
                        ];
                }
            }
        }

        if ($this->args['min'] > -1 && $this->args['min'] > sizeof($verified)) {
            return [
                false,
                sprintf(
                    _n(
                        'At least %d element is required.',
                        'At least %d elements are required.',
                        $this->args['min'],
                        'axis3'
                    ),
                    $this->args['min']
                ),
            ];
        }

        if ($this->args['max'] > -1 && $this->args['max'] < sizeof($verified)) {
            return [
                false,
                sprintf(
                    _n(
                        'At most %d element is allowed.',
                        'At most %d elements are allowed.',
                        $this->args['max'],
                        'axis3'
                    ),
                    $this->args['max']
                ),
            ];
        }

        if (!$this->args['associative'] && (sizeof($values) !== sizeof($verified))) {
            $verified = array_values($verified);
        }

        return [true, $verified];
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // bool: true 면 연관 배열 (associative array), false 면 순차 배열 (indexed array)
                //       기본은 순차 배열인 false 이다.
                //       연관 배열이면 반드시 keySanitizer 를 지정해야 한다.
                //       순차 배열이면 sanitize() 메소드에서 키를 모두 없애고 순차 배열화 시켜버린다.
                'associative'          => false,

                // callable: 연관 배열이라면 키를 세정하는 함수를 반드시 설정해야 한다.
                //           만약 associative 가 true 일 때, null 이라면 'sanitize_key'로 설정될 것이다.
                'keySanitizer'         => null,

                // array: 'associative'가 true 이면 지정할 수 있다. 입력하는 키를 제한할 수 있다.
                //        연관 배열로서, 키는 제한할 키. 값은 키에 대한 설명이다.
                'keys'                 => null,

                // string: 요소에 대해 세정, 검증을 통과하지 못하는 요소를 어떻게 처리할지 결정한다.
                //         다음 값 중 하나를 선택할 수 있다.
                //
                //         - skip:    해당 요소를 버린다.
                //         - default: 해당 값은 버리지만, 기본값으로 대체한다.
                //         - error:   즉시 에러를 낸다.
                'invalidElementPolicy' => 'skip',

                // int: 최소 원소 개수 제한. -1은 하한이 없음을 의미한다. 개수에 미달하는 경우 에러를 낸다.
                'min'                  => -1,

                // int: 최대 원소 개수 제한. -1은 상한이 없음을 의미한다. 개수를 초과하면 에러를 낸다.
                'max'                  => -1,
            ]
        );
    }
}
