<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
interface ScheduledJobInterface
{
    public function getSchedulerExpresion(): string;
}
