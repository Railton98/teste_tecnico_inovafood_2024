<?php

declare(strict_types=1);

namespace App\Library\Route;

use App\Library\Controller\Controller;
use Exception;

class Router
{
    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    public function add(string $uri, string $request, string $controller): void
    {
        $this->routes[] = new Route($uri, $request, $controller);
    }

    /**
     * @throws Exception
     */
    public function init()
    {
        foreach ($this->routes as $route) {
            if ($route->match()) {
                return (new Controller)->call($route);
            }
        }

        return (new Controller)->call(new Route('/404', 'GET', 'NotFoundController:index'));
    }
}
