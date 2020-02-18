<?php

namespace Shoplic\Axis3\Models\FieldModels;

use Shoplic\Axis3\Interfaces\Models\FieldModels\MetaFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;
use Shoplic\Axis3\Models\ValueTypes\ValueObjectType;
use WP_Comment;
use WP_Comment_Query;
use WP_Post;
use WP_Query;
use WP_Term;
use WP_Term_Query;
use WP_User;
use WP_User_Query;

class MetaFieldModel extends BaseFieldModel implements MetaFieldModelInterface
{
    const KEY_NOT_FOUND_SKIP        = '#skip';
    const KEY_NOT_FOUND_USE_DEFAULT = '#useDefault';

    /**
     * MetaFieldModel constructor.
     *
     * @param       $key
     * @param array $args
     *
     * @see BaseFieldModel::getDefaultArgs()        기본 인자의 목록을 여기서 참고하세요.
     * @see MetaFieldModel::getDefaultArgs()    기본 인자의 목록을 여기서 참고하세요.
     */
    public function __construct($key, $args = [])
    {
        $args['_fieldType'] = 'meta';

        parent::__construct($key, $args);

        if (true === $this->args['authCallback']) {
            /** @uses MetaFieldModel::defaultAuthCallback() */
            $this->args['authCallback'] = [$this, 'defaultAuthCallback'];
        } elseif (!is_callable($this->args['authCallback'])) {
            $this->args['authCallback'] = null;
        }

        if (is_null($this->args['updateCache'])) {
            $this->args['updateCache'] = $this->getValueType() instanceof ValueObjectType;
        }
    }

    public function isSingle(): bool
    {
        return $this->args['single'];
    }

    public function isOrdered(): bool
    {
        return $this->args['ordered'];
    }

    public function isUnique(): bool
    {
        return $this->args['unique'];
    }

    public function getObjectType(): string
    {
        return $this->args['objectType'];
    }

    public function getObjectSubtype(): string
    {
        if (is_callable($this->args['objectSubtype'])) {
            return call_user_func($this->args['objectSubtype'], $this);
        } elseif (empty($this->args['objectSubtype'])) {
            return $this->args['objectType'];
        } else {
            return $this->args['objectSubtype'];
        }
    }

    public function getTaxonomy(): string
    {
        return $this->args['taxonomy'];
    }

    public function retrieve($objectId)
    {
        $value = get_metadata(
            $this->getObjectType(),
            $this->checkObjectId($objectId),
            $this->getKey(),
            $this->isSingle()
        );

        if (empty($value)) {
            $cache = wp_cache_get($objectId, $this->getObjectType() . '_meta');
            if (!$cache || !isset($cache[$this->getKey()])) {
                $value = $this->getDefault();
            }
        }

        if ($this->args['updateCache']) {
            if (is_array($value) || is_scalar($value)) {
                $cache = wp_cache_get($objectId, $this->getObjectType() . '_meta');
                if (false === $cache) {
                    $cache = [];
                }
                $cache[$this->getKey()][0] = $value = $this->import($value);
                wp_cache_replace($objectId, $cache, $this->getObjectType() . '_meta');
            }
        } else {
            $value = $this->import($value);
        }

        return $value;
    }

    public function save($objectId, $value)
    {
        if ($this->isUnique() && !$this->checkUniqueness($this->checkObjectId($objectId), $value)) {
            $this->dieValidationError(
                __('Verification Failed', 'axis3'),
                sprintf(
                    __('\'%s\' is a unique field. The value is duplicated.', 'axis3'),
                    $this->getLabel()
                ),
                $value
            );
        }

        $objectType = $this->getObjectType();
        $objectId   = $this->checkObjectId($objectId);
        $metaKey    = $this->getKey();

        if ($this->isSingle()) {
            return update_metadata($objectType, $objectId, $metaKey, $value);
        } elseif (is_array($value)) {
            $result  = true;
            $current = get_metadata($objectType, $objectId, $metaKey, false);

            if ($this->isOrdered()) {
                delete_metadata($objectType, $objectId, $metaKey);
                foreach ($value as $item) {
                    $result &= add_metadata($objectType, $objectId, $metaKey, $item);
                }
            } else {
                $toDelete = array_diff($current, $value);
                $toAdd    = array_diff($value, $current);
                foreach ($toDelete as $item) {
                    $result &= delete_metadata($objectType, $objectId, $metaKey, $item);
                }
                foreach ($toAdd as $item) {
                    $result &= add_metadata($objectType, $objectId, $metaKey, $item);
                }
            }
            return $result;
        } else {
            return false;
        }
    }

