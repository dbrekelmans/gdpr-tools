<?php

require __DIR__ . '/vendor/autoload.php';

use GdprTools\Command\AnonymiseCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->add(new AnonymiseCommand());

/** @noinspection PhpUnhandledExceptionInspection */
$application->run();
