<?php
    namespace App\Core;

    final class Route {
        private $httpMethod;
        private $pattern;
        private $controllerName;
        private $methodName;

        private function __construct(
            string $httpMethod,
            string $pattern,
            string $controllerName,
            string $methodName) {
            $this->httpMethod     = $httpMethod;
            $this->pattern        = $pattern;
            $this->controllerName = $controllerName;
            $this->methodName     = $methodName;
        }

        public function getControllerName(): string {
            return $this->controllerName;
        }

        public function getMethodName(): string {
            return $this->methodName;
        }

        public static function get(
            string $pattern,
            string $controllerName,
            string $methodName) {
            return new Route('GET', $pattern, $controllerName, $methodName);
        }

        public static function post(
            string $pattern,
            string $controllerName,
            string $methodName) {
            return new Route('POST', $pattern, $controllerName, $methodName);
        }

        public static function any(
            string $pattern,
            string $controllerName,
            string $methodName) {
            return new Route('GET|POST', $pattern, $controllerName, $methodName);
        }

        public function matches(string $httpMethod, string $url): bool {
            if (!\preg_match('/^' . $this->httpMethod . '$/', $httpMethod)) {
                return false;
            }

            if (!\preg_match($this->pattern, $url)) {
                return false;
            }

            return true;
        }

        public function &extractArguments(string $url) : array {
            $matches = [];
            $arguments = [];

            preg_match_all($this->pattern, $url, $matches);

            array_shift($matches);
            if (isset($matches[0])) {
                $arguments = $matches[0];
            }

            if (isset($matches[2])) {
                $arguments = $matches;
            }

            return $arguments;
        }
    }
