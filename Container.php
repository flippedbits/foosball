<?php

class Container
{
    private $config;
    private $db;
    private $items;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function add($name, $item)
    {
        $this->items[$name] = $item;
    }

    public function get($name)
    {
        if (isset($this->items[$name])) {
            return $this->items[$name];
        }

        return null;
    }

    public function run($dispatcher)
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri        = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                echo 'not found';
                // ... 404 Not Found
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                echo 'not allowed';
                // ... 405 Method Not Allowed
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars    = $routeInfo[2];

                if (strpos($handler, ':') !== false) {
                    $bits = explode(':', $handler);

                    if (class_exists($bits[0])) {
                        $class = new $bits[0]($this);

                        if (method_exists($class, $bits[1])) {
                            return $class->$bits[1]($vars);
                        }
                    }
                } else if (function_exists($handler)) {
                    return $handler($this, $vars);
                }

                break;
        }
    }
}
