<?php

namespace WolfMVC\Session\Driver {

    use WolfMVC\Session as Session;

    class Server extends Session\Driver {

        /**
         * @readwrite
         */
        protected $_prefix = "app_";

        public function __construct($options = array()) {
            parent::__construct($options);
            session_start();
        }

        public function get($key, $default = null) {
            if (isset($_SESSION[$this->prefix . $key])) {
                return $_SESSION[$this->prefix . $key];
            }

            return $default;
        }

        public function set($key, $value) {
            $_SESSION[$this->prefix . $key] = $value;
            return $this;
        }

        public function erase($key) {
            $_SESSION[$this->prefix . $key] = null;
            unset($_SESSION[$this->prefix . $key]);
            return $this;
        }

        public function eraseall() {
            foreach ($_SESSION as $key => $value) {
                if (substr($key, 0, strlen($this->_prefix)) === $this->_prefix) {
                    unset($_SESSION[$key]);
                }
            }

            return $this;
        }

    }
}   