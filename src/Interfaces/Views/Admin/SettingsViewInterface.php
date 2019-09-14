<?php

namespace Shoplic\Axis3\Interfaces\Views\Admin;

use Shoplic\Axis3\Interfaces\Views\Admin\FieldWidgets\FieldWidgetInterface;

/**
 * Interface SettingsViewInterface
 *
 * 셋팅 페이지를 처리하는 뷰의 인터페이스
 *
 * @package Shoplic\Axis3\Interfaces\Views\Admin
 * @since   1.0.0
 */
interface SettingsViewInterface
{
    /**
     * 세팅 폼 HTML 템플릿을 리턴.
     *
     * @return string
     */
    public function getTemplate(): string;

    /**
     * 세팅 폼 HTML 템플릿을 설정.
     *
     * 설정하지 않으면 기본적으로 작성되어 있는 HTML 폼을 사용한다.
     *
     * @param string $template 템플릿 이름.
     *
     * @return self
     */
    public function setTemplate(string $template);

    /**
     * 세팅 페이지 슬러그를 리턴.
     *
     * @return string
     */
    public function getPage(): string;

    /**
     * 세팅 페이지를 설정.
     *
     * 올바른 폼 필드를 출력하기 위해서는 제대로 페이지 슬러그를 입력해야 한다.
     *
     * @param string $page 페이지 슬러그.
     *
     * @return self
     */
    public function setPage(string $page);

    /**
     * 옵션 그룹을 리턴
     *
     * @return string
     */
    public function getOptionGroup(): string;

    /**
     * 옵션 그룹을 설정
     *
     * @param string $optionGroup
     *
     * @return self
     */
    public function setOptionGroup(string $optionGroup);

    /**
     * 섹션을 하나 추가한다.
     *
     * @param string        $slug     섹션의 식별자.
     * @param string        $title    섹션의 레이블.
     * @param null|callable $callback 콜백이나 NULL. 콜백은 문자열을 리턴한다. 레이블 아래 추가적인 마크업을 출력한다.
     *
     * @return self
     * @see    add_settings_section()
     */
    public function addSection(string $slug, string $title, $callback = null);

    /**
     * 섹션 아래에 필드를 하나 추가한다.
     *
     * @param string               $section     필드가 속한 섹션.
     * @param FieldWidgetInterface $fieldWidget 필드 위젯.
     * @param array                $args        add_settings_field() 로 전달되는 인자.
     * @param null|string          $key         보통 키는 $fieldWidget 에서 가져오지만, 다른 이름을 주고 싶다면 설정한다.
     * @param null|callable        $callback    이 필드를 출력하는 콜백.
     *
     * @return self
     * @see    add_settings_field()
     */
    public function addField(string $section, $fieldWidget, $args = [], $key = null, $callback = null);

    /**
     * 옵션 화면을 출력한다.
     *
     * @return void
     */
    public function renderSettings();
}
