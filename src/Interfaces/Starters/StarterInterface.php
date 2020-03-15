<?php

namespace Shoplic\Axis3\Interfaces\Starters;

use Exception;
use Shoplic\Axis3\Interfaces\Initiators\InitiatorInterface;
use Shoplic\Axis3\Interfaces\Objects\AxisObjectInterface;
use Shoplic\Axis3\Interfaces\Starters\ClassFinders\ClassFinderInterface;

/**
 * Interface StarterInterface
 *
 * 개시자 인터페이스
 *
 * 플러그인의 시작을 균일하게 하기 위해 Starter (개시자) 콤포넌트를 도입합니다.
 * 개시자 콤포넌트를 사용하게 되면,
 *
 * - 플러그인이 제각각의 구현을 가지지 않고 Axis 의 일관된 구조를 가지게 됩니다.
 * - Axis 3 기반 위의 강력한, 그리고 재활용성이 높은 OOP 기반의 프로그램을 작성할 수 있습니다.
 *
 * @package Shoplic\Axis3\Interfaces\Starters
 * @since   1.0.0
 */
interface StarterInterface
{
    /**
     * 플러그인을 동작시키는 메소드입니다.
     *
     * @return void
     * @throws Exception
     */
    public function start();

    /**
     * 클래스 검색자를 추가한다.
     * @param ClassFinderInterface $classFinder
     *
     * @return self
     * @throws Exception 클래스 검색자의 콤포넌트 접미사가 설정되지 않았으면 예외를 던진다.
     */
    public function addClassFinder(ClassFinderInterface $classFinder);

    /**
     * 클래스 검색자 목록을 리턴.
     */
    public function getClassFinders();

    /**
     * 각 오브젝트의 초기 인자를 별도로 설정한다.
     *
     * @param string $fqcn 오브젝트의 FQCN
     * @param array  $args 인자
     *
     * @return self
     */
    public function addObjectSetupArgs(string $fqcn, array $args);

    /**
     * starter 에 의해 인스턴스화된 전수자 목록을 반환합니다.
     * 주의: 이 메소드는 외부 플러그인에서 본 플러그인에 피치 못할 커스터마이즈를 진행해야만 하는 경우에 사용을 합니다.
     *       그러므로 플러그인 개발시에는 사용하지 마십시오.
     *
     * @return InitiatorInterface[]
     */
    public function getInitiatorInstances();

    /**
     * 전수자 집합을 불러옵니다. 각 구현에 따라 전수자 목록을 가져오는 방법이 달라질 수 있습니다.
     *
     * @return array 키는 콘텍스트, 값은 전수자 목록입니다.
     */
    public function getInitiatorClasses();

    /**
     * 모델 집합을 가져옵니다. 각 구현에 따라 모델 클래스를 가져오는 방법이 달라질 수 있습니다.
     *
     * @return array 키는 콘텍스트, 값은 모델 클래스 목록입니다.
     */
    public function getModelClasses();

    /**
     * @return string 유일한 이름(슬러그)를 반환.
     */
    public function getSlug(): string;

    /**
     * 이 개시자에 사용될 유일한 이름을 지정합니다.
     *
     * @param string $slug 이 개시자에 붙일 유일한 이름. 소문자, 숫자, 언더바, 하이픈만 허용합니다.
     *
     * @return self
     */
    public function setSlug(string $slug);

    /**
     * 이 개시자가 있는 플러그인의 메인 파일을 리턴.
     *
     * @return string
     */
    public function getMainFile(): string;

    /**
     * 이 개시자가 있는 플러그인의 메인 파일을 지정. URL, 파일 경로를 찾아낼 때 필수적인 정보입니다.
     *
     * @param string $mainFile
     *
     * @return self
     */
    public function setMainFile(string $mainFile);

    /**
     * 플러그인의 텍스트도메인을 리턴.
     *
     * @return string
     */
    public function getTextdomain(): string;

    /**
     * 플러그인의 텍스트도메인을 기록. 번역 텍스트가 존재하는 경우 지정해 두면 편리함.
     *
     * @param string $textdomain
     *
     * @return self
     */
    public function setTextdomain(string $textdomain);

    /**
     * 플러그인의 버전을 리턴
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * 플러그인의 버전을 기록.
     *
     * @param string $version 플러그인의 버전. version_compare() 에서 인식할 수 있는 형태로 지정할 것을 권장합니다.
     *
     * @return self
     */
    public function setVersion(string $version);

