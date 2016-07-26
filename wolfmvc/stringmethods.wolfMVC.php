<?php

namespace WolfMVC
{
    class StringMethods
    {
        private static $_delimiter = "#";
        
        private static $_singular = array(
            "(matr)ices$" => "\\1ix",
            "(vert|ind)ices$" => "\\1ex",
            "^(ox)en" => "\\1",
            "(alias)es$" => "\\1",
            "([octop|vir])i$" => "\\1us",
            "(cris|ax|test)es$" => "\\1is",
            "(shoe)s$" => "\\1",
            "(o)es$" => "\\1",
            "(bus|campus)es$" => "\\1",
            "([m|l])ice$" => "\\1ouse",
            "(x|ch|ss|sh)es$" => "\\1",
            "(m)ovies$" => "\\1\\2ovie",
            "(s)eries$" => "\\1\\2eries",
            "([^aeiouy]|qu)ies$" => "\\1y",
            "([lr])ves$" => "\\1f",
            "(tive)s$" => "\\1",
            "(hive)s$" => "\\1",
            "([^f])ves$" => "\\1fe",
            "(^analy)ses$" => "\\1sis",
            "((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$" => "\\1\\2sis",
            "([ti])a$" => "\\1um",
            "(p)eople$" => "\\1\\2erson",
            "(m)en$" => "\\1an",
            "(s)tatuses$" => "\\1\\2tatus",
            "(c)hildren$" => "\\1\\2hild",
            "(n)ews$" => "\\1\\2ews",
            "([^u])s$" => "\\1"
        );
        
        private static $_plural = array(
            "^(ox)$" => "\\1\\2en",
            "([m|l])ouse$" => "\\1ice",
            "(matr|vert|ind)ix|ex$" => "\\1ices",
            "(x|ch|ss|sh)$" => "\\1es",
            "([^aeiouy]|qu)y$" => "\\1ies",
            "(hive)$" => "\\1s",
            "(?:([^f])fe|([lr])f)$" => "\\1\\2ves",
            "sis$" => "ses",
            "([ti])um$" => "\\1a",
            "(p)erson$" => "\\1eople",
            "(m)an$" => "\\1en",
            "(c)hild$" => "\\1hildren",
            "(buffal|tomat)o$" => "\\1\\2oes",
            "(bu|campu)s$" => "\\1\\2ses",
            "(alias|status|virus)" => "\\1es",
            "(octop)us$" => "\\1i",
            "(ax|cris|test)is$" => "\\1es",
            "s$" => "s",
            "$" => "s"
        );
        
        private function __construct()
        {
            // do nothing
        }
        
        private function __clone()
        {
            // do nothing
        }
        /**
         * Normalizza un pattern, trimmandolo e poi mettendo il {@link StringMethods::$_delimiter} in testa e in coda.
         * @param string $pattern
         * @return string
         */
        private static function _normalize($pattern)
        {
            return self::$_delimiter.trim($pattern, self::$_delimiter).self::$_delimiter;
        }
        /**
         * Restituisce l'attuale valore di {@link StringMethods::_delimiter}.
         * @return string
         */
        public static function getDelimiter()
        {
            return self::$_delimiter;
        }
        
        /**
         * Cambia l'attuale valore di {@link StringMethods::_delimiter}.
         * @param string$delimiter
         */
        public static function setDelimiter($delimiter)
        {
            self::$_delimiter = $delimiter;
        }
        
        /**
         * Esegue matching di pattern (normalizzato) su string e restituisce il contenuto delle espressioni in parentesi se questo è non vuoto
         * altrimenti restituisce le sottostringhe trovate se non sono vuote altrimenti null.
         * @param string $string
         * @param string $pattern
         * @return mixed
         */
        public static function match($string, $pattern)
        {
            preg_match_all(self::_normalize($pattern), $string, $matches, PREG_PATTERN_ORDER);
            
            if (!empty($matches[1]))
            {
                return $matches[1];
            }
            
            if (!empty($matches[0]))
            {
                return $matches[0];
            }
            
            return null;
        }
        
        /**
         * Divide string secondo pattern normalizzato usando preg_split
         * @param string $string
         * @param string $pattern
         * @param int $limit
         * @return array
         */
        public static function split($string, $pattern, $limit = null)
        {
            $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE;
            return preg_split(self::_normalize($pattern), $string, $limit, $flags);
        }
        
        /**
         * 
         * @param string $string
         * @param type $mask
         * @return type
         */
        public static function sanitize($string, $mask)
        {
            if (is_array($mask))
            {
                $parts = $mask;
            }
            else if (is_string($mask))
            {
                $parts = str_split($mask);
            }
            else
            {
                return $string;
            }
            
            foreach ($parts as $part)
            {
                $normalized = self::_normalize("\\{$part}");
                $string = preg_replace(
                    "{$normalized}m",
                    "\\{$part}",
                    $string
                );
            }
            
            return $string;
        }
        
        public static function unique($string)
        {
            $unique = "";
            $parts = str_split($string);
            
            foreach ($parts as $part)
            {
                if (!strstr($unique, $part))
                {
                    $unique .= $part;
                }
            }
            
            return $unique;
        }
            
        public static function indexOf($string, $substring, $offset = null)
        {
            $position = strpos($string, $substring, $offset);
            if (!is_int($position))
            {
                return -1;
            }
            return $position;
        }
        
        public static function lastIndexOf($string, $substring, $offset = null)
        {
            $position = strrpos($string, $substring, $offset);
            if (!is_int($position))
            {
                return -1;
            }
            return $position;
        }
        
        public static function singular($string)
        {
            $result = $string;
            
            foreach (self::$_singular as $rule => $replacement)
            {
                $rule = self::_normalize($rule);
            
                if (preg_match($rule, $string))
                {
                    $result = preg_replace($rule, $replacement, $string);
                    break;
                }
            }
            
            return $result;
        }
        
        function plural($string)
        {
            $result = $string;
            
            foreach (self::$_plural as $rule => $replacement)
            {
                $rule = self::_normalize($rule);
            
                if (preg_match($rule, $string))
                {
                    $result = preg_replace($rule, $replacement, $string);
                    break;
                }
            }
            
            return $result;
        }
        
        public static function random_string($length) {
	$string = "";

	// genera una stringa casuale che ha lunghezza
	// uguale al multiplo di 32 successivo a $length
	for ($i = 0; $i <= ($length/32); $i++)
		$string .= md5(time()+rand(0,99));

	// indice di partenza limite
	$max_start_index = (32*$i)-$length;

	// seleziona la stringa, utilizzando come indice iniziale
	// un valore tra 0 e $max_start_point
	$random_string = substr($string, rand(0, $max_start_index), $length);

	return $random_string;
}
    }    
}