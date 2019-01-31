<?php
namespace Setka\Editor\Service\SetkaAccount;

use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Actions;
use Setka\Editor\Admin\Cron;
use Setka\Editor\Admin\Options;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\AMP\AMPStylesManager;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SignIn
 */
class SignIn
{
    /**
     * @var SetkaEditorAPI\API
     */
    protected $setkaEditorAPI;

    /**
     * @var FilesManager
     */
    protected $filesManager;

    /**
     * @var Cron\AMPStylesCronEvent
     */
    protected $ampStylesCronEvent;

    /**
     * @var Cron\AMPStylesQueueCronEvent
     */
    protected $ampStylesQueueCronEvent;

    /**
     * SignIn constructor.
     * @param SetkaEditorAPI\API $setkaEditorAPI
     * @param FilesManager $filesManager
     * @param Cron\AMPStylesCronEvent $ampStylesCronEvent
     * @param Cron\AMPStylesQueueCronEvent $ampStylesQueueCronEvent
     */
    public function __construct(
        SetkaEditorAPI\API $setkaEditorAPI,
        FilesManager $filesManager,
        Cron\AMPStylesCronEvent $ampStylesCronEvent,
        Cron\AMPStylesQueueCronEvent $ampStylesQueueCronEvent
    ) {
        $this->setkaEditorAPI = $setkaEditorAPI;
        $this->filesManager   = $filesManager;

        $this->ampStylesCronEvent      = $ampStylesCronEvent;
        $this->ampStylesQueueCronEvent = $ampStylesQueueCronEvent;
    }

    /**
     * This function used for auth via Settings pages (WordPress automatically update and save Token).
     *
     * @param $token string New token to use.
     * @param $update_token bool Should this function insert this token to DB or not. By default token updated in DB
     * by WordPress (runned from Settings page). But if you are doing auth from script you should pass true (default).
     *
     * @return \Setka\Editor\Admin\Service\SetkaEditorAPI\Prototypes\ActionInterface[]
     */
    public function signInByToken($token, $update_token = true)
    {

        $responses = $this->sendAuthRequests($token);

        foreach($responses as $response) {
            /**
             * @var $response \Setka\Editor\Admin\Service\SetkaEditorAPI\Prototypes\ActionAbstract
             */
            if(count($response->getErrors()) > 0) {
                return $responses;
            }
        }
        unset($response);

        // Setup new account settings
        if($update_token) {
            $this->setupToken($token);
        }

        $this->setupNewAccount($responses[Actions\GetCurrentThemeAction::class], $responses[Actions\GetCompanyStatusAction::class]);

        return $responses;
    }

    /**
     * Send auth requests and return actions with validated responses.
     *
     * @param $token string Company token (license key).
     *
     * @return array Executed actions
     */
    public function sendAuthRequests($token)
    {
        // API (request token details on Setka Server via API)
        $this->setkaEditorAPI->setAuthCredits(new SetkaEditorAPI\AuthCredits($token));

        // Theme files
        $currentTheme = new Actions\GetCurrentThemeAction();
        $this->setkaEditorAPI->request($currentTheme);

        // Request for details of subscription
        $companyStatus = new Actions\GetCompanyStatusAction();
        $this->setkaEditorAPI->request($companyStatus);

        return array(
            Actions\GetCurrentThemeAction::class  => $currentTheme,
            Actions\GetCompanyStatusAction::class => $companyStatus,
        );
    }

    private function setupToken($token)
    {
        $option = new Options\TokenOption();
        return $option->updateValue($token);
    }

