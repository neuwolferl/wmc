<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Vtiger extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    public function script_including() {
        
    }

    /**
     * @protected
     */
    public function index() {
        echo "no data";
        return;
    }

    public static function AnaAppStatus($p_input, $p_status = "", $p_esito_visita = "", $p_comment = NULL) {
        $stati = array(
            "_ANAAPP_PROSPECTINGOLD" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Visita analista",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Da confermare"
            ),
            "_ANAAPP_QUALIFICATION" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Conferma analisi",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Da confermare"
            ),
            "_ANAAPP_QUALIFIED" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Analisi",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Confermato"
            ),
            "_ANAAPP_UNQUALIFIED" => array(
                "stadio_vendita" => "Closed Lost",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "Analisi non effettuata",
                "esito_conferma" => "Non confermato"
            ),
            "_ANAAPP_CALLAGAIN" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Richiamo",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Richiamo"
            ),
            "_ANAAPP_PASSAGAIN" => array(
                "stadio_vendita" => "Negotiation/Review",
                "prossimo_step" => "Ripasso analista",
                "probabilita" => "50",
                "esito_visita" => "Ripasso",
                "esito_conferma" => "Confermato"
            ),
//            "_ANAAPP_AUTOCLOSE" => array(
//                "stadio_vendita" => "Chiuso automaticamente",
//                "prossimo_step" => "Gestione manuale",
//                "probabilita" => "0",
//                "esito_visita" => "",
//                "esito_conferma" => "Confermato"
//            ),
//            "_ANAAPP_SYSTEMCANCELLED" => array(
//                "stadio_vendita" => "Annullato-Sistema",
//                "prossimo_step" => "",
//                "probabilita" => "0",
//                "esito_visita" => "",
//                "esito_conferma" => ""
//            ),
            "_ANAAPP_GO" => array(
                "stadio_vendita" => "Closed Won",
                "prossimo_step" => "Progetto",
                "probabilita" => "100",
                "esito_visita" => "Go",
                "esito_conferma" => "Confermato"
            ),
            "_ANAAPP_NOGO" => array(
                "stadio_vendita" => "Closed Lost",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "No Go",
                "esito_conferma" => "Confermato"
            )
        );
        if (is_int($p_input) || is_string($p_input))
        {
            $p_potentialid = $p_input;
            ActiveRecord\Config::initialize(
                    \WolfMVC\Registry::get("activerecord_initializer")
            );
            $options = array(
                'conditions' => array("potentialid = ? AND potentialname LIKE '%progetto%'", $p_potentialid),
                'include' => array('cf', 'ent', "account" => array("cf"))
            );
            $potentials = AR\Vtpotential::first($options);
            if ($potentials === NULL || $potentials->ent->deleted == 1)
            {

                return array(false, 404, NULL);
            }
        }
        else if ($p_input instanceof AR\Vtpotential)
        {
            $potentials = $p_input;
        }
        if (isset($p_status) && !empty($p_status))
        {
            if (isset($stati[$p_status]))
            {
                $change = false;
                $status_vm = $stati[$p_status];
                //codice per salvare
                if ($potentials->stadio_vendita !== $status_vm["stadio_vendita"])
                {
                    $change = true;
                }
                $potentials->stadio_vendita = $status_vm["stadio_vendita"];
                $potentials->prossimo_step = $status_vm["prossimo_step"];
                if ($potentials->prossimo_step !== $status_vm["prossimo_step"])
                {
                    $change = true;
                }
                $potentials->probabilita = $status_vm["probabilita"];
                if ($potentials->probabilita !== $status_vm["probabilita"])
                {
                    $change = true;
                }
                $esito_visita = $status_vm["esito_visita"];
                if (isset($esito_visita[0]) && $esito_visita[0] === "?")
                {
                    $esito_visita = str_ireplace("?", "", $esito_visita);
                    $esito_visita = explode(",", $esito_visita);
                    if (!isset($p_esito_visita) || !in_array($p_esito_visita, $esito_visita))
                    {
                        // se non è specificato, mantengo il dato precedente  se valido altrimenti scelgo il primo
                        if (!in_array($potentials->cf->esito_visita, $esito_visita))
                        {
                            $p_esito_visita = $esito_visita[0];
                        }
                        else
                        {
                            $p_esito_visita = $potentials->cf->esito_visita;
                        }
                    }
                    if ($potentials->cf->esito_visita !== $p_esito_visita)
                        $change = true;
                    $esito_visita = $p_esito_visita;
                }
                $potentials->cf->esito_visita = $esito_visita;
                $potentials->cf->esito_conferma = $status_vm["esito_conferma"];
                if ($potentials->cf->esito_conferma !== $status_vm["esito_conferma"])
                {
                    $change = true;
                }
                $saveresult = ($potentials->save() && $potentials->cf->save());
                $what_element = "set" . $p_status;
                $piva = $potentials->account->cf->partita_iva;
                $what = array("op", "Vtiger", "potentials", $what_element);
                $parameters = array(
                    "tmk" => $potentials->cf->telemarketing,
                    "com" => $potentials->cf->venditore,
                    "time" => $potentials->data_chiusura_attesa
                );
                if (isset($p_esito_visita) && $p_esito_visita)
                {
                    $parameters["cause"] = $p_esito_visita;
                }
                $logres = self::putLog($piva, $what, $parameters, $p_comment);
                $out = array();
                if ($logres[1] !== 200)
                {
                    $out["log"] = $logres;
                }
                $out[] = $saveresult;
                return ($saveresult ? array($p_status, 200, $change) : array(true, 500, null));
            }
            else
            {
                return array("Cant set unknown status " . $p_status, 404, NULL);
            }
        }
        else
        {
            $out = array();
            $vm = array(
                "stadio_vendita" => $potentials->stadio_vendita,
                "prossimo_step" => $potentials->prossimo_step,
                "probabilita" => $potentials->probabilita,
                "esito_visita" => $potentials->cf->esito_visita,
                "esito_conferma" => $potentials->cf->esito_conferma
            );

            $stato = WolfMVC\ArrayMethods::matchVMAmong($vm, $stati, true);
            if ($stato)
            {
                return array($stato, 200, false);
            }
            else
            {
                return array("stato indefinito", 404, false);
            }
        }
    }

    public function ws___AnaAppStatus() {
        $this->setJsonWS();
        $pars = array(
            "potentialid" => WolfMVC\RequestMethods::get("potentialid", false),
            "status" => WolfMVC\RequestMethods::get("status", false),
            "esito_visita" => WolfMVC\RequestMethods::get("esito_visita", false),
            "comment" => WolfMVC\RequestMethods::get("comment", false),
        );
        if (isset($pars["potentialid"]))
        {
            $pars["potentialid"] = trim($pars["potentialid"]);
        }
        if (isset($pars["status"]))
        {
            $pars["status"] = trim($pars["status"]);
        }
        if (isset($pars["esito_visita"]))
        {
            $pars["esito_visita"] = trim($pars["esito_visita"]);
        }
        if (isset($pars["comment"]))
        {
            $pars["comment"] = trim($pars["comment"]);
        }
//        $pars = $this->retrievePars(array(
//            array("potentialid", "get", true),
//            array("status", "get", false),
//            array("esito_visita", "get", false)
//        ));
        if (!isset($pars["potentialid"]) || !$pars["potentialid"])
        {
            $this->_setResponseStatus(400);
            echo json_encode("Invalid potentialid");
            return;
        }
        $res = self::AnaAppStatus($pars["potentialid"], $pars["status"], $pars["esito_visita"], $pars["comment"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;
    }

    public static function InferAnaAppStatus($p_potentialid) {
        $stati = array(
            "_ANAAPP_PROSPECTINGOLD" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Visita analista",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Da confermare"
            ),
            "_ANAAPP_QUALIFICATION" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Conferma analisi",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Da confermare"
            ),
            "_ANAAPP_QUALIFIED" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Analisi",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Confermato"
            ),
            "_ANAAPP_UNQUALIFIED" => array(
                "stadio_vendita" => "Closed Lost",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "Analisi non effettuata",
                "esito_conferma" => "Non confermato"
            ),
            "_ANAAPP_CALLAGAIN" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Richiamo",
                "probabilita" => "50",
                "esito_visita" => "",
                "esito_conferma" => "Richiamo"
            ),
            "_ANAAPP_PASSAGAIN" => array(
                "stadio_vendita" => "Negotiation/Review",
                "prossimo_step" => "Ripasso analista",
                "probabilita" => "50",
                "esito_visita" => "Ripasso",
                "esito_conferma" => "Confermato"
            ),
