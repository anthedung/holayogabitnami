<?php
namespace Setka\Editor\Admin\Options\EditorAccessPostTypes;

class Utilities
{

    public static function isEditorEnabledForPostType($post_type)
    {
        if(is_string($post_type) && !empty($post_type)) {
            try {
                $option = new Option();

                return in_array(
                    $post_type,
                    $option->getValue(),
                    true
                );
            }
            catch(\Exception $exception) {
                return false;
            }
        }
        return false;
    }
}
