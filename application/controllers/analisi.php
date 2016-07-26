<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Analisi extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    /**
     * @protected
     */
    public function script_including() {

        $view = new \WolfMVC\View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per il layout
            "file" => APP_PATH . "/{$this->getDefaultPath()}/layouts/daf.{$this->getDefaultExtension()}"
        ));
        $this->setLayoutView($view);

        $view->set("moduleName", "ANALISI");
    }

    public function index() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("VENDITE" => "last")));
        $view = $this->getActionView();
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $this->loadScript("lodash.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $view->set("action", array(
            "Gestione analisi" => $this->pathTo($this->nameofthiscontroller(), "gestioneanalisi"),
            "Archivio analisi" => $this->pathTo($this->nameofthiscontroller(), "archivioanalisi")
        ));
    }

    public function gestioneanalisi() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("ANALISI" => "mkt", "Analisi in corso" => "last")));
        $view = $this->getActionView();
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-messages.min.js");
        $this->loadScript("core/datamodule/data-0.6.js");
        $this->loadScript("lodash.js");
        $this->loadScript("component/logger/logger-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("component/menuVisite/menuVisite-0.1.js");
        $this->loadScript("component/superForm/superForm-0.1.js");
        $this->loadScript("core/common/alertSeed-0.1.css");
        $this->loadScript("component/tsnwinclude/tsnwinclude_stdlib.js");
        $view->set("parent", '$parent');
        $view->set("error", '$error');
        $view->set("invalid", '$invalid');
        $view->set("dirty", '$dirty');
        $view->set("index", '$index');
    }

    public function ws___gestioneanalisi() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "getAnalisti" => SITE_PATH . "mkt/getAnalisti.ws",
                "getAppuntamentiAnalisi" => SITE_PATH . "analisi/getAppuntamentiAnalisi.ws",
                "getDetails" => SITE_PATH . "analisi/getDetails.ws",
                "getVendLogDesc" => SITE_PATH . "vend/getVendLogDesc.ws",
                "inferAnaAppStatus" => SITE_PATH . "vtiger/InferAnaAppStatus.ws",
                "corrNoActivityError" => SITE_PATH . "analisi/corrNoActivityError.ws",
                "replicaTmkVendOnPot" => SITE_PATH . "vtiger/replicaTmkVendOnPot.ws",
                "comAppStatus" => SITE_PATH . "vtiger/ComAppStatus.ws",
                "getLog" => SITE_PATH . "vend/getLog.ws",
                "newPutLog" => SITE_PATH . "vend/newPutLog.ws",
                "editAccount" => SITE_PATH . "vtiger/editAccount.ws",
                "editContact" => SITE_PATH . "vtiger/editContact.ws",
                "editPotential" => SITE_PATH . "vtiger/editPotential.ws",
                "editActivity" => SITE_PATH . "vtiger/editActivity.ws",
                "addContact" => SITE_PATH . "vtiger/addContact.ws",
                "addActivity" => SITE_PATH . "vtiger/addActivity.ws",
                "addPotential" => SITE_PATH . "vtiger/addPotential.ws",
                "addQuotePro" => SITE_PATH . "vtiger/addQuotePro.ws",
                "AnaAppStatus" => SITE_PATH . "vtiger/AnaAppStatus.ws",
                "setEsclusioneBit" => SITE_PATH . "mkt/setEsclusioneBit.ws",
                "setLeadPipeStatus" => SITE_PATH . "mkt/setLeadPipeStatus.ws",
                "closeAllActivities" => SITE_PATH . "vtiger/closeAllActivities.ws",
                "authorizeGestioneAnalisi" => SITE_PATH . "analisi/authorizeGestioneAnalisi.ws",
                "sendMailGestioneAnalisi" => SITE_PATH . "analisi/sendMailGestioneAnalisi.ws"
            )
        );
        echo json_encode($config);
        return;
    }

    public function ws___getDetails() {
        $this->setJsonWS();
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $piva = \WolfMVC\RequestMethods::get("piva", false);
        if (!isset($piva) || !$piva)
        {
            $this->_setResponseStatus(400);
            echo json_encode("");
            return;
        }
        $potid = \WolfMVC\RequestMethods::get("potid", false);
        if (!isset($potid) || !$potid)
        {
            $this->_setResponseStatus(400);
            echo json_encode("");
            return;
        }
        $actid = \WolfMVC\RequestMethods::get("actid", false);
        if (!isset($actid) || !$actid)
        {
            $this->_setResponseStatus(400);
            echo json_encode("");
            return;
        }
//recupero piva in vtiger
        $options = array("conditions" => array("cf_641 = ?", $piva), "include" => array("acc" => array("billads", "contacts" => array("ent"), "potentials" => array("cf", "ent", "activitycf" => array("act" => array("cntactivityrel"), "ent"))), "ent"));
        $acccf = \AR\Vtaccountscf::find("all", $options);
        $accountid = "";
        $account = "";
        foreach ($acccf as $k => $a) {
            if ($a && $a->accountid && $a->ent->deleted == '0')
            {
                $accountid = $a->accountid;
                $account = $a->acc;
                break;
            }
        }
        if ($accountid === "")
        {
            $this->_setResponseStatus(500);
            echo json_encode("NOT FOUND");
            return;
        }
        $out = array();
        $out["account"] = $account->get_values_for(array(
            "id_cliente", "numero_cliente", "ragione_sociale", "telefono", "email", "sito"
        ));
        $out["account"] = array_merge($out["account"], $account->cf->get_values_for(array(
                    "partita_iva", "codice_fiscale", "venditore", "telemarketing", "fascia_fatturato", "classe_dipendenti", "forma_giuridica",
                    "fascia_fatturato_com", "classe_dipendenti_com", "forma_giuridica_com",
                    "fascia_fatturato_ana", "classe_dipendenti_ana", "forma_giuridica_ana"
        )));
        $out["account"] = array_merge($out["account"], $account->billads->get_values_for(array(
                    "citta", "cap", "via", "regione", "provincia"
        )));
        $out["contatti"] = array();
        foreach ($account->contacts as $cI => $cc) {
            if ($cc->ent->deleted == '0')
                $out["contatti"][] = $cc->get_values_for(array(
                    "id_contatto", "numero_contatto", "nome", "cognome", "email", "telefono", "cellulare", "ruolo", "fax"
                ));
        }
        $out["opportunita"] = array();
        $potFound = false;
        foreach ($account->potentials as $pI => $pp) {
            if ($pp->id_opportunita != $potid)
                continue;
            else
            {
                $potFound = true;
            }
            if ($pp->ent->deleted == '0' && strpos($pp->nome_opportunita, 'PROGETTO') !== FALSE)
                $opp = $pp->get_values_for(array(
                    "id_opportunita", "numero_opportunita", "nome_opportunita", "data_chiusura_attesa", "prossimo_step", "probabilita",
                    "stadio_vendita"
                ));
            $opp = array_merge($opp, $pp->cf->get_values_for(array(
                        "telemarketing", "venditore", "esito_visita", "analista", "esito_conferma", "grace_scadenza"
            )));
            $opp["status"] = Vtiger::ComAppStatus($opp["id_opportunita"]);
            $opp["descrizione"] = $pp->ent->description;
            if (is_array($opp["status"]))
                $opp["status"] = $opp["status"][0];
            $out["opportunita"] = $opp;
        }
        try {
            $attivita = \AR\Vtactivity::find($actid, array("include" => array("cf", "ent")));
        } catch (\ActiveRecord\RecordNotFound $e) {
            $this->_setResponseStatus(404);
            echo json_encode("ACT NOT FOUND");
            return;
        }
        $relcontactid = $attivita->cntactivityrel;

        if (count($relcontactid))
        {
            $relcontactid = array("id_contatto" => $relcontactid[0]->contactid);
        }
        else
        {
            $relcontactid = array("id_contatto" => "");
        }
        $out["attivita"] = array_merge(
                $attivita->attributes(true), $attivita->cf->attributes(true), $attivita->ent->get_values_for(array("descrizione")), $relcontactid
        );
        if (!$potFound)
        {
            $this->_setResponseStatus(404);
            echo json_encode("POT NOT FOUND");
            return;
        }
        echo json_encode($out);
        return;
    }

    public function ws___getAppuntamentiAnalisi() {
        $this->setJsonWS();
        $pars = array(
            "analysts" => \WolfMVC\RequestMethods::get("analysts", FALSE),
            "fromdate" => \WolfMVC\RequestMethods::get("fromdate", FALSE)
        );

        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (isset($pars["analysts"]) && strlen($pars["analysts"]))
        {
            $analists = json_decode($pars["analysts"]);
            if (!is_array($analists))
            {
                $analists = array($analists);
            }
        }
        else
        {
            $analists = FALSE;
        }
        $fromDate = ($pars["fromdate"] && $pars["fromdate"] !== false && strlen($pars["fromdate"]) ? $pars["fromdate"] : date('Y-m-d', time() - (60 * 60 * 24 * 10)));

        $users = \AR\Vtuser::all();

        if ($analists)
        {
            foreach ($analists as $kk => $vv) {
                if ($vv === 0 || $vv === '0')
                {
                    $analists[$kk] = "Da assegnare";
                    continue;
                }
                foreach ($users as $ui => $u) {
                    if ($u->id == $vv)
                    {
                        $analists[$kk] = $u->first_name . " " . $u->last_name;
                    }
                }
            }
        }
        $options = array(
            'joins' => array(
                'JOIN vtiger_account acc ON (related_to = acc.accountid)',
                'JOIN vtiger_crmentity accent ON (acc.accountid = accent.crmid)',
                'JOIN vtiger_crmentity potent ON (potentialid = potent.crmid)',
                'JOIN vtiger_potentialscf potcf USING (potentialid)'
            ),
            'conditions' => array(
                'accent.deleted = ? AND potent.deleted = ? AND potentialname LIKE \'%PROGETTO%\' '
                . ' AND (sales_stage IN (?) OR closingdate > ?)'
                . ' AND closingdate > ?',
                array('0'),
                array('0'),
                array('Prospecting', 'Negotiation/Review'),
                array($fromDate),
                array($fromDate)
            ),
            'include' => array('cf', 'ent', 'account' => array("billads"), "activitycf" => array("act", "ent"))
        );
        if ($analists)
        {
            $options["conditions"][0] .= " AND potcf.cf_655 IN (?)";
            $options["conditions"][] = $analists;
        }
        try {
            $pot = AR\Vtpotential::all($options);
            $query = AR\Vtpotential::connection()->last_query;
        } catch (\Exception $e) {
            $query = AR\Vtpotential::connection()->last_query;
            echo json_encode($query);
            return;
        }
        $out = array();
        $aux = array($fromDate);
        foreach ($pot as $k => $p) {

            $app = $p->get_values_for(array(
                "ammontare", "data_chiusura_attesa", "descrizione", "fonte_lead", "id_opportunita", "numero_opportunita", "nome_opportunita",
                "probabilita", "prossimo_step", "stadio_vendita", "tipo_opportunita"
            ));
            $app = array_merge($app, $p->cf->get_values_for(array(
                        "ammontare_pesato", "analista", "esito_conferma", "esito_visita", "telemarketing", "venditore", "grace_scadenza"
            )));
            $act = $p->activitycf;
            $app["prova"] = array();

            foreach ($act as $k => $v) {
                if ($v->ent->deleted != '0')
                {
                    unset($act[$k]);
                }
                $app["prova"][] = array($v->act->data_inizio->format(DATE_ATOM), $p->data_chiusura_attesa->format(DATE_ATOM));
                if ($v->act->data_inizio->format(DATE_ATOM) != $p->data_chiusura_attesa->format(DATE_ATOM))
                {
                    unset($act[$k]);
                }
            }
            if (!$act || !count($act))
            {
                $app["status"] = "NO ACTIVITY ERROR";
                $app["error"] = true;
                $app["ora_inizio"] = "01:00";
            }
            else if (count($act) > 1)
            {
                foreach ($act as $aI => $a) {
                    $dd = new DateTime(str_ireplace(" 00:00:00", "", $a->act->data_inizio->format('Y-m-d')) . " " . $a->act->orario_inizio);
                    if (!isset($actFlag))
                    {
                        $actFlag = array($a->act->stato, $dd, $aI);
                    }
                    else if ($dd > $actFlag[1])
                    {
                        if ($actFlag[0] === "Held" || ($actFlag[0] === $a->act->stato))
                        {
                            $actFlag = array($a->act->stato, $dd, $aI);
                        }
                    }
                }
                if (isset($act[$actFlag[2]]))
                {
                    $app["ora_inizio"] = str_ireplace(":00:00", ":00", $act[$actFlag[2]]->act->orario_inizio);
                    $app["data_inizio"] = $act[$actFlag[2]]->act->data_inizio;
                    $app["id_attivita"] = $act[$actFlag[2]]->act->id_attivita;
                    $app["status"] = Vtiger::AnaAppStatus($p);
                }
                else
                {
                    $app["error"] = true;
                    $app["status"] = "NO ACTIVITY ERROR";
                    $app["ora_inizio"] = "01:00";
                }
            }
            else
            {
                $key = array_keys($act);
                $key = $key[0];
                $app["id_attivita"] = $act[$key]->act->id_attivita;
                $app["data_inizio"] = $act[$key]->act->data_inizio;
                $app["ora_inizio"] = str_ireplace(":00:00", ":00", $act[$key]->act->orario_inizio);
                $app["status"] = Vtiger::AnaAppStatus($p);
            }
            $app["ragione_sociale"] = $p->account->ragione_sociale;
            $app["email"] = $p->account->email;
            $app["indirizzo"] = $p->account->billads->via . " " . $p->account->billads->cap . " " . $p->account->billads->citta . " " . $p->account->billads->provincia;
            $app["telefono"] = $p->account->telefono;
            $app["piva"] = $p->account->cf->partita_iva;
            if (is_array($app["status"]))
                $app["status"] = $app["status"][0];
            $out[] = $app;
        }
//        $out[] = $aux;
        echo json_encode($out);
        return;
    }

    public function archiviovendite() {
        $this->disablerender();
        echo "<h1>Questa sezione é ancora in lavorazione.</h1>";
        return;
    }

    /**
     * 
     * @usedby analisi/gestioneanalisi
     */
    public function ws___corrNoActivityError() { //correzione per analisi
        $this->setJsonWS();
        $this->_setResponseStatus(200);
        $pars = array(
            "potId" => WolfMVC\RequestMethods::get("potId", false),
        );
        if (!isset($pars["potId"]) || !$pars["potId"])
        {
            $this->_setResponseStatus(400);
            echo json_encode("Invalid potentialid");
            return;
        }
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $options = array(
            "include" => array("ent", "cf", "account" => array("ent" => array("activityrel" => array("ent", "activity" => array("cf")))))
        );
        try {
            $pot = \AR\Vtpotential::find_by_pk((int) $pars["potId"], $options);
        } catch (\ActiveRecord\RecordNotFound $e) {
            echo json_encode("Impossibile risolvere il problema automaticamente. Si fa riferimento ad una opportunità inesistente.");
            $this->_setResponseStatus(404);
            return;
        } catch (\Exception $e) {
            echo json_encode("Si é verificato un errore cercando di recuperare l'opportunità.");
            $this->_setResponseStatus(404);
            return;
        }
        try {
            if ($pot->ent->deleted != '0' || $pot->account->ent->deleted != '0')
            {
                echo json_encode("Impossibile risolvere il problema automaticamente. Si fa riferimento ad una opportunità rimossa dal sistema.");
                $this->_setResponseStatus(404);
                return;
            }
            if (strpos($pot->nome_opportunita, "PROGETTO") === FALSE)
            {
                echo json_encode("Impossibile risolvere il problema automaticamente. Si fa riferimento ad una opportunità che non rappresenta un'analisi.");
                $this->_setResponseStatus(404);
                return;
            }
            if (!$pot->account->ent->activityrel || !count($pot->account->ent->activityrel))
            {
                echo json_encode("Impossibile risolvere il problema automaticamente. Non ci sono attivita' registrate.");
                $this->_setResponseStatus(404);
                return;
            }
            $acts = array();
            try {
                $pot_status = Vtiger::AnaAppStatus($pars["potId"]);
                $pot_status = $pot_status[0];
                $inferred_pot_status = Vtiger::InferAnaAppStatus($pars["potId"]);
                $inferred_pot_status = $inferred_pot_status[0];
                if ($pot_status !== $inferred_pot_status)
                {
//                    Vtiger::AnaAppStatus($pars["potId"], $inferred_pot_status);
                    $pot_status = $inferred_pot_status;
                }
            } catch (\Exception $e) {
                $this->_setResponseStatus(500);
                echo json_encode(array("Qualcosa é andato storto.", $e->getMessage(), $e->getTrace()));
                return;
            }
            $subject_match = "";
            switch ($pot_status) {
                case '_ANAAPP_PROSPECTINGOLD':
                    $subject_match = "/Analisi - (.*)/i";
                    break;
                case '_ANAAPP_QUALIFICATION':
                    $subject_match = "/Analisi - (.*)/i";
                    break;
                case '_ANAAPP_QUALIFIED':
                    $subject_match = "/Analisi CONFERMATA - (.*)/i";
                    break;
                case '_ANAAPP_CALLAGAIN':
                    $subject_match = "/Analisi DA CONFERMARE - (.*)/i";
                    break;
                case '_ANAAPP_GO':
                    $subject_match = "/Ripasso Analisi - (.*)|Analisi - (.*)|Analisi CONFERMATA - (.*)/i";
                    break;
                case '_ANAAPP_PASSAGAIN':
                    $subject_match = "/Ripasso Analisi - (.*)/i";
                    break;
                case '_ANAAPP_NOGO':
                    $subject_match = "/Ripasso Analisi - (.*)|Analisi - (.*)|Analisi CONFERMATA - (.*)/i";
                    break;
                default:
                    $this->_setResponseStatus(500);
                    echo json_encode(array(
                        "Impossibile risolvere il problema automaticamente. Per correggere questo errore e' necessario correggere qualcosa nell'opportunita'.",
                        $pot_status,
                        $pot->attributes(true),
                        $pot->cf->attributes(true)
                    ));
                    return;
                    break;
            }
            if (is_object($pot->data_chiusura_attesa) && is_string($pot->data_chiusura_attesa->format("Y-m-d")))
            {
                $data_chiusura_attesa = $pot->data_chiusura_attesa->format("Y-m-d");
            }
            else
            {
                $this->_setResponseStatus(500);
                echo json_encode("Impossibile risolvere il problema automaticamente. Per correggere questo errore e' necessario correggere qualcosa nell'opportunita'.");
                return;
            }
            $telemarketing = $pot->cf->telemarketing;
            $venditore = $pot->cf->venditore;
            $analista = $pot->cf->analista;
            $ragione_sociale = $pot->account->ragione_sociale;

            foreach ($pot->account->ent->activityrel as $k => $v) { // pulisco le attività
                $matches = array();
//                echo $pot_status."----\n";
//                echo $data_chiusura_attesa."----\n";
//                echo $v->activity->nome_attivita."  ".$v->activity->data_inizio->format("Y-m-d");
                if (is_object($v->activity->data_inizio) && is_string($v->activity->data_inizio->format("Y-m-d")) && $data_chiusura_attesa == $v->activity->data_inizio->format("Y-m-d"))
                {
                    if (preg_match($subject_match, $v->activity->nome_attivita, $matches))
                    {
                        if (count($matches) > 1 && $matches[count($matches) - 1] == $pot->account->ragione_sociale)
                            $acts[] = $v->activity;
                    }
//                    echo "\n";
//                    print_r($matches);
//                    echo "\n";
                }
            }
//            print_r($acts);
            if (!is_array($acts) || !count($acts))
            {
                $this->_setResponseStatus(500);
                echo json_encode(array(
                    "Impossibile risolvere il problema automaticamente. Si e' verificato un errore riguardante le attivita'.",
                    array_map(function($x) {
                                return array_merge($x->activity->attributes(), array("format" => $x->activity->data_inizio->format("Y-m-d")));
                            }, $pot->account->ent->activityrel),
                            $ragione_sociale, $subject_match, $pot->attributes(true), $pot->cf->attributes(true), $pot_status)
                        );
                        return;
                    }

///
//            $acts = array_map(function($x) {
//                return $x->attributes(true);
//            }, $acts);
//            echo json_encode(array($pot_status, $inferred_pot_status, $acts, $data_chiusura_attesa));
//            return;
///



                    $flag = false;
                    switch ($pot_status) {
                        case '_ANAAPP_QUALIFICATION':
                            $actTitle = trim("Analisi - " . $ragione_sociale);
                            break;
                        case '_ANAAPP_QUALIFIED':
                            $actTitle = trim("Analisi CONFERMATA - " . $ragione_sociale);
                            break;
                        case '_ANAAPP_CALLAGAIN':
                            $actTitle = trim("Analisi DA CONFERMARE - " . $ragione_sociale);
                            break;
                        case '_ANAAPP_GO':
                            $actTitle = trim("Analisi - " . $ragione_sociale);
                            break;
                        case '_ANAAPP_PASSAGAIN':
                            $actTitle = trim("Ripasso Analisi - " . $ragione_sociale);
                            break;
                        case '_ANAAPP_NOGO':
                            $actTitle = trim("Analisi - " . $ragione_sociale);
                            break;
                        default:
                            $actTitle = trim("Analisi - " . $ragione_sociale);
                            break;
                    }
                    foreach ($acts as $i => $a) {
                        $a->cf->id_opportunita = $pot->id_opportunita;
                        $a->cf->venditore = $venditore;
                        $a->cf->telemarketing = $telemarketing;
                        $a->cf->save();
                    }
                    echo json_encode(array("Il problema sembra risolto. Sarà possibile controllare il risultato dopo il reload dei dati.",
                        array_map(function($x) {
                                    return array_merge($x->activity->attributes(true), $x->activity->cf->attributes(true), array("format" => $x->activity->data_inizio->format("Y-m-d")));
                                }, $pot->account->ent->activityrel),
                                $ragione_sociale, $subject_match, $pot->attributes(true), $pot->cf->attributes(true), $pot_status
                            ));
                            return;
                        } catch (\Exception $e) {
                            echo json_encode(array($e->getMessage(), $e->getTrace()));
                            return;
                        }
                    }

