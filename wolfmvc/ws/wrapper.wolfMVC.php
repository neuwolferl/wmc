<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\WS {


    class Wrapper extends \WolfMVC\Base {

        public $version = "0.1";
        protected static $_trans = array();

        /**
         * @readwrite
         */
        protected $_submodel = ""; 

        static $supports_multimodel = false;
        
        public function __construct($options = array()) {
            if (!isset($options["submodel"]))
            {
                throw new Exception("Wrapper not well defined, missing submodel", 400, NULL);
            }
            parent::__construct($options);
        }

        public function translate_field($fieldName) {
            
        }

        public function get_associated_model_by_desc($desc){
            
        }
    }

}