#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__. ' /../vendor/autoload.php';

use Leovie\PhpstanBaselineGuard\Command\PhpstanBaselineGuardCommand;
use Leovie\PhpstanBaselineGuard\Neon\NeonDecoder;
use Leovie\PhpstanBaselineGuard\Parser\BaselineParser;
use Leovie\PhpstanBaselineGuard\Service\BaselineGuardService;
use Symfony\Component\Console\Application;

$application = new Application('phpstan-baseline-guard', '1.0.0');
$command = new PhpstanBaselineGuardCommand(
    new BaselineGuardService(
        new NeonDecoder(),
        new BaselineParser()
    )
);

$application->add($command);

/** @var string $commandName */
$commandName = $command->getName();
$application->setDefaultCommand($commandName, true);
$application->run();