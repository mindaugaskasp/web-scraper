#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Container\ContainerBindings;
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$bindings = new ContainerBindings();
$bindings->bind();

$application = new Application();
$application->add($bindings->getContainer()->get('command'));
$application->run();
