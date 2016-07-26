<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Tmk extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    /**
     * @protected
     */
    public function script_including() {
//        $view = new \WolfMVC\View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per il layout
//            "file" => APP_PATH . "/{$this->getDefaultPath()}/layouts/daf.{$this->getDefaultExtension()}"
//        ));
//        $this->setLayoutView($view);

        $this->loadScript("utils.js");
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");


        $this->loadScript("lodash.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.core.js");
        $this->loadScript("core/RGraph/libraries/RGraph.gantt.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.dynamic.js");
        $this->loadScript("angular-google-maps.min.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");

        $view = $this->getLayoutView();
        $view->set("moduleName", "TELEMARKETING");
    }

    public function index() {
        $view = $this->getActionView();
        $view->set("actions", array(
            "Gestione chiamate" => $this->pathTo($this->nameofthiscontroller(), "gestionechiamate", array()),
            "Crea appuntamento" => $this->pathTo($this->nameofthiscontroller(), "creaappuntamento", array())
        ));
    }

    public function gestionechiamate() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");


        $this->loadScript("lodash.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.core.js");
        $this->loadScript("core/RGraph/libraries/RGraph.gantt.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.dynamic.js");
        $this->loadScript("angular-google-maps.min.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");

        $this->loadScript("core/datamodule/data-0.4.js");
        $this->loadScript("core/smartButton/smartButton-0.1.js");
        $this->loadScript("core/themask/themask-0.3.js");
        $this->loadScript("component/leadTable/leadTable-0.1.js");
        $this->loadScript("component/leadTable/leadTable-0.1.css");
        $this->loadScript("component/sinottico/sinottico-0.1.js");
        $this->loadScript("ng-lodash.min.js");
        $this->loadScript("core/logGeneral.css");
        $this->loadScript("component/logger/logger-0.1.js");
        $view = $this->getActionView();
        $view->set("root", '$root');
        $view->set("index", '$index');
        $view->set("status", '\'status\'');
    }

    public function ws___gestionechiamate() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getTMKPageConf" => SITE_PATH . "tmk/getTMKPageConf.ws",
                "getNewSheet" => SITE_PATH . "tmk/getNewSheet.ws",
                "amendPiva" => SITE_PATH . "mkt/amendPiva.ws",
                "creaVTAccount" => SITE_PATH . "tmk/creaVTAccount.ws",
                "creaVTContact" => SITE_PATH . "tmk/creaVTContact.ws",
                "creaVTPotential" => SITE_PATH . "tmk/creaVTPotential.ws",
                "creaVTActivity" => SITE_PATH . "tmk/creaVTActivity.ws",
                "creatoAppuntamento" => SITE_PATH . "tmk/creatoAppuntamento.ws",
                "creaRichiamo" => SITE_PATH . "tmk/creaRichiamo.ws",
                "creaRichiamoRapido" => SITE_PATH . "tmk/creaRichiamoRapido.ws",
                "scartoScheda" => SITE_PATH . "tmk/scartoScheda.ws",
                "getCampaign" => SITE_PATH . "mkt/getCampaign.ws",
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "getAppuntamenti" => SITE_PATH . "tmk/getAppuntamenti.ws",
                "putLog" => SITE_PATH . "tmk/putLog.ws",
                "getLog" => SITE_PATH . "tmk/getLog.ws",
                "chiamaNumero" => SITE_PATH . "tmk/chiamaNumero.ws",
                "configuraGestChiam" => SITE_PATH . "tmk/configuraGestChiam.ws",
                "getInventario" => SITE_PATH . "tmk/getInventario.ws",
                "getTmkLogDesc" => SITE_PATH . "tmk/getTmkLogDesc.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function creaappuntamento() {
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
        $view->set("breadCrumb", $this->breadCrumb(array("TMK" => "tmk", "Crea appuntamento" => "last")));
        $view->set("parent", '$parent');
        $view->set("error", '$error');
        $view->set("valid", '$valid');
        $view->set("invalid", '$invalid');
        $view->set("dirty", '$dirty');
        $view->set("index", '$index');
    }

    public function ws___creaappuntamento() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "searchLeadByPiva" => SITE_PATH . "mkt/searchLeadByPiva.ws",
                "setEsclusioneBit" => SITE_PATH . "mkt/setEsclusioneBit.ws",
                "getLocalita" => SITE_PATH . "mkt/getLocalita.ws",
                "getAteco" => SITE_PATH . "mkt/getAteco.ws",
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
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
                "getLead" => SITE_PATH . "mkt/getLead.ws"
            )
        );
        echo json_encode($config);
        return;
    }
    
    public function ws___getTMKPageConf() {
        $this->setJsonWS();

        $mainquery = "SELECT b.localita as loc, b.provincia as PROV, (count(a.PartitaIva)) as count, "
                . "IFNULL(convert((count(a.PartitaIva)) - (sum(a.Esclusione/a.Esclusione)),unsigned), 0) as callable, "
                . "b.cap as CAP, b.lat as LAT, b.lng as LNG " .
                "FROM comuni_geo_cap b "
                . "LEFT JOIN aziende_all a ON (a.Localita = b.localita AND a.Provincia = b.provincia)" .
                "WHERE b.provincia = '" . $provincia . "' " .
                "GROUP BY b.localita";
//        $mainquery = "SELECT a.Localita as loc, a.Provincia as PROV, count(a.PartitaIva) as count, b.cap as CAP, b.lat as LAT, b.lng as LNG " .
//                "FROM aziende_all a " .
//                "LEFT JOIN comuni_geo_cap b ON (a.Localita = b.localita AND a.Provincia = b.provincia) " .
//                "WHERE a.Provincia = '" . $provincia . "' " .
//                "GROUP BY a.Localita";
        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";
        $limitquery = $mainquery . " LIMIT %s,%s";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___getNewSheet() {
        $this->setJsonWS();
        sleep(1);
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("tmk", "get", true),
                    array("vend", "get", true),
                    array("camp", "get", true),
                    array("ts", "get", true),
                    array("sel", "get", false)
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            echo json_encode($pars);
            return;
        }
        if (!isset($pars["sel"]))
        {
            $pars["sel"] = "";
        }
        if ($pars["sel"] === "null")
        {
            $pars["sel"] = "";
        }
        if ($pars["sel"])
        {
            switch ($pars["sel"]) {
                case '1':
                case 1: 
                    $pars["sel"] = "AND b.Punteggio_Azienda >= '90' ";
                    break;
                case '2':
                    case2:
                    $pars["sel"] = "AND b.Punteggio_Azienda >= '50' AND b.Punteggio_Azienda < '90' ";
                    break;
                case '3':
                case 3:
                    $pars["sel"] = "AND b.Punteggio_Azienda >= '0' AND b.Punteggio_Azienda < '50' ";
                    break;
                default:
                    $pars["sel"] = "";
                    break;
            }
        }
        else
        {
            $pars["sel"] = "";
        }
        $pars["ts"] = (int) $pars["ts"];
        $pars["ts"] = floor($pars["ts"] / 1000);
        $time = new DateTime("@" . $pars["ts"]);
        $now = $time->format("Y-m-d H:i:s");
//        echo date("U", $pars["ts"]);
        //scheda in lavorazione
//        $result = $link->query("SELECT now() as n");
//        if ($result && $result->num_rows){
//            $nowDB = $result->fetch_all(MYSQLI_ASSOC);
//            $nowDB = $nowDB[0]["n"];
//            echo $nowDB;
//            echo $now;
//            return;
//        }
//        return;
                
        $retrquery = "SELECT now() as now, " .
                "b.Punteggio_Azienda as Punteggio, b.RagioneSociale, b.PartitaIva, b.Indirizzo, b.Localita, b.Cap, b.Provincia, b.Regione, b.SedePrincipale_IM as SedePrincipale, " .
                "b.Email_AI as Email,b.HomePage_IM as Sito, b.DataCostituzione_AI as DataCostituzione,b.FormaGiuridica,b.FasciaFatturato, b.ClasseDipendenti, " .
                "b.Attivita_ATECO as AttivitaAteco, " .
                "if(isnull(b.Telefono),GROUP_CONCAT(c.Tel),if(isnull(GROUP_CONCAT(c.Tel)),b.Telefono,CONCAT(b.Telefono, ',', GROUP_CONCAT(c.Tel)))) as Telefoni, " .
                "a.pipe, a.working, a.worker, a.tmklock, a.donotcallbefore, d.* FROM  " .
                "ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "LEFT JOIN Numeri_telefono c ON (a.piva = c.PartitaIva) " .
                "LEFT JOIN ts_piva_tmp_lock d ON (a.piva = d.piva) " .
//                "LEFT JOIN ClasseDipendenti e ON (b.ClasseDipendenti = e.ClasseDipendenti) " .
                "WHERE " .
                "a.tmklock = '{$pars['tmk']}' AND (isnull(d.static_lock) OR d.static_lock != '1') " .
                "AND a.working = '1' " .
                "group by c.PartitaIva " .
                "ORDER BY a.working DESC, lot ASC, a.pipe DESC, b.Punteggio_Azienda DESC " .
                "LIMIT 1";
        //prenotata
        $queryPrenotata = "SELECT now() as now, " .
                "b.Punteggio_Azienda as Punteggio, " .
                "b.RagioneSociale, " .
                "b.PartitaIva, " .
                "b.Indirizzo, " .
                "b.Localita, " .
                "b.Cap, " .
                "b.Provincia, " .
                "b.Regione, " .
                "b.SedePrincipale_IM as SedePrincipale, " .
                "b.Email_AI as Email, " .
                "b.HomePage_IM as Sito, " .
                "b.DataCostituzione_AI as DataCostituzione, " .
                "b.FormaGiuridica, " .
                "b.FasciaFatturato, b.ClasseDipendenti, " .
                "b.Attivita_ATECO as AttivitaAteco, " .
                "if(isnull(b.Telefono),GROUP_CONCAT(c.Tel),if(isnull(GROUP_CONCAT(c.Tel)),b.Telefono,CONCAT(b.Telefono, ',', GROUP_CONCAT(c.Tel)))) as Telefoni, " .
                "a.pipe, a.tmklock, a.working, a.worker, " .
                "a.donotcallbefore, d.* " .
                "FROM " .
                "ts_tmk_pipes a " .
                "JOIN " .
                "aziende_all b ON (a.piva = b.PartitaIva) " .
                "left JOIN " .
                "Numeri_telefono c ON (a.piva = c.PartitaIva) " .
                "LEFT JOIN ts_piva_tmp_lock d ON (a.piva = d.piva) " .
//                "LEFT JOIN " .
//                "ClasseDipendenti e ON (b.ClasseDipendenti = e.ClasseDipendenti) " .
                "WHERE " .
                "d.force = '{$pars['tmk']}' AND (isnull(d.static_lock) OR d.static_lock != '1') " .
                "group by c.PartitaIva " .
                "ORDER BY lot ASC, a.pipe DESC, a.tmklock DESC, a.donotcallbefore ASC " .
                "LIMIT 1";
//        echo $queryRichiamo1;
        //richiamo personale + altro venditore
//        $queryRichiamo2 = "SELECT " .
//                "b.Punteggio_Azienda as Punteggio, " .
//                "b.RagioneSociale, " .
//                "b.PartitaIva, " .
//                "b.Indirizzo, " .
//                "b.Localita, " .
//                "b.Cap, " .
//                "b.Provincia, " .
//                "b.Regione, " .
//                "b.SedePrincipale_IM as SedePrincipale, " .
//                "b.Email_AI as Email, " .
//                "b.HomePage_IM as Sito, " .
//                "b.DataCostituzione_AI as DataCostituzione, " .
//                "b.FormaGiuridica, " .
//                "b.FasciaFatturato, b.ClasseDipendenti, " .
//                "b.Attivita_ATECO as AttivitaAteco, " .
//                "if(isnull(b.Telefono),GROUP_CONCAT(c.Tel),CONCAT(b.Telefono, ',', GROUP_CONCAT(c.Tel))) as Telefoni, " .
//                "a.pipe, a.tmklock, a.working, a.worker, " .
//                "a.donotcallbefore, d.* " .
//                "FROM " .
//                "ts_tmk_pipes a " .
//                "JOIN " .
//                "aziende_all b ON (a.piva = b.PartitaIva) " .
//                "JOIN " .
//                "Numeri_telefono c ON (a.piva = c.PartitaIva) " .
//                "LEFT JOIN ts_piva_tmp_lock d ON (a.piva = d.piva) " .
////                "LEFT JOIN " .
////                "ClasseDipendenti e ON (b.ClasseDipendenti = e.ClasseDipendenti) " .
//                "WHERE " .
//                "a.tmklock = '{$pars['tmk']}' " .
//                "AND a.pipe = '2' AND d.unlock_timestamp <= str_to_date('{$now}','%Y-%m-%d %H:%i:%s') " .
//                "AND d.unlock_timestamp <> '' AND d.unlock_timestamp <> '0000-00-00 00:00:00' " .
//                "group by c.PartitaIva " .
//                "ORDER BY lot ASC, a.pipe DESC, a.donotcallbefore ASC " .
//                "LIMIT 1";
        //scheda vergine 
        $mainquery = "SELECT now() as now, " .
                "b.Punteggio_Azienda as Punteggio, b.RagioneSociale, b.PartitaIva, b.Indirizzo, b.Localita, b.Cap, b.Provincia, b.Regione, b.SedePrincipale_IM as SedePrincipale, " .
                "b.Email_AI as Email,b.HomePage_IM as Sito, b.DataCostituzione_AI as DataCostituzione,b.FormaGiuridica,b.FasciaFatturato, b.ClasseDipendenti, " .
                "b.Attivita_ATECO as AttivitaAteco, " .
                "if(isnull(b.Telefono),GROUP_CONCAT(c.Tel),if(isnull(GROUP_CONCAT(c.Tel)),b.Telefono,CONCAT(b.Telefono, ',', GROUP_CONCAT(c.Tel)))) as Telefoni, a.tmklock, a.pipe, a.working, a.worker, a.donotcallbefore FROM  " .
                "ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "LEFT JOIN Numeri_telefono c ON (a.piva = c.PartitaIva) " .
                "LEFT JOIN ts_piva_tmp_lock d ON (a.piva = d.piva) " .
                "WHERE a.worker = '{$pars['vend']}' " .
                "AND a.pipe = '1' " .
                "AND a.tmklock = '0' " .
                "AND ( isnull(d.unlock_timestamp) OR d.unlock_timestamp = '0000-00-00 00:00:00' OR isnull(d.unlock_timestamp) " .
                "OR (d.unlock_timestamp <> '' AND d.unlock_timestamp <> '0000-00-00 00:00:00' AND d.unlock_timestamp <= '{$now}'))" .
                
                $pars["sel"] .
                "AND b.Source = 'IM' " .
                "AND b.Localita in ( " .
                "SELECT loc FROM  " .
                "ts_jobcoverage " .
                "WHERE vend = '{$pars['vend']}' " .
                "AND tmk = '{$pars['tmk']}' " .
                "AND camp = '{$pars['camp']}' " .
                ") " .
                "group by c.PartitaIva " .
                "ORDER BY a.working DESC, lot ASC, b.Punteggio_Azienda DESC " .
                "LIMIT 1";

//                echo $mainquery; 
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $jump = false;
        if (!$jump) //cerco scheda in lavorazione
        {
            $firstret = array();
            $ret = array();
            $piva = "";
            $result = $link->query($retrquery);
            if (!$result)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                        . "Questo potrebbe essere dovuto ad un errore." . $link->error . "   " . $retrquery);
                return;
            }
            if ($result->num_rows)
            {
                $firstret = $result->fetch_all(MYSQLI_ASSOC);
                $piva = $firstret[0]["PartitaIva"];
                $jump = true;
            }
        }
        if (!$jump) //cerco scheda prenotata
        {
            $firstret = array();
            $ret = array();
            $piva = "";
            $result = $link->query($queryPrenotata);
            if (!$result)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                        . "Questo potrebbe essere dovuto ad un errore." . $link->error . "   " . $queryPrenotata);
                return;
            }
            if ($result->num_rows)
            {
                $firstret = $result->fetch_all(MYSQLI_ASSOC);
                $piva = $firstret[0]["PartitaIva"];
                $jump = true;
            }
        }
