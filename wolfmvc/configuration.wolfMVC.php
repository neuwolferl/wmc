<?php

namespace WolfMVC
{
    use WolfMVC\Base as Base;
    use WolfMVC\Events as Events;
    use WolfMVC\Configuration as Configuration;
    use WolfMVC\Configuration\Exception as Exception;
    
    class Configuration extends Base
    {
        /**
        * Indica il driver da adottare per la configurazione (ini, xml, altro)
        * @readwrite
        */
        protected $_type;
        
        /**
        * @readwrite
        */
        protected $_options;
        
        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
        
        /**
         * Controlla che il tipo sia stato configurato e restituisce il driver per quel tipo.
         * @return \WolfMVC\Configuration\Driver\..
         * @throws Exception\Argument
         */
        public function initialize()
        {
            if (!$this->type)
            {
                throw new Exception\Argument("Invalid type");
            }
            
            switch ($this->type)
            {
                case "ini":
                {
                    return new Configuration\Driver\Ini($this->options);
                    break;
                }
                default:
                {
                    throw new Exception\Argument("Invalid type");
                    break;
                }
            }
        }
    }
}