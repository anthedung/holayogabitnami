<?php
namespace Setka\Editor;

use Korobochkin\WPKit\Pages\Tabs\Tabs;
use Korobochkin\WPKit\Plugins\AbstractPlugin;
use Korobochkin\WPKit\Translations\PluginTranslations;
use Korobochkin\WPKit\Utils\WordPressFeatures;
use Korobochkin\WPKit\Uninstall\Uninstall;
use Psr\Log\LoggerInterface;
use Setka\Editor\Admin\Ajax\AjaxRunner;
use Setka\Editor\Admin\Ajax\SetkaEditorAjaxStack;
use Setka\Editor\Admin\Cron\AMPStylesQueueCronEvent;
use Setka\Editor\Admin\Cron\CronEventsRunner;
use Setka\Editor\Admin\Cron\Files\FilesManagerCronEvent;
use Setka\Editor\Admin\Cron\Files\FilesQueueCronEvent;
use Setka\Editor\Admin\Cron\Files\SendFilesStatCronEvent;
use Setka\Editor\Admin\Cron\AMPStylesCronEvent;
use Setka\Editor\Admin\Cron\SetkaPostCreatedCronEvent;
use Setka\Editor\Admin\Cron\SyncAccountCronEvent;
use Setka\Editor\Admin\Cron\UpdateAnonymousAccountCronEvent;
use Setka\Editor\Admin\Cron\UserSignedUpCronEvent;
use Setka\Editor\Admin\MetaBoxes\DashBoardMetaBoxesStack;
use Setka\Editor\Admin\MetaBoxes\DashBoardMetaBoxesStackRunner;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterDashboardMetaBox;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterDashboardMetaBoxFactory;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterMetaBox;
use Setka\Editor\Admin\MetaBoxes\InvitationToRegister\InvitationToRegisterMetaBoxFactory;
use Setka\Editor\Admin\MetaBoxes\MetaBoxesStack;
use Setka\Editor\Admin\MetaBoxes\MetaBoxesStackRunner;
use Setka\Editor\Admin\Migrations\Configuration;
use Setka\Editor\Admin\Migrations\Versions\Version20170720130303;
use Setka\Editor\Admin\Migrations\Versions\Version20180102150532;
use Setka\Editor\Admin\Notices\AfterSignInNotice;
use Setka\Editor\Admin\Notices\AMPSyncFailureNotice;
use Setka\Editor\Admin\Notices\InvitationToRegisterNotice;
use Setka\Editor\Admin\Notices\InvitationToRegisterNoticeFactory;
use Setka\Editor\Admin\Notices\NoticesStack;
use Setka\Editor\Admin\Notices\NoticesStackRunner;
use Setka\Editor\Admin\Notices\PaymentErrorNotice;
use Setka\Editor\Admin\Notices\SetkaEditorCantFindResourcesNotice;
use Setka\Editor\Admin\Notices\SetkaEditorThemeDisabledNotice;
use Setka\Editor\Admin\Notices\SubscriptionBlockedNotice;
use Setka\Editor\Admin\Notices\YouCanRegisterNotice;
use Setka\Editor\Admin\Options\AMP\AMPStylesIdOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncAttemptsLimitFailureOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncFailureNoticeOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncFailureOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncLastFailureNameOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncOption;
use Setka\Editor\Admin\Options\AMP\AMPSyncStageOption;
use Setka\Editor\Admin\Options\AMP\UseAMPStylesOption;
use Setka\Editor\Admin\Options\DBVersionOption;
use Setka\Editor\Admin\Options\EditorAccessPostTypesOption;
use Setka\Editor\Admin\Options\EditorAccessPostTypesUtilities;
use Setka\Editor\Admin\Options\EditorAccessRolesOption;
use Setka\Editor\Admin\Options\EditorCSSOption;
use Setka\Editor\Admin\Options\EditorJSOption;
use Setka\Editor\Admin\Options\EditorVersionOption;
use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Options\Files\FileSyncFailureOption;
use Setka\Editor\Admin\Options\Files\FileSyncOption;
use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Options\AMP\AMPCssOption;
use Setka\Editor\Admin\Options\AMP\AMPFontsOption;
use Setka\Editor\Admin\Options\AMP\AMPStylesOption;
use Setka\Editor\Admin\Options\PlanFeatures\PlanFeaturesOption;
use Setka\Editor\Admin\Options\PublicTokenOption;
use Setka\Editor\Admin\Options\SetkaPostCreatedOption;
use Setka\Editor\Admin\Options\SubscriptionActiveUntilOption;
use Setka\Editor\Admin\Options\SubscriptionPaymentStatusOption;
use Setka\Editor\Admin\Options\SubscriptionStatusOption;
use Setka\Editor\Admin\Options\ThemePluginsJSOption;
use Setka\Editor\Admin\Options\ThemeResourceCSSLocalOption;
use Setka\Editor\Admin\Options\ThemeResourceCSSOption;
use Setka\Editor\Admin\Options\ThemeResourceJSLocalOption;
use Setka\Editor\Admin\Options\ThemeResourceJSOption;
use Setka\Editor\Admin\Options\TokenOption;
use Setka\Editor\Admin\Options\WhiteLabelOption;
use Setka\Editor\Admin\Pages\AdminPages;
use Setka\Editor\Admin\Pages\AdminPagesFormFactory;
use Setka\Editor\Admin\Pages\AdminPagesRunner;
use Setka\Editor\Admin\Pages\PluginPagesFactory;
use Setka\Editor\Admin\Pages\Plugins;
use Setka\Editor\Admin\Pages\PluginsRunner;
use Setka\Editor\Admin\Pages\PostRunner;
use Setka\Editor\Admin\Pages\QuickLinks;
use Setka\Editor\Admin\Pages\QuickLinksRunner;
use Setka\Editor\Admin\Pages\Tabs\UninstallTab;
use Setka\Editor\Admin\Pages\TwigFactory;
use Setka\Editor\Admin\Pages\Uninstall\UninstallPage;
use Setka\Editor\Admin\Service\AdminScriptStyles;
use Setka\Editor\Admin\Service\AdminScriptStylesRunner;
use Setka\Editor\Admin\Service\GutenbergHandlePost;
use Setka\Editor\Admin\Service\GutenbergHandlePostRunner;
use Setka\Editor\Admin\Service\Editor;
use Setka\Editor\Admin\Service\FilesManager\DownloadListOfFiles;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Admin\Service\FilesSync\Synchronizer;
use Setka\Editor\Admin\Service\FilesSync\SynchronizerFactory;
use Setka\Editor\Admin\Service\Js\EditorAdapterJsSettings;
use Setka\Editor\Admin\Service\Kses;
use Setka\Editor\Admin\Service\KsesRunner;
use Setka\Editor\Admin\Service\MigrationsRunner;
use Setka\Editor\Admin\Service\SavePost;
use Setka\Editor\Admin\Service\SavePostRunner;
use Setka\Editor\Admin\Service\SetkaEditorAPI\API as SetkaEditorAPI;
use Setka\Editor\Admin\Service\SetkaEditorAPI\APIFactory as SetkaEditorAPIFactory;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Endpoints as SetkaEditorAPIEndpoints;
use Setka\Editor\Admin\Service\WPScreenFactory;
use Setka\Editor\Admin\Transients\AfterSignInNoticeTransient;
use Setka\Editor\Admin\Transients\SettingsErrorsTransient;
use Setka\Editor\Admin\Transients\SettingsTokenTransient;
use Setka\Editor\API as WebHooks;
use Setka\Editor\API\V1\SetkaEditorPluginHttpStack;
use Setka\Editor\API\V1\SetkaEditorPluginHttpStackRunner;
use Setka\Editor\CLI\CliCommandsRunner;
use Setka\Editor\CLI\Commands\AccountCommand;
use Setka\Editor\CLI\Commands\AMPCommand;
use Setka\Editor\CLI\Commands\EditorConfigCommand;
use Setka\Editor\CLI\Commands\FilesArchiveCommand;
use Setka\Editor\CLI\Commands\FilesCreateCommand;
use Setka\Editor\CLI\Commands\FilesDeleteCommand;
use Setka\Editor\CLI\Commands\FilesDownloadCommand;
use Setka\Editor\CLI\Commands\FilesSyncCommand;
use Setka\Editor\PostMetas\AttemptsToDownloadPostMeta;
use Setka\Editor\PostMetas\FileSubPathPostMeta;
use Setka\Editor\PostMetas\OriginUrlPostMeta;
use Setka\Editor\PostMetas\PostLayoutPostMeta;
use Setka\Editor\PostMetas\PostThemePostMeta;
use Setka\Editor\PostMetas\SetkaFileIDPostMeta;
use Setka\Editor\PostMetas\SetkaFileTypePostMeta;
use Setka\Editor\PostMetas\TypeKitIDPostMeta;
use Setka\Editor\PostMetas\UseEditorPostMeta;
use Setka\Editor\Service\Activation;
use Setka\Editor\Service\ActivationRunner;
use Setka\Editor\Admin\Pages\SetkaEditor\Account\AccountPage;
use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\SignUpPage;
use Setka\Editor\Admin\Pages\Tabs\AccessTab;
use Setka\Editor\Admin\Pages\Tabs\AccountTab;
use Setka\Editor\Admin\Pages\Tabs\StartTab;
use Setka\Editor\Service\Config\PluginConfig;
use Setka\Editor\Service\AMP\AMPFactory;
use Setka\Editor\Service\AMP\AMPStylesManagerFactory;
use Setka\Editor\Service\CronSchedules;
use Setka\Editor\Service\CronSchedulesRunner;
use Setka\Editor\Service\DataFactory;
use Setka\Editor\Service\Deactivation;
use Setka\Editor\Service\AMP\AMP;
use Setka\Editor\Service\AMP\AMPRunner;
use Setka\Editor\Service\AMP\AMPStylesManager;
use Setka\Editor\Service\EditorGutenbergModule;
use Setka\Editor\Service\ImageSizes;
use Setka\Editor\Service\ImageSizesRunner;
use Setka\Editor\Service\LoggerFactory;
use Setka\Editor\Service\Config\FileSystemCache;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Service\PostStatusesRunner;
use Setka\Editor\Service\ScriptStyles;
use Setka\Editor\Service\ScriptStylesRunner;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;
use Setka\Editor\Service\SetkaAccount\SignIn;
use Setka\Editor\Service\SetkaAccount\SignOut;
use Setka\Editor\Service\TranslationsRunner;
use Setka\Editor\Service\WhiteLabel;
use Setka\Editor\Service\WhiteLabelRunner;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\Validation;

