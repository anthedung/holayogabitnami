<?php
namespace Setka\Editor\Admin\Options\PlanFeatures;

class Utilities
{

    public static function isWhiteLabelEnabled()
    {
        $settings = new PlanFeaturesOption();

        if($settings->getNode('white_label')->getValue()) {
            return true;
        }
        return false;
    }
}
