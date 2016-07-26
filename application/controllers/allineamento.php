<?php

use WolfMVC\Controller as Controller;

class Allineamento extends Controller {

    public function __construct($options = array()) {

        parent::__construct($options);
    }

    public function script_including() {
        
    }

    public function ws___getChunk() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("chunkLength", "get", true),
                    array("chunk", "get", true),
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(400);
            echo json_encode($pars);
            return;
        }
        $chunk = (int) $pars["chunk"];
        $chunkLength = (int) $pars["chunkLength"];

        if ($chunk === 0 && $pars["chunk"] !== "0")
        {
            $this->_setResponseStatus(400);
            echo json_encode("Invalid chunk number!");
            return;
        }
        if ($chunkLength === 0)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Invalid chunk length!");
            return;
        }

        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );

        $atecoesclusi = array_map(function($x) {
            $att = $x->attributes();
            return $att["sottocategoria"];
        }, \AR\Ateco::find("all", array(
                    "conditions" => array("punteggioAteco = ?", 0),
                    "select" => "SOTTOCATEGORIA"
        )));
        $fatturatoesclusi = array_map(function($x) {
            $att = $x->attributes();
            return $att["fasciafatturato"];
        }, \AR\Fatturato::find("all", array(
                    "conditions" => array("FasciaFatturato_voto = ?", 0),
                    "select" => "FasciaFatturato"
        )));
        $out = array();
        $mktData = \AR\Lead::find("all", array(
                    "include" => array("pipe", "lock"),
                    "limit" => $chunkLength,
                    "offset" => $chunk * $chunkLength,
                    "order" => "internal_id ASC",
//                    "joins" => array("LEFT JOIN ts_tmk_pipes a ON (partitaiva = a.piva)")
//                    "select" => 'internal_id, partitaiva,telefono, ragionesociale,esclusione'
                        )
        );
        $mktDataClean = array();
        $pivas = array();
        foreach ($mktData as $k => $v) {
            $piva = $v->get_values_for(array("partitaiva"));
            $pivas[] = $piva["partitaiva"];
        }

        $vtData = \AR\Vtaccount::find("all", array(
                    "joins" => array("JOIN vtiger_crmentity a ON (vtiger_account.accountid = a.crmid)", "JOIN vtiger_accountscf b USING(accountid)"),
                    "include" => array("cf", "ent", "potentials" => array("cf", "ent")),
                    "conditions" => array("cf_641 IN (?) AND deleted = ?", $pivas, 0)
                        )
        );
        foreach ($mktData as $k => $v) {
            $mktDataClean[$k] = array();

            // correzioni
            $change = false;

            //esclusione per presenza in tubo
            if (count($v->pipe))
            {
                $corr = self::checkBit($v->partitaiva, 1, 1);
                $mktDataClean[$k]["calculated"]["pipeesc"] = true;
            }
            else
            {
                $corr = self::checkBit($v->partitaiva, 1, 0);
                $mktDataClean[$k]["calculated"]["pipeesc"] = false;
            }



            $mktDataClean[$k]["checks"]["pipeesc"] = TRUE;
            $mktDataClean[$k]["corrected"]["pipeesc"] = $corr[2];
            $change = ($change || $corr[2]);

            //controllo ateco  

            $atecoesc = array_search($v->codiceateco, $atecoesclusi);
            if ($atecoesc !== FALSE)
            {
                $corr = self::checkBit($v->partitaiva, 7, 1);
                $mktDataClean[$k]["calculated"]["atecoesc"] = TRUE;
            }
            else
            {
                $corr = self::checkBit($v->partitaiva, 7, 0);
                $mktDataClean[$k]["calculated"]["atecoesc"] = FALSE;
            }
            $mktDataClean[$k]["checks"]["atecoesc"] = TRUE;
            $mktDataClean[$k]["corrected"]["atecoesc"] = $corr[2];
            $change = ($change || $corr[2]);

            //controllo blocco scaduto
            if (count($v->lock))
            {
                $tempo_sblocco = $v->lock[0]->tempo_sblocco;
                if ($tempo_sblocco)
                {
                    $now = new DateTime(NULL);
                    $diff = $now->diff($tempo_sblocco);
                    if ($now > $tempo_sblocco && $diff->days > 5)
                    {
                        $res = self::deleteLeadLock($v->partitaiva);
                    }
                    else
                    {
                        $res = array("true");
                    }
                    $mktDataClean[$k]["checks"]["bloccoscaduto"] = $res[0];
                    $mktDataClean[$k]["calculated"]["bloccoscaduto"] = $diff->days;
                }
            }


            //controllo fatturato
            $fatturatoesc = array_search($v->fasciafatturato, $fatturatoesclusi);
            if ($fatturatoesc !== FALSE)
            {
                $corr = self::checkBit($v->partitaiva, 4, 1);
                $mktDataClean[$k]["calculated"]["fatturatoesc"] = TRUE;
            }
            else
            {
                $corr = self::checkBit($v->partitaiva, 4, 0);
                $mktDataClean[$k]["calculated"]["fatturatoesc"] = FALSE;
            }
            $mktDataClean[$k]["checks"]["fatturatoesc"] = TRUE;
            $mktDataClean[$k]["corrected"]["fatturatoesc"] = $corr[2];
            $change = ($change || $corr[2]);



            foreach ($vtData as $s => $t) {
                $piva = $t->cf->cf_641;
                if ($piva === $v->partitaiva)
                {
                    //correzioni
                    //esclusione per presenza progetto
                    $cliente = self::isCliente($piva, $t->potentials);
                    if ($cliente[0])
                    {
                        $corr = self::checkBit($v->partitaiva, 1, 1);
                        $mktDataClean[$k]["calculated"]["cliente"] = true;
                    }
                    else
                    {
                        $corr = self::checkBit($v->partitaiva, 1, 0);
                        $mktDataClean[$k]["calculated"]["cliente"] = false;
                    }
                    $mktDataClean[$k]["checks"]["cliente"] = TRUE;
                    $mktDataClean[$k]["corrected"]["cliente"] = $corr[2];

                    $change = ($change || $corr[2]);



                    $mktDataClean[$k]["vtAcc"] = $t->get_values_for(array(
                        'id_cliente', 'ragione_sociale', 'telefono'
                    ));
                    $mktDataClean[$k]["vtAcc"] = array_merge($mktDataClean[$k]["vtAcc"], $t->cf->get_values_for(array(
                                "venditore", "telemarketing"
                    )));
                    if (count($t->potentials))
                    {
                        $mktDataClean[$k]["vtPotStats"] = array(
                            "analisi" => array(
                                "numAnalisi" => 0,
                                "dataUltimaAnalisi" => new DateTime("1970-01-01 00:00:00"),
                                "esitoUltimaAnalisi" => "",
                                "correzioni" => 0
                            ),
                            "progetti" => array(
                                "numProgetti" => 0,
                                "dataUltimoProgetto" => new DateTime("1970-01-01 00:00:00"),
                                "esitoUltimoProgetto" => "",
                                "correzioni" => 0
                            )
                        );
                    }
                    $mktDataClean[$k]["vtPot"] = array();
                    $mktDataClean[$k]["vtPot"]["analisi"] = array_map(function($x) use (&$mktDataClean, $k) {
                        try {
                            if ($x->nome_opportunita === "Analisi")
                            {
                                if ($x->ent->deleted != 0)
                                    return NULL;
                                $out = $x->get_values_for(array(
                                    "id_opportunita", "nome_opportunita", "data_chiusura_attesa", "stadio_vendita", "prossimo_step"
                                ));
                                $out = array_merge($out, $x->cf->get_values_for(array(
                                            "telemarketing", "venditore", "esito_visita", "esito_conferma"
                                )));

                                $res = Vtiger::ComAppStatus($x->id_opportunita);
                                $status = $res[0];
                                if ($status === "stato indefinito")
                                {
                                    $res2 = Vtiger::InferComAppStatus($x->id_opportunita);
                                    if ($res2[0] !== "stato indefinito")
                                    {
                                        $mktDataClean[$k]["vtPotStats"]["analisi"]["correzioni"] ++;
                                        $res3 = Vtiger::ComAppStatus($x->id_opportunita, $res2[0]);
                                        $status = $res3[0];
                                    }
                                    else
                                    {
                                        $status = "stato indefinito";
                                    }
                                }

                                $mktDataClean[$k]["vtPotStats"]["analisi"]["numAnalisi"] ++;
                                if (!isset($x->data_chiusura_attesa->date) || !($x->data_chiusura_attesa->date))
                                {
                                    $dca = $out["data_chiusura_attesa"];
                                }
                                else
                                {
                                    $dca = new DateTime($x->data_chiusura_attesa->date);
                                }
                                if ($dca > $mktDataClean[$k]["vtPotStats"]["analisi"]["dataUltimaAnalisi"])
                                { //analisi più recente delle precedenti scansite
                                    $mktDataClean[$k]["vtPotStats"]["analisi"]["dataUltimaAnalisi"] = $dca;
                                    $mktDataClean[$k]["vtPotStats"]["analisi"]["esitoUltimaAnalisi"] = $status;
                                }
                                else
                                { //analisi più antica di un'altra precedentemente scansita ---> deve essere in uno stato terminale, altrimenti l'annullo
                                    if (!in_array($status, array("_COMAPP_NOCHECK", "_COMAPP_CHECK", "_COMAPP_SYSTEMCANCELLED", "_COMAPP_AUTOCLOSE")))
                                    {
                                        $res3 = Vtiger::ComAppStatus($x->id_opportunita, "_COMAPP_SYSTEMCANCELLED");
                                        $mktDataClean[$k]["vtPotStats"]["analisi"]["correzioni"] ++;
                                        $status = $res3[0];
                                    }
                                }
                                $out["ComAppStatus"] = $status;
                                return $out;
                            }
                            else
                            {
                                return NULL;
                            }
                        } catch (\Exception $e) {

                            return array($e->getLine(), $e->getMessage());
                        }
                    }, $t->potentials);

                    if ($mktDataClean[$k]["vtPotStats"]["analisi"]["dataUltimaAnalisi"] instanceof DateTime)
                    {
                        $now = new DateTime(NULL);
                        $elapsed = $now->diff($mktDataClean[$k]["vtPotStats"]["analisi"]["dataUltimaAnalisi"], true);
                        if ($elapsed->days > 61)
                        {

                            $corr = self::checkBit($v->partitaiva, 2, 0);
                            $mktDataClean[$k]["calculated"]["analisi_recente"] = false;
                        }
                        else
                        {
                            $corr = self::checkBit($v->partitaiva, 2, 1);
                            $mktDataClean[$k]["calculated"]["analisi_recente"] = true;
                        }
                        $mktDataClean[$k]["checks"]["analisi_recente"] = TRUE;
                        $mktDataClean[$k]["corrected"]["analisi_recente"] = $corr[2];
                        if (in_array($mktDataClean[$k]["vtPotStats"]["analisi"]["esitoUltimaAnalisi"], array("_COMAPP_QUALIFICATION", "_COMAPP_UNQUALIFIED", "_COMAPP_CALLAGAIN")))
                        {
                            // la palla è al telemarketing
                            if (count($v->pipe))
                            {
                                if (((int) $v->pipe->tubo) > 9)
                                {
                                    $v->pipe[0]->tubo = 5;
                                    $v->pipe[0]->save();
                                    $change = true;
                                }
                            }
                        }
                    }
                    if (count($v->pipe))
                    {
                        //se c'è analisi con palla al tmk
                        if ($mktDataClean[$k]["vtPotStats"]["analisi"]["numAnalisi"] === 0)
                        {
                            if (!in_array(((int) $v->pipe[0]->tubo), array(1, 2, 3, 4, 6, 7)))
                            {
                                $v->pipe[0]->tubo = 1;
                                $v->pipe[0]->save();
                                $change = true;
                            }
                        }
                        else
                        { // c'è almeno una analisi
                            //l'ultima analisi è in stato "telemarketing"
                            if (in_array($mktDataClean[$k]["vtPotStats"]["analisi"]["esitoUltimaAnalisi"], array("_COMAPP_QUALIFICATION", "_COMAPP_UNQUALIFIED", "_COMAPP_CALLAGAIN")))
                            {
                                if (((int) $v->pipe[0]->tubo) > 9 || ((int) $v->pipe[0]->tubo) > 9)
                                {
                                    $v->pipe[0]->tubo = 5;
                                    $v->pipe[0]->save();
                                    $change = true;
                                }
                            }
                        }


                        if (!in_array(((int) $v->pipe[0]->tubo), array(1, 2, 3, 4, 6, 7)))
                        {
                            $v->pipe[0]->tubo = 1;
                            $v->pipe[0]->save();
                            $change = true;
                        }
                    }

                    $mktDataClean[$k]["vtPot"]["progetti"] = array_map(function($x) use (&$mktDataClean, $k) {
//                        return $x->attributes(true);
                        try {
                            if ($x->nome_opportunita === "PROGETTO") // questo codice è da modificare una volta mappato le sezioni di processo di analisi e consulenza
                            {
                                if ($x->ent->deleted != 0)
                                    return NULL;
                                $out = $x->get_values_for(array(
                                    "id_opportunita", "nome_opportunita", "data_chiusura_attesa", "stadio_vendita", "prossimo_step"
                                ));
                                $out = array_merge($out, $x->cf->get_values_for(array(
                                            "telemarketing", "venditore", "esito_visita", "esito_conferma","analista"
                                )));

                                $res = Vtiger::AnaAppStatus($x->id_opportunita);
                                $status = $res[0];
                                if ($status === "stato indefinito")
                                {
                                    $res2 = Vtiger::InferAnaAppStatus($x->id_opportunita);
                                    if ($res2[0] !== "stato indefinito")
                                    {
                                        $mktDataClean[$k]["vtPotStats"]["progetti"]["correzioni"] ++;
                                        $res3 = Vtiger::AnaAppStatus($x->id_opportunita, $res2[0]);
                                        $status = $res3[0];
                                    }
                                    else
                                    {
                                        $status = "stato indefinito";
                                    }
                                }
                                $out["AnaAppStatus"] = $status;
                                $mktDataClean[$k]["vtPotStats"]["progetti"]["numProgetti"] ++;
                                $dca = new DateTime($x->closingdate->format('Y-m-d'));
                                if ($dca > $mktDataClean[$k]["vtPotStats"]["progetti"]["dataUltimoProgetto"])
                                { //analisi più recente delle precedenti scansite
                                    $mktDataClean[$k]["vtPotStats"]["progetti"]["dataUltimoProgetto"] = $dca;
                                    $mktDataClean[$k]["vtPotStats"]["progetti"]["esitoUltimoProgetto"] = $status;
                                }
//                                else
//                                { //analisi più antica di un'altra precedentemente scansita ---> deve essere in uno stato terminale, altrimenti l'annullo
//                                    if (!in_array($status, array("_COMAPP_NOCHECK", "_COMAPP_CHECK", "_COMAPP_SYSTEMCANCELLED", "_COMAPP_AUTOCLOSE")))
//                                    {
//                                        $res3 = Vtiger::ComAppStatus($x->id_opportunita, "_COMAPP_SYSTEMCANCELLED");
//                                        $mktDataClean[$k]["vtPotStats"]["analisi"]["correzioni"] ++;
//                                        $status = $res3[0];
//                                    }
//                                }
//                                $out["ComAppStatus"] = $status;
                                return $out;
                            }
                            else
                            {
                                return NULL;
                            }
                        } catch (\Exception $e) {

                            return "AN ERROR HAS OCCURRED";
                        }
                    }, $t->potentials);
                    break;
                }
            }
            $mktDataClean[$k]["mkt"] = $v->get_values_for(
                    array(
                        "internal_id", "partitaiva", "ragionesociale", "esclusione", "attivita_ateco", "fasciafatturato"
                    )
            );
            $mktDataClean[$k]["pipe"] = count($v->pipe) ? $v->pipe[0]->get_values_for(
                            array(
                                "lotto", "tubo", "venditore", "blocco_tmk", "disponibile_da", "tempo_inserimento"
                            )
                    ) : NULL;
            $mktDataClean[$k]["lock"] = count($v->lock) ? $v->lock[0]->get_values_for(
                            array(
                                "tempo_sblocco", "blocco_manuale", "forza", "tempo_blocco"
                            )
                    ) : NULL;
//            $mktDataVt[] = $v->attributes();
//            $piva = $v->get_values_for(array("partitaiva"));
//            $pivas[] = $piva["partitaiva"];
        }
        echo json_encode($mktDataClean);
    }

    public static function isCliente($piva, $vtPot = array()) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!isset($piva) || empty($piva))
        {
            return array("Missing piva", 400, null);
        }
        if (empty($vtPot))
        {

            //cerco per partita iva
            $vtData = \AR\Vtaccount::find("first", array(
                        "joins" => array("JOIN vtiger_crmentity a ON (vtiger_account.accountid = a.crmid)", "JOIN vtiger_accountscf b USING(accountid)"),
                        "include" => array("cf", "ent", "potentials" => array("cf", "ent")),
                        "conditions" => array("cf_641 IN (?) AND deleted = ?", $piva, 0)
                            )
            );
            if (!isset($vtData) || empty($vtData))
            {
                return array(false, 200, null);
            }
            if (is_array($vtData))
                $vtData = $vtData[0];
            if (!count($vtData->potentials))
            {
                return array(false, 200, null);
            }
            $vtPot = $vtData->potentials;
        }
        $cliente = false;
        foreach ($vtPot as $k => $v) {
            if ($v->nome_opportunita === "PROGETTO" && $v->ent->deleted == 0 && $v->stadio_vendita = 'Closed Won')
            {
                $cliente = true;
                break;
            }
        }
        return array($cliente, 200, null);
    }

    public function ws___isCliente() {
        $this->setJsonWS();
        $pars = $this->retrievePars(array(
            array("piva", "get", true)
        ));
        $res = $this->isCliente($pars["piva"]);
//        $this->_setResponseStatus($res[1]);
        echo json_encode($res);
        return;
    }

    public static function deleteLeadLock($piva) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $lock = \AR\Pivalock::find($piva);
        } catch (\Exception $e) {
            if ($e instanceof ActiveRecord\RecordNotFound)
            {
                return array(false, 404);
            }
            else
            {
                return array(false, 500);
            }
        }
        if ($lock->delete())
        {
            return array(true, 200);
        }
        else
        {
            return array(false, 500);
        }
    }

    public function ws___deleteLeadLock() {
        $this->setJsonWS();
        $pars = $this->retrievePars(array(
            array("piva", "get", true)
        ));
        $res = self::deleteLeadLock($pars["piva"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode($res[0]);
        return;
    }

    public static function checkBit($piva, $bit, $value) {
        if (!isset($piva) || empty($piva) || !isset($bit) || empty($bit) || !isset($value) || empty($value))
            ActiveRecord\Config::initialize(
                    \WolfMVC\Registry::get("activerecord_initializer")
            );
        try {
            $lead = \AR\Lead::find("first", array(
                        "conditions" => array("partitaiva = ?", $piva)
            ));
        } catch (\Exception $e) {
            if ($e instanceof ActiveRecord\RecordNotFound)
            {
                return array(false, 404, null);
            }
            else
            {
                return array(false, 500, null);
            }
        }
        $esc = (int) $lead->esclusione;
        $val = pow(2, (int) $bit);
        $check = ($esc & $val) / $val;
        $change = false;
        if ($value === 0 && $check === 1)
        {
            $lead->esclusione += $val;
            $change = true;
        }
        else if ($value === 1 && $check === 0)
        {
            $lead->esclusione -= $val;
            $change = true;
        }
        if ($lead->save())
        {
            return array($lead->esclusione, 200, $change);
        }
        else
        {
            return array(false, 500, null);
        }
    }

    public function ws___checkBit() {
        $this->setJsonWS();
        $pars = $this->retrievePars(array(
            array("piva", "get", true),
            array("bit", "get", true),
            array("value", "get", true)
        ));
        $pars["bit"] = (int) $pars["bit"];
        $pars["value"] = (int) $pars["value"];
        $res = self::checkBit($pars["piva"], $pars["bit"], $pars["value"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode($res[0]);
        return;
    }

}
