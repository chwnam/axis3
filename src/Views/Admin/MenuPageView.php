<?php

namespace Shoplic\Axis3\Views\Admin;

use Shoplic\Axis3\Views\BaseView;

/**
 * Class MenuPageView
 *
 * 메뉴 페이지를 지원하는 뷰 클래스
 *
 * @package Shoplic\Axis3\Views\Admin
 * @since   1.0.0
 */
abstract class MenuPageView extends BaseView
{
    /** @var string */
    protected $hook = '';

    /**
     * @var bool
     * @see alterAdminPageHook()
     */
    protected $fixAdminHook = false;

    abstract public static function getPageTitle(): string;

    abstract public static function getMenuTitle(): string;

    abstract public static function getCapability(): string;

    abstract public static function getMenuSlug(): string;

    abstract public function dispatch();

    public function setup($args = array())
    {
        $this->fixAdminHook = boolval($args['fixAdminHook'] ?? false);
    }

    public function getCallback()
    {
        return [$this, 'dispatch'];
    }

    public function getIconUrl(): string
    {
        return '';
    }

    public function getPosition()
    {
        return null;
    }

    public function getHook(): string
    {
        return $this->hook;
    }

    public function addMenuPage()
    {
        if ($this->fixAdminHook) {
            add_filter('sanitize_title', [$this, 'alterAdminPageHook'], 10, 3);
        }

        $this->hook = add_menu_page(
            static::getPageTitle(),
            static::getMenuTitle(),
            static::getCapability(),
            static::getMenuSlug(),
            $this->getCallback(),
            $this->getIconUrl(),
            $this->getPosition()
        );

        if ($this->fixAdminHook) {
            remove_filter('sanitize_title', [$this, 'alterAdminPageHook']);
        }
    }

    /**
     * 페이지 훅 이름을 교정
     *
     * add_menu_page() 내부에 있는 sanitize_title() 함수에서
     * 메뉴 이름이 URLEncode 처리되어 $admin_page_hooks 라는 글로별 변수에 기록된다.
     *
     * 코어의 메뉴 이름들은 다 영소문자를 써서 관계 없을지 모르지만,
     * 이렇게 인코딩 처리된 이름은 리스트 테이블의 열 숨김 AJAX 요청인 hidden-columns 액션 콜백 함수 내부에서
     * sanitize_key() 함수 통과를 할 수가 없어 동작이 멈추는 버그가 발생한다.
     * 어차피 큰 버그가 없다면 AJAX 콜백이 제대로 동작하도록 이름을 교정하면 된다.
     *
     * @callback
     * @filter      sanitize_title
     *
     * @param string $title    페이지 훅 이름
     * @param string $rawTitle 원래 그대로의 메뉴 이름.
     * @param string $context  보통 'save'로 입력됨.
     *
     * @return string
     *
     * @see         add_menu_page()
     * @see         sanitize_title()
     * @see         wp_ajax_hidden_columns()
     * @see         $admin_page_hooks
     */
    public function alterAdminPageHook($title, $rawTitle, $context)
    {
        if ($rawTitle === static::getMenuTitle() && 'save' === $context) {
            $title = static::getMenuSlug();
        }
        return $title;
    }
}
