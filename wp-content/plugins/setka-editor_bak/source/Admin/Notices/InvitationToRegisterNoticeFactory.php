<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Pages\PageInterface;
use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\SignUpPage;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class InvitationToRegisterNoticeFactory
 */
class InvitationToRegisterNoticeFactory
{
    /**
     * @param $container ContainerInterface
     */
    public static function create(ContainerInterface $container)
    {
        /**
         * @var $signUpPage PageInterface
         */
        $notice     = new InvitationToRegisterNotice();
        $signUpPage = $container->get(SignUpPage::class);
        $notice
            ->setSignUpPageUrl($signUpPage->getURL())
            ->setSetkaEditorAccount($container->get(SetkaEditorAccount::class));

        return $notice;
    }
}
