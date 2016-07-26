<?php

namespace WolfMVC
{
    use WolfMVC\Base as Base;
    use WolfMVC\Events as Events;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Database as Database;
    use WolfMVC\Database\Exception as Exception;
    
    class Database extends Base
    {
        /**
        * @var string
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
         * 
         * @param type $conf
         * @return \WolfMVC\Database\Connector\Mysql
         * @throws Exception\Argument
         */
        public function initialize($name)
        {
            if (!$this->_type)
            {
                $configuration = \WolfMVC\Registry::get("configuration");
                if ($configuration)
                {
                    $parsed = $configuration->getParsed();
                    $parsed = $parsed["configuration/".SITE_VERSION."/database"];
                    if (!empty($parsed->database->$name) && !empty($parsed->database->$name->type))
                    {
                        $this->_type = $parsed->database->$name->type;
                        unset($parsed->database->$name->type);
                        $this->options = (array) $parsed->database->$name;
                    }
                }
            }
            if (!($this->type))
            {
                throw new Exception\Argument("Invalid type");
            }
            
            switch ($this->type)
            {
                case "mysql":
                {
                    return new Database\Connector\Mysql($this->options);
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