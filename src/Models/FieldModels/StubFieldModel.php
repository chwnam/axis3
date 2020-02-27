<?php

namespace Shoplic\Axis3\Models\FieldModels;

/**
 * Class StubFieldModel
 *
 * 스텁 필드 모델
 *
 * 옵션이나 메타 필드로 정의하기는 어렵지만 필드위젯을 통해 임의의 필드를 폼 요소로 출력하고
 * 폼 전송을 통해 서버로부터 클라이언트가 보낸 정보를 받을 때 사용한다.
 *
 * @package Shoplic\Axis3\Models\FieldModels
 * @since   1.0.0
 */
class StubFieldModel extends BaseFieldModel
{
    public function __construct($key, $args = [])
    {
        $args['_fieldType'] = 'stub';

        parent::__construct($key, $args);
    }

    public function retrieve()
    {
        return $this->args['default'] ?? null;
    }
}
