<?php
namespace Setka\Editor\Admin\Notices;

use Setka\Editor\Admin\Prototypes\Notices\SuccessNotice;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\Account;

class YouCanRegisterNotice extends SuccessNotice
{
    public function __construct()
    {
        parent::__construct(Plugin::NAME, 'you_can_register');
    }

    public function lateConstruct()
    {
        parent::lateConstruct();

        $content = __('Create posts with limited functionality in Setka Editor right now — no registration required. Register for a free Setka Editor account to modify your post styles.', Plugin::NAME);

        $this->setContent('<p>' . $content . '</p>');
    }

    public function isRelevant()
    {
        if(Account::isLoggedIn()) {
            return false;
        }

        $screen = get_current_screen();

        if(Plugin::NAME === $screen->parent_base) {
            return true;
        }

        return false;
    }
}
