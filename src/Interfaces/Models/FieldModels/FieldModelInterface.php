<?php

namespace Shoplic\Axis3\Interfaces\Models\FieldModels;

use Shoplic\Axis3\INterfaces\Models\ModelInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;

/**
 * Interface FieldModelInterface
 *
 * 모델 인터페이스.
 *
 * @package Shoplic\Axis3\Interfaces\Models\FieldModels
 * @since   1.0.0
 */
interface FieldModelInterface extends ModelInterface
{
    /**
     * 필드의 이름을 리턴
     *
     * @return string
     */
    public function getKey(): string;

    /**
     * 필드의 타입을 리턴.
     *
     * @return string 'string', 'integer', 'number'.
     * @see    register_meta()
     */
    public function getType(): string;

    /**
     * 이 필드 모델의 타입을 리턴
     *
     * @return string 옵션 모델이면 'option', 메타 필드 모델이면 'meta', 스텁 필드 모델은 'stub'.
     */
    public function getFieldType(): string;

    /**
     * 레이블을 리턴.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * 짧은 레이블을 리턴.
     *
     * @return string
     */
    public function getShortLabel(): string;

    /**
     * 필드의 설명을 리턴
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * 기본값을 리턴
     *
     * @param string $context 기본값을 반환하는 문맥적 구분.
     *
     * @return mixed
     * @see    ValueTypeInterface::getDefault() 문맥 구분은 여기와 동일하다.
     */
    public function getDefault(string $context = ValueTypeInterface::DEFAULT_CONTEXT_DEFAULT);

    /**
     * 필수 필드인지 리턴
     *
     * @return bool
     */
    public function isRequired(): bool;

    /**
     * 필수 옵션임을 알리는 메시지를 출력
     *
     * 필수 메시지를 출력하는 방법은 각 필드 위젯의 구현마다 다르다.
     * 이 값이 콜백인 경우 이 인스턴스를 인자로 입력된다. 콜백은 반드시 문자열을 리턴해야 합니다.
     *
     * @return null|string
     */
    public function getRequiredMessage();

    /**
     * @return bool
     */
    public function isShowInRest(): bool;

    /**
     * 값 타입 객체를 리턴
     *
     * @return ValueTypeInterface
     */
    public function getValueType(): ValueTypeInterface;

    /**
     * 값 세정을 위한 콜백 함수를 리턴한다.
     *
     * 관련 필터:
     * - sanitize_option_{$option}
     * - sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}
     *
     * @return callable
     * @see    register_setting()
     * @see    sanitize_option()
     * @see    register_meta()
     */
    public function getSanitizeCallback(): callable;

    /**
     * 값의 세정을 처리한다.
     *
     * 외부로부터 받은 값이 안전한지 검사한다.
     * 안전한 타입인지 검사한다.
     *
     * @param mixed $value 입력값.
     *
     * @return mixed 세정된 출력값.
     * @see    ValueTypeInterface::sanitize()
     */
    public function sanitize($value);

    /**
     * 값의 논리적인 검증을 처리한다.
     *
     * sanitize() 메소드는 값이 서버에 저장해도 안전한지를 검사하는 반면,
     * 이 메소드는 그 값이 이 필드가 논리적으로 인정되는지를 판단하는 것이다.
     *
     * 예를 들어 정수 타입만을 허용하는 메타 필드에서 실수가 들어오면 정수로 캐스팅하거나,
     * 문자열을 허용하지 않는 것은 sanitize() 가 할 일로 본다.
     *
     * 한편 정수에서 0에서 100까지만을 허용하는 케이스도 있을 것이다. 이 경우에는 verify()가 담당한다.
     *
     * @param mixed $value 입력값.
     *
     * @return array 3개의 원소를 가진 순차 배열을 리턴한다.
     *               0: bool.   검증 결과.
     *               1: string. 검증 실패의 이유를 나타내는 메시지
     *               2: mixed.  검증을 거친 값.
     *
     *               - 입력된 값이 논리적으로 올바르면 [true, '', {검증된 값}]을 리턴한다.
     *               - 입력된 값이 논리적으로 바르지 않으면 [false, {이유}, {쓰레기값}]을 리턴한다.
     *                 {쓰레기값}에는 어떤 값이 있는지 정의되지 않았다.
     *               - 입력값에 논리적인 오류가 있으나, 메소드 내에서 교정이 가능하여 올바른 값으로 교정된 경우라면,
     *                 [true, {이유}, {교정된 값}]을 리턴한다.
     * @see    ValueTypeInterface::verify()
     */
    public function verify($value): array;

    /**
     * DB 에서 값을 가져올 때 이 메소드를 통과한다.
     * 데이터베이스의 값을 프로그램에서 처리할 수 있는 값으로 편집할 때 사용한다.
     *
     * @param mixed $value
     *
     * @return mixed
     * @see    ValueTypeInterface::import()
     */
    public function import($value);

    /**
     * 값을 DB 에 저장하기 전에 마지막으로 거친다.
     * 데이터베이스에 저장하기 적절한 값으로 편집할 때 사용한다.
     *
     * @param mixed $value
     *
     * @return mixed
     * @see    ValueTypeInterface::export()
     */
    public function export($value);

    /**
     * 기본 인자를 리턴합니다.
     *
     * @return array
     */
    public static function getDefaultArgs(): array;
}
