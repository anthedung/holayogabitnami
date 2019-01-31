<?php
namespace Setka\Editor\Admin\Options;

/**
 * Class WhiteLabelUtilities
 */
class WhiteLabelUtilities
{
    /**
     * @return bool
     */
    public static function isWhiteLabelEnabled()
    {
        $option = new WhiteLabelOption();
        $value  = $option->get();

        if($value) {
            return true;
        }

        return false;
    }
}
