<?php

namespace Shoplic\Axis3\Views;

use Parsedown;
use Shoplic\Axis3\Interfaces\Views\ViewInterface;
use Shoplic\Axis3\Objects\AxisObject;

use function Shoplic\Axis3\Functions\closeTag;
use function Shoplic\Axis3\Functions\openTag;
use function Shoplic\Axis3\Functions\toPascalCase;

class BaseView extends AxisObject implements ViewInterface
{
    /** @var array templates cache array */
    protected static $templates = [];

    /** @var array assets cache array */
    protected static $assets = [];

    public function setup($args = array())
    {
    }

    public function render(string $template, array $context = [], bool $return = false, bool $internal = false)
    {
        if (isset(static::$templates[$template])) {
            $templatePath = static::$templates[$template];
        } else {
            $templatePath = false;

            if ($internal) {
                $templatePath = plugin_dir_path(AXIS3_MAIN) . 'src/Templates/' . $template;
            } else {
                $main  = $this->getStarter()->getMainFile();
                $slug  = $this->getStarter()->getSlug();
                $paths = [
                    get_stylesheet_directory() . "/{$slug}/{$template}",
                    get_template_directory() . "/{$slug}/{$template}",
                    dirname($main) . "/src/Templates/{$template}",
                    dirname(AXIS3_MAIN) . "/src/Templates/{$template}",
                ];

                foreach ($paths as $path) {
                    if (file_exists($path) && is_readable($path)) {
                        $templatePath = $path;
                        break;
                    }
                }
            }

            if ($templatePath) {
                static::$templates[$template] = $templatePath;
            }
        }

        if ($templatePath) {
            $context['_starter'] = $this->getStarter();
            $context['_view']    = $this;

            return static::renderFile($templatePath, $context, $return);
        }

        return null;
    }

    public function getAssetUrl(string $assetType, string $relPath, bool $internal = false): string
    {
        $key = "{$assetType}-{$relPath}";
        $url = false;

        if (isset(static::$assets[$key])) {
            $url = static::$assets[$key];
        } else {
            $assetType = trim($assetType, '/\\');
            $relPath   = trim($relPath, '/\\');

            if ($internal) {
                $url = plugins_url("/src/assets/{$assetType}/{$relPath}", AXIS3_MAIN);
            } else {
                $main  = $this->getStarter()->getMainFile();
                $slug  = $this->getStarter()->getSlug();
                $paths = [
                    'stylesheet' => get_stylesheet_directory() . "/{$slug}/assets/{$assetType}/{$relPath}",
                    'template'   => get_template_directory() . "/{$slug}/assets/{$assetType}/{$relPath}",
                    'plugin'     => dirname($main) . "/src/assets/{$assetType}/{$relPath}",
                    'axis3'      => dirname(AXIS3_MAIN) . "/src/assets/{$assetType}/{$relPath}",
                ];

                foreach ($paths as $key => $path) {
                    if (file_exists($path) && is_readable($path)) {
                        switch ($key) {
                            case 'stylesheet':
                                $url = get_stylesheet_directory_uri() . "/{$slug}/assets/{$assetType}/{$relPath}";
                                break 2;
                            case 'template':
                                $url = get_template_directory_uri() . "/{$slug}/assets/{$assetType}/{$relPath}";
                                break 2;
                            case 'plugin':
                                $url = plugins_url("src/assets/{$assetType}/{$relPath}", $main);
                                break 2;
                            case 'axis3':
                                $url = plugins_url("src/assets/{$assetType}/{$relPath}", AXIS3_MAIN);
                                break 2;
                        }
                    }
                }
            }

            if ($url) {
                static::$assets[$key] = $url;
            }
        }

        return $url;
    }

    public function getCssUrl(string $relPath, bool $internal = false): string
    {
        return $this->getAssetUrl('css', $relPath, $internal);
    }

    public function getImgUrl(string $relPath, bool $internal = false): string
    {
        return $this->getAssetUrl('img', $relPath, $internal);
    }

    public function getJsUrl(string $relPath, bool $internal = false): string
    {
        return $this->getAssetUrl('js', $relPath, $internal);
    }

