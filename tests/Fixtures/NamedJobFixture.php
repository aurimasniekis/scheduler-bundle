<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle\Tests\Fixtures;

use AurimasNiekis\SchedulerBundle\NamedScheduledJobInterface;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class NamedJobFixture extends JobFixture implements NamedScheduledJobInterface
{
    public function getName(): string
    {
        return 'named';
    }
}
