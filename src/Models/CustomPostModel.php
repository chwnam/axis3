<?php

namespace Shoplic\Axis3\Models;

use Shoplic\Axis3\Interfaces\Models\CustomPostModelInterface;
use Shoplic\Axis3\Interfaces\Models\FieldModels\MetaFieldModelInterface;
use Shoplic\Axis3\Models\FieldHolders\MetaFieldHolderModel;
use Shoplic\Axis3\Models\FieldModels\MetaFieldModel;

/**
 * Class CustomPostModel
 *
 * 커스텀 포스트 모델입니다.
 *
 * @package Shoplic\Axis3\Models\FieldHolders
 * @since   1.0.0
 */
abstract class CustomPostModel extends MetaFieldHolderModel implements CustomPostModelInterface
{
    public function registerPostType()
    {
        if (post_type_exists(static::getPostType())) {
            $postTypeObject = get_post_type_object(static::getPostType());
        } else {
            $postTypeObject = register_post_type(static::getPostType(), $this->getPostTypeArgs());
        }

        return $postTypeObject;
    }

    public function saveFromRequest($postId, $post, $updated, &$request)
    {
        if ($post->post_status !== 'trash') {
            foreach ($this->getAllMetaFields() as $metaField) {
                $metaField->saveFromRequest($postId, $request);
            }
        }
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

    protected function getMetaFieldArgs(callable $argCallback)
    {
        $args = call_user_func($argCallback, $this);

        $args['objectType']    = MetaFieldModel::OBJECT_TYPE_POST;
        $args['objectSubtype'] = static::getPostType();

        return $args;
    }
}
