<?php

require __DIR__.'/../vendor/autoload.php';

Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2))
    ->load();
