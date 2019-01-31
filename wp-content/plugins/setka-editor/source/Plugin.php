<?php
namespace Setka\Editor;

use Korobochkin\WPKit\Pages\Tabs\Tabs;
use Korobochkin\WPKit\Plugins\AbstractPlugin;
use Korobochkin\WPKit\Translations\PluginTranslations;
use Korobochkin\WPKit\Utils\WordPressFeatures;
use Korobochkin\WPKit\Uninstall\Uninstall;
use Setka\Editor\Admin\Cron\CronEventsRunner;
use Setka\Editor\Admin\Cron\Files\FilesManagerCronEvent;
use Setka\Editor\Admin\Cron\Files\FilesQueueCronEvent;
use Setka\Editor\Admin\Cron\Files\SendFilesStatCronEvent;
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
use Setka\Editor\Admin\Notices\AfterSignInNotice;
use Setka\Editor\Admin\Notices\InvitationToRegisterNotice;
use Setka\Editor\Admin\Notices\InvitationToRegisterNoticeFactory;
use Setka\Editor\Admin\Notices\NoticesStack;
use Setka\Editor\Admin\Notices\NoticesStackRunner;
use Setka\Editor\Admin\Notices\PaymentErrorNotice;
use Setka\Editor\Admin\Notices\SetkaEditorCantFindResourcesNotice;
use Setka\Editor\Admin\Notices\SetkaEditorThemeDisabledNotice;
use Setka\Editor\Admin\Notices\SubscriptionBlockedNotice;
use Setka\Editor\Admin\Notices\YouCanRegisterNotice;
use Setka\Editor\Admin\Pages\AdminPages;
use Setka\Editor\Admin\Pages\AdminPagesFormFactory;
use Setka\Editor\Admin\Pages\AdminPagesRunner;
use Setka\Editor\Admin\Pages\PluginPagesFactory;
use Setka\Editor\Admin\Pages\Plugins;
use Setka\Editor\Admin\Pages\PluginsRunner;
use Setka\Editor\Admin\Pages\PostRunner;
use Setka\Editor\Admin\Pages\TwigFactory;
use Setka\Editor\Admin\Service\AdminScriptStyles;
use Setka\Editor\Admin\Service\AdminScriptStylesRunner;
use Setka\Editor\Service\Activation;
use Setka\Editor\Service\ActivationRunner;
use Setka\Editor\Admin\Pages\SetkaEditor\Account\AccountPage;
use Setka\Editor\Admin\Pages\SetkaEditor\SignUp\SignUpPage;
use Setka\Editor\Admin\Pages\Tabs\AccessTab;
use Setka\Editor\Admin\Pages\Tabs\AccountTab;
use Setka\Editor\Admin\Pages\Tabs\StartTab;
use Setka\Editor\Service\Config\Files;
use Setka\Editor\Service\CronSchedules;
use Setka\Editor\Service\CronSchedulesRunner;
use Setka\Editor\Service\PathsAndUrls;
use Setka\Editor\Service\Config\FileSystemCache;
use Setka\Editor\Service\ScriptStyles;
use Setka\Editor\Service\ScriptStylesRunner;
use Setka\Editor\Service\TranslationsRunner;
use Setka\Editor\Service\WhiteLabel;
use Setka\Editor\Service\WhiteLabelRunner;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Validator\Validation;

class Plugin extends AbstractPlugin
{
    const NAME = 'setka-editor';

    const _NAME_ = 'setka_editor';

    const VERSION = '1.13.1';

    const DB_VERSION = 20180102150532;

    const PHP_VERSION_ID_MIN = 50509; // 5.5.9

    const PHP_VERSION_MIN = '5.5.9';

    const WP_VERSION_MIN = '4.0';

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->configureDependencies();
        PathsAndUrls::setPlugin($this);

        Files::registerFilesDownloadingAttempts();

        ActivationRunner::setContainer($this->container);
        register_activation_hook($this->getFile(), array(ActivationRunner::class, 'run'));

        \Setka\Editor\Service\Uninstall::setContainer($this->container);

        register_deactivation_hook($this->getFile(), array('\Setka\Editor\Service\Deactivation', 'run'));

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

        // Register post meta (WP setup sanitizers and other stuff)
        add_action('init', array( '\Setka\Editor\Entries\Metas', 'run' ));

