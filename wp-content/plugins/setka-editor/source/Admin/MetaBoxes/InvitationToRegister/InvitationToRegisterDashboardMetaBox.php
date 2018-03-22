<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Korobochkin\WPKit\MetaBoxes\DashboardMetaBox;
use Setka\Editor\Plugin;

/**
 * Class InvitationToRegisterDashboardMetaBox
 */
class InvitationToRegisterDashboardMetaBox extends DashboardMetaBox
{
    /**
     * @var string
     */
    protected $signUpUrl;

    public function __construct()
    {
        $this
            ->setId(Plugin::_NAME_ . '_invitation_to_registerDashboardMetaBox')
            ->setTitle(_x('Setka Editor Registration', 'MetaBox title.', Plugin::NAME))
            ->setView(new InvitationToRegisterView());
    }

    /**
     * @return string
     */
    public function getSignUpUrl()
    {
        return $this->signUpUrl;
    }

    /**
     * @param string $signUpUrl
     *
     * @return $this
     */
    public function setSignUpUrl($signUpUrl)
    {
        $this->signUpUrl = $signUpUrl;
        return $this;
    }
}