    public function saveFromRequest($objectId, &$request, $whenKeyNotFound = null)
    {
        if (isset($request[$this->getKey()])) {
            return $this->save($objectId, $request[$this->getKey()]);
        } else {
            if (is_null($whenKeyNotFound)) {
                $whenKeyNotFound = $this->args['whenKeyNotFound'];
            }
            switch ($whenKeyNotFound) {
                case self::KEY_NOT_FOUND_SKIP:
                    return false;

                case self::KEY_NOT_FOUND_USE_DEFAULT:
                    return $this->save($objectId, $this->getDefault());

                default:
                    if (is_callable($whenKeyNotFound)) {
                        $value = call_user_func_array($whenKeyNotFound, [$objectId, &$this]);
                    } else {
                        $value = &$whenKeyNotFound;
                    }
                    return $this->save($objectId, $value);
            }
        }
    }

    public function registerMetaField()
    {
        if (!registered_meta_key_exists($this->getObjectType(), $this->getObjectType(), $this->getObjectSubtype())) {
            return register_meta(
                $this->getObjectType(),
                $this->getKey(),
                [
                    'object_subtype'    => $this->getObjectSubtype(),
                    'type'              => $this->getType(),
                    'description'       => $this->getDescription(),
                    'single'            => $this->isSingle(),
                    'sanitize_callback' => $this->getSanitizeCallback(),
                    'auth_callback'     => $this->getAuthCallback(),
                    'show_in_rest'      => $this->isShowInRest(),
                ]
            );
        }

        return true;
    }

    public function getAuthCallback()
    {
        return $this->args['authCallback'];
    }

    /**
     * 기본적으로 제공되는 sanitize_meta() 안의 필터
     *
     * @callback
     * @filter      "sanitize_{$object_type}_meta_{$meta_key}_for_{$object_subtype}"
     *
     * @param mixed  $metaValue
     * @param string $metaKey
     * @param string $objectType
     * @param string $objectSubtype
     *
     * @return mixed
     * @see sanitize_meta()
     * @see update_metadata()
     */
    public function defaultSanitizeCallback($metaValue, $metaKey, $objectType, $objectSubtype)
    {
        if ($this->getKey() === $metaKey) {
            /**@see ValueTypeInterface::verify() 결과값 참조 */
            list($verified, $result) = $this->verify($this->sanitize($metaValue));

            if ($verified) {
                /** @var mixed $result 겁증이 올바른 경우 $result 는 올바르게 교정된 값. */
                return $this->export($result);
            } else {
                /** @var string $result 검증에 실패한 경우 $result 는 실패 메시지 문자열. */
                if ($this->getValueType()->isStrict()) {
                    $description = sprintf(
                        __(
                            'The input value %s of field %s is invalid!',
                            'axis3'
                        ),
                        $metaValue,
                        $this->getLabel(),
                        $this->getDefault(ValueTypeInterface::DEFAULT_CONTEXT_VERIFY)
                    );
                    $this->dieValidationError($description, $result, $metaValue);
                } else {
                    $defaultValue = $this->getDefault(ValueTypeInterface::DEFAULT_CONTEXT_VERIFY);
                    if ($metaValue != $defaultValue) {
                        $desc = sprintf(
                            __(
                                'The value \'%s\' for custom field \'%s\' is invalid and replaced with the default value \'%s\'.',
                                'axis3'
                            ),
                            $metaValue,
                            $this->getLabel(),
                            $defaultValue
                        );
                        add_settings_error(
                            "{$objectType}-{$objectSubtype}",
                            'warning-' . $this->getKey(),
                            $desc,
                            'warning'
                        );
                    }
                    return $this->export($defaultValue);
                }
            }
        }

        return $metaValue;
    }

    /**
     * 기본적으로 제공되는 auth callback
     *
     * @callback
     * @filter      auth_{$object_type}_meta_{$meta_key}_for_{$object_subtype}
     *
     * @param bool     $allowed  편집 허용 여부
     * @param string   $metaKey  메타 키
     * @param int      $objectId 객체 ID
     * @param int      $userId   사용자 ID
     * @param string   $cap      요구하는 권한
     * @param string[] $caps     사용자의 권한 목록
     *
     * @return bool
     * @see         map_meta_cap()
     */
    public function defaultAuthCallback($allowed, $metaKey, $objectId, $userId, $cap, $caps)
    {
        if ($metaKey == $this->getKey() && $objectId && $userId) {
            $allowed = in_array($cap, $caps);
        }

        return $allowed;
    }

