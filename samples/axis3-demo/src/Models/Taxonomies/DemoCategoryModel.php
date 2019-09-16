<?php

namespace Shoplic\Axis3Sample\Models\Taxonomies;

use Shoplic\Axis3\Models\TaxonomyModel;
use Shoplic\Axis3Sample\Models\CustomPosts\DemoPostModel;
use function Shoplic\Axis3Sample\Functions\prefixed;

class DemoCategoryModel extends TaxonomyModel
{
    public static function getTaxonomy(): string
    {
        return prefixed('post_cat');
    }

    public function getObjectType()
    {
        return [DemoPostModel::getPostType()];
    }

    public function getTaxonomyArgs(): array
    {
        return [
            'labels'             => [
                'name'                       => _x('데모 분류들', '데모 분류 번역', 'axis3-demo'),
                'singular_name'              => _x('데모 분류', '데모 분류 번역', 'axis3-demo'),
                'menu_name'                  => _x('데모 분류', '데모 분류 번역', 'axis3-demo'),
                'all_items'                  => _x('모든 데모 분류', '데모 분류 번역', 'axis3-demo'),
                'edit_item'                  => _x('데모 분류 편집', '데모 분류 번역', 'axis3-demo'),
                'view_item'                  => _x('데모 분류 보기', '데모 분류 번역', 'axis3-demo'),
                'update_item'                => _x('데모 분류 업데이트', '데모 분류 번역', 'axis3-demo'),
                'add_new_item'               => _x('새 데모 분류 추가', '데모 분류 번역', 'axis3-demo'),
                'new_item_name'              => _x('새 데모 분류', '데모 분류 번역', 'axis3-demo'),
                'parent_item'                => _x('상위 데모 분류', '데모 분류 번역', 'axis3-demo'),
                'parent_item_colon'          => _x('상위 데모 분류: ', '데모 분류 번역', 'axis3-demo'),
                'search_items'               => _x('데모 분류 검색', '데모 분류 번역', 'axis3-demo'),
                'popular_items'              => _x('자주 사용되는 데모 분류', '데모 분류 번역', 'axis3-demo'),
                'separate_items_with_commas' => _x('쉼표로 데모 분류를 구분', '데모 분류 번역', 'axis3-demo'),
                'add_or_remove_items'        => _x('데모 분류를 추가하거나 삭제', '데모 분류 번역', 'axis3-demo'),
                'choose_from_most_used'      => _x('많이 사용된 목록에서 선택', '데모 분류 번역', 'axis3-demo'),
                'not_found'                  => _x('찾을 수 없음', '데모 분류 번역', 'axis3-demo'),
                'back_to_items'              => _x('목록으로 돌아가기', '데모 분류 번역', 'axis3-demo'),
            ],
            'public'             => true,
            'show_in_rest'       => true,
            'show_tagcloud'      => true,
            'show_in_quick_edit' => true,
            'show_admin_column'  => true,
            'description'        => '데모 분류 택소노미입니다. 위계적 (카테고리) 택소노미입니다.',
            'hierarchical'       => true,
        ];
    }
}
