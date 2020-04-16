<?php

namespace Shoplic\Axis3\Functions;

/**
 * 문자열을 감쌉니다.
 *
 * @param string $input     입력 문자열.
 * @param string $quoteChar 감쌀 문자. 기본은 쌍따옴표.
 *
 * @return string
 */
function encloseString(string $input, string $quoteChar = '"'): string
{
    return "{$quoteChar}{$input}{$quoteChar}";
}


/**
 * HTML 태그 속성을 맞춰줍니다.
 *
 * @param array $attributes 속성을 키와 값으로 정의한 배열.
 *
 * @return string 적절히 포뱃된 HTML 속성 문자열.
 */
function formatAttr(array $attributes): string
{
    $buffer = [];

    foreach ($attributes as $key => $val) {
        $key = sanitize_key($key);

        /** @link https://html.spec.whatwg.org/multipage/indices.html#attributes-3 */
        switch ($key) {
            case 'accept':
                $func = function ($key, $value) {
                    if (is_string($value)) {
                        $value = array_filter(array_map('trim', explode(',', $value)));
                    }
                    return $key . '=' . encloseString(
                            implode(', ', array_unique(array_map('sanitize_mime_type', $value)))
                        );
                };
                break;

            case 'class':
                $func = function ($key, $value) {
                    if (is_string($value)) {
                        $value = preg_split('/\s+/', $value);
                    }
                    return $key . '=' . encloseString(implode(' ', array_map('sanitize_html_class', $value)));
                };
                break;

            case 'action':
            case 'cite':
            case 'data':
            case 'formaction':
            case 'href':
            case 'itemid':
            case 'itemprop':
            case 'itemtype':
            case 'manifest':
            case 'ping':
            case 'poster':
            case 'src':
                $func = function ($key, $value) {
                    return $key . '=' . encloseString(implode(' ', array_map('esc_url', preg_split('/\s+/', $value))));
                };
                break;

            case 'allowfullscreen':
            case 'allowpaymentrequest':
            case 'async':
            case 'autofocus':
            case 'autoplay':
            case 'checked':
            case 'controls':
            case 'default':
            case 'defer':
            case 'disabled':
            case 'formnovalidate':
            case 'hidden':
            case 'ismap':
            case 'itemscope':
            case 'loop':
            case 'multiple':
            case 'muted':
            case 'nomodule':
            case 'novalidate':
            case 'open':
            case 'playsinline':
            case 'readonly':
            case 'required':
            case 'reversed':
            case 'selected':
                /*
                 * 이 속성들은 다음처럼 사용된다.
                 * <input ... readonly>
                 * <input ... readonly="">
                 * <input ... readonly="readonly">
                 *
                 * 이런 속성들은 PHP 배열의 키 - 값 설정시 초기화 구문으로 사용하기 어려운 면이 있다.
                 * 그러므로 PHP 배열 초기화시 어려움이 있으므르 다음처럼 되도록 배려한다.
                 * 예를 들면, 이렇게 처리하면 다음처럼 코드를 쓸 수 있다.
                 * $attrs = [
                 *   'id'       => 'foo',
                 *   'name'     => 'foo',
                 *   'required' => true,
                 * ]
                 *
                 * 참/거짓으로 표현하지 않으면 PHP 초기화를 한 후 대입을 해 줘야 하는 수고가 발행한다.
                 * $attrs = [
                 *  'id'  => 'bar',
                 *  'name' => 'bar',
                 * ];
                 *
                 * if($required) {
                 *   $attrs['required'] = 'required';
                 * }
                 */
                $func = function ($key, $value) {
                    if ((is_bool($value) && $value) || $key === $value) {
                        return $key . '=' . $key;
                    } else {
                        return '';
                    }
                };
                break;

            default:
                $func = function ($key, $val) {
                    return $key . '=' . encloseString(esc_attr($val));
                };
                break;
        }

        if ($key) {
            $buffer[] = call_user_func($func, $key, $val);
        }
    }

    return ' ' . implode(' ', $buffer);
}


