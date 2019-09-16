<?php

namespace Shoplic\Axis3Sample\Views\Admin\Menus;

use Shoplic\Axis3\Views\Admin\MenuPageView;
use Shoplic\Axis3\Views\Admin\TabView;
use Shoplic\Axis3Sample\Views\Admin\Menus\Tabs\MarkdownView;
use function Shoplic\Axis3\Functions\getSvgIcon;
use function Shoplic\Axis3\Functions\getSvgIconUrl;

class DemoMenuPageView extends MenuPageView
{
    public function getPageTitle(): string
    {
        return __('데모 메뉴', 'axis3-demo');
    }

    public function getMenuTitle(): string
    {
        return __('데모 메뉴', 'axis3-demo');
    }

    public function getMenuSlug(): string
    {
        return 'axis3-demo';
    }

    public function getCapability(): string
    {
        return 'read';
    }

    public function getIconUrl(): string
    {
        return getSvgIconUrl(plugin_dir_path(AXIS3_MAIN) . 'src/assets/img/axis3-icon.svg');
    }

    public function dispatch()
    {
        /** @var TabView $view */
        $view = $this->claimView(TabView::class, [], false);
        $view->addItem('hello', __('Axis3', 'axis3-demo'), '__return_empty_string');
        $view->addItem('what-is-mtiv', __('MTIV에 대해', 'axis3-demo'), '__return_empty_string');
        $view->addItem('markdown', __('Markdown 샘플', 'axis3-demo'), [MarkdownView::class, 'dispatch']);
        $view->addAllowedParam('page', $_GET['page'] ?? '');
        $view->dispatch();
    }
}