//            "_ANAAPP_AUTOCLOSE" => array(
//                "stadio_vendita" => "Chiuso automaticamente",
//                "prossimo_step" => "Gestione manuale",
//                "probabilita" => "0",
//                "esito_visita" => "",
//                "esito_conferma" => "Confermato"
//            ),
//            "_ANAAPP_SYSTEMCANCELLED" => array(
//                "stadio_vendita" => "Annullato-Sistema",
//                "prossimo_step" => "",
//                "probabilita" => "0",
//                "esito_visita" => "",
//                "esito_conferma" => ""
//            ),
            "_ANAAPP_GO" => array(
                "stadio_vendita" => "Closed Won",
                "prossimo_step" => "Progetto",
                "probabilita" => "100",
                "esito_visita" => "Go",
                "esito_conferma" => "Confermato"
            ),
            "_ANAAPP_NOGO" => array(
                "stadio_vendita" => "Closed Lost",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "No Go",
                "esito_conferma" => "Confermato"
            )
        );

        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (is_int($p_potentialid) || is_string($p_potentialid))
        {
            ActiveRecord\Config::initialize(
                    \WolfMVC\Registry::get("activerecord_initializer")
            );
            $options = array(
                'conditions' => array("potentialid = ? AND potentialname LIKE '%progetto%'", $p_potentialid),
                'include' => array('cf', 'ent')
            );
            try {
                $potentials = AR\Vtpotential::first($options);
            } catch (ActiveRecord\RecordNotFound $e) {
                return array(false, 404, NULL);
            }
            if ($potentials->ent->deleted == 1)
            {

                return array(false, 404, NULL);
            }
        }
        else if ($p_potentialid instanceof AR\Vtpotential)
        {
            $potentials = $p_potentialid;
            $p_potentialid = $potentials->id_opportunita;
        }
//        $options = array(
//            'conditions' => array("potentialid = ? AND potentialname LIKE '%progetto%'", $p_potentialid),
//            'include' => array('cf', 'ent')
//        );
//        $potentials = AR\Vtpotential::first($options);
//        if ($potentials === NULL || $potentials->ent->deleted == 1)
//        {
//            return array(false, 404);
//        }

        $out = array();
        $vm = array(
            "stadio_vendita" => $potentials->stadio_vendita,
            "prossimo_step" => $potentials->prossimo_step,
            "probabilita" => $potentials->probabilita,
            "esito_visita" => $potentials->cf->esito_visita,
            "esito_conferma" => $potentials->cf->esito_conferma
        );
        if ($potentials->stadio_vendita === "Closed Lost")
        {
            return array("_ANAAPP_NOGO", 200);
        }
        if ($potentials->stadio_vendita === "Closed Won")
        {
            return array("_ANAAPP_GO", 200);
        }
