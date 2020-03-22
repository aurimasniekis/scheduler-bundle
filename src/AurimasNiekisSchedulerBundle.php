<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle;

use AurimasNiekis\SchedulerBundle\Console\Command\SchedulerExecuteCommand;
use AurimasNiekis\SchedulerBundle\Console\Command\SchedulerListCommand;
use AurimasNiekis\SchedulerBundle\Console\Command\SchedulerRunCommand;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class AurimasNiekisSchedulerBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new class() extends Extension {
            public function load(array $configs, ContainerBuilder $container): void
            {
                $container->registerForAutoconfiguration(ScheduledJobInterface::class)
                    ->addTag('scheduler.job');

                $container->register(Scheduler::class)
                    ->setArgument(0, new TaggedIteratorArgument('scheduler.job'))
                    ->setAutowired(true)
                    ->setPublic(true);

                $container->setAlias('scheduler', Scheduler::class);

                $container->register(SchedulerListCommand::class)
                    ->setAutowired(true)
                    ->addTag('console.command');

                $container->register(SchedulerExecuteCommand::class)
                    ->setAutowired(true)
                    ->addTag('console.command');

                $container->register(SchedulerRunCommand::class)
                    ->setAutowired(true)
                    ->addTag('console.command');
            }

            public function getAlias()
            {
                return 'scheduler';
            }
        };
    }
}
