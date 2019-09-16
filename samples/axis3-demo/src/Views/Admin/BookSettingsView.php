<?php

namespace Shoplic\Axis3Sample\Views\Admin;

use Shoplic\Axis3\Views\Admin\FieldWidgets\InputWidget;
use Shoplic\Axis3\Views\Admin\SettingsView;
use Shoplic\Axis3Sample\Models\BookSettingModel;

class BookSettingsView extends SettingsView
{
    public function setup($args = array())
    {
        $this->setPage('book-setting');
    }

    protected function prepareSettings()
    {
        /** @var BookSettingModel $model */
        $model = $this->claimModel(BookSettingModel::class);

        $this
            ->setOptionGroup($model->getOptionGroup())
            ->addSection('general', '일반설정')
            ->addField(
                'general',
                new InputWidget(
                    $model->getFieldTestOption(),
                    [
                        'brDesc'          => false,
                        'tooltip'         => true,
                        'requiredMessage' => '필수 항목이에요.',
                    ]
                )
            );
    }
}
