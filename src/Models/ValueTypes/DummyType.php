<?php

namespace Shoplic\Axis3\Models\ValueTypes;

/**
 * Class DummyType
 *
 * 더미 값 타입.
 * 어떤 값도 실제 처리하지 않으므로 일반적인 값 타입으로 사용하기는 어렵다.
 * 테스트용으로 사용을 할 수 있다.
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class DummyType extends BaseValueType
{
    public function getType(): string
    {
        return 'string';
    }

    public function sanitize($value)
    {
        return '';
    }

    public function verify($value): array
    {
        return [true, ''];
    }

    public static function getDefaultArgs(): array
    {
        return [];
    }
}
