<?php

declare(strict_types=1);

namespace AurimasNiekis\SchedulerBundle\Tests;

use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;
use AurimasNiekis\SchedulerBundle\Scheduler;
use AurimasNiekis\SchedulerBundle\Tests\Fixtures\JobFixture;
use AurimasNiekis\SchedulerBundle\Tests\Fixtures\NamedJobFixture;
use DateTime;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class SchedulerTest extends TestCase
{
    public function testThatJobWithoutInvokeNotAccepted(): void
    {
        $scheduler = new Scheduler();

        $job = new class() implements ScheduledJobInterface {
            public function getSchedulerExpresion(): string
            {
                return '@daily';
            }
        };

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/ScheduledJob "[^"]+" must implement `__invoke` method/');

        $scheduler->addScheduledJob($job);
    }

    public function testJobNamedCorrectly(): void
    {
        $namedJob = new NamedJobFixture('@daily');
        $job      = new JobFixture('@daily');

        $scheduler = new Scheduler(
            [
                $namedJob,
                $job,
            ]
        );

        $expected = [
            'named'           => $namedJob,
            JobFixture::class => $job,
        ];

        $this->assertEquals($expected, $scheduler->getScheduledJobs());
    }

    public function testDueJobs(): void
    {
        $dueJob = new class('* * * * *') extends JobFixture {
        };
        $dueSecondJob = new class('* * * * *') extends JobFixture {
        };
        $notDueJob = new class('@daily') extends JobFixture {
        };

        $scheduler = new Scheduler([$dueJob, $dueSecondJob, $notDueJob]);

        $expected = [get_class($dueJob) => $dueJob, get_class($dueSecondJob) => $dueSecondJob];

        $this->assertEquals($expected, $scheduler->getDueJobs(new DateTime()));
    }

    public function testInvalidCronExpression(): void
    {
        $dueJob = new class('foo') extends JobFixture {
        };
        $scheduler = new Scheduler([$dueJob]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('foo is not a valid CRON expression');

        $scheduler->getDueJobs(new DateTime());
    }

    public function testGetScheduledJob(): void
    {
        $namedJob  = new NamedJobFixture('@daily');
        $scheduler = new Scheduler([$namedJob]);

        $this->assertEquals($namedJob, $scheduler->getScheduledJob('named'));
    }

    public function testShouldThrowErrorOnNonExisting(): void
    {
        $scheduler = new Scheduler();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('ScheduledJob with name "named" does not exist!');

        $scheduler->getScheduledJob('named');
    }

    public function testExecute(): void
    {
        $dueJob   = new NamedJobFixture('* * * * *');
        $dateTime = new DateTime();

        $logger = $this->createMock(LoggerInterface::class);

        $scheduler = new Scheduler([$dueJob], $logger);

        $logger->expects($this->at(0))
            ->method('debug')
            ->with('Executing Scheduler', ['at' => $dateTime]);

        $logger->expects($this->at(1))
            ->method('debug')
            ->with('Scheduled Events found to execute', ['count' => 1, 'at' => $dateTime]);

        $logger->expects($this->at(2))
            ->method('debug')
            ->with('Executing scheduled job', ['scheduledJob' => $dueJob]);

        $logger->expects($this->at(3))
            ->method('debug')
            ->with('Executing scheduled job finished', ['scheduledJob' => $dueJob]);

        $this->assertFalse($dueJob->isRan());

        $scheduler->execute($dateTime);

        $this->assertTrue($dueJob->isRan());
    }

    public function testExecuteSingleJob(): void
    {
        $dueJob = new NamedJobFixture('* * * * *');

        $logger = $this->createMock(LoggerInterface::class);

        $scheduler = new Scheduler([$dueJob], $logger);

        $logger->expects($this->at(0))
            ->method('debug')
            ->with('Executing scheduled job', ['scheduledJob' => $dueJob]);

        $logger->expects($this->at(1))
            ->method('debug')
            ->with('Executing scheduled job finished', ['scheduledJob' => $dueJob]);

        $this->assertFalse($dueJob->isRan());

        $scheduler->executeJob($dueJob);

        $this->assertTrue($dueJob->isRan());
    }
}