/**
 * 태그를 엽니다.
 *
 * @param string $tag
 * @param array  $attributes
 * @param bool   $echo
 *
 * @return string|null
 */
function openTag(string $tag, array $attributes = [], bool $echo = true)
{
    $output = '';
    $tag    = sanitize_key($tag);
    $attrs  = formatAttr($attributes);

    if ($tag && $attrs) {
        $output = '<' . $tag . $attrs . '>';
    }

    if ($echo) {
        echo $output;
        return null;
    }

    return $output;
}


/**
 * 태그를 닫습니다.
 *
 * @param string $tag  태그 이름.
 * @param bool   $echo 출력 여부.
 *
 * @return string|null
 */
function closeTag(string $tag, bool $echo = true)
{
    $output = '';
    $tag    = sanitize_key($tag);

    if ($tag) {
        $output = '</' . $tag . '>';
    }

    if ($echo) {
        echo $output;
        return null;
    }

    return $output;
}


/**
 * <input> 태그를 생성.
 *
 * @param array $attributes
 * @param bool  $echo
 *
 * @return string|null
 */
function inputTag(array $attributes = [], bool $echo = true)
{
    return openTag('input', $attributes, $echo);
}


/**
 * <option> 태그를 생성.
 *
 * @param string      $value      값 속성.
 * @param string      $label      레이블.
 * @param string|bool $selected   선택된 값. 이 값과 $value 가 동일하면 'selected' 속성이 추가. 불리언으로도 가능.
 * @param array       $attributes 태그의 기타 속성.
 * @param bool        $echo       출력 여부.
 *
 * @return string|null
 */
function optionTag(string $value, string $label, $selected, array $attributes = [], bool $echo = true)
{
    $attributes['value']    = $value;
    $attributes['selected'] = is_bool($selected) ? $selected : $value == $selected;

    $output = openTag('option', $attributes, false) . esc_html($label) . closeTag('option', false);

    if ($echo) {
        echo $output;
        return null;
    }

    return $output;
}


/**
 * select 태그를 출력.
 *
 * @param array              $options          키 - 값 배열을 이용해 옵션 목록을 제공할 수 있다.
 *                                             한편 값이 재차 배열인 경우는 이 키는 옵션 그룹의 레이블로, 값은 옵션 그룹의 옵션으로 쓰인다.
 * @param string|array       $selected         선택된 값.
 * @param array              $attributes       <select> 태그에 사용할 속성들.
 * @param array              $optionAttributes <option> 태그에 붙일 속성.
 *                                             키는 지칭을 옵션 태그의 값. 값은 재차 배열로 키는 속성, 값은 속성의 값.
 * @param array|string|false $headingOption    $options 로 지정된 옵션보다 더 먼저 삽입되는 선택 불가능한 옵션을 추가.
 *                                             false 이면 사용하지 않는다.
 *                                             array 면 길이 2여야 하고, 인덱스 0은 value 속성, 인덱스 1은 레이블로 사용된다.
 *                                             string 인 경우 바로 레이블로 사용되며 이 때 value 속성으로는 빈 문자열이 사용된다.
 *                                             즉 이런 식으로 출력된다:
 *                                             <option value="" disabled="disabled">레이블</option>
 * @param bool               $echo             출력 여부를 지정
 *
 * @return string|null
 *
 * @example
 * option 태그만 사용하는 예:
 * $options = [
 *   'volvo'    => 'Volvo',        // <option value="volvo">Volvo</option>
 *   'saab'     => 'Saab',         // <option value="saab">Saab</option>
 *   'mercedes' => 'Mercedes',     // <option value="mercedes">Mercedes</option>
 *   'audi'     => 'Audi',         // <option value="audi">Audi</option>
 * ]
 *
 * optgroup 태그와 혼용하는 예:
 * $options = [
 *   'Swedish Cars' => [             // <optgroup label="Swedish Cars">
 *     'volvo'    => 'Volvo',        //   <option value="volvo">Volvo</option>
 *     'saab'     => 'Saab',         //   <option value="saab">Saab</option>
 *   ],                              // </optgroup>
 *   'German Cars' => [              // <optgroup label="German Cars">
 *     'mercedes' => 'Mercedes',     //   <option value="mercedes">Mercedes</option>
 *     'audi'     => 'Audi',         //   <option value="audi">Audi</option>
 *   ],                              // </optgroup>
 * ]
 *
 * <option class="mercedes-option" data-type="car-brand"> ... 처럼 옵션 태그에 속성 추가 예:
 * $optionAttributes = [
 *   'mercedes' => [
 *     'class'      => 'mercedes-option',
 *     'data-type'  => 'car-brand',
 *   ]
 * ]
 */
