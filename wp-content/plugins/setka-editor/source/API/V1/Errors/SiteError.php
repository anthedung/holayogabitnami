<?php
namespace Setka\Editor\API\V1\Errors;

use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Error;

class SiteError extends Error {

	public function __construct() {
		$this->setCode(Plugin::_NAME_ . '_api_site_error');
		$this->setMessage(__('Site is experiencing technical issues while handling request.', Plugin::NAME));
	}
}