    /**
     * 플러그인이 사용하는 접두사를 가져옵니다.
     *
     * @param bool $preferDash 거짓(기본)이면 접두사 뒤에 언더바(_)를 붙이지만, 참이면 대시(-)를 붙인 접두사를 리턴합니다.
     *
     * @return string 접두사. 끝에 언더바나 대시가 붙습니다.
     */
    public function getPrefix(bool $preferDash = false): string;

    /**
     * 플러그인이 사용하는 접두사를 지정합니다.
     *
     * 커스텀 필드, 옵션 등에서 메타 키를 위해 주로 사용됩니다.
     * 즉, 여러 메타 키나 배열의 키 같은 문자열 뭉치에서 어떤 것이 이 플러그인으로부터 생성된 것인지
     * 보다 알기 쉽게 하는 역할을 합니다.
     *
     * @param string $prefix 접두사. 뒤에 붙은 언더바나 대시 기호는 자동으로 제거됩니다.
     *                       문자열은 반드시 소문자, 숫자, 언더바나 대시 기호여야만 합니다.
     *
     * @return self
     */
    public function setPrefix(string $prefix);

    /**
     * 문자열에 접두사를 붙여 줍니다.
     *
     * @param string $string     접두사를 붙일 문자열
     * @param bool   $preferDash 언더바(거짓), 혹은 대시(참)를 붙일 것인지 선택
     *
     * @return string 접두사가 붙은 문자열
     * @see    StarterInterface::getPrefix()
     */
    public function prefixed(string $string, bool $preferDash = false): string;

    /**
     * 모델 등록을 자동으로 수행할지를 결정합니다. 기본값은 true 로서, 명시적으로
     * setModelRegistrationEnabled()를 불러 수동으로 전환하지 않는다면
     * 모든 모델들은 자동으로 등록됩니다.
     *
     * @return bool
     */
    public function isModelRegistrationEnabled(): bool;

    /**
     * 모델 등록을 자동으로 할지, 수동으로 할지 지정합니다.
     *
     * @param bool $enabled
     *
     * @return self
     */
    public function setModelRegistrationEnabled(bool $enabled);

    /**
     * 블로그 ID 반환
     *
     * @return null|int|int[]|callable
     */
    public function getBlogId();

    /**
     * 블로그 ID 설정
     *
     * @param null|int|int[]|callable $blogId  정수, 정수의 배열, 함수를 넣을 수 있다.
     *                                         워드프레스가 멀티사이트 환경인 경우 이 개시자의 동작 기준이 된다.
     *                                         - null:     모든 멀티사이트에서 동작한다.
     *                                         - int:      특정 블로그 ID 와 일치하는 경우 동작한다.
     *                                         - int[]:    블로그 ID 가 제시된 배열에 포함된 경우 동작한다.
     *                                         - callable: 콜백 함수에 의해 판단한다. 개시자 객체가 콜백 함수의
     *                                                     인자로 전달된다.
     *
     * @return self
     */
    public function setBlogId($blogId);

    /**
     * 요청 컨텍스트를 추가한다.
     *
     * @param string   $context
     * @param callable $function
     * @param bool     $prepend
     *
     * @return self
     */
    public static function addRequestContext(string $context, callable $function, bool $prepend = false);

    /**
     * 현재 요청 콘텍스트를 리턴한다.
     *
     * @return string
     */
    public function getCurrentRequestContext();

    /**
     * 현재 요청 콘텍스트가 입력한 것과 일치하는지 판단한다.
     *
     * @param string $context
     *
     * @return bool
     */
    public function isRequestContext($context);

    /**
     * 이 개시자에서 사용할 오브젝트를 가져옵니다.
     * 없다면 새로 생성할 것이고, 가능한 생성된 객체는 재활용하기 위해 작성되었습니다.
     *
     * @param string        $type      오브젝트(콤포넌트)의 타입을 지정하기 위해 사용합니다.
     * @param string|object $fqcn      해당 타입에 속한 클래스의 이름. Fully Qualified Class Name 입니다.
     * @param array         $setupArgs 오브젝트가 AxisObjectInterface 인 경우 setup() 에서 불립니다.
     * @param bool          $reuse
     *
     * @return AxisObjectInterface|object|null
     * @see    AxisObjectInterface::claimAspect()
     * @see    AxisObjectInterface::claimModel()
     * @see    AxisObjectInterface::claimView()
     */
    public function claimObject(string $type, $fqcn, array $setupArgs = [], bool $reuse = false);
}
