<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
interface NamedScheduledJobInterface extends ScheduledJobInterface
{
    public function getName(): string;
}
