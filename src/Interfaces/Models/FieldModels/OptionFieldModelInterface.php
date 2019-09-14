<?php

namespace Shoplic\Axis3\Interfaces\Models\FieldModels;

/**
 * Interface OptionFieldModelInterface
 *
 * 옵션 필드 모델에 대한 인터페이스
 *
 * @package Shoplic\Axis3\Interfaces\Models
 * @version 1.0.0
 */
interface OptionFieldModelInterface extends FieldModelInterface
{
    /**
     * 문맥적 옵션 설정 여부 리턴.
     *
     * 같은 옵션 이름에 대한 값을 문맥에 따라 다르게 저장한다.
     * 가령 WPML 같은 다국어 상황에서 플러그인 옵션 저장시
     * 다국어별로 일관되게 옵션 값을 저장할 수 있는 방법을 제시할 수 있다.
     *
     * @return bool 문맥적 옵션을 지원하는지 리턴.
     */
    public function isContextual(): bool;

    /**
     * 자동 로드 여부 리턴.
     *
     * 의외로 옵션은 그다지 오토로드를 걸지 않아도 된다.
     * 특정 기능에 대한 옵션이라면, 그 특정 기능이 동작할 때만 유효하며 이외의 상황에는 필요가 없다.
     *
     * 이 값이 true 이면 (조금 더 오버헤드가 있지만) 워드프레스의 옵션 API 를 사용해도 autoload 필드를
     * 의도한 대로 yes, no 처리 가능하게 한다.
     *
     * @return bool
     */
    public function isAutoload(): bool;

    /**
     * 이 옵션이 속한 그룹 명.
     *
     * @return string
     * @see    register_setting()
     */
    public function getGroup(): string;

    /**
     * @param null|string $context 콘텍스트. contextual 이 아닌 경우는 null 로 넣어도 무방하다.
     *                             그러나 contextual 한 경우라면 반드시 영소문자, 하이픈, 언더바, 숫자만 사용해야 한다.
     *                             문자열이 아닌 입력은 공백 콘텍스트로 판단한다.
     *
     * @return mixed 옵션 값
     * @see    get_option()
     */
    public function retrieve($context = null);

    /**
     * @param mixed       $value   업데이트할 값.
     * @param null|string $context 콘텍스트. contextual 이 아닌 경우는 null 로 넣어도 무방하다.
     *                             그러나 contextual 한 경우라면 반드시 영소문자, 하이픈, 언더바, 숫자만 사용해야 한다.
     *                             문자열이 아닌 입력은 공백 콘텍스트로 판단한다.
     *
     * @return bool 업데이트되면 true, 아니면 false
     * @see    update_option()
     */
    public function save($value, $context = null);

    /**
     * 옵션 필드를 등록한다.
     *
     * @return void
     * @see    register_setting()
     * @see    get_registered_settings()
     */
    public function registerOptionField();
}
