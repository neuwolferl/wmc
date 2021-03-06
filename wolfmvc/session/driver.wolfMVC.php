<?php

namespace WolfMVC\Session
{
    use WolfMVC\Base as Base;
    use WolfMVC\Session\Exception as Exception;
    
    class Driver extends Base
    {
        public function initialize()
        {
            return $this;
        }
        
        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
    }
}