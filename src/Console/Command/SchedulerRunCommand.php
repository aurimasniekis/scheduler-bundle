<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle\Console\Command;

use AurimasNiekis\SchedulerBundle\Scheduler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class SchedulerRunCommand extends Command
{
    protected static $defaultName = 'scheduler:run';

    private Scheduler $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        parent::__construct();

        $this->scheduler = $scheduler;
    }

    protected function configure(): void
    {
        $this->setDescription('Executes scheduled jobs which satisfies scheduler expresion');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->scheduler->execute();

        return 0;
    }
}
