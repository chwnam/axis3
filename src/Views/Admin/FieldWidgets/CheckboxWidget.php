<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\inputTag;
use function Shoplic\Axis3\Functions\openTag;

/**
 * Class CheckboxWidget
 *
 * 체크박스를 출력합니다. 1개의 항목만 나오는 체크박스입니다.
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 */
class CheckboxWidget extends BaseFieldWidget
{
    public function outputWidgetCore()
    {
        $inputAttrs = wp_parse_args(
            $this->args['inputAttrs'],
            [
                'id'       => $this->getId(),
                'name'     => $this->getName(),
                'class'    => 'axis3-field-widget axis3-checkbox-widget',
                'type'     => 'checkbox',
                'value'    => 'yes',
                'required' => $this->isRequired(),
                'checked'  => filter_var($this->getValue(), FILTER_VALIDATE_BOOLEAN),
                'title'    => $this->getRequiredMessage(),
            ]
        );
        inputTag($inputAttrs);

        $labelAttrs = wp_parse_args(
            $this->args['labelAttrs'],
            ['for' => $inputAttrs['id']]
        );
        openTag('label', $labelAttrs);
        echo wp_kses_post($this->getDescription());
        closeTag('label');
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                'outputDesc' => false,
                'tooltip'    => false,
                'inputAttrs' => [],
                'labelAttrs' => [],
            ]
        );
    }
}
