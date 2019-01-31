<?php
namespace Setka\Editor\API\V1\Errors\Files;

use Setka\Editor\Plugin;
use Setka\Editor\Prototypes\Errors\Error;

class FilesError extends Error
{

    public function __construct()
    {
        $this->setCode(Plugin::_NAME_ . '_api_files_attribute_error');
        $this->setMessage(__('The request from Setka server has missed or not valid data[files] attribute.', Plugin::NAME));
    }
}
