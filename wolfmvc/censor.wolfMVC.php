<?php

namespace WolfMVC {

    /**
     * Censore degli oggetti del framework
     * @uses WolfMVC\Censorflags Enum per flag degli elementi censiti
     */
    class Censor {

        /**
         *
         * @var array Questa è la memoria del registro. Contiene le chiavi
         * database, session, modules a cui corrispondono gli elenchi di oggetti usati durante la vita del fw
         * e un flag 
         * 
         */
        private static $_objects = array(
            "database" => array(),
            "modules" => array(),
            "language" => array(),
            "systemtables" => array(),
        );

        public function __construct() {
            $database = array();
            $modules = array();

            $main_config_path = APP_PATH . "/application/configuration/main_config.php";
//            echo "<br>Search for main config = " . $main_config_path . "<br>";
            if (is_file($main_config_path)) {
                try {
                    require ($main_config_path);
                    foreach ($database as $key => $db) {
                        if ((count($db) == 4) && (is_string($db[0])) && (is_string($db[1])) && (is_string($db[2])) && (is_string($db[3]))) {
                            self::$_objects['database'][$key] = $db;
                            if (!(constant("\WolfMVC\Censorflags::" . $db[3]))) {
                                self::$_objects['init_database'][] = $key;
                            }
                        }
                    }
                    self::$_objects['database'] = $database;

                    foreach ($systemtables as $key => $table) {
                        if ((count($table) == 3) && (is_string($table[0])) && (is_string($table[1])) && (is_string($table[2]))) {
                            self::$_objects['systemtables'][$table[0]] = array("table" => $table[2], "db" => $table[1]);
                        }
                    }


                    foreach ($language as $key => $lang) {
                        if ((count($lang) == 2) && (is_string($lang[0])) && (is_string($lang[1]))) {
                            self::$_objects['language'][$key] = $lang;
                        }
                    }
                    self::$_objects['language'] = $language;

                    foreach ($module as $key => $mod) {
                        if ((count($mod) == 2) && (is_string($mod[0])) && (is_string($mod[1]))) {
                            self::$_objects['module'][$key] = $mod;
                        }
                    }
                    self::$_objects['module'] = $module;
                    unset($mod);
                    foreach ($model as $key => $mod) {
                        if ((count($mod) == 2) && (is_string($mod[0])) && (is_string($mod[1]))) {
                            self::$_objects['model'][$key] = $mod;
                        }
                    }
                    self::$_objects['model'] = $model;
                } catch (Exception $e) {
                    die('Error in main configuration file.');
                }
            } else
                echo "Il file non esiste<br>";
        }

//        private function __clone()
//        {
//            // do nothing
//        }

        /**
         * Getter per valori nel censore
         * @param mixed $key
         * @param mixed $default
         * @return mixed
         */
        public static function get($key, $default = null) {
            if (isset(self::$_objects[$key])) {
                return self::$_objects[$key];
            }
            return $default;
        }

        /**
         * Setter per valori nel censore
         * @param mixed $key
         * @param mixed $object
         */
        public static function set($key, $object = null) {
            self::$_objects[$key] = $object;
        }

        /**
         * Cancella valori memorizzati nel registro (se esistono)
         * @param mixed $key
         */
        public static function erase($key) {
            unset(self::$_instances[$key]);
        }

        public static function esponi() {
            echo "<br><br><h1>BEGIN CENSOR DUMP</h1><BR>";
            echo "<pre>";
            foreach (self::$_objects as $key => $instance) {
                echo "<strong>" . $key . "</strong>\n";
                print_r($instance);
            }
            echo "</pre>";
        }

    }

}