<?php

namespace WolfMVC\WS\Crm\Cd {

//    use \WolfMVC\WS\Apiresource as Apiresource;

    class Account extends \WolfMVC\WS\Apiresource {

        protected static $_selectable = true;
        protected static $_creatable = false;
        protected static $_updatable = false;
        protected static $_deletable = false;
        protected static $_id = "id_cliente";
        protected static $_modeltype = "activerecord";
        protected static $_modelsinvolved = array("Vtaccount", "Vtaccountscf", "Vtcrmentity");
        protected static $_mainmodel = "Vtaccount";
//        protected $_modelwrapper = array();

        /**
         * @readwrite
         */
        protected static $_vm = array(
            "id_cliente" => array(
                "content" => "integer"
            ),
            "ragione_sociale" => array(
                "content" => "string"
            ),
            "numero_cliente" => array(
                "content" => "string"
            ),
            "partita_iva" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "codice_fiscale" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "contatti" => array(
                "content" => array(
                    "telefono" => array(
                        "content" => "string"
                    ),
                    "email" => array(
                        "content" => "string",
                        "inmodel" => "email1"
                    )
                )
            ),
            "eco" => array(
                "content" => array(
                    "fatturato" => array(
                        "content" => "integer"
                    ),
                    "dipendenti" => array(
                        "content" => "integer"
                    ),
                    "fascia_fatturato" => array(
                        "content" => "string",
                        "from" => "cf"
                    ),
                    "classe_dipendenti" => array(
                        "content" => "string",
                        "from" => "cf"
                    ),
                    "forma_giuridica" => array(
                        "content" => "string",
                        "from" => "cf"
                    )
                )
            ),
            "venditore" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "telemarketing" => array(
                "content" => "string",
                "from" => "cf"
            ),
            "descrizione" => array(
                "content" => "string",
                "from" => "ent",
                "inmodel" => "description"
            ),
        );
        protected static $_staticFilters = array(
            "ent" => array(
                "deleted" => array(
                    array("£f£ = ?", 0)
                )
            )
        );

        public function __construct($options = array()) {
            parent::__construct($options);
        }

        public function select($id, $filters, $limit, $offset) {
            return parent::select($id, $filters, $limit, $offset);
        }

    }

}
?>