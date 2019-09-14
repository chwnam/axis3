<?php

namespace Shoplic\Axis3\Interfaces\Models\FieldHolders;

use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;
use Shoplic\Axis3\Models\FieldModels\OptionFieldModel;

/**
 * Interface OptionFieldHolderInterface
 *
 * 옵션 필드를 가지는 모델 클래스를 위한 인터페이스
 *
 * @package Shoplic\Axis3\Interfaces\Models\FieldHolders
 * @since   1.0.0
 */
interface OptionFieldHolderInterface
{
    /**
     * 옵션 필드 모델을 하나 생성합니다. 한 번 생성된 옵션 모델 객체는 재활용됩니다.
     *
     * @param string        $key                  옵션 이름
     * @param callable|null $argCallback          인자 콜백. 콜백 인자로 현재 모델의 인스턴스가 주어집니다. 반드시 필드 모델을 초기화하는 배열을 리턴해야 합니다.
     *                                            이미 옵션 필드를 재차 불러올 경우는 키 만을 이용해 가져올 수 있으므로 생략합니다.
     * @param string|null   $optionFieldClassName null 인 경우 기본 구현인 OptionFieldModel 을 씁니다.
     *                                            그러나 만일 다른 OptionFieldInterface 구현체를 사용할 경우에는 여기에 그 클래스의 FQCN 를 입력하세요.
     *
     * @return OptionFieldModelInterface|null     옵션 필드 모델이 생성되지 않았는데 $argCallback 이 null 이거나, $argCallback 이 호출 가능한 오브젝트가 아니라면 null 을 리턴합니다.
     * @see    BaseFieldModel::__construct() $argCallback 의 리턴은 이 생성자의 두 번째 인자와 관련 있습니다.
     * @see    OptionFieldModel          기본 메타 필드의 구현입니다.
     */
    public function claimOptionField(string $key, $argCallback = null, $optionFieldClassName = null);

    /**
     * 클래스 내부에 구현된 모든 옵션 필드를 리턴합니다.
     *
     * @param string $methodPrefix 메타 필드를 리턴하는 메소드의 접두. 기본은 'getField'
     *
     * @return OptionFieldModelInterface[] 연관배열이며, 키는 옵션 이름입니다.
     */
    public function getAllOptionFields(string $methodPrefix = 'getField'): array;
}
