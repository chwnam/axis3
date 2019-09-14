<?php

namespace Shoplic\Axis3\Interfaces\Views\Admin\FieldWidgets;

use Shoplic\Axis3\Interfaces\Models\FieldModels\FieldModelInterface;
use Shoplic\Axis3\Interfaces\Views\ViewInterface;

/**
 * Interface FieldWidgetInterface
 *
 * 관리자 영역에서 필드를 출력하기 위한 인터페이스.
 *
 * 폼 테이블 (table.form-table)을 대상으로 하여 폼 테이블의 tr, th, td 를 효과적으로 출력하기 위한 인터페이스를 가진다.
 * 그러나 위젯 자체를 출력하는 것은 꼭 폼 테이블에 국한되지 않고 여러 곳에 응용될 수 있다.
 *
 * @package Shoplic\Axis3\Interfaces\Views\Admin
 * @since   1.0.0
 */
interface FieldWidgetInterface extends ViewInterface
{
    /**
     * 필드 모델을 반환.
     *
     * @return FieldModelInterface
     */
    public function getFieldModel(): FieldModelInterface;

    /**
     * 위젯에 필드 모델을 지정한다.
     *
     * @param FieldModelInterface $fieldModel 필드 모델.
     *
     * @return void
     */
    public function setFieldModel(FieldModelInterface $fieldModel);

    /**
     * 위젯 자체만들 출력한다.
     *
     * @return null|string
     */
    public function renderWidget();

    /**
     * 위젯의 설명란을 출력한다.
     *
     * @return void
     */
    public function renderDescription();

    /**
     * 위젯 출력 바로 전에 호출되는 콜백 메소드.
     * 주의: 이 메소드는 필터 없이 그대로 출력한다.
     *       구현하는 측에서 악의적인 코드가 삽입되지 않도록 조심해야 한다.
     *
     * @return void
     */
    public function beforeRenderWidget();

    /**
     * 위젯 출력 바로 후에 호출되는 콜벡 메소드.
     * 주의: 이 메소드는 필터 없이 그대로 출력한다.
     *       구현하는 측에서 악의적인 코드가 삽입되지 않도록 조심해야 한다.
     *
     * @return void
     */
    public function afterRenderWidget();

    /**
     * 폼 테이블의 <tr> 태그 부분을 출력한다.
     *
     * @return void
     */
    public function renderFormTableTr();

    /**
     * 폼 테이블의 <th> 태그 부분을 출력한다.
     *
     * @return void
     */
    public function renderFormTableTh();

    /**
     * 폼 테이블의 <td> 태그 부분을 출력한다.
     *
     * @return void
     */
    public function renderFormTableTd();

    /**
     * 콘텍스트를 주었다면 콘텍스트를 렌더한다.
     *
     * @return void
     */
    public function renderContext();

    /**
     * 위젯의 HTML id 속성을 리턴.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * 위젯의 HTML name 속성을 리턴.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * 위젯이 표시해야 할 값을 리턴.
     *
     * @return mixed
     */
    public function getValue();

    /**
     * 폼 테이블에서 <th> 태그 안에 필요한 타이틀을 출력한다.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * 폼 테이블의 <th> &gtl; <label> 테그의 텍스트를 가져온다.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * 폼 테이블의 <th> 태그 안의 <label> 태그 for 속성을 위한 값을 리턴한다.
     *
     * @return string
     */
    public function getLabelFor(): string;

    /**
     * 위젯의 설명을 리턴한다.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * 툴팁과 관련된 HTML 마크업 부분을 리턴한다.
     *
     * @return string
     */
    public function getTooltip(): string;

    /**
     * 이 필드가 필수인지 리턴한다.
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * 이 필드가 필수일 때 필수입력임을 알리는 메시지를 리턴한다.
     *
     * @return string|callable
     */
    public function getRequiredMessage();

    /**
     * 이 위젯이 renderWidget() 메소드를 호출하면서 beforeRenderWidget() 이전 단 한 번만 실행한다.
     * 이 타입의 인스턴스가 여러 벌 생성되어도 이 메소드는 단 한 번만 불린다.
     * 위젯이 필요로 하는 스크립트를 큐잉할 때 유용하다.
     *
     * @return void
     */
    public function onceBeforeRender();

    /**
     * 이 위젯이 renderWidget() 메소드를 호출하면서 afterRenderWidget() 이후 단 한 번만 실행한다.
     * 이 타입의 인스턴스가 여러 벌 생성되어도 이 메소드는 단 한 번만 불린다.
     * 위젯이 필요로 하는 스크립트를 큐잉할 때 유용하다.
     *
     * @return void
     */
    public function onceAfterRender();

    /**
     * 위젯의 기본 인자 목록을 리턴한다.
     *
     * @return array
     */
    public static function getDefaultArgs(): array;
}
