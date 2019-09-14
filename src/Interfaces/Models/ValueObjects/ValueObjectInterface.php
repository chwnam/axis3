<?php

namespace Shoplic\Axis3\Interfaces\Models\ValueObjects;

/**
 * Interface ValueObjectInterface
 *
 * 메타 필드의 직렬화(serialized)된 값을 보다 객체적으로 해석하기 위한 도구
 *
 * 보통 메타 필드 값은 스칼라형, 배열, 혹은 stdClass 객체를 이용해 나타낸다.
 * 특히 복잡한 값을 저장하기 위한 가장 대표적인 방법은 배열이며,
 * PHP 의 직렬화를 이용해 하나의 값은 인코딩된 문자열로 변환되어 데이터베이스에 저장된다.
 *
 * 배열은 자유분방하게 사용할 수 있는 매력이 있긴 하지만, 값의 표현이 '키'라는 문자열 때문에
 * 명료한 코딩에는 방해가 되는 단점도 존재한다.
 *
 * Axis 3의 값-객체 인터페이스는 데이터베이스로부터 받은 배열을 객체로 변환하여
 * 코드에서 보다 객체적인 관점에서 데이터를 처리할 수 있도록 한다.
 * (물론 배열에서 객체로 변환하는 비용은 들겠지만...)
 *
 * @package Shoplic\Axis3\Interfaces\Models\ValueObjects
 * @since   1.0.0
 */
interface ValueObjectInterface
{
    public static function fromArray($array);

    public function toArray();

    public function isValid(): bool;
}
