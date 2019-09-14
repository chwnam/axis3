<?php

namespace Shoplic\Axis3\Models\ValueTypes;

use Shoplic\Axis3\Interfaces\Models\ValueObjects\ValueObjectInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;
use function Shoplic\Axis3\Functions\classImplements;

/**
 * Class BaseValueType
 *
 * 기본 값 타입 클래스.
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @version 1.0.0
 */
abstract class BaseValueType implements ValueTypeInterface
{
    protected $args;

    public function __construct(array $args = [])
    {
        $this->args = wp_parse_args($args, static::getDefaultArgs());
    }

    public function getArgs()
    {
        return $this->args;
    }

    public function getArg(string $key)
    {
        return $this->args[$key] ?? null;
    }

    public function export($value)
    {
        return $value;
    }

    public function import($value)
    {
        return $value;
    }

    public function isStrict(): bool
    {
        return $this->args['strict'] ?? false;
    }

    public function getDefault(string $context = self::DEFAULT_CONTEXT_DEFAULT)
    {
        $default = '';

        if (isset($this->args['default'])) {
            $default = $this->args['default'];
            if (is_callable($default)) {
                /** @see BaseFieldModel::getDefaultArgs()  'default' 파라미터의 주석을 참고하세요. */
                $default = call_user_func($default, $context, $this);
            }
        } elseif ('array' === $this->getType()) {
            $default = [];
        }

        return $default;
    }

    public static function getDefaultArgs(): array
    {
        // bool: verify() 메소드에서 검증을 엄격하게 한다. 실제 처리는 상속된 클래스의 방침에 따라 조금씩 다를 수 있다.
        return [
            'strict' => false,
        ];
    }
}
