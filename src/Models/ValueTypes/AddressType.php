<?php

namespace Shoplic\Axis3\Models\ValueTypes;

use InvalidArgumentException;

/**
 * Class MapAddressType
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class AddressType extends BaseValueType
{
    public function getType(): string
    {
        return $this->args['structured'] ? 'array' : 'string';
    }

    public function sanitize($value)
    {
        if ($this->args['structured']) {
            if (!is_array($value)) {
                $value = [];
            }
            $output = [
                'addr1' => sanitize_text_field($value['addr1'] ?? ''),
                'addr2' => sanitize_text_field($value['addr2'] ?? ''),
                'zip'   => sanitize_text_field($value['zip'] ?? ''),
            ];
        } else {
            $output = sanitize_text_field($value);
        }

        return $output;
    }

    public function verify($value): array
    {
        return [true, $value];
    }

    public function import($value)
    {
        if ($this->args['structured']) {
            $structured = explode('|', $value);
            return [
                'zip'   => $structured[0] ?? '',
                'addr1' => $structured[1] ?? '',
                'addr2' => $structured[2] ?? '',
            ];
        } else {
            return $value;
        }
    }

    public function export($value)
    {
        if ($this->args['structured']) {
            $value = "{$value['zip']}|{$value['addr1']}|{$value['addr2']}";
        }

        return $value;
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * bool: 주소를 데이터베이스에 기록할 때 {우편번호}|{시군구주소}|{상세주소} 처럼 구조화할지
                 *       아니면 그냥 한 줄로 이어붙일지 결정합니다.
                 *       참일 경우 값 타입은 배열. 키에 'zip', 'addr1', 'addr2'가 기록됩니다.
                 *       거짓이면 값은 그냥 문자열입니다.
                 */
                'structured' => true,
            ]
        );
    }
}
