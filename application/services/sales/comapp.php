<?php

namespace WolfMVC\WS\Sales {

//    use \WolfMVC\WS\Apiresource as Apiresource;

    class Comapp extends \WolfMVC\WS\Apiresource {

        protected static $_selectable = true;
        protected static $_creatable = false;
        protected static $_updatable = false;
        protected static $_deletable = false;
        protected static $_id = "id_appuntamento";
        protected static $_modeltype = "activerecord";
        protected static $_modelsinvolved = array("Vtpotential", "Vtpotentialscf", "Vtcrmentity");
        protected static $_mainmodel = "Vtpotential";
//        protected $_modelwrapper = array();

        private $_stati = array(
            "_COMAPP_QUALIFICATION" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Conferma TMK",
                "probabilita" => "10",
                "esito_visita" => "Non applicabile",
                "esito_conferma" => "Da confermare"
            ),
            "_COMAPP_QUALIFIED" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Visita venditore",
                "probabilita" => "10",
                "esito_visita" => "Non applicabile",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_UNQUALIFIED" => array(
                "stadio_vendita" => "Da Richiamare",
                "prossimo_step" => "Richiamo TMK",
                "probabilita" => "0",
                "esito_visita" => "Non applicabile",
                "esito_conferma" => "Non confermato"
            ),
            "_COMAPP_CALLAGAIN" => array(
                "stadio_vendita" => "Negotiation/Review",
                "prossimo_step" => "Rifissare appuntamento",
                "probabilita" => "0",
                "esito_visita" => "?Assente,Imp. commerciale,Non decisionale?",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_PASSAGAIN" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Ripasso venditore",
                "probabilita" => "10",
                "esito_visita" => "Ripasso",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_AUTOCLOSE" => array(
                "stadio_vendita" => "Chiuso automaticamente",
                "prossimo_step" => "Gestione manuale",
                "probabilita" => "0",
                "esito_visita" => "Non applicabile",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_SYSTEMCANCELLED" => array(
                "stadio_vendita" => "Annullato-Sistema",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "Non applicabile",
                "esito_conferma" => ""
            ),
            "_COMAPP_CHECK" => array(
                "stadio_vendita" => "Closed Won",
                "prossimo_step" => "Analisi",
                "probabilita" => "100",
                "esito_visita" => "Check",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_NOCHECK" => array(
                "stadio_vendita" => "Closed Lost",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "?No quota,No show,Altri consulenti,Non interessato,No vendita?",
                "esito_conferma" => "Confermato"
            )
        );

        /**
         * @readwrite
         */
        protected static $_vm = array(
            "id_appuntamento" => array(
                "content" => "integer",
                "inmodel" => "id_opportunita"
            ),
            "stadio_vendita" => array(
                "content" => "string"
            ),
            "prossimo_step" => array(
                "content" => "string"
            ),
            "probabilita" => array(
                "content" => "string"
            ),
            "esito_visita" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "esito_conferma" => array(
                "content" => "integer",
                "from" => "cf"
            )
        );

        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function select($id, $filters, $limit,$offset) {
            $potentialdata = parent::select($id, $filters, $limit,$offset);
//            print_r($potentialdata);
            $potmatch = $potentialdata[0];
            unset($potmatch["id_appuntamento"]);
            unset($potmatch["_see_also"]);
//            print_r($potmatch); 
            //tento di riconoscere lo stato:
            $stato = \WolfMVC\ArrayMethods::matchVMAmong($potmatch, $this->_stati, true);
            $result = array(
                "id_appuntamento" => $potentialdata[0]["id_appuntamento"],
                "_see_also" => $potentialdata[0]["_see_also"],
                "stato" => ($stato? $stato : "stato indefinito")
            );
            return $result;
            
        }

        public function make_suggestions($scope, $data) {
            if ($scope === "local")
            {
                $sugg = array();
                $sugg["moreinfo"] = $this->makeref($data["id_appuntamento"], "com_potential/{{v}}");
                return $sugg;
            }
            else if ($scope === "global")
            {
                return array();
            }
        }

    }

}
?>