<?php
namespace Setka\Editor\Admin\Pages\Settings\Files;

use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Options\Files\FileSyncFailureOption;
use Setka\Editor\Admin\Options\Files\FileSyncOption;
use Setka\Editor\Admin\Options\Files\FileSyncStageOption;
use Setka\Editor\Admin\Options\Files\UseLocalFilesOption;
use Setka\Editor\Admin\Prototypes\Pages;
use Setka\Editor\Admin\Service\FilesManager\FilesManagerFactory;
use Setka\Editor\Entries\PostStatuses;
use Setka\Editor\Entries\SetkaEditorFilePostType;
use Setka\Editor\Plugin;

class FilesPage extends Pages\SubMenuPage {

	public function __construct() {
		$this->setParentSlug(Plugin::NAME);
		$this->setPageTitle(__('Files', Plugin::NAME));
		$this->setMenuTitle($this->getPageTitle());
		$this->setCapability('manage_options');
		$this->setMenuSlug(Plugin::NAME . '-files');

		$this->setName('files');
	}

	public function lateConstruct() {
		$manager = FilesManagerFactory::create();
        $attributes = $manager->getFilesStat();

		$attributes = array(
			'posts' => $attributes,
            'options' => $this->lateConstructOptions(),
		);

		$view = new Pages\Views\TwigPageView();
		$view->setTemplate('admin/settings/setka-editor/files/page.html.twig');
		$view->setContext($attributes);
		$this->setView($view);
	}

	protected function lateConstructOptions() {
	    $options = array();

	    $option = new FilesOption();
	    $value = $option->getValue();
	    if(is_array($value) && !empty($value)) {
            $value = '[contains list of files]';
        } else {
	        $value = 'UNKNOWN VALUE';
        }
        $key = new \ReflectionClass($option);
	    $key = $key->getShortName();
	    $options[$key] = $value;

        $option = new FileSyncFailureOption();
        $key = new \ReflectionClass($option);
        $key = $key->getShortName();
        $options[$key] = $option->getValue();

        $option = new FileSyncOption();
        $key = new \ReflectionClass($option);
        $key = $key->getShortName();
        $options[$key] = $option->getValue();

        $option = new FileSyncStageOption();
        $key = new \ReflectionClass($option);
        $key = $key->getShortName();
        $options[$key] = $option->getValue();

        $option = new UseLocalFilesOption();
        $key = new \ReflectionClass($option);
        $key = $key->getShortName();
        $options[$key] = $option->getValue();

        return $options;
    }
}
