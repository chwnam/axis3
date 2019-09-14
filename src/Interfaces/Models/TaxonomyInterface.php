<?php

namespace Shoplic\Axis3\Interfaces\Models;

use Shoplic\Axis3\Interfaces\Models\FieldHolders\MetaFieldHolderInterface;
use WP_Error;

/**
 * Interface TaxonomyInterface
 *
 * 택소노미를 위한 인터페이스
 *
 * @package Shoplic\Axis3\Interfaces\Models
 * @since   1.0.0
 * @link    https://codex.wordpress.org/Function_Reference/register_taxonomy
 */
interface TaxonomyInterface extends MetaFieldHolderInterface
{
    /**
     * 택소노미 식별을 위한 문자열.
     * register_taxonomy() 첫 번째 인자로 사용되는 문자열 리턴
     *
     * @return string
     */
    public static function getTaxonomy(): string;

    /**
     * register_taxonomy() 두 번째 인자로 사용되는 배열 리턴
     *
     * @return array
     * @see register_taxonomy()
     */
    public function getTaxonomyArgs(): array;

    /**
     * register_taxonomy() 세 번째 인자로 사용되는 문자열 리턴
     *
     * @return string|array
     */
    public function getObjectType();

    /**
     * 택소노미 등록을 맡은 메소드
     *
     * @return void|WP_Error
     */
    public function registerTaxonomy();

    /**
     * 커스텀 택소노미와 맞물린 메타 필드를 등록합니다.
     *
     * @return void
     * @see    MetaFieldModelInterface::registerMetaField()
     */
    public function registerMetaFields();

    /**
     * 플러그인 활성화시 1회 실행
     *
     * @return void
     */
    public function activationSetup();

    /**
     * 플러그인 비활성화시 1회 실행
     *
     * @return void
     */
    public function deactivationCleanup();
}

/*
  NOTE: 아래를 참고하여 register_post_type 의 두번째 인자를 작성하세요.
[
    'label'                 => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
    'labels'                => [
        'name'                       => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'singular_name'              => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'menu_name'                  => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'all_items'                  => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'edit_item'                  => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'view_item'                  => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'update_item'                => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'add_new_item'               => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'new_item_name'              => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'parent_item'                => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'parent_item_colon'          => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'search_items'               => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'popular_items'              => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'separate_items_with_commas' => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'add_or_remove_items'        => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'choose_from_most_used'      => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'not_found'                  => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
        'back_to_items'              => _x('', 'Custom taxonomy \'taxonomy\' argument.', 'textdomain'),
    ],
    'public'                => true,
    'publicly_queryable'    => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'show_in_nav_menus'     => true,
    'show_in_rest'          => true,
    'rest_base'             => '',
    'rest_controller_base'  => 'WP_REST_Terms_Controller',
    'show_tagcloud'         => true,
    'show_in_quick_edit'    => true,
    'register_meta_box_cb'  => null,
    'show_admin_column'     => true,
    'description'           => '',
    'hierarchical'          => false,
    'update_count_callback' => null,
    'query_var'             => '',
    'rewrite'               => [
        'slug'         => '',
        'with_front'   => true,
        'hierarchical' => true,
        'ep_mask'      => EP_NONE,
    ],
    'capabilities'          => [
        'manage_terms' => "manage_{$taxonomy}",
        'edit_terms'   => "manage_{$taxonomy}",
        'delete_terms' => "manage_{$taxonomy}",
        'assign_terms' => "manage_{$taxonomy}",
    ],
    'sort' => null,
]
*/
