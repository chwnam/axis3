<?php

namespace Shoplic\Axis3\Views\Admin\FieldWidgets;

use Shoplic\Axis3\Interfaces\Models\FieldModels\FieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\MetaFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\OptionFieldModelInterface;
use Shoplic\Axis3\Interfaces\Models\ValueObjects\ValueObjectInterface;
use Shoplic\Axis3\Interfaces\Views\Admin\FieldWidgets\FieldWidgetInterface;
use Shoplic\Axis3\Models\FieldModels\StubFieldModel;
use Shoplic\Axis3\Models\ValueTypes\ValueObjectType;
use Shoplic\Axis3\Views\BaseView;

/**
 * Class BaseFieldWidget
 *
 * 필드 위젯 클래스
 *
 * 모든 필드의 기본 클래스입니다.
 * 상속받는 클래스는 outputWidgetCore() 메소드를 완성해야 합니다.
 *
 * @package Shoplic\Axis3\Views\Admin\FieldWidgets
 * @since   1.0.0
 */
abstract class BaseFieldWidget extends BaseView implements FieldWidgetInterface
{
    /** @var array 위젯 옵션 인자 목록 */
    protected $args = [];

    /** @var FieldModelInterface 이 위젯과 묶인 모델 인스턴스 */
    private $fieldModel = null;

    /** @var array 렌더된 필드 위젯 목록. 키가 FQCN, 값은 true. false 없음. */
    private static $renderedWidgets = [];

    /**
     * BaseFieldWidget constructor.
     *
     * @param null|FieldModelInterface $fieldModel
     * @param array                    $args
     */
    public function __construct($fieldModel, $args = [])
    {
        $this->setFieldModel($fieldModel);
        if ($fieldModel->getStarter()) {
            $this->setStarter($fieldModel->getStarter());
        }

        $this->args = wp_parse_args($args, static::getDefaultArgs());

        // 파라미터 교정. 툴팁 사용시 설명란 개행하지 않고, 출력하지 않는다.
        if ($this->args['tooltip']) {
            $this->args['outputDesc'] = $this->args['brDesc'] = false;
        }

        // keyPostfix 처리
        $this->args['keyPostfix'] = array_filter(array_map('sanitize_key', (array)$this->args['keyPostfix']));

        if ($fieldModel->getValueType() instanceof ValueObjectType) {
            if (!isset($this->args['getterMethod'])) {
                throw new \InvalidArgumentException(__('ValueType must define \'getterMethod\' property.', 'axis3'));
            } elseif (!method_exists($fieldModel->getValueType()->getType(), $this->args['getterMethod'])) {
                throw new \InvalidArgumentException(
                    sprintf(
                        __('The value type \'%s\'does not have method \'%s\'.', 'axis3'),
                        $fieldModel->getValueType()->getType(),
                        $this->args['getterMethod']
                    )
                );
            }
        }
    }

    public function getFieldModel(): FieldModelInterface
    {
        return $this->fieldModel;
    }

    public function setFieldModel(FieldModelInterface $fieldModel)
    {
        $this->fieldModel = $fieldModel;
    }

    public function renderWidget()
    {
        if (!$this->args['echo']) {
            ob_start();
        }

        $className = get_class($this);
        if (!isset(self::$renderedWidgets[$className])) {
            $this->onceBeforeRender();
        }

        $this->beforeRenderWidget();
        $this->outputWidgetCore();
        $this->renderContext();
        $this->afterRenderWidget();

        if (!isset(self::$renderedWidgets[$className])) {
            $this->onceAfterRender();
            self::$renderedWidgets[$className] = true;
        }

        if (!$this->args['echo']) {
            return ob_get_clean();
        }

        return null;
    }

    public function renderDescription()
    {
        if (!$this->args['outputDesc']) {
            return;
        }

        $description = $this->getDescription();
        if (empty($description)) {
            return;
        }

        if ($this->args['brDesc']) {
            echo '<br>';
        } else {
            echo (isset($this->args['noSpacer']) && $this->args['noSpacer']) ? '' : '<span class="spacer"></span>';
        }

        echo '<span class="description">' . wp_kses_post($description) . '</span>';
    }

