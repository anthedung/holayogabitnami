<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Psr\Log\LoggerInterface;
use Setka\Editor\Admin\Service\ContinueExecution\OutOfTimeException;
use Setka\Editor\Plugin;
use Setka\Editor\Service\AMP\AMPStylesManager;
use Setka\Editor\Service\AMP\Exceptions\PendingFilesException;

/**
 * Class AMPStylesCronEvent downloads styles for AMP pages.
 */
class AMPStylesCronEvent extends AbstractCronEvent
{
    /**
     * @var AMPStylesManager
     */
    protected $ampStylesManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct()
    {
        $this->setTimestamp(1);
        $this->setRecurrence(Plugin::_NAME_ . '_every_minute');
        $this->setName(Plugin::_NAME_.'_cron_amp_styles');
    }

    public function execute()
    {
        $this->logger->debug('Start executing cron event.', $this->generateContext());
        try {
            $this->ampStylesManager->run();
        } catch (OutOfTimeException $exception) {
            // Current cron process is obsolete. Stop execution.
            $this->logException($exception);
        } catch (PendingFilesException $exception) {
            // All files in queue downloaded but there is pending files.
            $this->logException($exception);
        } catch (\Exception $exception) {
            $this->ampStylesManager->saveFailure($exception);
            $this->logException($exception);
        }
        $this->logger->debug('Method "execute" in cron class finished.', $this->generateContext());
    }

    /**
     * @param \Exception $exception
     * @return $this
     */
    public function logException(\Exception $exception)
    {
        $context              = $this->generateContext();
        $context['exception'] = $exception;

        $this->logger->debug('Catched exception in cron event.', $context);
        return $this;
    }

    /**
     * @return array Context for logger.
     */
    public function generateContext()
    {
        return array(
            'name' => $this->getName(),
            'class' => get_class($this),
        );
    }

    /**
     * Restart sync.
     *
     * @return $this For chain calls.
     */
    public function restart()
    {
        $this->getAmpStylesManager()->resetSync();
        $result = $this->unscheduleAll()->schedule();

        $context                    = $this->generateContext();
        $context['schedule_result'] = $result;
        $this->logger->debug('Cron event re-scheduled.', $context);

        return $this;
    }

    /**
     * @return AMPStylesManager
     */
    public function getAmpStylesManager()
    {
        return $this->ampStylesManager;
    }

    /**
     * @param AMPStylesManager $ampStylesManager
     * @return $this
     */
    public function setAmpStylesManager(AMPStylesManager $ampStylesManager)
    {
        $this->ampStylesManager = $ampStylesManager;
        return $this;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }
}
