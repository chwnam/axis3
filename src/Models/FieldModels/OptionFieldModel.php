<?php

namespace Shoplic\Axis3\Models\FieldModels;

use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\ValueTypes\ValueTypeInterface;

class OptionFieldModel extends BaseFieldModel implements OptionFieldModelInterface
{
    private $context = null;

    /**
     * 워드프레스 캐시를 강제 업데이트해야 할 경우에 사용하는 플래그.
     *
     * ValueObject 같은 값 타입을 사용하는 옵션 모델일 때 활용된다.
     * 이 타입의 옵션 값은 테이블에 저장시 먼저 배열 형태로 변환되고, 그 배열을 직렬화시킨다.
     *
     * 그리고 배열값을 읽을 때 다시 배열을 ValueObject 로 복구하는 작업을 한다.
     * 직렬화된 문자열을 배열로 변환하는 것은 그렇다 치더라도, 매번 배열을 다시 ValueObject 변환하는 것은 비효율적이다.
     * 모델에서 'updateCache' 옵션을 true 로 주면 retrieve() 메소드 호출시 아래처럼 동작한다.
     *
     * 1. import() 하여 ValueObject 같은 원래 형태로 되돌린다.
     * 2. 그리고 이 값을 $cacheUpdated 에 기록되지 않았다면 캐시를 원래 형태의 값으로 업데이트 처리한다.
     * 3. $cacheUpdate 에 현재 키를 플래그로 기록하여 중복된 업데이트를 방지한다.
     *
     * @var bool[]
     * @see MetaFieldModel::$cacheUpdated
     */
    private static $cacheUpdated = [];

    public function __construct($key, $args = [])
    {
        $args['_fieldType'] = 'option';

        parent::__construct($key, $args);

        if ($this->args['beforeUpdate'] && is_callable($this->args['beforeUpdate'])) {
            $priority = $this->args['beforeUpdatePriority'] ? $this->args['beforeUpdatePriority'] : 10;
            add_filter('pre_update_option_' . $this->getKey(), $this->args['beforeUpdate'], $priority, 3);
        }

        if ($this->args['afterUpdate'] && is_callable($this->args['afterUpdate'])) {
            $priority = $this->args['afterUpdatePriority'] ? $this->args['afterUpdatePriority'] : 10;
            add_action('update_option_' . $this->getKey(), $this->args['afterUpdate'], $priority, 3);
        }
    }

    public function isAutoload(): bool
    {
        return $this->args['autoload'];
    }

    public function isContextual(): bool
    {
        return $this->args['contextual'];
    }

    public function getGroup(): string
    {
        return $this->args['group'];
    }

    public function retrieve($context = null)
    {
        $value = null;

        if ($this->isContextual()) {
            $default = $this->getDefault();
            $value   = (array)get_option($this->getKey(), $default);
            $context = sanitize_key($context);
            if (isset($value[$context])) {
                $contextValue = &$value[$context];
            } elseif (isset($default[$context])) {
                $contextValue = &$default[$context];
            } else {
                $contextValue = &$default;
            }
            if ($this->args['updateCache']) {
                if (!isset(static::$cacheUpdated[$this->getKey() . '_' . $context])) {
                    static::$cacheUpdated[$this->getKey() . '_' . $context] = true;

                    $value[$context] = $this->import($contextValue);
                    $this->updateCache($value);
                }
            } else {
                $value = $this->import($contextValue);
            }
        } else {
            $value = get_option($this->getKey(), $this->getDefault());
            if ($this->args['updateCache']) {
                if (!isset(static::$cacheUpdated[$this->getKey()])) {
                    static::$cacheUpdated[$this->getKey()] = true;

                    $value = $this->import($value);
                    $this->updateCache($value);
                }
            } else {
                $value = $this->import($value);
            }
        }

        return $value;
    }

    public function save($value, $context = null)
    {
        /**
         * @see OptionFieldModel::defaultSanitizeCallback() 콜백에 현재 콘텍스트를 알리기 이해 콘텍스트 저장
         */
        if ($this->isContextual()) {
            if ($context) {
                $this->context = sanitize_key($context);
                unset(static::$cacheUpdated[$this->getKey() . '_' . $context]);
            }
        } else {
            unset(static::$cacheUpdated[$this->getKey()]);
        }

        // update_option() 함수 내부에서 sanitize 용도로 defaultSanitizeCallback() 메소드를 호출할 것임.
        return update_option($this->getKey(), $value, $this->isAutoload());
    }

