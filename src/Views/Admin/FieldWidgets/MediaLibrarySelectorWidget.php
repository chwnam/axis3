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
                $dataId = '';
                $value  = $this->getValue();
            } elseif ($this->args['saveField'] == 'id') {
                $dataId = $this->getValue();
                if (wp_attachment_is_image($dataId)) {
                    $value = wp_get_attachment_image_url($this->getValue());
                } else {
                    $value = wp_get_attachment_url($dataId);
                }
            } else {
                $dataId = '';
                $value  = '';
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
                        'readonly' => 'id' === $this->args['saveField'],
                        'title'    => $this->getRequiredMessage(),
                        'style'    => 'margin: 2px 10px 2px 0;',
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
                        'style' => 'margin: 2px 0;',
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
            'library'                => $this->args['library'],
            'params'                 => $this->args['params'],
            'saveField'              => $this->args['saveField'],
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

                /**
                 * array: media library 쿼리로 사용됨.
                 *
                 * 사용할 수 있는 필터는 제한되며, 더 확장을 원하는 경우라면 PHP 쪽에서
                 * add_filter() 로 접근해야 한다.
                 *
                 * @see: wp_ajax_query_attachments()
                 */
                'library'                => [],

                /**
                 * array: async-upload.php 로 파일 업로드시 변수를 추가.
                 *
                 * @see wp-includes/js/media-views.js
                 * @see wp.media.view.UploaderWindow.ready()
                 */
                'params'                 => [],

                // string: 'id', 'url', 중 택일.
                'saveField'              => 'url',
            ]
        );
    }
}
