<?php

namespace Shoplic\Axis3\Initiators\Admin;

use Shoplic\Axis3\Initiators\AutoHookInitiator;
use Shoplic\Axis3\Interfaces\Models\CustomPostModelInterface;
use Shoplic\Axis3\Views\Admin\MetaBoxView;
use WP_Post;
use WP_Screen;
use function Shoplic\Axis3\Functions\callbackFreeTask;

/**
 * Class CustomPostAdminInitiator
 *
 * @package Shoplic\Axis3\Initiators\Admin
 * @since   1.0.0
 */
abstract class CustomPostAdminInitiator extends AutoHookInitiator
{
    const KEY_ACTION_ADD_META_BOXES = 'add_meta_boxes';
    const KEY_ACTION_SAVE_POST      = 'save_post';

    /** @var string[] 미리 정의된 기능의 키워드 */
    private $keywords = [];

    /** @var string[]|MetaBoxView[] 등록된 메타 박스 */
    private $metaBoxViews = [];

    private $settingErrors = [];

    abstract public function getModel(): CustomPostModelInterface;

    public function action_current_screen(WP_Screen $screen)
    {
        $postType = $this->getModel()->getPostType();

        if ($screen->post_type !== $postType) {
            return;
        }

        if ($this->isEnabled(self::KEY_ACTION_ADD_META_BOXES)) {
            add_action("add_meta_boxes_{$postType}", [$this, 'actionAddMetaBoxes']);
        }

        if ($this->isEnabled(self::KEY_ACTION_SAVE_POST)) {
            add_action("save_post_{$postType}", [$this, 'actionSavePost'], 10, 3);
        }

        if ('edit' === $screen->base) {
            // 포스트 목록 화면
        } elseif ('post' === $screen->base) {
            // 포스트 편집 화면
            $this->settingErrors = get_transient('axis3_post_' . $postType . '_update_warnings');
            delete_transient('axis3_post_' . $postType . '_update_warnings');

            if (!empty($this->settingErrors)) {
                add_action('admin_notices', [$this, 'outputValidationErrors']);
            }

            if ($this->isEnabled(self::KEY_ACTION_ADD_META_BOXES)) {
                add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts'], 100);
            }
        }
    }

    /**
     * 포스트 편집 화면에서 검증 에러 메시지 출력
     *
     * @callback
     * @action      admin_notices
     */
    public function outputValidationErrors()
    {
        $output    = '';
        $doneCodes = [];

        foreach ($this->settingErrors as $error) {
            // 중복된 메시지는 걸러낸다.
            if (isset($doneCodes[$error['code']])) {
                continue;
            }

            $cssId    = 'axis3-post-' . $this->getModel()->getPostType() . '_update-warning';
            $cssClass = "axis3-update-warning notice notice-{$error['type']} is-dismissible";

            $output .= "<div id='{$cssId}' class='{$cssClass}'>\n";
            $output .= '<p><strong>' . esc_html($error['message']) . '</strong></p>';
            $output .= "</div>\n";

            $doneCodes[$error['code']] = true;
        }

        echo $output;
    }

    /**
     * @callback
     * @action      admin_enqueue_scripts
     */
    public function adminEnqueueScripts()
    {
        wp_enqueue_script('axis3-field-widgets');
        wp_enqueue_style('axis3-field-widgets');
    }

    public function enableKeyword(string $keyword): CustomPostAdminInitiator
    {
        $this->keywords[$keyword] = true;

        return $this;
    }

    public function disableKeyword(string $keyword): CustomPostAdminInitiator
    {
        $this->keywords[$keyword] = false;
    }

    public function isEnabled(string $keyword): bool
    {
        return $this->keywords[$keyword] ?? false;
    }

    public function addMetaBoxView($view): CustomPostAdminInitiator
    {
        $this->metaBoxViews[] = $view;

        return $this;
    }

    public function getMetaBoxViews(): array
    {
        return $this->metaBoxViews;
    }

    /**
     * 메타 박스 추가 콜백
     *
     * @callback
     * @action add_meta_boxes
     *
     * @param WP_Post $post
     */
    public function actionAddMetaBoxes(WP_Post $post)
    {
        if ($post->post_type === $this->getModel()->getPostType()) {
            foreach ($this->getMetaBoxViews() as $view) {
                /** @var MetaBoxView|null $instance */
                $instance = $this->claimView($view);
                if ($instance) {
                    $instance->addMetaBox();
                }
            }
        }
    }

    /**
     * 메타 박스 저장 콜백
     *
     * @param int     $postId
     * @param WP_Post $post
     * @param bool    $updated
     */
    public function actionSavePost(int $postId, WP_Post $post, bool $updated)
    {
        if (
            $updated &&
            $postId == $post->ID &&
            $post->post_type == $this->getModel()->getPostType() &&
            $post->post_status !== 'trash' &&
            !(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) &&
            current_user_can('edit_post', $postId)
        ) {
            foreach ($this->getMetaBoxViews() as $view) {
                /** @var MetaBoxView|null $instance */
                $instance = $this->claimView($view);
                if (!$instance ||
                    !wp_verify_nonce($_REQUEST[$instance->getNonceParam()] ?? '', $instance->getNonceAction())
                ) {
                    wp_die(
                        sprintf(__('Nonce failure of class \'%s\'', 'axis3'), get_class($instance)),
                        __('Nonce Failure', 'axis3'),
                        [
                            'back_link' => true,
                            'response'  => 400,
                        ]
                    );
                }
            }

            callbackFreeTask(
                "save_post_{$this->getModel()->getPostType()}",
                [$this, 'actionSavePost'],
                function (CustomPostModelInterface $model, int $postId, WP_Post $post, bool $updated, array &$request) {
                    $model->saveFromRequest($postId, $post, $updated, $request);
                },
                [$this->getModel(), $postId, $post, $updated, &$_REQUEST],
                false,
                3
            );

            set_transient('axis3_post_' . $post->post_type . '_update_warnings', get_settings_errors(), 30);
        }
    }
}