    public function beforeRenderWidget()
    {
        $this->renderCallback('before');
    }

    public function afterRenderWidget()
    {
        $this->renderCallback('after');
    }

    public function renderFormTableTr()
    {
        echo '<tr>';
        $this->renderFormTableTh();
        $this->renderFormTableTd();
        echo '</tr>';
    }

    public function renderFormTableTh()
    {
        if (is_array($this->args['thClass'])) {
            $tdClass = implode(' ', array_map('sanitize_html_class', $this->args['thClass']));
        } elseif (is_string($this->args['thClass'])) {
            $tdClass = implode(' ', array_map('sanitize_html_class', preg_split('/\s+/', $this->args['thClass'])));
        } else {
            $tdClass = '';
        }

        $thStyle  = esc_attr($this->args['thStyle']);
        $labelFor = esc_attr($this->getLabelFor());

        echo "<th class='{$tdClass}' style='{$thStyle}' scope='row'>";
        echo "<label for='{$labelFor}'>";
        echo $this->getTitle();
        echo $this->args['tooltip'] ? $this->getTooltip() : '';
        echo '</label>';
        echo '</th>';
    }

    public function renderFormTableTd()
    {
        if (is_array($this->args['tdClass'])) {
            $tdClass = implode(' ', array_map('sanitize_html_class', $this->args['tdClass']));
        } elseif (is_string($this->args['tdClass'])) {
            $tdClass = implode(' ', array_map('sanitize_html_class', preg_split('/\s+/', $this->args['tdClass'])));
        } else {
            $tdClass = '';
        }

        $thStyle = esc_attr($this->args['tdStyle']);

        echo "<td class='{$tdClass}' style='{$thStyle}'>";
        $this->renderWidget();
        $this->renderDescription();
        echo '</td>';
    }

    public function renderContext()
    {
        if (!empty($this->args['context'])) {
            $id    = esc_attr($this->getId() . '-context');
            $name  = esc_attr($this->getName() . '_context');
            $value = esc_attr($this->args['context']);
            echo "<input type='hidden' id='{$id}' name='${name}' value='{$value}'>";
        }
    }

    public function getId(): string
    {
        $id      = $this->getFieldModel()->getKey();
        $postfix = '';

        if (!empty($this->args['keyPostfix'])) {
            $postfix = '-' . implode('-', $this->args['keyPostfix']);
        };

        return "{$id}{$postfix}";
    }

    public function getName(): string
    {
        $name    = $this->getFieldModel()->getKey();
        $postfix = '';

        if (!empty($this->args['keyPostfix'])) {
            $postfix = '[' . implode('][', $this->args['keyPostfix']) . ']';
        }

        return "{$name}{$postfix}";
    }

    public function getValue()
    {
        $fieldModel = $this->getFieldModel();
        $value      = null;

        switch ($fieldModel->getFieldType()) {
            case 'meta':
                $objectId = $this->getObjectId();
                $value    = $objectId ? $fieldModel->retrieve($objectId) : $fieldModel->getDefault();
                break;

            case 'option':
                /** @var OptionFieldModelInterface $fieldModel */
                $context = null;
                if ($fieldModel->isContextual()) {
                    $context = $this->args['context'];
                }
                $value = $fieldModel->retrieve($context);
                break;

            case 'stub':
                /** @var StubFieldModel $fieldModel */
                $value = $fieldModel->getDefault();
                break;
        }

        if ($value instanceof ValueObjectInterface) {
            $value = $value->{$this->args['getterMethod']}();
        }

        return $value;
    }

    public function getTitle(): string
    {
        return $this->getLabel() .
            ($this->isRequired() ? ' <span class="axis3-widget-required">[필수]</span>' : '') .
            $this->getTooltip();
    }

