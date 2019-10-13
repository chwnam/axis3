<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use DateTimeZone;
use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\datetimeI18n;
use function Shoplic\Axis3\Functions\getTimezone;
use function Shoplic\Axis3\Functions\inputTag;
use function Shoplic\Axis3\Functions\openTag;

/**
 * Class DatePickerWidget
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 *
 * @link    https://api.jqueryui.com/datepicker/
 * @link    https://jqueryui.com/datepicker/
 * @link    https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 */
class DatePickerWidget extends BaseFieldWidget
{
    public function __construct($fieldModel, $args = [])
    {
        parent::__construct($fieldModel, $args);

        if (!$this->args['dateFormat']) {
            $this->args['dateFormat'] = $this->getFieldModel()->getValueType()->getArg('dateFormat') ?? 'Y-m-d';
        }

        if (is_string($this->args['timezone'])) {
            $this->args['timezone'] = new DateTimeZone($this->args['timezone']);
        } elseif (!$this->args['timezone']) {
            $this->args['timezone'] = getTimezone();
        }

        $this->args['labelFor'] = $this->getId() . '-picker';
    }

    public function outputWidgetCore()
    {
        /**
         * @link https://www.php.net/manual/en/datetime.createfromformat.php
         * @link https://api.jqueryui.com/datepicker/#utility-formatDate
         */
        $datepickerDateFormat = str_replace(
            [
                'j', // 날짜 앞에 0 붙이지 않게.
                'd', // 날짜 앞에 0 붙여서.
                'D', // 요일 표현 짧게. (Mon, Tue, ...)
                'l', // 요일 표현 길게. (Monday, Tuesday, ...)
                'z', // 0 - 365 사이 날짜. (0부터 시작)
                'M', // 월을 문자로 짧게. (Jan, Feb, ...)
                'F', // 월을 문자로 길게. (January, February, ...)
                'n', // 월을 숫자로 0 붙이지 않게.
                'm', // 월을 숫자로 0을 붙여서.
                'y', // 년도를 짧게. 뒤 두 자리만.
                'Y', // 년도를 길게. 4자리 모두.
            ],
            [
                'd',
                'dd',
                'D',
                'DD',
                'o',
                'M',
                'MM',
                'm',
                'mm',
                'y',
                'yy',
            ],
            $this->args['dateFormat']
        );

        // picker field
        inputTag(
            wp_parse_args(
                $this->args['attrs'],
                [
                    'id'       => $this->args['labelFor'],
                    'value'    => datetimeI18n($this->getValue(), 'date'),
                    'type'     => 'text',
                    'class'    => 'text axis3-field-widget axis3-datepicker-widget',
                    'style'    => 'cursor: pointer;',
                    'required' => $this->isRequired(),
                    'title'    => $this->getRequiredMessage(),
                ]
            )

        );

        // real field, name 속성은 여기에 있음.
        inputTag(
            [
                'id'    => $this->getId(),
                'name'  => $this->getName(),
                'value' => datetimeI18n($this->getValue(), $this->args['dateFormat']),
                'class' => 'axis3-field-widget axis3-datepicker-widget hidden',
                'type'  => 'hidden',
            ]
        );

        openTag('span', ['class' => 'spacer']);
        closeTag('span');

        inputTag(
            [
                'id'    => $this->getId() . '-picker-reset',
                'type'  => 'button',
                'value' => __('Clear', 'axis3'),
                'class' => 'axis3-field-widget axis3-datepicker-widget button button-secondary',
            ]
        );

        $opt = wp_json_encode(
            wp_parse_args(
                $this->args['datepickerOpt'] ?? [],
                [
                    'altField'  => '#' . $this->getId(),
                    'altFormat' => $datepickerDateFormat,
                ]
            )
        );

        if (!$opt) {
            $opt = '{}';
        }

        wp_add_inline_script(
            'axis3-datepicker-widget',
            "jQuery('#{$this->getId()}-picker').axis3Datepicker({$opt});"
        );
    }

    /**
     * 렌더되기 전 필요한 스크립트를 준비한다.
     * Datepicker UI 는 워드프레스에서 로컬라이즈를 담당한다.
     */
    public function onceBeforeRender()
    {
        if (!wp_style_is('axis3-jquery-ui')) {
            wp_enqueue_style('axis3-jquery-ui');
        }

        if (!wp_script_is('axis3-datepicker-widget')) {
            wp_enqueue_script('axis3-datepicker-widget');
            wp_localize_jquery_ui_datepicker();
        }
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * array: 키-값 쌍으로 된 속성 목록.
                 *        피커가 나오는 input 태그에 사용된다. 실제 값을 가지는 hidden 에는 적용되지 않는다.
                 */
                'attrs'         => [],

                /**
                 * null|string: 문자열을 해석할 포맷. 지정하지 않으면 값 타입에서 가져온다.
                 *              만약 값 타입에 'dateFormat' 인자가 지정되어 있지 않다면 Y-m-d 형식을 쓴다.
                 */
                'dateFormat'    => null,

                /**
                 * null|string: 시간대. 지정하지 않으면 값 타입에서 가져온다.
                 */
                'timezone'      => null,

                /**
                 * datepicker 로 전달할 초기화 객체.
                 */
                'datepickerOpt' => [],
            ]
        );
    }
}
