<?php

namespace Shoplic\Axis3\Interfaces\Views;

use Shoplic\Axis3\Interfaces\Objects\AxisObjectInterface;

/**
 * Interface ViewInterface
 *
 * 뷰(View)는 MTV 패턴의 View 로, MVC 패턴의 Controller 에 더 가까운 콤포넌트입니다.
 * 주로 워드프레스의 액션/필터의 콜백으로 등록하여 클라이언트에 필요한 정보를 전달합니다.
 * HTML 문서를 전달해야 할 경우 뷰는 Template 을 이용하여 PHP/HTML 코드가 섞이지 않고 최대한 분리하여 처리합니다.
 *
 * @package Shoplic\Axis3\Interfaces\Views
 * @since   1.0.0
 */
interface ViewInterface extends AxisObjectInterface
{
    public function render(string $template, array $context = [], bool $return = false, bool $internal = false);

    public function getAssetUrl(string $assetType, string $relPath, bool $internal = false): string;

    public function getCssUrl(string $relPath, bool $internal = false): string;

    public function getImgUrl(string $relPath, bool $internal = false): string;

    public function getJsUrl(string $relPath, bool $internal = false): string;

    /**
     * 스크립트를 삽입
     *
     * @param string $handle
     * @param string $relPath
     * @param array  $deps
     * @param bool   $version
     * @param bool   $inFooter
     * @param string $objName
     * @param array  $l10n
     * @param string $inline
     * @param string $inlinePosition
     * @param bool   $finishEnqueue
     * @param bool   $internal
     *
     * @return self
     */
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
    );

    public function enqueueStyle(
        string $handle,
        string $relPath = '',
        array $deps = [],
        $version = false,
        string $media = 'all',
        string $inline = '',
        bool $finishEnqueue = true,
        bool $internal = false
    );

    /**
     * 콘텍스트를 뷰 메소드에서 분리.
     *
     * 콘텍스트의 각 키워드를 각자 메소드로 분리한다.
     *
     * @param array $keywords     키워드 목록. 각 원소는 문자열이거나, 배열.
     *                            문자열이면 각 콘텍스트의 변수입니다.
     *                            배열이면 첫번째 원소는 콘텍스트, 두번째부터는 메소드에 전달할 인자.
     *                            공통 파라미터 뒤로 전달할 인자가 이어집니다.
     * @param array $commonParams 공통 파라미터
     *
     * @return array
     */
    public function populateContext(array $keywords = [], array $commonParams = []);

    /**
     * 템플릿 파일을 렌더한다.
     *
     * 외부에서도 사용할 수 있도록 공개 정적 메소드로 되어 있다.
     *
     * @param string $templateFile 온전한 템플릿 경로.
     * @param array  $context      콘텍스트.
     * @param bool   $return       리턴 여부. 기본은 거짓, 즉 바로 출력.
     *
     * @return string|null
     */
    public static function renderFile(string $templateFile, array $context = [], bool $return = false);
}
