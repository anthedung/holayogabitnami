<?php
namespace Setka\Editor\Admin\Options\Files;

use Setka\Editor\Admin\Prototypes;
use Setka\Editor\Plugin;
use Symfony\Component\Validator\Constraints;

/**
 * Indicates a current state of files sync.
 *
 * There are multiple states (order matters):
 *
 * 1. download_files_list. Download list of files from API and save it.
 * 2. create_entries. Create entries in DB for each item in the list (see 1.).
 * 3. download_files. Download all files.
 * 4. generate_editor_config. Generate Editor config.
 * 5. switch_to_local_usage. Switch to local usage.
 *
 * If you switch to 1. we also need to disable local usage.
 */
class FileSyncStageOption extends Prototypes\Options\AbstractOption {

    // 1 //
    const DOWNLOAD_FILES_LIST = 'download_files_list';

    // 2 //
    const CREATE_ENTRIES = 'create_entries';

    // 3 //
    const DOWNLOAD_FILES = 'download_files';

    // 4 //
    const GENERATE_EDITOR_CONFIG = 'generate_editor_config';

    // 5 //
    const OK = 'ok';

    public function __construct() {
        parent::__construct(Plugin::_NAME_ . '_file_sync_stage', '');
        $this->setDefaultValue('download_files_list');
    }

    public function buildConstraint() {
        return array(
            new Constraints\NotNull(),
            new Constraints\Type(array(
                'type' => 'string',
            )),
            new Constraints\Choice(array(
                'choices' => array(
                    'download_files_list',
                    'create_entries',
                    'download_files',
                    'generate_editor_config',
                    'ok',
                ),
                'multiple' => false,
                'strict' => true,
            )),
        );
    }

    public function sanitize($instance) {
        if($this->validateValue($instance)) {
            return $instance;
        }
        return $this->getDefaultValue();
    }
}