//        if (!$jump) //cerco scheda richiamo2
//        {
//            $firstret = array();
//            $ret = array();
//            $piva = "";
//            $result = $link->query($queryRichiamo2);
//            if (!$result)
//            {
//                $this->_setResponseStatus(500);
//                echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
//                        . "Questo potrebbe essere dovuto ad un errore." . $link->error . "   " . $queryRichiamo2);
//                return;
//            }
//            if ($result->num_rows)
//            {
//                $firstret = $result->fetch_all(MYSQLI_ASSOC);
//                $piva = $firstret[0]["PartitaIva"];
//                $jump = true;
//            }
//        }
        if (!$jump) //cerco scheda vergine
        {
            $firstret = array();
            $ret = array();
            $piva = "";
            $result = $link->query($mainquery);
            if (!$result)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                        . "Questo potrebbe essere dovuto ad un errore." . $link->error . "   " . $mainquery);
                return;
            }
            if ($result->num_rows)
            {
                $firstret = $result->fetch_all(MYSQLI_ASSOC);
                $piva = $firstret[0]["PartitaIva"];
                $jump = true;
            }
        }
        if (!$jump)
        {
            $this->_setResponseStatus(404);
            echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                    . "Questo potrebbe essere dovuto ad un errore." . $link->error . " " . $now);
            return;
        }
        //qui sono sicuro di avere almeno una piva


        $queryvt = "SELECT " .
                "b.accountid as vtaccid, b.account_no as vtaccno, b.website as Sito, b.email1 as Email, " .
                "b.accountname as RagioneSociale, employees as NumeroDipendenti, b.annualrevenue as Fatturato, " .
                "concat('[', " .
                "GROUP_CONCAT( " .
                "CONCAT('{','\"vtid\" : \"',c.contactid, '\", \"vtno\" : \"', c.contact_no,'\", \"nome\" : \"', c.firstname,'\", \"cognome\" : \"', c.lastname,'\", \"email\" : \"', "
                . "c.email, '\", \"phone\" : \"', c.phone,'\", \"cel\" : \"', c.mobile,'\", \"fax\" : \"', c.fax,'\", \"ruolo\" : \"',c.title,'\"}') " .
                ") " .
                ",\"]\") " .
                "as contatti " .
                "FROM " .
                "vtiger_accountscf a " .
                "LEFT JOIN vtiger_account b ON (a.accountid = b.accountid) " .
                "LEFT JOIN vtiger_crmentity accent ON (a.accountid = accent.crmid) " .
                "LEFT JOIN vtiger_contactdetails c  ON (c.accountid = a.accountid) " .
                "LEFT JOIN vtiger_crmentity conent ON (c.contactid = conent.crmid) " .
                "WHERE accent.deleted = '0'  " .
                "AND conent.deleted = '0' " .
                "AND a.cf_641 = '%s'";

        $queryamend = "SELECT * FROM aziende_all_amend " .
                "WHERE PartitaIva = '%s'";




        $ret = $firstret;
        $ret[0]["req_time"] = $now;
        $sql2 = sprintf($queryvt, $piva);
        $sql3 = sprintf($queryamend, $piva);
        $rresult2 = $link2->query($sql2);
        $rresult3 = $link->query($sql3);
        if (!$rresult2)
        {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                    . "Questo potrebbe essere dovuto ad un errore." . $link->error2 . "   " . $sql2);
            return;
        }
        if (!$rresult3)
        {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                    . "Questo potrebbe essere dovuto ad un errore." . $link->error . "   " . $sql3);
            return;
        }
        if (!$rresult2->num_rows)
        {
            $ret[0]["VT"] = false;
        }
        else
        {
            $retret = $rresult2->fetch_all(MYSQLI_ASSOC);
//                print_r($retret);
            $ret[0]["VT"] = $retret[0];
        }
        if (!$rresult3->num_rows)
        {
            $ret[0]["amend"] = array();
        }
        else
        {
            $retret = $rresult3->fetch_all(MYSQLI_ASSOC);
            $ret[0]["amend"] = $retret;
        }
        $querylock = "UPDATE ts_tmk_pipes SET tmklock = '{$pars['tmk']}', working = '1' WHERE piva = '{$piva}'";
        $resultlock = $link->query($querylock);
        $ret["pippo"] = $mainquery;
        if ($resultlock)
        {
            echo json_encode($ret);
            return;
        }
        else
        {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode("Attenzione, non e' stato possibile caricare una nuova scheda. "
                    . "Questo potrebbe essere dovuto ad un errore." . $link->error . "   " . $querylock);
            return;
        }
    }

    public function ws___chiamaNumero() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("numero", "get", true),
                    array("debug", "get", false)
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            echo json_encode($pars);
            return;
        }
        $session = \WolfMVC\Registry::get("session");
        $name = $session->get("googleUser");

        $ext = $session->get("asterisk_extension");
        $com = new Asterisk\Asteriskcall();