    /**
     * save(), retrieve() 함수의 인자로 ID 가 아닌, 객체 그대로를 넣어도 안전하도록 처리한다.
     *
     * @param int|string|WP_Comment|WP_Post|WP_Term|WP_User $objectId
     *
     * @return false|int;
     */
    private function checkObjectId($objectId)
    {
        if (is_int($objectId) || is_numeric($objectId)) {
            return intval($objectId);
        } elseif ($this->getObjectType() === self::OBJECT_TYPE_COMMENT && $objectId instanceof WP_Comment) {
            return $objectId->comment_ID;
        } elseif ($this->getObjectType() === self::OBJECT_TYPE_POST && $objectId instanceof WP_Post) {
            return $objectId->ID;
        } elseif ($this->getObjectType() === self::OBJECT_TYPE_TERM && $objectId instanceof WP_Term) {
            return $objectId->term_id;
        } elseif ($this->getObjectType() === self::OBJECT_TYPE_USER && $objectId instanceof WP_User) {
            return $objectId->ID;
        }

        return false;
    }

    /**
     * 메타 키/값에 대한 유일성을 점검한다.
     *
     * 오브젝트 타입에 따라 유일성 점검의 의미가 미묘하게 다르므로 각 메소드의 설명을 잘 참고하세요.
     * 또한, 이 기능은 데이터베이스 자체의 UNIQUE 제한 같은 것이 아닌
     * DB 쿼리를 다수 사용하므로 다소 성능 면에서 유리하지는 않습니다. 꼭 필요할 때만 사용하세요.
     *
     * @param int   $objectId
     * @param mixed $value
     *
     * @return bool
     * @uses   MetaFieldModel::checkUniquenessForComment()
     * @uses   MetaFieldModel::checkUniquenessForPost()
     * @uses   MetaFieldModel::checkUniquenessForTerm()
     * @uses   MetaFieldModel::checkUniquenessForUser()
     */
    private function checkUniqueness($objectId, $value)
    {
        switch ($this->getObjectType()) {
            case self::OBJECT_TYPE_COMMENT:
                return $this->checkUniquenessForComment($objectId, $value);
            case self::OBJECT_TYPE_POST:
                return $this->checkUniquenessForPost($objectId, $value);
            case self::OBJECT_TYPE_TERM:
                return $this->checkUniquenessForTerm($objectId, $value);
            case self::OBJECT_TYPE_USER:
                return $this->checkUniquenessForUser($objectId, $value);
        }

        return false;
    }

    /**
     * 댓글 메타 키/값의 다른 곳에서 발견되는지 검사.
     * 단, 댓글의 상위 포스트 타입 내애서만 검사한다. 서로 다른 포스트 타입에 걸린 댓글에 대한 중복은 관계 없다.
     *
     * @param int   $objectId
     * @param mixed $value
     *
     * @return bool 유일성의 결과. 잘못된 댓글 ID 가 입력되어도 false 를 리턴한다.
     */
    private function checkUniquenessForComment($objectId, $value)
    {
        $comment = get_comment($objectId);
        if (!$comment) {
            return false;
        }

        $post = get_post($comment->comment_post_ID);
        if (!$post) {
            return false;
        }

        $query = new WP_Comment_Query(
            [
                'comment__not_in' => $objectId,
                'post_type'       => $post->post_type,
                'meta_key'        => $this->getKey(),
                'meta_value'      => $this->export($value),
                'number'          => 1,
                'type'            => 'comment',
                'count'           => true,
            ]
        );

        // 'count' 인자를 참으로 하였으므로 정수가 리턴됨
        return $query->get_comments() == 0;
    }

    /**
     * 포스트 메타 키/값에 대한 중복을 검사한다.
     *
     * 중복은 포스트 타입은 입력한 포스트의 타입에만 해당한다.
     * 또한 포스트의 상태는 'publish', 'pending', 'draft', 'future', 'private' 에만 해당한다
     *
     * @param int   $objectId
     * @param mixed $value
     *
     * @return bool 유일한지에 대한 검사 결과. 잘못된 ID 를 입력하면 false 를 리턴한다.
     */
    private function checkUniquenessForPost($objectId, $value)
    {
        $post = get_post($objectId);
        if (!$post) {
            return false;
        }

        $query = new WP_Query(
            [
                'post__not_in'     => [$post->ID],
                'post_type'        => $post->post_type,
                'post_status'      => ['publish', 'pending', 'draft', 'future', 'private'],
                'meta_key'         => $this->getKey(),
                'meta_value'       => $this->export($value),
                'fields'           => 'ids',
                'posts_per_page'   => 1,
                'no_found_rows'    => true,
                'suppress_filters' => true,
            ]
        );

        return $query->post_count == 0;
    }

