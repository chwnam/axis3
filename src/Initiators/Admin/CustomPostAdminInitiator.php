<?php

namespace Shoplic\Axis3\Initiators\Admin;

use Shoplic\Axis3\Initiators\AutoHookInitiator;
use Shoplic\Axis3\Interfaces\Models\CustomPostModelInterface;
use Shoplic\Axis3\Views\Admin\MetaBoxView;
use WP_Post;
use WP_Query;
use WP_Screen;

use function Shoplic\Axis3\Functions\callbackFreeTask;

/**
 * Class CustomPostAdminInitiator
 *
 * 커스텀 포스트의 관리 화면을 보조하는 기본 전수자 클래스.
 *
 * public function getModel() 을 구현해야 한다.
 *
 * enableKeyword() 메소에 KEY_* 상수를 집어 넣어 잘 알려진 몇몇 대표적인
 * 관리 화면의 커스텀 요소에 대한 액션/필터를 빠르게 설정할 수 있다.
 *
 * 키워드는 해당 상수의 주석을 참고.
 *
 * @package Shoplic\Axis3\Initiators\Admin
 * @since   1.0.0
 */
abstract class CustomPostAdminInitiator extends AutoHookInitiator
{
    /**
     * 메타 박스 추가를 위한 키워드.
     * 키워드를 추가하고 addMetaBoxView() 메소드로 메타박스 뷰를 추가한다.
     *
     * @var string
     * @see CustomPostAdminInitiator::addMetaBoxView()
     */
    const KEY_ACTION_ADD_META_BOXES = 'add_meta_boxes';

    /**
     * 목록 테이블에 열 확장을 위한 키워드.
     * 키워드를 추가하고 filterCustomColumns(), actionCustomColumn() 메소드를 오버라이드한다.
     *
     * @var string
     * @see CustomPostAdminInitiator::actionCustomColumn()
     * @see CustomPostAdminInitiator::filterCustomColumns()
     */
    const KEY_ACTION_CUSTOM_COLUMNS = 'custom_columns';

    /**
     * 포스트 저장을 위한 키워드.
     * 키워드를 추가하면 기본 actionSavePost() 메소드가 기본적인 역할을 처리해 준다.
     * 필요시 오버라이드.
     *
     * @var string
     * @see CustomPostAdminInitiator::actionSavePost()
     */
    const KEY_ACTION_SAVE_POST = 'save_post';

    /**
     * 목록 테이블에서 포스트 쿼리 전 쿼리 수정을 위한 액션 동작.
     * 키워드를 추가하고 actionPreGetPosts() 메소드를 오버라이드한다.
     *
     * @var string
     * @see CustomPostAdminInitiator::addMetaBoxView()
     */
    const KEY_ACTION_PRE_GET_POSTS = 'pre_get_posts';

    /**
     * 목록 테이블 상단 '필터' 버튼이 나오기 전에 뭔가 더 붙여줄 수 있는 액션을 추가.
     */
    const KEY_ACTION_RESTRICT_MANAGE_POSTS = 'restrict_manage_posts';

    /**
     * 정렬 가능한 열 확장을 위한 키워드.
     * 키워드를 추가하고 filterSortableColumns() 메소드를 오버라이드한다.
     * 원하는대로 정렬하려면 반드시 KEY_ACTION_PRE_GET_POSTS 와 연계해야 할 것이다.
     *
     * @var string
     * @see CustomPostAdminInitiator::filterSortableColumns()
     */
    const KEY_FILTER_CUSTOM_SORTABLE_COLUMNS = 'sortable_columns';

    /**
     * 제목란의 플레이스홀더 문구 수정.
     *
     * @see wp-admin/edit-form-advanced.php
     */
    const KEY_FILTER_ENTER_TITLE_HERE = 'enter_title_here';

    /**
     * 디폴트 에디터 콘텐트를 수정. 클래식 에디터.
     *
     * @see \_WP_Editors::editor()
     */
    const KEY_FILTER_THE_EDITOR_CONTENT = 'the_editor_content';

    /** @var string[] 미리 정의된 기능의 키워드 */
    private $keywords = [];

    /** @var string[]|MetaBoxView[] 등록된 메타 박스 */
    private $metaBoxViews = [];

    private $settingErrors = [];

    abstract public function getModel(): CustomPostModelInterface;

    public function action_admin_init()
    {
        $postType = $this->getModel()->getPostType();

        if ($this->isEnabled(self::KEY_ACTION_CUSTOM_COLUMNS)) {
            add_filter("manage_{$postType}_posts_columns", [$this, 'filterCustomColumns']);
            add_action("manage_{$postType}_posts_custom_column", [$this, 'actionCustomColumn'], 10, 2);
        }

        if ($this->isEnabled(self::KEY_ACTION_SAVE_POST)) {
            add_action("save_post_{$postType}", [$this, 'actionSavePost'], 10, 3);
        }
    }

