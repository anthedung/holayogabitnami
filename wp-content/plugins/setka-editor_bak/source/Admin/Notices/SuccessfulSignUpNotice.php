<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\Notice;
use Setka\Editor\Plugin;

/**
 * Class SuccessfulSignUpNotice
 */
class SuccessfulSignUpNotice extends Notice
{
    public function __construct()
    {
        $this->setName(Plugin::NAME . 'successful_sign_up');
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

        $this->setContent('<p>' . __('Registration completed. We sent you an email with license key that you need to enter to start the plugin.', Plugin::NAME) . '</p>');

        return $this;
    }
}
