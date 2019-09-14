<?php

namespace Shoplic\Axis3\Models\FieldHolders;

use Shoplic\Axis3\Interfaces\Models\FieldHolders\MetaFieldHolderInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\MetaFieldModelInterface;
use Shoplic\Axis3\Models\BaseModel;
use Shoplic\Axis3\Models\FieldModels\MetaFieldModel;
use Shoplic\Axis3\Traits\Models\FieldHolders\FieldHolderModelTrait;

/**
 * Class MetaFieldHolderModel
 *
 * 메타 필드를 가지는 모델 필드의 구현.
 *
 * 메타 필드 모델을 정의, 사용하려면 다음처럼 하세요.
 * 1. 이 클래스를 상속하여 모델 클래스를 생성합니다.
 * 2. 클래스 내부에 'getField'로 시작하는 메소드를 생성합니다.
 *    나머지 이름은 메타 키의 파스칼 표기볍을 준수하는 것을 권장합니다.
 * 3. getField- 메소드는 claimMetaFieldModel() 메소드를 호출하여 메타 필드 모델을 생성하세요.
 *
 * @package Shoplic\Axis3\Models\FieldHolders
 * @since   1.0.0
 */
abstract class MetaFieldHolderModel extends BaseModel implements MetaFieldHolderInterface
{
    use FieldHolderModelTrait;

    /** @var MetaFieldModelInterface[] 연관 배열. 키는 해당 메타 키 */
    private $metaFields = [];

    public function claimMetaFieldModel(
        string $key,
        $argCallback = null,
        $metaFieldClassName = null
    ): MetaFieldModelInterface {
        if (empty($key)) {
            return null;
        } elseif (isset($this->metaFields[$key])) {
            return $this->metaFields[$key];
        } else {
            if (is_callable($argCallback)) {
                if (!$metaFieldClassName) {
                    $metaFieldClassName = MetaFieldModel::class;
                }

                /** @var MetaFieldModelInterface $instance */
                $instance = new $metaFieldClassName($key, $this->getMetaFieldArgs($argCallback));
                $instance->setStarter($this->getStarter());
                $instance->registerMetaField();

                $this->metaFields[$key] = $instance;

                return $this->metaFields[$key];
            } else {
                return null;
            }
        }
    }

    /**
     * 클래스 내부에 구현된 모든 메타 필드를 리턴합니다.
     *
     * @param string $methodPrefix 메타 필드를 리턴하는 메소드의 접두. 기본은 'getField'
     *
     * @return MetaFieldModelInterface[] 연관배열이며 키는 메타 키입니다.
     */
    public function getAllMetaFields(string $methodPrefix = 'getField'): array
    {
        static $exclude = [
            'activationSetup',
            'deactivationCleanup',
            'claimAspect',
            'claimMetaFieldModel',
            'claimModel',
            'claimView',
            'getAllMetFields',
            'getMetaFieldArgs',
            'getPostType',
            'getPostTypeArgs',
            'getStarter',
            'getTaxonomy',
            'getWpdb',
            'guessKey',
            'registerPostType',
            'registerMetaFields',
            'registerSettings',
            'registerTaxonomy',
            'saveFromRequest',
            'setStarter',
            'setup',
        ];

        $prefixLen = strlen($methodPrefix);

        foreach (array_diff(get_class_methods($this), $exclude) as $methodName) {
            if (strlen($methodName) > $prefixLen && substr($methodName, 0, $prefixLen) === $methodPrefix) {
                call_user_func([$this, $methodName]);
            }
        }

        return $this->metaFields;
    }

    protected function getMetaFieldArgs(callable $argCallback)
    {
        return call_user_func($argCallback, $this);
    }
}
