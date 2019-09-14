<?php

namespace Shoplic\Axis3Sample\Models\Taxonomies;

use Shoplic\Axis3\Models\TaxonomyModel;
use Shoplic\Axis3Sample\Models\CustomPosts\SamplePostModel;
use function Shoplic\Axis3Sample\Functions\prefixed;

class SampleTagModel extends TaxonomyModel
{
    public static function getTaxonomy(): string
    {
        return prefixed('post_tag');
    }

    public function getObjectType()
    {
        return [SamplePostModel::getPostType()];
    }

    public function getTaxonomyArgs(): array
    {
        return [
            'labels'             => [
                'name'                       => _x('샘플 태그들들', '샘플 태그 번역', 'axis3_sample'),
                'singular_name'              => _x('샘플 태그', '샘플 태그 번역', 'axis3_sample'),
                'menu_name'                  => _x('샘플 태그', '샘플 태그 번역', 'axis3_sample'),
                'all_items'                  => _x('모든 샘플 태그', '샘플 태그 번역', 'axis3_sample'),
                'edit_item'                  => _x('샘플 태그 편집', '샘플 태그 번역', 'axis3_sample'),
                'view_item'                  => _x('샘플 태그 보기', '샘플 태그 번역', 'axis3_sample'),
                'update_item'                => _x('샘플 태그 업데이트', '샘플 태그 번역', 'axis3_sample'),
                'add_new_item'               => _x('새 샘플 태그 추가', '샘플 태그 번역', 'axis3_sample'),
                'new_item_name'              => _x('새 샘플 태그', '샘플 태그 번역', 'axis3_sample'),
                'parent_item'                => _x('상위 샘플 태그', '샘플 태그 번역', 'axis3_sample'),
                'parent_item_colon'          => _x('상위 샘플 태그: ', '샘플 태그 번역', 'axis3_sample'),
                'search_items'               => _x('샘플 태그 검색', '샘플 태그 번역', 'axis3_sample'),
                'popular_items'              => _x('자주 사용되는 샘플 태그', '샘플 태그 번역', 'axis3_sample'),
                'separate_items_with_commas' => _x('쉼표로 샘플 태그를 구분', '샘플 태그 번역', 'axis3_sample'),
                'add_or_remove_items'        => _x('샘플 태그를 추가하거나 삭제', '샘플 태그 번역', 'axis3_sample'),
                'choose_from_most_used'      => _x('많이 사용된 목록에서 선택', '샘플 태그 번역', 'axis3_sample'),
                'not_found'                  => _x('찾을 수 없음', '샘플 태그 번역', 'axis3_sample'),
                'back_to_items'              => _x('목록으로 돌아가기', '샘플 태그 번역', 'axis3_sample'),
            ],
            'public'             => true,
            'show_in_rest'       => true,
            'show_tagcloud'      => true,
            'show_in_quick_edit' => true,
            'show_admin_column'  => true,
            'description'        => '샘플 태그 택소노미입니다. 비위계적인 (태그) 택소노미입니다.',
            'hierarchical'       => false,
        ];
    }
}
