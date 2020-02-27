<?php

namespace Shoplic\Axis3\Views\Admin;

use Shoplic\Axis3\Views\BaseView;
use WP_Post;

/**
 * Class MetaBoxView
 *
 * 메타박스를 지원하는 뷰 클래스.
 *
 * @package Shoplic\Axis3\Views\Admin
 * @since   1.0.0
 */
abstract class MetaBoxView extends BaseView
{
    /**
     * 이 메타박스의 아이디. 메타박스 출력시 HTML ID 속성으로도 활용된다.
     *
     * @return string
     */
    abstract public function getId(): string;

    /**
     * 메타박스의 제목
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * 기본 화면 콜백입니다.
     *
     * @param WP_Post $post 콜백으로 전달되는 포스트
     *
     * @return void
     */
    abstract public function dispatch($post);

    /**
     * nonce 액션값을 리턴해야 한다. 출력시 폼에 반드시 삽입하라.
     *
     * @param WP_Post $post 포스트
     *
     * @return string
     */
    public function getNonceAction($post = null): string
    {
        return sanitize_key($this->getId());
    }

    /**
     * nonce 파라미터 문자열을 리턴해야 한다. 출력시 폼에 반드시 삽입하라.
     *
     * @param WP_Post $post 포스트
     *
     * @return string
     */
    public function getNonceParam($post = null): string
    {
        return 'nonce-' . sanitize_key($this->getId());
    }

    /**
     * 기본 메뉴 콜백을 리턴합니다.
     *
     * @return callable
     */
    public function getCallback()
    {
        return [$this, 'dispatch'];
    }

    /**
     * 스크린을 지정한다.
     *
     * @return \WP_Screen|null
     */
    public function getScreen()
    {
        return null;
    }

    /**
     * 스크린 콘텍스트.
     *
     * 보통 편집 화면에서는 'normal', 'side', 'advanced' 가 존재한다. 셋 중 하나를 리턴하면 된다.
     *
     * @return string
     */
    public function getContext()
    {
        return 'advanced';
    }

    /**
     * 출력 우선순위.
     *
     * 'high', 'default', 'low' 중 하나를 리턴하면 된다.
     *
     * @return string
     */
    public function getPriority()
    {
        return 'default';
    }

    /**
     * 콜백 함수에 전달되는 인자로 사용된다.
     *
     * @return array|null
     */
    public function getCallbackArgs()
    {
        return null;
    }

    /**
     * 메뉴를 추가합니다.
     *
     * @return void
     */
    public function addMetaBox()
    {
        add_meta_box(
            $this->getId(),
            $this->getTitle(),
            $this->getCallback(),
            $this->getScreen(),
            $this->getContext(),
            $this->getPriority(),
            $this->getCallbackArgs()
        );
    }
}
