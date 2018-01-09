<?php
namespace Setka\Editor\Admin\Options\Files;

class UseLocalFilesUtilities
{
    public static function useLocal()
    {
        $option = new UseLocalFilesOption();
        if('1' === $option->getValue()) {
            return true;
        }
        return false;
    }
}
