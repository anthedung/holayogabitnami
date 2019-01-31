<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\Notice;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class YouCanRegisterNotice
 */
class YouCanRegisterNotice extends Notice
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    public function __construct()
    {
        $this->setName(Plugin::NAME . '_you_can_register');
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this
            ->setView(new NoticeSuccessView())
            ->getView()->setCssClasses(array_merge(
                $this->getView()->getCssClasses(),
                array('setka-editor-notice', 'setka-editor-notice-success')
            ));

        $content = __('Create posts with limited functionality in Setka Editor right now — no registration required. Register for a free Setka Editor account to modify your post styles.', Plugin::NAME);

        $this->setContent('<p>' . $content . '</p>');

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isRelevant()
    {
        if($this->setkaEditorAccount->isLoggedIn()) {
            return false;
        }

        $screen = get_current_screen();

        if(Plugin::NAME === $screen->parent_base) {
            return true;
        }

        return false;
    }

    /**
     * @return SetkaEditorAccount
     */
    public function getSetkaEditorAccount()
    {
        return $this->setkaEditorAccount;
    }

    /**
     * @param SetkaEditorAccount $setkaEditorAccount
     *
     * @return $this
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }
}
