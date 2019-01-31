<?php
namespace Setka\Editor\Service;

use Setka\Editor\Admin\Options\PlanFeatures\PlanFeaturesOption;
use Setka\Editor\Admin\Options\WhiteLabelUtilities;
use Setka\Editor\PostMetas\UseEditorPostMeta;

/**
 * Class WhiteLabel
 */
class WhiteLabel
{
    /**
     * Add white label.
     *
     * @param $content string Post content.
     *
     * @throws \Exception
     *
     * @return string Post content with white label.
     */
    public function addLabel($content)
    {
        if(WhiteLabelUtilities::isWhiteLabelEnabled() && !is_admin()) {
            $useEditorPostMeta = new UseEditorPostMeta();
            $useEditorPostMeta->setPostId(get_the_ID());
            if($useEditorPostMeta->get()) {
                $whiteLabel      = new PlanFeaturesOption();
                $whiteLabelValue = $whiteLabel->get();

                $content .= $whiteLabelValue['white_label_html'];
            }
        }
        return $content;
    }
}
