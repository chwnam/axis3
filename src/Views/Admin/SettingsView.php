<?php

namespace Shoplic\Axis3\Views\Admin;

use Shoplic\Axis3\Interfaces\Views\Admin\FieldWIdgets\FieldWidgetInterface;
use Shoplic\Axis3\Interfaces\Views\Admin\SettingsViewInterface;
use Shoplic\Axis3\Views\BaseView;
use function Shoplic\Axis3\Functions\toPascalCase;

abstract class SettingsView extends BaseView implements SettingsViewInterface
{
    /** @var array 추가된 섹션. */
    protected $sections = [];

    /** @var array 추가된 필드. */
    protected $fields = [];

    /** @var bool 섹션과 필드를 코어에 등록하면 클래스에 추가한 내용은 불필요하므로 해당 내용은 소거한다. 기본 true. */
    protected $flush = true;

    private $template = 'generics/generic-options.php';

    /** @var string 옵션 페이지 */
    private $page = '';

    /** @var string 옵션 그룹 */
    private $optionGroup = '';

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function setTemplate(string $template)
    {
        $this->template = $template;

        return $this;
    }

    public function getPage(): string
    {
        return $this->page;
    }

    public function setPage(string $page)
    {
        $this->page = $page;

        return $this;
    }

    public function getOptionGroup(): string
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(string $optionGroup)
    {
        $this->optionGroup = sanitize_key($optionGroup);

        return $this;
    }

    public function addSection(string $slug, string $title, $callback = null)
    {
        $slug = sanitize_key($slug);

        if (is_callable($callback)) {
        } elseif (is_callable([$this, 'renderSection' . toPascalCase($slug)])) {
            $callback = [$this, 'renderSection' . toPascalCase($slug)];
        } else {
            $callback = '__return_empty_string';
        }

        $this->sections[$slug] = [
            'title'    => $title,
            'callback' => $callback,
        ];

        return $this;
    }

    public function addField(string $section, $fieldWidget, $args = [], $key = null, $callback = null)
    {
        $section = sanitize_key($section);

        if (!$key) {
            $key = $fieldWidget->getFieldModel()->getKey();
        }

        $fieldWidget->setStarter($this->getStarter());

        $args['_fieldWidget'] = $fieldWidget;

        $this->fields[$key] = [
            'section'  => $section,
            'callback' => $callback,
            'args'     => $args,
        ];
    }

    /**
     * 옵션 화면을 출력한다.
     *
     * 정상적인 출력을 위해서는 인터페이스에 정의된 메소드를 이용해서 섹션, 필드를 코어에 등록해야 한다.
     * 보통 다음 순서대로 실행한다.
     *
     * setup() 메소드:
     * - setPage() 메소드로 페이지 이름을 등록한다.'
     * - setOptionGroup() 메소드를 사용해 옵션 그룹을 설정한다.
     *
     * prepareSettings() 메소드:
     * - addSection() 메소드를 사용해 섹션을 등록한다.
     * - addField() 메소드를 사용해 필드를 등록한다.
     * - 추가로 템플릿을 변경하려면 setTemplate() 메소드를 사용할 수 있다.
     *
     * @return void
     */
    public function renderSettings()
    {
        $this->prepareSettings();
        $this->addSettingsSections();
        $this->addSettingsFields();

        if ($this->flush) {
            $this->sections = null;
            $this->fields   = null;
        }

        $this
            ->enqueueStyle(
                'axis3-field-widget',
                'admin/field-widgets/style.css',
                ['axis3-jquery-ui'], AXIS3_VERSION, 'all', '', true, true
            )
            ->enqueueScript(
                'axis3-field-widget',
                'admin/field-widgets/script.js',
                ['jquery', 'jquery-ui-tooltip'], AXIS3_VERSION, true, '', [], '', 'after', true, true
            )
            ->render(
                $this->getTemplate(),
                [
                    'option_group' => $this->getOptionGroup(),
                    'page'         => $this->getPage(),
                ]
            );
    }

    /**
     * 기본 렌더 필드.
     *
     * 각 위젯별로 약속된 화면을 그려낸다.
     *
     * @param array $args
     *
     * @used-by SettingsView::addSettingsFields()
     */
    public function defaultRenderField(array $args)
    {
        $fieldWidget = $args['_fieldWidget'] ?? null;

        /** @var FieldWidgetInterface $fieldWidget */
        if ($fieldWidget) {
            $fieldWidget->renderWidget();
            $fieldWidget->renderDescription();
        }
    }

    /**
     * renderSettings() 메소드가 불리기 전 호출됩니다. 여기서 필요한 모든 설정 준비를 마쳐야 합니다.
     *
     * @return self
     */
    abstract protected function prepareSettings();

    protected function getSections(): array
    {
        return $this->sections;
    }

    protected function getFields(): array
    {
        return $this->fields;
    }

    private function addSettingsSections()
    {
        foreach ($this->getSections() as $key => $section) {
            $title    = $section['title'] ?? '';
            $callback = $section['callback'] ?? '__return_empty_string';
            add_settings_section($key, $title, $callback, $this->getPage());
        }
    }

    /**
     * @uses SettingsView::defaultRenderField()
     */
    private function addSettingsFields()
    {
        foreach ($this->getFields() as $key => $field) {
            /** @var array $args */
            $section  = $field['section'] ?? '';
            $callback = $field['callback'] ?? null;
            $args     = $field['args'] ?? [];

            /** @var FieldWidgetInterface|null $fieldWidget */
            $fieldWidget = $args['_fieldWidget'] ?? null;
            $title       = $fieldWidget ? $fieldWidget->getTitle() : '';
            $labelFor    = $fieldWidget->getLabelFor();

            if ($labelFor) {
                $args['label_for'] = $labelFor;
            }

            if (!$callback) {
                if (is_callable([$this, 'renderField' . toPascalCase($key)])) {
                    $callback = [$this, 'renderField' . toPascalCase($key)];
                } else {
                    $callback = [$this, 'defaultRenderField'];
                }
            }

            add_settings_field($key, $title, $callback, $this->getPage(), $section, $args);
        }
    }
}
