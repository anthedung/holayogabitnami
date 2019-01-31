<?php
namespace Setka\Editor\Admin\Service\SetkaEditorAPI;

use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class APIFactory
 */
class APIFactory
{
    /**
     * Build API instance and setup all required instances for it.
     *
     * @param $validator ValidatorInterface
     * @param $pluginVersion string
     * @param $endpoint string
     * @param $basicAuthLogin string|false
     * @param $basicAuthPassword string|false
     *
     * @return API API instance.
     */
    public static function create(
        ValidatorInterface $validator,
        $pluginVersion,
        $endpoint = Endpoints::API,
        $basicAuthLogin = false,
        $basicAuthPassword = false
    ) {
        global $wp_version;

        $options = array(
            'app_version' => $wp_version,
            'plugin_version' => $pluginVersion,
            'domain' => get_site_url(),
            'endpoint' => $endpoint,
            'basic_auth_login' => $basicAuthLogin,
            'basic_auth_password' => $basicAuthPassword,
        );

        $api = new API($options);

        $api->setValidator($validator)
            ->setClient(new WordPressClient());

        return $api;
    }
}
