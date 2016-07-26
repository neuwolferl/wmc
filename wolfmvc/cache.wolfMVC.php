<?php

namespace WolfMVC {

    use WolfMVC\Base as Base;
    use WolfMVC\Cache as Cache;
    use WolfMVC\Events as Events;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Cache\Exception as Exception;

    /**
     * Classe base per la gestione della cache.
     */
    class Cache extends Base {

        /**
         * @readwrite
         * @var string
         */
        protected $_type;

        /**
         * @readwrite
         * @var array
         */        
        protected $_options;

        /**
         * 
         * @param string $method
         * @return \WolfMVC\Cache\Exception\Implementation
         */
        protected function _getExceptionForImplementation($method) {
            return new Exception\Implementation("{$method} method not implemented");
        }

        /**
         * Inizializza driver per la cache. Attualmente gestisce solo il caso memcached quindi restituisce {@see \WolfMVC\Cache\Driver\Memcached Memcached}
         * @return mixed
         * @throws Exception\Argument
         */
        public function initialize() {
            if (!$this->type) {

                $configuration = Registry::get("configuration");

                if ($configuration) {
                    $configuration = $configuration->initialize();
                    $parsed = $configuration->parse("configuration/cache");

                    if (!empty($parsed->cache->default) && !empty($parsed->cache->default->type)) {
                        $this->type = $parsed->cache->default->type;
                        unset($parsed->cache->default->type);
                        $this->options = (array) $parsed->cache->default;
                    }
                }
            }

            if (!$this->type) {
                throw new Exception\Argument("Invalid type");
            }

            switch ($this->type) {
                case "memcached": {
                        return new Cache\Driver\Memcached($this->options);
                        break;
                    }
                default: {
                        throw new Exception\Argument("Invalid type");
                        break;
                    }
            }
        }

    }

}