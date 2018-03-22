<?php
namespace Setka\Editor\Admin\Service\SetkaAPI\Errors;

use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Error;

class InvalidRequestDataError extends Error
{

    public function __construct()
    {
        parent::__construct(
            Plugin::_NAME_ . '_setka_api_invalid_request_data',
            __('Invalid request data.', Plugin::NAME)
        );
    }
}