    public function getLabel(): string
    {
        if ($this->args['label']) {
            $label = esc_html($this->args['label']);
        } else {
            if ($this->args['preferShortLabel']) {
                $label = esc_html($this->getFieldModel()->getShortLabel());
            } else {
                $label = esc_html($this->getFieldModel()->getLabel());
            }
        }

        return $label;
    }

    public function getLabelFor(): string
    {
        if (true === $this->args['labelFor']) {
            return $this->getId();
        } elseif (false === $this->args['labelFor']) {
            return '';
        } else {
            return $this->args['labelFor'];
        }
    }

    /**
     * 위젯의 설명을 리턴한다.
     *
     * 옵션의 'desc' 속성을 우선하여 출력한다.
     * desc 속성이 비어 있으면 필드 모델의 설명을 가져온다.
     *
     * @return string
     */
    public function getDescription(): string
    {
        if (empty($this->args['desc'])) {
            return $this->getFieldModel()->getDescription();
        } else {
            return $this->args['desc'];
        }
    }

    public function getTooltip(): string
    {
        if (!$this->args['tooltip']) {
            return '';
        }

        $tooltip = htmlspecialchars(
            wp_kses(
                html_entity_decode($this->getDescription()),
                [
                    'br'     => [],
                    'em'     => [],
                    'strong' => [],
                    'small'  => [],
                    'span'   => [],
                    'ul'     => [],
                    'li'     => [],
                    'ol'     => [],
                    'p'      => [],
                ]
            ),
            ENT_COMPAT | ENT_QUOTES
        );

        return "<span class='dashicons dashicons-editor-help axis3-widget-tooltip' data-tooltip='{$tooltip}'></span><div class='wp-clearfix'></div>";
    }

    public function isRequired(): bool
    {
        if (!is_null($this->args['required'])) {
            return $this->args['required'];
        } else {
            return $this->getFieldModel()->isRequired();
        }
    }

    public function getRequiredMessage()
    {
        if ($this->args['requiredMessage']) {
            if (is_callable($this->args['requiredMessage'])) {
                return call_user_func($this->args['requiredMessage'], $this);
            } else {
                return $this->args['requiredMessage'];
            }
        } else {
            return $this->getFieldModel()->getRequiredMessage();
        }
    }

    public function onceBeforeRender()
    {
    }

    public function onceAfterRender()
    {
    }

