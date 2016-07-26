<?php

namespace WolfMVC\Database
{
    use WolfMVC\Base as Base;
    use WolfMVC\Database\Exception as Exception;
    
    class Connector extends Base
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