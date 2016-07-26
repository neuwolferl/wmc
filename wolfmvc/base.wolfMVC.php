<?php

namespace WolfMVC {

    use WolfMVC\Boatswain as Boatswain;
    use WolfMVC\ArrayMethods as ArrayMethods;
    use WolfMVC\StringMethods as StringMethods;
    use WolfMVC\Core\Exception as Exception;

    /**
     * Classe base che fa da parent a tutte le classi applicative del framework
     */
    class Base {

        /**
         *
         * @var \WolfMVC\Boatswain
         */
        private $_boatswain;
        
        /**
         * @todo che è? commentare?
         * @var type 
         */
        public static $_lang;

        /**
         * Questo costruttore è disponibile per la maggior parte delle classi applicative
         * del framework. Accetta un array-oggetto di opzioni della forma chiave => valore.
         * Per ogni coppia il costruttore tenta di chiamare la funzione setChiave(valore).
         * @param array $options
         */
        public function __construct($options = array()) {
            $this->_boatswain = new Boatswain($this);

            if (is_array($options) || is_object($options)) {
                foreach ($options as $key => $value) {
                    $key = ucfirst($key);
                    $method = "set{$key}";
                    $this->$method($value);
                }
            }
        }

        /**
         * Override del metodo magico __call. Controlla se il metodo richiesto può essere interpretato come un
         * getter o un setter per la classe corrente, ovvero se esiste la proprietà richiesta dal metodo. 
         * Nel primo caso agisce solo se la proprietà è leggibile, nel secondo agisce solo se la proprietà è scrivibile.
         * Nel caso peggiore lancia eccezione.
         * @param strin $name
         * @param array $arguments
         * @return \WolfMVC\Base|null
         * @throws Exception\Argument
         * @throws Exception\ReadOnly|Exception\WriteOnly
         */
        public function __call($name, $arguments) {
            if (empty($this->_boatswain)) {
                throw new Exception("Call parent::__construct!");
            }

            $getMatches = StringMethods::match($name, "^get([a-zA-Z0-9]+)$"); //forse è un get
            if (sizeof($getMatches) > 0) {
                $normalized = lcfirst($getMatches[0]);
                $property = "_{$normalized}";

                if (property_exists($this, $property)) {
                    $meta = $this->_boatswain->getPropertyMeta($property);

                    if (empty($meta["@readwrite"]) && empty($meta["@read"])) {
                        throw $this->_getExceptionForWriteonly($normalized);
                    }

                    if (isset($this->$property)) {
                        return $this->$property;
                    }

                    return null;
                }
            }


            $setMatches = StringMethods::match($name, "^set([a-zA-Z0-9]+)$"); //forse è un set
            if (sizeof($setMatches) > 0) {
                $normalized = lcfirst($setMatches[0]);
                $property = "_{$normalized}";

                if (property_exists($this, $property)) {
                    $meta = $this->_boatswain->getPropertyMeta($property);

                    if (empty($meta["@readwrite"]) && empty($meta["@write"])) {
                        throw $this->_getExceptionForReadonly($normalized);
                    }

                    $this->$property = $arguments[0];
                    return $this;
                }
            }
            $getMatches = StringMethods::match($name, "^fromindex([a-zA-Z0-9]+)$"); //forse è un fromindex

            if (sizeof($getMatches) > 0) {
                $normalized = lcfirst($getMatches[0]);
                $property = "_{$normalized}";

                if (property_exists($this, $property)) {
                    $meta = $this->_boatswain->getPropertyMeta($property);

                    if (empty($meta["@readwrite"]) && empty($meta["@read"])) {
                        throw $this->_getExceptionForWriteonly($normalized);
                    }

                    if (isset($this->$property)) {
                        if (is_array($this->$property)) {
                            $prop = $this->$property;
                            if (isset($prop[$arguments[0]])) {
                                return $prop[$arguments[0]];
                            }
                        }
                    }

                    return null;
                }
            }


            throw $this->_getExceptionForImplementation($name); //non è un metodo noto-gestito
        }

