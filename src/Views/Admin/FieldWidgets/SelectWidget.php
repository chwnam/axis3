<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use Shoplic\Axis3\Models\ValueTypes\TextType;
use function Shoplic\Axis3\Functions\selectTag;

class SelectWidget extends BaseFieldWidget
{
    public function outputWidgetCore()
    {
        if ($this->args['choices']) {
            $choices = $this->args['choices'];
        } else {
            $choices = $this->getFieldModel()->getValueType()->getArg('choices') ?? [];
        }

        $attrs = wp_parse_args(
            $this->args['attrs'],
            [
                'id'       => $this->getId(),
                'name'     => $this->getName() . ($this->args['multiple'] ? '[]' : ''),
                'class'    => 'axis3-field-widget axis3-select-widget',
                'required' => $this->isRequired(),
                'title'    => $this->getRequiredMessage(),
                'multiple' => $this->args['multiple'],
            ]
        );

        selectTag(
            $choices,
            $this->args['value'] ? $this->args['value'] : $this->getValue(),
            $attrs,
            $this->args['optionAttrs'],
            $this->args['headingOption']
        );

        // TODO: augmentation
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * array: 속성 목록
                 */
                'attrs'         => [],

                /**
                 * array: 옵션 태그에 붙는 별도의 속성
                 *        키는 옵션 값.
                 *        값은 속성의 배열.
                 */
                'optionAttrs'   => [],

                /**
                 * array|null: 사용 가능한 값의 목록. null 이면 값 타입의 'choice' 인자에서 가져온다.
                 *             이 필드는 대개 TextType 과 연계되고, TextType 은 'choice' 인자를 가지고 있다.
                 *
                 * @see selectTag()
                 * @see TextType::getDefaultArgs()
                 */
                'choices'       => null,

                /**
                 * string: 선택된 값. null 이면 값 타입의 value 를 참조한다.
                 *         이 값이 설정되어 있다면 이 값을 우선한다.
                 */
                'value'         => null,

                /**
                 * bool: 다중 선택을 위한 플래그. true 면 다중 선택이 가능해진다.
                 */
                'multiple'      => false,

                /**
                 * array|string|false: selectTag() 함수의 $headingOption 인자로 사용된다.
                 *
                 * @see selectTag()
                 */
                'headingOption' => false,

                /**
                 * string: 미관, 혹은 기능상 더 나은 select 위젯을 출력하도록 할 수 있다.
                 *         기본은 plain 이며 가능한 값은 다음과 같다.
                 *         - plain:       기본적인 셀렉트 박스.
                 *         - jquery:      jquery selectmenu 사용
                 *         - nice-select: nice-select 를 사용한다.
                 *         - select2:     select2 를 사용한다.
                 */
                'augmentation'  => 'plain',
            ]
        );
    }
}
