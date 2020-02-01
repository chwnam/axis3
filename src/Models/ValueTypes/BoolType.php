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

    public function export($value)
    {
        // 옵션이나 메타에서 필드 값이 없을 때 'false'를 리턴한다. 진짜 필드 값을 false 로 설정한 것인지
        // 필드가 아직 저장되지 않은 건지 모호한 경우가 발생한다. 그런 경우를 방지하기 위해 명시적으로 캐스팅한다.
        return intval($value);
    }

    public static function getDefaultArgs(): array
    {
        return [];
    }
}
