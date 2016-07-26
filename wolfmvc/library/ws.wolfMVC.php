<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Library {
    
    abstract class Ws extends \WolfMVC\Base {
        
        /**
         *
         * @var string Url a cui connettersi
         */
        protected $_url;
        
        /**
         *
         * @var string
         */
        protected $_method = "GET";
        
        /**
         *
         * @var array Array associativo chiave => valore
         */
        protected $_params = array();
        
        
        
    } 
}