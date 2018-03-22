<?php
namespace Setka\Editor\Service\SetkaAccount;

use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Admin\Service\SetkaAPI;
use Setka\Editor\Admin\Service\SetkaAPI\Actions;
use Setka\Editor\Admin\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\Config\PluginConfig;
use Symfony\Component\HttpFoundation\Response;

class SignIn
{

    /**
     * This function used for auth via Settings pages (WordPress automatically update and save Token).
     *
     * @param $token string New token to use.
     * @param $update_token bool Should this function insert this token to DB or not. By default token updated in DB
     * by WordPress (runned from Settings page). But if you are doing auth from script you should pass true (default).
     *
     * @return \Setka\Editor\Admin\Service\SetkaAPI\Prototypes\ActionInterface[]
     */
    public static function signInByToken($token, $update_token = true)
    {

        $responses = self::sendAuthRequests($token);

        foreach($responses as $response) {
            /**
             * @var $response \Setka\Editor\Admin\Service\SetkaAPI\Prototypes\ActionAbstract
             */
            if($response->getErrors()->hasErrors()) {
                return $responses;
            }
        }
        unset($response);

        // Setup new account settings
        if($update_token) {
            self::setupToken($token);
        }

        self::setupNewAccount($responses[Actions\GetCurrentThemeAction::class], $responses[Actions\GetCompanyStatusAction::class]);

        return $responses;
    }

    /**
     * Send auth requests and return actions with validated responses.
     *
     * @param $token string Company token (license key).
     *
     * @return array Executed actions
     */
    public static function sendAuthRequests($token)
    {
        // API (request token details on Setka Server via API)
        $api = SetkaAPI\APIFactory::create();
        $api->setAuthCredits(new SetkaAPI\AuthCredits($token));

        // Theme files
        $currentTheme = new Actions\GetCurrentThemeAction();
        $api->request($currentTheme);

        // Request for details of subscription
        $companyStatus = new Actions\GetCompanyStatusAction();
        $api->request($companyStatus);

        return array(
            Actions\GetCurrentThemeAction::class  => $currentTheme,
            Actions\GetCompanyStatusAction::class => $companyStatus,
        );
    }

    private static function setupToken($token)
    {
        $option = new Options\Token\Option();
        return $option->updateValue($token);
    }

