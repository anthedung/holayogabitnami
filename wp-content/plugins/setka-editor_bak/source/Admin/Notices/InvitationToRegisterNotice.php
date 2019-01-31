<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\Notice;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class InvitationToRegisterNotice
 */
class InvitationToRegisterNotice extends Notice
{
    /**
     * @var string
     */
    protected $signUpPageUrl;

    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    public function __construct()
    {
        $this->setName(Plugin::NAME . '_invitation_to_register');
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

        $content = sprintf(
            /* translators: Notice message in notice showed after plugin activation. %1$s - plugin settings page where you can create a new account. */
            __('<a href="%1$s" target="_blank">Register a Setka Editor account</a> to create your own post style with branded fonts, colors and other visual elements and customized grid system.', Plugin::NAME),
            esc_url($this->getSignUpPageUrl())
        );

        $this->setContent('<p>' . $content . '</p>');

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isRelevant()
    {
        if(!current_user_can('manage_options')) {
            return false;
        }

        if($this->setkaEditorAccount->isLoggedIn()) {
            return false;
        }

        $screen = get_current_screen();

        if('post' === $screen->id && 'add' === $screen->action) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getSignUpPageUrl()
    {
        return $this->signUpPageUrl;
    }

    /**
     * @param string $signUpPageUrl
     *
     * @return $this
     */
    public function setSignUpPageUrl($signUpPageUrl)
    {
        $this->signUpPageUrl = $signUpPageUrl;
        return $this;
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
