<?php
namespace Setka\Editor\Admin\Notices\InvitationToRegister;

use Setka\Editor\Admin\Prototypes\Notices\SuccessNotice;
use Setka\Editor\Plugin;
use Setka\Editor\Admin;
use Setka\Editor\Service\SetkaAccount\Account;

class InvitationToRegisterNotice extends SuccessNotice
{
    public function __construct(){
        parent::__construct(Plugin::NAME, 'invitation_to_register');
    }

    public function lateConstruct() {
        parent::lateConstruct();

        if(isset(Admin\Pages\Settings\Loader::$pages[Plugin::NAME])) {
            $url = Admin\Pages\Settings\Loader::$pages[Plugin::NAME]->getURL();
        } else {
            $url = '';
        }

        $content = sprintf(
            /* translators: Notice message in notice showed after plugin activation. %1$s - plugin settings page where you can create a new account. */
            __('<a href="%1$s" target="_blank">Register a Setka Editor account</a> to create your own post style with branded fonts, colors and other visual elements and customized grid system.', Plugin::NAME),
            esc_url($url)
        );

        $this->setContent('<p>' . $content . '</p>');
    }

    public function isRelevant() {
        if(!current_user_can('manage_options')) {
            return false;
        }

        if(Account::is_logged_in()) {
            return false;
        }

        $screen = get_current_screen();

        if($screen->id === 'post' && $screen->action === 'add') {
            return true;
        }

        return false;
    }
}
