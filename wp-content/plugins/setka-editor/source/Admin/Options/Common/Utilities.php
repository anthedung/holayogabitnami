<?php
namespace Setka\Editor\Admin\Options\Common;

use Setka\Editor\Admin\Options;

class Utilities
{

    /**
     * @return \Setka\Editor\Admin\Prototypes\Options\OptionInterface[]
     */
    public static function getAllOptions()
    {
        $options = array(
            Options\DBVersion\DBVersionOption::class,
            Options\EditorAccessPostTypes\Option::class,
            Options\EditorAccessRoles\Option::class,
            Options\EditorCSS\Option::class,
            Options\EditorJS\Option::class,
            Options\EditorVersion\Option::class,

            Options\Files\FileSyncFailureOption::class,
            Options\Files\FileSyncOption::class,
            Options\Files\FileSyncStageOption::class,
            Options\Files\FilesOption::class,
            Options\Files\UseLocalFilesOption::class,

            Options\PlanFeatures\PlanFeaturesOption::class,
            Options\PublicToken\PublicTokenOption::class,
            Options\SetkaPostCreated\Option::class,
            Options\SubscriptionActiveUntil\Option::class,
            Options\SubscriptionPaymentStatus\Option::class,
            Options\SubscriptionStatus\Option::class,
            Options\ThemePluginsJS\Option::class,
            Options\ThemeResourceCSS\Option::class,
            Options\ThemeResourceCSSLocal\ThemeResourceCSSLocalOption::class,
            Options\ThemeResourceJS\Option::class,
            Options\ThemeResourceJSLocal\ThemeResourceJSLocalOption::class,
            Options\Token\Option::class,
            Options\WhiteLabel\WhiteLabelOption::class
        );

        return $options;
    }

    /**
     * Check if any of our options presented in DB.
     * This is a helper method for plugin Activation.
     *
     * @see \Setka\Editor\Service\Activation::isActivatedFirstTime()
     *
     * @return bool true if any of options founded in DB, false if no options saved in DB.
     */
    public static function isOptionsExistsInDb()
    {
        $options = self::getAllOptions();

        foreach($options as $option) {
            /**
             * @var $option \Setka\Editor\Admin\Prototypes\Options\OptionInterface
             */
            if(class_exists($option)) {
                $option = new $option();
                $value  = $option->getValueRaw();
                if(false !== $value) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Removes all options from DB. This is a helper method for plugin Uninstaller.
     *
     * @see \Setka\Editor\Service\Uninstall::run()
     */
    public static function removeAllOptionsFromDb()
    {
        $options = self::getAllOptions();

        foreach($options as $option) {
            try {
                $option = new $option();
                if(is_a($option, 'Setka\Editor\Admin\Prototypes\Options\OptionInterface')) {
                    /**
                     * @var $option \Setka\Editor\Admin\Prototypes\Options\OptionInterface
                     */
                    $option->delete();
                }
            } catch (\Exception $exception) {
                // Do nothing.
            }
        }
    }
}
