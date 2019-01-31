<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\NoticeErrorView;
use Korobochkin\WPKit\Notices\NoticeInterface;
use Korobochkin\WPKit\Notices\NoticesStackInterface;
use Korobochkin\WPKit\Notices\NoticeSuccessView;
use Setka\Editor\Admin\Notices\NoticeSuccessView as SetkaNoticeSuccessView;
use Setka\Editor\Admin\Notices\NoticeErrorView as SetkaNoticeErrorView;

class NoticesStack extends \Korobochkin\WPKit\Notices\NoticesStack implements NoticesStackInterface
{
    /**
     * @var boolean
     */
    protected $gutenbergSupport;

    /**
     * NoticesStack constructor.
     *
     * @param bool $gutenbergSupport
     */
    public function __construct($gutenbergSupport)
    {
        $this->gutenbergSupport = $gutenbergSupport;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $screen = get_current_screen();
        if($this->gutenbergSupport && $screen->is_block_editor()) {
            return;
        }
        parent::run();
    }

    /**
     * Return all relevant notices as array which can be useful in JS.
     *
     * @return array All notices info.
     */
    public function getNoticesAsArray()
    {
        /**
         * @var $notice NoticeInterface
         */
        $notices = array();
        foreach ($this->notices as $notice) {
            if($notice->isRelevant()) {
                $a = array(
                    'name' => $notice->getName(),
                    'content' => $notice->lateConstruct()->getContent(),
                    'class' => get_class($notice),
                    'relevant' => true,
                    'isDismissible' => true,
                );

                switch (get_class($notice->getView())) {
                    case NoticeSuccessView::class:
                    case SetkaNoticeSuccessView::class:
                        $status = 'success';
                        break;

                    case NoticeErrorView::class:
                    case SetkaNoticeErrorView::class:
                        $status = 'error';
                        break;

                    default:
                        $status = 'info';
                        break;
                }

                $a['status'] = $status;

                $notices[] = $a;
            }
        }
        return $notices;
    }
}
