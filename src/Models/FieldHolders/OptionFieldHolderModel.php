<?php

namespace Shoplic\Axis3\Models\FieldHolders;

use Shoplic\Axis3\Interfaces\Models\FieldHolders\OptionFieldHolderInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;
use Shoplic\Axis3\Models\BaseModel;
use Shoplic\Axis3\Models\FieldModels\OptionFieldModel;
use Shoplic\Axis3\Traits\Models\FieldHolders\FieldHolderModelTrait;

/**
 * Class OptionFieldHolderModel
 *
 * 옵션 필드를 가지는 모델 필드의 구현.
 *
 * 옵션 필드를 정의하고 사용하려면 다음처럼 하세요.
 * 1. 이 클래스를 상속하여 모델 클래스를 생성합니다.
 * 2. 클래스 내부에 'getField'로 시작하는 메소드를 생성합니다.
 *    나머지 이름은 메타 키의 파스칼 표기볍을 준수하는 것을 권장합니다.
 * 3. getField- 메소드는 claimOptionFieldModel() 메소드를 호출하여 옵션 필드 모델을 생성하세요.
 *
 * @package Shoplic\Axis3\Models\FieldHolders
 */
abstract class OptionFieldHolderModel extends BaseModel implements OptionFieldHolderInterface
{
    use FieldHolderModelTrait;

    /**
     * @var OptionFieldModelInterface[] 연관 배열. 키는 해당 메타 키
     */
    private $optionFields = [];

    public function claimOptionField(
        string $key,
        $argCallback = null,
        $optionFieldClassName = null
    ): OptionFieldModelInterface {
        if (isset($this->optionFields[$key])) {
            return $this->optionFields[$key];
        } else {
            if (is_callable($argCallback)) {
                if (!$optionFieldClassName) {
                    $optionFieldClassName = OptionFieldModel::class;
                }

                /** @var OptionFieldModelInterface $instance */
                $instance = new $optionFieldClassName($key, $this->getOptionFieldArgs($argCallback));
                $instance->setStarter($this->getStarter());
                $instance->registerOptionField();

                $this->optionFields[$key] = $instance;

                return $this->optionFields[$key];
            } else {
                return null;
            }
        }
    }

    /**
     * 클래스 내부에 구현된 모든 옵션 필드를 리턴합니다.
     *
     * @param string $methodPrefix 메타 필드를 리턴하는 메소드의 접두. 기본은 'getField'
     *
     * @return OptionFieldModelInterface[] 연관배열이며, 키는 옵션 이름입니다.
     */
    public function getAllOptionFields(string $methodPrefix = 'getField'): array
    {
        static $exclude = [
            'activationSetup',
            'claimAspect',
            'claimModel',
            'claimOptionField',
            'claimView',
            'correctAutoloadField',
            'deactivationCleanup',
            'getAllOptionFields',
            'getOptionFieldArgs',
            'getOptionGroup',
            'getStarter',
            'getWpdb',
            'guessKey',
            'preUpdateContextualOption',
            'registerSettings',
            'setOptionGroup',
            'setStarter',
            'setup',
        ];

        $prefixLen = strlen($methodPrefix);

        foreach (array_diff(get_class_methods($this), $exclude) as $methodName) {
            if (strlen($methodName) > $prefixLen && substr($methodName, 0, $prefixLen) === $methodPrefix) {
                call_user_func([$this, $methodName]);
            }
        }

        return $this->optionFields;
    }

    protected function getOptionFieldArgs(callable $args)
    {
        return call_user_func($args, $this);
    }
}
