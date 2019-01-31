<?php

namespace Setka\Editor\Admin\Options\WhiteLabel;

class Utilities
{

    public static function isWhiteLabelEnabled()
    {
        $option = new WhiteLabelOption();
        $value  = $option->getValue();

        if('1' === $value) {
            return true;
        }

        return false;
    }
}
