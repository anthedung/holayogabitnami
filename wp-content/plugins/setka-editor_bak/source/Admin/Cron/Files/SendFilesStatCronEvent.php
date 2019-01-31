<?php
namespace Setka\Editor\Admin\Cron\Files;

use Korobochkin\WPKit\Cron\AbstractCronSingleEvent;
use Korobochkin\WPKit\Options\OptionInterface;
use Setka\Editor\Admin\Service\FilesManager\FilesManager;
use Setka\Editor\Admin\Service\SetkaEditorAPI\Actions\SendFilesStatAction;
use Setka\Editor\Admin\Service\SetkaEditorAPI\AuthCredits;
use Setka\Editor\Admin\Service\SetkaEditorAPI;
use Setka\Editor\Service\PostStatuses;
use Setka\Editor\Plugin;
use Setka\Editor\Service\SetkaAccount\SetkaEditorAccount;

/**
 * Class SendFilesStatCronEvent
 */
class SendFilesStatCronEvent extends AbstractCronSingleEvent
{
    /**
     * @var SetkaEditorAccount
     */
    protected $setkaEditorAccount;

    /**
     * @var SetkaEditorAPI\API
     */
    protected $setkaEditorAPI;

    /**
     * @var OptionInterface
     */
    protected $useLocalFilesOption;

    /**
     * @var FilesManager
     */
    protected $filesManager;

    /**
     * SendFilesStatCronEvent constructor.
     */
    public function __construct()
    {
        $this
            ->immediately()
            ->setName(Plugin::_NAME_ . '_cron_files_send_files_stat');
    }

    public function execute()
    {
        if(!$this->setkaEditorAccount->isLoggedIn()) {
            return $this;
        }

        $this->setkaEditorAPI
            ->setAuthCredits(
                new AuthCredits(
                    $this->setkaEditorAccount->getTokenOption()->get()
                )
            );
        $action = new SendFilesStatAction();

        $stat = $this->filesManager->getFilesStat();

        $statFixed = array(
            'downloaded' => 0,
            'failed' => 0,
            'archived' => 0,
            'queued' => 0,
            'total' => 0,
        );

        $statFixed['downloaded'] = $stat[PostStatuses::PUBLISH];
        $statFixed['failed']     = $stat[PostStatuses::PENDING];
        $statFixed['archived']   = $stat[PostStatuses::ARCHIVE];
        $statFixed['queued']     = $stat[PostStatuses::DRAFT];
        $statFixed['total']      = $stat[PostStatuses::ANY];

        $statFixed['files_source'] = 'cdn';

        if(true === $this->useLocalFilesOption->get()) {
            $statFixed['files_source'] = 'self';
        }

        $action->setRequestDetails(array(
            'body' => array(
                'event' => $statFixed,
            ),
        ));

        $this->setkaEditorAPI->request($action);
    }

    /**
     * @return SetkaEditorAccount
     */
    public function getSetkaEditorAccount()
    {
        return $this->setkaEditorAccount;
    }

    /**
     * @param SetkaEditorAccount $setkaEditorAccount
     * @return $this
     */
    public function setSetkaEditorAccount(SetkaEditorAccount $setkaEditorAccount)
    {
        $this->setkaEditorAccount = $setkaEditorAccount;
        return $this;
    }

    /**
     * @return SetkaEditorAPI\API
     */
    public function getSetkaEditorAPI()
    {
        return $this->setkaEditorAPI;
    }

    /**
     * @param SetkaEditorAPI\API $setkaEditorAPI
     * @return $this
     */
    public function setSetkaEditorAPI(SetkaEditorAPI\API $setkaEditorAPI)
    {
        $this->setkaEditorAPI = $setkaEditorAPI;
        return $this;
    }

    /**
     * @return OptionInterface
     */
    public function getUseLocalFilesOption()
    {
        return $this->useLocalFilesOption;
    }

    /**
     * @param OptionInterface $useLocalFilesOption
     * @return $this
     */
    public function setUseLocalFilesOption(OptionInterface $useLocalFilesOption)
    {
        $this->useLocalFilesOption = $useLocalFilesOption;
        return $this;
    }

    /**
     * @return FilesManager
     */
    public function getFilesManager()
    {
        return $this->filesManager;
    }

    /**
     * @param FilesManager $filesManager
     * @return $this
     */
    public function setFilesManager(FilesManager $filesManager)
    {
        $this->filesManager = $filesManager;
        return $this;
    }
}