//        if ($potentials->stadio_vendita === "Annullato-Sistema")
//        {
//            return array("_COMAPP_SYSTEMCANCELLED", 200);
//        }
//        if ($potentials->stadio_vendita === "Chiuso automaticamente")
//        {
//            return array("_COMAPP_AUTOCLOSE", 200);
//        }
//        if ($potentials->stadio_vendita === "Chiuso automaticamente")
//        {
//            return array("_COMAPP_AUTOCLOSE", 200);
//        }
        if ($potentials->stadio_vendita === "Negotiation/Review")
        {
            return array("_ANAAPP_PASSAGAIN", 200);
        }
        if ($potentials->stadio_vendita === "Qualification")
        {
            $stato = WolfMVC\ArrayMethods::matchVMAmong($vm, $stati, true);
            return array($stato ? $stato : "_ANAAPP_QUALIFICATION", 200);
        }
        if ($potentials->stadio_vendita === "Prospecting")
        {
            if ($potentials->cf->esito_conferma == "Richiamo")
            {
                return array("_ANAAPP_CALLAGAIN", 200);
            }
            if ($potentials->cf->esito_conferma == "Da confermare")
            {
                return array("_ANAAPP_PROSPECTINGOLD", 200);
            }
            $stato = WolfMVC\ArrayMethods::matchVMAmong($vm, $stati, true);
            return array($stato ? $stato : "_ANAAPP_QUALIFIED", 200);
        }
        return array("stato indefinito", 404);
    }

    public function ws___InferAnaAppStatus() {
        $this->setJsonWS();
        $pars = $this->retrievePars(array(
            array("potentialid", "get", true)
        ));
        $res = self::InferAnaAppStatus($pars["potentialid"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode($res[0]);
    }

    public static function ComAppStatus($p_input, $p_status = "", $p_esito_visita = "", $p_comment = NULL) {
        $stati = array(
            "_COMAPP_QUALIFICATION" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Conferma TMK",
                "probabilita" => "10",
                "esito_visita" => "",
                "esito_conferma" => "Da confermare"
            ),
            "_COMAPP_QUALIFIED" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Visita venditore",
                "probabilita" => "10",
                "esito_visita" => "",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_UNQUALIFIED" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Richiamo TMK",
                "probabilita" => "0",
                "esito_visita" => "",
                "esito_conferma" => "Non confermato"
            ),
            "_COMAPP_CALLAGAIN" => array(
                "stadio_vendita" => "Negotiation/Review",
                "prossimo_step" => "Rifissare appuntamento",
                "probabilita" => "0",
                "esito_visita" => "?Assente,Imp Commerciale,Non decisionale?",
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
                "esito_visita" => "",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_SYSTEMCANCELLED" => array(
                "stadio_vendita" => "Annullato-Sistema",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "",
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
                "esito_visita" => "?No quota,No show,Altri consulenti,Non interessato,No vendita,Non decisionale?",
                "esito_conferma" => "Confermato"
            )
        );
        if (is_int($p_input) || is_string($p_input))
        {
            $p_potentialid = $p_input;
            ActiveRecord\Config::initialize(
                    \WolfMVC\Registry::get("activerecord_initializer")
            );
            $options = array(
                'conditions' => array("potentialid = ? AND potentialname = 'Analisi'", $p_potentialid),
                'include' => array('cf', 'ent', "account" => array("cf"))
            );
            $potentials = AR\Vtpotential::first($options);
            if ($potentials === NULL || $potentials->ent->deleted == 1)
            {

                return array(false, 404, NULL);
            }
        }
        else if ($p_input instanceof AR\Vtpotential)
        {
            $potentials = $p_input;
        }
        if (isset($p_status) && !empty($p_status))
        {
            if (isset($stati[$p_status]))
            {
                $change = false;
                $status_vm = $stati[$p_status];
                //codice per salvare
                if ($potentials->stadio_vendita !== $status_vm["stadio_vendita"])
                {
                    $change = true;
                }
                $potentials->stadio_vendita = $status_vm["stadio_vendita"];
                $potentials->prossimo_step = $status_vm["prossimo_step"];
                if ($potentials->prossimo_step !== $status_vm["prossimo_step"])
                {
                    $change = true;
                }
                $potentials->probabilita = $status_vm["probabilita"];
                if ($potentials->probabilita !== $status_vm["probabilita"])
                {
                    $change = true;
                }
                $esito_visita = $status_vm["esito_visita"];
                if (isset($esito_visita[0]) && $esito_visita[0] === "?")
                {
                    $esito_visita = str_ireplace("?", "", $esito_visita);
                    $esito_visita = explode(",", $esito_visita);
                    if (!isset($p_esito_visita) || !in_array($p_esito_visita, $esito_visita))
                    {
//                        $this->_setResponseStatus(400);
//                        echo "Esito visita must be in " . join(", ", $esito_visita);
//                        return;
                        // se non è specificato, mantengo il dato precedente  se valido altrimenti scelgo il primo
                        if (!in_array($potentials->cf->esito_visita, $esito_visita))
                        {
                            $p_esito_visita = $esito_visita[0];
                        }
                        else
                        {
                            $p_esito_visita = $potentials->cf->esito_visita;
                        }
                    }
                    if ($potentials->cf->esito_visita !== $p_esito_visita)
                        $change = true;
                    $esito_visita = $p_esito_visita;
                }
                $potentials->cf->esito_visita = $esito_visita;
                $potentials->cf->esito_conferma = $status_vm["esito_conferma"];
                if ($potentials->cf->esito_conferma !== $status_vm["esito_conferma"])
                {
                    $change = true;
                }
                $saveresult = ($potentials->save() && $potentials->cf->save());
                $what_element = "set" . $p_status;
                $piva = $potentials->account->cf->partita_iva;
                $what = array("op", "Vtiger", "potentials", $what_element);
                $parameters = array(
                    "tmk" => $potentials->cf->telemarketing,
                    "com" => $potentials->cf->venditore,
                    "time" => $potentials->data_chiusura_attesa
                );
                if (isset($p_esito_visita) && $p_esito_visita)
                {
                    $parameters["cause"] = $p_esito_visita;
                }
                $logres = self::putLog($piva, $what, $parameters, $p_comment);
                $out = array();
                if ($logres[1] !== 200)
                {
                    $out["log"] = $logres;
                }
                $out[] = $saveresult;
                return ($saveresult ? array($p_status, 200, $change) : array(true, 500, null));
            }
            else
            {
                return array("Cant set unknown status " . $p_status, 404, NULL);
            }
        }
        else
        {
            $out = array();
            $vm = array(
                "stadio_vendita" => $potentials->stadio_vendita,
                "prossimo_step" => $potentials->prossimo_step,
                "probabilita" => $potentials->probabilita,
                "esito_visita" => $potentials->cf->esito_visita,
                "esito_conferma" => $potentials->cf->esito_conferma
            );

            $stato = WolfMVC\ArrayMethods::matchVMAmong($vm, $stati, true);
            if ($stato)
            {
                return array($stato, 200, false);
            }
            else
            {
                return array("stato indefinito", 404, false);
            }
        }
    }

    public function ws___ComAppStatus() {
        $this->setJsonWS();
        $pars = array(
            "potentialid" => WolfMVC\RequestMethods::get("potentialid", false),
            "status" => WolfMVC\RequestMethods::get("status", false),
            "esito_visita" => WolfMVC\RequestMethods::get("esito_visita", false),
            "comment" => WolfMVC\RequestMethods::get("comment", false),
        );
        if (isset($pars["potentialid"]))
        {
            $pars["potentialid"] = trim($pars["potentialid"]);
        }
        if (isset($pars["status"]))
        {
            $pars["status"] = trim($pars["status"]);
        }
        if (isset($pars["esito_visita"]))
        {
            $pars["esito_visita"] = trim($pars["esito_visita"]);
        }
        if (isset($pars["comment"]))
        {
            $pars["comment"] = trim($pars["comment"]);
        }
//        $pars = $this->retrievePars(array(
//            array("potentialid", "get", true),
//            array("status", "get", false),
//            array("esito_visita", "get", false)
//        ));
        if (!isset($pars["potentialid"]) || !$pars["potentialid"])
        {
            $this->_setResponseStatus(400);
            echo json_encode("Invalid potentialid");
            return;
        }
        $res = self::ComAppStatus($pars["potentialid"], $pars["status"], $pars["esito_visita"], $pars["comment"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode($res[0]);
        return;
    }

    public static function InferComAppStatus($p_potentialid) {
        $stati = array(
            "_COMAPP_QUALIFICATION" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Conferma TMK",
                "probabilita" => "10",
                "esito_visita" => "",
                "esito_conferma" => "Da confermare"
            ),
            "_COMAPP_QUALIFIED" => array(
                "stadio_vendita" => "Prospecting",
                "prossimo_step" => "Visita venditore",
                "probabilita" => "10",
                "esito_visita" => "",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_UNQUALIFIED" => array(
                "stadio_vendita" => "Qualification",
                "prossimo_step" => "Richiamo TMK",
                "probabilita" => "0",
                "esito_visita" => "",
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
                "esito_visita" => "",
                "esito_conferma" => "Confermato"
            ),
            "_COMAPP_SYSTEMCANCELLED" => array(
                "stadio_vendita" => "Annullato-Sistema",
                "prossimo_step" => "",
                "probabilita" => "0",
                "esito_visita" => "",
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
                "esito_visita" => "?No quota,No show,Altri consulenti,Non interessato,No vendita,Non decisionale?",
                "esito_conferma" => "Confermato"
            )
        );

        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );

        if (is_int($p_potentialid) || is_string($p_potentialid))
        {
            ActiveRecord\Config::initialize(
                    \WolfMVC\Registry::get("activerecord_initializer")
            );
            $options = array(
                'conditions' => array("potentialid = ? AND potentialname = 'Analisi'", $p_potentialid),
                'include' => array('cf', 'ent', "account" => array("cf"))
            );
            try {
                $potentials = AR\Vtpotential::first($options);
            } catch (ActiveRecord\RecordNotFound $e) {
                return array(false, 404, NULL);
            }
            if ($potentials->ent->deleted == 1)
            {

                return array(false, 404, NULL);
            }
        }
        else if ($p_potentialid instanceof AR\Vtpotential)
        {
            $potentials = $p_potentialid;
            $p_potentialid = $potentials->id_opportunita;
        }
//        $options = array(
//            'conditions' => array("potentialid = ? AND potentialname = 'Analisi'", $p_potentialid),
//            'include' => array('cf', 'ent')
//        );
//        $potentials = AR\Vtpotential::first($options);
//        if ($potentials === NULL || $potentials->ent->deleted == 1)
//        {
//            return array(false, 404);
//        }

        $out = array();
        $vm = array(
            "stadio_vendita" => $potentials->stadio_vendita,
            "prossimo_step" => $potentials->prossimo_step,
            "probabilita" => $potentials->probabilita,
            "esito_visita" => $potentials->cf->esito_visita,
            "esito_conferma" => $potentials->cf->esito_conferma
        );
        if ($potentials->stadio_vendita === "Closed Lost")
        {
            return array("_COMAPP_NOCHECK", 200);
        }
        if ($potentials->stadio_vendita === "Closed Won")
        {
            return array("_COMAPP_CHECK", 200);
        }
        if ($potentials->stadio_vendita === "Annullato-Sistema")
        {
            return array("_COMAPP_SYSTEMCANCELLED", 200);
        }
        if ($potentials->stadio_vendita === "Chiuso automaticamente")
        {
            return array("_COMAPP_AUTOCLOSE", 200);
        }
        if ($potentials->stadio_vendita === "Chiuso automaticamente")
        {
            return array("_COMAPP_AUTOCLOSE", 200);
        }
        if ($potentials->stadio_vendita === "Negotiation/Review")
        {
            return array("_COMAPP_CALLAGAIN", 200);
        }
        if ($potentials->stadio_vendita === "Qualification")
        {
            $stato = WolfMVC\ArrayMethods::matchVMAmong($vm, $stati, true);
            return array($stato ? $stato : "_COMAPP_QUALIFICATION", 200);
        }
        if ($potentials->stadio_vendita === "Prospecting")
        {
            $stato = WolfMVC\ArrayMethods::matchVMAmong($vm, $stati, true);
            return array($stato ? $stato : "_COMAPP_QUALIFIED", 200);
        }
        return array("stato indefinito", 404);
    }

    public function ws___InferComAppStatus() {
        $this->setJsonWS();
        $pars = $this->retrievePars(array(
            array("potentialid", "get", true)
        ));
        $res = self::InferComAppStatus($pars["potentialid"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode($res[0]);
    }

    public static function editAccount($account_vm) {
        if (!is_array($account_vm) || (!isset($account_vm["accountid"]) && !isset($account_vm["id_cliente"])))
        {
            return array($account_vm, 400, "No accountid specified");
        }
        $accountid = (isset($account_vm["accountid"]) ? $account_vm["accountid"] : $account_vm["id_cliente"]);
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $account = AR\Vtaccount::find($accountid, array('include' => array('ent', 'cf', 'billads')));
        } catch (\ActiveRecord\RecordNotFound $e) {
            if (isset($account_vm["error_on_not_found"]) && $account_vm["error_on_not_found"])
                return array($account_vm, 404, $e->getMessage());
            else
                return array($account_vm, 200, $e->getMessage());
        } catch (\Exception $e) {
            return array($account_vm, 500, $e->getMessage());
        }
        if ($account->ent->deleted != 0)
        {
            return array($account_vm, 404, "Deleted record");
        }
        foreach ($account_vm as $k => $v) {
            if ($k === "id_cliente" || $k === "accountid" || $k === "numero_cliente" || $k === "account_no" || $k === "partita_iva" || $k === "cf_641" || $k === "ragione_sociale" || $k === "accountname")
            {
                continue;
            }
            $set = true;
            try {
                $account->{$k} = $v;
            } catch (\ActiveRecord\UndefinedPropertyException $e) {
                $set = false;
            }
            if (!$set)
            {
                try {
                    $account->cf->{$k} = $v;
                } catch (\ActiveRecord\UndefinedPropertyException $e) {
                    $set = false;
                }
            }
            if (!$set)
            {
                try {
                    $account->billads->{$k} = $v;
                } catch (\ActiveRecord\UndefinedPropertyException $e) {
                    $set = false;
                }
            }
        }
        $account->billads->save();
        $account->cf->save();
        $account->save();

        return array(true, 200, true);
    }

    public static function retrieveAccountByPiva($piva) { // da finire
        if (!isset($piva) || !is_string($piva))
        {
            return array($piva, 400, "No valid piva specified");
        }
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $account = AR\Vtaccountscf::all(array(
                        'conditions' => array("cf_641 = ?", $piva),
                        'include' => array('acc', 'ent', 'billads')
            ));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array($piva, 404, $e->getMessage());
        } catch (\Exception $e) {
            return array($piva, 500, $e->getMessage());
        }
        $notdeletedacc = array();
        foreach ($account as $k => $a) {
            if ($a->ent->deleted == 0)
            {
                array_push($notdeletedacc, $a);
            }
        }
        $account = $notdeletedacc;
        if (!count($account))
        {
            return array($piva, 404, false);
        }
        $out = array();
        $out["account"] = array_merge($account[count($account) - 1]->attributes(true), $account[count($account) - 1]->acc->attributes(true), $account[count($account) - 1]->billads->attributes(true));
        $contatti = $account[count($account) - 1]->acc->contacts;
        foreach ($contatti as $k => $c) {
            if ($c->ent->deleted != 0)
            {
                array_splice($contatti, $k, 1);
            }
        }
        $out["contatti"] = array_map(function($x) {
            return $x->attributes(true);
        }, $contatti);
        $opportunita = $account[count($account) - 1]->acc->potentials;
        $opportunitaAnalisi = array();
        $opportunitaProgetto = array();
        foreach ($opportunita as $k => $o) {
            if ($o->ent->deleted != 0)
            {
                continue;
            }
            if (strpos($o->nome_opportunita, "Analisi") !== FALSE)
            {
                array_push($opportunitaAnalisi, $o);
            }
            else if (strpos($o->nome_opportunita, "PROGETTO") !== FALSE)
            {
                array_push($opportunitaProgetto, $o);
            }
        }
        $out["contatti"] = array_map(function($x) {
            return $x->attributes(true);
        }, $contatti);

        $out["opportunitaAnalisi"] = array_map(function($x) {
            $stato = Vtiger::ComAppStatus($x);
            if ($stato[1] === 404)
            {
                $stato = Vtiger::InferComAppStatus($x);
            }
            return array_merge($x->attributes(true), $x->cf->attributes(true), array("stato" => $stato[0]));
        }, $opportunitaAnalisi);
        $out["opportunitaProgetto"] = array_map(function($x) {
            $stato = Vtiger::AnaAppStatus($x);
            if ($stato[1] === 404)
            {
                $stato = Vtiger::InferAnaAppStatus($x);
            }
            return array_merge($x->attributes(true), $x->cf->attributes(true), array("stato" => $stato[0]));
        }, $opportunitaProgetto);

        return array($out, 200, true);
    }

    public function ws___retrieveAccountByPiva() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $piva = WolfMVC\RequestMethods::get("piva", FALSE);
        if ($piva === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        $response = self::retrieveAccountByPiva($piva);
        $this->_setResponseStatus($response[1]);
        echo json_encode(array($response[0], $response[2]));
    }

    public function ws___editAccount() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $account_vm = WolfMVC\RequestMethods::post("account_vm", FALSE);
        if ($account_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::editAccount($account_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[1]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function editPotential($potential_vm) {
        if (!is_array($potential_vm) || (!isset($potential_vm["potentialid"]) && !isset($potential_vm["id_opportunita"])))
        {
            return array($potential_vm, 400, "No potentialid specified");
        }
        $potentialid = (isset($potential_vm["potentialid"]) ? $potential_vm["potentialid"] : $potential_vm["id_opportunita"]);
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $potential = AR\Vtpotential::find($potentialid, array('include' => array('ent', 'cf')));
        } catch (\ActiveRecord\RecordNotFound $e) {
            if (isset($potential_vm["error_on_not_found"]) && $potential_vm["error_on_not_found"])
                return array($potential_vm, 404, $e->getMessage());
            else
                return array($potential_vm, 200, $e->getMessage());
        } catch (\Exception $e) {
            return array($potential_vm, 500, $e->getMessage());
        }
        if ($potential->ent->deleted != 0)
        {
            return array($potential_vm, 404, "Deleted record");
        }
        foreach ($potential_vm as $k => $v) {
            if ($k === "description" || $k === "descrizione")
            {
                $potential->ent->description = $v;
            }
            if ($k === "id_opportunita" || $k === "potentialid" || $k === "numero_opportunita" || $k === "potential_no" || $k === "nome_opportunita" || $k === "potentialname" || $k === "collegato_a" || $k === "related_to")
            {
                continue;
            }
            $set = true;
            try {
                $potential->{$k} = $v;
            } catch (\ActiveRecord\UndefinedPropertyException $e) {
                $set = false;
            }
            if (!$set)
            {
                try {
                    $potential->cf->{$k} = $v;
                } catch (\ActiveRecord\UndefinedPropertyException $e) {
                    $set = false;
                }
            }
        }
        $potential->ent->save();
        $potential->cf->save();
        $potential->save();

        return array(true, 200, true);
    }

    public function ws___editPotential() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $potential_vm = WolfMVC\RequestMethods::post("potential_vm", FALSE);
        if ($potential_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::editPotential($potential_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[2]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function closeAllActivities($accountid) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $account = AR\Vtaccount::find($accountid, array('include' => array('ent' => array('activityrel' => array('activity')))));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array($accountid, 200, $e->getMessage());
        } catch (\Exception $e) {
            return array($accountid, 500, $e->getMessage());
        }
        try {
            $acts = $account->ent->activityrel;
            foreach ($acts as $k => $a) {
                $a->activity->stato = "Held";
                $a->activity->save();
            }
        } catch (\Exception $e) {
            return array($accountid, 500, $e->getMessage());
        }
        return array(true, 200, true);
    }

    public function ws___closeAllActivities() {
        $this->setJsonWS();
        $accountid = WolfMVC\RequestMethods::get("accountid", FALSE);
        if (!isset($accountid) || $accountid === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid accountid value");
            return;
        }
        $res = self::closeAllActivities($accountid);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;
    }

    public static function editActivity($activity_vm) {
        if (!is_array($activity_vm) || (!isset($activity_vm["activityid"]) && !isset($activity_vm["id_attivita"])))
        {
            return array($activity_vm, 400, "No activityid specified");
        }
        $activityid = (isset($activity_vm["activityid"]) ? $activity_vm["activityid"] : $activity_vm["id_attivita"]);
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );

        try {
            $activity = AR\Vtactivity::find($activityid, array('include' => array('ent', 'cf')));
        } catch (\ActiveRecord\RecordNotFound $e) {
            if (isset($activity_vm["error_on_not_found"]) && $activity_vm["error_on_not_found"])
                return array($activity_vm, 404, $e->getMessage());
            else
                return array($activity_vm, 200, $e->getMessage());
        } catch (\Exception $e) {
            return array($activity_vm, 500, $e->getMessage());
        }
        if ($activity->ent->deleted != 0)
        {
            return array($activity_vm, 404, "Deleted record");
        }
        foreach ($activity_vm as $k => $v) {
            if ($k === "description" || $k === "descrizione")
            {
                $activity->ent->description = $v;
            }
            if ($k === "id_opportunita" || $k === "activityid" || $k === "nome_attivita" || $k === "subject")
            {
                continue;
            }
            $set = true;
            try {
                $activity->{$k} = $v;
            } catch (\ActiveRecord\UndefinedPropertyException $e) {
                $set = false;
            }
            if (!$set)
            {
                try {
                    $activity->cf->{$k} = $v;
                } catch (\ActiveRecord\UndefinedPropertyException $e) {
                    $set = false;
                }
            }
        }
        $activity->ent->save();
        $activity->cf->save();
        $activity->save();

        return array(true, 200, true);
    }

    public function ws___editActivity() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $activity_vm = WolfMVC\RequestMethods::post("activity_vm", FALSE);
        if ($activity_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::editActivity($activity_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[2]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function editContact($contact_vm) {
        if (!is_array($contact_vm) || (!isset($contact_vm["contactid"]) && !isset($contact_vm["id_contatto"])))
        {
            return array($contact_vm, 400, "No contactid specified");
        }
        $contactid = (isset($contact_vm["contactid"]) ? $contact_vm["contactid"] : $contact_vm["id_contatto"]);
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $contact = AR\Vtcontact::find($contactid, array('include' => array('ent')));
        } catch (\ActiveRecord\RecordNotFound $e) {
            if (isset($contact_vm["error_on_not_found"]) && $contact_vm["error_on_not_found"])
                return array($contact_vm, 404, $e->getMessage());
            else
                return array($contact_vm, 200, $e->getMessage());
        } catch (\Exception $e) {
            return array($contact_vm, 500, $e->getMessage());
        }
        if ($contact->ent->deleted != 0)
        {
            return array($contact_vm, 404, "Deleted record");
        }
        foreach ($contact_vm as $k => $v) {
            if ($k === "id_contatto" || $k === "contactid" || $k === "id_cliente" || $k === "accountid")
            {
                continue;
            }

            try {
                $contact->{$k} = $v;
            } catch (\ActiveRecord\UndefinedPropertyException $e) {
                //skip
            }
        }
        $contact->save();

        return array(true, 200, true);
    }

    public function ws___editContact() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $contact_vm = WolfMVC\RequestMethods::post("contact_vm", FALSE);
        if ($contact_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::editContact($contact_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[1]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function addActivity($activity_vm) {
        if (!is_array($activity_vm))
        {
            return array($activity_vm, 400, "Invalid vm");
        }
        if (!isset($activity_vm["parent_id"]) && !isset($activity_vm["id_cliente"]))
        {
            return array($activity_vm, 400, "No account specified");
        }
        if (!isset($activity_vm["subject"]) && !isset($activity_vm["nome_attivita"]))
        {
            return array($activity_vm, 400, "No subject specified");
        }

        $vtws = \WolfMVC\Registry::get("VTWS");

        $vtws_activity_vm = array(
            "subject" => (isset($activity_vm["subject"]) ? $activity_vm["subject"] : (isset($activity_vm["nome_attivita"]) ? $activity_vm["nome_attivita"] : "")),
            "date_start" => (isset($activity_vm["date_start"]) ? $activity_vm["date_start"] : (isset($activity_vm["data_inizio"]) ? $activity_vm["data_inizio"] : "")),
            "time_start" => (isset($activity_vm["time_start"]) ? $activity_vm["time_start"] : (isset($activity_vm["orario_inizio"]) ? $activity_vm["orario_inizio"] : "")),
            "due_date" => (isset($activity_vm["due_date"]) ? $activity_vm["due_date"] : (isset($activity_vm["data_fine"]) ? $activity_vm["data_fine"] : "")),
            "time_end" => (isset($activity_vm["time_end"]) ? $activity_vm["time_end"] : (isset($activity_vm["orario_fine"]) ? $activity_vm["orario_fine"] : "")),
            "duration_hours" => (isset($activity_vm["duration_hours"]) ? $activity_vm["duration_hours"] : (isset($activity_vm["durata_ore"]) ? $activity_vm["durata_ore"] : "")),
            "activitytype" => (isset($activity_vm["activitytype"]) ? $activity_vm["activitytype"] : (isset($activity_vm["tipo_attivita"]) ? $activity_vm["tipo_attivita"] : "")),
            "location" => (isset($activity_vm["location"]) ? $activity_vm["location"] : (isset($activity_vm["indirizzo"]) ? $activity_vm["indirizzo"] : "")),
            "description" => (isset($activity_vm["description"]) ? $activity_vm["description"] : (isset($activity_vm["descrizione"]) ? $activity_vm["descrizione"] : "")),
            "cf_649" => (isset($activity_vm["cf_649"]) ? $activity_vm["cf_649"] : (isset($activity_vm["telemarketing"]) ? $activity_vm["telemarketing"] : "")),
            "cf_650" => (isset($activity_vm["cf_650"]) ? $activity_vm["cf_650"] : (isset($activity_vm["venditore"]) ? $activity_vm["venditore"] : "")),
            "cf_651" => "New Business PMI",
            "cf_702" => (isset($activity_vm["potentialid"]) ? $activity_vm["potentialid"] : (isset($activity_vm["id_opportunita"]) ? $activity_vm["id_opportunita"] : "")),
            "contact_id" => '12x' . (isset($activity_vm["contact_id"]) ? $activity_vm["contact_id"] : (isset($activity_vm["id_contatto"]) ? $activity_vm["id_contatto"] : "")),
            "eventstatus" => "Planned",
            "parent_id" => '11x' . (isset($activity_vm["parent_id"]) ? $activity_vm["parent_id"] : (isset($activity_vm["id_cliente"]) ? $activity_vm["id_cliente"] : "")),
            "assigned_user_id" => "19x1"
        );
        try {
            $new_activity = $vtws->doCreate("Events", $vtws_activity_vm);
        } catch (\Exception $e) {
            return array($activity_vm, 404, $e->getMessage());
        }
        $lE = $vtws->lastError();
        if ($lE && !empty($lE))
        {
            return array($vtws_activity_vm, 500, $lE);
        }
        return array($new_activity, 200, $vtws->lastError());
    }

    public function ws___addActivity() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $activity_vm = WolfMVC\RequestMethods::post("activity_vm", FALSE);
        if ($activity_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::addActivity($activity_vm);
        $this->_setResponseStatus($response[1]);
//        if ($response[1] != 200)
//        {
        echo json_encode(array($response[0], $response[2]));
//        }
//        else
//        {
//            echo json_encode($response[0]);
//        }
    }

    public static function addContact($contact_vm) {
        if (!is_array($contact_vm))
        {
            return array($contact_vm, 400, "Invalid vm");
        }
        if (!isset($contact_vm["accountid"]) && !isset($contact_vm["id_cliente"]))
        {
            return array($contact_vm, 400, "No account specified");
        }
        if (!isset($contact_vm["lastname"]) && !isset($contact_vm["cognome"]))
        {
            return array($contact_vm, 400, "No lastname specified");
        }

        $vtws = \WolfMVC\Registry::get("VTWS");

        $vtws_contact_vm = array(
            "firstname" => (isset($contact_vm["firstname"]) ? $contact_vm["firstname"] : (isset($contact_vm["nome"]) ? $contact_vm["nome"] : "")),
            "phone" => (isset($contact_vm["phone"]) ? $contact_vm["phone"] : (isset($contact_vm["telefono"]) ? $contact_vm["telefono"] : "")),
            "lastname" => (isset($contact_vm["lastname"]) ? $contact_vm["lastname"] : (isset($contact_vm["cognome"]) ? $contact_vm["cognome"] : "")),
            "mobile" => (isset($contact_vm["mobile"]) ? $contact_vm["mobile"] : (isset($contact_vm["cellulare"]) ? $contact_vm["cellulare"] : "")),
            "title" => (isset($contact_vm["title"]) ? $contact_vm["title"] : (isset($contact_vm["ruolo"]) ? $contact_vm["ruolo"] : "")),
            "fax" => (isset($contact_vm["fax"]) ? $contact_vm["fax"] : (isset($contact_vm["fax"]) ? $contact_vm["fax"] : "")),
            "email" => (isset($contact_vm["email"]) ? $contact_vm["email"] : (isset($contact_vm["email"]) ? $contact_vm["email"] : "")),
            "account_id" => '11x' . (isset($contact_vm["accountid"]) ? $contact_vm["accountid"] : (isset($contact_vm["id_cliente"]) ? $contact_vm["id_cliente"] : "")),
            "assigned_user_id" => "19x1"
        );
        try {
            $new_contact = $vtws->doCreate("Contacts", $vtws_contact_vm);
        } catch (\Exception $e) {
            return array($contact_vm, 404, $e->getMessage());
        }
        return array($new_contact, 200, $vtws->lastError());
    }

    public function ws___addContact() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $contact_vm = WolfMVC\RequestMethods::post("contact_vm", FALSE);
        if ($contact_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::addContact($contact_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[1]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function addAccount($account_vm) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!is_array($account_vm))
        {
            return array($account_vm, 400, "Invalid vm");
        }
        if (!isset($account_vm["accountname"]) && !isset($account_vm["ragione_sociale"]))
        {
            return array($account_vm, 400, "No accountname specified");
        }

        $vtws = \WolfMVC\Registry::get("VTWS");

        $vtws_account_vm = array(
            "accountname" => (isset($account_vm["accountname"]) ? $account_vm["accountname"] : (isset($account_vm["ragione_sociale"]) ? $account_vm["ragione_sociale"] : "")),
            "phone" => (isset($account_vm["phone"]) ? $account_vm["phone"] : (isset($account_vm["telefono"]) ? $account_vm["telefono"] : "")),
            "website" => (isset($account_vm["website"]) ? $account_vm["website"] : (isset($account_vm["sito"]) ? $account_vm["sito"] : "")),
            "email1" => (isset($account_vm["email1"]) ? $account_vm["email1"] : (isset($account_vm["email"]) ? $account_vm["email"] : "")),
            "bill_street" => (isset($account_vm["bill_street"]) ? $account_vm["bill_street"] : (isset($account_vm["via"]) ? $account_vm["via"] : "")),
            "ship_street" => (isset($account_vm["ship_street"]) ? $account_vm["ship_street"] : (isset($account_vm["via"]) ? $account_vm["via"] : "")),
            "bill_city" => (isset($account_vm["bill_city"]) ? $account_vm["bill_city"] : (isset($account_vm["citta"]) ? $account_vm["citta"] : "")),
            "ship_city" => (isset($account_vm["ship_city"]) ? $account_vm["ship_city"] : (isset($account_vm["citta"]) ? $account_vm["citta"] : "")),
            "bill_state" => (isset($account_vm["bill_state"]) ? $account_vm["bill_state"] : (isset($account_vm["provincia"]) ? $account_vm["provincia"] : "")),
            "ship_state" => (isset($account_vm["ship_state"]) ? $account_vm["ship_state"] : (isset($account_vm["provincia"]) ? $account_vm["provincia"] : "")),
            "bill_code" => (isset($account_vm["bill_code"]) ? $account_vm["bill_code"] : (isset($account_vm["cap"]) ? $account_vm["cap"] : "")),
            "ship_code" => (isset($account_vm["ship_code"]) ? $account_vm["ship_code"] : (isset($account_vm["cap"]) ? $account_vm["cap"] : "")),
            "description" => (isset($account_vm["description"]) ? $account_vm["description"] : (isset($account_vm["descrizione"]) ? $account_vm["descrizione"] : "")),
            "cf_641" => (isset($account_vm["cf_641"]) ? $account_vm["cf_641"] : (isset($account_vm["partita_iva"]) ? $account_vm["partita_iva"] : "")),
            "cf_643" => (isset($account_vm["cf_643"]) ? $account_vm["cf_643"] : (isset($account_vm["venditore"]) ? $account_vm["venditore"] : "")),
            "cf_644" => (isset($account_vm["cf_644"]) ? $account_vm["cf_644"] : (isset($account_vm["telemarketing"]) ? $account_vm["telemarketing"] : "")),
            "cf_703" => (isset($account_vm["cf_703"]) ? $account_vm["cf_703"] : (isset($account_vm["fascia_fatturato_tmk"]) ? $account_vm["fascia_fatturato_tmk"] : "")),
            "cf_704" => (isset($account_vm["cf_704"]) ? $account_vm["cf_704"] : (isset($account_vm["classe_dipendenti_tmk"]) ? $account_vm["classe_dipendenti_tmk"] : "")),
            "cf_705" => (isset($account_vm["cf_705"]) ? $account_vm["cf_705"] : (isset($account_vm["forma_giuridica_tmk"]) ? $account_vm["forma_giuridica_tmk"] : "")),
            "assigned_user_id" => "19x1",
            "accounttype" => "New Business PMI"
        );
        try {
            $new_account = $vtws->doCreate("Accounts", $vtws_account_vm);
        } catch (\Exception $e) {
            return array($account_vm, 404, $e->getMessage());
        }

        $out = $vtws->lastError();
        return array($new_account, 200, $out);
    }

    public function ws___addAccount() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $account_vm = WolfMVC\RequestMethods::post("account_vm", FALSE);
        if ($account_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::addAccount($account_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[2]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function addPotential($potential_vm) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!is_array($potential_vm))
        {
            return array($potential_vm, 400, "Invalid vm");
        }
        if (!isset($potential_vm["accountid"]) && !isset($potential_vm["id_cliente"]))
        {
            return array($potential_vm, 400, "No account specified");
        }
        $accountid = (isset($potential_vm["accountid"]) ? $potential_vm["accountid"] : $potential_vm["id_cliente"] );
        if (!isset($potential_vm["potentialname"]) && !isset($potential_vm["nome_opportunita"]))
        {
            return array($potential_vm, 400, "No potentialname specified");
        }
        if (!isset($potential_vm["closingdate"]) && !isset($potential_vm["data_chiusura_attesa"]))
        {
            return array($potential_vm, 400, "No closingdate specified");
        }
        if (!isset($potential_vm["sales_stage"]) && !isset($potential_vm["stadio_vendita"]))
        {
            return array($potential_vm, 400, "No sales_stage specified");
        }

        $vtws = \WolfMVC\Registry::get("VTWS");

        $vtws_potential_vm = array(
            "potentialname" => (isset($potential_vm["potentialname"]) ? $potential_vm["potentialname"] : (isset($potential_vm["nome_opportunita"]) ? $potential_vm["nome_opportunita"] : "")),
            "amount" => (isset($potential_vm["amount"]) ? $potential_vm["amount"] : (isset($potential_vm["ammontare"]) ? $potential_vm["ammontare"] : "")),
            "closingdate" => (isset($potential_vm["closingdate"]) ? $potential_vm["closingdate"] : (isset($potential_vm["data_chiusura_attesa"]) ? $potential_vm["data_chiusura_attesa"] : "")),
            "nextstep" => (isset($potential_vm["nextstep"]) ? $potential_vm["nextstep"] : (isset($potential_vm["prossimo_step"]) ? $potential_vm["prossimo_step"] : "")),
            "leadsource" => (isset($potential_vm["leadsource"]) ? $potential_vm["leadsource"] : (isset($potential_vm["fonte_lead"]) ? $potential_vm["fonte_lead"] : "")),
            "sales_stage" => (isset($potential_vm["sales_stage"]) ? $potential_vm["sales_stage"] : (isset($potential_vm["stadio_vendita"]) ? $potential_vm["stadio_vendita"] : "")),
            "description" => (isset($potential_vm["description"]) ? $potential_vm["description"] : (isset($potential_vm["descrizione"]) ? $potential_vm["descrizione"] : "")),
            "cf_645" => (isset($potential_vm["cf_645"]) ? $potential_vm["cf_645"] : (isset($potential_vm["ammontare_pesato"]) ? $potential_vm["ammontare_pesato"] : "")),
            "cf_646" => (isset($potential_vm["cf_646"]) ? $potential_vm["cf_646"] : (isset($potential_vm["telemarketing"]) ? $potential_vm["telemarketing"] : "")),
            "cf_647" => (isset($potential_vm["cf_647"]) ? $potential_vm["cf_647"] : (isset($potential_vm["venditore"]) ? $potential_vm["venditore"] : "")),
            "cf_648" => (isset($potential_vm["cf_648"]) ? $potential_vm["cf_648"] : (isset($potential_vm["esito_visita"]) ? $potential_vm["esito_visita"] : "")),
            "cf_655" => (isset($potential_vm["cf_655"]) ? $potential_vm["cf_655"] : (isset($potential_vm["analista"]) ? $potential_vm["analista"] : "")),
            "cf_656" => (isset($potential_vm["cf_656"]) ? $potential_vm["cf_656"] : (isset($potential_vm["esito_conferma"]) ? $potential_vm["esito_conferma"] : "")),
            "cf_698" => '13x' . (isset($potential_vm["cf_698"]) ? $potential_vm["cf_698"] : (isset($potential_vm["pot_analisi_collegata"]) ? $potential_vm["pot_analisi_collegata"] : "")),
            "related_to" => '11x' . (isset($potential_vm["accountid"]) ? $potential_vm["accountid"] : (isset($potential_vm["id_cliente"]) ? $potential_vm["id_cliente"] : "")),
            "assigned_user_id" => "19x1",
            "opportunity_type" => "New Business PMI"
        );
        try {
            $new_potential = $vtws->doCreate("Potentials", $vtws_potential_vm);
        } catch (\Exception $e) {
            return array($potential_vm, 404, $e->getMessage());
        }
        $what = array("op", "Vtiger", "potentials", "Creation");
        $parameters = array(
            "tmk" => (isset($potential_vm["cf_646"]) ? $potential_vm["cf_646"] : (isset($potential_vm["telemarketing"]) ? $potential_vm["telemarketing"] : "")),
            "com" => (isset($potential_vm["cf_647"]) ? $potential_vm["cf_647"] : (isset($potential_vm["venditore"]) ? $potential_vm["venditore"] : "")),
            "time" => (isset($potential_vm["closingdate"]) ? $potential_vm["closingdate"] : (isset($potential_vm["data_chiusura_attesa"]) ? $potential_vm["data_chiusura_attesa"] : "")),
        );
        $logres = array("NO LOG DONE");
        try {
            $acccf = \AR\Vtaccountscf::find($accountid);
            $logres = self::putLog($acccf->partita_iva, $what, $parameters);
        } catch (\Exception $e) {
            return array($potential_vm, 404, $e->getMessage());
        }
        $out = array();
        if ($logres[1] !== 200)
        {
            $out["log"] = $logres;
        }
        $out[] = $vtws->lastError();
        return array($new_potential, 200, $out);
    }

    public function ws___addPotential() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $potential_vm = WolfMVC\RequestMethods::post("potential_vm", FALSE);
        if ($potential_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::addPotential($potential_vm);
        $this->_setResponseStatus($response[1]);
        if ($response[1] != 200)
        {
            echo json_encode(array($response[0], $response[2]));
        }
        else
        {
            echo json_encode($response[0]);
        }
    }

    public static function addQuote($quote_vm) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!is_array($quote_vm))
        {
            return array($quote_vm, 400, "Invalid vm");
        }
        if (!isset($quote_vm["accountid"]) && !isset($quote_vm["id_cliente"]))
        {
            return array($quote_vm, 400, "No account specified");
        }
        if (!isset($quote_vm["subject"]) && !isset($quote_vm["nome_quote"]))
        {
            return array($quote_vm, 400, "No quotename specified");
        }
        if (!isset($quote_vm["quotestage"]) && !isset($quote_vm["stato_quote"]))
        {
            return array($quote_vm, 400, "No closingdate specified");
        }
        if (!isset($quote_vm["bill_street"]) && !isset($quote_vm["ship_street"]) && !isset($quote_vm["indirizzo"]))
        {
            return array($quote_vm, 400, "No sales_stage specified");
        }
        if (!isset($quote_vm["listPrice1"]))
        {
            return array($quote_vm, 400, "No listPrice1 specified");
        }
        try {
            $account = AR\Vtaccountscf::find((isset($quote_vm["accountid"]) ? $quote_vm["accountid"] : $quote_vm["id_cliente"]));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array((isset($quote_vm["accountid"]) ? $quote_vm["accountid"] : $quote_vm["id_cliente"]), 404, "Invalid accountid");
        }
        $piva = $account->partita_iva;
        $vtws = \WolfMVC\Registry::get("VTWS");
        $tax = 22;
        $totalWithoutTaxes = (double) $quote_vm["listPrice1"];
        $totalWithTaxes = $totalWithoutTaxes * (($tax / 100) + 1);
        $vtws_quote_vm = array(
            "account_id" => '11x' . (isset($quote_vm["accountid"]) ? $quote_vm["accountid"] : (isset($quote_vm["id_cliente"]) ? $quote_vm["id_cliente"] : "")),
            "subject" => (isset($quote_vm["subject"]) ? $quote_vm["subject"] : (isset($quote_vm["nome_quote"]) ? $quote_vm["nome_quote"] : "")),
            "contact_id" => '12x' . (isset($quote_vm["contact_id"]) ? $quote_vm["contact_id"] : (isset($quote_vm["id_contatto"]) ? $quote_vm["id_contatto"] : "")),
            "ship_city" => (isset($quote_vm["ship_city"]) ? $quote_vm["ship_city"] : (isset($quote_vm["citta"]) ? $quote_vm["citta"] : "")),
            "ship_code" => (isset($quote_vm["ship_code"]) ? $quote_vm["ship_code"] : (isset($quote_vm["cap"]) ? $quote_vm["cap"] : "")),
            "ship_state" => (isset($quote_vm["ship_state"]) ? $quote_vm["ship_state"] : (isset($quote_vm["provincia"]) ? $quote_vm["provincia"] : "")),
            "ship_street" => (isset($quote_vm["ship_street"]) ? $quote_vm["ship_street"] : (isset($quote_vm["indirizzo"]) ? $quote_vm["indirizzo"] : "")),
            "bill_city" => (isset($quote_vm["bill_city"]) ? $quote_vm["bill_city"] : (isset($quote_vm["citta"]) ? $quote_vm["citta"] : "")),
            "bill_code" => (isset($quote_vm["bill_code"]) ? $quote_vm["bill_code"] : (isset($quote_vm["cap"]) ? $quote_vm["cap"] : "")),
            "bill_state" => (isset($quote_vm["bill_state"]) ? $quote_vm["bill_state"] : (isset($quote_vm["provincia"]) ? $quote_vm["provincia"] : "")),
            "bill_street" => (isset($quote_vm["bill_street"]) ? $quote_vm["bill_street"] : (isset($quote_vm["indirizzo"]) ? $quote_vm["indirizzo"] : "")),
            "potential_id" => '13x' . (isset($quote_vm["potential_id"]) ? $quote_vm["potential_id"] : (isset($quote_vm["id_opportunita"]) ? $quote_vm["id_opportunita"] : "")),
            "quotestage" => (isset($quote_vm["quotestage"]) ? $quote_vm["quotestage"] : (isset($quote_vm["stato_quote"]) ? $quote_vm["stato_quote"] : "")),
            "description" => (isset($quote_vm["description"]) ? $quote_vm["description"] : (isset($quote_vm["descrizione"]) ? $quote_vm["descrizione"] : "")),
            "validtill" => (isset($quote_vm["validtill"]) ? $quote_vm["validtill"] : (isset($quote_vm["valido_fino"]) ? $quote_vm["valido_fino"] : "")),
            "taxtype" => (isset($quote_vm["taxtype"]) ? $quote_vm["taxtype"] : (isset($quote_vm["formato_tassazione"]) ? $quote_vm["formato_tassazione"] : "")),
            "cf_664" => (isset($quote_vm["cf_664"]) ? $quote_vm["cf_664"] : (isset($quote_vm["telemarketing"]) ? $quote_vm["telemarketing"] : "")),
            "cf_665" => (isset($quote_vm["cf_665"]) ? $quote_vm["cf_665"] : (isset($quote_vm["venditore"]) ? $quote_vm["venditore"] : "")),
            "cf_667" => (isset($quote_vm["cf_667"]) ? $quote_vm["cf_667"] : (isset($quote_vm["consulente"]) ? $quote_vm["consulente"] : "")),
            "cf_666" => (isset($quote_vm["cf_666"]) ? $quote_vm["cf_666"] : (isset($quote_vm["analista"]) ? $quote_vm["analista"] : "")),
            "assigned_user_id" => "19x6",
            "assigned_user_id1" => "19x6",
            "currency_id" => "21x1",
            "cf_681" => "New Business PMI",
            "totalProductCount" => 1,
            "hdnProductId1" => "3444",
            "productDescription1" => "Giornata di analisi aziendale",
            "qty1" => 1,
            "listPrice1" => (isset($quote_vm["listPrice1"]) ? $quote_vm["listPrice1"] : (isset($quote_vm["listPrice1"]) ? $quote_vm["listPrice1"] : "")),
            "comment1" => "Giornata di analisi aziendale - Rimborso spese",
            "tax2_percentage1" => $tax,
            "total" => $totalWithTaxes,
            "subtotal" => $totalWithoutTaxes,
            "hdnGrandTotal" => $totalWithTaxes,
            "hdnSubTotal" => $totalWithoutTaxes
        );
        try {
            $new_quote = $vtws->doCreate("Quotes", $vtws_quote_vm);
        } catch (\Exception $e) {
            return array($quote_vm, 404, $e->getMessage());
        }
        $what = array("op", "Vtiger", "quotes", "Creation");
        $parameters = array(
            "tmk" => (isset($quote_vm["cf_664"]) ? $quote_vm["cf_664"] : (isset($quote_vm["telemarketing"]) ? $quote_vm["telemarketing"] : "")),
            "com" => (isset($quote_vm["cf_665"]) ? $quote_vm["cf_665"] : (isset($quote_vm["venditore"]) ? $quote_vm["venditore"] : "")),
            "pot" => (isset($quote_vm["potential_id"]) ? $quote_vm["potential_id"] : (isset($quote_vm["id_opportunita"]) ? $quote_vm["id_opportunita"] : ""))
        );
        $logres = self::putLog($piva, $what, $parameters);
        $out = array();
        if ($logres[1] !== 200)
        {
            $out["log"] = $logres;
        }
        $out[] = $vtws->lastError();
        return array($new_quote, 200, $out);
    }

    public static function addQuotePro($quote_vm) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!is_array($quote_vm))
        {
            return array($quote_vm, 400, "Invalid vm");
        }
        if (!isset($quote_vm["accountid"]) && !isset($quote_vm["id_cliente"]))
        {
            return array($quote_vm, 400, "No account specified");
        }
        if (!isset($quote_vm["subject"]) && !isset($quote_vm["nome_quote"]))
        {
            return array($quote_vm, 400, "No quotename specified");
        }
        if (!isset($quote_vm["quotestage"]) && !isset($quote_vm["stato_quote"]))
        {
            return array($quote_vm, 400, "No closingdate specified");
        }
        if (!isset($quote_vm["bill_street"]) && !isset($quote_vm["ship_street"]) && !isset($quote_vm["indirizzo"]))
        {
            return array($quote_vm, 400, "No sales_stage specified");
        }
        try {
            $account = AR\Vtaccountscf::find((isset($quote_vm["accountid"]) ? $quote_vm["accountid"] : $quote_vm["id_cliente"]));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array((isset($quote_vm["accountid"]) ? $quote_vm["accountid"] : $quote_vm["id_cliente"]), 404, "Invalid accountid");
        }
        $piva = $account->partita_iva;
        $vtws = \WolfMVC\Registry::get("VTWS");
        $tax = 22;
        $totalWithoutTaxes = (double) $quote_vm["listPrice1"];
        $totalWithTaxes = $totalWithoutTaxes * (($tax / 100) + 1);
        $valoreProgetto = (isset($quote_vm["valoreProgetto"]) ? $quote_vm["valoreProgetto"] : 0);
        $valoreProgetto = (double) $valoreProgetto;
        $speseProgetto = (isset($quote_vm["speseProgetto"]) ? $quote_vm["speseProgetto"] : 0);
        $speseProgetto = (double) $speseProgetto;
        $caparraProgetto = (isset($quote_vm["caparraProgetto"]) ? $quote_vm["caparraProgetto"] : 0);
        $caparraProgetto = (double) $caparraProgetto;
        $vtws_quote_vm = array(
            "account_id" => '11x' . (isset($quote_vm["accountid"]) ? $quote_vm["accountid"] : (isset($quote_vm["id_cliente"]) ? $quote_vm["id_cliente"] : "")),
            "subject" => (isset($quote_vm["subject"]) ? $quote_vm["subject"] : (isset($quote_vm["nome_quote"]) ? $quote_vm["nome_quote"] : "")),
            "contact_id" => '12x' . (isset($quote_vm["contact_id"]) ? $quote_vm["contact_id"] : (isset($quote_vm["id_contatto"]) ? $quote_vm["id_contatto"] : "")),
            "ship_city" => (isset($quote_vm["ship_city"]) ? $quote_vm["ship_city"] : (isset($quote_vm["citta"]) ? $quote_vm["citta"] : "")),
            "ship_code" => (isset($quote_vm["ship_code"]) ? $quote_vm["ship_code"] : (isset($quote_vm["cap"]) ? $quote_vm["cap"] : "")),
            "ship_state" => (isset($quote_vm["ship_state"]) ? $quote_vm["ship_state"] : (isset($quote_vm["provincia"]) ? $quote_vm["provincia"] : "")),
            "ship_street" => (isset($quote_vm["ship_street"]) ? $quote_vm["ship_street"] : (isset($quote_vm["indirizzo"]) ? $quote_vm["indirizzo"] : "")),
            "bill_city" => (isset($quote_vm["bill_city"]) ? $quote_vm["bill_city"] : (isset($quote_vm["citta"]) ? $quote_vm["citta"] : "")),
            "bill_code" => (isset($quote_vm["bill_code"]) ? $quote_vm["bill_code"] : (isset($quote_vm["cap"]) ? $quote_vm["cap"] : "")),
            "bill_state" => (isset($quote_vm["bill_state"]) ? $quote_vm["bill_state"] : (isset($quote_vm["provincia"]) ? $quote_vm["provincia"] : "")),
            "bill_street" => (isset($quote_vm["bill_street"]) ? $quote_vm["bill_street"] : (isset($quote_vm["indirizzo"]) ? $quote_vm["indirizzo"] : "")),
            "potential_id" => '13x' . (isset($quote_vm["potential_id"]) ? $quote_vm["potential_id"] : (isset($quote_vm["id_opportunita"]) ? $quote_vm["id_opportunita"] : "")),
            "quotestage" => (isset($quote_vm["quotestage"]) ? $quote_vm["quotestage"] : (isset($quote_vm["stato_quote"]) ? $quote_vm["stato_quote"] : "")),
            "description" => (isset($quote_vm["description"]) ? $quote_vm["description"] : (isset($quote_vm["descrizione"]) ? $quote_vm["descrizione"] : "")),
            "validtill" => (isset($quote_vm["validtill"]) ? $quote_vm["validtill"] : (isset($quote_vm["valido_fino"]) ? $quote_vm["valido_fino"] : "")),
            "taxtype" => (isset($quote_vm["taxtype"]) ? $quote_vm["taxtype"] : (isset($quote_vm["formato_tassazione"]) ? $quote_vm["formato_tassazione"] : "")),
            "cf_664" => (isset($quote_vm["cf_664"]) ? $quote_vm["cf_664"] : (isset($quote_vm["telemarketing"]) ? $quote_vm["telemarketing"] : "")),
            "cf_665" => (isset($quote_vm["cf_665"]) ? $quote_vm["cf_665"] : (isset($quote_vm["venditore"]) ? $quote_vm["venditore"] : "")),
            "cf_667" => (isset($quote_vm["cf_667"]) ? $quote_vm["cf_667"] : (isset($quote_vm["consulente"]) ? $quote_vm["consulente"] : "")),
            "cf_666" => (isset($quote_vm["cf_666"]) ? $quote_vm["cf_666"] : (isset($quote_vm["analista"]) ? $quote_vm["analista"] : "")),
            "assigned_user_id" => "19x6",
            "assigned_user_id1" => "19x6",
            "currency_id" => "21x1",
            "cf_681" => "New Business PMI",
            "totalProductCount" => 3,
            "hdnProductId1" => "3437",
            "productDescription1" => "Progetto di Consulenza Aziendale",
            "qty1" => 1,
            "listPrice1" => $caparraProgetto,
            "comment1" => "Progetto di Consulenza Aziendale: Anticipo",
            "tax2_percentage1" => $tax,
            "hdnProductId2" => "3442",
            "productDescription2" => "SAL di Progetto",
            "qty2" => 1,
            "listPrice2" => max(array($valoreProgetto - $caparraProgetto, 0)),
            "comment2" => "Progetto di Consulenza Aziendale: SAL di Progetto",
            "tax2_percentage2" => $tax,
            "hdnProductId2" => "3438",
            "productDescription2" => "Rimborso Spese Progetto SAL",
            "qty2" => 1,
            "listPrice2" => $speseProgetto,
            "comment2" => "Rimborso Spese Progetto SAL",
            "tax2_percentage2" => $tax,
            "total" => $totalWithTaxes,
            "subtotal" => $totalWithoutTaxes,
            "hdnGrandTotal" => $totalWithTaxes,
            "hdnSubTotal" => $totalWithoutTaxes,
        );
        try {
            $new_quote = $vtws->doCreate("Quotes", $vtws_quote_vm);
        } catch (\Exception $e) {
            return array($quote_vm, 404, $e->getMessage());
        }
        $what = array("op", "Vtiger", "quotes", "Creation");
        $parameters = array(
            "tmk" => (isset($quote_vm["cf_664"]) ? $quote_vm["cf_664"] : (isset($quote_vm["telemarketing"]) ? $quote_vm["telemarketing"] : "")),
            "com" => (isset($quote_vm["cf_665"]) ? $quote_vm["cf_665"] : (isset($quote_vm["venditore"]) ? $quote_vm["venditore"] : "")),
            "ana" => (isset($quote_vm["cf_655"]) ? $quote_vm["cf_655"] : (isset($quote_vm["analista"]) ? $quote_vm["analista"] : "")),
            "pot" => (isset($quote_vm["potential_id"]) ? $quote_vm["potential_id"] : (isset($quote_vm["id_opportunita"]) ? $quote_vm["id_opportunita"] : ""))
        );
        $logres = self::putLog($piva, $what, $parameters);
        $out = array();
        if ($logres[1] !== 200)
        {
            $out["log"] = $logres;
        }
        $out[] = $vtws->lastError();
        return array($new_quote, 200, $out);
    }

    public function ws___addQuote() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $quote_vm = WolfMVC\RequestMethods::post("quote_vm", FALSE);
        if ($quote_vm === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing vm");
            return;
        }
        $response = self::addQuote($quote_vm);
        $this->_setResponseStatus($response[1]);
        echo json_encode(array($response[0], $response[2]));
    }

    public function ws___prova() {
        $this->setJsonWS();
//        print_r($_REQUEST);
        $vtws = WolfMVC\Registry::get("VTWS");
        echo json_encode($vtws->doDescribe("Accounts"));
//        echo json_encode($vtws->doQuery("SELECT * FROM Events ORDER BY id DESC LIMIT 10"));
    }

    public static function getVtUserById($vtuserid) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $user = AR\Vtuser::find($vtuserid, array("include" => array("role" => array("roledetails"), "groups" => array("groupdetails"))));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array("No such user", 404, false);
        }
        $out = array();
        $out["vt_id_utente"] = $vtuserid;
        $out["vt_nome_utente"] = $user->nome_utente;
        $out["vt_id_ruolo"] = $user->role->id_ruolo;
        $out["vt_nome_ruolo"] = $user->role->roledetails->nome_ruolo;
        $out["vt_gruppi"] = array_map(function($x) {
            return $x->groupdetails->get_values_for(array("id_gruppo", "nome_gruppo"));
        }, $user->groups);
        return array($out, 200, true);
    }

    public static function getVtUserIdsByRole($roleid) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!is_array($roleid))
        {
            $roleid = array($roleid);
        }
        $out = array();

        foreach ($roleid as $rid) {
            try {
                $role = \AR\Vtrole::all(array("conditions" => array(
                                "roleid = ?", $rid
                            )
                ));
            } catch (\ActiveRecord\RecordNotFound $e) {
                return array("No such role " . $rid, 404, false);
            }
            $out = array_merge($out, array_map(function($x) {
                        return (int) $x->id_utente;
                    }, $role));
        }

        return array($out, 200, true);
    }

    public static function replicaTmkVendOnPot($potId) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $pot = AR\Vtpotential::find($potId, array("include" => "cf"));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(false, 404, $e->getMessage());
        } catch (\Exception $e) {
            return array(false, 404, $e->getMessage());
        }
        $id_cliente = $pot->collegato_a;
        try {
            $acc = AR\Vtaccount::find($id_cliente, array("include" => "cf"));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(false, 404, $e->getMessage());
        } catch (\Exception $e) {
            return array(false, 404, $e->getMessage());
        }
        try {
            $pot->cf->telemarketing = $acc->cf->telemarketing;
            $pot->cf->venditore = $acc->cf->venditore;
            $pot->cf->save();
            return array(array($pot->cf->telemarketing, $pot->cf->venditore), 200, true);
        } catch (\ActiveRecord\ActiveRecordException $e) {
            return array(false, 404, $e->getMessage());
        } catch (\Exception $e) {
            return array(false, 404, $e->getMessage());
        }
    }

    public function ws___replicaTmkVendOnPot() {
        $this->setJsonWS();
        $potId = WolfMVC\RequestMethods::get("potId");
        $res = self::replicaTmkVendOnPot($potId);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[1]));
        return;
    }

}
