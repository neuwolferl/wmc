<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Controller\Component {

    class Flux extends \WolfMVC\Base {

        /**
         * @var int Numero di passi
         * @readwrite
         */
        protected $_numberofsteps = 1;

        /**
         * @var int Passo attuale
         * @readwrite
         */
        protected $_actualstep;

        /**
         * @var array Vettore dei parametri passati al controller
         * @readwrite
         */
        protected $_passedparameters = array();

    }

}

?>