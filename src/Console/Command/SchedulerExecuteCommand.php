<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle\Console\Command;

use AurimasNiekis\SchedulerBundle\Scheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class SchedulerExecuteCommand extends Command
{
    protected static $defaultName = 'scheduler:execute';

    private Scheduler $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        parent::__construct();

        $this->scheduler = $scheduler;
    }

    protected function configure(): void
    {
        $this->setDescription('Execute specific scheduled job')
            ->addArgument('name', InputArgument::REQUIRED, 'Scheduled job name or className');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name         = $input->getArgument('name');
        $scheduledJob = $this->scheduler->getScheduledJob($name);

        $output->writeln('<info>Executing Scheduled Job: "</info><comment>' . $name . '</comment><info>"</info>');

        $this->scheduler->executeJob($scheduledJob);

        return 0;
    }
}
