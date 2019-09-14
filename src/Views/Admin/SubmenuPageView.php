<?php

namespace Shoplic\Axis3\Views\Admin;

/**
 * Class SubmenuPageView
 *
 * @package Shoplic\Axis3\Views\Admin
 * @since   1.0.0
 */
abstract class SubmenuPageView extends MenuPageView
{
    abstract public function getParentSlug();

    public function addSubMenuPage()
    {
        $this->hook = add_submenu_page(
            $this->getParentSlug(),
            $this->getPageTitle(),
            $this->getMenuTitle(),
            $this->getCapability(),
            $this->getMenuSlug(),
            $this->getCallback()
        );
    }
}
