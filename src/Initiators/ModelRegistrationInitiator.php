<?php

namespace Shoplic\Axis3\Initiators;

use Shoplic\Axis3\Interfaces\Models\CustomPostModelInterface;
use Shoplic\Axis3\Interfaces\Models\ModelInterface;
use Shoplic\Axis3\Interfaces\Models\RolesCapsInterface;
use Shoplic\Axis3\Interfaces\Models\SettingsModelInterface;
use Shoplic\Axis3\Interfaces\Models\TaxonomyInterface;

use function Shoplic\Axis3\Functions\requestFlushRewrite;

/**
 * Class ModelRegistrationInitiator
 *
 * 프레임워크 자체가 가지고 있는 번들 개시자.
 * 프로젝트에서 검출되는 모델 클래스 중 자동 등록 가능한 모델은 등록시킵니다.
 *
 * @package Shoplic\Axis3\Initiators
 * @since   1.0.0
 */
class ModelRegistrationInitiator extends BaseInitiator
{
    private $modelClasses = [];

    public function initHooks()
    {
        add_action('init', [$this, 'registerObjects']);

        register_activation_hook($this->getStarter()->getMainFile(), [$this, 'activationSetup']);
        register_deactivation_hook($this->getStarter()->getMainFile(), [$this, 'deactivationCleanup']);
    }

    public function getModelClasses()
    {
        return $this->modelClasses;
    }

    public function setModelClasses($modelClasses)
    {
        $this->modelClasses = $modelClasses;
    }

    /**
     * @callback
     * @action      init
     * @used-by     ModelRegistrationInitiator::initHooks()
     */
    public function registerObjects()
    {
        foreach ($this->getModelClasses() as $context => $modelClasses) {
            foreach ($modelClasses as $modelClass) {
                $implemented = class_implements($modelClass);
                if (isset($implemented[CustomPostModelInterface::class])) {
                    /** @var CustomPostModelInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->registerPostType();
                    $instance->registerMetaFields();
                } elseif (isset($implemented[TaxonomyInterface::class])) {
                    /** @var TaxonomyInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->registerTaxonomy();
                    $instance->registerMetaFields();
                } elseif (isset($implemented[SettingsModelInterface::class])) {
                    /** @var SettingsModelInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->registerSettings();
                }
            }
        }
    }

    public function activationSetup()
    {
        $this->registerObjects();
        foreach ($this->getModelClasses() as $modelClasses) {
            foreach ($modelClasses as $modelClass) {
                $implemented = class_implements($modelClass);
                if (
                    isset($implemented[CustomPostModelInterface::class]) ||
                    isset($implemented[TaxonomyInterface::class]) ||
                    isset($implemented[RolesCapsInterface::class]) ||
                    method_exists($modelClass, 'activationSetup')
                ) {
                    /** @var CustomPostModelInterface|TaxonomyInterface|RolesCapsInterface|ModelInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->activationSetup();
                }
            }
        }
        requestFlushRewrite();
    }

    public function deactivationCleanup()
    {
        foreach ($this->getModelClasses() as $modelClasses) {
            foreach ($modelClasses as $modelClass) {
                $implemented = class_implements($modelClass);
                if (isset($implemented[CustomPostModelInterface::class])) {
                    /** @var CustomPostModelInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->deactivationCleanup();
                    unregister_post_type($instance->getPostType());
                } elseif (isset($implemented[TaxonomyInterface::class])) {
                    /** @var TaxonomyInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->deactivationCleanup();
                    unregister_taxonomy($instance->getTaxonomy());
                } elseif (isset($implemented[RolesCapsInterface::class])) {
                    /** @var RolesCapsInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->deactivationCleanup();
                } elseif (method_exists($modelClass, 'deactivationCleanup')) {
                    /** @var ModelInterface $instance */
                    $instance = $this->claimModel($modelClass);
                    $instance->deactivationCleanup();
                }
            }
        }
        requestFlushRewrite();
    }
}
