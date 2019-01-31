<?php
namespace Setka\Editor\Admin\Cron;

use Korobochkin\WPKit\Cron\AbstractCronEvent;
use Psr\Log\LoggerInterface;
use Setka\Editor\Admin\Service\ContinueExecution\OutOfTimeException;
use Setka\Editor\Plugin;
use Setka\Editor\Service\AMP\AMPStylesManager;

class AMPStylesQueueCronEvent extends AbstractCronEvent
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
        $this
            ->setTimestamp(1)
            ->setRecurrence('hourly')
            ->setName(Plugin::_NAME_ . '_cron_amp_styles_queue');
    }

    public function execute()
    {
        $this->logger->debug('Start executing cron event.', $this->generateContext());
        try {
            $this->ampStylesManager->checkPendingFiles();
            $this->logger->debug('Finished executing cron event.', $this->generateContext());
        } catch (OutOfTimeException $exception) {
            // Current cron process is obsolete. Stop execution.
            $this->logException($exception);
        } catch (\Exception $exception) {
            $this->logException($exception);
            $this->ampStylesManager->saveFailure($exception);
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
     * Re-add event.
     *
     * @return $this For chain calls.
     */
    public function restart()
    {
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
