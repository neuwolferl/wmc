<?php

namespace WolfMVC\Router
{
    use WolfMVC\Base as Base;
    use WolfMVC\Router\Exception as Exception;
    
    class Route extends Base
    {
        /**
        * @readwrite
        */
        protected $_pattern;
        
        /**
        * @readwrite
        */
        protected $_controller;
        
        /**
        * @readwrite
        */
        protected $_action;
        
        /**
        * @readwrite
        */
        protected $_parameters = array();
        
        public function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
    }
}