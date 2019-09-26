<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\inputTag;
use function Shoplic\Axis3\Functions\openTag;

class MediaLibrarySelectorWidget extends BaseFieldWidget
{
    public function outputWidgetCore()
    {
        openTag(
            'div',
            [
                'id'    => $this->getId(),
                'class' => 'axis3-field-widget axis3-compound-widget axis3-media-library-selector-widget',
            ]
        );

        {
            if ($this->args['saveField'] == 'url') {
                $value  = $this->getValue();
                $dataId = '';
            } elseif ($this->args['saveField'] == 'id') {
                $value  = wp_get_attachment_image_url($this->getValue());
                $dataId = $this->getValue();
            } else {
                $value  = '';
                $dataId = '';
            }

            inputTag(
                wp_parse_args(
                    $this->args['inputAttrs'],
                    [
                        'id'       => $this->getId() . '-text',
                        'name'     => $this->getName(),
                        'value'    => $value,
                        'type'     => 'text',
                        'class'    => 'axis3-field-widget axis3-media-library-selector-widget text',
                        'data-id'  => $dataId,
                        'required' => $this->isRequired(),
                        'title'    => $this->getRequiredMessage(),
                    ]
                )
            );

            inputTag(
                wp_parse_args(
                    $this->args['buttonAttrs'],
                    [
                        'id'    => $this->getId() . '-button',
                        'type'  => 'button',
                        'class' => 'axis3-field-widget axis3-media-library-selector-widget button button-secondary',
                        'value' => $this->args['textSelectMedia'],
                    ]
                )
            );

            echo '<span class="spacer"></span>';

            openTag(
                'a',
                [
                    'id'     => $this->getId() . '-preview',
                    'href'   => $value ? $value : '#',
                    'target' => '_blank',
                ]
            );

            esc_html_e($value ? $this->args['textPreview'] : $this->args['textPreviewChooseImage']);

            closeTag('a');
        }

        closeTag('div');

        $opt = [
            'textButton'             => $this->args['textButton'],
            'textSelectMedia'        => $this->args['textSelectMedia'],
            'textTitle'              => $this->args['textTitle'],
            'textPreview'            => $this->args['textPreview'],
            'textPreviewChooseImage' => $this->args['textPreviewChooseImage'],
            'saveField'              => 'url',
        ];

        wp_add_inline_script(
            'axis3-media-library-selector-widget',
            sprintf('jQuery("#%s").mediaLibrarySelector(%s);', $this->getId(), wp_json_encode($opt))
        );
    }

    public function onceBeforeRender()
    {
        if (!wp_script_is('axis3-media-library-selector-widget')) {
            wp_enqueue_media();
            wp_enqueue_script('axis3-media-library-selector-widget');
        }
    }

    public function renderDescription()
    {
        echo '<span class="description">' . wp_kses_post($this->getDescription()) . '</span>';
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                // array: 인풋 상자의 속성 목록
                'inputAttrs'             => [],

                // array: 버튼의 속성 목록
                'buttonAttrs'            => [],

                // string: 텍스트. 미디어 라이브러리 창의 텍스트.
                'textTitle'              => __('Select or upload media', 'axis3'),

                // string: 텍스트. 미디어 라이브러리의 미디어 선택 버튼 레이블.
                'textButton'             => __('Use this media', 'axis3'),

                // string: 텍스트. 미디어 라이브러리 창을 띄우는 버튼의 레이블.
                'textSelectMedia'        => __('Select Media', 'axis3'),

                // string: 텍스트. 미리 보기 <a> 태그 텍스트.
                'textPreview'            => __('Preview', 'axis3'),

                // string: 텍스트. 미리보기 <a> 태그 텍스트. 선택된 이미지 없을 때.
                'textPreviewChooseImage' => __('Choose an image', 'axis3'),

                // string: 'id', 'url', 중 택일.
                'saveField'              => 'url',
            ]
        );
    }
}
