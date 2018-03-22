<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Pages\PageInterface;
use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\SignUpPage;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class InvitationToRegisterNoticeFactory
 */
class InvitationToRegisterNoticeFactory
{
    /**
     * @param $container Container
     */
    public static function create($container)
    {
        /**
         * @var $signUpPage PageInterface
         */
        $notice     = new InvitationToRegisterNotice();
        $signUpPage = $container->get(SignUpPage::class);
        $notice->setSignUpPage($signUpPage->getURL());

        return $notice;
    }
}