    public static function getDefaultArgs(): array
    {
        return [
            // string: 기본으로 필드 모델의 설명을 사용하지만, 이 인자는 그 값을 대신한다.
            'desc'             => '',

            // bool: 설명란을 출력할지 결정. 기본적으로 span.description 을 사용함.
            'outputDesc'       => true,

            // bool: 설명란 출력시 강제 개행할지 결정. 사용하면 설명 출력 전 <br> 태그를 먼저 넣는다.
            'brDesc'           => true,

            // null|bool: required 옵션은 필드 모델에도 있지만, null 이 아니라면 이 값을 우선한다.
            'required'         => null,

            // string|callable:  required 필드일 경우 기본 메시지를 오버라이드하기 위한 메시지.
            //                   이 옵션 또한 필드 모델에도 있으나, null 이 아니라면 이 값을 우선한다.
            //                   입력하지 않으면 웹브라우저의 '이 항목을 필수로 입력해 주십시오' 같은 기본 메시지를 대체한다.
            'requiredMessage'  => null,

            // bool: 사용시 jquery-ui-tooltip 을 이용하며 설명란을 툴팁으로 대신한다.
            'tooltip'          => false,

            // null|string: 기본으로는 필드 모델의 레이블을 사용하나, 이 인자는 그 값을 대체할 수 있다.
            //              이 값을 사용하면 preferShortLabel 은 무시된다.
            'label'            => null,

            // bool|string: <label> 태그의 for 속성을 출력할지 결정. true/false 로 결정할 수 있다.
            //              기본값은 위젯 필드에서 가져오지만 이 값을 문자열로 하면 그 문자열을 속성으로 이용한다.
            'labelFor'         => true,

            // bool: 필드 모델에 설정한 shortLabel 을 더 선호하여 출력할지 결정한다.
            'preferShortLabel' => false,

            // null|int: 위젯 값을 불러올 오브젝트 ID. 포스트형의 오브젝트가 아니라면 이 값을 잘 설정해 줘야 한다.
            //           포스트 메타라면 NULL 일 때 최대한 전역 변수로 설정된 $post 를 찾으려고 노력할 것이다.
            'objectId'         => null,

            // string|array: <th> 태그의 클래스를 별도로 줄 수 있다.
            'thClass'          => '',

            // string: <th> 태그 출력시 style 속성값을 줄 수 있다.
            'thStyle'          => '',

            // array|string: <td> 태그의 클래스를 별도로 줄 수 있다.
            'tdClass'          => '',

            // string: <td> 태그 출력시 style 속성값을 줄 수 있다.
            'tdStyle'          => 'vertical-align: middle;',

            // string: 콘텍스트를 사용하는 옵션 모델인 경우라면 콘텍스트를 명시할 필요가 있다.
            //         이 인자를 통해 콘텍스트를 입력한다.
            'context'          => '',

            // null|string|callable: 위젯 출력 전 어떤 행동을 더할 수 있다.
            //                       null 은 무시.
            //                       string 은 그대로 출력.
            //                       callable 위젯 인스턴스를 인자로 넘긴다. 출력은 없다.
            'before'           => null,

            // null|string|callable: 위젯 출력 후 어떤 행동을 더할 수 있다.
            //                       null 은 무시.
            //                       string 은 그대로 출력.
            //                       callable 위젯 인스턴스를 인자로 넘긴다. 출력은 없다.
            'after'            => null,

            // bool: renderWidget() 호출시 바로 출력할지, 아니면 렌더링된 결과를 리턴할지 결정.
            //       폼 필드의 행을 출력 보다는 단일 위젯을 별개로 출력해야 할 때 유용하다.
            'echo'             => true,

            // null|string|array: name, id 속성의 접미어를 더한다.
            //                    폼 제출시 전달하는 변수를 배열 형태로, 배열의 키를 명시적으로 지정할 수 있다.
            //                    다중 배열로 지정하려면 array 를 이용한다.
            'keyPostfix'       => null,

            // string: 값 타입이 valueObject 인 경우 명시해야 한다.
            //         메소드 이름으로부터 값을 구해 온다.
            // 'getterMethod' => null,

            // bool: 이 키를 설정하고 false 로 두면 brDesc=false 일 때 공간을 추가하지 않는다.
            // 'noSpacer' => false
        ];
    }

    /**
     * 실제 위젯의 출력을 하는 알짜 부분이며, 상속된 클래스가 구현해야 할 부분이다.
     *
     * @return void
     */
    abstract protected function outputWidgetCore();

    protected function getObjectId()
    {
        $objectId = null;

        if ('meta' === $this->getFieldModel()->getFieldType()) {
            $objectId = $this->args['objectId'] ?? null;
            if (is_null($objectId)) {
                $objectType = $this->getFieldModel()->getObjectType();
                if (MetaFieldModelInterface::OBJECT_TYPE_POST === $objectType) {
                    // objectId 가 주어진 적 없지만, 오브젝트 타입이 포스트라면 전역변수에서 가져올 수 있다.
                    $post = get_post();
                    if ($post && $post->ID) {
                        $objectId = $post->ID;
                    }
                } elseif (MetaFieldModelInterface::OBJECT_TYPE_USER) {
                    throw new \RuntimeException('OBJECT_TYPE_USER must supply an objectId argument.');
                }
            }
        }

        return $objectId;
    }

    private function renderCallback($param)
    {
        if (!isset($this->args[$param]) || !$this->args[$param]) {
            return;
        } else {
            if (is_callable($this->args[$param])) {
                call_user_func($this->args[$param], $this);
            } elseif (is_string($this->args[$param])) {
                echo $this->args[$param];
            }
        }
    }
}
