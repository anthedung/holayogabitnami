<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Korobochkin\WPKit\MetaBoxes\MetaBoxInterface;
use Korobochkin\WPKit\MetaBoxes\MetaBoxTwigView;
use Setka\Editor\Admin;
use Setka\Editor\Plugin;

/**
 * Class InvitationToRegisterView
 */
class InvitationToRegisterView extends MetaBoxTwigView
{
    /**
     * @inheritdoc
     */
    public function render(MetaBoxInterface $metaBox)
    {
        /**
         * @var $metaBox InvitationToRegisterDashboardMetaBox|InvitationToRegisterMetaBox
         */
        $url = $metaBox->getSignUpUrl();

        $content = sprintf(
            /* translators: %1$s - plugin settings page where you can create a new account. */
            __('<a href="%1$s" target="_blank">Register a Setka Editor account</a> to create your own post style with branded fonts, colors and other visual elements and customized grid system.', Plugin::NAME),
            esc_url($url)
        );

        echo '<p>' . $content . '</p>'; // WPCS: XSS ok.
    }
}
