<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle\Console\Command;

use AurimasNiekis\SchedulerBundle\Scheduler;
use Cron\CronExpression;
use DateTimeInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class SchedulerListCommand extends Command
{
    protected static $defaultName = 'scheduler:list';

    private Scheduler $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        parent::__construct();

        $this->scheduler = $scheduler;
    }

    protected function configure(): void
    {
        $this->setDescription('List defined Scheduler jobs');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schedulerJobs = $this->scheduler->getScheduledJobs();

        $table = new Table($output);
        $table->setHeaderTitle('Scheduled Jobs');
        $table->setHeaders(['Name', 'Expression', 'Next Scheduled Run Times']);

        foreach ($schedulerJobs as $name => $schedulerJob) {
            $cronExpression = CronExpression::factory($schedulerJob->getSchedulerExpresion());

            $table->addRow(
                [
                    $name,
                    $schedulerJob->getSchedulerExpresion(),
                    implode(
                        ', ',
                        array_map(
                            fn (DateTimeInterface $dateTime) => $dateTime->format('c'),
                            $cronExpression->getMultipleRunDates(3)
                        )
                    ),
                ]
            );
        }

        $table->render();

        return 0;
    }
}
