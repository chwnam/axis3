<?php

namespace Shoplic\Axis3\Views\Admin;

use Shoplic\Axis3\Interfaces\Views\Admin\FieldWidgets\FieldWidgetInterface;
use WP_Post;

/**
 * Class PropertyMetaBoxView
 *
 * 속성 메타 박스.
 * 커스텀 필드 모델과 필드 위젯을 이용해 메타 박스를 만들어낸다.
 *
 * @package Shoplic\Axis3\Views\Admin
 * @version 1.0.0
 */
abstract class PropertyMetaBoxView extends MetaBoxView
{
    /** @var string */
    private $template = 'generics/property-meta-box.php';

    /**
     * @param WP_Post $post
     *
     * @return FieldWidgetInterface[]
     */
    abstract public function getFieldWidgets($post): array;

    /**
     * @param WP_Post $post
     */
    public function dispatch($post)
    {
        $widgets = $this->getFieldWidgets($post);
        foreach ($widgets as $widget) {
            if (!$widget->getStarter()) {
                $widget->setStarter($this->getStarter());
            }
        }

        $this->render(
            $this->getTemplate(),
            [
                'content_header' => $this->getContentHeader($post),
                'content_footer' => $this->getContentFooter($post),
                'table_header'   => $this->getTableHeader($post),
                'table_footer'   => $this->getTableFooter($post),
                'nonce_action'   => $this->getNonceAction($post),
                'nonce_param'    => $this->getNonceParam($post),
                'field_widgets'  => &$widgets,
            ]
        );
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;
    }

    protected function getContentFooter($post)
    {
        return '';
    }

    protected function getContentHeader($post)
    {
        return '';
    }

    protected function getTableFooter($post)
    {
        return '';
    }

    protected function getTableHeader($post)
    {
        return '';
    }
}
