<?php

namespace WolfMVC {

    use WolfMVC\Core\Exception as Exception;

    /**
     * Classe di inizializzazione del framework
     */
    class Core {

        /**
         *
         * @var array 
         */
        private static $_loaded = array();

        /**
         * I percorsi relativi all'applicazione in cui andare a cercare le classi durante l'autoloading
         * @TODO portare fuori un configuratore manuale per questa impostazione
         * @var array 
         */
        private static $_paths = array(
            "/application/libraries",
            "/application/controllers",
            "/application/models",
            "/application",
            ""
        );

        /**
         * Controlla che sia stato definito APP_PATH, controlla lo stato di magic_quotes_gpc
         * e se è il caso chiama il metodo {@link Core::strips} per ripulire dagli slash.
         * Infine aggiunge APP_PATH ai path registrati in {@link Core::_paths}, aggiorna
         * l'include path e setta il metodo {@link Core::_autoload} come autoloader registrato.
         * @return void
         * @throws Exception
         */
        public static function initialize() {
            if (!defined("APP_PATH"))
            {
                throw new Exception("APP_PATH not defined");
            }



            if (get_magic_quotes_gpc())
            {
                $globals = array("_POST", "_GET", "_COOKIE", "_REQUEST", "_SESSION");

                foreach ($globals as $global) {
                    if (isset($GLOBALS[$global]))
                    {
                        $GLOBALS[$global] = Utils::_strips($GLOBALS[$global]);
                    }
                }
            }



            $paths = array_map(function($item) {
                return APP_PATH . $item;
            }, self::$_paths);

            $paths[] = get_include_path();
            set_include_path(join(PATH_SEPARATOR, $paths));
            spl_autoload_register(__CLASS__ . "::activerecord_autoload");
            spl_autoload_register(__CLASS__ . "::vtws_autoload");
            spl_autoload_register(__CLASS__ . "::google_api_php_client_autoload");
            spl_autoload_register(__CLASS__ . "::_api_autoload");
            spl_autoload_register(__CLASS__ . "::_autoload");
        }

        protected static function activerecord_autoload($className) {
            $debug = false;
            if ($debug)
                echo "activerecord_autoload<br><br>";
            $classPath = explode('\\', $className);
            if ($debug)
                print_r($classPath);
            if ($classPath[0] != 'AR')
            {
                return;
            }

            array_shift($classPath);
            $className = join("/", $classPath);
            if ($debug)
                echo "Partito" . "<br>";
            $path = \ActiveRecord\Config::instance()->get_model_directory();
            if ($debug)
                echo "PATH " . $path . "<br>";
            $root = realpath(isset($path) ? $path : '.');
            if ($debug)
                echo $root . "<br>";
//            if (($namespaces = \ActiveRecord\get_namespaces($className)))
//            {
//                print_r($namespaces);
//                $className = array_pop($namespaces);
//                $directories = array();
//
//                foreach ($namespaces as $directory)
//                    $directories[] = $directory;
//
//                $root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
//            }

            $file = "$root/$className.php";
            if ($debug)
                echo $file . "<br>----<br>";
            if (file_exists($file))
                require $file;
        }

        protected static function vtws_autoload($className) {
            $debug = false;
            if ($debug)
                echo "vtws_autoload<br><br>";
            $classPath = explode('\\', $className);
            if ($debug)
                print_r($classPath);
            if ($classPath[0] != 'VT')
            {
                return;
            }

            array_shift($classPath);
            $className = join("/", $classPath);
            if ($debug)
                echo "Partito" . "<br>";
            $path = APP_PATH . "/application/configuration/models/vt";
            if ($debug)
                echo "PATH " . $path . "<br>";
            $root = realpath(isset($path) ? $path : '.');
            if ($debug)
                echo $root . "<br>";
//            if (($namespaces = \ActiveRecord\get_namespaces($className)))
//            {
//                print_r($namespaces);
//                $className = array_pop($namespaces);
//                $directories = array();
//
//                foreach ($namespaces as $directory)
//                    $directories[] = $directory;
//
//                $root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
//            }

            $file = "$root/" . strtolower($className) . ".php";
            if ($debug)
                echo $file . "<br>----<br>";
            if (file_exists($file))
                require $file;
        }

