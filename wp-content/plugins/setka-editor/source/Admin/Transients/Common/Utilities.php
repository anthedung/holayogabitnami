<?php
namespace Setka\Editor\Admin\Transients\Common;

use Setka\Editor\Admin\Transients;

class Utilities
{

    /**
     * @return \Setka\Editor\Admin\Prototypes\Transients\TransientInterface[]
     */
    public static function getAllTransients()
    {
        $transients = array(
            Transients\AfterSignInNotice\Transient::class,
            Transients\SettingsErrors\Transient::class,
            Transients\SettingsToken\Transient::class,
        );

        return $transients;
    }

    /**
     * Removes all transients from DB. This is a helper method for plugin Uninstaller.
     * Technically transients can be stored not in DB if your site using object cache.
     *
     * @see \Setka\Editor\Service\Uninstall::run()
     */
    public static function removeAllTransientsFromDb()
    {
        $transients = self::getAllTransients();

        try {
            foreach($transients as $transient) {
                $transient = new $transient();
                if(is_a($transient, 'Setka\Editor\Admin\Prototypes\Transients\TransientInterface')) {
                    /**
                     * @var $transient \Setka\Editor\Admin\Prototypes\Transients\TransientInterface
                     */
                    $transient->delete();
                }
            }
        } catch (\Exception $exception) {
            // Do nothing.
        }
    }
}