    /**
     * 텀 메타 키/값에 대한 유일성을 점검한다.
     *
     * 올바로 점검하려면 반드시 생성자 인자중 'taxonomy'를 맞춰 줘야 한다.
     *
     * @param int   $objectId
     * @param mixed $value
     *
     * @return bool 유일한지에 대한 검사 결과. 잘못된 ID 를 입력하면 false 를 리턴한다.
     */
    private function checkUniquenessForTerm($objectId, $value)
    {
        $term = get_term($objectId);
        if (!$term) {
            return false;
        }

        $query = new WP_Term_Query(
            [
                'exclude'          => [$term->term_id],
                'taxonomy'         => $this->getTaxonomy(),
                'meta_key'         => $this->getKey(),
                'meta_value'       => $this->export($value),
                'hide_empty'       => false,
                'suppress_filters' => true,
                'number'           => 1,
                'fields'           => 'ids',
            ]
        );

        return sizeof($query->get_terms()) == 0;
    }

    /**
     * 유저 메타 키/값에 대한 유일성을 점검한다.
     *
     * @param int   $objectId
     * @param mixed $value
     *
     * @return bool 유일한지에 대한 검사 결과. 잘못된 ID 를 입력하면 false 를 리턴한다.
     */
    private function checkUniquenessForUser($objectId, $value)
    {
        $user = get_user_by('id', $objectId);
        if (!$user) {
            return $user;
        }

        $query = new WP_User_Query(
            [
                'exclude'          => [$user->ID],
                'meta_key'         => $this->getKey(),
                'meta_value'       => $this->export($value),
                'suppress_filters' => true,
                'count_total'      => false,
                'number'           => 1,
                'search_columns'   => ['ID'],
            ]
        );

        return sizeof($query->get_results()) == 0;
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /**
                 * bool get_metadata() 네 번째 인자로 사용됩니다.
                 *      true 면 발견된 키 중 첫번째 것만 사용합니다.
                 *
                 * @see get_metadata()
                 * @see MetaFieldModelInterface::isSingle()
                 */
                'single'          => true,

                /**
                 * bool non-single 일 경우, 값의 순서를 유지한 채로 저장, 복원해야 할지 결정.
                 *
                 * @see MetaFieldModelInterface::isOrdered()
                 */
                'ordered'         => false,

                /**
                 * bool 저장 전 키의 유일성을 검사
                 *
                 * @see MetaFieldModelInterface::isUnique()
                 */
                'unique'          => false,

                /** string 오브젝트 타입을 결정. comment, post, term, user 가 존재한다. */
                'objectType'      => self::OBJECT_TYPE_POST,

                /**
                 * string|callable 오브젝트 서브타입.
                 *
                 * 가령 커스텀 포스트라면, 커스텀 포스트의 포스트 타입.
                 * 호출 가능한 객체를 넣어도 된다. 이 때 이 객체를 호출하며, 인자로는 현재 필드가 입력된다.
                 * 호출된 객체는 반드시 문자열을 리턴해야 한다.
                 *
                 * 따로 지정하지 않으면 기본값은 공백인데, 이러면 모든 오브젝트 타입에 대해 등록된다.
                 * 만약 커스텀 포스트 타입 모델 클래스에서 필드를 생성하면 이 겂이 자동으로 교정된다.
                 */
                'objectSubtype'   => '',

                /** string|null term_meta 인 경우에만 택소노미를 지정합니다. */
                'taxonomy'        => null,

                /**
                 * string|null saveFromRequest() 메소드에서 값을 찾지 못한 경우 대응을 지시합니다.
                 *
                 * @see MetaFieldModelInterface::saveFromRequest()
                 */
                'whenKeyNotFound' => self::KEY_NOT_FOUND_SKIP,

                /**
                 * bool|callable 기본값 false.
                 *               사용하려면 true.
                 *               auth callback 을 오버라이드하려면 여기에 콜백 함수를 대입하세요.
                 *
                 * @see MetaFieldModelInterface::getAuthCallback()
                 */
                'authCallback'    => false,
            ]
        );
    }
}
