<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Korobochkin\WPKit\MetaBoxes\MetaBox;
use Setka\Editor\Plugin;

/**
 * Class InvitationToRegisterMetaBox
 */
class InvitationToRegisterMetaBox extends MetaBox
{
    /**
     * @var string
     */
    protected $signUpUrl;

    public function __construct()
    {
        $this
            ->setId(Plugin::_NAME_.'_invitation_to_registerMetaBox')
            ->setTitle(_x('Setka Editor Registration', 'MetaBox title.', Plugin::NAME))
            ->setContext('side')
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