        ScriptStylesRunner::setContainer($this->container);
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'register'));
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'registerThemeResources'), 1000);
        // Enqueue resources for post markup on frontend
        add_action('wp_enqueue_scripts', array(ScriptStylesRunner::class, 'enqueue'), 1100);

        CronSchedulesRunner::setContainer($this->container);
        // @codingStandardsIgnoreStart
        add_filter('cron_schedules', array(CronSchedulesRunner::class, 'addSchedules'));
        // @codingStandardsIgnoreEnd

        if(defined('DOING_CRON') && DOING_CRON) {
            CronEventsRunner::setContainer($this->container);
            add_action('init', array(CronEventsRunner::class, 'run')) ;
        }

        // WP CLI commands
        CLI\Loader::run();

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
        require_once(PathsAndUrls::getPluginDirPath('source/libraries/polyfill-wordpress/bootstrap.php'));

        Admin\Service\Freemius::run();

        add_action('admin_init', array('Setka\Editor\Admin\Service\Migrations', 'run'));

        add_action('admin_init', array(FileSystemCache::class, 'run'));

        // Save content from POST request
        add_action('save_post', array( '\Setka\Editor\Admin\Service\SavePost', 'savePostAction'), 10, 3);

        AdminPagesRunner::setContainer($this->container);
        add_action('admin_menu', array(AdminPagesRunner::class, 'run'));

        ScriptStylesRunner::setContainer($this->container);
        add_action('admin_enqueue_scripts', array(ScriptStylesRunner::class, 'register'));
        add_action('admin_enqueue_scripts', array(ScriptStylesRunner::class, 'registerThemeResources'), 1000);

        AdminScriptStylesRunner::setContainer($this->container);
        add_action('admin_enqueue_scripts', array(AdminScriptStylesRunner::class, 'run'));
        add_action('admin_enqueue_scripts', array(AdminScriptStylesRunner::class, 'enqueueForAllPages'), 1100);

        // New and edit post
        PostRunner::setContainer($this->container);
        add_action('load-post.php', array(PostRunner::class, 'run'));
        add_action('load-post-new.php', array(PostRunner::class, 'run'));

        // Action links on /wp-admin/plugins.php
        PluginsRunner::setContainer($this->container);
        add_filter('plugin_action_links_' . $this->getBasename(), array(PluginsRunner::class, 'addActionLinks'));

        // WordPress post auto save
        add_filter('heartbeat_received', array( '\Setka\Editor\Admin\Service\SavePost', 'heartbeatReceived'), 10, 2);

        NoticesStackRunner::setContainer($this->container);
        add_action('admin_notices', array(NoticesStackRunner::class, 'run'));

        /**
         * Catch Setka API requests (webhooks).
         */
        add_action('admin_init', array( '\Setka\Editor\Admin\Service\Webhooks', 'run' ));

        MetaBoxesStackRunner::setContainer($this->container);
        add_action('current_screen', array(MetaBoxesStackRunner::class, 'run'));

        DashBoardMetaBoxesStackRunner::setContainer($this->container);
        add_action('wp_dashboard_setup', array(DashBoardMetaBoxesStackRunner::class, 'run'));

        add_filter('wp_kses_allowed_html', array('Setka\Editor\Admin\Service\Kses', 'allowedHTML'), 10, 2);
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
        $container->setParameter(
            'wp.plugins.setka_editor.cache_dir',
            (defined('SETKA_EDITOR_CACHE_DIR')) ? SETKA_EDITOR_CACHE_DIR : false
        );

        // Folder with Twig templates.
        $container->setParameter(
            'wp.plugins.setka_editor.templates_path',
            path_join($this->getDir(), 'twig-templates')
        );

        $container->setParameter(
            'wp.plugin.setka_editor.languages_path',
            dirname($this->getBasename()) . '/languages'
        );

        $container
            ->register('wp.plugins.setka_editor.translations', PluginTranslations::class)
            ->addArgument(self::NAME)
            ->addArgument('%wp.plugin.setka_editor.languages_path%');

        $container
            ->register(Activation::class, Activation::class);

        // Factory for Twig.
        $container
            ->register(TwigFactory::class, TwigFactory::class)
            ->addArgument('%wp.plugins.setka_editor.cache_dir%')
            ->addArgument('%wp.plugins.setka_editor.templates_path%');

        // Twig itself prepared for rendering Symfony Forms.
        $container
            ->register('wp.plugins.setka_editor.twig')
            ->setFactory(array(
                new Reference(TwigFactory::class),
                'create'
            ))
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

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(FilesManagerCronEvent::class, FilesManagerCronEvent::class);

        $container
            ->register(FilesQueueCronEvent::class, FilesQueueCronEvent::class);

        $container
            ->register(SendFilesStatCronEvent::class, SendFilesStatCronEvent::class);

        $container
            ->register(SetkaPostCreatedCronEvent::class, SetkaPostCreatedCronEvent::class);

        $container
            ->register(SyncAccountCronEvent::class, SyncAccountCronEvent::class);

        $container
            ->register(UpdateAnonymousAccountCronEvent::class, UpdateAnonymousAccountCronEvent::class);

        $container
            ->register(UserSignedUpCronEvent::class, UserSignedUpCronEvent::class);

        $container->setParameter(
            'wp.plugins.setka_editor.all_cron_events',
            array(
                new Reference(FilesManagerCronEvent::class),
                new Reference(FilesQueueCronEvent::class),
                new Reference(SendFilesStatCronEvent::class),
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
            ));


        // Files
        $container
            ->register(Admin\Pages\Files\FilesPage::class, Admin\Pages\Files\FilesPage::class);

        // Root
        $container
            ->register(Admin\Pages\SetkaEditor\SetkaEditorPage::class, Admin\Pages\SetkaEditor\SetkaEditorPage::class)
            ->addArgument(new Reference(AccountPage::class))
            ->addArgument(new Reference(SignUpPage::class));

        $container
            ->register(AccountPage::class, AccountPage::class)
            ->addMethodCall('setTabs', array(new Reference('wp.plugins.setka_editor.admin.account_tabs')));

        $container
            ->register(SignUpPage::class, SignUpPage::class)
            ->addMethodCall('setTabs', array(new Reference('wp.plugins.setka_editor.admin.sign_up_tabs')))
            ->addMethodCall('setNoticesStack', array(new Reference('wp.plugins.setka_editor.notices_stack')));

        // Settings
        $container
            ->register(Admin\Pages\Settings\SettingsPage::class)
            ->setFactory(array(PluginPagesFactory::class, 'create'))
            ->addArgument(Admin\Pages\Settings\SettingsPage::class)
            ->addArgument($container);

        // Upgrade
        $container
            ->register(Admin\Pages\Upgrade\Upgrade::class, Admin\Pages\Upgrade\Upgrade::class);

        // WordPress plugins
        $container
            ->register(Plugins::class, Plugins::class)
            ->addArgument(new Reference(Admin\Pages\SetkaEditor\SetkaEditorPage::class));

        // Edit and New post
        $container
            ->register(PostRunner::class, PostRunner::class);

        $container
            ->register(AdminScriptStyles::class, AdminScriptStyles::class)
            ->addArgument($this->getUrl())
            ->addArgument(WordPressFeatures::isScriptDebug());

        $container
            ->register(CronSchedules::class, CronSchedules::class);

        $container
            ->register(ScriptStyles::class, ScriptStyles::class)
            ->addArgument($this->getUrl())
            ->addArgument(WordPressFeatures::isScriptDebug());

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register('wp.plugins.setka_editor.uninstall', Uninstall::class)
            ->addMethodCall('setCronEvents', array('%wp.plugins.setka_editor.all_cron_events%'))
            ->addMethodCall('setSuppressExceptions', array(true));

        $container
            ->register(WhiteLabel::class, WhiteLabel::class);

        $container
            ->register(AccessTab::class, AccessTab::class);

        $container
            ->register(AccountTab::class, AccountTab::class);

        $container
            ->register(StartTab::class, StartTab::class);

        $container
            ->register('wp.plugins.setka_editor.admin.account_tabs', Tabs::class)
            ->addMethodCall('addTab', array(new Reference(AccountTab::class)))
            ->addMethodCall('addTab', array(new Reference(AccessTab::class)));

        $container
            ->register('wp.plugins.setka_editor.admin.sign_up_tabs', Tabs::class)
            ->addMethodCall('addTab', array(new Reference(StartTab::class)))
            ->addMethodCall('addTab', array(new Reference(AccessTab::class)));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(AfterSignInNotice::class, AfterSignInNotice::class);

        $container
            ->register(InvitationToRegisterNotice::class)
            ->setFactory(array(InvitationToRegisterNoticeFactory::class, 'create'))
            ->addArgument($this->container);

        $container
            ->register(PaymentErrorNotice::class, PaymentErrorNotice::class);

        $container
            ->register(SetkaEditorCantFindResourcesNotice::class, SetkaEditorCantFindResourcesNotice::class);

        $container
            ->register(SetkaEditorThemeDisabledNotice::class, SetkaEditorThemeDisabledNotice::class);

        $container
            ->register(SubscriptionBlockedNotice::class, SubscriptionBlockedNotice::class);

        $container
            ->register(YouCanRegisterNotice::class, YouCanRegisterNotice::class);

        $container->setParameter(
            'wp.plugins.setka_editor.all_notices',
            array(
                new Reference(AfterSignInNotice::class),
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
            ->addMethodCall('setNotices', array('%wp.plugins.setka_editor.all_notices%'));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(InvitationToRegisterDashboardMetaBox::class)
            ->setFactory(array(InvitationToRegisterDashboardMetaBoxFactory::class, 'create'))
            ->addArgument($container);

        $container
            ->register(DashBoardMetaBoxesStack::class, DashBoardMetaBoxesStack::class)
            ->addMethodCall('setContainer', array($container));

        //--------------------------------------------------------------------------------------------------------------

        $container
            ->register(InvitationToRegisterMetaBox::class)
            ->setFactory(array(InvitationToRegisterMetaBoxFactory::class, 'create'))
            ->addArgument($container);

        $container
            ->register(MetaBoxesStack::class, MetaBoxesStack::class)
            ->addMethodCall('setContainer', array($container));
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
