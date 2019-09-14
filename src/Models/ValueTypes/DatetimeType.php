<?php

namespace Shoplic\Axis3\Models\ValueTypes;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use function Shoplic\Axis3\Functions\datetimeI18n;
use function Shoplic\Axis3\Functions\getDatetime;
use function Shoplic\Axis3\Functions\getTimezone;

/**
 * Class DatetimeType
 *
 * PHP DateTime 객체를 기반으로 한 값 타입.
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class DatetimeType extends BaseValueType
{
    public function __construct(array $args = [])
    {
        parent::__construct($args);

        if (!$this->args['dateFormat']) {
            $this->args['dateFormat'] = DateTimeInterface::ATOM;
        }

        if ($this->args['default']) {
            $this->args['default'] = getDatetime($this->args['default']);
        }

        if ($this->args['min']) {
            $this->args['min'] = getDatetime($this->args['min']);
        }

        if ($this->args['max']) {
            $this->args['max'] = getDatetime($this->args['max']);
        }

        if (is_string($this->args['timezone'])) {
            $this->args['timezone'] = new DateTimeZone($this->args['timezone']);
        } elseif (!$this->args['timezone']) {
            $this->args['timezone'] = getTimezone();
        }
    }

    public function getType(): string
    {
        if ('U' === $this->args['dateFormat']) {
            return 'integer';
        } else {
            return 'string';
        }
    }

    public function sanitize($value)
    {
        return getDatetime($value, $this->args['timezone'], $this->args['dateFormat']);
    }

    public function verify($value): array
    {
        if (!$value) {
            return [false, __('Invalid datetime string.', 'axis3')];
        }

        if ($this->args['min'] && $value < $this->args['min']) {
            return [
                false,
                sprintf(
                    __('The datetime is earlier then the minimum datetime \'%s\'.', 'axis3'),
                    datetimeI18n($this->args['min'])
                ),
            ];
        }

        if ($this->args['max'] && $value > $this->args['max']) {
            return [
                false,
                sprintf(
                    __('The datetime is later than the maximum datetime \'%s\'.', 'axis3'),
                    datetimeI18n($this->args['max'])
                ),
            ];
        }

        return [true, $value];
    }

    public function import($value)
    {
        return getDatetime($value, $this->args['timezone'], $this->args['dateFormat']);
    }

    public function export($value)
    {
        if ($value instanceof DateTime) {
            return $value->format($this->args['dateFormat']);
        }

        return false;
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * string: 문자열을 해석할 포맷을 지정한다. DB 저장, 그리고 폼 전송값을 해석하는데 사용된다.
                 *         null:   ISO8601. DateTimeInterface::ATOM
                 *         string: PHP 일자 형식 문자열. 'Y-m-d' 같은 문자열이다.
                 *
                 * 주의. 날짜만 표현하는 경우라면 null 이 아닌 명시적으로 'Y-m-d' 같이 형식을 지정해야 한다.
                 *
                 * @link https://www.php.net/manual/en/datetime.createfromformat.php
                 * @link https://www.php.net/manual/en/class.datetimeinterface.php#datetime.constants.atom
                 */
                'dateFormat' => null,

                /**
                 * string: 시간대. null 이면 getTimeZone() 함수를 사용한 워드프레스 기본 시간대를 사용한다.
                 *         만약 타임스탬프를 그대로 활용하려면 이 타입이 아닌 IntType 사용을 고려하라.
                 *
                 * @see getTimezone()
                 */
                'timezone'   => null,

                /**
                 * DateTime|string|int|null: 최대, 최소 날짜 (포함) 입력된 날짜가 해당 범위 내에 있는지 점검한다.
                 */
                'min'        => null,
                'max'        => null,

                /**
                 * DateTime|string|int|false: 기본값.
                 */
                'default'    => false,
            ]
        );
    }
}
