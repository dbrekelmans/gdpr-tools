<?php

require __DIR__ . '/vendor/autoload.php';

use GdprTools\Command\AnonymiseCommand;
use GdprTools\Command\TruncateCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new TruncateCommand());
$application->add(new AnonymiseCommand());

/** @noinspection PhpUnhandledExceptionInspection */
$application->run();
