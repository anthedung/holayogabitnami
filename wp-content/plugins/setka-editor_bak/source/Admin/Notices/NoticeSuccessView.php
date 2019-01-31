<?php
namespace Setka\Editor\Admin\Notices;

class NoticeSuccessView extends \Korobochkin\WPKit\Notices\NoticeSuccessView
{
    /**
     * @return array
     */
    public function getCssClasses()
    {
        return $this->cssClasses;
    }

    /**
     * @param array $cssClasses
     * @return $this
     */
    public function setCssClasses(array $cssClasses)
    {
        $this->cssClasses = $cssClasses;
        return $this;
    }
}
