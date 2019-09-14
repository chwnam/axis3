<?php

namespace Shoplic\Axis3Sample\Models\CustomPosts;

use Shoplic\Axis3\Models\CustomPostModel;
use Shoplic\Axis3\Models\ValueTypes\DatetimeType;
use Shoplic\Axis3\Models\ValueTypes\DoubleType;
use Shoplic\Axis3\Models\ValueTypes\DummyType;
use Shoplic\Axis3\Models\ValueTypes\IntType;
use Shoplic\Axis3\Models\ValueTypes\TextType;
use function Shoplic\Axis3Sample\Functions\prefixed;

class SamplePostModel extends CustomPostModel
{
    public static function getPostType(): string
    {
        return prefixed('post');
    }

    public function getPostTypeArgs(): array
    {
        return [
            'labels'            => [
                'name'                     => _x('샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'singular_name'            => _x('샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'add_new'                  => _x('새로 추가', 'axis3 sample book', 'axis3_sample'),
                'add_new_item'             => _x('새 샘플 포스트 추가', 'axis3 sample book', 'axis3_sample'),
                'edit_item'                => _x('샘플 포스트 편집', 'axis3 sample book', 'axis3_sample'),
                'new_item'                 => _x('새 샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'view_item'                => _x('샘플 포스트 보기', 'axis3 sample book', 'axis3_sample'),
                'view_items'               => _x('샘플 포스트 목록 보기', 'axis3 sample book', 'axis3_sample'),
                'search_items'             => _x('책 검색', 'axis3 sample book', 'axis3_sample'),
                'not_found'                => _x('찾을 수 없음', 'axis3 sample book', 'axis3_sample'),
                'not_found_in_trash'       => _x('휴지통에서 찾을 수 없음', 'axis3 sample book', 'axis3_sample'),
                'parent_item_colon'        => _x('상위 샘플 포스트:', 'axis3 sample book', 'axis3_sample'),
                'all_items'                => _x('모든 샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'archives'                 => _x('샘플 포스트 목록 페이지', 'axis3 sample book', 'axis3_sample'),
                'attributes'               => _x('속성', 'axis3 sample book', 'axis3_sample'),
                'insert_into_item'         => _x('샘플 포스트에 삽입', 'axis3 sample book', 'axis3_sample'),
                'uploaded_to_this_item'    => _x('이 샘플 포스트로 업로드', 'axis3 sample book', 'axis3_sample'),
                'featured_image'           => _x('대표 이미지', 'axis3 sample book', 'axis3_sample'),
                'set_featured_image'       => _x('대표 이미지로 설정', 'axis3 sample book', 'axis3_sample'),
                'remove_featured_image'    => _x('대표 이미지 제거', 'axis3 sample book', 'axis3_sample'),
                'use_featured_image'       => _x('대표 이미지 사용', 'axis3 sample book', 'axis3_sample'),
                'menu_name'                => _x('샘플 포스트 목록', 'axis3 sample book', 'axis3_sample'),
                'filter_items_list'        => _x('샘플 포스트 목록 필터', 'axis3 sample book', 'axis3_sample'),
                'items_list_navigation'    => _x('샘플 포스트 목록 네비게이션', 'axis3 sample book', 'axis3_sample'),
                'items_list'               => _x('샘플 포스트 목록', 'axis3 sample book', 'axis3_sample'),
                'name_admin_bar'           => _x('샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'item_published'           => _x('공개된 샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'item_published_privately' => _x('비공개 샘플 포스트', 'axis3 sample book', 'axis3_sample'),
                'item_reverted_to_draft'   => _x('임시 작성 샘플 포스트로 변경', 'axis3 sample book', 'axis3_sample'),
                'item_scheduled'           => _x('샘플 포스트 공개 예약됨', 'axis3 sample book', 'axis3_sample'),
                'item_updated'             => _x('샘플 포스트 업데이트됨', 'axis3 sample book', 'axis3_sample'),
            ],
            'description'       => '샘플 포스트입니다.',
            'public'            => true,
            'hierarchical'      => false,
            'show_in_nav_menus' => false,
            'menu_icon'         => 'dashicons-book',
            'capability_type'   => 'post',
            'supports'          => ['title', 'editor', 'thumbnail', 'excerpt'],
            'taxonomies'        => array(),
            'has_archive'       => true,
            'can_export'        => true,
            'delete_with_user'  => false,
            'show_in_rest'      => false,
        ];
    }

    public function getFieldPostPlainText()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('평범한 문자열', 'axis3-sample'),
                    'shortLabel'  => __('평문', 'axis3-sample'),
                    'description' => __(
                        '가장 평범한 문자열입니다. 설명에는 간단한 <strong>HTML 태그</strong>를 삽입할 수도 있습니다. <a href="http://google.com" target="_blank">구글 링크</a>',
                        'axis3-sample'
                    ),
                    'default'     => '',
                    'valueType'   => new TextType(),
                ];
            }
        );
    }