    private static function setupNewAccount(Actions\GetCurrentThemeAction $currentTheme, Actions\GetCompanyStatusAction $companyStatus)
    {

        // Subscription info

        $_option = new Options\SubscriptionPaymentStatus\Option();
        $_option->updateValue($companyStatus->getResponse()->content->get('payment_status'));

        $_option = new Options\SubscriptionStatus\Option();
        $_option->updateValue($companyStatus->getResponse()->content->get('status'));

        $activeUntil = new Options\SubscriptionActiveUntil\Option();
        // Try to sync account on expiration date.
        $syncAccountTask = new Cron\SyncAccountCronEvent();
        // Delete all previously events.
        $syncAccountTask->unScheduleAll();
        if($companyStatus->getResponse()->isOk()) {
            $activeUntil->updateValue($companyStatus->getResponse()->content->get('active_until'));

            $datetime = \DateTime::createFromFormat(\DateTime::ISO8601, $activeUntil->getValue());
            if($datetime) {
                // Setup new event.
                $syncAccountTask->setTimestamp($datetime->getTimestamp());
                $syncAccountTask->schedule();
            }
        } else {
            $activeUntil->delete();
        }

        // -------------------------------------------------------------------------------------------------------------

        $_option = new Options\SetkaPostCreated\Option();
        $_option->delete();

        // -------------------------------------------------------------------------------------------------------------

        // Theme info

        foreach($currentTheme->getResponse()->content->get('theme_files') as $file) {
            switch($file['filetype']) {
                case 'css':
                    $_option = new Options\ThemeResourceCSS\Option();
                    $_option->updateValue($file['url']);
                    break;

                case 'json':
                    $_option = new Options\ThemeResourceJS\Option();
                    $_option->updateValue($file['url']);
                    break;
            }
        }
        unset($file, $_option);

        $_option_css = new Options\EditorCSS\Option();
        $_option_js  = new Options\EditorJS\Option();
        if($currentTheme->getResponse()->isOk()) {
            foreach($currentTheme->getResponse()->content->get('content_editor_files') as $file) {
                if('css' === $file['filetype']) {
                    $_option_css->updateValue($file['url']);
                } elseif('js' === $file['filetype']) {
                    $_option_js->updateValue($file['url']);
                }
            }
        }
        elseif($currentTheme->getResponse()->getStatusCode() === Response::HTTP_FORBIDDEN) {
            $_option_js->delete();
            $_option_css->delete();
        }
        unset($file, $_option_css, $_option_js);

        $editorVersion = new Options\EditorVersion\Option();
        $editorVersion->updateValue($currentTheme->getResponse()->content->get('content_editor_version'));
        unset($editorVersion);

        // -------------------------------------------------------------------------------------------------------------

        // Plugins
        $_option = new Options\ThemePluginsJS\Option();
        if($currentTheme->getResponse()->content->has('plugins')) {
            $plugins = $currentTheme->getResponse()->content->get('plugins');
            $_option->updateValue($plugins[0]['url']);
        }
        else {
            $_option->delete();
        }
        unset($_option, $plugins);

        // Public Token
        $publicTokenOption = new Options\PublicToken\PublicTokenOption();
        $publicTokenOption->updateValue($currentTheme->getResponse()->content->get('public_token'));

        $_option = new Options\PlanFeatures\PlanFeaturesOption();
        $_option->updateValue($companyStatus->getResponse()->content->get('features'));

        // -------------------------------------------------------------------------------------------------------------

        // Report about successfully signed in
        $user_signed_up = new Cron\UserSignedUpCronEvent();
        $user_signed_up->unScheduleAll();
        $user_signed_up->schedule();

        // -------------------------------------------------------------------------------------------------------------

        $updateAnonymousAccountTask = new Cron\UpdateAnonymousAccountCronEvent();
        $updateAnonymousAccountTask->unScheduleAll();

        // -------------------------------------------------------------------------------------------------------------

        $manager = FilesManagerFactory::create();
        if(!PluginConfig::isVIP()) {
            // Tasks for Files syncing
            $manager
                ->restartSyncing()
                ->enableSyncingTasks();
        } else {
            $manager
                ->disableSyncingTasks();
        }
    }

    public static function signInAnonymous()
    {
        $api          = SetkaAPI\APIFactory::create();
        $currentTheme = new Actions\GetCurrentThemeAnonymouslyAction();

        $api->request($currentTheme);

        if($currentTheme->getErrors()->hasErrors()) {
            return $currentTheme;
        }

        foreach($currentTheme->getResponse()->content->get('theme_files') as $file) {
            switch($file['filetype']) {
                case 'css':
                    $themeResourceCSSOption = new Options\ThemeResourceCSS\Option();
                    $themeResourceCSSOption->updateValue($file['url']);
                    break;

                case 'json':
                    $themeResourceJSOption = new Options\ThemeResourceJS\Option();
                    $themeResourceJSOption->updateValue($file['url']);
                    break;
            }
        }

        $editorCSSOption = new Options\EditorCSS\Option();
        $editorJSOption  = new Options\EditorJS\Option();
        if($currentTheme->getResponse()->isOk()) {
            foreach($currentTheme->getResponse()->content->get('content_editor_files') as $file) {
                if('css' === $file['filetype']) {
                    $editorCSSOption->updateValue($file['url']);
                } elseif('js' === $file['filetype']) {
                    $editorJSOption->updateValue($file['url']);
                }
            }
        }

        $editorVersionOption = new Options\EditorVersion\Option();
        $editorVersionOption->updateValue($currentTheme->getResponse()->content->get('content_editor_version'));

        // -------------------------------------------------------------------------------------------------------------

        // Plugins
        $themePluginsJSOption = new Options\ThemePluginsJS\Option();
        if($currentTheme->getResponse()->content->has('plugins')) {
            $plugins = $currentTheme->getResponse()->content->get('plugins');
            $themePluginsJSOption->updateValue($plugins[0]['url']);
        } else {
            $themePluginsJSOption->delete();
        }

        // Subscription
        $subscriptionStatusOption = new Options\SubscriptionStatus\Option();
        $subscriptionStatusOption->updateValue('running');

        $updateAnonymousAccountTask = new Cron\UpdateAnonymousAccountCronEvent();
        $updateAnonymousAccountTask->unScheduleAll();
        $updateAnonymousAccountTask->schedule();
    }
}
