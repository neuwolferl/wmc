<?php

namespace WolfMVC\WS\Com {

//    use \WolfMVC\WS\Apiresource as Apiresource;

    class Potential extends \WolfMVC\WS\Apiresource {

        protected static $_selectable = true;
        protected static $_creatable = false;
        protected static $_updatable = false;
        protected static $_deletable = false;
        protected static $_id = "id_opportunita";
        protected static $_modeltype = "activerecord";
        protected static $_modelsinvolved = array("Vtpotential", "Vtpotentialscf", "Vtcrmentity");
        protected static $_mainmodel = "Vtpotential";
//        protected $_modelwrapper = array();

        /**
         * @readwrite
         */
        protected static $_vm = array(
            "id_opportunita" => array(
                "content" => "integer"
            ),
            "nome_opportunita" => array(
                "content" => "string"
            ),
            "collegato_a" => array(
                "content" => "refto::crm_cd_account/{{v}}"
            ),
            "numero_opportunita" => array(
                "content" => "string"
            ),
            "ammontare" => array(
                "content" => "integer"
            ),
            "data_chiusura_attesa" => array(
                "content" => "date"
            ),
            "prossimo_step" => array(
                "content" => "string"
            ),
            "probabilita" => array(
                "content" => "number"
            ),
            "stadio_vendita" => array(
                "content" => "string"
            ),
            "tipo_opportunita" => array(
                "content" => "string"
            ),
            "esito_visita" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "esito_conferma" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "dettagli" => array(
                "content" => array(
                    "fonte_lead" => array(
                        "content" => "string"
                    ),
                    "ammontare_pesato" => array(
                        "content" => "number",
                        "from" => "cf"
                    ),
                    "telemarketing" => array(
                        "content" => "string",
                        "from" => "cf"
                    ),
                    "venditore" => array(
                        "content" => "string",
                        "from" => "cf"
                    ),
                    "analista" => array(
                        "content" => "string",
                        "from" => "cf"
                    ),
                    "pot_analisi_collegata" => array(
                        "content" => "string",
                        "from" => "cf"
                    ),
                )
            ),
            "descrizione" => array(
                "content" => "string",
                "from" => "ent",
                "inmodel" => "description"
            )
        );

        protected static $_staticFilters = array(
            "ent" => array(
                "deleted" => array(
                    array("£f£ = ?",0)
                )
            )
        );
        
        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function select($id, $filters, $limit, $offset) {
            return parent::select($id, $filters, $limit, $offset);
        }

        public function make_suggestions($scope, $data) {
            if ($scope === "local")
            {
                $sugg = array();
                if ($data["nome_opportunita"] === "Analisi")
                {
                    $sugg["comapp"] = $this->makeref($data["id_opportunita"], "sales_comapp/{{v}}");
                }
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