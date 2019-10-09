<?php

namespace Shoplic\Axis3\Starters;

use Exception;
use Shoplic\Axis3\Initiators\ModelRegistrationInitiator;
use Shoplic\Axis3\Interfaces\Initiators\InitiatorInterface;
use Shoplic\Axis3\Interfaces\Objects\AxisObjectInterface;
use Shoplic\Axis3\Interfaces\Starters\ClassFinders\ClassFinderInterface;
use Shoplic\Axis3\Interfaces\Starters\StarterInterface;
use Shoplic\Axis3\Starters\ClassFinders\AutoDiscoverClassFinder;

/**
 * Class Starter
 *
 * Axis 3의 기본 개시자.
 *
 * 플러그인의 동작 전체를 조율하는 역할을 맡습니다.
 *
 * @package Shoplic\Axis3\Starters
 * @since   1.0.0
 */
class Starter implements StarterInterface
{
    /** @var AxisObjectInterface[]|object[] claimObject() 메소드를 통해 생성된 객체들의 저장소 */
    private $axisObjects = [];

    /** @var string 필수값. 플러그인의 메인 파일 경로. 절대 경로. 플러그인 내의 경로나 URL 조회시 반드시 사용된다. */
    private $mainFile = '';

    /** @var string 필수값. 이 개시자의 유일한 이름. 반드시 start() 메소드 호출 전에 지정되어야 한다. */
    private $slug = '';

    /** @var string 선택값. 플러그인의 버전. version_compare() 함수가 인식할 수 있는 형태로 지정해야 한다. */
    private $version = '';

    /** @var string 선택값. 이 개시자에서 사용되는 텍스트도메인. 다국어 사용시 지정하면 플러그인 경로로부터 번역 파일을 로드한다. */
    private $textdomain = '';

    /** @var string 선택값. 접두사입니다. 빈 값이면 $slug 에서 가져옵니다. */
    private $prefix = '';

    /** @var bool 모델 자동 등록을 시도합니다. */
    private $modelRegistrationEnabled = true;

    /** @var array key: fqcn, value: mixed */
    private $objectSetupArgs = [];

    private $blogId = null;

    private $initiatorInstances = [];

    /**
     * @var callable[]
     *
     * @see Starter::addRequestContext()
     * @see Starter::initRequestContexts()
     */
    private static $requestContexts = null;

    /**
     * @var array 클래스 검색자의 인스턴스.
     *            키: 콤포넌트 접미사, 값: 인스턴스
     */
    private $classFinders = [
        'Initiator' => [],
        'Model'     => [],
    ];

    public function start()
    {
        if (!$this->checkMultiSiteCondition()) {
            return;
        }

        if (empty($this->getMainFile())) {
            throw new Exception(__('The plugin\'s main file has not been set.', 'axis3'));
        } elseif (empty($this->getVersion())) {
            throw new Exception(__('The plugin\'s version has not been set.', 'axis3'));
        }

        if (empty($this->getSlug())) {
            $this->setSlug(pathinfo($this->getMainFile(), PATHINFO_FILENAME));
        }

        if (empty($this->getTextdomain())) {
            $this->setTextdomain($this->getSlug());
        }

        if (empty($this->getPrefix())) {
            $this->setPrefix($this->getSlug());
        }

        if ($this->isModelRegistrationEnabled()) {
            $initiator = new ModelRegistrationInitiator();
            $initiator->setStarter($this);
            $initiator->setModelClasses($this->getModelClasses());
            $initiator->initHooks();
        }

        foreach ($this->getInitiatorClasses() as $context => $initiatorClasses) {
            if ($this->checkContext($context)) {
                foreach ($initiatorClasses as $initiatorClass) {
                    // 각 개시자 클래스에서 static public property 로서 $disabled = true 로 지정되어 있으면
                    // 해당 개시자 클래스는 동작하지 않는다. 디버깅시 잠시 기능을 죽일 때 유용할 것이다.
                    if (!property_exists($initiatorClass, 'disabled') || !$initiatorClass::$disabled) {
                        /** @var InitiatorInterface $initiator */
                        $initiator = new $initiatorClass();
                        $initiator->setStarter($this);
                        $initiator->setup($this->objectSetupArgs[$initiatorClass] ?? []);
                        $initiator->initHooks();

                        $this->initiatorInstances[ltrim($initiatorClass, '\\')] = $initiator;
                    }
                }
            }
        }

        $this->objectSetupArgs = null;

        StarterPool::getInstance()->addStarter($this);
    }