    /**
     * 옵션 값을 읽은 후 옵션 값을 import 된 값으로 업데이트해 놓는다.
     *
     * @callback
     * @filter    option_{$option}
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public function updateCache($value)
    {
        $key        = $this->getKey();
        $allOptions = wp_cache_get('alloptions', 'options');

        if (isset($allOptions[$key])) {
            $allOptions[$key] = $value;
            wp_cache_set('alloptions', $allOptions, 'options');
        } else {
            $option = wp_cache_get($key, 'options');
            if (false !== $option) {
                wp_cache_replace($key, $value, 'options');
            } else {
                wp_cache_set($key, $value, 'options');
            }
        }

        return $value;
    }

    /**
     * 기본으로 셋업되는 sanitize_option() 안의 'sanitize_option_{$option}' 필터의 콜백
     *
     * @callback
     * @filter      sanitize_option_{$option}
     *
     * @param mixed $value 전달된 값. register_settings() 에서 인자 수를 1개로 두었다는 점을 참고.
     *
     * @return mixed
     * @see         sanitize_option()
     * @see         update_option()
     * @see         register_setting()  add_filter() 함수가 호출되는 지점은 이 곳
     * @used-by     OptionFieldModel::getSanitizeCallback()
     */
    public function defaultSanitizeCallback($value)
    {
        /** @see ValueTypeInterface::verify() 결과값 참조 */
        list($verified, $result) = $this->verify($this->sanitize($value));

        $verifiedValue = null;

        if ($verified) {
            /** @var mixed $result */
            $verifiedValue = $result;
        } else {
            /** @var string $result 검증에 실패한 경우 $result 는 실패 메시지 문자열. */
            if ($this->getValueType()->isStrict()) {
                $description = sprintf(
                    __('Option value \'%s\' for \'%s\' is invalid.', 'axis3'),
                    $value,
                    $this->getLabel()
                );
                $this->dieValidationError($description, $result, $value);
            } else {
                $description = sprintf(
                    __('Option value %s for \'%s\' is invalid and replaced with the default value.', 'axis3'),
                    is_scalar($value) ? $value : '',
                    $this->getLabel()
                );
                add_settings_error(
                    $this->getGroup(),
                    'warning-' . $this->getKey(),
                    $description,
                    'notice-warning'
                );
                $verifiedValue = $this->getDefault(ValueTypeInterface::DEFAULT_CONTEXT_VERIFY);
            }
        }

        if ($this->isContextual()) {
            if (!is_array($value)) {
                $value = [];
            }
            $value[$this->context] = $this->export($verifiedValue);
        } else {
            $value = $this->export($verifiedValue);
        }

        /**
         * save() 메소드를 위해 저장한 콘텍스트 초기화.
         *
         * @see OptionFieldModel::save()
         */
        $this->context = null;

        return $value;
    }

    public function registerOptionField()
    {
        $registeredSettings = get_registered_settings();
        if (!isset($registeredSettings[$this->getKey()])) {
            register_setting(
                $this->getGroup(),
                $this->getKey(),
                [
                    'type'              => $this->getType(),
                    'group'             => $this->getGroup(),
                    'description'       => $this->getDescription(),
                    'sanitize_callback' => $this->getSanitizeCallback(),
                    'show_in_rest'      => $this->isShowInRest(),
                ]
            );
        }
    }

    public static function getDefaultArgs(): array
    {
        return array_merge(
            parent::getDefaultArgs(),
            [
                /** bool 옵션 오토로딩을 지원합니다. 기본 true. */
                'autoload'             => true,

                /** bool 문맥적 옵션을 지원합니다. 기본 false. */
                'contextual'           => false,

                /** string 옵션 그룹의 이름입니다. 옵션 API 사용을 위해 필요합니다. */
                'group'                => '',

                /** bool REST API 에 보일 지 결정합니다. 기본 false */
                'showInRest'           => false,

                /**
                 * callable 업데이트 전 콜백. pre_update_option_{$option} 필터.
                 *          콜백은 3개의 인자를 받음.
                 *          주의! 필터라서 반드시 $value 리턴이 있어야 함.
                 *
                 * @param mixed  $value
                 * @param mixed  $old_value
                 * @param string $option_name
                 *
                 * @return mixed
                 * @see update_option()
                 */
                'beforeUpdate'         => null,
                'beforeUpdatePriority' => null,

                /**
                 * callable 업데이트 후 콜백. update_option_{$option} 액선.
                 *          콜백은 3개의 인자를 받음.
                 *          주의! 필터라서 반드시 $value 리턴이 있어야 함.
                 *
                 * @param mixed $value
                 * @param mixed $old_value
                 * @paramstring $option_name
                 *
                 * @see update_option()
                 */
                'afterUpdate'          => null,
                'afterUpdatePriority'  => null,
            ]
        );
    }
}
