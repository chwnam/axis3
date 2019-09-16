<?php

namespace Shoplic\Axis3Sample\Initiators\Admin;

use Shoplic\Axis3\Initiators\AutoHookInitiator;
use Shoplic\Axis3Sample\Views\Admin\Menus\DemoMenuPageView;

class MenuInitiator extends AutoHookInitiator
{
    public function action_admin_menu()
    {
        $this->claimView(DemoMenuPageView::class)->addMenuPage();
    }
}
