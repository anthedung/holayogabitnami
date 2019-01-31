<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\Notice;
use Setka\Editor\Plugin;

/**
 * Class SignUpErrorNotice
 */
class SignUpErrorNotice extends Notice
{
    public function __construct()
    {
        $this->setName(Plugin::NAME . 'sign_up_error');
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
                array('setka-editor-notice', 'setka-editor-notice-error')
            ));

        $this->setContent('<p>' . __('Oops… Couldn’t connect to Setka Editor server to complete your registration. Please contact Setka Editor support team <a href="mailto:helpme@setka.io">helpme@setka.io</a>.', Plugin::NAME) . '</p>');

        return $this;
    }
}
