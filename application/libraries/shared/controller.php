<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace Shared {

    class  Controller extends \WolfMVC\Controller {
        
        /**
        * @readwrite
        */
        protected $_db = array(); //questo mi permetterà di scrivere dall'esterno l'elenco dei db disponibili
        
        public function __construct($options = array()) {
            parent::__construct($options);
            $database = \WolfMVC\Registry::get("database_local");
        }

    }

}
?>