    public function addClassFinder(ClassFinderInterface $classFinder)
    {
        $component = $classFinder->getComponentPostfix();

        if (empty($component) || !isset($this->classFinders[$component])) {
            throw new Exception(
                sprintf(__('Component postfix \'%s\' is not defined.', 'axis3'), $component)
            );
        }

        if (!isset($this->classFinders[$component])) {
            $this->classFinders[$component] = [];
        }

        $classFinder->setStarter($this);

        $this->classFinders[$component][] = $classFinder;

        return $this;
    }

    public function addObjectSetupArgs(string $fqcn, array $args)
    {
        $this->objectSetupArgs[$fqcn] = $args;

        return $this;
    }

    public function getInitiatorInstances()
    {
        return $this->initiatorInstances;
    }

    public function getInitiatorClasses()
    {
        $foundClasses = [];

        foreach ($this->classFinders['Initiator'] as $finder) {
            /** @var ClassFinderInterface $finder */
            $finder->find($foundClasses);
        }

        return $foundClasses;
    }

    public function getModelClasses()
    {
        $foundClasses = [];

        foreach ($this->classFinders['Model'] as $finder) {
            /** @var ClassFinderInterface $finder */
            $finder->find($foundClasses);
        }

        return $foundClasses;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug)
    {
        $this->slug = sanitize_key($slug);

        return $this;
    }

    public function getMainFile(): string
    {
        return $this->mainFile;
    }

    public function setMainFile(string $mainFile)
    {
        $this->mainFile = $mainFile;

        return $this;
    }

    public function getTextdomain(): string
    {
        return $this->textdomain;
    }

    public function setTextdomain(string $textdomain)
    {
        $this->textdomain = $textdomain;

        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version)
    {
        $this->version = $version;

        return $this;
    }

    public function getPrefix(bool $preferDash = false): string
    {
        return $this->prefix . ($preferDash ? '-' : '_');
    }

    public function setPrefix(string $prefix)
    {
        $this->prefix = rtrim(sanitize_key($prefix), '-_');

        return $this;
    }

    public function prefixed(string $string, bool $preferDash = false): string
    {
        return $this->getPrefix($preferDash) . $string;
    }

    public function isModelRegistrationEnabled(): bool
    {
        return $this->modelRegistrationEnabled;
    }

    public function setModelRegistrationEnabled(bool $enabled)
    {
        $this->modelRegistrationEnabled = $enabled;

        return $this;
    }

    public function getBlogId()
    {
        return $this->blogId;
    }

    public function setBlogId($blogId)
    {
        $this->blogId = $blogId;

        return $this;
    }

    public static function addRequestContext(string $context, callable $function, bool $prepend = false)
    {
        if ($prepend) {
            static::$requestContexts = array_merge([$context => $function], static::$requestContexts);
        } else {
            static::$requestContexts[$context] = $function;
        }
    }

    public function isRequestContext($context)
    {
        $context = ucfirst($context);

        return isset(static::$requestContexts[$context]) &&
            call_user_func(static::$requestContexts[$context], [$context, $this]);
    }

    public function getCurrentRequestContext()
    {
        foreach (static::$requestContexts as $context => $function) {
            if (call_user_func($function, [$context, $this])) {
                return $context;
            }
        }

        return null;
    }

    public function claimObject(string $type, $fqcn, array $setupArgs = [], bool $reuse = true)
    {
        /** @var AxisObjectInterface|object|null $instance */
        $instance = null;

        if (is_object($fqcn) || is_callable($fqcn)) {
            return $fqcn;
        }

        $fqcn = ltrim($fqcn, '\\');

        if (class_exists($fqcn)) {
            if ($reuse && isset($this->axisObjects[$type][$fqcn])) {
                $instance = $this->axisObjects[$type][$fqcn];
            } else {
                $instance = new $fqcn();
                if ($instance instanceof AxisObjectInterface) {
                    $instance->setStarter($this);
                    $instance->setup($setupArgs);
                }
                if ($reuse) {
                    if (!isset($this->axisObjects[$type])) {
                        $this->axisObjects[$type] = [];
                    }
                    $this->axisObjects[$type][$fqcn] = $instance;
                }
            }
        }

        return $instance;
    }

