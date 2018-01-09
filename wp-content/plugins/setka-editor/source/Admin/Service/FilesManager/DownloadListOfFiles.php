<?php
namespace Setka\Editor\Admin\Service\FilesManager;

use Setka\Editor\Admin\Options\Files\FilesOption;
use Setka\Editor\Admin\Prototypes\Options\OptionInterface;
use Setka\Editor\Admin\Service\SetkaAPI\Actions\GetFilesAction;
use Setka\Editor\Admin\Service\SetkaAPI\API;
use Setka\Editor\Admin\Service\SetkaAPI\APIFactory;
use Setka\Editor\Admin\Service\SetkaAPI\AuthCredits;

class DownloadListOfFiles {

    /**
     * @var OptionInterface
     */
    protected $tokenOption;

    /**
     * @var API
     */
    protected $api;

    /**
     * @var GetFilesAction
     */
    protected $action;

    /**
     * DownloadListOfFiles constructor.
     *
     * @param $tokenOption
     */
    public function __construct(OptionInterface $tokenOption) {
        $this->tokenOption = $tokenOption;
    }

    public function execute() {
        // Prepare API
        $api = $this->api = APIFactory::create();
        $api->setAuthCredits(new AuthCredits($this->tokenOption->getValue()));

        // Make request
        $action = $this->action = new GetFilesAction();
        $api->request($action);

        // Processing request
        if($action->getErrors()->hasErrors()) {
            throw new \Exception();
        } else {
            $filesOption = new FilesOption();
            $filesOption->updateValue($action->getResponse()->content->all());
        }

        return $this;
    }
}
