<?php

namespace Shoplic\Axis3\Interfaces\Models;

use Shoplic\Axis3\Interfaces\Models\FieldHolders\MetaFieldHolderInterface;
use WP_Role;

/**
 * Interface RolesCapsInterface
 *
 * @package Shoplic\Axis3\Interfaces\Models
 * @since   1.0.0
 */
interface RolesCapsInterface extends MetaFieldHolderInterface
{
    /**
     * 코어에 등록된 이 역할을 조회한다.
     *
     * @return  WP_Role|null 현재 역할인 WP_Role 객체를 리턴. 단, 이미 코어에 등록되어 있어야 한다.
     * @see     get_role()
     */
    public static function getRole();

    /**
     * 역할 이름. 프로그램 상에서 인지되는 역할 이름.
     * 예) editor, subscriber, administrator
     *
     * @return string
     */
    public static function getRoleName(): string;

    /**
     * UI 상에서 사람에게 더 인지되기 편한 역할 문자열
     * 예) 편집자, 구독자, 관리자
     *
     * @return string
     */
    public static function getDisplayName(): string;

    /**
     * 이 역할이 가지는 세부적인 역할을 나열한다.
     *
     * @return array 키는 권한 이름을 나타내는 문자열, 값은 불리언.
     */
    public function getCapabilities(): array;

    /**
     * 코어에 이 클래스에 정의된 역할을 더한다.
     *
     * @return WP_Role|null 역할이 더해지면 WP_Role 객체를, 이미 있다면 null 을 리턴한다.
     * @see    add_role()
     */
    public function addRole();

    /**
     * 코어에 이 클래스가 정의하는 역할을 제거한다.
     *
     * @return void
     * @see    remove_role()
     */
    public function removeRole();

    /**
     * 특정 사용자 그룹에게 일괄적으로 이 역할을 추가합니다.
     *
     * @param array $queryVar 쿼리로 사용되는 배열
     *
     * @return void
     * @see    \WP_User_Query::prepare_query() $queryVar 에게 들어갈 키-값을 참고.
     */
    public function assignTo(array $queryVar);

    /**
     * @param array $queryVar 쿼리로 사용되는 배열
     *
     * @return void
     * @see    \WP_User_Query::prepare_query() $queryVar 에게 들어갈 키-값을 참고.
     */
    public function revokeFrom(array $queryVar);

    /**
     * 플러그인 활성화시 1회 실행
     *
     * @return void
     */
    public function activationSetup();

    /**
     * 플러그인 비활성화시 1회 실행
     *
     * @return void
     */
    public function deactivationCleanup();
}