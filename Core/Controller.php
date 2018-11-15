<?php
    namespace App\Core;

    class Controller {
        private $dbc;
        private $data = [];
        private $session;

        public function __construct(\App\Core\DatabaseConnection &$dbc) {
            $this->dbc = $dbc;
        }

        public function __pre(){
            
        }

        public final function &getSession() : \App\Core\Session\Session {
            return $this->session;
        }

        public final function setSession(\App\Core\Session\Session &$session) {
            $this->session = $session;
        }

        final public function &getDatabaseConnection() : \App\Core\DatabaseConnection {
            return $this->dbc;
        }

        final protected function set(string $name, $value) {
            if (!\preg_match('/^[a-z]+(?:[A-Z][a-z0-9]+)*$/', $name)) {
                throw new \Exception('Nije ispravno ime promenljive.');
            }

            $this->data[$name] = $value;
        }

        final public function getData(): array {
            return $this->data;
        }

        final protected function redirect(string $path, int $code = 303) {
            ob_clean();
            header('Location: ' . $path, true, $code);
            exit;
        }
    }
    