<?php
namespace Setka\Editor\Admin\Notices;

use Setka\Editor\Admin\Prototypes\Notices\NoticeInterface;

/**
 * Class NoticesStack
 */
class NoticesStack
{
    /**
     * @var NoticeInterface[]
     */
    protected $notices = array();

    /**
     * @return NoticeInterface[]
     */
    public function getNotices()
    {
        return $this->notices;
    }

    /**
     * @param NoticeInterface[] $notices
     */
    public function setNotices(array $notices)
    {
        $this->notices = $notices;
        return $this;
    }

    public function addNotice(NoticeInterface $notice)
    {
        $this->notices[] = $notice;
        return $this;
    }

    /**
     * @return $this
     */
    public function render()
    {
        foreach ($this->notices as $notice) {
            if ($notice->isRelevant()) {
                $notice->lateConstruct();
                $notice->render();
            }
        }
        return $this;
    }
}
