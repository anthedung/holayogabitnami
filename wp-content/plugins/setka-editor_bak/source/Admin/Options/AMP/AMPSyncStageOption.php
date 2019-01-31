<?php
namespace Setka\Editor\Admin\Options\AMP;

use Korobochkin\WPKit\Options\AbstractOption;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

class AMPSyncStageOption extends AbstractOption
{
    // 1 //
    const PREPARE_CONFIG = 'prepare_config';

    // 2 //
    const RESET_PREVIOUS_STATE = 'reset_previous_state';

    // 3 //
    const CREATE_ENTRIES = 'create_entries';

    // 4 //
    const REMOVE_OLD_ENTRIES = 'remove_old_entries';

    // 5 //
    const DOWNLOAD_FILES = 'download_files';

    // 6 //
    const OK = 'ok';

    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_amp_sync_stage')
            ->setDefaultValue(self::PREPARE_CONFIG);
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return array(
            new Constraints\Type(array(
                'type' => 'string',
            )),
            new Constraints\Choice(array(
                'choices' => array(
                    'reset_download_attempts_counters',
                    'create_entries',
                    'download_files',
                    'ok',
                ),
                'multiple' => false,
                'strict' => true,
            )),
        );
    }
}