//        echo "extension ".$ext;
        if (isset($pars["debug"]) && $pars["debug"] === "debug")
        {
            echo json_encode("Chiamata finta... ci siamo capiti!");
            return;
        }
        else
        {
            $com->startCall($session->get("vtiger_logged_user_id"), $pars["numero"], $ext);
            echo json_encode(true);
            return;
        }
    }

    public function ws___configuraGestChiam() {
        $this->setJsonWS();
        sleep(1);
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
//        $pars = $this->retrievePars(
//                array(
//                    
//                )
//        );
        $session = \WolfMVC\Registry::get("session");
        $tmk = $session->get("vtiger_logged_user_id");
        $tmkName = $session->get("googleUser");
        $tmkName = $tmkName["displayName"];

        $query = "SELECT DISTINCT vend FROM ts_jobcoverage WHERE tmk = '{$tmk}' LIMIT 1";

        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $link->query($sql);
        if ($result)
        {
            if (!$result->num_rows)
            {
                $this->_setResponseStatus(404);
                echo json_encode(false);
                return;
            }
            else
            {
                $vend = $result->fetch_all(MYSQLI_ASSOC);
                $vend = $vend[0]["vend"];
                $query2 = "SELECT a.id, CONCAT(a.first_name,' ',a.last_name) as vendorName FROM vtiger540.vtiger_users a " .
                        "LEFT JOIN vtiger540.vtiger_user2role b ON (b.userid = a.id) " .
                        "WHERE b.roleid IN ('H4','H5')";
                $result2 = $link2->query($query2);
                if ($result2 && $result2->num_rows)
                {
                    $vendors = $result2->fetch_all(MYSQLI_ASSOC);
                    foreach ($vendors as $k => $v) {
                        if ($v["id"] == $vend)
                        {
                            $vendorName = $v["vendorName"];
                        }
                    }
                    echo json_encode(array(
                        "tmk" => $tmk,
                        "vend" => $vend,
                        "vendorName" => $vendorName,
                        "tmkName" => $tmkName,
                        "vendors" => $vendors
                    ));
                    return;
                }
                else
                {
                    $this->_setResponseStatus(500);
                    echo $query2;
                }
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode(false);
            return;
        }
    }

    public function ws___creatoAppuntamento() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("piva", "get", true)
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }
        $query = "UPDATE ts_tmk_pipes SET working = '0', pipe = '5' WHERE piva = '{$pars["piva"]}'";
        $querySblocco = "UPDATE ts_piva_tmp_lock SET `unlock_timestamp`='0', `static_lock`='0', `force`='0' WHERE piva = '{$pars["piva"]}'";
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $link->query($sql);
        $result2 = $link->query($querySblocco);
        if ($result && $result2)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode($link->error);
            return;
        }
    }

    public function ws___scartoScheda() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
        $pars = array(
            "piva" => \WolfMVC\RequestMethods::post("piva", false),
            "esito" => \WolfMVC\RequestMethods::post("esito", false),
            "dettagli" => \WolfMVC\RequestMethods::post("dettagli", false)
        );
        if (!isset($pars["piva"]) || $pars["piva"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid piva par");
            return;
        }
        if (!isset($pars["esito"]) || $pars["esito"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid esito par");
            return;
        }
        if (!isset($pars["dettagli"]) || $pars["dettagli"] === false)
        {
            $pars["dettagli"] = "";
        }
        if ($pars["esito"] == "0" || $pars["esito"] == "1")
        {
            $queryazall = "UPDATE aziende_all SET Esclusione = (Esclusione | 8) WHERE PartitaIva = '{$pars["piva"]}'";
        }
        else if ($pars["esito"] == "2")
        {
            $queryazall = "UPDATE aziende_all SET Esclusione = (Esclusione | 8 | 64) WHERE PartitaIva = '{$pars["piva"]}'";
        }
        $query = "DELETE FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";
        $querySblocco = "UPDATE ts_piva_tmp_lock SET `unlock_timestamp`='0', `static_lock`='0', `force`='0' WHERE piva = '{$pars["piva"]}'";
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $link->query($queryazall);
        if ($result)
        {
            $result2 = $link->query($query);
            $result3 = $link->query($querySblocco);
            if ($result2 && $result3)
            {
                echo json_encode(true);
                return;
            }
            else
            {
                $this->_setResponseStatus(500);
                echo json_encode($link->error);
                return;
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode($link->error);
            return;
        }
    }

    public function ws___getTmkLogDesc() {
        $this->setJsonWS();
        $linkmkt = $this->instantiateMysqli("mkt");
        $linkvt = $this->instantiateMysqli("vtiger");
        $query = "SELECT * FROM tmklog";

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

    public function ws___getInventario() {
        $this->setJsonWS();
        $pars = array(
            "tmk" => WolfMVC\RequestMethods::get("tmk", false)
        );
        if ($pars["tmk"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing tmk value");
            return;
        }
//        ActiveRecord\Table::clear_cache();
//        ActiveRecord\Config::initialize(
//                function($cfg) {
//            $cfg->set_model_directory('../application/configuration/models');
//            $cfg->set_connections_from_system(array("imprese" => "mkt"), "imprese");
//        }
//        );
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $options = array_merge(array('conditions' => "pipe = '6' AND tmklock = '{$pars["tmk"]}'"), array('include' => array('pivalock', 'lead')));
        $leads = \AR\Pipelead::all($options);
        $out = array_map(function($x) {
            $vett = $x->attributes();
            $vett["piva_lock"] = $x->pivalock;
            if ($vett["piva_lock"])
            {
                $vett["piva_lock"] = $vett["piva_lock"]->attributes();
            }
            $vett["lead"] = $x->lead;
            if ($vett["lead"])
            {
                $vett["lead"] = $vett["lead"]->attributes();
            }
            $vett = array_map(function($y) {
                if (is_a($y, "ActiveRecord\DateTime"))
                {
                    return $y->format('d-m-Y H:i:s');
                }
                else
                {
                    return $y;
                }
            }, $vett);
            return $vett;
        }, $leads);
//        echo print_r(\ActiveRecord\Reflections::instance(),true);
        echo json_encode($out);
        return;





        $linkmkt = $this->instantiateMysqli("mkt");

        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }
        $query = "SELECT * FROM ts_tmk_pipes a " .
                "LEFT JOIN ts_piva_tmp_lock b ON (a.piva = b.piva) " .
                "LEFT JOIN aziende_all c ON (a.piva = c.PartitaIva) " .
                "WHERE pipe='6' AND tmklock='{$pars["tmk"]}'";
        if ($linkmkt->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $linkmkt->query($query);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode($linkvt->error . "\nquery was " . $sql);
            return;
        }
    }

    public function ws___getAppuntamenti() {
        $this->setJsonWS();
        $linkmkt = $this->instantiateMysqli("mkt");
        $linkvt = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("commerciali", "post", true),
                    array("data", "post", true)
                )
        );
        foreach ($pars["commerciali"] as $k => $v) {
            $pars["commerciali"][$k] = "'" . $v . "'";
        }

        $commerciali = join(", ", $pars["commerciali"]);
        $query = "SELECT " .
                "UPPER(substring(potcf.cf_647, LOCATE(' ', potcf.cf_647) + 1)) as cognome," .
                "IF(pot.sales_stage = 'Prospecting', IF(pot.nextstep = 'Ripasso venditore', 'ripasso', 'confermato'), 'nonconfermato') as stato," .
                "pot.potentialid, pot.potential_no, pot.potentialname, pot.amount, pot.closingdate, pot.nextstep, pot.sales_stage," .
                "potcf.cf_646 as tmk, acccf.cf_644 as tmk2, actcf.cf_649 as tmk3, potcf.cf_647 as vend, acccf.cf_643 as vend2, actcf.cf_650 as vend3, " .
                "potcf.cf_648, potent.description as potdesc,"
                . "potcf.cf_647 as label," .
                "acc.accountid, acc.accountname, acc.account_no, acc.phone, acccf.cf_641 as piva," .
                "accent.description as accdesc," .
                "act.activityid, act.subject, act.date_start, act.time_start, act.due_date, act.time_end, act.eventstatus, act.location, " .
                "actent.description as actdesc," .
                "(potcf.cf_646 = acccf.cf_644 AND acccf.cf_644 = actcf.cf_649) as check1," .
                "(potcf.cf_647 = acccf.cf_643 AND acccf.cf_643 = actcf.cf_650) as check2," .
                "(UPPER(substring(potcf.cf_647, LOCATE(' ', potcf.cf_647) + 1)) = UPPER(substring(act.subject, 1, LOCATE('-', act.subject)-1 ))) as check3 " .
                "FROM " .
                "vtiger_potential pot " .
                "JOIN vtiger_potentialscf potcf ON (pot.potentialid = potcf.potentialid) " .
                "JOIN vtiger_crmentity potent ON (pot.potentialid = potent.crmid) " .
                "JOIN vtiger_account acc ON (acc.accountid = pot.related_to) " .
                "JOIN vtiger_accountscf acccf ON (acc.accountid = acccf.accountid) " .
                "JOIN vtiger_crmentity accent ON (acc.accountid = accent.crmid) " .
                "JOIN vtiger_seactivityrel sar ON (sar.crmid = accent.crmid) " .
                "JOIN vtiger_activity act ON (sar.activityid = act.activityid) " .
                "JOIN vtiger_activitycf actcf ON (act.activityid = actcf.activityid) " .
                "JOIN vtiger_crmentity actent ON (act.activityid = actent.crmid) " .
                "WHERE " .
                "   pot.potentialname = 'Analisi' " .
                "AND" .
                "   pot.sales_stage <> 'Da Richiamare' " .
                "AND" .
                "   acc.account_type = 'New Business PMI' " .
                "AND" .
                "   pot.closingdate = '{$pars["data"]}' " .
                "AND" .
                "   act.date_start = pot.closingdate " .
                "AND" .
                "   act.eventstatus = 'Planned' " .
                "AND " .
                "   potent.deleted = '0' " .
                "AND" .
                "   accent.deleted = '0' " .
                "AND " .
                "   actent.deleted = '0' " .
                "AND" .
                "   potcf.cf_647 IN ({$commerciali}) " .
                "ORDER BY " .
                "   substring(vend, LOCATE(' ', vend) + 1) ASC, " .
                "   time_start";
        if ($linkvt->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $linkvt->query($query);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode($linkvt->error . "\nquery was " . $sql);
            return;
        }
    }

    public function ws___getAppuntamenti2() {
        $this->setJsonWS();
        $linkmkt = $this->instantiateMysqli("mkt");
        $linkvt = $this->instantiateMysqli("vtiger");

        $pars = $this->retrievePars(
                array(
                    array("data", "get", true)
                )
        );
//        foreach ($pars["commerciali"] as $k => $v) {
//            $pars["commerciali"][$k] = "'" . $v . "'";
//        }
//        $commerciali = join(", ", $pars["commerciali"]);
        $query = "SELECT " .
                "UPPER(substring(potcf.cf_647, LOCATE(' ', potcf.cf_647) + 1)) as cognome," .
                "IF(pot.sales_stage = 'Prospecting', IF(pot.nextstep = 'Ripasso venditore', 'ripasso', 'confermato'), IF(potcf.cf_656 = 'Da confermare','daconfermare','nonconfermato')) as stato," .
                "pot.potentialid, pot.potential_no, pot.potentialname, pot.amount, pot.closingdate, pot.nextstep, pot.sales_stage," .
                "potcf.cf_646 as tmk, acccf.cf_644 as tmk2, actcf.cf_649 as tmk3, potcf.cf_647 as vend, acccf.cf_643 as vend2, actcf.cf_650 as vend3, " .
                "potcf.cf_648, potent.description as potdesc," .
                "potcf.cf_647 as label," .
                "acc.accountid, acc.accountname, acc.account_no, acc.phone, acccf.cf_641 as piva, acc.employees as dipendenti, acc.annualrevenue as fatturato, " .
                "accent.description as accdesc," .
                "act.activityid, act.subject, act.date_start, act.time_start, act.due_date, act.time_end, act.eventstatus, act.location, " .
                "actent.description as actdesc, " .
                "con.firstname as con_fn, con.lastname as con_ln, con.title as con_role, " .
                "con.phone as con_phone, con.mobile as con_mobile, con.email as con_email, con.fax as con_fax, " .
                "actcf.cf_652 as googleId, " .
                "actcf.cf_702 as pot, " .
                "bill_city, bill_code, bill_country, bill_state, bill_street, " .
                "(potcf.cf_646 = acccf.cf_644 AND acccf.cf_644 = actcf.cf_649) as check1," .
                "(potcf.cf_647 = acccf.cf_643 AND acccf.cf_643 = actcf.cf_650) as check2," .
                "(UPPER(substring(potcf.cf_647, LOCATE(' ', potcf.cf_647) + 1)) = UPPER(substring(act.subject, 1, LOCATE('-', act.subject)-1 ))) as check3 " .
                "FROM " .
                "vtiger_potential pot " .
                "JOIN vtiger_potentialscf potcf ON (pot.potentialid = potcf.potentialid) " .
                "JOIN vtiger_crmentity potent ON (pot.potentialid = potent.crmid) " .
                "JOIN vtiger_account acc ON (acc.accountid = pot.related_to) " .
                "JOIN vtiger_accountscf acccf ON (acc.accountid = acccf.accountid) " .
                "JOIN vtiger_accountbillads accba ON (accba.accountaddressid = acc.accountid) " .
                "JOIN vtiger_crmentity accent ON (acc.accountid = accent.crmid) " .
                "JOIN vtiger_seactivityrel sar ON (sar.crmid = accent.crmid) " .
                "JOIN vtiger_activity act ON (sar.activityid = act.activityid) " .
                "JOIN vtiger_activitycf actcf ON (act.activityid = actcf.activityid) " .
                "JOIN vtiger_crmentity actent ON (act.activityid = actent.crmid) " .
                "LEFT JOIN vtiger_cntactivityrel car ON (car.activityid = act.activityid) " .
                "LEFT JOIN vtiger_contactdetails con ON (con.contactid = car.contactid) " .
                "WHERE " .
                "   pot.potentialname = 'Analisi' " .
                "AND" .
                "   pot.sales_stage <> 'Da Richiamare' " .
                "AND" .
                "   acc.account_type = 'New Business PMI' " .
                "AND" .
                "   pot.closingdate = '{$pars["data"]}' " .
                "AND" .
                "   act.date_start = pot.closingdate " .
                "AND" .
                "   act.eventstatus = 'Planned' " .
                "AND " .
                "   potent.deleted = '0' " .
                "AND" .
                "   accent.deleted = '0' " .
                "AND " .
                "   actent.deleted = '0' " .
//                "AND" .
//                "   potcf.cf_647 IN ({$commerciali}) " .
                "ORDER BY " .
                "   substring(vend, LOCATE(' ', vend) + 1) ASC, " .
                "   time_start";
        if ($linkvt->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $linkvt->query($query);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode($linkvt->error . "\nquery was " . $sql);
            return;
        }
    }

    public function ws___contactCorrection() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("activityid", "get", true),
                    array("accountid", "get", true)
                )
        );
        $vtws = \WolfMVC\Registry::get("VTWS");
        $query = "SELECT * FROM Contacts WHERE account_id = '11x{$pars["accountid"]}';";
        $result = $vtws->doQuery($query);
        if (!count($result))
        {
            $this->_setResponseStatus(404);
            echo json_encode(false);
            return;
        }
        else
        {
            $conId = $result[0]["id"];
            $act = $vtws->doRetrieve("18x" . $pars["activityid"]);
            if ($act)
            {
                $act["contact_id"] = $conId;
                $res = $vtws->doUpdate($act);
                echo json_encode($res);
                return;
            }
            return;
        }
    }

    public function ws___activityCorrection() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("activityid", "get", true),
                    array("newtitle", "get", true)
                )
        );
        $vtws = \WolfMVC\Registry::get("VTWS");
        $act = $vtws->doRetrieve("18x" . $pars["activityid"]);
        $act["subject"] = $this->_ammazzaSlash($pars["newtitle"]);
        $acc = $vtws->doRetrieve($act["parent_id"]);
        if (!$acc)
        {
            $this->_setResponseStatus(500);
            echo json_encode(array("Cant find account"));
        }
        $pot = $vtws->doQuery("SELECT * FROM Potentials WHERE related_to = '{$acc["id"]}';");
        if (!$pot)
        {
            $this->_setResponseStatus(500);
            echo json_encode(array("Cant find potential"));
        }
        $newpot = array();
        foreach ($pot as $k => $p) {
            if ($p["sales_stage"] === "Prospecting" || $p["sales_stage"] === "Qualification" || $p["sales_stage"] === "Da Richiamare")
                $newpot[] = $p;
        }
        if (count($newpot) !== 1 || $newpot[0]["sales_stage"] === "Da Richiamare")
        {
            $this->_setResponseStatus(500);
            echo json_encode("Errore POT");
            return;
        }
        $pot = $newpot[0];
        if ($act["cf_702"] === "")
        {
            $act["cf_702"] = str_ireplace("13x", "", $pot["id"]);
        }
        if ($act["cf_649"] === "")
        {
            $act["cf_649"] = $pot["cf_646"];
        }
        if ($act["cf_650"] === "")
        {
            $act["cf_650"] = $pot["cf_647"];
        }
        $res = $vtws->doUpdate($act);
        if ($res)
        {
//            echo json_encode(array($res, $acc, $pot));
            echo json_encode($res);
            return;
        }
        $this->_setResponseStatus(500);
        echo json_encode($vtws->lastError());
        return;
    }

    private function _ammazzaSlash($input) {
        $shouldBe = $input;
        if (strpos($shouldBe, "\\'") !== FALSE)
        {
            $shouldBe = str_ireplace("A\\\\' ", "A'", $shouldBe);
            $shouldBe = str_ireplace("E\\\\' ", "E'", $shouldBe);
            $shouldBe = str_ireplace("I\\\\' ", "I'", $shouldBe);
            $shouldBe = str_ireplace("O\\\\' ", "O'", $shouldBe);
            $shouldBe = str_ireplace("U\\\\' ", "U'", $shouldBe);
            $shouldBe = str_ireplace("a\\\\' ", "à", $shouldBe);
            $shouldBe = str_ireplace("e\\\\' ", "è", $shouldBe);
            $shouldBe = str_ireplace("i\\\\' ", "ì", $shouldBe);
            $shouldBe = str_ireplace("o\\\\' ", "ò", $shouldBe);
            $shouldBe = str_ireplace("u\\\\' ", "ù", $shouldBe);
        }
        if (strpos($shouldBe, "\\") !== FALSE)
        {
            $shouldBe = str_ireplace("\\", "", $shouldBe);
        }
        return $shouldBe;
    }

    public function ws___accountCorrection() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("accountid", "get", true)
                )
        );
        $vtws = \WolfMVC\Registry::get("VTWS");
        $acc = $vtws->doRetrieve("11x" . $pars["accountid"]);
        $acc["accountname"] = $this->_ammazzaSlash($acc["accountname"]);
        $acc["bill_street"] = $this->_ammazzaSlash($acc["bill_street"]);
        $acc["ship_street"] = $this->_ammazzaSlash($acc["ship_street"]);
        $acc["bill_city"] = $this->_ammazzaSlash($acc["bill_city"]);
        $acc["ship_city"] = $this->_ammazzaSlash($acc["ship_city"]);
        $acc["cf_703"] = $this->_ammazzaSlash($acc["cf_703"]);
        $acc["cf_704"] = $this->_ammazzaSlash($acc["cf_704"]);
        $acc["cf_705"] = $this->_ammazzaSlash($acc["cf_705"]);

        $res = $vtws->doUpdate($acc);
        if ($res)
        {
            echo json_encode($res);
            return;
        }
        echo json_encode($vtws->lastError());
        return;
    }

    public function ws___locationCorrection() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("activityid", "get", true)
                )
        );
        $vtws = \WolfMVC\Registry::get("VTWS");
        $act = $vtws->doRetrieve("18x" . $pars["activityid"]);
        if (!$act)
        {
            echo json_encode($vtws->lastError());
        }
        $acc = $vtws->doRetrieve($act["parent_id"]);
        if (!$acc)
        {
            echo json_encode($vtws->lastError());
        }
        $shouldBe = trim($acc["bill_street"] . " " . $acc["bill_code"] . " " . $acc["bill_city"] . " " . $acc["bill_country"]);
        if (strpos($shouldBe, "\\'") !== FALSE)
        {
            $shouldBe = str_ireplace("A\\\\'", "A'", $shouldBe);
            $shouldBe = str_ireplace("E\\\\'", "E'", $shouldBe);
            $shouldBe = str_ireplace("I\\\\'", "I'", $shouldBe);
            $shouldBe = str_ireplace("O\\\\'", "O'", $shouldBe);
            $shouldBe = str_ireplace("U\\\\'", "U'", $shouldBe);
        }
        if (strpos($shouldBe, "\\") !== FALSE)
        {
            $shouldBe = str_ireplace("\\", "", $shouldBe);
        }
        $act["location"] = $shouldBe;
        $res = $vtws->doUpdate($act);
        if ($res)
        {
            echo json_encode($res);
            return;
        }
        echo json_encode($vtws->lastError());
        return;
    }

    public function ws___putGId() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("activityid", "get", true),
                    array("googleid", "get", true),
                    array("pot", "get", true),
                    array("vend", "get", true),
                    array("tmk", "get", true)
                )
        );
        $vtws = \WolfMVC\Registry::get("VTWS");
        $act = $vtws->doRetrieve("18x" . $pars["activityid"]);
        if (!$act)
        {
            $this->_setResponseStatus(404);
            echo json_encode($vtws->lastError());
        }
        $act["cf_652"] = $pars["googleid"];
        $act["cf_650"] = $pars["vend"];
        $act["cf_649"] = $pars["tmk"];
        $act["cf_702"] = $pars["pot"];
        $res = $vtws->doUpdate($act);
        if ($res)
        {
            echo json_encode($res);
            return;
        }
        $ret = new stdClass();
        $ret->error = $vtws->lastError();
        $ret->pippo = $act;
        $ret->pippo2 = $pars["activityid"];
        echo json_encode($ret);
        return;
    }

    public function ws___creaRichiamo() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("piva", "post", true),
                    array("donotcallbefore", "post", true),
                    array("note", "post", false),
                    array("self", "post", false),
                )
        );
        $pars["donotcallbefore"] = (int) $pars["donotcallbefore"];
        $pars["donotcallbefore"] = floor($pars["donotcallbefore"] / 1000);
        $time = new DateTime("@" . $pars["donotcallbefore"]);
        $donotcallbefore = $time->format("Y-m-d H:i:s");
        if (!isset($pars["note"]))
            $pars["note"] = "";
        if (!isset($pars["self"]))
            $pars["self"] = "false";
        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }

        if ($pars["self"] == "false")
        {
            $query1 = "UPDATE ts_tmk_pipes SET tmklock = '0', working = '0', donotcallbefore = '{$donotcallbefore}' WHERE piva = '{$pars["piva"]}'";
            $query2 = "SELECT * FROM ts_piva_tmp_lock WHERE piva = '{$pars["piva"]}'";
            $query3 = "UPDATE ts_piva_tmp_lock SET `unlock_timestamp` = '{$donotcallbefore}', `force`='0' WHERE piva = '{$pars["piva"]}'";
            $query4 = "INSERT INTO ts_piva_tmp_lock (`piva`, `unlock_timestamp`,`force`) VALUES ('{$pars["piva"]}','{$donotcallbefore}','0')";
            if ($link->connect_errno)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred in db connection!");
                return;
            }
            $result1 = $link->query($query1);
            if (!$result1)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred in unlocking lead: " . $link->error);
                return;
            }
            $result2 = $link->query($query2);
            if (!$result2)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred searching lead in tmp_lock: " . $link->error);
                return;
            }
            if ($result2->num_rows)
            {
                $result3 = $link->query($query3);
                if (!$result3)
                {
                    $this->_setResponseStatus(500);
                    echo json_encode("Error occurred updating lead in tmp_lock: " . $link->error);
                    return;
                }
            }
            else
            {
                $result4 = $link->query($query4);
                if (!$result4)
                {
                    $this->_setResponseStatus(500);
                    echo json_encode("Error occurred inserting lead in tmp_lock: " . $link->error);
                    return;
                }
            }
            echo json_encode(true);
            return;
        }
        else
        {
            $query1 = "UPDATE ts_tmk_pipes SET pipe = '6', working = '0', donotcallbefore = '{$donotcallbefore}' WHERE piva = '{$pars["piva"]}'";
            $result1 = $link->query($query1);
            if (!$result1)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred putting lead in personal pipe: " . $link->error);
                return;
            }
        }

        echo json_encode(true);
        return;
    }

    public function ws___creaRichiamoRapido() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("piva", "post", true),
                    array("donotcallbefore", "post", true),
                    array("note", "post", false)
                )
        );
        $pars["donotcallbefore"] = (int) $pars["donotcallbefore"];
        $pars["donotcallbefore"] = floor($pars["donotcallbefore"] / 1000);
        $time = new DateTime("@" . $pars["donotcallbefore"]);
        $donotcallbefore = $time->format("Y-m-d H:i:s");
        if (!isset($pars["note"]))
            $pars["note"] = "";

        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }
        $query1 = "UPDATE ts_tmk_pipes SET tmklock = '0', working = '0', donotcallbefore = '{$donotcallbefore}' WHERE piva = '{$pars["piva"]}'";
        $query2 = "SELECT * FROM ts_piva_tmp_lock WHERE piva = '{$pars["piva"]}'";
        $query3 = "UPDATE ts_piva_tmp_lock SET `unlock_timestamp` = '{$donotcallbefore}', `force`='0' WHERE piva = '{$pars["piva"]}'";
        $query4 = "INSERT INTO ts_piva_tmp_lock (`piva`, `unlock_timestamp`,`force`) VALUES ('{$pars["piva"]}','{$donotcallbefore}','0')";

        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result1 = $link->query($query1);
        if (!$result1)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in unlocking lead: " . $link->error);
            return;
        }
        $result2 = $link->query($query2);
        if (!$result2)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred searching lead in tmp_lock: " . $link->error);
            return;
        }
        if ($result2->num_rows)
        {
            $result3 = $link->query($query3);
            if (!$result3)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred updating lead in tmp_lock: " . $link->error);
                return;
            }
        }
        else
        {
            $result4 = $link->query($query4);
            if (!$result4)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred inserting lead in tmp_lock: " . $link->error);
                return;
            }
        }
        echo json_encode(true);
        return;
    }

    public function ws___esponiLista() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $link2 = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("tmk", "post", true),
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            echo json_encode($pars);
            return;
        }
        $query = "UPDATE ts_tmk_pipes SET ";
        if (!(bool) $pars["self"])
        {
            $query .= "tmklock = '0', ";
        }
        $query .= "working = '0', pipe = '2', ";
        $query .= "donotcallbefore = '{$pars["donotcallbefore"]}'";
        $query .= "WHERE piva = '{$pars["piva"]}'";

        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $query;

        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            echo json_encode(false);
            return;
        }
    }

    public function ws___creaVTAccount() {
        $this->setJsonWS();
        $vtws = \WolfMVC\Registry::get("VTWS");
        $pars = $this->retrievePars(
                array(
                    array("vm", "post", true),
                    array("piva", "post", true)
                )
        );

        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }
        $fieldTrans = array(
            "accountname" => "RagioneSociale",
            "cf_641" => "PartitaIva",
            "accounttype" => "accounttype", //statico
            "bill_street" => "Indirizzo",
            "bill_city" => "Localita",
            "bill_code" => "Cap",
            "bill_state" => "Provincia",
            "bill_country" => "Regione",
            "ship_street" => "Indirizzo",
            "ship_city" => "Localita",
            "ship_code" => "Cap",
            "ship_state" => "Provincia",
            "ship_country" => "Regione",
            "email1" => "Email",
            "website" => "Sito",
            "cf_643" => "Venditore", //inserire
            "cf_644" => "Telemarketing", //inserire
            "cf_fatturato" => "FasciaFatturato",
            "cf_formagiuridica" => "FormaGiuridica",
            "cf_classedipendenti" => "ClasseDipendenti",
            "cf_industry" => "AttivitaAteco",
            "assigned_user_id" => "assigned_user_id", //statico
            "phone" => "Telefono", //inserire
            "cf_703" => "cf_703", //inserire
            "cf_704" => "cf_704", //inserire
            "cf_705" => "cf_705" //inserire
        );