    /**
     * 콘텍스트를 뷰 메소드에서 분리.
     *
     * 콘텍스트의 각 키워드를 각자 메소드로 분리한다.
     *
     * 사용 예:
     *
     * ...
     * $this->populateContext(
     *   ['foo', 'bar', ['baz', 'one', 'two']],
     *   ['x', 'y']
     * );
     * ...
     *
     * 아래처럼 메소드를 선언합니다.
     *
     * protected function getParamFoo($x, $y) { ... }          // foo 대응. $x, $y 에 각각 'x', 'y' 대입
     * protected function getParamBar($x, $y) { ... }          // bar 대응. $x, $y 에 각각 'x', 'y' 대입
     * protected function getParamBaz($x, $y, $z, $w) { ... }  // baz 대응.
     *                                                         // $x, $y, $z, $w 에 각각 'x', 'y', 'one', 'two' 대입.
     *
     * @param array $keywords     키워드 목록. 각 원소는 문자열이거나, 배열.
     *                            문자열이면 각 콘텍스트의 변수입니다.
     *                            배열이면 첫번째 원소는 콘텍스트, 두번째부터는 메소드에 전달할 인자.
     *                            공통 파라미터 뒤로 전달할 인자가 이어집니다.
     * @param array $commonParams 공통 파라미터
     *
     * @return array
     */
    public function populateContext(array $keywords = [], array $commonParams = [])
    {
        $context = [];

        /** @var string|string[] $keyword */
        foreach ($keywords as $keyword) {
            $method = null;
            $params = [];
            $key    = null;

            if (is_string($keyword) && !empty($keyword)) {
                $key    = $keyword;
                $method = [$this, 'getContext' . toPascalCase($key)];
                $params = &$commonParams;
            } elseif (is_array($keyword) && !empty($keyword)) {
                $key    = array_shift($keyword);
                $method = [$this, 'getContext' . toPascalCase($key)];
                $params = array_merge($commonParams, $keyword);
            }

            if ($method && $key && is_callable($method)) {
                $context[$key] = call_user_func_array($method, $params);
            }
        }

        return $context;
    }

    public function enqueueScript(
        string $handle,
        string $relPath = '',
        array $deps = [],
        $version = false,
        bool $inFooter = false,
        string $objName = '',
        array $l10n = [],
        string $inline = '',
        string $inlinePosition = 'after',
        bool $finishEnqueue = true,
        bool $internal = false
    ) {
        if (!wp_script_is($handle, 'registered')) {
            $registeredHere = true;
            wp_register_script(
                $handle,
                $this->getJsUrl($relPath, $internal),
                $deps,
                $version,
                $inFooter
            );
        } else {
            $registeredHere = false;
        }

        if (!wp_script_is($handle, 'enqueued')) {
            if ($finishEnqueue) {
                if ($registeredHere) {
                    wp_enqueue_script($handle);
                } else {
                    wp_enqueue_script($handle, $this->getJsUrl($relPath, $internal), $deps, $version, $inFooter);
                }
            }
            if ($objName && !empty($l10n)) {
                wp_localize_script($handle, $objName, $l10n);
            }
            if (!empty($inline)) {
                wp_add_inline_script($handle, $inline, $inlinePosition);
            }
        }

        return $this;
    }

    public function enqueueStyle(
        string $handle,
        string $relPath = '',
        array $deps = [],
        $version = false,
        string $media = 'all',
        string $inline = '',
        bool $finishEnqueue = true,
        bool $internal = false
    ) {
        if (!wp_style_is($handle, 'registered')) {
            wp_register_style(
                $handle,
                $this->getCssUrl($relPath, $internal),
                $deps,
                $media
            );
            $registeredHere = true;
        } else {
            $registeredHere = false;
        }

        if (!wp_style_is($handle, 'enqueued')) {
            if ($finishEnqueue) {
                if ($registeredHere) {
                    wp_enqueue_style($handle, $this->getCssUrl($relPath, $internal), $deps, $version, $media);
                } else {
                    wp_enqueue_style($handle);
                }
            }
            if (!empty($inline)) {
                wp_add_inline_style($handle, $inline);
            }
        }

        return $this;
    }

    public static function renderFile(string $templateFile, array $context = [], bool $return = false)
    {
        if ($return) {
            ob_start();
        }

        extract($context, EXTR_SKIP);

        /** @noinspection PhpIncludeInspection */
        include $templateFile;

        return $return ? ob_get_clean() : null;
    }

    public static function renderMarkdown(string $file, string $id, callable $contentFilter = null)
    {
        if (!class_exists('\\Parsedown')) {
            echo '<h1>' . esc_html__('Parsedown Not Found', 'axis3') . '</h1>';
            echo '<p>' . __('<a href="https://parsedown.org/">Parsedown</a> is not installed.', 'axis3') . '</p>';
            _e(
                '<p>Install it by running <code>composer install</code> command in the <strong>Axis3 root path</strong>.</p>',
                'axis3'
            );
            return;
        }

        if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
            echo '<h1>' . esc_html__('Markdown File Not Found', 'axis3') . '</h1>';
            printf(__('<p>Markdown file \'%s\' is not found, or an invalid file.</p>', 'axis3'), $file);
            return;
        }

        openTag('div', ['id' => $id, 'class' => 'markdown-body']);
        {
            $pd      = new Parsedown();
            $content = file_get_contents($file);

            if ($contentFilter) {
                $content = call_user_func($contentFilter, $content, $file);
            }

            echo $pd->parse($content);
        }
        closeTag('div');


        if (!wp_script_is('axis3-prism')) {
            wp_enqueue_script('axis3-prism');
        }

        if (!wp_style_is('axis3-prism')) {
            wp_enqueue_style('axis3-prism');
        }

        if (!wp_style_is('axis3-github-markdown')) {
            wp_enqueue_style('axis3-github-markdown');
        }

        if (is_admin() && !wp_style_is('axis3-admin-github-markdown')) {
            wp_enqueue_style('axis3-admin-github-markdown');
        }
    }
}
