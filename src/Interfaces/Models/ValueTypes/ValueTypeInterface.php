<?php

namespace Shoplic\Axis3\Interfaces\Models\ValueTypes;

use Shoplic\Axis3\Interfaces\Models\FieldModels\FieldModelInterface;

interface ValueTypeInterface
{
    const DEFAULT_CONTEXT_DEFAULT = 'default';
    const DEFAULT_CONTEXT_VERIFY  = 'verify';

    /**
     * 이 필드의 타입. string, boolean, integer, array, ...
     */
    public function getType(): string;

    /**
     * 값 세정 메소드
     *
     * 이 타입으로서, 안전한 값을 만들어야 한다. 최대한 안전하며, 올바른 타입으로 변경 처리해야 한다.
     *
     * 정확한 타입 교정 및 검증은 verify() 메소드에서 진행된다.
     *
     * @param mixed $value
     *
     * @return mixed 세정된 값
     */
    public function sanitize($value);

    /**
     * 값 검증 메소드
     *
     * @param mixed $value
     *
     * @return array 길이 2인 배열을 리턴해야 한다.
     *               - bool    검증 결과
     *               - mixed   검증 결과
     *               첫번째 인자가 true 이면 두번째는 검증을 통과한 값.
     *               첫번째 인자가 false 이면 두번째는 에러 메시지 문자열.
     *
     * @see FieldModelInterface::verify()
     */
    public function verify($value): array;

    /**
     * 이 값 타입으로 지정한 설정 배열을 리턴한다.
     *
     * @return array
     */
    public function getArgs();

    /**
     * 값 타입으로 배열된 설정 배열의 키를 찾고, 찾으면 값을 리턴한다.
     * 찾지 못하면 null 을 리턴한다.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function getArg(string $key);

    /**
     * 객체에서 데이터베이스로 값을 내보낼 때 한 번 거친다.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function export($value);

    /**
     * 데이터베이스에서 값을 가져올 때 값을 한 번 거친다.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function import($value);

    /**
     * 이 값 타입의 검증을 엄격하게 할지 결정합니다.
     * verify() 메소드에서 값 검증 실패시 판단 기준이 됩니다.
     *
     * @return bool
     */
    public function isStrict(): bool;

    /**
     * 이 값 타입의 기본값을 리턴한다.
     *
     * @param string $context 기본값 요청이 어떤 문맥에서 인지 구분한다.
     *                        문맥은 'default' 파라미터가 콜백 함수인 경우 두번째 인자로 입력된다.
     *                        값 타입에서 verify() 함수 구현시 반드시 주의해서 구현해야 한다.
     *                        axis3 내에서 기본적으로 사용하는 문맥은 다음에 나열되어 있다. 커스텀 러그인에서
     *                        임의 문자열을 대입해 기본값을 청구하는 여러 문맥적 상황을 구분해야 하는 경우 사용하라.
     *                        - default: 기본적인 경우.
     *                        - verify:  값 검증인 경우.
     *
     * @return mixed
     */
    public function getDefault(string $context = self::DEFAULT_CONTEXT_DEFAULT);

    public static function getDefaultArgs(): array;
}
