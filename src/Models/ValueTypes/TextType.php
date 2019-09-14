<?php

namespace Shoplic\Axis3\Models\ValueTypes;

use function Shoplic\Axis3\Functions\strEndsWith;
use function Shoplic\Axis3\Functions\strStartsWith;

/**
 * Class TextType
 *
 * 문자열 값 타입
 *
 * @package Shoplic\Axis3\Models\ValueTypes
 * @since   1.0.0
 */
class TextType extends BaseValueType
{
    public function __construct(array $args = [])
    {
        parent::__construct($args);

        if (!is_callable($this->args['sanitizer'])) {
            $this->args['sanitizer'] = 'sanitize_text_field';
        }

        if (!is_null($this->args['minChar']) &&
            !is_null($this->args['maxChar']) &&
            $this->args['minChar'] > $this->args['maxChar']
        ) {
            throw new \InvalidArgumentException(
                sprintf(
                    __('maxChar (%d) is smaller than minChar (%d). ', 'axis3'),
                    $this->args['maxChar'],
                    $this->args['minChar']
                )
            );
        }

        if (!is_null($this->args['choices']) && (!is_array($this->args['choices']) || empty($this->args['choices']))) {
            throw new \InvalidArgumentException(__('choice should be null, or a non-empty array.', 'axis3'));
        }

        if (is_string($this->args['startsWith'])) {
            $this->args['startsWith'] = [$this->args['startsWith']];
        }

        if (is_string($this->args['endsWith'])) {
            $this->args['endsWith'] = [$this->args['endsWith']];
        }
    }

    public function getType(): string
    {
        return 'string';
    }

    public function sanitize($value)
    {
        $value = call_user_func($this->args['sanitizer'], $value);

        if (is_callable($this->args['trimmer']) && !empty($this->args['trimChars'])) {
            $value = call_user_func($this->args['trimmer'], $this->args['trimChars']);
        }

        return $value;
    }

    public function verify($value): array
    {
        if (!$this->args['allowBlank'] && empty($value)) {
            return [false, __('The textType does not allow an empty string.', 'axis3')];
        }

        if (!is_null($this->args['minChar']) && strlen($value) < $this->args['minChar']) {
            return [
                false,
                sprintf(
                    _n(
                        'The length of text should be longer than %d character.',
                        'The length of text should be longer than %d characters.',
                        $this->args['minChar'],
                        'axis3'
                    ),
                    $this->args['minChar']
                ),
            ];
        }

        if (!is_null($this->args['maxChar']) && strlen($value) > $this->args['maxChar']) {
            return [
                false,
                sprintf(
                    _n(
                        'The length of text should be shorter than %d character.',
                        'The length of text should be shorter than %d characters.',
                        $this->args['maxChar'],
                        'axis3'
                    ),
                    $this->args['maxChar']
                ),
            ];
        }

        if (is_array($this->args['choices'])) {
            // <optgroup>을 사용한 choices 인자를 배려.
            $choices = [];
            foreach ($this->args['choices'] as $key => $item) {
                if (is_array($item)) {
                    $choices = array_merge($choices, array_keys($item));
                } else {
                    $choices[] = $key;
                }
            }
            if (!in_array($value, $choices)) {
                return [
                    false,
                    __('The value is not in the choices list.', 'axis3'),
                ];
            }
        }

        if ($this->args['startsWith']) {
            $matched = false;
            foreach ((array)$this->args['startsWith'] as $needle) {
                if (strStartsWith($value, $needle)) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                return [
                    false,
                    __('The text value does not start with any given \'startsWith\' strings.', 'axis3'),
                ];
            }
        }

        if ($this->args['endsWith']) {
            $matched = false;
            foreach ((array)$this->args['endsWith'] as $needle) {
                if (strEndsWith($value, $needle)) {
                    $matched = true;
                    break;
                }
            }
            if (!$matched) {
                return [
                    false,
                    __('The text value does not start with any given \'endsWith\' strings.', 'axis3'),
                ];
            }
        }

        if ($this->args['regex'] && !preg_match($this->args['regex'], $value)) {
            return [
                false,
                sprintf(
                    __('The text value does not match the regex pattern \'%s\'.', 'axis3'),
                    $this->args['regex']
                ),
            ];
        }

        return [true, $value];
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // //////////////////////
                // 아래는 세정용 파라미터
                //
                // callable: 세정 함수. 기본은 'sanitize_text_field'. 값은 반드시 지정되어야 하며,
                //           불릴 수 없는 것이 입력되었다면 기본값으로 대치된다.
                //           가능한 세정 함수 예시. 이외 콜백 함수로 등록해도 무방함.
                //           - sanitize_text_field
                //           - sanitize_textarea_field
                //           - sanitize_file_name
                //           - sanitize_user
                //           - sanitize_key
                //           - sanitize_title
                //           - sanitize_email
                //           - sanitize_mime_type
                //           - sanitize_hex_color
                //           - wp_kses_post
                //           - esc_url_raw
                'sanitizer'  => 'sanitize_text_field',

                // callable: 기본 세정 후 추가로 문자열을 다듬을 함수를 지정한다. ltrim, rtrim, trim 같은 함수를 사용 가능하다.
                //           trim 류의 함수와 파라미터 형식만 맞으면 어떤 함수라도 가능하다.
                //           이 값이 null 이면 문자열 다듬기를 하지 않는다.
                'trimmer'    => null,

                // string: 문자열 다듬는 함수의 인자를 지정한다. trim 함수의 두번째 인자로 사용된다.
                'trimChars'  => '',

                // ///////////////////////////////////////////////
                // 아래는 검증용 파라미터. 검증 순서대로 나열하였다.
                //
                // bool: 빈 문자열도 저장을 허용한다. 기본값은 참.
                'allowBlank' => true,

                // null|int: 최소 문자를 지정할 수 있다. null 이면 사용하지 않는다. 숫자가 아니면 null 로 처리된다.
                'minChar'    => null,

                // null|int: 최대 문자를 지정할 수 있다. null 이면 사용하지 않는다. 숫자가 아니면 null 로 처리된다.
                'maxChar'    => null,

                // null|array: 이 값이 가질 수 있는 값의 종류를 지정.
                //             키는 실제 저장되는 문자열, 값은 UI 상에 노출될 레이블.
                'choices'    => null,

                // string|array: 문자열이 주어진 문자열로 시작해야 한다. null 이면 사용하지 않음.
                //               배열로 문자열 후보를 전달 할 수 잇으며, 값은 이 중 하나만 일치하면 됨
                'startsWith' => null,

                // string|array: 문자열이 주어진 문자열로 끝나야 한다. null 이면 사용하지 않음.
                //               배열로 문자열 후보를 전달 할 수 잇으며, 값은 이 중 하나만 일치하면 됨
                'endsWith'   => null,

                // string: 문자열에 대한 정규 표현 패턴식을 지정할 수 있다. null 이면 사용하지 않는다.
                //         입력시 /pattern/ 처럼 앞뒤 구분자도 다 입력.
                'regex'      => null,
            ]
        );
    }
}