    public function getFieldPostRequiredText()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'           => __('필수 문자열', 'axis-sample'),
                    'description'     => __('이 필드는 필수로 입력해야 합니다. 레이블에 붉은 별 마크가 보일 것입니다.', 'axis3-sample'),
                    'valueType'       => new TextType(),
                    'required'        => true,
                    'requiredMessage' => '이 문자열은 필수로 입력하셔야 합니다!',
                ];
            }
        );
    }

    public function getFieldPostEmail()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('다양한 형식 필드', 'axis3-sample'),
                    'description' => __(
                        '<span>sanitize_email()</span> 함수를 이용하여 올바른 이메일만 입력합니다. 물론 input 태그의 속성도 \'email\'로 맞춰 두었습니다. ' .
                        'Axis3에서는 데이터를 관리하는 모델과 데이터를 표시하는 필드 위젯을 분리하여 다양하고 유연한 표현이 가능합니다.',
                        'axis3-sample'
                    ),
                    'valueType'   => new TextType(['sanitizer' => 'sanitize_email']),
                ];
            }
        );
    }

    public function getFieldPostHasDefaultValue()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('기본값', 'axis3-sample'),
                    'description' => __(
                        '모델은 기본값을 가질 수 있습니다. 폼 입력 뿐 아니라, get_post_meta() 같은 함수에서도 적용되므로, 일관성 있는 기본값 체계를 유지할 수 있습니다.',
                        'axis3-sample'
                    ),
                    'default'     => __('기본값', 'axis3-sample'),
                    'valueType'   => new TextType(),
                ];
            }
        );
    }

    public function getFieldPostInteger01()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('정수값 예제 01', 'axis3-sample'),
                    'description' => __(
                        '이 필드는 정수값만을 가지게 됩니다. 물론 TextType을 이용해 숫자를 받는 것도 가능하지만 IntType을 사용하면 보다 입력값을 정수 타입에 한정합니다. ' .
                        '그러므로 보다 타입에 엄격한 프로그래밍이 가능해집니다.',
                        'axis3-sample'
                    ),
                    'valueType'   => new IntType(),
                ];
            }
        );
    }

    public function getFieldPostInteger02()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('정수값 예제 02', 'axis3-sample'),
                    'description' => __(
                        '정수값의 상한/하한을 설정할 수 있습니다. 범위 밖의 숫자를 저장하려고 하면 알림 영역에 경고를 띄우며 기본값으로 대체되어 저장되는 것을 볼 수 있습니다. 의도적으로 input 태그의 상한 속성을 없앴습니다. 10보다 큰 수를 입력하고 저장해 보세요.',
                        'axis3-sample'
                    ),
                    'valueType'   => new IntType(
                        [
                            'min' => 1,
                            'max' => 10,
                        ]
                    ),
                    'default'     => 5,
                ];
            }
        );
    }

    public function getFieldPostInteger03()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('정수값 예제 03', 'axis3-sample'),
                    'description' => __(
                        '이 필드는 보다 엄격하게 값을 제한합니다. 10보다 더 큰 수를 입력하면 어떤 일이 발생하는지 확인해 보세요.',
                        'axis3-sample'
                    ),
                    'valueType'   => new IntType(
                        [
                            'strict' => true,
                            'min'    => 1,
                            'max'    => 10,
                        ]
                    ),
                    'default'     => 5,
                ];
            }
        );
    }

    public function getFieldPostSelect()
    {
        return $this->claimMetaFieldModel(
            $this->guessKey(__METHOD__),
            function () {
                return [
                    'label'       => __('선택 예제', 'axis3-sample'),
                    'description' => __(
                        '이 필드는 텍스트 타입을 가집니다. 택스트 타입은 값 범위를 목록에서 선택하도록 제한을 줄 수 있습니다. 한편 필드 위젯은 이 목록을 이용하여 &lt;select&gt;를 만들어냅니다. 모델은 데이터를 제한하고, 위젯을 데이터를 표현하는 법에 집중합니다.',
                        'axis3-sample'
                    ),
                    'valueType'   => new TextType(
                        [
                            'choices' => [
                                'opt-a-0-1' => '상위 1번 옵션',
                                'opt-a-0-2' => '상위 2번 옵션',
                                '1차 하위 그룹'  => [
                                    'opt-a-1-1' => '1차 1번 옵션',
                                    'opt-a-1-2' => '1차 2번 옵션',
                                ],
                                '2차 하위 그룹'  => [
                                    'opt-a-2-1' => '2차 1번 옵션',
                                    'opt-a-2-2' => '2차 2번 옵션',
                                    'opt-a-2-3' => '2차 3번 옵션',
                                ],
                            ],
                        ]
                    ),
                ];
            }
        );
    }
}
