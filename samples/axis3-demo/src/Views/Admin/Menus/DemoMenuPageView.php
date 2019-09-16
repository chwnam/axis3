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

    /**
     * 페이지 출력. 탭을 설정하고, 선택된 탭을 출력한다.
     *
     * @uses MarkdownView::renderAxis3()
     * @uses MarkdownView::renderMtiv()
     * @uses MarkdownView::renderMarkdownCssDemo()
     */
    public function dispatch()
    {
        /** @var TabView $view */
        $view = $this->claimView(TabView::class, [], false);

        $view->addItem(
            'hello',
            __('Axis3', 'axis3-demo'),
            [MarkdownView::class, 'renderAxis3']
        );
        $view->addItem(
            'what-is-mtiv',
            __('MTIV', 'axis3-demo'),
            [MarkdownView::class, 'renderMtiv']
        );
        $view->addItem(
            'markdown',
            __('Markdown 샘플', 'axis3-demo'),
            [MarkdownView::class, 'renderMarkdownCssDemo']
        );

        $view->addAllowedParam('page', $_GET['page'] ?? '');
        $view->dispatch();
    }
}
