<?php

namespace Shoplic\Axis3\Aspects;

use function Shoplic\Axis3\Functions\formatAttr;

/**
 * Class ScriptPropFilterAspect
 *
 * script 태그 속성을 추가할 수 있도록 한다.
 * wp_enqueue_script() 함수를 통해 추가하는 스크립트는 태그에 기타 속성 추가가 어렵다.
 *
 * 이 애즈팩트를 통해 쉽게 속성을 추가할 수 있다.
 *
 * @example
 * $aspect = $this->claimAspect(ScriptPropFilterAspect::class);
 * $aspect->addScriptProps( 'my-handle', ['async' => 'false'] );
 * $aspect->addScriptProps( 'my-handle', 'defer' );
 * $aspect->addScriptProps( 'my-handle', 'module' );
 *
 * @package Shoplic\Axis3\Aspects
 * @link    https://developer.mozilla.org/ko/docs/Web/HTML/Element/script
 */
class ScriptPropFilterAspect extends BaseAspect
{
    private $scriptHandles = [];

    public function setup($args = array())
    {
        add_filter('script_loader_tag', [$this, 'scriptLoaderTag'], 10, 2);
    }

    /**
     * 속성을 추가한다.
     *
     * @param string       $handle 핸들 이름.
     * @param string|array $props  속성. 'defer'처럼 문자열 형태로 입력하면 &lt;script defer&gt; 혹은 &lt;script nomodule=""&gt; 같이 나온다.
     *                             배열의 경우 formatAttr() 함수 참고.
     *
     * @see \Shoplic\Axis3\Functions\formatAttr()
     */
    public function addScriptProps($handle, $props)
    {
        if (is_string($props)) {
            $props = [sanitize_key($props) => ''];
        }

        $this->scriptHandles[$handle] = $props;
    }

    /**
     * 핸들에 지정한 속성 목록을 리턴.
     *
     * @param string $handle 핸들 이름.
     *
     * @return string|array|null 없으면 NULL, 있다면 입력한 대로 출력.
     */
    public function getScriptProps($handle)
    {
        return $this->scriptHandles[$handle] ?? null;
    }

    /**
     * 핸들에 지정한 속성을 제거.
     *
     * @param string $handle 핸들 이름.
     */
    public function removeScriptProps($handle)
    {
        unset($this->scriptHandles[$handle]);
    }

    /**
     * @param string $tag    <script> tag.
     * @param string $handle handle.
     *
     * @return string;
     */
    public function scriptLoaderTag($tag, $handle)
    {
        if (isset($this->scriptHandles[$handle])) {
            if (preg_match(';(<script.+)(></script>);', $tag, $matches)) {
                $attrs = formatAttr($this->scriptHandles[$handle]);
                if ($attrs) {
                    $tag = "{$matches[1]}{$attrs}{$matches[2]}";
                }
            }
        }

        return $tag;
    }
}
