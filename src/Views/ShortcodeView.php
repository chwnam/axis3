<?php

namespace Shoplic\Axis3\Views;

/**
 * Class ShortcodeView
 *
 * 쇼트코드 처리를 담당하기 위한 위한 뷰 클래스
 *
 * @package Shoplic\Axis3\Views
 * @since   1.0.0
 */
abstract class ShortcodeView extends BaseView
{
    /** @var string 이 클래스가 처리하는 쇼트코드 이름. 쇼트코드를 처리하면서 자동으로 입력됩니다. */
    protected $shortcode = '';

    /** @var array 쇼트코드에 설정된 속성값. */
    protected $atts = [];

    /** @var string 쇼트코드 사이에서 동봉된 값. */
    protected $enclosed = '';

    /**
     * 이 메소드를 구현해야 합니다. 쇼트코드로 출력할 내용을 리턴해야 합니다.
     *
     * @return string
     */
    abstract protected function processContent(): string;

    /**
     * 쇼트코드 처리를 위한 콜백 메소드로 사용합니다.
     *
     * @param array  $atts      속성 키 - 값 배열. 쇼트코드에 있는 내용만 받는다.
     * @param string $enclosed  열림-닫힘 사이에 있는 동봉된 텍스트 예: [쇼트코드]동봉텍스트[/쇼트코드]
     * @param string $shortcode 쇼트코드 이름
     *
     * @return string
     */
    public function dispatch($atts, $enclosed, $shortcode)
    {
        if (!has_filter("shortcode_atts_{$shortcode}", [$this, 'filterShortcodeAtts'])) {
            add_filter("shortcode_atts_{$shortcode}", [$this, 'filterShortcodeAtts']);
        }

        $this->shortcode = $shortcode;
        $this->atts      = shortcode_atts($this->getDefaultAtts(), $atts, $this->shortcode);
        $this->enclosed  = $enclosed;

        return $this->processContent();
    }

    /**
     * 쇼트코드 동작 전 속성을 필터합니다.
     * 상속받는 클래스에서 오버라이드하여 적절히 속성값을 정리할 수 있습니다.
     *
     * @callback
     * @filter      shortcode_atts_{$shortcode}
     *
     * @param array $atts
     *
     * @return array
     */
    public function filterShortcodeAtts(array $atts): array
    {
        return $atts;
    }

    /**
     * 이 쇼트코드의 기본 속성값을 리턴. 상속받는 클래스에서 오버라이드하여 적절한 속성 값으로 세팅해야 합니다.
     *
     * @return array
     */
    public function getDefaultAtts(): array
    {
        return [];
    }
}
