<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {

    class Lang {
        protected $name;
        public $dictionary = array();
        
        public function __construct($lang) {
            $name = $lang[0];
            if (count($lang) != 2){
                die ('Errore di configurazione lingue'); //correggere con eccezione
            }
            else{
                if (is_file(APP_PATH . "/application/configuration/languages/".$lang[1].".ini")){
                    $dictionary = parse_ini_file(APP_PATH . "/application/configuration/languages/".$lang[1].".ini");
//                    echo "<pre>";
//                    print_r($dictionary);
//                    echo "</pre>";
//                    foreach ($dictionary as $key => $value){
//                        $dictionary = $this->pair($dictionary, $key, $value);
//                    }
//                    $dictionary = ArrayMethods::toObject($dictionary);
//                    echo "<pre>";
//                    print_r($dictionary);
//                    echo "</pre>";
                    $this->dictionary = $dictionary;
                }
                else{
                    die ('Errore di configurazione lingue - file mancante'); //correggere con eccezione
                }
            }
            
        }
        
        public function pair($config, $key, $value)
        {
            if (strstr($key, "."))
            {
                $parts = explode(".", $key, 2);
                
                if (empty($config[$parts[0]]))
                {
                    $config[$parts[0]] = array();
                    
                }
                $config[$parts[0]] = $this->pair($config[$parts[0]], $parts[1], $value);
                unset($config[$key]);
            }
            else
            {
                $config[$key] = $value;
            }
            
            return $config;
        }
        
        public function sh($key,$params = null){
            if (isset($this->dictionary[$key])){
                $string = $this->dictionary[$key]; 
                if (is_array($params) && count($params) > 0){    
                    foreach ($params as $key=>$value){
                        $string = str_replace("{###".$key."###}", $params[$key], $string);
                    }
                }
                return $string; 
            }
            else
                return "Stringa non disponibile";
        }
    }

}