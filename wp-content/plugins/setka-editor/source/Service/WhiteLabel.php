<?php
namespace Setka\Editor\Service;

use Setka\Editor\Admin\Options\PlanFeatures\PlanFeaturesOption;
use Setka\Editor\Admin\Options\WhiteLabel\Utilities;
use Setka\Editor\Entries\Meta;

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
        if(Utilities::isWhiteLabelEnabled() && !is_admin()) {
            $useEditorMeta = new Meta\UseEditorMeta();
            $useEditorMeta->setPostId(get_the_ID());
            if($useEditorMeta->getValue() === '1') {
                $whiteLabel = new PlanFeaturesOption();
                $content   .= $whiteLabel->getNode('white_label_html')->getValue();
            }
        }
        return $content;
    }
}
