<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

/**
 * Class CheckboxRadioWidget
 *
 * 체크박스 목록을 출력. 여러 선택 항목에서 복수의 선택을 할 때 사용.
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 */
class CheckboxRadioWidget extends BaseFieldWidget
{
    public function outputWidgetCore()
    {
        if ($this->args['choices']) {
            $choices = $this->args['choices'];
        } else {
            $valueTypeArgs = $this->getFieldModel()->getValueType()->getArgs();
            $choices       = $valueTypeArgs['choices'] ?? [];
        }

        $attributes = [];
        $selected   = (array)$this->getValue();

        foreach ($choices as $value => $label) {
            $attributes[] = [
                wp_parse_args(
                    $this->args['inputAttrs'],
                    [
                        'id'       => "{$this->getId()}-$value",
                        'name'     => $this->getName() . ('checkbox' === $this->args['type'] ? '[]' : ''),
                        'class'    => 'axis3-checkbox-radio',
                        'type'     => $this->args['type'],
                        'required' => $this->isRequired(),
                        'title'    => $this->isRequired() ? $this->getRequiredMessage() : $label,
                        'value'    => $value,
                        'checked'  => in_array($value, $selected),
                    ]
                ),
                wp_parse_args(
                    $this->args['labelAttrs'],
                    [
                        'for'   => "{$this->getId()}-$value",
                        'class' => 'axis3-checkbox-radio',
                    ]
                ),
                $label,
            ];
        }

        $this->render(
            $this->args['template'],
            [
                'attributes' => &$attributes,
                'direction'  => &$this->args['direction'],
            ]
        );

        // TODO: augmentation
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * string: 템플릿.
                 */
                'template'     => 'generics/generic-checkbox-radio.php',

                /**
                 * string: 타입을 설정한다. 'checkbox', 혹은 'radio' 중 하나를 선택할 수 있다.
                 *         이 속성은 'attrs'에 의해 오버라이드 되지 않는다.
                 */
                'type'         => 'checkbox',

                /**
                 * array: <input> 태그에 공통적으로 붙는 속성 목록.
                 */
                'inputAttrs'   => [],

                /**
                 * array: <label> 태그에 공통적으로 붙는 속성 목록.
                 */
                'labelAttrs'   => [],

                /**
                 * array: 특정 <input> 태그에만 붙는 속성
                 *        키는 옵션 값.
                 *        값은 속성의 배열.
                 */
                'optionAttrs'  => [],

                /**
                 * array|null: 사용 가능한 값의 목록. null 이면 값 타입의 'choices' 인자에서 가져온다.
                 *             이 필드는 대개 TextType 과 연계되고, TextType 은 'choice' 인자를 가지고 있다.
                 *
                 * @see selectTag()
                 * @see TextType::getDefaultArgs()
                 */
                'choices'      => null,

                /**
                 * array: 선택된 값. null 이면 값 타입의 value 를 참조한다.
                 *        이 값이 설정되어 있다면 이 값을 우선한다.
                 */
                'value'        => null,

                /**
                 * string: 항목 출력 방향.
                 *         - horizontal: 항목을 가로로 나열.
                 *         - vertical:   항목을 하나씩 세로로 나열
                 */
                'direction'    => 'horizontal',

                /**
                 * string: 미관, 혹은 기능상 더 나은 select 위젯을 출력하도록 할 수 있다.
                 *         기본은 plain 이며 가능한 값은 다음과 같다.
                 *         - plain:   기본적인 체크박스.
                 *          jquery:   jquery checkboxradio 사용
                 *         - select2: select2 를 사용한다.
                 */
                'augmentation' => 'plain',
            ]
        );
    }
}
