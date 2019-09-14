<?php

namespace Shoplic\Axis3\Models;

use Shoplic\Axis3\Interfaces\Models\RolesCapsInterface;
use Shoplic\Axis3\Models\FieldHolders\MetaFieldHolderModel;
use WP_User;
use WP_User_Query;

/**
 * Class RolesCapsModel
 *
 * @package Shoplic\Axis3\Models
 * @since   1.0.0
 */
abstract class RolesCapsModel extends MetaFieldHolderModel implements RolesCapsInterface
{
    /**
     * 역할 내에 정의된 권한의 목록.
     *
     * - 상속하는 클래스에서 이 프로퍼티를 직접 초기화해서 쓸 수 있습니다. 키는 역할 이름, 값은 불리언 true 입니다.
     * - 'getCap'으로 시작하는 메소드를 구현하세요. 초기화시 직접 호출하여 목록을 업데이트합니다.
     *
     * @var array
     */
    protected $capabilities = [];

    /**
     * $capabilities 배열이 초기화되었는지 기록
     *
     * @var bool
     */
    private $capabilityInitialized = false;

    /**
     * 이 값이 참이면 플러그인 활성화시 이 클래스에 정의된 역할과 권한은 관리자 역할들에게 자동으로 부여햡니다.
     * 단 개시자에서 자동 모델 등록 기능을 사용하여 이 클래스를 인식시켜야 합니다.
     *
     * [주의하세요]
     * 플러그인 개발 중 역할, 권한 이름을 변경해야할 수 있습니다. 이 때 이미 역할 권한이 사용자들에게 부여되어 있을
     * 수 있습니다. 이 때는 플러그인을 먼저 비활성하여 사용자들에게서 역할과 권한을 회수한 것을 확인한 후,
     * 이름을 변경합니다. 그리고 다시 플러그인을 활성화하세요.
     *
     * @var bool
     */
    protected $assignToAdmin = true;

    public static function getRole()
    {
        return get_role(static::getRoleName());
    }

    /**
     * getCap* 매직 메소드를 이용해 동적으로 역할 이름을 불러올 수 있습니다.
     *
     * @return array
     */
    public function getCapabilities(): array
    {
        if ($this->capabilityInitialized) {
            foreach (get_class_methods($this) as $method) {
                if (strlen($method) > 6 && substr($method, 0, 6) === 'getCap' && $method !== 'getCapabilities') {
                    $returned = $this->{$method}();
                    if (is_string($returned) && !empty($returned)) {
                        $this->capabilities[$returned] = true;
                    }
                }
            }
            ksort($this->capabilities);
            $this->capabilityInitialized = true;
        }

        return $this->capabilities;
    }

    public function addRole()
    {
        if (!static::getRole()) {
            return add_role(static::getRoleName(), static::getDisplayName(), $this->getCapabilities());
        }

        return null;
    }

    public function removeRole()
    {
        remove_role(static::getRoleName());
    }

    public function assignTo(array $queryVar)
    {
        $query = new WP_User_Query($queryVar);
        /** @var WP_User $user */
        foreach ($query->get_results() as $user) {
            $user->add_role(static::getRoleName());
        }
    }

    public function revokeFrom(array $queryVar)
    {
        $query = new WP_User_Query($queryVar);
        /** @var WP_User $user */
        foreach ($query->get_results() as $user) {
            $user->remove_role(static::getRoleName());
        }
    }

    public function activationSetup()
    {
        $this->addRole();
        if ($this->assignToAdmin) {
            $this->assignTo(['role' => 'administrator']);
        }
    }

    public function deactivationCleanup()
    {
        if ($this->assignToAdmin) {
            $this->revokeFrom(['role' => 'administrator']);
        }
        $this->removeRole();
    }
}
