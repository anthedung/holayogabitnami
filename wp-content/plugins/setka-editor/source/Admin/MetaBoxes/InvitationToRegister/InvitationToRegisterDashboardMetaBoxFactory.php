<?php
namespace Setka\Editor\Admin\MetaBoxes\InvitationToRegister;

use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\SignUpPage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class InvitationToRegisterDashboardMetaBoxFactory
 */
class InvitationToRegisterDashboardMetaBoxFactory
{
    /**
     * @param $container ContainerInterface
     *
     * @return InvitationToRegisterDashboardMetaBox
     */
    public static function create($container)
    {
        $metaBox = new InvitationToRegisterDashboardMetaBox();
        $metaBox->setSignUpUrl($container->get(SignUpPage::class)->getURL());
        $metaBox->getView()->setTwigEnvironment($container->get('wp.plugins.setka_editor.twig'));

        return $metaBox;
    }
}