function selectTag(
    array $options = [],
    $selected = '',
    array $attributes = [],
    array $optionAttributes = [],
    $headingOption = false,
    bool $echo = true
) {
    $buffer = [openTag('select', $attributes, false)];

    if (is_array($selected)) {
        $selected = array_combine(array_values($selected), array_pad([], count($selected), true));
    }

    if (is_array($headingOption) && sizeof($headingOption) >= 2) {
        $buffer[] = optionTag(
            $headingOption[0],
            $headingOption[1],
            $selected,
            [
                'disabled' => true,
                'selected' => isset($selected[$headingOption[0]]),
            ],
            false
        );
    } elseif (is_string($headingOption)) {
        $buffer[] = optionTag(
            '',
            $headingOption,
            $selected,
            [
                'disabled' => true,
                'selected' => empty($selected),
            ],
            false
        );
    }

    foreach ($options as $value => $item) {
        if (is_array($item)) {
            $buffer[] = openTag('optgroup', array_merge(['label' => $value], $optionAttributes[$value] ?? []), false);
            foreach ($item as $val => $label) {
                $buffer[] = optionTag(
                    $val,
                    $label,
                    is_array($selected) ? isset($selected[$val]) : $selected,
                    $optionAttributes[$val] ?? [],
                    false
                );
            }
            $buffer[] = closeTag('optgroup');
        } else {
            $buffer[] = optionTag(
                $value,
                $item,
                is_array($selected) ? isset($selected[$value]) : $selected,
                $optionAttributes[$value] ?? [],
                false
            );
        }
    }

    $buffer[] = closeTag('select', false);

    if ($echo) {
        echo implode("\n", $buffer);
        return null;
    }

    return implode("\n", $buffer);
}


/**
 * 간단한 <ul>, <ol> 목록 태그 출력.
 *
 * @param string $tag            태그. ol, ul.
 * @param array  $attributes     태그 속성.
 * @param string $itemTag        태그내 각 아이템 속성. 기본 li.
 * @param array  $items          아이템들.
 * @param array  $listAttributes 키는 문자열, 값은 재차 배열.
 *                               $items 에 걸어둔 키와 같은 값은 그 아이템의 <li> 태그 속성으로 이용된다.
 * @param bool   $escape         <li> 태그 내 이스케이프 처리. 기본 true.
 * @param bool   $echo           출력 여부.
 *
 * @return string|null
 */
function listTag(
    string $tag,
    array $attributes = [],
    string $itemTag = 'li',
    array $items = [],
    array $listAttributes = [],
    bool $escape = true,
    bool $echo = true
) {
    if (!$echo) {
        ob_start();
    }

    openTag($tag, $attributes);
    foreach ($items as $idx => $item) {
        openTag($itemTag, $listAttributes[$idx] ?? []);
        echo $escape ? esc_html($item) : $item;
        closeTag($itemTag);
    }
    closeTag($tag);

    if (!$escape) {
        return ob_get_clean();
    }

    return null;
}
