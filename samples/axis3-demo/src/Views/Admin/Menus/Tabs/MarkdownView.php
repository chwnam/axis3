<?php

namespace Shoplic\Axis3Sample\Views\Admin\Menus\Tabs;

use Shoplic\Axis3\Views\BaseView;
use Shoplic\Axis3Sample\Views\Admin\Menus\DemoMenuPageView;

class MarkdownView extends BaseView
{
    /**
     * Axis 문서를 출력합니다.
     *
     * @used-by DemoMenuPageView::dispatch()
     */
    public function renderAxis3()
    {
        static::renderMarkdown(plugin_dir_path(AXIS3_MAIN) . 'docs/axis3.md', 'axis3');
    }

    /**
     * Axis 문서를 출력합니다.
     *
     * @used-by DemoMenuPageView::dispatch()
     */
    public function renderMtiv()
    {
    }

    /**
     * Github Markdown CSS Demo 문서를 출력합니다.
     *
     * @used-by DemoMenuPageView::dispatch()
     */
    public function renderMarkdownCssDemo()
    {
        static::renderMarkdown(plugin_dir_path(AXIS3_MAIN) . 'docs/markdown-css-demo.md', 'markdown-css-demo');
    }
}
