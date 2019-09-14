<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\openTag;

/**
 * Class TextareaWidget
 *
 * <textarea> 태그를 생성하는 위젯
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 */
class TextareaWidget extends BaseFieldWidget
{
    public function outputWidgetCore()
    {
        $attrs = wp_parse_args(
            $this->args['attrs'],
            [
                'id'       => $this->getId(),
                'name'     => $this->getName(),
                'required' => $this->isRequired(),
                'title'    => $this->isRequired() ? $this->getRequiredMessage() : '',
            ]
        );

        $sanitizer = $this->args['escapeMethod'];
        if (!is_callable($sanitizer)) {
            $sanitizer = 'esc_textarea';
        }

        openTag('textarea', $attrs);
        echo call_user_func($sanitizer, $this->getValue());
        closeTag('textarea');
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // array: textarea 속성값을 키-값 쌍으로 입력.
                'attrs'        => [],

                // callable: 텍스트영역 텍스트를 이스케이핑하는 함수.
                'escapeMethod' => 'esc_textarea',
            ]
        );
    }
}
