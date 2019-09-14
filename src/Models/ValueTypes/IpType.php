<?php

namespace Shoplic\Axis3\Models\ValueTypes;

/**
 * Class IpType
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 *
 * @see     filter_var()
 * @link    http://php.net/manual/en/function.filter-var.php
 * @link    http://php.net/manual/en/filter.filters.validate.php
 * @link    http://php.net/manual/en/function.natsort.php
 */
class IpType extends BaseValueType
{
    public function getType(): string
    {
        return 'string';
    }

    public function sanitize($value)
    {
        return sanitize_text_field($value);
    }

    public function verify($value): array
    {
        $exploded = explode('/', $value);

        if (sizeof($exploded) > 2) {
            return [false, __('Invalid IP notation', 'axis3')];
        } elseif (sizeof($exploded) === 2) {
            $mask = intval($exploded[1]);
            if ($mask < 0 || $mask > 32) {
                return [false, __('Invalid CIDR prefix length number', 'axis3')];
            }
        }

        if (filter_var($exploded[0], FILTER_VALIDATE_IP, $this->args['options'])) {
            return [true, $value];
        } else {
            return [false, __('Invalid IP address', 'axis3')];
        }
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * int: filter_var() 함수에 사용되는 IP 필터 옵션
                 *
                 * @link
                 */
                'options' =>
                    FILTER_FLAG_IPV4 |          // IPv4 검증.
                    FILTER_FLAG_IPV6 |          // IPv6 검증.
                    FILTER_FLAG_NO_PRIV_RANGE | // 사설영역 허용하지 않음.
                    FILTER_FLAG_NO_RES_RANGE,   // 예약 영역 허용하지 않음.
            ]
        );
    }

    /**
     * 유틸리티 함수. IPv4 에서 X.Y.Z.W1-W2 같이 표시된 표기는 W1과 W2 사이의 IP 목록으로 해석해 준다.
     *
     * 예시
     *   - 입력: 210.120.18.40-42
     *   - 출력: ['210.120.18.40', '210.120.18.41', '210.120.18.42']
     *
     * @param string $notation
     *
     * @return string[]
     */
    public static function expandIpV4LastPart($notation): array
    {
        if (false === strpos($notation, ':') && false === strpos($notation, '/')) {
            $output   = [];
            $exploded = explode('.', $notation);
            if (count($exploded) == 4 && strpos($exploded[3], '-') !== false) {
                $lastParts = array_map(
                    function ($elem) {
                        return intval(trim($elem));
                    },
                    explode('-', $exploded[3])
                );
                if (2 === sizeof($lastParts)) {
                    $beg = &$lastParts[0];
                    $end = &$lastParts[1];
                    if ((0 < $beg && $beg < 255) && (0 < $end && $end < 255)) {
                        for ($i = $beg; $i <= $end; ++$i) {
                            $output[] = "$exploded[0].$exploded[1].$exploded[2].{$i}";
                        }
                    }
                }
            }
            return $output;
        } else {
            return [];
        }
    }
}
