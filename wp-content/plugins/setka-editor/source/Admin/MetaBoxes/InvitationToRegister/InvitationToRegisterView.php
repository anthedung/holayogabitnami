<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Setka\Editor\Admin;
use Setka\Editor\Admin\Prototypes\MetaBoxes\MetaBoxInterface;
use Setka\Editor\Admin\Prototypes\MetaBoxes\TwigMetaBoxView;
use Setka\Editor\Plugin;

class InvitationToRegisterView extends TwigMetaBoxView
{
    /**
     * @inheritdoc
     */
    public function render(MetaBoxInterface $metaBox)
    {
        if(isset(Admin\Pages\Settings\Loader::$pages[Plugin::NAME])) {
            $url = Admin\Pages\Settings\Loader::$pages[Plugin::NAME]->getURL();
        } else {
            $url = '';
        }

        $content = sprintf(
            /* translators: %1$s - plugin settings page where you can create a new account. */
            __('<a href="%1$s" target="_blank">Register a Setka Editor account</a> to create your own post style with branded fonts, colors and other visual elements and customized grid system.', Plugin::NAME),
            esc_url($url)
        );

        echo '<p>' . $content . '</p>';
    }
}
