<?php

namespace Shoplic\Axis3\Views\Admin;

class SectionView extends SplitView
{
    public function __construct()
    {
        $this->setParam('section');
        $this->setTemplate('generics/generic-sections.php');
    }

    protected function renderItems()
    {
        $baseUrl  = $this->getBaseUrl();
        $current  = $this->getCurrent();
        $param    = $this->getParam();
        $sections = [];

        foreach ($this->getItems() as $slug => $sectionInfo) {
            $sections[$slug] = [
                'class' => [($current === $slug ? 'current' : '')],
                'url'   => add_query_arg($param, $slug, $baseUrl),
                'label' => $sectionInfo['label'],
            ];
        }

        $this->render($this->getTemplate(), ['sections' => &$sections]);
    }
}
