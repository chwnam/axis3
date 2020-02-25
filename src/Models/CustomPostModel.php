<?php

namespace Shoplic\Axis3\Models;

use Shoplic\Axis3\Interfaces\Models\CustomPostModelInterface;
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

    public function getPrimitiveCapabilities()
    {
        $object = get_post_type_object(static::getPostType());

        if ($object) {
            return array_intersect_key(
                (array)$object->cap,
                [
                    'delete_others_posts'    => '',
                    'delete_posts'           => '',
                    'delete_private_posts'   => '',
                    'delete_published_posts' => '',
                    'edit_others_posts'      => '',
                    'edit_posts'             => '',
                    'edit_private_posts'     => '',
                    'edit_published_posts'   => '',
                    'read_private_posts'     => '',
                    'publish_posts'          => '',
                ]
            );
        }

        return null;
    }
}
