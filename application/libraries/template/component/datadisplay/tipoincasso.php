<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */
namespace Template\Component\Datadisplay {
    use WolfMVC\Template\Component\Datatrans as DT;

    class Tipoincasso extends DT\Datatrans {

        public function tr($input) {
            switch ($input) {
                case 1:
                case '1':
                    return "ASS";
                    break;
                case 2:
                case '2':
                    return "BON";
                    break;
                case 3:
                case '3':
                    return "CON";
                    break;
                default :
                    return "Valore mancante";
            }
        }

    }

}