<?php

namespace WolfMVC {

    use WolfMVC\Exception as Exception;

    /**
     * Classe di ausilio allo start
     */
    class Starter {

        /**
         * I percorsi relativi all'applicazione in cui andare a cercare le classi durante l'autoloading
         * @TODO portare fuori un configuratore manuale per questa impostazione
         * @var array 
         */
        private static $_paths = array(
            "{{APP_CONF}}" => APP_PATH . "/application/configuration/",
            "{{PUBLIC_CONF}}" => APP_PATH . "/public/configuration/",
            "{{FWK_CONF}}" => APP_PATH . "/wolfmvc/configuration/"
        );

        private static function decodeConfPath($path) {
            return str_ireplace(array_keys(self::$_paths), array_values(self::$_paths), $path);
        }

        /**
         * Metodo di inizializzazione. E' un listone di operazioni da compiere ad ogni partenza di pagina
         * @return void
         * @throws Exception
         */
        public static function initialize($verboseFlag) {
            $log = new Simplelog();
            $log->log("APP INITIALIZATION");

            if (!defined("APP_PATH")) {
                $log->log("Error: APP_PATH not defined");
                throw new Exception("APP_PATH not defined");
            }
            $log->log("App configuration");
            $v = self::configureApp();
            echo "<pre>";
            $log->log($v["conf log"]);
            echo "</pre>";
            $log->log("App configured");
            /*
             *    Session
             */
            $log->log("Session initialization");
            $v = self::initSession();
            $c = $v["session object"];
            $log->log($v["session log"]);
            unset($v);
            /*
             *    Cache
             */
            $log->log("Cache initialization");
            $v = self::initCache();
            $c = $v["cache object"];
            $log->log($v["cache log"]);
            unset($v);





            if ($verboseFlag) {
                echo "<pre>";
                $log->publish(True);
                echo "</pre>";
            }
        }

        /**
         * 
         */
        public static function initSession() {
            $sessionPrint = null;
            $session = $s = null;
            $log = new Simplelog();
            try {
                $session = new Session();

                $log->log("Session instantiated");
                $sessionPrint = print_r($session, TRUE);
            } catch (\Exception $e) {
                $log->log("Error in session instance creation - " . $e->getMessage());
                $sessionPrint = null;
                return array("session object" => null, "session log" => $log, "session print" => $sessionPrint);
            }
            try {
                $s = $session->initialize();
                $log->log("Session initialized");
                Registry::set("session", $s);
            } catch (\Exception $e) {
                $log->log("Error in session initialization - " . $e->getMessage());
                $sessionPrint = print_r($s, TRUE);
                return array("session object" => null, "session log" => $log, "session print" => $sessionPrint);
            }
            $log->log("Done");
            return array("session object" => null, "session log" => $log, "session print" => $cachePrint);
        }

        /**
         * 
         */
        public static function initCache() {
            $cachePrint = null;
            $cache = $c = null;
            $log = new Simplelog();
            try {
                $cache = new Cache();
                $log->log("Cache created");
                $cachePrint = print_r($cache, TRUE);
            } catch (\Exception $e) {
                $log->log("Error in cache instance creation - " . $e->getMessage());
                $cachePrint = null;
                return array("cache object" => null, "cache log" => $log, "cache print" => $cachePrint);
            }
            try {
                $c = $cache->initialize();
                $log->log("Cache initialized");
            } catch (\Exception $e) {
                $log->log("Error in cache initialization - " . $e->getMessage());
                $cachePrint = print_r($c, TRUE);
                return array("cache object" => null, "cache log" => $log, "cache print" => $cachePrint);
            }
            try {
                if (!($c->connect()))
                    throw new Exception("Unable to connect to cache");
                $log->log("Connected to cache server");
            } catch (\Exception $e) {
                $log->log("Error during connection to cache server - " . $e->getMessage());
                $cachePrint = print_r($c, TRUE);
                return array("cache object" => null, "cache log" => $log, "cache print" => $cachePrint);
            }
            $log->log("Done");
            return array("cache object" => null, "cache log" => $log, "cache print" => $cachePrint);
        }

        /**
         * 
         */
        public static function configureApp() {
            $log = new Simplelog();
            try {
                $configuration = new Configuration(array(
                    "type" => "ini"
                ));
                $log->log("App configuration started");
            } catch (\Exception $e) {
                $log->log("App configuration failed" . $e->getMessage());
                return array("conf object" => null, "conf log" => $log);
            }

            try {
                $conf = $configuration->initialize();
                Registry::set("configuration", $conf);
                $log->log("Conf initialized");
            } catch (\Exception $e) {
                $log->log("App configuration failed" . $e->getMessage());
                return array("conf object" => $conf, "conf log" => $log);
            }
            try {
                $mainconfpath = self::$_paths["{{PUBLIC_CONF}}"] . "mainconf";

                $log->log("Try to parse " . $mainconfpath);
                $mainconf = $conf->parse($mainconfpath);
                $mainconf = $mainconf->mainconf;
                Registry::set("mainconf", $mainconf);
                $log->log($mainconfpath . " parse succeeded ");
                $extconf = new \stdClass();
                foreach (json_decode(json_encode($mainconf), true) as $i => $j) {
                    try {
                        $path = self::decodeConfPath($j["conf"]["path"]);
                        $log->log("Try to parse " . $path);
                        $extconf = $conf->parse($path);
                        $log->log($path . " parse succeeded ");
                        $mainconf->{$i} = (object) array_merge((array) $mainconf->{$i}, (array) $extconf->{$i});
                    } catch (\Exception $e) {
                        $log->log("Conf parse failed " . $e->getMessage());
                        if (isset($j["conf"]["priority"]) && $j["conf"]["priority"] == "mandatory") {
                            throw new Exception($path . " mandatory conf file could not be parsed. Conf halted.");
                        }
                    }
                }
                Registry::set("mainconf", $mainconf);
            } catch (\Exception $e) {
                $log->log("Conf parse failed " . $e->getMessage());
                return array("conf object" => $conf, "conf log" => $log);
            }
//                //$conf->parse("configuration/database"); //riattivare quando sarà
//                $conf->parse("configuration/" . SITE_VERSION . "/cache");
//                $conf->parse("configuration/" . SITE_VERSION . "/session");
//                WolfMVC\Registry::set("layoutenvvars", $conf->parse("configuration/" . SITE_VERSION . "/layoutenvvars"));
//                WolfMVC\Registry::set("systemstatus", $conf->parse("configuration/" . SITE_VERSION . "/systemstatus"));
//                //WolfMVC\Registry::set("googleApiConf", $conf->parse("configuration/" . SITE_VERSION . "/google"));
////    WolfMVC\Registry::set("activerecord_initializer", function($cfg) {
////        $cfg->set_model_directory(APP_PATH.'/application/configuration/models/ar');
////        $cfg->set_connections_from_system(array("vtiger" => "vtiger", "tsnw" => "tsnw", "mkt" => "mkt", "tsnw6" => "tsnw6"));
////        return $cfg;
////    }
////    );

            return array("conf object" => $conf, "conf log" => $log);
        }

        /**
         * 
         */
        public static function startDatasource() {
            
        }

        /**
         * 
         */
        public static function startDatabase() {
            
        }

        /**
         * 
         */
        public static function startLanguage() {
            
        }

    }

}