    /**
     * 스크린의 포스트 타입을 보고 일치했을 때 필요한 액션/필터를 발동시킨다.
     *
     * @callback
     * @action   current_screen
     *
     * @param WP_Screen $screen
     */
    public function action_current_screen($screen)
    {
        $postType = $this->getModel()->getPostType();
        if ($screen->post_type !== $postType) {
            return;
        }

        if ($this->isEnabled(self::KEY_ACTION_PRE_GET_POSTS)) {
            add_action('pre_get_posts', [$this, 'actionPreGetPosts']);
        }

        if ('edit' === $screen->base) {
            // 포스트 목록 화면
            if ($this->isEnabled(self::KEY_FILTER_CUSTOM_SORTABLE_COLUMNS)) {
                add_filter(
                    "manage_{$screen->id}_sortable_columns",
                    [$this, 'filterSortableColumns']
                );
            }

            if ($this->isEnabled(self::KEY_ACTION_RESTRICT_MANAGE_POSTS)) {
                add_action(
                    'restrict_manage_posts',
                    [$this, 'actionRestrictManagePosts'],
                    10,
                    2
                );
            }
        } elseif ('post' === $screen->base) {
            // 포스트 편집 화면
            $this->settingErrors = get_transient('axis3_post_' . $postType . '_update_warnings');
            delete_transient('axis3_post_' . $postType . '_update_warnings');

            if (!empty($this->settingErrors)) {
                add_action('admin_notices', [$this, 'outputValidationErrors']);
            }

            if ($this->isEnabled(self::KEY_ACTION_ADD_META_BOXES)) {
                add_action("add_meta_boxes_{$postType}", [$this, 'actionAddMetaBoxes']);
                add_action('admin_enqueue_scripts', [$this, 'adminEnqueueScripts'], 100);
            }

            if ($this->isEnabled(self::KEY_FILTER_ENTER_TITLE_HERE)) {
                add_filter('enter_title_here', [$this, 'filterEnterTitleHere'], 10, 2);
            }

            if ($this->isEnabled(self::KEY_FILTER_THE_EDITOR_CONTENT)) {
                add_filter('the_editor_content', [$this, 'filterTheEditorContent'], 10, 2);
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
            (($screen = get_current_screen()) && 'post' === $screen->base) &&
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

            $this->beforeSavePost($post);

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

            $this->afterSavePost($post);
        }
    }

    /**
     * 목록 화면에서 커스텀 컬럼 추가 콜백
     *
     * @callback
     * @filter      manage_{$post_type}_posts_columns
     *
     * @param array $columns 칼럼 목록.
     *
     * @return array
     */
    public function filterCustomColumns($columns): array
    {
        return $columns;
    }

    /**
     * 목록 화면에서 커스텀 컬럼의 내용 추가.
     *
     * @callback
     * @action      manage_{$post->post_type}_posts_custom_column
     *
     * @param string $columnName 킬럼 이름. 'filterCustomColumns'에서 리턴한 배열의 키가 전달된다.
     * @param int    $postId     포스트 아이디.
     *
     * @return void
     */
    public function actionCustomColumn(string $columnName, int $postId)
    {
    }

    /**
     * 목록 테이블에 필터 추가.
     *
     * @param string $postType 포스트 타입.
     * @param string $which    위치. top, bottom, bar.
     */
    public function actionRestrictManagePosts(string $postType, string $which)
    {
    }

    /**
     * 정렬 가능한 칼럼 목록 리턴.
     *
     * @callback
     * @filter      manage_{$screen->id}_sortable_columns
     *
     * 목록 화면의 커스텀 열 중, 정렬 가능한 열을 추가한다.
     * 정렬 가능한 칼럼을 입력해도 절대 워드프레스는 알아서 정렬을 해 주지 않는다.
     * pre_get_posts 액션을 사용해 원하는대로 데이터가 정렬되도록 쿼리를 수정해야 한다.
     *
     * @param array $columns   키/값의 배열.
     *                         - 키는 칼럼의 키.
     *                         - 값은 칼럼을 클릭했을 때 GET orderby 파라미터의 값이 된다. 길이 2짜리 배열을 입력할 수 있다.
     *                         - 값이 배열일 경우 0번째는 칼럼의 키, 1번째는 불리언 변수.
     *                         - 불리언 변수가 true 인 경우 초기값의 정렬이 역순정렬이 된다.
     *                         날짜 같은 것을 최신순으로 먼저 보여줄 때 유용하다.
     *
     * @return array
     *
     * @example
     * [
     *   'foo' => 'foo',         // 가장 일반적인 설정. 올림차순 -> 내림차순으로 동작.
     *   'bar' => 'b',           // 'orderby=b' 처럼 파라미터가 전달될 것임.
     *   'baz' => ['baz', true], // 'baz' 칼럼은 내림차순 -> 올림차순으로 동작.
     * ]
     */
    public function filterSortableColumns(array $columns)
    {
        return $columns;
    }

    /**
     * 제목 입력란 플레이스홀더 변경 필터 콜백.
     *
     * @callback
     * @filter      enter_title_here
     *
     * @param string   $title 제목.
     * @param \WP_Post $post  포스트.
     *
     * @return string
     * @used-by     action_current_screen()
     */
    public function filterEnterTitleHere($title, $post)
    {
        return $title;
    }

    /**
     * 에디터에 들어가는 초기 내용 필터 콜백.
     *
     * @callback
     * @filter      the_editor_content
     *
     * @param string $content       내용.
     * @param string $defaultEditor 현재 사용자에게 지정된 기본 편집기. 'html'이나 'tinymce'.
     *
     * @return string
     * @used-by     CustomPostAdminInitiator::action_current_screen()
     */
    public function filterTheEditorContent($content, $defaultEditor)
    {
        return $content;
    }

    /**
     * pre_get_posts 액션 콜백.
     *
     * @callback
     * @action      pre_get_posts
     *
     * @param WP_Query $query
     */
    public function actionPreGetPosts(WP_Query $query)
    {
    }

    /**
     * 저장 후 불리는 메소드. 오버라이드하여 사용할 수 있다.
     *
     * @param WP_Post $post 저장된 포스트.
     */
    protected function afterSavePost($post)
    {
    }

    /**
     * 저장 전 불리는 메소드. 오버라이드하여 사용할 수 있다.
     *
     * @param WP_Post $post 저장된 포스트.
     */
    protected function beforeSavePost($post)
    {
    }
}
