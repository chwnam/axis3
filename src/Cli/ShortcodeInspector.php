<?php

namespace Shoplic\Axis3\Cli;

use Shoplic\Axis3\Initiators\AutoHookInitiator;
use Shoplic\Axis3\Views\ShortcodeView;

use function Shoplic\Axis3\Functions\datetimeI18n;

/**
 * Class ShortcodeInspector
 *
 * 숏코드 검출 클래스.
 *
 * 단, 모든 숏코드를 검전하지는 못하고 AutoHookInitiator 의 function shortcode_* 메소드나,
 * function v_shortcode_* 메소드의 경우 인지해 낸다.
 *
 * v_shortcode 를 처리하는 경우 명시적으로 처리 클래스가 있음을 나타낸다.
 * 이 클래스의 주석문은 적절히 처리되어 각 숏코드의 소개로 사용된다.
 *
 * @package Shoplic\Axis3\Cli
 */
class ShortcodeInspector extends PluginInspector
{
    public function inspect()
    {
        $shortcodes = [];
        $instances  = $this->getStarter()->getInitiatorInstances();
        foreach ($this->getStarter()->getInitiatorClasses() as $context => $classes) {
            foreach ($classes as $path => $class) {
                if (is_subclass_of($class, AutoHookInitiator::class)) {
                    /** @var AutoHookInitiator $instance */
                    if (isset($instances[$class])) {
                        $instance = $instances[$class];
                        $created  = false;
                    } else {
                        $instance = new $class();
                        $instance->setStarter($this->getStarter());
                        $created = true;
                    }
                    $params             = $instance->getCallbackParams();
                    $shortcodes[$class] = $params['add_shortcode'];
                    if ($created) {
                        unset($instance);
                    }
                }
            }
        }

        $output = [];
        foreach ($shortcodes as $class => $params) {
            foreach ($params as $param) {
                $tag       = $param['tag'];
                $callback  = $param['callback'];
                $directive = $param['directive'];
                $document  = '';

                if (is_array($callback) && 2 === count($callback) && is_object($callback[0])) {
                    $callback[0] = get_class($callback[0]);
                    if (is_subclass_of($callback[0], ShortcodeView::class)) {
                        try {
                            $reflection = new \ReflectionClass($callback[0]);
                            $document   = $this->cleanupClassDocComment($reflection->getDocComment(), $callback[0]);
                        } catch (\ReflectionException $e) {
                        }
                    }
                }

                $output[] = [
                    'initiator'   => $class,
                    'tag'         => $tag,
                    'callback'    => $callback,
                    'directive'   => $directive,
                    'description' => $document,
                ];
            }
        }

        return $output;
    }

    public function renderMarkdown(array $output, string $fileName)
    {
        $pluginData = get_plugin_data($this->getStarter()->getMainFile());

        $name    = $pluginData['Name'] ?? '';
        $version = $pluginData['Version'] ?? '';

        usort(
            $output,
            function ($a, $b) {
                return strcmp($a['tag'], $b['tag']);
            }
        );

        ob_start();

        echo ($name ? "# {$name}" : "# 플러그인 {$this->getStarter()->getSlug()}") . " 숏코드 정의 일람\n\n";
        echo "이 문서는 [Axis3](https://github.com/chwnma/axis3.git) inspection 기능에 의해 자동 생성된 문서입니다.\n\n";
        echo "* 플러그인 버전: $version\n";
        echo "* 문서 생성 일시: " . datetimeI18n('now') . "\n";
        echo "* 숏코드 inspection 기능은 Axis3의 AutoHookInitiator 클래스에 의해 선언된 것만을 인식합니다.\n";
        echo "  이 문서에서 안내된 목록 외 별도의 숏코드가 선언되었을 수도 있습니다.\n\n";

        foreach ($output as $item) {
            echo "## 숏코드 `{$item['tag']}` {#{$item['tag']}}\n";
            echo "{$item['description']}";
            echo "* 생성 위치: `{$item['initiator']}`\n";
            echo "* 콜백 위치: `{$item['callback'][0]}::{$item['callback'][1]}()`\n";
            echo "\n";
        }

        echo "\n\nE.O.D\n\n";

        file_put_contents($fileName, ob_get_clean());
    }
}
