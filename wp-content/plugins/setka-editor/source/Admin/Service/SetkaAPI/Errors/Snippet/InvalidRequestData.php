<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Errors\Snippet;

use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Error;

class InvalidRequestData extends Error {

	public function __construct() {
	    parent::__construct(
            Plugin::_NAME_ . '_setka_api_snippet_invalid_request_data',
            __('Snippet name or snippet body can’t be blank', Plugin::NAME)
        );
	}
}
