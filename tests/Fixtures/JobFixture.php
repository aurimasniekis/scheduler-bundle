<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle\Tests\Fixtures;

use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class JobFixture implements ScheduledJobInterface
{
    private string $expresion;
    private bool   $ran;

    public function __construct(string $expresion)
    {
        $this->expresion = $expresion;
        $this->ran       = false;
    }

    public function isRan(): bool
    {
        return $this->ran;
    }

    public function getSchedulerExpresion(): string
    {
        return $this->expresion;
    }

    public function __invoke(): void
    {
        $this->ran = true;
    }
}
