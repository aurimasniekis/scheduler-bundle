<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle;

use Cron\CronExpression;
use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Scheduler
{
    /** @var ScheduledJobInterface[] */
    private array $scheduledJobs;

    private LoggerInterface $logger;

    public function __construct(iterable $scheduledJobs = [], LoggerInterface $logger = null)
    {
        $this->scheduledJobs = [];
        $this->logger        = $logger ?? new NullLogger();

        foreach ($scheduledJobs as $scheduledJob) {
            $this->addScheduledJob($scheduledJob);
        }
    }

    public function addScheduledJob(ScheduledJobInterface $scheduledJob): self
    {
        $name = $this->getScheduledJobName($scheduledJob);

        if (false === is_callable($scheduledJob)) {
            throw new InvalidArgumentException('ScheduledJob "' . $name . '" must implement `__invoke` method');
        }

        $this->scheduledJobs[$name] = $scheduledJob;

        return $this;
    }

    private function getScheduledJobName(ScheduledJobInterface $scheduledJob): string
    {
        if ($scheduledJob instanceof NamedScheduledJobInterface) {
            return $scheduledJob->getName();
        }

        return get_class($scheduledJob);
    }

    public function getScheduledJob(string $name): ScheduledJobInterface
    {
        if (false === isset($this->scheduledJobs[$name])) {
            throw new InvalidArgumentException('ScheduledJob with name "' . $name . '" does not exist!');
        }

        return $this->scheduledJobs[$name];
    }

    public function execute(DateTimeInterface $at = null): void
    {
        $at = $at ?? new DateTime();

        $this->logger->debug('Executing Scheduler', ['at' => $at]);

        $dueJobs = $this->getDueJobs($at);

        $this->logger->debug(
            'Scheduled Events found to execute',
            ['count' => count($dueJobs), 'at' => $at]
        );

        foreach ($dueJobs as $dueJob) {
            $this->executeJob($dueJob);
        }
    }

    public function getDueJobs(DateTimeInterface $at): array
    {
        $dueJobs = [];

        foreach ($this->getScheduledJobs() as $name => $scheduledJob) {
            $cronExpression = CronExpression::factory($scheduledJob->getSchedulerExpresion());

            if ($cronExpression->isDue($at)) {
                $dueJobs[$name] = $scheduledJob;
            }
        }

        return $dueJobs;
    }

    /**
     * @return ScheduledJobInterface[]
     */
    public function getScheduledJobs(): array
    {
        return $this->scheduledJobs;
    }

    /**
     * @param ScheduledJobInterface|callable $scheduledJob
     */
    public function executeJob(ScheduledJobInterface $scheduledJob): void
    {
        $this->logger->debug('Executing scheduled job', ['scheduledJob' => $scheduledJob]);

        $scheduledJob();

        $this->logger->debug('Executing scheduled job finished', ['scheduledJob' => $scheduledJob]);
    }
}
