<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Pages\PageInterface;
use Setka\Editor\Admin\Prototypes\Notices\SuccessNotice;
use Setka\Editor\Plugin;
use Setka\Editor\Admin;
use Setka\Editor\Service\SetkaAccount\Account;

class InvitationToRegisterNotice extends SuccessNotice
{
    /**
     * @var string
     */
    protected $signUpPageUrl;

    public function __construct()
    {
        parent::__construct(Plugin::NAME, 'invitation_to_register');
    }

    public function lateConstruct()
    {
        parent::lateConstruct();

        $content = sprintf(
            /* translators: Notice message in notice showed after plugin activation. %1$s - plugin settings page where you can create a new account. */
            __('<a href="%1$s" target="_blank">Register a Setka Editor account</a> to create your own post style with branded fonts, colors and other visual elements and customized grid system.', Plugin::NAME),
            esc_url($this->getSignUpPageUrl())
        );

        $this->setContent('<p>' . $content . '</p>');
    }

    public function isRelevant()
    {
        if(!current_user_can('manage_options')) {
            return false;
        }

        if(Account::isLoggedIn()) {
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
    public function setSignUpPage($signUpPageUrl)
    {
        $this->signUpPageUrl = $signUpPageUrl;
        return $this;
    }
}
