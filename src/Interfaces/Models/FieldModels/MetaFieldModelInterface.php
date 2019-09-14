<?php

namespace Shoplic\Axis3\Interfaces\Models\FieldModels;

/**
 * Interface MetaFieldModelInterface
 *
 * 메타 필드 모델에 대한 인터페이스.
 *
 * 커스텀 포스트의 메타 필드는 CustomPostModel 클래스를 정의하는 것을 추천합니다.
 * 택소노미의 메타 필드는 TaxonomyModel 클래스에서 정의하는 것을 추천합니다
 *
 * @package Shoplic\Axis3\Interfaces\Models\FieldModels
 * @since   1.0.0
 */
interface MetaFieldModelInterface extends FieldModelInterface
{
    const OBJECT_TYPE_COMMENT = 'comment';
    const OBJECT_TYPE_POST    = 'post';
    const OBJECT_TYPE_TERM    = 'term';
    const OBJECT_TYPE_USER    = 'user';

    /**
     * get_metadata() 호출 인자 'single'에 대응.
     *
     * @return bool
     * @see    get_metadata()
     */
    public function isSingle(): bool;

    /**
     * 지정된 메타데이터의 순서 유지 정책을 리턴.
     *
     * isSinge()이 참이면 이 값은 의미가 없습니다. 단, isSingle() 거짓이면
     * 한 오브젝트에 이 메타데이타가 복수가 존재할 수 있으므로 값의 순서가 중요해질 수 있습니다.
     *
     * 이 값이 true 이면 저장 순서가 중요한 것으로 취급되어 해당 오브젝트의 같은 메타데이터가 동시에 편집됩니다.
     * 반면 false 라면 값의 순서는 그다지 중요하다고 판단되지 않아 각 개별 메타 키에 대해서만 편집이 이뤄집니다.
     * 값의 성격에 따라 적절히 선택하여 사용하십시오.
     *
     * @return bool
     */
    public function isOrdered(): bool;

    /**
     * 지정된 메타데이터의 유일성 설정을 리턴
     *
     * isSingle() 이 참일 때만 의미가 있습니다. 단, 저장시 unique 를 위한 DB 쿼리 부하가 더 들어가게 됩니다.
     * 잘 사용되지는 않으나 필요시 사용하세요.
     *
     * 이 값이 true 이면,
     * - 커스텀 포스트: 해당 포스트 타입에서 해당 메타 키의 값이 유일함.
     * - 택소노미:      해당 택소노미에서 메타 키의 값이 유일함.
     * - 유저:          해당 메타 키의 값을 가진 유저는 유일함.
     *
     * @return bool
     */
    public function isUnique(): bool;

    /**
     * 지정된 오브젝트 타입을 리턴
     *
     * @return string comment, post, term, or user,
     * @see    get_metadata()
     * @see    update_metadata()
     */
    public function getObjectType(): string;

    /**
     * 지정된 오브젝트 서브타입을 리턴
     *
     * @return string
     * @see    register_meta()
     * @see    registered_meta_key_exists()
     */
    public function getObjectSubtype(): string;

    /**
     * 지정된 택소노미를 리턴.
     *
     * @return string objectType 'term' 에만 의미가 있습니다.
     */
    public function getTaxonomy(): string;

    /**
     * 메타데이터를 해당 오브젝트에서 검사
     *
     * @param object|int $objectId
     *
     * @return mixed
     */
    public function retrieve($objectId);

    /**
     * 지정한 오브젝트에 메타데이터를 저장.
     *
     * @param object|int $objectId
     * @param mixed      $value
     *
     * @return bool|int
     */
    public function save($objectId, $value);

    /**
     * @param object|int $objectId        대상 오브젝트.
     * @param array      $request         $_GET, $_POST, $_REQUEST 변수.
     * @param null       $whenKeyNotFound $request 변수에서 키를 찾지 못했을 때의 대응.
     *                                    - null:        args['whenKeyNotFound'] 값을 참조할 것입니다.
     *                                    - #skip:       저장하지 않고 넘어갑니다.
     *                                    - #useDefault: 기본값으로 대체합니다.
     *                                    - 나머지 형태: 대체 값을 지정할 수 있습니다.
     *                                    호출할 수 있는 것이 입력되면 호출한 결과를 사용할 것입니다.
     *                                    함수의 인자로 $objectId, $this 를 전달합니다.
     *
     * @return bool|int
     */
    public function saveFromRequest($objectId, &$request, $whenKeyNotFound = null);

    /**
     * 이 메타 필드를 코어에 등록시킵니다.
     *
     * @return    bool  키 등록에 성공하거나 이전에 키를 등록했다면 참, 등록 실패시 거짓을 리턴.
     * @see       register_meta()
     * @see       registered_meta_key_exists()
     */
    public function registerMetaField();

    /**
     * 값의 수정을 할 때 권한이 있는지 파악한다.
     *
     * 관련 필터:
     * - sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}
     *
     * @return callable|null
     * @see    register_meta()
     * @see    map_meta_cap()
     */
    public function getAuthCallback();
}
