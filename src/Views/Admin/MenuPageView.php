<?php

namespace Shoplic\Axis3\Views\Admin;

use Shoplic\Axis3\Views\BaseView;

/**
 * Class MenuPageView
 *
 * 메뉴 페이지를 지원하는 뷰 클래스
 *
 * @package Shoplic\Axis3\Views\Admin
 * @since   1.0.0
 */
abstract class MenuPageView extends BaseView
{
    /** @var string */
    protected $hook = '';

    abstract public function getPageTitle(): string;

    abstract public function getMenuTitle(): string;

    abstract public function getCapability(): string;

    abstract public function getMenuSlug(): string;

    abstract public function dispatch();

    public function getCallback()
    {
        return [$this, 'dispatch'];
    }

    public function getIconUrl(): string
    {
        return '';
    }

    public function getPosition()
    {
        return null;
    }

    public function getHook(): string
    {
        return $this->hook;
    }

    public function addMenuPage()
    {
        $this->hook = add_menu_page(
            $this->getPageTitle(),
            $this->getMenuTitle(),
            $this->getCapability(),
            $this->getMenuSlug(),
            $this->getCallback(),
            $this->getIconUrl(),
            $this->getPosition()
        );
    }
}
