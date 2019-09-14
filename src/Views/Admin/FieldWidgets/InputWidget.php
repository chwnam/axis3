<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use function Shoplic\Axis3\Functions\inputTag;

class InputWidget extends BaseFieldWidget
{
    protected function outputWidgetCore()
    {
        $attrs = wp_parse_args(
            $this->args['attrs'],
            [
                'id'       => $this->getId(),
                'name'     => $this->getName(),
                'value'    => $this->getValue(),
                'class'    => 'text axis3-field-widget axis3-input-widget',
                'type'     => 'text',
                'required' => $this->isRequired(),
                'title'    => $this->isRequired() ? $this->getRequiredMessage() : '',
            ]
        );

        inputTag($attrs);
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // array: input 태그의 속성을 지정. 키는 속성, 값은 속성의 값.
                'attrs' => [],
            ]
        );
    }
}
