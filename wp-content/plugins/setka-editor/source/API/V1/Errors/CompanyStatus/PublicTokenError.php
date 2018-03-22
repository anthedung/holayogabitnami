<?php
namespace Setka\Editor\API\V1\Errors\CompanyStatus;

use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Error;

class PublicTokenError extends Error
{

    public function __construct()
    {
        $this->setCode(Plugin::_NAME_ . '_api_company_status_public_token_error');
        $this->setMessage(__('The request from Setka server missed `public_token` attribute or it has not valid format.', Plugin::NAME));
    }
}
