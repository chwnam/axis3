<?php

namespace Shoplic\Axis3\Views\Admin;

class TabView extends SplitView
{
    public function __construct()
    {
        $this->setParam('tab');
        $this->setTemplate('generics/generic-tabs.php');
    }

    protected function renderItems()
    {
        $baseUrl = $this->getBaseUrl();
        $current = $this->getCurrent();
        $param   = $this->getParam();
        $tabs    = [];

        foreach ($this->getItems() as $slug => $tabInfo) {
            $tabs[$slug] = [
                'class' => 'nav-tab' . ($current === $slug ? ' nav-tab-active' : ''),
                'url'   => add_query_arg($param, $slug, $baseUrl),
                'label' => $tabInfo['label'],
            ];
        }

        $this->render($this->getTemplate(), ['tabs' => &$tabs]);
    }
}
