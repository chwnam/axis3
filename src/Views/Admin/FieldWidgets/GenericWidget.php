<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use Shoplic\Axis3\Interfaces\Views\ViewInterface;

/**
 * Class GenericWidget
 *
 * 템플릿과 콘텍스트를 외부에서 임의로 지정하여 출력할 수 있는 위젯.
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 */
class GenericWidget extends BaseFieldWidget
{
    public function outputWidgetCore()
    {
        if (is_callable($this->args['widgetTemplate'])) {
            $template = call_user_func($this->args['widgetTemplate'], $this);
        } else {
            $template = &$this->args['widgetTemplate'];
        }

        if (is_callable($this->args['widgetContext'])) {
            $context = call_user_func($this->args['widgetContext'], $this);
        } else {
            $context = $this->args['widgetContext'];
        }

        $this->render($template, $context);
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * string|callable: 템플릿 위치
                 *                  ViewInterface::render() 의 첫번째 인자로 사용된다.
                 *                  호출 가능한 객체를 넣을 수도 있다. 이 때 첫번째 인자로 위젯 객체가 전달되며,
                 *                  반드시 템플릿의 이름인 문자열을 리턴해야 한다.
                 *
                 * @see ViewInterface::render()
                 */
                'widgetTemplate' => null,

                /**
                 * array|callable: 템플릿으로 전달할 콘텍스트.
                 *                 ViewInterface::render() 의 두번째 인자로 사용된다.
                 *                 호출 가능한 객체를 넣을 수도 있다. 이 때 첫번째 인자로 위젯 객체가 전달되며,
                 *                 반드시 템플릿의에 전달될 문맥인 연관 배열을 리턴해야 한다.
                 */
                'widgetContext'  => [],
            ]
        );
    }
}
