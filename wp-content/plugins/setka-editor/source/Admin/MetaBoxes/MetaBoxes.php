<?php
namespace Setka\Editor\Admin\MetaBoxes;

use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterMetaBox;
use Setka\Editor\Admin\Prototypes\MetaBoxes\MetaBoxInterface;
use Setka\Editor\Service\SetkaAccount\Account;

/**
 * Class MetaBoxes
 */
class MetaBoxes
{
    /**
     * @var MetaBoxInterface[]
     */
    protected static $metaBoxes = array();

    public static function run()
    {
        self::initializeMetaBoxes();
        self::register();
    }

    public static function initializeMetaBoxes()
    {
        if(!Account::is_logged_in()) {
            self::$metaBoxes[] = new InvitationToRegisterMetaBox();
        }

        $metaBoxesAssoc = array();

        foreach (self::$metaBoxes as $metaBox) {
            $metaBoxesAssoc[$metaBox->getId()] = $metaBox;
        }
        self::$metaBoxes = $metaBoxesAssoc;
    }

    public static function register()
    {
        foreach (self::$metaBoxes as $metaBox) {
            $metaBox->register();
        }
    }
}
