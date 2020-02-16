<?php

namespace Shoplic\Axis3\Objects;

/**
 * Class ScriptObject
 *
 * 스크립트 등록시 사용하는 간단한 객체.
 *
 * @package Shoplic\Axis3\Objects
 *
 * @see     wp_register_script()
 */
class ScriptObject
{
    /**
     * 핸들 이름
     *
     * 필수로 입력. 이것이 공백이면 스크립트가 등록되지 않습니다.
     *
     * @var string
     */
    public $handle = '';

    /**
     * 소스 위치.
     *
     * 필수로 입력해야 합니다. 이것이 공백이면 스크립트가 등록되지 않습니다.
     *
     * @var string
     */
    public $src = '';

    /**
     * 의존하는 스크립트의 핸들 목록
     *
     * @var string[]
     */
    public $deps = [];

    /**
     * 스크립트의 버전.
     *
     * 문자열이면 그 문자열을 버전 문자열로 사용. 캐시 폭파용으로 적절하게 사용할 수 있다.
     * false: 워드프레스 버전 사용.
     * null:  버전을 붙이지 않음.
     *
     * @var string|bool|null
     */
    public $ver = false;

    /**
     * 푸터에 출력되기를 선호하는지?
     *
     * @var bool
     */
    public $inFooter = false;

    public function __construct($handle, $src, $deps = [], $ver = false, $inFooter = false)
    {
        $this->handle   = $handle;
        $this->src      = $src;
        $this->deps     = $deps;
        $this->ver      = $ver;
        $this->inFooter = $inFooter;
    }
}
