<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Errors\GetFiles;

use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Error;

class InvalidFileError extends Error {

	public function __construct() {
	    parent::__construct(
	        Plugin::_NAME_ . '_setka_api_get_files_invalid_file_error',
            __('One of file objects is invalid.', Plugin::NAME)
        );
	}
}
