<?php
namespace Setka\Editor\Admin\Notices;

use Korobochkin\WPKit\Notices\NoticeInterface;

class NoticeErrorView extends \Korobochkin\WPKit\Notices\NoticeErrorView
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

    /**
     * @inheritdoc
     */
    public function render(NoticeInterface $notice)
    {
        $this->notice = $notice;
        $this->prepareCssClasses();

        if ($notice->getTitle()) {
            $title = '<p class="notice-title">' . $notice->getTitle() . '</p>';
        } else {
            $title = '';
        }

        $cssClasses = implode(' ', $this->cssClasses);

        $id = 'wp-kit-notice-' . $notice->getName();

        printf(
            '<div class="%1$s" id="%3$s" data-notice-class="%4$s">%2$s</div>',
            esc_attr($cssClasses),
            $title . $notice->getContent(),
            esc_attr($id),
            esc_attr(get_class($notice))
        ); // WPCS: XSS, ok.
    }
}
