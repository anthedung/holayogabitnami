<?php
namespace Setka\Editor\Admin\Ajax;

use Korobochkin\WPKit\AlmostControllers\AbstractAction;
use Korobochkin\WPKit\AlmostControllers\ActionInterface;
use Korobochkin\WPKit\Notices\NoticeInterface;
use Korobochkin\WPKit\Options\OptionInterface;
use Korobochkin\WPKit\PostMeta\PostMetaInterface;
use Korobochkin\WPKit\Transients\TransientInterface;
use Symfony\Component\HttpFoundation\Response;

class DismissNoticesAction extends AbstractAction implements ActionInterface
{
    public function __construct()
    {
        $this
            ->setEnabledForNotLoggedIn(false)
            ->setEnabledForLoggedIn(true);
    }

    /**
     * @inheritdoc
     */
    public function handleRequest()
    {
        if(!current_user_can('manage_options')) {
            $this->getResponse()->setStatusCode(Response::HTTP_UNAUTHORIZED);
            return $this;
        }

        $noticeClass = $this->getRequest()->request->get('noticeClass');

        if(!$noticeClass) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this;
        }

        $notices = $this->container->getParameter('wp.plugins.setka_editor.all_notices');

        $key = array_search($noticeClass, $notices, true);

        if(is_bool($key) || !isset($notices[$key])) {
            $this->getResponse()->setStatusCode(Response::HTTP_NOT_FOUND);
            return $this;
        }

        try {
            /**
             * @var $notice NoticeInterface
             */
            $notice = $this->get($notices[$key]);
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            return $this;
        }

        try {
            $notice->disable();
        } catch (\Exception $exception) {
            $this->getResponse()->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $this;
        }

        $this->getResponse()->setStatusCode(Response::HTTP_OK);
        return $this;
    }
}
