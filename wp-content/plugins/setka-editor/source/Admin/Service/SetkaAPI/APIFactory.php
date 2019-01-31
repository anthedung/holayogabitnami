<?php
namespace Setka\Editor\Admin\Service\SetkaAPI;

use Symfony\Component\Validator\Validation;

class APIFactory
{

    /**
     * Build API instance and setup all required instances for it.
     *
     * @return API API instance.
     */
    public static function create()
    {
        $options = array();

        if(defined('SETKA_EDITOR_DEBUG') && SETKA_EDITOR_DEBUG === true) {
            $options['endpoint'] = Endpoints::API_DEV;
        } else {
            $options['endpoint'] = Endpoints::API;
        }

        if(defined('SETKA_EDITOR_API_BASIC_AUTH_USERNAME')) {
            $options['basic_auth_login'] = SETKA_EDITOR_API_BASIC_AUTH_USERNAME;
        }

        if(defined('SETKA_EDITOR_API_BASIC_AUTH_PASSWORD')) {
            $options['basic_auth_password'] = SETKA_EDITOR_API_BASIC_AUTH_PASSWORD;
        }

        $api = new API($options);

        $api->setValidator(Validation::createValidator())
            ->setClient(new WordPressClient());

        return $api;
    }
}
