<?php
namespace App\Core;

final class Router {
    private $routes = [];

    public function add(Route $route) {
        $this->routes[] = $route;
    }

    public function find(string $httpMethod, string $url): ?Route {
        foreach ($this->routes as $route) {
            if ($route->matches($httpMethod, $url)) {
                return $route;
            }
        }

        return null;
    }
}