    /**
     * 스타터를 가장 간단한 세팅으로 맞춘다.
     *
     * @param array|string $args
     *
     * @return self
     * @throws Exception
     */
    public static function factory($args = ''): Starter
    {
        $defaults = [
            // 필수 요소
            //
            // string: 플러그인 메인 파일.
            'mainFile'   => '',

            // string: 플러그인의 네임스페이스.
            'namespace'  => '',

            // string: 플러그인의  버전.
            'version'    => '',

            // 선택 요소
            //
            // null|int|int[]|callable: 블로그 아이디. 싱글 사이트를 위해서는 필요 없음.
            'blogId'     => null,

            // string: 플러그인 접두사. 지정하지 않으면 메인 파일 이름으로부터 추출.
            'prefix'     => null,

            // string: 플러그인 슬러그. 지정하지 않으면 메인 파일 이름으로부터 추출.
            'slug'       => null,

            // string: 텍스트도메인. 지정하지 않으면 메인 파일 이름으로부터 추출.
            'textdomain' => null,
        ];

        $args = wp_parse_args($args, $defaults);

        if (!$args['mainFile']) {
            throw new Exception(__('\'mainFile\' parameter is required.', 'axis3'));
        } elseif (!isset($args['namespace'])) {
            throw new Exception(__('\'namespace\' parameter should be set.', 'axis3'));
        } elseif (!$args['version']) {
            throw new Exception(__('\'version\' parameter is required.', 'axis3'));
        }

        $starter = (new Starter())
            ->addClassFinder(
                (new AutoDiscoverClassFinder())
                    ->setComponentPostfix('Initiator')
                    ->setRootPath(dirname($args['mainFile']) . '/src/Initiators')
                    ->setRootNamespace(trim($args['namespace'], '\\') . '\\Initiators\\')
            )
            ->addClassFinder(
                (new AutoDiscoverClassFinder())
                    ->setComponentPostfix('Model')
                    ->setRootPath(dirname($args['mainFile']) . '/src/Models')
                    ->setRootNamespace(trim($args['namespace'], '\\') . '\\Models\\')
            )
            ->setMainFile($args['mainFile'])
            ->setVersion($args['version']);

        if ($args['blogId']) {
            $starter->setBlogId($args['blogId']);
        }

        if ($args['prefix']) {
            $starter->setPrefix($args['prefix']);
        }

        if ($args['slug']) {
            $starter->setSlug($args['slug']);
        }

        if ($args['textdomain']) {
            $starter->setTextdomain($args['textdomain']);
        }

        return $starter;
    }

    /**
     * 멀티사이트의 경우 개시자 실행 조건을 점검합니다.
     *
     * @return bool
     * @see    Starter::getBlogId()
     */
    protected function checkMultiSiteCondition()
    {
        if (!is_multisite()) {
            return true;
        }

        return (
            null === $this->getBlogId() ||
            get_current_blog_id() == $this->getBlogId() ||
            (is_array($this->getBlogId())) && in_array(get_current_blog_id(), $this->getBlogId()) ||
            (is_callable($this->getBlogId()) && call_user_func($this->getBlogId(), $this))
        );
    }

    /**
     * 전수자의 콘텍스트가 현재 요청 콘텍스트에 어울리는지 파악합니다.
     *
     * - 등록된 요청 콘텍스트가 존재하고, 콜백 함수가 참이면 참을 리턴합니다.
     * - 입력된 콘텍스트가 등록되지 않으면 무조건 참을 리턴합니다.
     *
     * @param string $context
     *
     * @return bool
     */
    protected function checkContext(string $context)
    {
        return (
            !isset(static::$requestContexts[$context]) ||
            call_user_func(static::$requestContexts[$context], $context, $this)
        );
    }

    /**
     * 요청 콘텍스트를 초기화합니다.
     */
    public static function initRequestContexts()
    {
        if (is_null(static::$requestContexts)) {
            static::$requestContexts = [
                // Front: no admin, no cron, but allows ajax
                'Front'       => function () {
                    return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
                },

                // FrontNoAjax, no admin, no cron, no ajax, that means only front area.
                'FrontNoAjax' => function () {
                    return !is_admin() && !defined('DOING_AJAX') && !defined('DOING_CRON');
                },

                // Ajax: only ajax (meaning it is also admin)
                'Ajax'        => function () {
                    return defined('DOING_AJAX') && DOING_AJAX;
                },

                // Admin: is admin
                'Admin'       => function () {
                    return is_admin();
                },

                // Autosave: just doing autosave
                'Autosave'    => function () {
                    return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
                },

                // Cron: just doing cron
                'Cron'        => function () {
                    return defined('DOING_CRON') && DOING_CRON;
                },
            ];
        }
    }
}

Starter::initRequestContexts();
