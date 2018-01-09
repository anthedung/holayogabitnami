<?php
namespace Setka\Editor\CLI;

use Setka\Editor\CLI\Commands\DummyCommand;
use Setka\Editor\CLI\Commands\EditorConfigCommand;
use Setka\Editor\CLI\Commands\FilesArchiveCommand;
use Setka\Editor\CLI\Commands\FilesCreateCommand;
use Setka\Editor\CLI\Commands\FilesDeleteCommand;
use Setka\Editor\CLI\Commands\FilesDownloadCommand;
use Setka\Editor\CLI\Commands\FilesSyncCommand;
use Setka\Editor\Plugin;

class Loader {

	public static function run() {
		if( defined('WP_CLI') && WP_CLI === true ) {
			self::registerCommands();
		}
	}

	public static function registerCommands() {
		\WP_CLI::add_command(Plugin::NAME, 'Setka\Editor\CLI\Commands\AccountCommand');

		// Please keep this order of registering commands
		// Because WP CLI discard commands. For example, if you register
		// 1. "files create"
		// 2. "files"
		// Then "files create" will not registered.
		\WP_CLI::add_command(Plugin::NAME . ' files', FilesDownloadCommand::class);
		\WP_CLI::add_command(Plugin::NAME . ' files create', FilesCreateCommand::class);
		\WP_CLI::add_command(Plugin::NAME . ' files delete', FilesDeleteCommand::class);
		\WP_CLI::add_command(Plugin::NAME . ' files archive-all', FilesArchiveCommand::class);
        \WP_CLI::add_command(Plugin::NAME . ' sync', FilesSyncCommand::class);

		\WP_CLI::add_command(Plugin::NAME . ' editor-config', EditorConfigCommand::class);
	}
}
