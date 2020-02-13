<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use DateTimeZone;

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\datetimeI18n;
use function Shoplic\Axis3\Functions\getTimezone;
use function Shoplic\Axis3\Functions\inputTag;
use function Shoplic\Axis3\Functions\openTag;
use function Shoplic\Axis3\Functions\strStartsWith;

/**
 * Class DatePickerWidget
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 *
 * @link    https://api.jqueryui.com/datepicker/
 * @link    https://jqueryui.com/datepicker/
 * @link    https://trentrichardson.com/examples/timepicker/
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

        // DATE_ATOM 은 DatetimeType 의 기본값이다. 기본값대로 안 하면 'dateFormat', 'timeFormat', 'datepickerOpt'
        // 세 속성에 유의해서 세팅해야 한다.
        if (DATE_ATOM === $this->args['dateFormat']) {
            $this->args['dateFormat'] = substr(DATE_ATOM, 0, 5);
            if ($this->args['timepickerAddon'] && !$this->args['timeFormat']) {
                $this->args['timeFormat'] = substr(DATE_ATOM, 5);
                if (strStartsWith($this->args['timeFormat'], '\\T')) {
                    $this->args['timeFormat'] = str_replace('\\T', '\'T\'', $this->args['timeFormat']);
                }
            }
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
        $search = [
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
            'G', // 24시간제 시간. 앞에 0 붙이지 않고.
            'H', // 24시간제 시간. 앞에 0을 붙이고.
            'g', // 12시간제 시간. 앞에 0을 붙이지 않고.
            'h', // 12시간제 시간. 앞에 0을 붙이고.
            'i', // 앞에 0을 붙인 분.
            's', // 앞에 0을 붙인 초.
            'v', // 앞에 0을 붙인 밀리초.
            'u', // 앞에 0을 붙인 마이크로초.
            'a', // am, pm (소문자)로 표시한 오전 오후.
            'A', // AM, PM (대문자)로 표시한 오전 오후.
            'e', // 시간대.
            'P', // ISO 8601 포맷의 시간대 표시 (+04:45)
        ];

        $replace       = [
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
            'H',
            'HH',
            'h',
            'hh',
            'mm',
            'ss',
            'l',
            'c',
            'tt',
            'TT',
            'z',
            'Z',
        ];
        $dateFormat    = str_replace($search, $replace, $this->args['dateFormat']);
        $altTimeFormat = str_replace($search, $replace, $this->args['timeFormat']);
        $timeFormat    = str_replace($search, $replace, get_option('time_format'));

        // picker field
        inputTag(
            wp_parse_args(
                $this->args['attrs'],
                [
                    'id'       => $this->args['labelFor'],
                    'value'    => datetimeI18n($this->getValue(), $this->args['timepickerAddon'] ? 'both' : 'date'),
                    'type'     => 'text',
                    'class'    => 'text axis3-field-widget axis3-datepicker-widget',
                    'style'    => 'cursor: pointer; ',
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
                'value' => datetimeI18n(
                    $this->getValue(),
                    $this->getFieldModel()->getValueType()->getArg('dateFormat')
                ),
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
                    'timeFormat'       => $timeFormat,
                    'altField'         => '#' . $this->getId(),
                    'altFormat'        => $dateFormat,
                    'altSeparator'     => strStartsWith($altTimeFormat, '\'T\'') ? '' : ' ',
                    'altTimeFormat'    => $altTimeFormat,
                    'altFieldTimeOnly' => false,
                ]
            )
        );

        if (!$opt) {
            $opt = '{}';
        }

        $addon = $this->args['timepickerAddon'] ? 'true' : 'false';

        wp_add_inline_script(
            'axis3-datepicker-widget',
            "jQuery(function(\$){\$('#{$this->getId()}-picker').axis3Datepicker({$opt}, {$addon});});"
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
            if ($this->args['timepickerAddon'] && !wp_style_is('axis3-timepicker-addon')) {
                wp_enqueue_style('axis3-timepicker-addon');
            }
        }

        if (!wp_script_is('axis3-datepicker-widget')) {
            wp_enqueue_script('axis3-datepicker-widget');
            wp_localize_jquery_ui_datepicker();
            if ($this->args['timepickerAddon'] && !wp_script_is('axis3-timepicker-addon')) {
                wp_enqueue_script('axis3-timepicker-addon');
                wp_enqueue_script('axis3-timepicker-addon-i18n');
                $locale = $this->args['localize'];
                if (!$locale) {
                    $locale = get_option('WPLANG', 'en');
                    if (false !== strpos($locale, '_')) {
                        $locale = explode('_', $locale);
                        $locale = $locale[0];
                    }
                }
                wp_add_inline_script(
                    'axis3-datepicker-widget',
                    "jQuery(document).ready(function(\$){\$.timepicker.setDefaults('{$locale}');});"
                );
            }
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
                'attrs'           => [],

                /**
                 * null|string: 날짜 형식 문자열을 해석할 포맷. 지정하지 않으면 값 타입에서 가져온다.
                 *              만약 값 타입에 'dateFormat' 인자가 지정되어 있지 않다면 Y-m-d 형식을 쓴다.
                 */
                'dateFormat'      => null,

                /**
                 * null|string: 시간 형식 문자열 해석할 포맷. 지정하지 않으면 값 타입에서 가져온다.
                 *              만약 값 타입에 'dateFormat' 인자가 지정되어 있지 않다면 H-i-s 형식을 쓴다.
                 */
                'timeFormat'      => null,

                /**
                 * null|string: 시간대. 지정하지 않으면 값 타입에서 가져온다.
                 *
                 */
                'timezone'        => null,

                /**
                 * array: datepicker 로 전달할 초기화 객체.
                 */
                'datepickerOpt'   => [],

                /**
                 * bool: timepicker addon 위젯을 추가로 붙인다.
                 *       true 인 경우 timepicker addon 이 추가로 생긴다.
                 *       모델 필드가 DatetimeType 이고 기본 dateFormat 을 쓰면 위젯에서 적절히 처리한다.
                 *
                 *       원래 datepicker 는 날짜만 취급하고 시간을 취급하지 않는다.
                 *       그래서 timepicker addon 은 추가로 시간의 형식만 따로 설정하는 필드를 가지고 있다.
                 *       위젯에서 시간 형식이 올바로 적용되려면 timeFormat 인자에 주의를 기울여야 한다.
                 */
                'timepickerAddon' => false,

                /**
                 * string: 로컬라이즈. 놔두면 현재 언어에 맞춘다.
                 */
                'localize'        => null,
            ]
        );
    }
}
