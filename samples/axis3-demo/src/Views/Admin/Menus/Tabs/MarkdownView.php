<?php

namespace Shoplic\Axis3Sample\Views\Admin\Menus\Tabs;

use Shoplic\Axis3\Views\BaseView;

class MarkdownView extends BaseView
{
    public function dispatch()
    {
        static::renderMarkdown(plugin_dir_path(AXIS3_MAIN) . 'docs/markdown-css-demo.md', 'markdown-css-demo');
    }
}