        protected static function google_api_php_client_autoload($className) {
            $classPath = explode('_', $className);
            if ($classPath[0] != 'Google')
            {
                return;
            }
            if (count($classPath) > 3)
            {
                // Maximum class file path depth in this project is 3.
                $classPath = array_slice($classPath, 0, 3);
            }
            $filePath = APP_PATH . '/application/libraries/googleapi/src/' . implode('/', $classPath) . '.php';
            if (file_exists($filePath))
            {
                require_once($filePath);
            }
        }

        protected static function _api_autoload($className) {
            $debug = FALSE; //(strpos($className, "Account") !== FALSE);
            if ($debug)
                echo "api_autoload for {$className}<br>";
            $classPath = explode('\\', $className);
            if (count($classPath) < 3)
            {
                return;
            }
            $prefix1 = array_shift($classPath);
            $prefix2 = array_shift($classPath);
            if ($debug)
                echo "$prefix1<br>";
            if ($debug)
                echo "$prefix2<br>";
            if ($prefix1 !== 'WolfMVC' || $prefix2 !== 'WS')
            {
                return;
            }
            foreach ($classPath as $k => $v) {
                $classPath[$k] = strtolower($v);
            }
            $classPath = implode("/", $classPath);
            $filePath = APP_PATH . "/application/services/" . $classPath . '.php';
            if ($debug)
                echo "cerco file " . $filePath . "<br>";
            if (file_exists($filePath))
            {
                require_once($filePath);
                if ($debug)
                    echo "trovato<br>";
            }
        }

        /**
         * 
         * @param string $class
         * @return void
         * @throws Exception
         */
        protected static function _autoload($class) {
            $debug = false;
            $percorsi = explode(PATH_SEPARATOR, get_include_path());
            $flags = PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE; //cattura solo i pezzi non vuoti e cattura anche l'offset dei pezzi catturati
            // se $class = ns1\ns2\..\nsm\classe -> $file = ns1/ns2/../nsm/classe.wolfMVC.php  

            $file = strtolower(str_replace("\\", DIRECTORY_SEPARATOR, trim($class, "\\"))) . ".wolfMVC.php";
            if ($debug)
                echo getcwd() . "<br>";

            if ($debug)
                echo "<br><strong>Richiesto file : " . $file . "</strong><br />";
            foreach ($percorsi as $percorso) {
                $tentativo = $percorso . DIRECTORY_SEPARATOR . $file;
                if ($debug)
                {
//                    echo "Lo cerco in " . $percorso . DIRECTORY_SEPARATOR . "<br>";
                    if (is_dir($percorso . DIRECTORY_SEPARATOR))
                    {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $percorso . DIRECTORY_SEPARATOR . " &eacute; una directory<br>";
//                        echo "<pre>";
//                        print_r(scandir($percorso . DIRECTORY_SEPARATOR));
//                        echo "</pre>";
                    }
                    else
                    {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $percorso . DIRECTORY_SEPARATOR . " non &eacute; una directory<br>";
                    }
                }
                if (file_exists($tentativo))
                {
                    if ($debug)
                        echo $tentativo . "<strong> TROVATO!!!</strong><br><br>";
                    include($tentativo);
                    return;
                }
//                else if ($debug)
//                    echo $tentativo . " NON TROVATO...<br>";
            }
            $file = strtolower(str_replace("\\", DIRECTORY_SEPARATOR, trim($class, "\\"))) . ".php";
            if ($debug)
                echo getcwd() . "<br>";

            if ($debug)
                echo "<br><strong>Richiesto file : " . $file . "</strong><br />";
            foreach ($percorsi as $percorso) {
                $tentativo = $percorso . DIRECTORY_SEPARATOR . $file;
                if ($debug)
                {
                    echo "Lo cerco in " . $percorso . DIRECTORY_SEPARATOR . "<br>";
                    if (is_dir($percorso . DIRECTORY_SEPARATOR))
                    {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $percorso . DIRECTORY_SEPARATOR . " &eacute; una directory<br>";
//                        echo "<pre>";
//                        print_r(scandir($percorso . DIRECTORY_SEPARATOR));
//                        echo "</pre>";
                    }
                    else
                    {
                        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $percorso . DIRECTORY_SEPARATOR . " non &eacute; una directory<br>";
                    }
                }
                if (file_exists($tentativo))
                {
                    if ($debug)
                        echo $tentativo . "<strong> TROVATO!!!</strong><br><br>";
                    include($tentativo);
                    return;
                }
                else if ($debug)
                    echo $tentativo . " NON TROVATO...<br>";
            }
//            echo "<pre>";
//            print_r(debug_backtrace());
//            echo "</pre>";
            throw new Exception("{$class} not found");
        }

    }

}