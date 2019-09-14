<?php

namespace Shoplic\Axis3\Models\ValueObjects;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Shoplic\Axis3\Interfaces\Models\ValueObjects\ValueObjectInterface;
use function Shoplic\Axis3\Functions\toPascalCase;
use function Shoplic\Axis3\Functions\toSnakeCase;

/**
 * Class BaseValueObject
 *
 * 구현의 편의를 위해 fromArray(), toArray() 메소드를 미리 구현하였습니다.
 *
 * - private 속성은 관례에 따라 카멜 표기법으로 작성합니다.
 * - 속성값의 안전한 설정을 위해 getter/setter 작성하세요.
 * - 배열로 변경할 때 속성값을 키로 변환하는데, 이 때 표기법은 스네이크 표기법으로 변환됨.
 *
 * @package Shoplic\Axis3\Models\ValueObjects
 * @since   1.0.0
 */
abstract class BaseValueObject implements ValueObjectInterface
{
    public static function fromArray($array)
    {
        $instance = new static();
        foreach ((array)$array as $key => $value) {
            if (is_string($key) && !empty($key)) {
                $instance->{'set' . toPascalCase($key)}($value);
            }
        }

        return $instance;
    }

    public function toArray()
    {
        $out = [];

        try {
            $reflection = new ReflectionClass(__CLASS__);
            $properties = $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
            foreach ($properties as $property) {
                $out[toSnakeCase($property->getName())] = $this->{'get' . ucfirst($property->getName())}();
            }
        } catch (ReflectionException $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }

        return $out;
    }
}
