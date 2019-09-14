<?php

namespace Shoplic\Axis3\Models;

use Shoplic\Axis3\Interfaces\Models\TaxonomyInterface;
use Shoplic\Axis3\Models\FieldHolders\MetaFieldHolderModel;
use WP_Error;

/**
 * Class TaxonomyModel
 *
 * @package Shoplic\Axis3\Models
 * @since   1.0.0
 */
abstract class TaxonomyModel extends MetaFieldHolderModel implements TaxonomyInterface
{
    /**
     * @return void|WP_Error
     */
    public function registerTaxonomy()
    {
        if (!taxonomy_exists(static::getTaxonomy())) {
            $returned = register_taxonomy(
                static::getTaxonomy(),
                $this->getObjectType(),
                $this->getTaxonomyArgs()
            );
            if (is_wp_error($returned)) {
                return $returned;
            }
        }

        return null;
    }

    public function registerMetaFields()
    {
        $this->getAllMetaFields();
    }

    public function activationSetup()
    {
    }

    public function deactivationCleanup()
    {
    }
}
