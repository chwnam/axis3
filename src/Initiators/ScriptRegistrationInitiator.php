<?php

namespace Shoplic\Axis3\Initiators;

use Shoplic\Axis3\Aspects\ScriptPropFilterAspect;
use Shoplic\Axis3\Objects\ScriptObject;
use Shoplic\Axis3\Objects\StyleObject;

/**
 * Class ScriptRegistrationInitiator
 *
 * 스크립트 등록을 도외주는 전수자.
 *
 * @package Shoplic\Axis3\Initiators
 */
class ScriptRegistrationInitiator extends BaseInitiator
{
    public function initHooks()
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'callbackEnqueueScripts'],
            $this->getWpEnqueueScriptsPriority()
        );

        add_action(
            'admin_enqueue_scripts',
            [$this, 'callbackAdminEnqueueScript'],
            $this->getAdminEnqueueScriptsPriority()
        );
    }

    /**
     * 'wp_enqueue_scripts' 액션 동작시 우선순위.
     *
     * @return int
     */
    public function getWpEnqueueScriptsPriority()
    {
        return 10;
    }

    /**
     * 'admin_enqueue_scripts' 액션 동작시 우선순위.
     *
     * @return int
     */
    public function getAdminEnqueueScriptsPriority()
    {
        return 10;
    }

    public function callbackEnqueueScripts()
    {
        $uriBase = $this->getUriBase();
        $min     = $this->getMin();
        $ver     = $this->getVer();

        $this->registerScripts($this->getCommonJsList($uriBase, $min, $ver));
        $this->registerScripts($this->getFrontJsList($uriBase, $min, $ver));
        $this->registerStyles($this->getCommonCssList($uriBase, $min, $ver));
        $this->registerStyles($this->getFrontCssList($uriBase, $min, $ver));
    }

    public function callbackAdminEnqueueScript()
    {
        $uriBase = $this->getUriBase();
        $min     = $this->getMin();
        $ver     = $this->getVer();

        $this->registerScripts($this->getCommonJsList($uriBase, $min, $ver));
        $this->registerScripts($this->getAdminJsList($uriBase, $min, $ver));
        $this->registerStyles($this->getCommonCssList($uriBase, $min, $ver));
        $this->registerStyles($this->getAdminCssList($uriBase, $min, $ver));
    }

    /**
     * 공통 스크립트 등록시 목록 리턴. 오버라이드하여 원하는 동작으로 만들 수 있다.
     *
     * @param string           $uri getUriBase() 의 리턴. 보통 루트 디렉토리의 URL.
     * @param string           $min getMin() 의 리턴. SCRIPT_DEBUG 상수가 참이면 '.min'. 아니면 공백.
     * @param string|bool|null $ver getVer() 의 리턴. 보통 플러그인의 버전.
     *
     * @return ScriptObject[]
     */
    public function getCommonJsList($uri, $min, $ver)
    {
        return [];
    }

    /**
     * 프론트 스크립트 등록시 목록 리턴. 오버라이드하여 원하는 동작으로 만들 수 있다.
     *
     * @param string           $uri getUriBase() 의 리턴. 보통 루트 디렉토리의 URL.
     * @param string           $min getMin() 의 리턴. SCRIPT_DEBUG 상수가 참이면 '.min'. 아니면 공백.
     * @param string|bool|null $ver getVer() 의 리턴. 보통 플러그인의 버전.
     *
     * @return ScriptObject[]
     */
    public function getFrontJsList($uri, $min, $ver)
    {
        return [];
    }

    /**
     * 관리자 스크립트 등록시 목록 리턴. 오버라이드하여 원하는 동작으로 만들 수 있다.
     *
     * @param string           $uri getUriBase() 의 리턴. 보통 루트 디렉토리의 URL.
     * @param string           $min getMin() 의 리턴. SCRIPT_DEBUG 상수가 참이면 '.min'. 아니면 공백.
     * @param string|bool|null $ver getVer() 의 리턴. 보통 플러그인의 버전.
     *
     * @return ScriptObject[]
     */
    public function getAdminJsList($uri, $min, $ver)
    {
        return [];
    }

    /**
     * 공통 스타일 등록시 목록 리턴. 오버라이드하여 원하는 동작으로 만들 수 있다.
     *
     * @param string           $uri getUriBase() 의 리턴. 보통 루트 디렉토리의 URL.
     * @param string           $min getMin() 의 리턴. SCRIPT_DEBUG 상수가 참이면 '.min'. 아니면 공백.
     * @param string|bool|null $ver getVer() 의 리턴. 보통 플러그인의 버전.
     *
     * @return StyleObject[]
     */
    public function getCommonCssList($uri, $min, $ver)
    {
        return [];
    }


    /**
     * 프론트 스타일 등록시 목록 리턴. 오버라이드하여 원하는 동작으로 만들 수 있다.
     *
     * @param string           $uri getUriBase() 의 리턴. 보통 루트 디렉토리의 URL.
     * @param string           $min getMin() 의 리턴. SCRIPT_DEBUG 상수가 참이면 '.min'. 아니면 공백.
     * @param string|bool|null $ver getVer() 의 리턴. 보통 플러그인의 버전.
     *
     * @return StyleObject[]
     */
    public function getFrontCssList($uri, $min, $ver)
    {
        return [];
    }

    /**
     * 관리자 스타일 등록시 목록 리턴. 오버라이드하여 원하는 동작으로 만들 수 있다.
     *
     * @param string           $uri getUriBase() 의 리턴. 보통 루트 디렉토리의 URL.
     * @param string           $min getMin() 의 리턴. SCRIPT_DEBUG 상수가 참이면 '.min'. 아니면 공백.
     * @param string|bool|null $ver getVer() 의 리턴. 보통 플러그인의 버전.
     *
     * @return StyleObject[]
     */
    public function getAdminCssList($uri, $min, $ver)
    {
        return [];
    }

    /**
     * 기본적으로 플러그인의 루트 디렉토리의 URL 주소를 리턴한다.
     *
     * @return string
     */
    protected function getUriBase()
    {
        return plugin_dir_url($this->getStarter()->getMainFile());
    }

    /**
     * SCRIPT_DEBUG 상수가 true 이면 '.min' 문자열을, 아니면 빈 문자열을 리턴한다.
     *
     * @return string
     */
    protected function getMin()
    {
        return (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
    }

    /**
     * 기본적으로 플러그인의 버전을 리턴한다.
     *
     * @return string
     */
    protected function getVer()
    {
        return $this->getStarter()->getVersion();
    }

    /**
     * 스크립트 등록 절차를 실행.
     *
     * @param ScriptObject[] $scripts
     */
    protected function registerScripts($scripts)
    {
        $aspect = $this->claimAspect(ScriptPropFilterAspect::class);

        foreach ($scripts as $script) {
            if ($script instanceof ScriptObject && $script->handle && $script->src) {
                $result = wp_register_script(
                    $script->handle,
                    $script->src,
                    $script->deps,
                    $script->ver,
                    $script->inFooter
                );
                if ($aspect && $script->props && $result) {
                    $aspect->addScriptProps($script->handle, $script->props);
                }
            }
        }
    }

    /**
     * 스타일 등록 절차를 시행.
     *
     * @param StyleObject[] $styles
     */
    protected function registerStyles($styles)
    {
        foreach ($styles as $style) {
            if ($style instanceof StyleObject && $style->handle && $style->src) {
                wp_register_style($style->handle, $style->src, $style->deps, $style->ver, $style->media);
            }
        }
    }
}
