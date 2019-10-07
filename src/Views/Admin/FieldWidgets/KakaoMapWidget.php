<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use InvalidArgumentException;
use Shoplic\Axis3\Interfaces\Models\FieldModels\MetaFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;

/**
 * Class KakaoMapWidget
 *
 * 카카오 맵 지도를 보여주고 주소를 기록합니다.
 * 주소는 텍스트 형태로 저장되지만, 파라미터를 통해 위도와 경도 좌표를 줄 수도 있습니다.
 * 매번 정확하게 지도에 기억된 장소를 찍거나 좌표를 사용한 계산을 해야 할 때는 좌표 필드가 있어야 합니다.
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 */
class KakaoMapWidget extends BaseFieldWidget
{
    public function __construct($fieldModel, $args = [])
    {
        parent::__construct($fieldModel, $args);

        if ($this->args['coordinate'] && (!$this->args['lat'] || !$this->args['lng'])) {
            throw new InvalidArgumentException(
                __('When \'coordinate\' is true, \'lat\' and \'lng\' must be specified.', 'axis3')
            );
        }

        // 위젯이 블록 요소라 굳이 강제개행할 필요가 없다. 스페이서 제거한다.
        $this->args['brDesc']   = false;
        $this->args['noSpacer'] = true;
    }

    public function outputWidgetCore()
    {
        $containerId = $this->getId() . '-map-container';
        $mapOpts     = $this->args['mapOpts'];
        $fieldType   = $this->getFieldModel()->getFieldType();
        $objectId    = $this->getObjectId();
        $keys        = [
            'lat' => '',
            'lng' => '',
        ];
        $mapAttrs    = [
            'id'    => $containerId,
            'style' => 'width: 80%; height: 450px;',
        ];
        $wrapAttrs   = [
            'id'    => $this->getId() . '-map-wrap',
            'class' => 'axis3-field-widget axis3-kakao-map-widget',
        ];

        foreach (['lat', 'lng'] as $c) {
            $v = $mapOpts['center'][$c] ?? null;
            if (is_null($v)) {
                /** @var MetaFieldModelInterface|OptionFieldModelInterface|null $m */
                $m = &$this->args[$c];
                if ($m) {
                    $keys[$c] = $m->getKey();
                    switch ($fieldType) {
                        case 'meta':
                            $v = $m->retrieve($objectId);
                            break;
                        case 'option':
                            $v = $m->retrieve();
                            break;
                        default:
                            $v = $m->getDefault();
                            break;
                    }
                    $mapOpts['center'][$c] = $v;
                }
            }
        }

        $this->render(
            'admin/field-widgets/kakao-map.php',
            [
                'addr_id'    => $this->getId(),
                'addr_name'  => $this->getName(),
                'addr_value' => $this->getValue(),
                'lat_name'   => $keys['lat'],
                'lng_name'   => $keys['lng'],
                'map_attrs'  => wp_parse_args($this->args['mapAttrs'], $mapAttrs),
                'map_opts'   => &$mapOpts,
                'wrap_attrs' => wp_parse_args($this->args['wrapAttrs'], $wrapAttrs),
            ]
        );

        wp_add_inline_script(
            'axis3-kakao-map',
            "jQuery(function (\$) { $('#{$containerId}').axis3KakaoMap(); });"
        );
    }

    public function onceAfterRender()
    {
        if (!wp_script_is('axis3-kakao-map')) {
            wp_enqueue_script(
                'axis3-kakao-map-api',
                add_query_arg(
                    [
                        'appkey'    => $this->args['apiKey'],
                        'libraries' => 'services',
                    ],
                    'https://dapi.kakao.com/v2/maps/sdk.js'
                ),
                [],
                null,
                true
            );
            wp_enqueue_script('axis3-kakao-map');
            wp_localize_script(
                'axis3-kakao-map',
                'axis3KakaoMap',
                ['textOverwrite' => $this->args['textOverwrite']]
            );
        }

        $this->render('admin/field-widgets/ejs/kakao-map.ejs');
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * bool: 위도 경도 좌표를 기록할지 결정.
                 */
                'coordinate'    => true,

                /**
                 * FieldModelInterface: 위도 필드.
                 */
                'lat'           => null,

                /**
                 * FieldModelInterface: 경도 필드.
                 */
                'lng'           => null,

                /**
                 * string: 구글맵을 사용하기 위한 API 키.
                 */
                'apiKey'        => '',

                /**
                 * array: 구글맵에 제공되는 기본 옵션.
                 */
                'mapOpts'       => [
                    // null - 필드값으로부터 받음
                    'center' => [
                        'lat' => null,
                        'lng' => null,
                    ],
                    'level'  => 3,
                ],

                /**
                 * array: 맵 태그에 붙는 속성.
                 */
                'mapAttrs'      => [],

                /**
                 * array: 위젯 최외곽에 붙는 속성.
                 */
                'wrapAttrs'     => [],

                /**
                 * string: 주소가 덮어 씌워질 때 경고 문구.
                 */
                'textOverwrite' => __('The selected address is about to be overwritten! Are you sure?', 'axis3'),
            ]
        );
    }
}
