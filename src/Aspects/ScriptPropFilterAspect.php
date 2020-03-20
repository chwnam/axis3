<?php

namespace Shoplic\Axis3\Aspects;

class ScriptPropFilterAspect extends BaseAspect
{
    const TYPE_ASYNC = 'async';
    const TYPE_DEFER = 'defer';

    private $scriptHandles = [];

    public function setup($args = array())
    {
        add_filter('script_loader_tag', [$this, 'scriptLoaderTag'], 10, 2);
    }

    public function enableScriptProp($handle, $type = self::TYPE_DEFER)
    {
        $this->scriptHandles[$handle] = $type;
    }

    public function disableScriptProp($handle)
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
                if ($this->scriptHandles[$handle] === self::TYPE_DEFER) {
                    $tag = $matches[1] . ' defer="defer"' . $matches[2];
                } elseif ($this->scriptHandles[$handle] === self::TYPE_ASYNC) {
                    $tag = $matches[1] . ' defer="async"' . $matches[2];
                }
            }
        }

        return $tag;
    }
}
