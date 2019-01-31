<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\Notice;
use Setka\Editor\Plugin;

/**
 * Class SetkaEditorCantFindResourcesNotice
 */
class SetkaEditorCantFindResourcesNotice extends Notice
{
    public function __construct()
    {
        $this
            ->setName(Plugin::NAME . '-setka-editor-cant-find-resources');
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

        $content = __('Post Style or Grid System was removed from Style Manager or you’ve changed your license key. Please contact <a href="https://editor.setka.io/support" target="_blank">Setka Editor team</a>.', Plugin::NAME);
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
