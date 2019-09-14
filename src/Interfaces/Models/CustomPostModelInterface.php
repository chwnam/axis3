<?php

namespace Shoplic\Axis3\Interfaces\Models;

use Shoplic\Axis3\Interfaces\Models\FieldHolders\MetaFieldHolderInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\MetaFieldModelInterface;
use WP_Error;
use WP_Post;
use WP_Post_Type;

/**
 * Interface CustomPostModelInterface
 *
 * 커스텀 포스트 모델을 위한 인터페이스
 *
 * @package Shoplic\Axis3\Interfaces\Models
 * @since   1.0.0
 * @link    https://codex.wordpress.org/Function_Reference/register_post_type
 */
interface CustomPostModelInterface extends MetaFieldHolderInterface
{
    /**
     * 포스트 타입 이름을 리턴
     *
     * @return string
     */
    public static function getPostType(): string;

    /**
     * register_post_type() 두번째 인자인 포스트 옵션을 담은 배열을 리턴한다.
     *
     * @return array
     * @see    register_post_type()
     */
    public function getPostTypeArgs(): array;

    /**
     * @return WP_Post_Type|WP_Error
     */
    public function registerPostType();

    /**
     * @param int     $postId  포스트 ID
     * @param WP_Post $post    포스트 객체
     * @param bool    $updated 포스트가 기존 내용을 업데이트하는 것이면 참, 새 포스트면 거짓
     * @param array   $request $_GET, $_POST 같은 전역 변수
     *
     * @return void
     * @see    wp_insert_post() 'save_post' 액션과 깊은 관계가 있습니다.
     */
    public function saveFromRequest($postId, $post, $updated, &$request);

    /**
     * 커스텀 포스트와 맞물린 메타 필드를 등록합니다.
     *
     * @return void
     * @see    MetaFieldModelInterface::registerMetaField()
     */
    public function registerMetaFields();

    /**
     * 모델 자동 등록이 된 경우, 플러그인 활성화 때 이 콜백이 호출됩니다.
     * 모델의 기본 자료를 셋업할 때 유용합니다.
     *
     * @return void
     */
    public function activationSetup();

    /**
     * 모델 자동 등록이 된 경우, 플러그인 비활성화 때 이 콜백이 호출됩니다.
     * 모델의 기본 자료를 셋업할 때 유용합니다.
     *
     * @return void
     */
    public function deactivationCleanup();
}

/*
 NOTE: 아래를 참고하여 register_post_type 의 두번째 인자를 작성하세요.
    [
        'label'               => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
        'labels'              => [
            'name'                     => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'singular_name'            => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'add_new'                  => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'add_new_item'             => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'edit_item'                => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'new_item'                 => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'view_item'                => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'view_items'               => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'search_items'             => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'not_found'                => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'not_found_in_trash'       => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'parent_item_colon'        => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'all_items'                => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'archives'                 => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'attributes'               => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'insert_into_item'         => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'uploaded_to_this_item'    => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'featured_image'           => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'set_featured_image'       => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'remove_featured_image'    => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'use_featured_image'       => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'menu_name'                => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'filter_items_list'        => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'items_list_navigation'    => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'items_list'               => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'name_admin_bar'           => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'item_published'           => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'item_published_privately' => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'item_reverted_to_draft'   => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'item_scheduled'           => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
            'item_updated'             => _x('', 'Custom post type \'post_type\' argument.', 'textdomain'),
        ],
        'description'         => '',
        'public'              => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_nav_menus'   => true,
        'show_in_menu'        => true,
        'show_in_admin_bar'   => true,

        // Position guide:
        // 5 - below Posts
        // 10 - below Media
        // 15 - below Links
        // 20 - below Pages
        // 25 - below comments
        // 60 - below first separator
        // 65 - below Plugins
        // 70 - below Users
        // 75 - below Tools
        // 80 - below Settings
        // 100 - below second separator
        'menu_position'       => null,

        'menu_icon'       => null,
        'capability_type' => 'post',
        'capabilities'    => [
            'edit_post'   => "edit_{$capability_type}",
            'read_post'   => "read_{$capability_type}",
            'delete_post' => "delete_{$capability_type}",

            'edit_posts'         => "edit_{$capability_type}s",
            'edit_others_posts'  => "edit_others_{$capability_type}s",
            'publish_posts'      => "publish_{$capability_type}s",
            'read_private_posts' => "read_private_{$capability_type}s",

            'read'                   => "read",
            'delete_posts'           => delete_{$capability_type}s",
            'delete_private_posts'   => "delete_private_{$capability_type}s"
            'delete_published_posts' => "delete_published_{$capability_type}s"
            'delete_others_posts'    => "delete_others_{$capability_type}s"
            'edit_private_posts'     => "edit_private_{$capability_type}s"
            'edit_published_posts'   => "edit_published_{$capability_type}s"
            'create_posts]           => "edit_{$capability_type}s"
        ],
        'map_meta_cap'    => null,
        'hierarchical'    => false,

        // Supports guide:
        // 'title'
        // 'editor' (content)
        // 'author'
        // 'thumbnail' (featured image, current theme must also support post-thumbnails)
        // 'excerpt'
        // 'trackbacks'
        // 'custom-fields'
        // 'comments' (also will see comment count balloon on edit screen)
        // 'revisions' (will store revisions)
        // 'page-attributes' (menu order, hierarchical must be true to show Parent option)
        // 'post-formats' add post formats, see Post Formats
        'supports'        => ['title', 'editor'],

        'register_meta_box_cb' => null,
        'taxonomies'           => [],
        'has_archive'          => false,
        'rewrite'              => [
            'slug'       => '',
            'with_front' => true,
            'feeds'      => true,
            'pages'      => true,
            'ep_mask'    => EP_PERMALINK,
        ],
        'permalink_epmask'     => EP_PERMALINK,
        'query_var'            => '',
        'can_export'           => true,
        'delete_with_user'     => null,
        'show_in_rest'         => true,
        'rest_base'            => $post_type,
        'rest_controller_base' => 'WP_REST_Posts_Controller',
    ]
*/
