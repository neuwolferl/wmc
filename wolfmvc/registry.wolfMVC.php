<?php

namespace WolfMVC {

    /**
     * Registro delle classi istanziate
     */
    class Registry {

        /**
         *
         * @var array Questa è la memoria del registro. 
         */
        private static $_instances = array();

        /**
         * @var array Questa è una tabella hash per generare nomi causali e non ripetuti
         */
        private static $_hash = array();

        private function __construct() {
// do nothing
        }

        private function __clone() {
// do nothing
        }

        /**
         * Getter per valori nel registro
         * @param mixed $key
         * @param mixed $default
         * @return mixed
         */
        public static function get($key, $default = null) {
            if (isset(self::$_instances[$key]))
            {
                return self::$_instances[$key];
            }
            return $default;
        }

        /**
         * Setter per valori nel registro
         * @param mixed $key
         * @param mixed $instance
         */
        public static function set($key, $instance = null) {
            self::$_instances[$key] = $instance;
        }

        /**
         * Cancella valori memorizzati nel registro (se esistono)
         * @param mixed $key
         */
        public static function erase($key) {
            unset(self::$_instances[$key]);
        }

        public static function esponi($flag) {
            if ($flag){
                ob_start();
            }
            echo "<br><br><h1>BEGIN REGISTRY DUMP</h1><BR>";
            echo "<pre>";
            foreach (self::$_instances as $key => $instance) {
                echo "<strong>" . $key . "</strong>\n";
                print_r($instance);
            }
            echo "</pre>";
            if ($flag){
                $pippo = ob_get_contents();
                ob_end_clean();
                return $pippo;
            }
            
            
        }

        /**
         * restituisce una stringa di lunghezza $len non usata in precedenza
         * @param int $len2
         */
        public static function hashing($par) {
            if (is_int($par))
            {
                $len = $par;
                $characters = "abcdefghijklmnopqrstuvwxyz";
                $j = 0;
                while (true) {
                    $randstring = '';
                    for ($i = 0; $i < $len; $i++) {
                        $randstring .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    if (!isset(self::$_hash[$randstring]))
                    {
                        self::$_hash[$randstring] = $randstring;
                        return $randstring;
                    }
                }
            }
            else if (is_string($par))
            {
                if (!isset(self::$_hash[$par]))
                {
                    self::$_hash[$par] = $par;
                    return $par;
                }
                else
                {
                    $i=2;
                    while (true) {
                        if (!isset(self::$_hash[$par."_".$i]))
                        {
                            self::$_hash[$par."_".$i] = $par."_".$i;
                            return $par."_".$i;
                        }
                        $i++;
                    }
                }
            }
        }

    }

}