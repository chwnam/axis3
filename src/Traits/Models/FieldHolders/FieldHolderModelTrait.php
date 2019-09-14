<?php

namespace Shoplic\Axis3\Traits\Models\FieldHolders;

use Shoplic\Axis3\Interfaces\Starters\StarterInterface;
use Shoplic\Axis3\Models\FieldHolders\MetaFieldHolderModel;
use Shoplic\Axis3\Models\FieldHolders\OptionFieldHolderModel;
use function Shoplic\Axis3\Functions\toSnakeCase;

/**
 * Trait FieldHolderModelTrait
 *
 * @package Shoplic\Axis3\Traits\Models\FieldHolders
 * @since   1.0.0
 *
 * @method StarterInterface getStarter()
 *
 * @used-by MetaFieldHolderModel
 * @used-by OptionFieldHolderModel
 */
trait FieldHolderModelTrait
{
    /**
     * 키 이름을 메소드로부터 추측함.
     * 키 이름을 불필요하게 중복하지 않도록 메소드 이름으로부터 추측해낸다.
     *
     * - 메소드 이름의 접두 (보통 getField) 제거.
     * - 나머지 문자열을 스네이크 표기법으로 변경.
     * - 플러그인 접두어를 붙여서 리턴.
     *
     * @param string $methodName
     * @param string $prefix
     *
     * @return string 추축해낸 키 이름. 추측하지 못하면 입력한 문자열을 리턴.
     */
    protected function guessKey(string $methodName, string $prefix = 'getField'): string
    {
        $name = trim($methodName, '\\/');

        $doubleColonPos = strrpos($name, '::');
        if (false !== $doubleColonPos) {
            $name = substr($name, $doubleColonPos + 2);
        }

        $backslashPos = strrpos($name, '\\');
        if (false !== $backslashPos) {
            $name = substr($name, $backslashPos + 1);
        }

        if (strlen($name) > 8 && substr($name, 0, 8) === $prefix) {
            return $this->getStarter()->prefixed(toSnakeCase(substr($name, 8)));
        } else {
            return $methodName;
        }
    }
}
