<?php

namespace Shoplic\Axis3\Models\ValueTypes;

use Shoplic\Axis3\Interfaces\Models\ValueObjects\ValueObjectInterface;
use function Shoplic\Axis3\Functions\classImplements;

class ValueObjectType extends BaseValueType
{
    /** @var ValueObjectInterface */
    private $valueObjectType;

    public function __construct($valueObjectType)
    {
        if (!classImplements($valueObjectType, ValueObjectInterface::class)) {
            throw new \InvalidArgumentException(
                __('$valueObjectType must be an ValueObjectInterface.', 'axis3')
            );
        }

        parent::__construct();

        $this->valueObjectType = $valueObjectType;
    }

    public function getType(): string
    {
        return $this->valueObjectType;
    }

    /**
     * @param array $value 기본적으로 웹 기반의 form post 전송에 의한 전송을 가정하므로,
     *                     전달되는 복잡한 값 타입의 원시적인 표현은 배열이 된다.
     *                     이 배열을 보다 구조적인 표현을 가진 ValueObjectInterface 형태로 가공한다.
     *
     * @return ValueObjectInterface
     */
    public function sanitize($value)
    {
        /** @var ValueObjectInterface $typeClass */
        $typeClass = $this->getType();

        return $typeClass::fromArray($value);
    }

    /**
     * @param ValueObjectInterface $value
     *
     * @return array|void
     */
    public function verify($value): array
    {
        if ($value instanceof ValueObjectInterface && $value->isValid()) {
            return [true, $value];
        } else {
            return [
                false,
                __('Not a valid ValueObjectType.', 'axis3'),
            ];
        }
    }

    public function export($value)
    {
        /** @var ValueObjectInterface $value */
        if ($value instanceof ValueObjectInterface) {
            return $value->toArray();
        } elseif (is_array($value)) {
            return $value;
        }

        return [];
    }

    public function import($value)
    {
        /** @var ValueObjectInterface $typeClass */
        $typeClass = $this->getType();

        return $typeClass::fromArray($value);
    }

    public function getDefault(string $context = 'default')
    {
        /** @var ValueObjectInterface $typeClass */
        $typeClass = $this->getType();

        return new $typeClass();
    }
}
