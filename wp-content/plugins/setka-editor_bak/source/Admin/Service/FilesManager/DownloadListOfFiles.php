<?php
namespace Setka\Editor\Admin\Service\FilesManager;

use Korobochkin\WPKit\Options\OptionInterface;
use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Actions\GetFilesAction;
use Setka\Editor\Admin\Service\SetkaEditorAPI\API;
use Setka\Editor\Admin\Service\SetkaEditorAPI\AuthCredits;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class DownloadListOfFiles
 */
class DownloadListOfFiles
{
    /**
     * @var API
     */
    protected $api;

    /**
     * @var OptionInterface
     */
    protected $tokenOption;

    /**
     * @var GetFilesAction
     */
    protected $action;

    /**
     * DownloadListOfFiles constructor.
     *
     * @param $api API
     * @param $tokenOption OptionInterface
     */
    public function __construct(API $api, OptionInterface $tokenOption)
    {
        $this->api         = $api;
        $this->tokenOption = $tokenOption;
    }

    /**
     * @throws \Exception
     * @return $this
     */
    public function execute()
    {
        $this->api->setAuthCredits(new AuthCredits($this->tokenOption->get()));

        $action = $this->action = new GetFilesAction();
        $this->api->request($action);

        if(count($action->getErrors()) !== 0) {
            throw new \Exception();
        } else {
            $filesOption = new FilesOption();
            $filesOption->updateValue($action->getResponse()->content->all());
        }

        return $this;
    }
}
