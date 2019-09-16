<?php

namespace Shoplic\Axis3Sample\Views\Admin\Metaboxes;

use Shoplic\Axis3\Views\Admin\FieldWidgets\InputWidget;
use Shoplic\Axis3\Views\Admin\FieldWidgets\SelectWidget;
use Shoplic\Axis3\Views\Admin\PropertyMetaBoxView;
use Shoplic\Axis3Sample\Models\CustomPosts\DemoPostModel;

class DemoPropertyMetaBoxView extends PropertyMetaBoxView
{
    public function getNonceAction(): string
    {
        return 'sample-property-meta-box';
    }

    public function getNonceParam(): string
    {
        return 'sample_property_nonce';
    }

    public function getId(): string
    {
        return 'sample-properties';
    }

    public function getTitle(): string
    {
        return __('샘플 속성들', 'axis3-demo');
    }

    public function getFieldWidgets($post)
    {
        /** @var DemoPostModel $model */
        $model = $this->claimModel(DemoPostModel::class);

        return [
            new InputWidget(
                $model->getFieldPostPlainText(),
                []
            ),
            new InputWidget(
                $model->getFieldPostRequiredText(),
                []
            ),
            new InputWidget(
                $model->getFieldPostEmail(),
                [
                    'attrs' => ['type' => 'email'],
                ]
            ),
            new InputWidget(
                $model->getFieldPostHasDefaultValue(),
                []
            ),
            new InputWidget(
                $model->getFieldPostInteger01(),
                [
                    'attrs' => ['type' => 'number'],
                ]
            ),
            new InputWidget(
                $model->getFieldPostInteger02(),
                [
                    'attrs' => [
                        'type' => 'number',
                        'min'  => $model->getFieldPostInteger02()->getValueType()->getArg('min'),
                    ],
                ]
            ),
            new InputWidget(
                $model->getFieldPostInteger03(),
                [
                    'attrs' => [
                        'type' => 'number',
                        'min'  => $model->getFieldPostInteger03()->getValueType()->getArg('min'),
                    ],
                ]
            ),
            new SelectWidget(
                $model->getFieldPostSelect(),
                [
                ]
            ),
        ];
    }
}