//            public function ws___corrStatoIndefinito() {
//                $this->setJsonWS();
//                $this->_setResponseStatus(200);
//                $pars = $this->retrievePars(array(
//                    array("potId", "get", true)
//                ));
//                $res = vtiger::InferAnaAppStatus($pars["potId"]);
//            }

                    public static function authorizeGestioneAnalisi($authData) {
                        if (!isset($authData["id_utente"]))
                        {
                            return array("unable to find id_utente", 400, false);
                        }
                        $auth = array();
                        if (isset($authData["vt_id_utente"]))
                        {
                            $supervisione = FALSE;
                            foreach ($authData["vt_gruppi"] as $k => $g) {
                                if ($g["id_gruppo"] === 111)
                                {
                                    $supervisione = true;
                                    break;
                                }
                            }
                            $auth["supervisione"] = $supervisione;
                            if ($supervisione)
                            {
                                $auth["analisti"] = array();
                                $users_for_roles = Vtiger::getVtUserIdsByRole(array("H10", "H11"));
                                if ($users_for_roles[1] === 200)
                                {
                                    $auth["analisti"] = $users_for_roles[0];
                                }
                            }
                            else
                            {
                                $auth["analisti"] = array($authData["vt_id_utente"]);
                            }
                        }

                        return array($auth, 200, true);
                    }

                    public function ws___authorizeGestioneAnalisi() {
                        $session = WolfMVC\Registry::get("session");
                        $userid = $session->get("userid");
                        $vtuserid = $session->get("vtiger_logged_user_id");
                        ActiveRecord\Config::initialize(
                                \WolfMVC\Registry::get("activerecord_initializer")
                        );
                        $this->setJsonWS();
                        $user = AR\Vtuser::find($vtuserid, array("include" => array("role" => array("roledetails"), "groups" => array("groupdetails"))));
                        $authData = array();
                        $authData["id_utente"] = $userid;
                        $authData["vt_id_utente"] = $vtuserid;
                        $authData["vt_nome_utente"] = $user->nome_utente;
                        $authData["vt_id_ruolo"] = $user->role->id_ruolo;
                        $authData["vt_nome_ruolo"] = $user->role->roledetails->nome_ruolo;
                        $authData["vt_gruppi"] = array_map(function($x) {
                            return $x->groupdetails->get_values_for(array("id_gruppo", "nome_gruppo"));
                        }, $user->groups);
                        $auth = self::authorizeGestioneAnalisi($authData);
                        $this->_setResponseStatus($auth[1]);
                        if ($auth[1] === 200)
                        {
                            $authData["auth"] = $auth[0];
                            echo json_encode($authData);
                            return;
                        }
                        else
                        {
                            echo json_encode(array($auth[0], $auth[2]));
                            return;
                        }




//        $res = self::authorizeGestioneVendite()
                    }

                    public function ws___getVendLogDesc() {
                        $this->setJsonWS();
                        $linkmkt = $this->instantiateMysqli("mkt");
                        $linkvt = $this->instantiateMysqli("vtiger");
                        $query = "SELECT * FROM vendlog";

                        if ($linkmkt->connect_errno)
                        {
                            $this->_setResponseStatus(500);
                            echo json_encode("Error occurred in db connection!");
                            return;
                        }

                        $result = $linkmkt->query($query);
                        if ($result)
                        {
                            $tmklog = $result->fetch_all(MYSQLI_ASSOC);
                        }
                        else
                        {
                            $this->_setResponseStatus(500);
                            echo json_encode($linkmkt->error . "\nquery was " . $sql);
                            return;
                        }

                        $query = "SELECT a.id as USERID, a.first_name as NOME, a.last_name as COGNOME, a.email1 as EMAIL FROM vtiger_users a " .
                                "LEFT JOIN vtiger_user2role b ON (a.id = b.userid) " .
                                "WHERE a.status = 'Active'";
                        if ($linkvt->connect_errno)
                        {
                            $this->_setResponseStatus(500);
                            echo json_encode("Error occurred in db connection!");
                            return;
                        }
                        $result2 = $linkvt->query($query);
                        if ($result2)
                        {
                            $users = $result2->fetch_all(MYSQLI_ASSOC);
                        }
                        else
                        {
                            $this->_setResponseStatus(500);
                            echo json_encode("An error occurred retrieving collections: " . $linkvt->error);
                            return;
                        }

                        echo json_encode(array("tmklog" => $tmklog, "users" => $users));
//        echo json_encode($tmklog);
                        return;
                    }

                    public function ws___sendMailGestioneAnalisi() {
                        $this->setJsonWS();
                        $payload = array(
                            "dove" => \WolfMVC\RequestMethods::post("dove", false),
                            "cosa" => \WolfMVC\RequestMethods::post("cosa", false),
                            "commento" => \WolfMVC\RequestMethods::post("commento", false),
                        );
                        if (!isset($payload["dove"]) || !$payload["dove"])
                        {
                            $this->_setResponseStatus(400);
                            echo json_encode("Missing dove");
                            return;
                        }
                        if (!isset($payload["cosa"]) || !$payload["cosa"])
                        {
                            $this->_setResponseStatus(400);
                            echo json_encode("Missing cosa");
                            return;
                        }
                        $payload["quando"] = new \DateTime();
                        $payload["quando"] = $payload["quando"]->format('Y-m-d H:i:s');
                        $session = \WolfMVC\Registry::get("session");

                        $payload["chi"] = $session->get("googleUser");
                        if (is_array($payload["chi"]))
                        {
                            $payload["chi"] = $payload["chi"]["displayName"];
                        }
                        else if (is_object($payload["chi"]))
                        {
                            $payload["chi"] = $payload["chi"]->displayName;
                        }

                        $url = 'https://script.google.com/macros/s/AKfycbxau6se2P8Pi-AzneZrIxBS9q0HSFJ9AZQBP5QZRpw93l5JBKk/exec';
                        $fields = array();
                        foreach ($payload as $k => $p) {
                            if (is_string($p))
                            {
                                $fields[$k] = urldecode($p);
                            }
                            else
                            {
                                $fields[$k] = urldecode(json_encode($p));
                            }
                        }
                        $out_payload = http_build_query($fields);

//
                        ob_start();
                        $ch = curl_init($url);
                        $options = array(CURLOPT_RETURNTRANSFER => true, // return web page
                            CURLOPT_HEADER => 0, // don't return headers
                            CURLOPT_FOLLOWLOCATION => true, // follow redirects
                            CURLOPT_ENCODING => "", // handle compressed
                            CURLOPT_USERAGENT => "test", // who am i
                            CURLOPT_AUTOREFERER => true, // set referer on redirect
                            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
                            CURLOPT_TIMEOUT => 120, // timeout on response
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_POST => count($fields),
                            CURLOPT_POSTFIELDS => $out_payload
                        );

                        curl_setopt_array($ch, $options);

                        $result = curl_exec($ch);
                        if (is_string($result))
                            $result = json_decode($result, true);
                        $err = curl_errno($ch);
                        $errmsg = curl_error($ch);
                        $header = curl_getinfo($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        curl_close($ch);
                        ob_end_clean();
                        $out["fields"] = $fields;

                        $payload["ricevuto"] = true;
                        $out["payload"] = $payload;

                        $out["out_payload"] = $out_payload;
                        $out["result"] = $result;
                        $out["err"] = $err;
                        $out["errmsg"] = $errmsg;
                        $this->_setResponseStatus($httpCode);
                        if ($httpCode !== 200)
                        {
                            echo json_encode(array(
                                "err" => $err,
                                "errmsg" => $errmsg
                            ));
                            return;
                        }
                        if (is_array($result) && isset($result["ricevuto"]) && $result["ricevuto"] !== "ok")
                        {
                            echo json_encode(array(
                                "err" => $err,
                                "errmsg" => $errmsg,
                                "result" => $result
                            ));
                            return;
                        }
                        echo json_encode($out);
                        return;
                    }

                }
                