/**
 * Class Plugin
 */
class Plugin extends AbstractPlugin
{
    const NAME = 'setka-editor';

    const _NAME_ = 'setka_editor';

    const VERSION = '1.17.1';

    const DB_VERSION = 20180102150532;

    const PHP_VERSION_ID_MIN = 50509; // 5.5.9

    const PHP_VERSION_MIN = '5.5.9';

    const WP_VERSION_MIN = '4.1';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->configureDependencies();

        ActivationRunner::setContainer($this->container);
        register_activation_hook($this->getFile(), array(ActivationRunner::class, 'run'));

        \Setka\Editor\Service\Uninstall::setContainer($this->container);

        register_deactivation_hook($this->getFile(), array($this->getContainer()->get('wp.plugins.setka_editor.deactivation'), 'run'));

        /**
         * Uninstall. WordPress call this action when user click "Delete" link.
         *
         * Freemius rewrite register_uninstall_hook() call and we can't use it.
         * And until we are using Freemius we can run un-installer by just adding this action.
         *
         * @since 0.0.2
         */
        add_action('uninstall_' . $this->getBasename(), array('\Setka\Editor\Service\Uninstall', 'run'));

        TranslationsRunner::setContainer($this->container);
        add_action('plugins_loaded', array(TranslationsRunner::class, 'run'), 99);

        ImageSizesRunner::setContainer($this->container);
        add_action('after_setup_theme', array(ImageSizesRunner::class, 'run'));