        /**
         * Ovverride del metodo magico __get. Esegue una funzione getter per la proprietà richiesta.
         * @param string $name
         * @return mixed
         */
        public function __get($name) {
            $function = "get" . ucfirst($name);
            return $this->$function();
        }

        /**
         * Ovverride del metodo magico __set. esegue una funzione setter per la proprietà richiesta.
         * @param string $name
         * @param mixed $value
         * @return mixed
         */
        public function __set($name, $value) {
            $function = "set" . ucfirst($name);
            return $this->$function($value);
        }

        protected function _getExceptionForReadonly($property) {
            return new Exception\ReadOnly("{$property} is read-only");
        }

        protected function _getExceptionForWriteonly($property) {
            return new Exception\WriteOnly("{$property} is write-only");
        }

        protected function _getExceptionForProperty() {
            return new Exception\Property("Invalid property");
        }

        protected function _getExceptionForImplementation($method) {
            return new Exception\Argument("{$method} method not implemented");
        }

        public function print_rec($obj, $level, $tabs = 0) {
            $prefix = "";
            for ($i = 0; $i < $tabs; $i++) {
                $prefix .= "  ";
            }
            if ($level === 0) {
                if (is_object($obj)) {
                    echo $prefix . "[Object " . get_class($obj) . "]<br>";
                    $props = get_object_vars($obj);
                    foreach ($props as $key => $prop) {
                        echo "  " . $prefix . $key . "<br>";
                    }
                } else if (is_array($obj)) {
                    echo $prefix . "[Array]<br>";
                    foreach ($obj as $key => $val) {
                        echo "  " . $prefix . "[".$key . "] => ...<br>";
                    }
                } else {
                    echo $prefix . $obj . "<br>";
                }
                return;
            } else {
                if (is_object($obj)) {
                    echo $prefix . "[Object " . get_class($obj) . "]<br>";
                    $props = get_object_vars($obj);
                    foreach ($props as $key => $prop) {
                        echo "  " . $prefix . $key . "<br>";
                        $this->print_rec($obj->{$key}, $level - 1, $tabs + 2);
                    }
                } else if (is_array($obj)) {
                    echo $prefix . "[Array]<br>";
                    foreach ($obj as $key => $val) {
                        echo "  " . $prefix . "[".$key . "] => <br>";
                        $this->print_rec($val, $level - 1, $tabs + 2);
                    }
                } else {
                    echo $prefix . $obj . "<br>";
                }
                return;
            }
        }
        /**
         * Metodo debugger
         * @param string $what
         * @param it $level
         * @todo rimuovere in produzione
         */
        public function debug_describe($what, $level) {
            echo "<br>-------------------------------------------------------------------------------------------------<br>";
            if (is_string($what)) {
                echo "DEBUG DESCRIBE: " . get_class($this) . "  " . $what . "<br>";
                if (property_exists($this, $what)) {
                    if ($level === "all") {
                        
                        echo "<pre>";
                        print_r($this->{$what});
                        echo "<pre>";
                    } else if (is_int($level) && $level >= 0) {
                        
                        echo "<pre>";
                        $this->print_rec($this->{$what},$level);
                        echo "<pre>";
                    }
                } else {
                    echo "<br>Attributo inesistente in questa classe<br>";
                }
            } else if (is_array($what)) {
                
                echo "DEBUG DESCRIBE: " . get_class($this) . "<br>";
                foreach ($what as $key => $w) {
                    if (property_exists($this, $w)) {
                        if ($level === "all") {
                            
                            echo "<pre>";
                            print_r($this->{$w});
                            echo "<pre>";
                        } else if (is_int($level) && $level >= 0) {
                            echo "<pre>";
                            $this->print_rec($this->{$w},$level);
                            echo "<pre>________________________________________________________________________________________________<br><br>";
                        }
                    } else {
                        echo "<br>" . $w . " Attributo inesistente in questa classe<br>";
                    }
                }
            } else {
                echo "DEBUG DESCRIBE: " . get_class($this) . "<br>";
                echo "<pre>";
                print_r($this);
                echo "<pre>";
            }
            echo "<br>-------------------------------------------------------------------------------------------------<br>";
        }

    }

}
    