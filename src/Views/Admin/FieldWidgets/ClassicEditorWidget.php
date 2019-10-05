<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\openTag;

class ClassicEditorWidget extends BaseFieldWidget
{
    private static $dummyId = 'axis3-classic-editor-dummy-id';

    private static $dummyName = 'axis3-classic-editor-dummy-name';

    private static $htmlTemplate = false;

    public function outputWidgetCore()
    {
        ob_start();
        wp_editor(
            '{{{ data.content }}}',
            static::$dummyId,
            array_merge($this->args['wpEditor'], ['textarea_name' => static::$dummyName])
        );
        static::$htmlTemplate = trim(str_replace(
                [
                    static::$dummyId,
                    static::$dummyName,
                ],
                [
                    '{{ data.editorId }}',
                    '{{ data.editorName }}',
                ],
                ob_get_clean())
        );

        echo openTag(
            'div',
            array_merge(
                [
                    'id'    => 'axis3-classic-editor-' . $this->getId(),
                    'class' => 'axis3-field-widget axis3-classic-editor-widget',
                ],
                $this->args['attrs']
            )
        );
        echo closeTag('div');
    }

    public function onceBeforeRender()
    {
        if (!wp_script_is('wp-util')) {
            wp_enqueue_script('wp-util');
        }

        $varObj = 'axis3ClassicEditorWidget_' . str_replace('-', '_', $this->getId());

        $this->enqueueScript(
            'axis3-classic-editor-widget',
            'admin/field-widgets/classic-editor.js',
            ['jquery', 'wp-util'],
            $this->getStarter()->getVersion(),
            true,
            $varObj,
            [
                'dummyId'    => static::$dummyId,
                'editorId'   => $this->getId(),
                'editorName' => $this->getName(),
                'content'    => $this->getValue(),
                'target'     => '#axis3-classic-editor-' . $this->getId(),
            ],
            "(function ($) {
                $(document).ready(function ($) {
                    var obj = {$varObj};
                    axis3ClassicEditor(obj); 
                });
            })(jQuery);"
        );

        if (is_admin()) {
            add_action('admin_print_footer_scripts', [$this, 'outputHtmlTemplate']);
        } else {
            add_action('wp_print_footer_scripts', [$this, 'outputHtmlTemplate']);
        }
    }

    public function outputHtmlTemplate()
    {
        echo "\n<script type='text/template' id='tmpl-axis3-classic-editor-widget'>\n";
        echo static::$htmlTemplate;
        echo "\n</script>";
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * array: wp_editor() 의 세번째 인자.
                 *
                 * @link https://codex.wordpress.org/Function_Reference/wp_editor
                 * @see  \_WP_Editors::parse_settings()
                 */
                'wpEditor' => [
                    'textarea_rows' => 3,
                ],

                /**
                 * array: 에디터 최외곽 태그에 붙는 별도의 속성
                 */
                'attrs'    => [],
            ]
        );
    }
}
