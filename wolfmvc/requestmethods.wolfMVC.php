<?php

namespace WolfMVC
{
    class RequestMethods
    {
        private function __construct()
        {
            // do nothing
        }
        
        private function __clone()
        {
            // do nothing
        }
        
        public static function get($key, $default = "")
        {
            if (isset($_GET[$key]))
            {
                return $_GET[$key];
            }
            return $default;
        }
        
        public static function post($key, $default = "")
        {
            if (isset($_POST[$key]))
            {
                return $_POST[$key];
            }
//            parse_str(file_get_contents('php://input'), $requestData);
//            if (!empty($requestData[$key]))
//            {
//                return $requestData[$key];
//            }
            return $default;
        }
        
        public static function server($key, $default = "")
        {
            if (isset($_SERVER[$key]))
            {
                return $_SERVER[$key];
            }
            return $default;
        }
        
        public static function cookie($key, $default = "")
        {
            if (isset($_COOKIE[$key]))
            {
                return $_COOKIE[$key];
            }
            return $default;
        }
    }
}