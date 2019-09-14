<?php

namespace Shoplic\Axis3\Models\ValueTypes;

/**
 * Class BoolType
 *
 * 불리언 타입의 값을 정의
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class BoolType extends BaseValueType
{
    public function getType(): string
    {
        return 'bool';
    }

    public function sanitize($value)
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    public function verify($value): array
    {
        // filter_var() 함수를 통과한 값으로 충분하다.
        return [true, $value];
    }

    public function import($value)
    {
        return boolval($value);
    }

    public static function getDefaultArgs(): array
    {
        return [];
    }
}
