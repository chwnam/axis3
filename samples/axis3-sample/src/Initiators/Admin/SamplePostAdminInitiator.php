<?php

namespace Shoplic\Axis3Sample\Initiators\Admin;

use Shoplic\Axis3\Initiators\Admin\CustomPostAdminInitiator;
use Shoplic\Axis3Sample\Models\CustomPosts\SamplePostModel;
use Shoplic\Axis3Sample\Views\Admin\Metaboxes\SamplePostPropertyMetaBoxView;

class SamplePostAdminInitiator extends CustomPostAdminInitiator
{
    public function setup($args = array())
    {
        $this
            ->setModel($this->claimModel(SamplePostModel::class))
            ->enableKeyword(self::KEY_ACTION_ADD_META_BOXES)
            ->enableKeyword(self::KEY_ACTION_SAVE_POST)
            ->addMetaBoxView(SamplePostPropertyMetaBoxView::class);
    }
}
