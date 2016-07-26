<?php

namespace WolfMVC
{
    use WolfMVC\ArrayMethods as ArrayMethods;
    use WolfMVC\StringMethods as StringMethods;
    /**
     * Classe di appoggio che è in grado di leggere i metodi, le proprietà e la relativa documentazione delle altre classi.
     * 
     */
    class Boatswain
    {
        protected $_class;
        
        protected $_meta = array(
            "class" => array(),
            "properties" => array(),
            "methods" => array()
        );
        
        protected $_properties = array();
        protected $_methods = array();
        
        public function __construct($class)
        {
            $this->_class = $class;
        }
        
        protected function _getClassComment()
        {
            $reflection = new \ReflectionClass($this->_class);
            return $reflection->getDocComment();
        }
        
        protected function _getClassProperties()
        {
            $reflection = new \ReflectionClass($this->_class);
            return $reflection->getProperties();
        }
        
        protected function _getClassMethods()
        {
            $reflection = new \ReflectionClass($this->_class);
            return $reflection->getMethods();
        }
        
        protected function _getPropertyComment($property)
        {
            $reflection = new \ReflectionProperty($this->_class, $property);
            return $reflection->getDocComment();
        }
        
        protected function _getMethodComment($method)
        {
            $reflection = new \ReflectionMethod($this->_class, $method);
            return $reflection->getDocComment();
        }
        /**
         * Cerca nei commenti la struttura @ qualcosa qualcosaltro. Se la trova la processa
         * restituendo un array chiave valore qualcosa => qualcosaltro
         * @param type $comment
         * @return type
         */
        protected function _parse($comment)
        {
            $meta = array();
            $pattern = "(@[a-zA-Z]+\s*[a-zA-Z0-9, ()_]*)";
            $matches = StringMethods::match($comment, $pattern);
            
            if ($matches != null)
            {
                foreach ($matches as $match)
                {
                    $parts = ArrayMethods::clean(
                       ArrayMethods::trim(
                            StringMethods::split($match, "[\s]", 2)
                        )
                    );
                    
                    $meta[$parts[0]] = true;
                    
                    if (sizeof($parts) > 1)
                    {
                        $meta[$parts[0]] = ArrayMethods::clean(
                            ArrayMethods::trim(
                                StringMethods::split($parts[1], ",")
                            )
                        );
                    }
                }
            }
            
            return $meta;
        }
        
        /**
         * Estrae i meta dalla classe e li parsa. Restituisce null se sono vuoti.
         * @return array
         */
        public function getClassMeta()
        {
            if (!isset($_meta["class"]))
            {
                $comment = $this->_getClassComment();
                
                if (!empty($comment))
                {
                    $_meta["class"] = $this->_parse($comment);
                }
                else
                {
                    $_meta["class"] = null;
                }
            }
            
            return $_meta["class"];
        }
        
        /**
         * Restituisce l'elenco delle proprietà della classe
         * @return array
         */
        public function getClassProperties()
        {
            if (!isset($_properties))
            {
                $properties = $this->_getClassProperties();
                
                foreach ($properties as $property)
                {
                    $_properties[] = $property->getName();
                }
            }
            
            return $_properties;
        }
        
        /**
         * Restituisce l'elenco dei metodi della classe
         * @return array
         */
        
        public function getClassMethods()
        {
            if (!isset($_methods))
            {
                $methods = $this->_getClassMethods();
                
                foreach ($methods as $method)
                {
                    $_methods[] = $method->getName();
                }
            }
            
            return $_methods;
        }
        
        
        /**
         * Estrae e parsa i meta nei commenti della proprietà
         * @param string $property
         * @return array
         */
        public function getPropertyMeta($property)
        {
            if (!isset($_meta["properties"][$property]))
            {
                $comment = $this->_getPropertyComment($property);
                
                if (!empty($comment))
                {
                    $_meta["properties"][$property] = $this->_parse($comment);
                }
                else
                {
                    $_meta["properties"][$property] = null;
                }
            }
            
            return $_meta["properties"][$property];
        }
        
        /**
         * Estrae e parsa i meta nei commenti del metodo
         * @param string $method
         * @return array
         */
        public function getMethodMeta($method)
        {    
            if (!isset($_meta["actions"][$method]))
            {
                $comment = $this->_getMethodComment($method);
                
                if (!empty($comment))
                {
                    $_meta["methods"][$method] = $this->_parse($comment);
                }
                else
                {
                    $_meta["methods"][$method] = null;
                }
            }
            
            return $_meta["methods"][$method];
        }
    }
}