    private function setupNewAccount(Actions\GetCurrentThemeAction $currentTheme, Actions\GetCompanyStatusAction $companyStatus)
    {

        // Subscription info

        $_option = new Options\SubscriptionPaymentStatusOption();
        $_option->updateValue($companyStatus->getResponse()->content->get('payment_status'));

        $_option = new Options\SubscriptionStatusOption();
        $_option->updateValue($companyStatus->getResponse()->content->get('status'));

        $activeUntil = new Options\SubscriptionActiveUntilOption();
        // Try to sync account on expiration date.
        $syncAccountTask = new Cron\SyncAccountCronEvent();
        // Delete all previously events.
        $syncAccountTask->unScheduleAll();
        if($companyStatus->getResponse()->isOk()) {
            $activeUntil->updateValue($companyStatus->getResponse()->content->get('active_until'));

            $datetime = \DateTime::createFromFormat(\DateTime::ISO8601, $activeUntil->get());
            if($datetime) {
                // Setup new event.
                $syncAccountTask->setTimestamp($datetime->getTimestamp());
                $syncAccountTask->schedule();
            }
        } else {
            $activeUntil->delete();
        }

        // -------------------------------------------------------------------------------------------------------------

        $_option = new Options\SetkaPostCreatedOption();
        $_option->delete();

        // -------------------------------------------------------------------------------------------------------------

        // Theme info

        foreach($currentTheme->getResponse()->content->get('theme_files') as $file) {
            switch($file['filetype']) {
                case 'css':
                    $_option = new Options\ThemeResourceCSSOption();
                    $_option->updateValue($file['url']);
                    break;

                case 'json':
                    $_option = new Options\ThemeResourceJSOption();
                    $_option->updateValue($file['url']);
                    break;
            }
        }
        unset($file, $_option);

        $_option_css = new Options\EditorCSSOption();
        $_option_js  = new Options\EditorJSOption();
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

        $editorVersion = new Options\EditorVersionOption();
        $editorVersion->updateValue($currentTheme->getResponse()->content->get('content_editor_version'));
        unset($editorVersion);

        // -------------------------------------------------------------------------------------------------------------

        // Plugins
        $_option = new Options\ThemePluginsJSOption();
        if($currentTheme->getResponse()->content->has('plugins')) {
            $plugins = $currentTheme->getResponse()->content->get('plugins');
            $_option->updateValue($plugins[0]['url']);
        }
        else {
            $_option->delete();
        }
        unset($_option, $plugins);

        // Public Token
        $publicTokenOption = new Options\PublicTokenOption();
        $publicTokenOption->updateValue($currentTheme->getResponse()->content->get('public_token'));

        $_option = new Options\PlanFeatures\PlanFeaturesOption();
        $_option->updateValue($companyStatus->getResponse()->content->get('features'));

        // -------------------------------------------------------------------------------------------------------------

        if($currentTheme->getResponse()->content->has('amp_styles')) {
            try {
                $this->ampStylesCronEvent
                    ->getAmpStylesManager()
                    ->addNewConfig($currentTheme->getResponse()->content->get('amp_styles'));
                $this->ampStylesCronEvent->restart();
                $this->ampStylesQueueCronEvent->restart();
            } catch (\Exception $exception) {
                // Do nothing.
            }
        } else {
            $this->ampStylesCronEvent->getAmpStylesManager()->resetSync();
            $this->ampStylesCronEvent->unscheduleAll();
            $this->ampStylesQueueCronEvent->unscheduleAll();
        }

        // -------------------------------------------------------------------------------------------------------------

        // Report about successfully signed in
        $user_signed_up = new Cron\UserSignedUpCronEvent();
        $user_signed_up->unScheduleAll();
        $user_signed_up->schedule();

        // -------------------------------------------------------------------------------------------------------------

        $updateAnonymousAccountTask = new Cron\UpdateAnonymousAccountCronEvent();
        $updateAnonymousAccountTask->unScheduleAll();

        // -------------------------------------------------------------------------------------------------------------

        if(!PluginConfig::isVIP()) {
            // Tasks for Files syncing
            $this->filesManager
                ->restartSyncing()
                ->enableSyncingTasks();
        } else {
            $this->filesManager
                ->disableSyncingTasks();
        }
    }

    public function signInAnonymous()
    {
        $currentTheme = new Actions\GetCurrentThemeAnonymouslyAction();

        $this->setkaEditorAPI->request($currentTheme);

        if(count($currentTheme->getErrors()) > 0) {
            return $currentTheme;
        }

        foreach($currentTheme->getResponse()->content->get('theme_files') as $file) {
            switch($file['filetype']) {
                case 'css':
                    $themeResourceCSSOption = new Options\ThemeResourceCSSOption();
                    $themeResourceCSSOption->updateValue($file['url']);
                    break;

                case 'json':
                    $themeResourceJSOption = new Options\ThemeResourceJSOption();
                    $themeResourceJSOption->updateValue($file['url']);
                    break;
            }
        }

        $editorCSSOption = new Options\EditorCSSOption();
        $editorJSOption  = new Options\EditorJSOption();
        if($currentTheme->getResponse()->isOk()) {
            foreach($currentTheme->getResponse()->content->get('content_editor_files') as $file) {
                if('css' === $file['filetype']) {
                    $editorCSSOption->updateValue($file['url']);
                } elseif('js' === $file['filetype']) {
                    $editorJSOption->updateValue($file['url']);
                }
            }
        }

        $editorVersionOption = new Options\EditorVersionOption();
        $editorVersionOption->updateValue($currentTheme->getResponse()->content->get('content_editor_version'));

        // -------------------------------------------------------------------------------------------------------------

        // Plugins
        $themePluginsJSOption = new Options\ThemePluginsJSOption();
        if($currentTheme->getResponse()->content->has('plugins')) {
            $plugins = $currentTheme->getResponse()->content->get('plugins');
            $themePluginsJSOption->updateValue($plugins[0]['url']);
        } else {
            $themePluginsJSOption->delete();
        }

        // -------------------------------------------------------------------------------------------------------------

        if($currentTheme->getResponse()->content->has('amp_styles')) {
            try {
                $this->ampStylesCronEvent
                    ->getAmpStylesManager()
                    ->addNewConfig($currentTheme->getResponse()->content->get('amp_styles'));
                $this->ampStylesCronEvent->restart();
                $this->ampStylesQueueCronEvent->restart();
            } catch (\Exception $exception) {
                // Do nothing.
            }
        } else {
            $this->ampStylesCronEvent->getAmpStylesManager()->resetSync();
            $this->ampStylesCronEvent->unscheduleAll();
            $this->ampStylesQueueCronEvent->unscheduleAll();
        }

        // -------------------------------------------------------------------------------------------------------------

        // Subscription
        $subscriptionStatusOption = new Options\SubscriptionStatusOption();
        $subscriptionStatusOption->updateValue('running');

        $updateAnonymousAccountTask = new Cron\UpdateAnonymousAccountCronEvent();
        $updateAnonymousAccountTask->unScheduleAll();
        $updateAnonymousAccountTask->schedule();
    }
}