        ScriptStylesRunner::setContainer($this->container);
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'register'));
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'registerThemeResources'), 1000);
        // Enqueue resources for post markup on frontend
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'enqueue'), 1100);
        add_filter('script_loader_tag', array(ScriptStylesRunner::class, 'scriptLoaderTag'), 10, 2);
        add_action('wp_footer', array(ScriptStylesRunner::class, 'footer'));
        if ($this->container->getParameter('wp.plugins.setka_editor.gutenberg_support')) {
            add_action('init', array(ScriptStylesRunner::class, 'registerGutenberg'));

            GutenbergHandlePostRunner::setContainer($this->container);
            add_action('save_post', array(GutenbergHandlePostRunner::class, 'runSave'), 10, 3);

            foreach($this->container->get(EditorAccessPostTypesOption::class)->get() as $postType) {
                add_filter('rest_prepare_' . $postType, array(GutenbergHandlePostRunner::class, 'maybeConvertClassicEditorPost'), 10, 3);
            }
        }

        CronSchedulesRunner::setContainer($this->container);
        add_filter('cron_schedules', array(CronSchedulesRunner::class, 'addSchedules'));

        PostStatusesRunner::setContainer($this->container);
        add_action('init', array(PostStatusesRunner::class, 'run'));

        if(defined('DOING_CRON') && DOING_CRON) {
            CronEventsRunner::setContainer($this->container);
            add_action('init', array(CronEventsRunner::class, 'run')) ;
        }

        if(defined('WP_CLI') && true === WP_CLI) {
            CliCommandsRunner::setContainer($this->container);
            CliCommandsRunner::run();
        }

        if($this->getContainer()->getParameter('wp.plugins.setka_editor.amp_support')) {
            AMPRunner::setContainer($this->container);
            // We setup few filters inside AMPRunner::afterSetupTheme because it not available early.
            add_action('after_setup_theme', array(AMPRunner::class, 'afterSetupTheme'), 100);
            add_filter('amp_content_sanitizers', array(AMPRunner::class, 'addSanitizers'));
        }

        if (is_admin()) {
            /**
             * Runs admin only stuff.
             */
            $this->runAdmin();
        } else {
            /**
             * If post created with Setka Editor when this post don't need preparation before outputting
             * content via the_content(). For example: we don't need wpautop(), shortcode_unautop()...
             * More info (documentation) in \Setka\Editor\Service\TheContent class.
             *
             * You can easily disable this stuff and manipulate this filters as you need by simply removing
             * this three filters below. Don't forget what posts created with Setka Editor not should be
             * parsed by wpautop().
             *
             * @see \Setka\Editor\Service\TheContent
             */
            add_filter('the_content', array('\Setka\Editor\Service\TheContent', 'checkTheContentFilters'), 1);
            add_filter('the_content', array('\Setka\Editor\Service\TheContent', 'checkTheContentFiltersAfter'), 999);
            WhiteLabelRunner::setContainer($this->container);
            add_filter('the_content', array(WhiteLabelRunner::class, 'addLabel'), 1100);
        }

        return $this;
    }

    /**
     * Run plugin for WordPress admin area.
     */
    public function runAdmin()
    {
        Admin\Service\Freemius::run($this->getDir());

        MigrationsRunner::setContainer($this->container);
        add_action('admin_init', array(MigrationsRunner::class, 'run'));

        AjaxRunner::setContainer($this->container);
        add_action('admin_init', array(AjaxRunner::class, 'run'));

        SavePostRunner::setContainer($this->container);
        // POST request
        //add_action('save_post', array( '\Setka\Editor\Admin\Service\SavePost', 'savePostAction'), 10, 3);
        add_action('save_post', array(SavePostRunner::class, 'postAction'), 10, 3);
        // Auto save
        add_filter('heartbeat_received', array(SavePostRunner::class, 'heartbeatReceived'), 10, 2);

        AdminPagesRunner::setContainer($this->container);
        add_action('admin_menu', array(AdminPagesRunner::class, 'run'));

        ScriptStylesRunner::setContainer($this->container);
        add_action('admin_enqueue_scripts', array(ScriptStylesRunner::class, 'register'));
        add_action('admin_enqueue_scripts', array(ScriptStylesRunner::class, 'registerThemeResources'), 1000);

        AdminScriptStylesRunner::setContainer($this->container);
        add_action('admin_enqueue_scripts', array(AdminScriptStylesRunner::class, 'run'));
        add_action('admin_enqueue_scripts', array(AdminScriptStylesRunner::class, 'enqueue'), 1100);

        // New and edit post
        PostRunner::setContainer($this->container);
        add_action('load-post.php', array(PostRunner::class, 'run'));
        add_action('load-post-new.php', array(PostRunner::class, 'run'));

        // Action links on /wp-admin/plugins.php
        PluginsRunner::setContainer($this->container);
        add_filter('plugin_action_links_' . $this->getBasename(), array(PluginsRunner::class, 'addActionLinks'));

        NoticesStackRunner::setContainer($this->container);
        add_action('admin_notices', array(NoticesStackRunner::class, 'run'));

        // Setka API requests (webhooks).
        SetkaEditorPluginHttpStackRunner::setContainer($this->container);
        add_action('admin_init', array(SetkaEditorPluginHttpStackRunner::class, 'run'));

        MetaBoxesStackRunner::setContainer($this->container);
        add_action('current_screen', array(MetaBoxesStackRunner::class, 'run'));

        DashBoardMetaBoxesStackRunner::setContainer($this->container);
        add_action('wp_dashboard_setup', array(DashBoardMetaBoxesStackRunner::class, 'run'));

        KsesRunner::setContainer($this->container);
        add_filter('wp_kses_allowed_html', array(KsesRunner::class, 'allowedHTML'), 10, 2);

        QuickLinksRunner::setContainer($this->container);
        add_action('admin_init', array(QuickLinksRunner::class, 'run'));
    }

    /**
     * Configure DI container.
     */
    public function configureDependencies()
    {
        /**
         * @var $container ContainerBuilder
         */
        $container = $this->getContainer();

        // Folder with cached files (templates + translations).
        if(!$container->hasParameter('wp.plugins.setka_editor.cache_dir')) {
            if(defined('SETKA_EDITOR_CACHE_DIR')) {
                $container->setParameter(
                    'wp.plugins.setka_editor.cache_dir',
                    SETKA_EDITOR_CACHE_DIR
                );
            } elseif (is_admin()) {
                $container->setParameter(
                    'wp.plugins.setka_editor.cache_dir',
                    FileSystemCache::define($this->getDir()) // Require WordPress FS stuff only on wp-admin.
                );
            } else {
                $container->setParameter(
                    'wp.plugins.setka_editor.cache_dir',
                    false
                );
            }
        }

        // Folder with Twig templates.
        $container->setParameter(
            'wp.plugins.setka_editor.templates_path',
            path_join($this->getDir(), 'twig-templates')
        );

        $container->setParameter(
            'wp.plugins.setka_editor.languages_path',
            dirname($this->getBasename()) . '/languages'
        );

        if(!$container->hasParameter('wp.plugins.setka_editor.download_attempts')) {
            $container->setParameter(
                'wp.plugins.setka_editor.download_attempts',
                (defined('SETKA_EDITOR_FILES_DOWNLOADING_ATTEMPTS')) ? SETKA_EDITOR_FILES_DOWNLOADING_ATTEMPTS : 3
            );
        }

        if(!$container->hasParameter('wp.plugins.setka_editor.amp.file_max_size')) {
            $container->setParameter('wp.plugins.setka_editor.amp.file_max_size', 50000);
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.api.endpoint')) {
            $container->setParameter(
                'wp.plugins.setka_editor.api.endpoint',
                (defined('SETKA_EDITOR_DEBUG') && SETKA_EDITOR_DEBUG === true) ? SetkaEditorAPIEndpoints::API_DEV : SetkaEditorAPIEndpoints::API
            );
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.api.basic_auth_login')) {
            $container->setParameter(
                'wp.plugins.setka_editor.api.basic_auth_login',
                (defined('SETKA_EDITOR_API_BASIC_AUTH_USERNAME')) ? SETKA_EDITOR_API_BASIC_AUTH_USERNAME : false
            );
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.api.basic_auth_password')) {
            $container->setParameter(
                'wp.plugins.setka_editor.api.basic_auth_password',
                (defined('SETKA_EDITOR_API_BASIC_AUTH_PASSWORD')) ? SETKA_EDITOR_API_BASIC_AUTH_PASSWORD : false
            );
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.amp_support')) {
            $container->setParameter('wp.plugins.setka_editor.amp_support', true);
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.amp_mode')) {
            // We detect this parameter later on after_setup_theme WordPress action in AMPFactory::create.
            $container->setParameter('wp.plugins.setka_editor.amp_mode', false);
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.manage_type_kit')) {
            $container->setParameter('wp.plugins.setka_editor.manage_type_kit', true);
        }

        if (!$container->hasParameter('wp.plugins.setka_editor.gutenberg_support')) {
            $container->setParameter('wp.plugins.setka_editor.gutenberg_support', PluginConfig::isGutenberg());
        }

        $container
            ->register('wp.plugins.setka_editor.translations', PluginTranslations::class)
            ->addArgument(self::NAME)
            ->addArgument('%wp.plugins.setka_editor.languages_path%');

        $container
            ->register(Activation::class, Activation::class)
            ->addArgument(new Reference(SetkaEditorAccount::class));

        // Twig itself prepared for rendering Symfony Forms.
        $container
            ->register('wp.plugins.setka_editor.twig')
            ->setFactory(array(TwigFactory::class, 'create'))
            ->addArgument('%wp.plugins.setka_editor.cache_dir%')
            ->addArgument('%wp.plugins.setka_editor.templates_path%')
            ->setLazy(true);

        // Symfony Validator.
        $container
            ->register('wp.plugins.setka_editor.validator')
            ->setFactory(array(Validation::class, 'createValidator'))
            ->setLazy(true);

        // Symfony Form Factory for factory %).
        $container
            ->register('wp.plugins.setka_editor.form_factory_for_factory', AdminPagesFormFactory::class)
            ->addArgument(new Reference('wp.plugins.setka_editor.validator'));

        // Symfony Form Factory.
        $container
            ->register('wp.plugins.setka_editor.form_factory')
            ->setFactory(array(new Reference('wp.plugins.setka_editor.form_factory_for_factory'), 'create'))
            ->setLazy(true);

        // Logger Factory
        $container
            ->register('wp.plugins.setka_editor.logger_factory', LoggerFactory::class)
            ->addArgument($this->getDir());

        // Logger for files sync.
        $container
            ->register('wp.plugins.setka_editor.logger.main', LoggerInterface::class)
            ->setFactory(array(new Reference('wp.plugins.setka_editor.logger_factory'), 'create'))
            ->addArgument(self::NAME);

        $container
            ->register('wp.plugins.setka_editor.wp_screen', \WP_Screen::class)
            ->setFactory(array(WPScreenFactory::class, 'create'));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(FilesManagerCronEvent::class, FilesManagerCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setFilesManager', array(new Reference(FilesManager::class)));

        $container
            ->register(FilesQueueCronEvent::class, FilesQueueCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setFilesManager', array(new Reference(FilesManager::class)));

        $container
            ->register(SendFilesStatCronEvent::class, SendFilesStatCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setSetkaEditorAPI', array(new Reference(SetkaEditorAPI::class)))
            ->addMethodCall('setUseLocalFilesOption', array(new Reference(UseLocalFilesOption::class)))
            ->addMethodCall('setFilesManager', array(new Reference(FilesManager::class)));

        $container
            ->register(AMPStylesCronEvent::class, AMPStylesCronEvent::class)
            ->addMethodCall('setAMPStylesManager', array( new Reference(AMPStylesManager::class)))
            ->addMethodCall('setLogger', array(new Reference('wp.plugins.setka_editor.logger.main')));

        $container
            ->register(AMPStylesQueueCronEvent::class, AMPStylesQueueCronEvent::class)
            ->addMethodCall('setAMPStylesManager', array( new Reference(AMPStylesManager::class)))
            ->addMethodCall('setLogger', array(new Reference('wp.plugins.setka_editor.logger.main')));

        $container
            ->register(SetkaPostCreatedCronEvent::class, SetkaPostCreatedCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setSetkaEditorAPI', array(new Reference(SetkaEditorAPI::class)))
            ->addMethodCall('setSetkaPostCreatedOption', array(new Reference(SetkaPostCreatedOption::class)));

        $container
            ->register(SyncAccountCronEvent::class, SyncAccountCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)));

        $container
            ->register(UpdateAnonymousAccountCronEvent::class, UpdateAnonymousAccountCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)));

        $container
            ->register(UserSignedUpCronEvent::class, UserSignedUpCronEvent::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setSetkaEditorAPI', array(new Reference(SetkaEditorAPI::class)));

        $container->setParameter(
            'wp.plugins.setka_editor.all_cron_events',
            array(
                new Reference(FilesManagerCronEvent::class),
                new Reference(FilesQueueCronEvent::class),
                new Reference(SendFilesStatCronEvent::class),
                new Reference(AMPStylesCronEvent::class),
                new Reference(AMPStylesQueueCronEvent::class),
                new Reference(SetkaPostCreatedCronEvent::class),
                new Reference(SyncAccountCronEvent::class),
                new Reference(UpdateAnonymousAccountCronEvent::class),
                new Reference(UserSignedUpCronEvent::class),
            )
        );

        //--------------------------------------------------------------------------------------------------------------

        // Admin pages.
        $container
            ->register(AdminPages::class, AdminPages::class)
            ->addArgument(new Reference('wp.plugins.setka_editor.twig'))
            ->addArgument(new Reference('wp.plugins.setka_editor.form_factory'))
            ->addArgument(array(
                new Reference(Admin\Pages\SetkaEditor\SetkaEditorPage::class),
                new Reference(Admin\Pages\Settings\SettingsPage::class),
                new Reference(Admin\Pages\Files\FilesPage::class),
                new Reference(Admin\Pages\Upgrade\Upgrade::class),
                new Reference(Admin\Pages\AMP\AMPPage::class),
                new Reference(Admin\Pages\Uninstall\UninstallPage::class),
            ));


        // Files
        $container
            ->register(Admin\Pages\Files\FilesPage::class, Admin\Pages\Files\FilesPage::class)
            ->addMethodCall('setFilesManager', array(new Reference(FilesManager::class)));

        // Root
        $container
            ->register(Admin\Pages\SetkaEditor\SetkaEditorPage::class, Admin\Pages\SetkaEditor\SetkaEditorPage::class)
            ->addArgument(new Reference(AccountPage::class))
            ->addArgument(new Reference(SignUpPage::class))
            ->addArgument(new Reference(SetkaEditorAccount::class));

        $container
            ->register(AccountPage::class, AccountPage::class)
            ->addMethodCall('setTabs', array(new Reference('wp.plugins.setka_editor.admin.account_tabs')))
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setFormFactory', array(new Reference('wp.plugins.setka_editor.form_factory')));

        $container
            ->register(SignUpPage::class, SignUpPage::class)
            ->addMethodCall('setTabs', array(new Reference('wp.plugins.setka_editor.admin.sign_up_tabs')))
            ->addMethodCall('setNoticesStack', array(new Reference('wp.plugins.setka_editor.notices_stack')))
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setSetkaEditorAPI', array(new Reference(SetkaEditorAPI::class)))
            ->addMethodCall('setFormFactory', array(new Reference('wp.plugins.setka_editor.form_factory')));

        $container
            ->register(Admin\Pages\Settings\SettingsPage::class)
            ->setFactory(array(PluginPagesFactory::class, 'create'))
            ->addArgument(Admin\Pages\Settings\SettingsPage::class)
            ->addArgument($container);

        $container
            ->register(UninstallPage::class)
            ->setFactory(array(PluginPagesFactory::class, 'create'))
            ->addArgument(UninstallPage::class)
            ->addArgument($container);

        // Upgrade
        $container
            ->register(Admin\Pages\Upgrade\Upgrade::class, Admin\Pages\Upgrade\Upgrade::class);

        $container
            ->register(Admin\Pages\AMP\AMPPage::class)
            ->setFactory(array(PluginPagesFactory::class, 'create'))
            ->addArgument(Admin\Pages\AMP\AMPPage::class)
            ->addArgument($container);

        // WordPress plugins
        $container
            ->register(Plugins::class, Plugins::class)
            ->addArgument(new Reference(Admin\Pages\SetkaEditor\SetkaEditorPage::class))
            ->addArgument(new Reference(SetkaEditorAccount::class));

        // Edit and New post
        $container
            ->register(PostRunner::class, PostRunner::class);

        $container
            ->register(EditorAdapterJsSettings::class, EditorAdapterJsSettings::class)
            ->addArgument(new Reference(DataFactory::class))
            ->addArgument(new Reference(SetkaEditorAccount::class));

        $container
            ->register(AdminScriptStyles::class, AdminScriptStyles::class)
            ->addArgument($this->getUrl())
            ->addArgument(WordPressFeatures::isScriptDebug())
            ->addMethodCall('setEditorAdapterJsSettings', array(new Reference(EditorAdapterJsSettings::class)))
            ->addMethodCall('setScreen', array(new Reference('wp.plugins.setka_editor.wp_screen')));

        $container
            ->register(CronSchedules::class, CronSchedules::class);

        $container
            ->register(ScriptStyles::class, ScriptStyles::class)
            ->addArgument($this->getUrl())
            ->addArgument(WordPressFeatures::isScriptDebug())
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)))
            ->addMethodCall('setAmp', array(new Reference(AMP::class)))
            ->addMethodCall('setPluginSettingsPage', array(new Reference(Admin\Pages\SetkaEditor\SetkaEditorPage::class)))
            ->addMethodCall('setEditorGutenbergModule', array(new Reference(EditorGutenbergModule::class)))
            ->addMethodCall('setEditorAdapterJsSettings', array(new Reference(EditorAdapterJsSettings::class)))
            ->addMethodCall('setManageTypeKit', array('%wp.plugins.setka_editor.manage_type_kit%'))
            ->addMethodCall('setGutenbergSupport', array('%wp.plugins.setka_editor.gutenberg_support%'))
            ->addMethodCall('setNoticesStack', array(new Reference('wp.plugins.setka_editor.notices_stack')));

        $container
            ->register(PostStatuses::class, PostStatuses::class);

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register('wp.plugins.setka_editor.uninstall', Uninstall::class)
            ->addMethodCall('setCronEvents', array('%wp.plugins.setka_editor.all_cron_events%'))
            ->addMethodCall('setSuppressExceptions', array(true));

        $container
            ->register('wp.plugins.setka_editor.deactivation', Deactivation::class)
            ->addArgument($this->getFile());

        $container
            ->register(WhiteLabel::class, WhiteLabel::class);

        $container
            ->register(AccessTab::class, AccessTab::class);

        $container
            ->register(AccountTab::class, AccountTab::class);

        $container
            ->register(StartTab::class, StartTab::class);

        $container
            ->register(UninstallTab::class, UninstallTab::class);

        $container
            ->register('wp.plugins.setka_editor.admin.account_tabs', Tabs::class)
            ->addMethodCall('addTab', array(new Reference(AccountTab::class)))
            ->addMethodCall('addTab', array(new Reference(AccessTab::class)))
            ->addMethodCall('addTab', array(new Reference(UninstallTab::class)));

        $container
            ->register('wp.plugins.setka_editor.admin.sign_up_tabs', Tabs::class)
            ->addMethodCall('addTab', array(new Reference(StartTab::class)))
            ->addMethodCall('addTab', array(new Reference(AccessTab::class)))
            ->addMethodCall('addTab', array(new Reference(UninstallTab::class)));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AfterSignInNotice::class, AfterSignInNotice::class);

        $container
            ->register(AMPSyncFailureNotice::class, AMPSyncFailureNotice::class)
            ->addArgument(new Reference(AMPSyncFailureNoticeOption::class))
            ->addMethodCall('setAmpSyncFailureOption', array(new Reference(AMPSyncFailureOption::class)))
            ->addMethodCall('setAmpSyncLastFailureNameOption', array(new Reference(AMPSyncLastFailureNameOption::class)));

        $container
            ->register(InvitationToRegisterNotice::class)
            ->setFactory(array(InvitationToRegisterNoticeFactory::class, 'create'))
            ->addArgument($this->container);

        $container
            ->register(PaymentErrorNotice::class, PaymentErrorNotice::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)));

        $container
            ->register(SetkaEditorCantFindResourcesNotice::class, SetkaEditorCantFindResourcesNotice::class);

        $container
            ->register(SetkaEditorThemeDisabledNotice::class, SetkaEditorThemeDisabledNotice::class);

        $container
            ->register(SubscriptionBlockedNotice::class, SubscriptionBlockedNotice::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)));

        $container
            ->register(YouCanRegisterNotice::class, YouCanRegisterNotice::class)
            ->addMethodCall('setSetkaEditorAccount', array(new Reference(SetkaEditorAccount::class)));

        $container->setParameter(
            'wp.plugins.setka_editor.all_notices',
            array(
                new Reference(AfterSignInNotice::class),
                new Reference(AMPSyncFailureNotice::class),
                new Reference(InvitationToRegisterNotice::class),
                new Reference(PaymentErrorNotice::class),
                new Reference(SetkaEditorCantFindResourcesNotice::class),
                new Reference(SetkaEditorThemeDisabledNotice::class),
                new Reference(SubscriptionBlockedNotice::class),
                new Reference(YouCanRegisterNotice::class),
            )
        );

        $container
            ->register('wp.plugins.setka_editor.notices_stack', NoticesStack::class)
            ->addArgument('%wp.plugins.setka_editor.gutenberg_support%')
            ->addMethodCall('setNotices', array('%wp.plugins.setka_editor.all_notices%'));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(InvitationToRegisterDashboardMetaBox::class)
            ->setFactory(array(InvitationToRegisterDashboardMetaBoxFactory::class, 'create'))
            ->addArgument($container);

        $container
            ->register(DashBoardMetaBoxesStack::class, DashBoardMetaBoxesStack::class)
            ->addArgument(new Reference(SetkaEditorAccount::class))
            ->addMethodCall('setContainer', array($container));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(InvitationToRegisterMetaBox::class)
            ->setFactory(array(InvitationToRegisterMetaBoxFactory::class, 'create'))
            ->addArgument($container);

        $container
            ->register(MetaBoxesStack::class, MetaBoxesStack::class)
            ->addArgument(new Reference(SetkaEditorAccount::class))
            ->addMethodCall('setContainer', array($container));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(DataFactory::class, DataFactory::class)
            ->addArgument(new Reference('wp.plugins.setka_editor.validator'));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AMPCssOption::class, AMPCssOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPCssOption::class);

        $container
            ->register(AMPFontsOption::class, AMPFontsOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPFontsOption::class);

        $container
            ->register(AMPStylesIdOption::class, AMPStylesIdOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPStylesIdOption::class);

        $container
            ->register(AMPStylesOption::class, AMPStylesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPStylesOption::class);

        $container
            ->register(AMPSyncAttemptsLimitFailureOption::class, AMPSyncAttemptsLimitFailureOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPSyncAttemptsLimitFailureOption::class);

        $container
            ->register(AMPSyncFailureNoticeOption::class, AMPSyncFailureNoticeOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPSyncFailureNoticeOption::class);

        $container
            ->register(AMPSyncFailureOption::class, AMPSyncFailureOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPSyncFailureOption::class);

        $container
            ->register(AMPSyncLastFailureNameOption::class, AMPSyncLastFailureNameOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
        ->addArgument(AMPSyncLastFailureNameOption::class);

        $container
            ->register(AMPSyncOption::class, AMPSyncOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
        ->addArgument(AMPSyncOption::class);

        $container
            ->register(AMPSyncStageOption::class, AMPSyncStageOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AMPSyncStageOption::class);

        $container
            ->register(UseAMPStylesOption::class, UseAMPStylesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(UseAMPStylesOption::class);

        $container
            ->register(DBVersionOption::class, DBVersionOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(DBVersionOption::class);

        $container
            ->register(EditorAccessPostTypesOption::class, EditorAccessPostTypesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(EditorAccessPostTypesOption::class);

        $container
            ->register(EditorAccessPostTypesUtilities::class, EditorAccessPostTypesUtilities::class)
            ->addArgument(new Reference(EditorAccessPostTypesOption::class));

        $container
            ->register(EditorAccessRolesOption::class, EditorAccessRolesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(EditorAccessRolesOption::class);

        $container
            ->register(EditorCSSOption::class, EditorCSSOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(EditorCSSOption::class);

        $container
            ->register(EditorJSOption::class, EditorJSOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(EditorJSOption::class);

        $container
            ->register(EditorVersionOption::class, EditorVersionOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(EditorVersionOption::class);

        $container
            ->register(FilesOption::class, FilesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(FilesOption::class);

        $container
            ->register(FileSyncFailureOption::class, FileSyncFailureOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(FileSyncFailureOption::class);

        $container
            ->register(FileSyncOption::class, FileSyncOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(FileSyncOption::class);

        $container
            ->register(FileSyncStageOption::class, FileSyncStageOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(FileSyncStageOption::class);

        $container
            ->register(UseLocalFilesOption::class, UseLocalFilesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(UseLocalFilesOption::class);

        $container
            ->register(PlanFeaturesOption::class, PlanFeaturesOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(PlanFeaturesOption::class);

        $container
            ->register(PublicTokenOption::class, PublicTokenOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(PublicTokenOption::class);

        $container
            ->register(SetkaPostCreatedOption::class, SetkaPostCreatedOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(SetkaPostCreatedOption::class);

        $container
            ->register(SubscriptionActiveUntilOption::class, SubscriptionActiveUntilOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(SubscriptionActiveUntilOption::class);

        $container
            ->register(SubscriptionPaymentStatusOption::class, SubscriptionPaymentStatusOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(SubscriptionPaymentStatusOption::class);

        $container
            ->register(SubscriptionStatusOption::class, SubscriptionStatusOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(SubscriptionStatusOption::class);

        $container
            ->register(ThemePluginsJSOption::class, ThemePluginsJSOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(ThemePluginsJSOption::class);

        $container
            ->register(ThemeResourceCSSOption::class, ThemeResourceCSSOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(ThemeResourceCSSOption::class);

        $container
            ->register(ThemeResourceCSSLocalOption::class, ThemeResourceCSSLocalOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(ThemeResourceCSSLocalOption::class);

        $container
            ->register(ThemeResourceJSOption::class, ThemeResourceJSOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(ThemeResourceJSOption::class);

        $container
            ->register(ThemeResourceJSLocalOption::class, ThemeResourceJSLocalOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(ThemeResourceJSLocalOption::class);

        $container
            ->register(TokenOption::class, TokenOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(TokenOption::class);

        $container
            ->register(WhiteLabelOption::class, WhiteLabelOption::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(WhiteLabelOption::class);

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AfterSignInNoticeTransient::class, AfterSignInNoticeTransient::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AfterSignInNoticeTransient::class);

        $container
            ->register(SettingsErrorsTransient::class, SettingsErrorsTransient::class);

        $container
            ->register(SettingsTokenTransient::class, SettingsTokenTransient::class);

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AttemptsToDownloadPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(AttemptsToDownloadPostMeta::class);

        $container
            ->register(FileSubPathPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(FileSubPathPostMeta::class);

        $container
            ->register(OriginUrlPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(OriginUrlPostMeta::class);

        $container
            ->register(PostLayoutPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(PostLayoutPostMeta::class);

        $container
            ->register(PostThemePostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(PostThemePostMeta::class);

        $container
            ->register(SetkaFileIDPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(SetkaFileIDPostMeta::class);

        $container
            ->register(SetkaFileTypePostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(SetkaFileTypePostMeta::class);

        $container
            ->register(TypeKitIDPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(TypeKitIDPostMeta::class);

        $container
            ->register(UseEditorPostMeta::class)
            ->setFactory(array(new Reference(DataFactory::class), 'create'))
            ->addArgument(UseEditorPostMeta::class);

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(Version20170720130303::class, Version20170720130303::class)
            ->addArgument(new Reference(SetkaEditorAccount::class))
            ->addArgument(new Reference(FilesManager::class));

        $container
            ->register(Version20180102150532::class, Version20180102150532::class)
            ->addArgument(new Reference(SetkaEditorAccount::class));

        $container->setParameter(
            'wp.plugins.setka_editor.migration_versions',
            array(
                new Reference(Version20170720130303::class),
                new Reference(Version20180102150532::class),
            )
        );

        $container
            ->register('wp.plugins.setka_editor.migrations', Configuration::class)
            ->addArgument(new Reference(DBVersionOption::class))
            ->addArgument(self::DB_VERSION)
            ->addArgument('%wp.plugins.setka_editor.migration_versions%');

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(SetkaEditorAccount::class, SetkaEditorAccount::class)
            ->addArgument(new Reference(TokenOption::class))
            ->addArgument(new Reference(SubscriptionStatusOption::class))
            ->addArgument(new Reference(SubscriptionActiveUntilOption::class))
            ->addArgument(new Reference(SubscriptionPaymentStatusOption::class))
            ->addArgument(new Reference(EditorJSOption::class))
            ->addArgument(new Reference(EditorCSSOption::class))
            ->addArgument(new Reference(ThemeResourceCSSOption::class))
            ->addArgument(new Reference(ThemeResourceCSSLocalOption::class))
            ->addArgument(new Reference(ThemeResourceJSOption::class))
            ->addArgument(new Reference(ThemeResourceJSLocalOption::class))
            ->addArgument(new Reference(ThemePluginsJSOption::class))
            ->addArgument(new Reference(UseLocalFilesOption::class))
            ->addArgument(new Reference(SignIn::class))
            ->addArgument(new Reference(SignOut::class))
            ->addMethodCall('setPublicTokenOption', array(new Reference(PublicTokenOption::class)));

        $container
            ->register(SignIn::class, SignIn::class)
            ->addArgument(new Reference(SetkaEditorAPI::class))
            ->addArgument(new Reference(FilesManager::class))
            ->addArgument(new Reference(AMPStylesCronEvent::class))
            ->addArgument(new Reference(AMPStylesQueueCronEvent::class));

        $container
            ->register(SignOut::class, SignOut::class)
            ->addArgument($container);

        $container
            ->register(Editor::class, Editor::class)
            ->addArgument(new Reference(SetkaEditorAccount::class))
            ->addArgument(new Reference(EditorAccessPostTypesUtilities::class));

        $container
            ->register(EditorGutenbergModule::class, EditorGutenbergModule::class)
            ->addArgument(new Reference(ScriptStyles::class))
            ->addArgument(new Reference(DataFactory::class));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AccountCommand::class, AccountCommand::class)
            ->addArgument(new Reference(DataFactory::class))
            ->addArgument(new Reference(SetkaEditorAccount::class));

        $container
            ->register(AMPCommand::class, AMPCommand::class)
            ->addArgument(new Reference(AMPStylesCronEvent::class))
            ->addArgument(new Reference(AMPStylesQueueCronEvent::class))
            ->addArgument(array(
                new Reference(AMPStylesIdOption::class),
                new Reference(AMPStylesOption::class),
                new Reference(AMPSyncAttemptsLimitFailureOption::class),
                new Reference(AMPSyncFailureNoticeOption::class),
                new Reference(AMPSyncFailureOption::class),
                new Reference(AMPSyncLastFailureNameOption::class),
                new Reference(AMPSyncOption::class),
                new Reference(AMPSyncStageOption::class),
                new Reference(UseAMPStylesOption::class),
            ));

        $container
            ->register(EditorConfigCommand::class, EditorConfigCommand::class);

        $container
            ->register(FilesArchiveCommand::class, FilesArchiveCommand::class)
            ->addArgument(new Reference(FilesManager::class));

        $container
            ->register(FilesCreateCommand::class, FilesCreateCommand::class)
            ->addArgument(new Reference(SetkaEditorAccount::class));

        $container
            ->register(FilesDeleteCommand::class, FilesDeleteCommand::class);

        $container
            ->register(FilesDownloadCommand::class, FilesDownloadCommand::class)
            ->addArgument(new Reference(SetkaEditorAccount::class))
            ->addArgument(new Reference(Synchronizer::class))
            ->addArgument('%wp.plugins.setka_editor.download_attempts%');

        $container
            ->register(FilesSyncCommand::class, FilesSyncCommand::class)
            ->addArgument(new Reference(FilesManager::class));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(SetkaEditorAPI::class)
            ->setFactory(array(SetkaEditorAPIFactory::class, 'create'))
            ->addArgument(new Reference('wp.plugins.setka_editor.validator'))
            ->addArgument(self::VERSION)
            ->addArgument('%wp.plugins.setka_editor.api.endpoint%')
            ->addArgument('%wp.plugins.setka_editor.api.basic_auth_login%')
            ->addArgument('%wp.plugins.setka_editor.api.basic_auth_password%');

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register('wp.plugins.setka_editor.web_hooks', SetkaEditorPluginHttpStack::class)
            ->addArgument(array(
                '/webhook/setka-editor/v1/company_status/update' => WebHooks\V1\Actions\CompanyStatusUpdateAction::class,
                '/webhook/setka-editor/v1/resources/update' => WebHooks\V1\Actions\ResourcesUpdateAction::class,
                '/webhook/setka-editor/v1/token/check' => WebHooks\V1\Actions\TokenCheckAction::class,
                '/webhook/setka-editor/v1/files/update' => WebHooks\V1\Actions\UpdateFilesAction::class,
            ))
            ->addArgument(self::NAME)
            ->addMethodCall('setContainer', array($container));

        $container
            ->register('wp.plugins.setka_editor.ajax', SetkaEditorAjaxStack::class)
            ->addArgument(array(
                Admin\Ajax\DismissNoticesAction::class => Admin\Ajax\DismissNoticesAction::class,
            ))
            ->addArgument(self::NAME)
            ->addMethodCall('setContainer', array($container));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(DownloadListOfFiles::class, DownloadListOfFiles::class)
            ->addArgument(new Reference(SetkaEditorAPI::class))
            ->addArgument(new Reference(TokenOption::class));

        $container
            ->register(FilesManager::class, FilesManager::class)
            ->setFactory(array(FilesManagerFactory::class, 'create'))
            ->addArgument(new Reference(DataFactory::class))
            ->addArgument(new Reference(DownloadListOfFiles::class))
            ->addArgument(new Reference(Synchronizer::class))
            ->addArgument('%wp.plugins.setka_editor.download_attempts%');

        $container
            ->register(Synchronizer::class, Synchronizer::class)
            ->setFactory(array(SynchronizerFactory::class, 'create'))
            ->addArgument(new Reference('wp.plugins.setka_editor.logger.main'))
            ->addArgument('%wp.plugins.setka_editor.download_attempts%');

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(Kses::class, Kses::class);

        $container
            ->register(SavePost::class, SavePost::class)
            ->addArgument(new Reference(SetkaPostCreatedOption::class))
            ->addArgument(new Reference(SetkaPostCreatedCronEvent::class))
            ->addArgument(new Reference(UseEditorPostMeta::class))
            ->addArgument(new Reference(PostThemePostMeta::class))
            ->addArgument(new Reference(PostLayoutPostMeta::class))
            ->addArgument(new Reference(TypeKitIDPostMeta::class));

        $container
            ->register(GutenbergHandlePost::class, GutenbergHandlePost::class)
            ->addArgument(new Reference(DataFactory::class))
            ->addArgument(new Reference('wp.plugins.setka_editor.wp_screen'))
            ->addArgument(new Reference(EditorGutenbergModule::class));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AMP::class, AMP::class)
            ->setFactory(array(AMPFactory::class, 'create'))
            ->addArgument('%wp.plugins.setka_editor.amp_support%')
            ->addArgument('%wp.plugins.setka_editor.amp_mode%')
            ->addArgument(new Reference(AMPCssOption::class))
            ->addArgument(new Reference(AMPFontsOption::class))
            ->addArgument(new Reference(AMPStylesOption::class))
            ->addArgument(new Reference(UseAMPStylesOption::class))
            ->addArgument(new Reference(DataFactory::class));

        $container
            ->register(AMPStylesManager::class, AMPStylesManager::class)
            ->setFactory(array(AMPStylesManagerFactory::class, 'create'))

            ->addArgument(new Reference('wp.plugins.setka_editor.logger.main'))

            ->addArgument(new Reference(AMPStylesIdOption::class))
            ->addArgument(new Reference(AMPStylesOption::class))
            ->addArgument(new Reference(AMPSyncAttemptsLimitFailureOption::class))
            ->addArgument(new Reference(AMPSyncFailureNoticeOption::class))
            ->addArgument(new Reference(AMPSyncFailureOption::class))
            ->addArgument(new Reference(AMPSyncLastFailureNameOption::class))
            ->addArgument(new Reference(AMPSyncOption::class))
            ->addArgument(new Reference(AMPSyncStageOption::class))
            ->addArgument(new Reference(UseAMPStylesOption::class))

            ->addArgument(new Reference(DataFactory::class))

            ->addArgument('%wp.plugins.setka_editor.download_attempts%')
            ->addArgument('%wp.plugins.setka_editor.amp.file_max_size%')
        ;

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(QuickLinks::class, QuickLinks::class)
            ->addArgument(new Reference(UseEditorPostMeta::class))
            ->addArgument(new Reference(EditorAccessPostTypesOption::class));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(ImageSizes::class, ImageSizes::class);
    }

    /**
     * @inheritdoc
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::NAME;
    }
}
