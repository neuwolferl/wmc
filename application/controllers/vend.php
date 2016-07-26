<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Vend extends Controller {

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

        $view->set("moduleName", "COMMERCIALE");
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
            "Gestione vendite" => $this->pathTo($this->nameofthiscontroller(), "gestionevendite"),
            "Diretta venditore" => $this->pathTo($this->nameofthiscontroller(), "direttavenditore")
        ));
    }

    public function gestionevendite() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("VENDITE" => "mkt", "Vendite in corso" => "last")));
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

    public function ws___gestionevendite() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "getAppuntamenti" => SITE_PATH . "vend/getAppuntamenti.ws",
                "getDetails" => SITE_PATH . "vend/getDetails.ws",
                "getVendLogDesc" => SITE_PATH . "vend/getVendLogDesc.ws",
                "inferComAppStatus" => SITE_PATH . "vtiger/InferComAppStatus.ws",
                "corrNoActivityError" => SITE_PATH . "vend/corrNoActivityError.ws",
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
                "addQuote" => SITE_PATH . "vtiger/addQuote.ws",
                "ComAppStatus" => SITE_PATH . "vtiger/ComAppStatus.ws",
                "setEsclusione32" => SITE_PATH . "mkt/setEsclusione32.ws",
                "setLeadPipeStatus" => SITE_PATH . "mkt/setLeadPipeStatus.ws",
                "authorizeGestioneVendite" => SITE_PATH . "vend/authorizeGestioneVendite.ws",
            )
        );
        echo json_encode($config);
        return;
    }

    public function direttavenditore() {
        $lay = $this->getLayoutView();
//        $lay->set("breadCrumb", $this->breadCrumb(array("VENDITE" => "mkt", "Diretta Venditore" => "last")));
        
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
        $view = $this->getActionView();
        $view->set("breadCrumb", $this->breadCrumb(array("VENDITE" => "vend", "Diretta Venditore" => "last")));
        $view->set("parent", '$parent');
        $view->set("error", '$error');
        $view->set("valid", '$valid');
        $view->set("invalid", '$invalid');
        $view->set("dirty", '$dirty');
        $view->set("index", '$index');
    }

    public function ws___direttavenditore() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "searchLeadByPiva" => SITE_PATH . "mkt/searchLeadByPiva.ws",
                "setEsclusioneBit" => SITE_PATH . "mkt/setEsclusioneBit.ws",
                "getLocalita" => SITE_PATH . "mkt/getLocalita.ws",
                "getAteco" => SITE_PATH . "mkt/getAteco.ws",
                "createLead" => SITE_PATH . "mkt/createLead.ws",
                "sendReportMail" => SITE_PATH . "mkt/sendReportMail.ws",
                "authorizeGestioneVendite" => SITE_PATH . "vend/authorizeGestioneVendite.ws",
                "retrieveAccountByPiva" => SITE_PATH . "vtiger/retrieveAccountByPiva.ws",
                "addAccount" => SITE_PATH . "vtiger/addAccount.ws",
                "editAccount" => SITE_PATH . "vtiger/editAccount.ws",
                "closeAllActivities" => SITE_PATH . "vtiger/closeAllActivities.ws",
                "addContact" => SITE_PATH . "vtiger/addContact.ws",
                "addPotential" => SITE_PATH . "vtiger/addPotential.ws",
                "addActivity" => SITE_PATH . "vtiger/addActivity.ws",
                "ComAppStatus" => SITE_PATH . "vtiger/ComAppStatus.ws",
                "getLead" => SITE_PATH . "mkt/getLead.ws",
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
                    "fascia_fatturato_com", "classe_dipendenti_com", "forma_giuridica_com"
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
            if ($pp->ent->deleted == '0' && $pp->nome_opportunita == 'Analisi')
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

    public function ws___getAppuntamenti() {
        $this->setJsonWS();
        $pars = array(
            "vendors" => \WolfMVC\RequestMethods::get("vendors", FALSE),
            "fromdate" => \WolfMVC\RequestMethods::get("fromdate", FALSE)
        );

        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (isset($pars["vendors"]) && strlen($pars["vendors"]))
        {
            $vend = json_decode($pars["vendors"]);
            if (!is_array($vend))
            {
                $vend = array($vend);
            }
        }
        else
        {
            $vend = FALSE;
        }
        $fromDate = ($pars["fromdate"] && strlen($pars["fromdate"]) ? $pars["fromdate"] : date('Y-m-d', time() - (60 * 60 * 24 * 10)));

        $users = \AR\Vtuser::all();
        if ($vend)
        {
            foreach ($vend as $kk => $vv) {
                foreach ($users as $ui => $u) {
                    if ($u->id == $vv)
                    {
                        $vend[$kk] = $u->first_name . " " . $u->last_name;
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
                'accent.deleted = ? AND potent.deleted = ? AND potentialname = ?'
                . ' AND (sales_stage IN (?) OR closingdate > ?)'
                . ' AND sales_stage <> ?'
                . ' AND closingdate > ?',
                array('0'),
                array('0'),
                array('Analisi'),
                array('Prospecting', 'Negotiation/Review'),
                array($fromDate),
                array('Qualification'),
                array($fromDate)
            ),
            'include' => array('cf', 'ent', 'account' => array("billads"), "activitycf" => array("act", "ent"))
        );
        if ($vend)
        {
            $options["conditions"][0] .= " AND potcf.cf_647 IN (?)";
            $options["conditions"][] = $vend;
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
                "ammontare", "data_chiusura_attesa", "descrizione", "fonte_lead", "id_opportunita", "numero_opportunita",
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
                    $app["status"] = Vtiger::ComAppStatus($p);
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
                $app["status"] = Vtiger::ComAppStatus($p);
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

    public function ws___corrNoActivityError() {
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
            "include" => array("cf", "account" => array("ent" => array("activityrel" => array("ent", "activity" => array("cf")))))
        );
        try {
            $pot = \AR\Vtpotential::find_by_pk((int) $pars["potId"], $options);
            if (!isset($pot->id_opportunita))
            {
                echo json_encode("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema");
                $this->_setResponseStatus(404);
                return;
            }
            if ($pot->account->ent->deleted != '0')
            {
                echo json_encode("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema");
                $this->_setResponseStatus(404);
                return;
            }
            if (!$pot->account->ent->activityrel)
            {
//                print_r($pot->account->ent);
//                print_r($pot->account->ent->activityrel);
                echo json_encode("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema");
                $this->_setResponseStatus(404);
                return;
            }
            $acts = array();
            foreach ($pot->account->ent->activityrel as $k => $v) {
//                print_r($v->activity);
                $acts[] = $v->activity;
            }
//            print_r($acts);
//            return;
            if (!is_array($acts) || !count($acts))
            {
                echo json_encode("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema.");
                $this->_setResponseStatus(500);
                return;
            }
            $venditore = $pot->cf->venditore;
            $venditore = explode(" ", $venditore);
            $venditore = $venditore[1];
            $ragione_sociale = $pot->account->ragione_sociale;
            $flag = false;
            $actTitle = trim(strtoupper($venditore) . " - " . $ragione_sociale);
            $outForError = array("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema.");
            try {
                foreach ($acts as $i => $a) {

                    $outForError[] = $a->id_attivita;
                    $outForError[] = $pot->data_chiusura_attesa->format(DATE_ATOM);
                    $outForError[] = $a->data_inizio->format(DATE_ATOM);
                    $outForError[] = $a->nome_attivita;
                    $outForError[] = $actTitle;
                    if ($a->nome_attivita === $actTitle &&
                            $a->data_inizio->format(DATE_ATOM) === $pot->data_chiusura_attesa->format(DATE_ATOM))
                    {
                        $flag = true;
                        $a->cf->id_opportunita = $pot->id_opportunita;
                        $a->cf->save();
                    }
                }
            } catch (\Exception $e) {
                echo json_encode("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema.");
                $this->_setResponseStatus(500);
                return;
            }
            if ($flag)
            {
                echo json_encode("Il problema sembra risolto. Sarà possibile controllare il risultato dopo il reload dei dati.");
            }
            else
            {
//                echo json_encode("Impossibile risolvere il problema automaticamente. Contattare un amministratore fornendo i dati del problema.");
                echo json_encode($outForError);
                $this->_setResponseStatus(500);
            }
            return;
        } catch (\Exception $e) {
            echo json_encode(array($e->getMessage(), $e->getTrace()));
            return;
        }
    }

    public function ws___corrStatoIndefinito() {
        $this->setJsonWS();
        $this->_setResponseStatus(200);
        $pars = $this->retrievePars(array(
            array("potId", "get", true)
        ));
        $res = self::InferComAppStatus($pars["potId"]);
    }

    public static function authorizeGestioneVendite($authData) {
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
                $auth["venditori"] = array();
                $users_for_roles = Vtiger::getVtUserIdsByRole(array("H4", "H5"));
                if ($users_for_roles[1] === 200)
                {
                    $auth["venditori"] = $users_for_roles[0];
                }
            }
            else
            {
                $auth["venditori"] = array($authData["vt_id_utente"]);
            }
        }

        return array($auth, 200, true);
    }

    public function ws___authorizeGestioneVendite() {
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
        $authData["user"] = $user->get_values_for(array("nome","cognome"));
        $authData["vt_id_utente"] = $vtuserid;
        $authData["vt_nome_utente"] = $user->nome_utente;
        $authData["vt_id_ruolo"] = $user->role->id_ruolo;
        $authData["vt_nome_ruolo"] = $user->role->roledetails->nome_ruolo;
        $authData["vt_gruppi"] = array_map(function($x) {
            return $x->groupdetails->get_values_for(array("id_gruppo", "nome_gruppo"));
        }, $user->groups);
        $auth = self::authorizeGestioneVendite($authData);
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

}
