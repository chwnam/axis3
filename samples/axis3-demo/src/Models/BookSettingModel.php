<?php

namespace Shoplic\Axis3Sample\Models;

use Shoplic\Axis3\Models\SettingsModel;
use Shoplic\Axis3\Models\ValueTypes\TextType;

class BookSettingModel extends SettingsModel
{
    public function setup($args = [])
    {
        $this->setOptionGroup('book_setting');
    }

    public function getFieldTestOption()
    {
        return $this->claimOptionField(
            $this->getStarter()->prefixed('test_option'),
            function () {
                return [
                    'label'       => '테스트 옵션',
                    'shortLabel'  => '테',
                    'description' => '테스트 옵션의 설명란입니다.',
                    'valueType'   => new TextType(),
                    'required'    => true,
                ];
            }
        );
    }
}
