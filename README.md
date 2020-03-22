# Scheduler Bundle

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Build Status][ico-travis]][link-travis]
[![Code Quality][ico-quality]][link-scrutinizer]
[![Code Coverage][ico-coverage]][link-scrutinizer]
[![Mutation testing badge][ico-mutation]][link-mutator]
[![Total Downloads][ico-downloads]][link-downloads]

[![Email][ico-email]][link-email]

A simple scheduler bundle for Symfony which provides a way to execute code at specific cron expressions without
modifying cron tab every time.


## Install

Via Composer

```bash
$ composer require aurimasniekis/scheduler-bundle
```

Add line to `bundle.php`:

```php
<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    // ...
    AurimasNiekis\SchedulerBundle\AurimasNiekisSchedulerBundle::class => ['all' => true],
];
```

Add the scheduler to cron tab to run every minute:

```bash
* * * * * /path/to/symfony/install/bin/console scheduler:run 1>> /dev/null 2>&1
```

## Usage

Scheduler Bundle uses Symfony Container `autoconfigure` feature which automatically registers all services
which implement `ScheduledJobInterface` or `NamedScheduledJobInterface` interface into Scheduler.

To create a scheduled job you have two options either simple Scheduled Job or Named Scheduled Job. First one uses
class name as job name, second provides method to define a job name.

```php
<?php

use AurimasNiekis\SchedulerBundle\ScheduledJobInterface;

class NamelessJob implements ScheduledJobInterface
{
    public function __invoke(): void
    {
        // Execute some logic        
    }

    public function getSchedulerExpresion(): string
    {
        return '*/5 * * * *'; // Run every 5 minutes   
    }
}
```

```php
<?php

use AurimasNiekis\SchedulerBundle\NamedScheduledJobInterface;

class NamedJob implements NamedScheduledJobInterface
{
    public function __invoke(): void
    {
        // Execute some logic        
    }

    public function getName(): string
    {
        return 'named_job';
    }

    public function getSchedulerExpresion(): string
    {
        return '*/5 * * * *'; // Run every 5 minutes   
    }
}
```


## Console Commands

### `scheduler:list`

List all registered scheduled jobs, and their next scheduled run times

```bash
$ bin/console scheduler:list

+------------------------------+-------------+---------- Scheduled Jobs -------------------------------------------------------+
| Name                         | Expression  | Next Scheduled Run Times                                                        |
+------------------------------+-------------+---------------------------------------------------------------------------------+
| named_job                    | */5 * * * * | 2020-03-22T04:55:00+00:00, 2020-03-22T05:00:00+00:00, 2020-03-22T05:05:00+00:00 |
| App\ScheduledJob\NamelessJob | */5 * * * * | 2020-03-22T04:55:00+00:00, 2020-03-22T05:00:00+00:00, 2020-03-22T05:05:00+00:00 |
+------------------------------+-------------+---------------------------------------------------------------------------------+
```

### `scheduler:execute`

Executes a scheduled job manually

```bash
$ bin/console scheduler:execute named_job

Executing Scheduled Job: "named_job"
```

### `scheduler:run`

Command to run all scheduled jobs at this moment. (Used for cron job definition)

```bash
* * * * * /path/to/symfony/install/bin/console scheduler:run 1>> /dev/null 2>&1
```

## Testing


Run test cases

```bash
$ composer test
```

Run test cases with coverage (HTML format)


```bash
$ composer test-coverage
```

Run PHP style checker

```bash
$ composer cs-check
```

Run PHP style fixer

```bash
$ composer cs-fix
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.


## License

Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/aurimasniekis/scheduler-bundle.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/com/aurimasniekis/scheduler-bundle/master.svg?style=flat-square
[ico-quality]: https://img.shields.io/scrutinizer/quality/g/aurimasniekis/scheduler-bundle?style=flat-square
[ico-coverage]: https://img.shields.io/scrutinizer/coverage/g/aurimasniekis/scheduler-bundle?style=flat-square
[ico-mutation]: https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Faurimasniekis%2Fscheduler-bundle%2Fmaster
[ico-downloads]: https://img.shields.io/packagist/dt/aurimasniekis/scheduler-bundle.svg?style=flat-square
[ico-email]: https://img.shields.io/badge/email-aurimas@niekis.lt-blue.svg?style=flat-square

[link-travis]: https://travis-ci.com/aurimasniekis/scheduler-bundle
[link-packagist]: https://packagist.org/packages/aurimasniekis/scheduler-bundle
[link-scrutinizer]: https://scrutinizer-ci.com/g/aurimasniekis/scheduler-bundle
[link-mutator]: https://dashboard.stryker-mutator.io/reports/github.com/aurimasniekis/scheduler-bundle/master
[link-downloads]: https://packagist.org/packages/aurimasniekis/scheduler-bundle/stats
[link-email]: mailto:aurimas@niekis.lt
