<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\Notice;
use Setka\Editor\Plugin;

/**
 * Class SetkaEditorThemeDisabledNotice
 */
class SetkaEditorThemeDisabledNotice extends Notice
{
    public function __construct()
    {
        $this->setName(Plugin::NAME . '-setka-editor-theme-disabled');
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this
            ->setView(new NoticeErrorView())
            ->getView()->setCssClasses(array_merge(
                $this->getView()->getCssClasses(),
                array('setka-editor-notice', ' setka-editor-notice-error', 'hidden')
            ));

        $content = __('This post uses a disabled style. You can safely edit this post but if you change the style you won’t be able to switch it back.', Plugin::NAME);
        $content = '<p>' . $content . '</p>';

        $this->setContent($content);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isRelevant()
    {
        $screen = get_current_screen();

        if (method_exists($screen, 'is_block_editor') && $screen->is_block_editor()) {
            return false;
        }

        if('post' === $screen->base) {
            return true;
        }

        return false;
    }
}
