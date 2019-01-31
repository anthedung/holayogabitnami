<?php
namespace Setka\Editor\Admin\Service;

class EditorExpand
{

    /**
     * Fix WordPress editor-expand logic on auto init Setka Editor.
     * By default WordPress check `wp-editor-expand` class and if it exists
     * then WP runs the adjust() in loop (about 6 times) method which is setup
     * some styles for DIVs.
     *
     * Setka Editor does't need this stuffs and we disable this as we can.
     *
     * Checkout more info in wp-admin/js/editor-expand.js from 704 line (init section).
     *
     * @see \Setka\Editor\Plugin::runAdmin()
     *
     * @since 1.0.4
     *
     * @return bool False if we don't need editorExpand stuff, default value otherwise.
     */
    public static function wpEditorExpand($expand)
    {
        if(Editor::enableEditor() && Editor::isAutoInitEnabled()) {
            return false;
        }
        return $expand;
    }
}
