<?php

namespace Shoplic\Axis3Sample\Views\Admin;

use Shoplic\Axis3\Views\Admin\SubmenuPageView;

class BookSettingsSubmenuPageView extends SubmenuPageView
{
    public function getParentSlug()
    {
        return 'options-general.php';
    }

    public function getPageTitle(): string
    {
        return '도서 설정';
    }

    public function getMenuTitle(): string
    {
        return '도서 설정 페이지';
    }

    public function getCapability(): string
    {
        return 'manage_options';
    }

    public function getMenuSlug(): string
    {
        return 'book-settings';
    }

    public function dispatch()
    {
        /** @var BookSettingsView $settingsView */
        $settingsView = $this->claimView(BookSettingsView::class);
        $settingsView->renderSettings();
    }
}
