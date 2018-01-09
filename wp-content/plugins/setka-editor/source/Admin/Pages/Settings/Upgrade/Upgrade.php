<?php
namespace Setka\Editor\Admin\Pages\Settings\Upgrade;

use Setka\Editor\Admin\Prototypes\Pages;
use Setka\Editor\Plugin;

class Upgrade extends Pages\SubMenuPage {

	public function __construct() {
		$this->setParentSlug(Plugin::NAME);
		$this->setPageTitle(__('Upgrade plan', Plugin::NAME ));
		$this->setMenuTitle($this->getPageTitle());
		$this->setCapability('manage_options');
		$this->setMenuSlug(Plugin::NAME . '-upgrade');

		$this->setName('upgrade');
	}
}