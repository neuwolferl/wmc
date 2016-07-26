<?php

namespace WolfMVC\Configuration
{
    use WolfMVC\Base as Base;
    use WolfMVC\Configuration\Exception as Exception;
    
    /**
     * Parent di tutti i driver per file di configurazione
     */
    class Driver extends Base
    {
        /**
         * @read
         * @var array Il valore ultimo del parse di una configurazione.
         */
        protected $_parsed = array();
        
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