//        print_r($vtws->doDescribe("Accounts"));
//        return;
        $precedentiAccount = $vtws->doQuery("SELECT * FROM Accounts WHERE cf_641 = '{$pars['piva']}'");
        if (count($precedentiAccount))
        { // c'è già
            $edit = $precedentiAccount[0];
            $edit["cf_643"] = $pars["vm"]["Venditore"];
            $edit["cf_644"] = $pars["vm"]["Telemarketing"];
            $edit = $vtws->doUpdate($edit);
            $ret = $edit;
            echo json_encode($ret);
            return;
        }
        else
        { //creo
            $vm = array();
            foreach ($fieldTrans as $k => $v) {
                $vm[$k] = $pars["vm"][$v];
            }
            $new = $vtws->doCreate("Accounts", $vm);
            if ($new)
            {
                echo json_encode($new);
                return;
            }
            else
            {
                $this->_setResponseStatus(500);
                echo json_encode(array(
                    "error" => true,
                    "error_desc" => $vtws->lastError()
                ));
            }
        }
        return;
    }

    public function ws___creaVTContact() {
        $this->setJsonWS();
        $vtws = \WolfMVC\Registry::get("VTWS");
        $pars = $this->retrievePars(
                array(
                    array("vm", "post", true)
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }
        $fieldTrans = array
            (
            "lastname" => "lastname",
            "firstname" => "firstname",
            "title" => "title",
            "email" => "email",
            "mobile" => "mobile", //New Business PMI,
            "fax" => "fax", // Visita venditore
            "phone" => "phone", //DB Marketing
            "mailingstreet" => "mailingstreet", //Qualification
            "account_id" => "account_id"
        );
        $vm = array();
        foreach ($fieldTrans as $k => $v) {
            $vm[$k] = $pars["vm"][$v];
        }
        $prev = $vtws->doQuery("SELECT * FROM Contacts WHERE lastname = '{$vm["lastname"]}' AND firstname = '{$vm["firstname"]}' AND account_id = '{$vm["account_id"]}'");
        if (isset($prev[0]) && is_array($prev[0]) && !empty($prev[0]))
        {

            echo json_encode($prev[0]);
            return;
        }
        $new = $vtws->doCreate("Contacts", $vm);
        if ($new)
        {
            echo json_encode($new);
            return;
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode(array(
                "error" => true,
                "error_desc" => $vtws->lastError()
            ));
        }
    }

    public function ws___creaVTPotential() {
        $this->setJsonWS();
        $vtws = \WolfMVC\Registry::get("VTWS");
         $pars = array(
            "vm" => \WolfMVC\RequestMethods::post("vm", false),
            "overwrite" => \WolfMVC\RequestMethods::post("overwrite", false)
        );
        if (!isset($pars["vm"]) && $pars["vm"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid vm");
            return;
        }
        if (!isset($pars["overwrite"]) && $pars["overwrite"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid overwrite");
            return;
        }
        $fieldTrans = array
            (
            "potentialname" => "Potentialname",
            "amount" => "Ammontare",
            "related_to" => "RelatedTo",
            "closingdate" => "Date",
            "opportunity_type" => "OpportunityType", //New Business PMI,
            "nextstep" => "NextStep", // Visita venditore
            "leadsource" => "LeadSource", //DB Marketing
            "sales_stage" => "SalesStage", //Qualification
            "assigned_user_id" => "assigned_user_id",
            "probability" => "Probability", //10.000
            "description" => "Description",
            "cf_645" => "AmmontarePesato", //prob*ammontare
            "cf_646" => "Telemarketing",
            "cf_647" => "Venditore"
        );
//        print_r($vtws->doDescribe("Accounts"));
//        return;
        $vm = array();

        foreach ($fieldTrans as $k => $v) {
            $vm[$k] = $pars["vm"][$v];
        }
        if ($pars["overwrite"] == -1 || $pars["overwrite"] == "-1")
        {
            $prev = $vtws->doQuery("SELECT * FROM Potentials WHERE related_to = '{$vm["related_to"]}' AND potentialname = 'Analisi' AND sales_stage IN ('Prospecting', 'Qualification');");
            if (isset($prev[0]) && is_array($prev[0]) && !empty($prev[0]))
            {
                $potid = $prev[0]["id"];
                $potid = str_ireplace("13x", "", $potid);
                $prevact = $vtws->doQuery("SELECT * FROM Events WHERE cf_702 = '{$potid}';");
                $ret = array("pot" => $prev[0]);
                if (isset($prevact[0]) && is_array($prevact[0]) && !empty($prevact[0]))
                {
                    $ret["act"] = $prevact[0];
                }
                $this->_setResponseStatus(409);
                echo json_encode($ret);
                return;
            }
        }
        else if ($pars["overwrite"] == 1 || $pars["overwrite"] == "1")
        {
            $prev = $vtws->doQuery("SELECT * FROM Potentials WHERE related_to = '{$vm["related_to"]}' AND potentialname = 'Analisi' AND sales_stage IN ('Prospecting', 'Qualification');");
            usleep(100000);
            if (isset($prev[0]) && is_array($prev[0]) && !empty($prev[0]))
            {
                foreach ($prev as $k => $v) {
                    usleep(500000);
                    $potid = $v["id"];
                    $potid = str_ireplace("13x", "", $potid);
                    $prevact = $vtws->doQuery("SELECT * FROM Events WHERE cf_702 = '{$potid}';");
                    if (isset($prevact[0]) && is_array($prevact[0]) && !empty($prevact[0]))
                    {
                        foreach ($prevact as $t => $w) {
                            usleep(500000);
                            $w["eventstatus"] = "Held";
                            $vtws->doUpdate($w);
                        }
                    }
                    $v["sales_stage"] = "Closed Lost";
                    $v["nextstep"] = "Chiuso per sostituzione";
                    $vtws->doUpdate($v);
                }
            }
        }
        usleep(500000);
        $new = $vtws->doCreate("Potentials", $vm);
        if ($new)
        {
            echo json_encode($new);
            return;
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode(array(
                "error" => true,
                "error_desc" => $vtws->lastError()
            ));
        }
    }

    public function ws___creaVTActivity() {
        $this->setJsonWS();
        $vtws = \WolfMVC\Registry::get("VTWS");
        $pars = $this->retrievePars(
                array(
                    array("vm", "post", true)
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            $this->_setResponseStatus(500);
            echo json_encode($pars);
            return;
        }
        $fieldTrans = array
            (
            "activitytype" => "activitytype",
            "subject" => "subject",
            "eventstatus" => "eventstatus",
            "date_start" => "date_start",
            "due_date" => "due_date", //New Business PMI,
            "time_start" => "time_start", // Visita venditore
            "time_end" => "time_end", //DB Marketing
            "duration_hours" => "duration_hours", //Qualification
            "location" => "location",
            "description" => "description", //10.000
            "cf_650" => "cf_650",
            "cf_649" => "cf_649", //prob*ammontare
            "cf_651" => "cf_651",
            "cf_652" => "cf_652",
            "cf_702" => "cf_702",
            "parent_id" => "parent_id",
            "contact_id" => "contact_id"
        );
//        print_r($vtws->doDescribe("Accounts"));
//        return;
        $vm = array();
        foreach ($fieldTrans as $k => $v) {
            $vm[$k] = $pars["vm"][$v];
        }
        $new = $vtws->doCreate("Events", $vm);
        if ($new)
        {
            echo json_encode($new);
            return;
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode(array(
                "error" => true,
                "error_desc" => $vtws->lastError()
            ));
        }
    }

    public function report() {
        $view = $this->getActionView();
        $view->set("actions", array(
            "Gestione telefonata" => $this->pathTo($this->nameofthiscontroller(), "gestionechiamate", array()),
            "Report" => $this->pathTo($this->nameofthiscontroller(), "report", array())
        ));
    }

    public function ws___data() {
        $this->usePageComp("0.1");
        parent::ws___data();
    }

}
