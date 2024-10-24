<?php

use App\Library\Route\Router;

require './bootstrap.php';

try {
    $route = new Router;
    $route->add('/', 'GET', 'HomeController:index');
    $route->add('/process', 'POST', 'ProcessDocsController:process');

    echo $route->init();
} catch (Exception $e) {
    dump($e->getMessage().'::'.$e->getFile().'::'.$e->getLine());
}
