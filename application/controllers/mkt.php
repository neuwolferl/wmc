<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Mkt extends Controller {

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

        $view->set("moduleName", "MARKETING");
    }

    public function index() {
        $view = $this->getActionView();
        $view->set("action", array(
            "Selezione zone" => $this->pathTo($this->nameofthiscontroller(), "selezionezone", array(1)),
            "Copertura lavoro" => $this->pathTo($this->nameofthiscontroller(), "jobcoverage"),
            "Situazione liste" => $this->pathTo($this->nameofthiscontroller(), "situazioneliste"),
            "Situazione appuntamenti" => $this->pathTo($this->nameofthiscontroller(), "situazioneappuntamenti"),
            "Inserimento lead" => $this->pathTo($this->nameofthiscontroller(), "inserimentoLead")
        ));
    }

    public function codemkt() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
//        $this->loadScript("ngsortable.js");
        $this->loadScript("core/themask.js");
//        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("core/tsMkt/tsMkt-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $view = $this->getActionView();
        $view->set("root", '$root');
    }

    public function inserimentoLead() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("angular-google-maps.min.js");
        $this->loadScript("core/tsMkt/tsMkt-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("core/datamodule/data-0.6.js");
        $this->loadScript("core/themask/themask-0.3.js");
        $this->loadScript("core/smartButton/smartButton-0.1-min.js");
        $this->loadScript("component/tsnwinclude/tsnwinclude_stdlib.js");
        $this->loadScript("daniele.css");
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Inserimento lead" => "last")));
        $view = $this->getActionView();
        $view->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Inserimento lead" => "last")));
        $view->set("root", '$root');
        $view->set("error", '$error');
        $view->set("valid", '$valid');
        $view->set("invalid", '$invalid');
        $view->set("pristine", '$pristine');
    }

    public function ws___inserimentoLead() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "searchLeadByPiva" => SITE_PATH . "mkt/searchLeadByPiva.ws",
                "setEsclusioneBit" => SITE_PATH . "mkt/setEsclusioneBit.ws",
                "getLocalita" => SITE_PATH . "mkt/getLocalita.ws",
                "getAteco" => SITE_PATH . "mkt/getAteco.ws",
                "createLead" => SITE_PATH . "mkt/createLead.ws",
                "sendReportMail" => SITE_PATH . "mkt/sendReportMail.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function jobcoverage() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/themask.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("core/tsMkt/tsMkt-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Job Coverage" => "last")));
        $view = $this->getActionView();
        $view->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Copertura lavoro" => "last")));
        $view->set("root", '$root');
    }

    public function situazioneliste() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("angular-google-maps.min.js");
        $this->loadScript("core/tsMkt/tsMkt-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $this->loadScript("core/themask/themask-0.3.js");
        $this->loadScript("core/smartButton/smartButton-0.1-min.js");
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Situazione liste" => "last")));
        $view = $this->getActionView();
        $view->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Situazione liste" => "last")));
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function situazioneappuntamenti() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $this->loadScript("core/smartButton/smartButton-0.1-min.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.core.js");
        $this->loadScript("core/RGraph/libraries/RGraph.gantt.js");
        $this->loadScript("core/RGraph/libraries/RGraph.common.dynamic.js");
        $this->loadScript("component/sinottico/sinottico-0.2.js");
        $this->loadScript("core/logger/logger-0.1.js");
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Situazione appuntamenti" => "last")));
        $view = $this->getActionView();
        $view->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Situazione appuntamenti" => "last")));
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function selezionezone() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("angular-touch.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("ngsortable.js");
        $this->loadScript("core/themask.js");
        $this->loadScript("core/angdad.js");
        $this->loadScript("lodash.js");
        $this->loadScript("angular-google-maps.min.js");
        $this->loadScript("core/tsMkt/tsMkt-0.1.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("core/datamodule/data-0.5.js");
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"https://maps.googleapis.com/maps/api/js?libraries=drawing&v=3.17\"></script>";
//        $this->_system_js_including .="<script type=\"text/javascript\" src=\"https://maps.googleapis.com/maps/api/js?libraries=drawing&v=3.17&key=AIzaSyAN6gZPMrAStrYRHahyoRFacAOF6uRo_SI\"></script>";
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Selezione zone" => "last")));
        $view = $this->getActionView();
        $view->set("breadCrumb", $this->breadCrumb(array("MARKETING" => "mkt", "Selezione zone" => "last")));
        $view->set("root", '$root');
        $view->set("codemkt", $this->pathTo($this->nameofthiscontroller(), 'codemkt'));
    }

    public function ws___selezionezone() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getLocalitaPerProvincia" => SITE_PATH . "mkt/getLocalitaPerProvincia.ws",
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "pipeCommand" => SITE_PATH . "mkt/pipeCommand.ws",
                "getPipeStatus" => SITE_PATH . "mkt/getPipeStatus.ws",
                "getCampaign" => SITE_PATH . "mkt/getCampaign.ws",
                "newLot" => SITE_PATH . "mkt/newLot.ws",
                "emptyLot" => SITE_PATH . "mkt/emptyLot.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___situazioneappuntamenti() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getLog" => SITE_PATH . "mkt/getLog.ws",
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "getAppuntamenti2" => SITE_PATH . "mkt/getAppuntamenti2.ws",
                "ComAppStatus" => SITE_PATH . "vtiger/ComAppStatus.ws",
                "CambiaCommerciale" => SITE_PATH . "mkt/CambiaCommerciale.ws",
                "cambiaDataOra" => SITE_PATH . "mkt/cambiaDataOra.ws",
                "sendComMail" => SITE_PATH . "mkt/sendComMail.ws",
                "getTmkLogDesc" => SITE_PATH . "tmk/getTmkLogDesc.ws",
                "sendReportMail" => SITE_PATH . "mkt/sendReportMail.ws"
//                "getCampaign" => SITE_PATH . "mkt/getCampaign.ws",
//                "newLot" => SITE_PATH . "mkt/newLot.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___codemkt() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getCampaigns" => SITE_PATH . "mkt/getCampaigns.ws",
                "newCamp" => SITE_PATH . "mkt/newCamp.ws",
                "closeCamp" => SITE_PATH . "mkt/closeCamp.ws",
                "suspendCamp" => SITE_PATH . "mkt/suspendCamp.ws",
                "restoreCamp" => SITE_PATH . "mkt/restoreCamp.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___situazioneliste() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getCampaigns" => SITE_PATH . "mkt/getCampaigns.ws",
                "getCampaign" => SITE_PATH . "mkt/getCampaign.ws",
                "listaPerTelemarketing" => SITE_PATH . "mkt/listaPerTelemarketing.ws",
                "listaPersonaliTelemarketing" => SITE_PATH . "mkt/listaPersonaliTelemarketing.ws",
                "listaRifissiTelemarketing" => SITE_PATH . "mkt/listaRifissiTelemarketing.ws",
                "getTelemarketing" => SITE_PATH . "mkt/getTelemarketing.ws",
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "getJobCoverage" => SITE_PATH . "mkt/getJobCoverage.ws",
                "configuraGestChiam" => SITE_PATH . "tmk/configuraGestChiam.ws",
                "cercaLead" => SITE_PATH . "mkt/cercaLead.ws",
                "mktLeadChangeStatus" => SITE_PATH . "mkt/mktLeadChangeStatus.ws",
                "mktLeadDisable" => SITE_PATH . "mkt/mktLeadDisable.ws",
                "mktLeadForce" => SITE_PATH . "mkt/mktLeadForce.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___jobCoverage() {
        $this->setJsonWS();
        $config = array(
            "services" => array(
                "getCampaigns" => SITE_PATH . "mkt/getCampaigns.ws",
                "getCampaign" => SITE_PATH . "mkt/getCampaign.ws",
                "getCommerciali" => SITE_PATH . "mkt/getCommerciali.ws",
                "getTelemarketing" => SITE_PATH . "mkt/getTelemarketing.ws",
                "getJobCoverage" => SITE_PATH . "mkt/getJobCoverage.ws",
                "getPipeStatus" => SITE_PATH . "mkt/getPipeStatus.ws",
                "getWorkerAssignment" => SITE_PATH . "mkt/getWorkerAssignment.ws",
                "setTmkVendAssociation" => SITE_PATH . "mkt/setTmkVendAssociation.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public static function getLocalitaPerProvincia($provincia) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $conditions = array(
                "conditions" => array("provincia = ?", $provincia),
            );
            $com = \AR\Comunegeocap::all($conditions);
            $out = array_map(function($x) {
                $out = array();
                $out["loc"] = $x->localita;
                $out["PROV"] = $x->provincia;
                $out["CAP"] = $x->cap;
                $out["LAT"] = $x->lat;
                $out["LNG"] = $x->lng;

                $out["callable"] = \AR\Lead::count(array("conditions" => array("esclusione = '0' AND localita = ? AND provincia = ?", $x->localita, $x->provincia)));
                $out["excluded"] = \AR\Lead::count(array("conditions" => array("(esclusione & (128+64+32+16)) <> '0' AND localita = ? AND provincia = ?", $x->localita, $x->provincia)));
                $out["count"] = \AR\Lead::count(array("conditions" => array("localita = ? AND provincia = ?", $x->localita, $x->provincia)));
                return $out;
            }, $com);
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(false, 404, $e->getMessage());
        } catch (\ActiveRecord\ActiveRecordException $e) {
            return array(false, 500, $e->getMessage());
        } catch (\Exception $e) {
            return array(false, 500, $e->getMessage());
        }
        return array($out, 200, true);
    }

    public function ws___getLocalitaPerProvincia() {
        $this->setJsonWS();
        $provincia = WolfMVC\RequestMethods::get("provincia", false);
        if (!isset($provincia) || $provincia === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or wrong parameter 'provincia'");
            return;
        }

        $res = self::getLocalitaPerProvincia($provincia);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;

//        $mainquery = "SELECT b.localita as loc, b.provincia as PROV, (count(a.PartitaIva)) as count, "
//                . "IFNULL(convert((count(a.PartitaIva)) - (sum(a.Esclusione/a.Esclusione)),unsigned), 0) as callable, "
//                . "b.cap as CAP, b.lat as LAT, b.lng as LNG " .
//                "FROM comuni_geo_cap b "
//                . "LEFT JOIN aziende_all a ON (a.Localita = b.localita AND a.Provincia = b.provincia)" .
//                "WHERE b.provincia = '" . $provincia . "' " .
//                "GROUP BY b.localita";
////        $mainquery = "SELECT a.Localita as loc, a.Provincia as PROV, count(a.PartitaIva) as count, b.cap as CAP, b.lat as LAT, b.lng as LNG " .
////                "FROM aziende_all a " .
////                "LEFT JOIN comuni_geo_cap b ON (a.Localita = b.localita AND a.Provincia = b.provincia) " .
////                "WHERE a.Provincia = '" . $provincia . "' " .
////                "GROUP BY a.Localita";
//        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";
//        $limitquery = $mainquery . " LIMIT %s,%s";
//        if ($link->connect_errno)
//        {
//            echo json_encode("Error occurred in db connection!");
//            return;
//        }
//        $sql = $mainquery;
//        $result = $link->query($sql);
//        if ($result)
//        {
//            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
//            return;
//        }
//        else
//        {
//            echo json_encode("An error occurred retrieving collections: " . $link->error);
//            return;
//        }
    }

    public function ws___pipeCommand() {
        $this->setJsonWS();
//        $headers = getallheaders();
//        $db = \WolfMVC\Registry::get("database_mkt");
//        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
//        $link->set_charset("utf8");
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $loc = WolfMVC\RequestMethods::post("loc", false);
        $lot = WolfMVC\RequestMethods::post("lot", false);
        $amount = WolfMVC\RequestMethods::post("amount", false);
        $vend = WolfMVC\RequestMethods::post("vend", false);

        if (!isset($loc) || $loc === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode(array(false, "Missing or invalid loc"));
            return;
        }
        if (!isset($lot) || $lot === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode(array(false, "Missing or invalid lot"));
            return;
        }
        if (!isset($amount) || $amount === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode(array(false, "Missing or invalid amount"));
            return;
        }
        if (!isset($vend) || $vend === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode(array(false, "Missing or invalid vend"));
            return;
        }
        try {
            AR\Pipeleadbulk::create(array(
                "lotto" => $lot,
                "localita" => $loc,
                "venditore" => $vend,
                "ammontare" => $amount,
                "tubo" => '1',
            ));
        } catch (\ActiveRecord\ActiveRecordException $e) {
            $this->_setResponseStatus(500);
            echo json_encode(array(false, $e->getMessage()));
            return;
        } catch (\Exception $e) {
            $this->_setResponseStatus(500);
            echo json_encode(array(false, $e->getMessage()));
            return;
        }
        try {
            $res = AR\Lead::all(array(
                        "conditions" => array("b.worker = ? AND b.pipe = '1' AND b.lot = ?", $vend, $lot),
                        "group" => "localita",
                        "joins" => "JOIN ts_tmk_pipes b ON (b.piva = partitaiva)",
                        "select" => "count(PartitaIva) as count, localita,provincia"
            ));
            $out = array_map(function($x) {
                return array(
                    "Localita" => $x->localita,
                    "Provincia" => $x->provincia,
                    "count" => $x->count,
                );
            }, $res);
        } catch (\ActiveRecord\RecordNotFound $e) {
            $this->_setResponseStatus(200);
            echo json_encode(array(array(), true));
            return;
        } catch (\ActiveRecord\ActiveRecordException $e) {
            $this->_setResponseStatus(500);
            echo json_encode(array(false, array($e->getMessage(), $e->getTrace())));
            return;
        } catch (\Exception $e) {
            $this->_setResponseStatus(500);
            echo json_encode(array(false, $e->getMessage()));
            return;
        }

        echo json_encode(array($out, true));
        return;
//        if ($link->connect_errno)
//        {
//            echo json_encode("Error occurred in db connection!");
//            return;
//        }
//        $sql = $mainquery;
//        $result = $link->query($sql);
//        if ($result && $link->affected_rows == 1)
//        {
//            $sql2 = "SELECT Localita, Provincia, count(piva) as count FROM ts_tmk_pipes a LEFT JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
//                    "WHERE worker = '{$vend}' " .
//                    "AND pipe = '1' " .
//                    "AND lot = '{$lot}' " .
//                    "GROUP BY Localita";
//            $result2 = $link->query($sql2);
//            if ($result2)
//            {
//                echo json_encode($result2->fetch_all(MYSQLI_ASSOC));
//                return;
//            }
//            else
//            {
//                echo json_encode("An error occurred retrieving collections: " . $link->error . " \n query was: " + $sql);
//                return;
//            }
//        }
//        else
//        {
//            echo json_encode("An error occurred retrieving collections: " . $link->error . " \n query was: " + $sql);
//            return;
//        }
    }

    public function ws___newCamp() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_POST["name"]))
        {
            echo json_encode("Missing or wrong parameter 'name'");
            return;
        }
        $name = $this->anti_injection($_POST["name"]);
        $mainquery = "INSERT INTO campaigns " .
                "(name,lots) " .
                "VALUES " .
                "('{$name}','[]')";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result && $link->affected_rows == 1)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error . " \n query was: " + $sql);
            return;
        }
    }

    public function ws___setTmkVendAssociation() {
        $this->setJsonWS();
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_POST["camp"]))
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or wrong parameter 'camp'");
            return;
        }
        $camp = $this->anti_injection($_POST["camp"]);
        if (!isset($_POST["tmk"]))
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or wrong parameter 'tmk'");
            return;
        }
        $tmk = $this->anti_injection($_POST["tmk"]);
        if (!isset($_POST["vend"]))
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or wrong parameter 'vend'");
            return;
        }
        $vend = $this->anti_injection($_POST["vend"]);
        if (!isset($_POST["locs"]))
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or wrong parameter 'locs'");
            return;
        }

        $locs = $this->anti_injection($_POST["locs"]);
        $sql1 = "DELETE FROM ts_jobcoverage WHERE camp='{$camp}' AND tmk = '{$tmk}'";
        $result1 = $link->query($sql1);
        if ($result1 && $locs === "")
        {
            echo json_encode(true);
            return;
        }
        $locs = explode("|", $locs);
        if ($result1 && !count($locs))
        {
            echo json_encode(true);
            return;
        }
        if (is_array($locs) && count($locs))
        {
            $sql2 = "INSERT INTO ts_jobcoverage (camp,vend,loc,tmk) VALUES";

            $insert = array();
            foreach ($locs as $k => $l) {
                $ll = str_ireplace("\\\'", "\'", $l);
                $insert [] = "('{$camp}','{$vend}','{$ll}','{$tmk}')";
            }
            $sql2 .= join(",", $insert);
            if ($link->connect_errno)
            {
                $this->_setResponseStatus(500);
                echo json_encode("Error occurred in db connection!");
                return;
            }


            $result2 = $link->query($sql2);
            if ($result1 && $result2)
            {
                echo json_encode(true);
                return;
            }
            else
            {
                $this->_setResponseStatus(500);
                echo json_encode("An error occurred retrieving collections: " . $link->error . " \n queries were: " + $sql1 + "   " + $sql2);
                return;
            }
        }
        else
        {
            echo json_encode(true);
            return;
        }
    }

    public function ws___restoreCamp() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_POST["camp"]))
        {
            echo json_encode("Missing or wrong parameter 'camp'");
            return;
        }
        $camp = $this->anti_injection($_POST["camp"]);
        $mainquery = "UPDATE campaigns " .
                "SET status = '0' " .
                "WHERE " .
                "id ='{$camp}'";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result && $link->affected_rows == 1)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            echo json_encode("An error occurred updating: " . $link->error . " \n query was: " + $sql);
            return;
        }
    }

    public function ws___suspendCamp() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_POST["camp"]))
        {
            echo json_encode("Missing or wrong parameter 'camp'");
            return;
        }
        $camp = $this->anti_injection($_POST["camp"]);
        $mainquery = "UPDATE campaigns " .
                "SET status = '1' " .
                "WHERE " .
                "id ='{$camp}'";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result && $link->affected_rows == 1)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            echo json_encode("An error occurred updating: " . $link->error . " \n query was: " + $sql);
            return;
        }
    }

    public function ws___closeCamp() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_POST["camp"]))
        {
            echo json_encode("Missing or wrong parameter 'camp'");
            return;
        }
        $camp = $this->anti_injection($_POST["camp"]);
        $mainquery = "UPDATE campaigns " .
                "SET status = '2' " .
                "WHERE " .
                "id ='{$camp}'";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result && $link->affected_rows == 1)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            echo json_encode("An error occurred updating: " . $link->error . " \n query was: " + $sql);
            return;
        }
    }

    public function ws___newLot() {
        $this->setJsonWS();
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_POST["camp"]))
        {
            echo json_encode("Missing or wrong parameter 'camp'");
            return;
        }
        $camp = $this->anti_injection($_POST["camp"]);
        $mainquery = "SELECT id, lots FROM campaigns";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result)
        {
            $lots = $result->fetch_all(MYSQLI_ASSOC);
            $campLot = array();
            $allLots = array();
            foreach ($lots as $k => $v) {
                $ll = str_ireplace(array("[", "]"), "", $v["lots"]);
                $ll = explode(",", $ll);
                if ($v["id"] == $camp)
                {
                    foreach ($ll as $kk => $vv) {
                        $campLot[] = (int) $vv;
                    }
                }
                foreach ($ll as $kk => $vv) {
                    $allLots[] = (int) $vv;
                }
            }
            $newLot = max($allLots) + 1;

            $campLot[] = $newLot;
            $campLot = "[" . join(",", $campLot) . "]";
            $sql = "UPDATE campaigns SET lots = '{$campLot}' WHERE id = '{$camp}'";
            $result2 = $link->query($sql);
            if ($result2)
            {
                echo json_encode($campLot);
                return;
            }
            else
            {
                echo json_encode("An error occurred retrieving: " . $link->error . " \n query was: " + $sql);
                return;
            }
        }
        else
        {
            echo json_encode("An error occurred retrieving: " . $link->error . " \n query was: " + $sql);
            return;
        }
    }

    public static function emptyLot($lot) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!isset($lot) && !is_int($lot))
        {
            return array(false, 400, "Given invalid lot number");
        }
        try {
            $leads = \AR\Pipelead::table()->delete(array("lot" => $lot));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(false, 404, $e->getMessage());
        } catch (\ActiveRecord\ActiveRecordException $e) {
            return array(false, 500, $e->getMessage());
        } catch (\Exception $e) {
            return array(false, 500, $e->getMessage());
        }
        return array(true, 200, true);
    }

    public function ws___emptyLot() {
        $this->setJsonWS();
        $lot = WolfMVC\RequestMethods::get("lot", false);
        if (!isset($lot) || (int) $lot === 0)
        {
            $this->_setResponseStatus(400);
            echo json_encode(array(false, "Invalid lot number"));
            return;
        }
        $res = self::emptyLot((int) $lot);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;
    }

    public function ws___getPipeStatus() {
        $this->setJsonWS();
        $headers = getallheaders();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");

        if (!isset($_GET["pipe"]))
        {
            echo json_encode("Missing or wrong parameter 'pipe'");
            return;
        }
        $pipe = $this->anti_injection($_GET["pipe"]);
        if (!isset($_GET["lot"]))
        {
            $lot = false;
        }
        else
        {
            $lot = $this->anti_injection($_GET["lot"]);
        }
        $mainquery = "SELECT worker as vend,Localita,Provincia,count(piva) as count FROM ts_tmk_pipes " .
                "LEFT JOIN aziende_all ON (piva = PartitaIva) " .
                "WHERE pipe = '{$pipe}' " .
                "AND lot = '{$lot}' " .
                "GROUP BY worker, localita";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        header('X-Query-Was: ' + $sql);
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error . " \n query was: " + $sql);
            return;
        }
    }

    public function ws___getJobCoverage() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $mainquery = "SELECT * FROM ts_jobcoverage";
        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";
        $limitquery = $mainquery . " LIMIT %s,%s";
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___getWorkerAssignment() {
        $this->setJsonWS();
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        if (!isset($_GET["lots"]))
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or wrong parameter 'lots'");
            return;
        }
        $lots = $this->anti_injection($_GET["lots"]);
        if (!isset($_GET["worker"]))
        {
            $worker = false;
        }
        else
        {
            $worker = $this->anti_injection($_GET["worker"]);
        }
        $lots = explode("|", $lots);
        foreach ($lots as $k => $v) {
            $lots[$k] = "'" . $v . "'";
        }
        $lots = join(",", $lots);
        $mainquery = "SELECT worker,b.Localita,count(a.piva) as countForWorker, GROUP_CONCAT(DISTINCT lot ORDER BY lot) as lot FROM " .
                "ts_tmk_pipes a " .
                "LEFT JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "where a.pipe = '1' " .
                "AND a.lot IN ({$lots}) " .
                ($worker ? "AND worker = '{$worker}' " : "") .
                "group by worker,localita;";
        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";
        $limitquery = $mainquery . " LIMIT %s,%s";
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

//    public function ws___getCommerciali() {
//        $this->setJsonWS();
//        $googleClient = \WolfMVC\Registry::get("googleClient");
//        $googleDir = new Google_Service_Directory($googleClient);
//        $session = \WolfMVC\Registry::get("session");
//        $db = \WolfMVC\Registry::get("database_vtiger");
//        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
//        $link->set_charset("utf8");
//        $mainquery = "SELECT a.id as USERID, a.first_name as NOME, a.last_name as COGNOME, a.email1 as EMAIL FROM vtiger_users a " .
//                "LEFT JOIN vtiger_user2role b ON (a.id = b.userid) " .
//                "WHERE b.roleid IN ('H4','H5') " .
//                "AND a.status = 'Active'";
//        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";
//        $limitquery = $mainquery . " LIMIT %s,%s";
//        if ($link->connect_errno)
//        {
//            echo json_encode("Error occurred in db connection!");
//            return;
//        }
//        $sql = $mainquery;
//        $result = $link->query($sql);
//        if ($result)
//        {
//            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
//            return;
//        }
//        else
//        {
//            echo json_encode("An error occurred retrieving collections: " . $link->error);
//            return;
//        }
//    }

    public function ws___getCommerciali() {
        $this->setJsonWS();
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
//        $options = array_merge(array('conditions' => "pipe = '6' AND tmklock = '{$pars["tmk"]}'"), ));
        $options = array(
            'joins' => array('role'),
            'select' => 'id,first_name,last_name,email1',
            'conditions' => array('status = ? AND roleid in (?)', array('Active'), array('H4', 'H5')),
            'include' => array('role'),
            'order' => 'last_name asc'
        );
        $users = AR\Vtuser::all($options);
        $out = array();
        foreach ($users as $k => $v) {
            $user = WolfMVC\ArrayMethods::translateVMLabels($v->attributes(), array(
                        "id" => "USERID",
                        "first_name" => "NOME",
                        "last_name" => "COGNOME",
                        "email1" => "EMAIL"
            ));
            $out[] = $user;
        }
        echo json_encode($out);
        return;
    }

    public function ws___getAnalisti() {
        $this->setJsonWS();
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
//        $options = array_merge(array('conditions' => "pipe = '6' AND tmklock = '{$pars["tmk"]}'"), ));
        $options = array(
            'joins' => array('role'),
            'select' => 'id,first_name,last_name,email1',
            'conditions' => array('status = ? AND roleid in (?)', array('Active'), array('H10', 'H11')),
            'include' => array('role')
        );
        $users = AR\Vtuser::all($options);
        $out = array();
        foreach ($users as $k => $v) {
            $user = WolfMVC\ArrayMethods::translateVMLabels($v->attributes(), array(
                        "id" => "USERID",
                        "first_name" => "NOME",
                        "last_name" => "COGNOME",
                        "email1" => "EMAIL"
            ));
            $out[] = $user;
        }
        echo json_encode($out);
        return;
    }

    public function ws___listaPerTelemarketing() {
        $this->setJsonWS();
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $link = $this->instantiateMysqli("mkt");
        $pars = array(
            "tmk" => WolfMVC\RequestMethods::post("tmk", false),
            "lots" => WolfMVC\RequestMethods::post("lots", false),
            "locs" => WolfMVC\RequestMethods::post("locs", false),
            "worker" => WolfMVC\RequestMethods::post("worker", false)
        );
        $lots = array();

        if (!is_array($pars["lots"]))
        {
            $pars["lots"] = explode("|", $pars["lots"]);
        }
        $params = array();
//        print_r($pars["lots"]);
        for ($ijk = 0; $ijk < 3; $ijk++) {
            $lotsph = array();
            $lotsexp = array();
            foreach ($pars["lots"] as $k => $v) {
                $params[] = &$pars["lots"][$k];
                $lotsph[] = "?";
                $lotsexp[] = "i";
            }
        }
//        $lots = join(",", $lots);

        $lotsph = "(" . join(",", $lotsph) . ")";
        $locs = array();
        $locsph = array();
        $locsexp = array();
        if (!is_array($pars["locs"]))
        {
            $pars["locs"] = explode("|", $pars["locs"]);
        }

        foreach ($pars["locs"] as $k => $v) {
            if (get_magic_quotes_gpc())
            {
                $v = stripslashes($v);
            }
            $params[] = &$pars["locs"][$k];
            $locsph[] = "?";
            $locsexp[] = "s";
        }

//        print_r($params);
        $locsph = "(" . join(",", $locsph) . ")";
//        $locsexp = join("", $locsexp);
//        $params = array_merge($lots, $lots, $lots, $locs);
        $types = join("", array_merge($lotsexp, $lotsexp, $lotsexp, $locsexp));
//        try {
//            $leads = AR\Pipelead::all(array(
//                        "conditions" => array("tmklock = ? AND lot IN (?) AND working = '1' AND pipe IN ('1','2','3')", $pars["tmk"], $pars["lots"]),
//                        "include" => array("lead" => array("lock", "numeritelefono"))
//            ));
//        } catch (\Exception $e) {
//            print_r($e);
//        }
////        echo \AR\Pipelead::table()->last_sql;
//        $out = array_map(function($x) {
//            $out = array();
//            $out = array_merge($out, $x->get_values_for(array("lot", "piva", "worker", "working")));
//            $out = array_merge($out, $x->lead->get_values_for(array("classedipendenti", "codiceateco", "fasciafatturato", "formagiuridica", "localita", "punteggio_azienda", "ragionesociale")));
//            if (count($x->lead->numeritelefono))
//            {
//                $out = array_merge($out, array_merge($x->lead->get_values_for(array("telefono")), array("telefono" => join(",", array_map(function($z) {
//                                        return $z->tel;
//                                    }, $x->lead->numeritelefono)))));
//            }
//            else
//            {
//                $out = array_merge($out, array_merge($x->lead->get_values_for(array("telefono"))));
//            }
//            return $out;
//        }, $leads);
//        echo json_encode($out);
//        return;
        $mainquery = "SELECT piva, worker, Punteggio_Azienda, working, lot, " .
                "RagioneSociale,Localita,CodiceAteco, FormaGiuridica, FasciaFatturato, ClasseDipendenti, unlock_timestamp, static_lock, " .
                "Telefoni, fff as 'force' FROM ( " .
                "SELECT a.*,b.RagioneSociale,b.Localita,b.CodiceAteco, b.FormaGiuridica, " .
                "b.FasciaFatturato, b.ClasseDipendenti, " .
                "if(isnull(c.unlock_timestamp),0,c.unlock_timestamp) as unlock_timestamp, " .
                "if(isnull(c.static_lock),0,c.static_lock) as static_lock,if(isnull(GROUP_CONCAT(nt.Tel)),b.Telefono,CONCAT(b.Telefono, ',', GROUP_CONCAT(nt.Tel))) as Telefoni, " .
                "c.force as fff " .
                "FROM ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "JOIN Numeri_telefono nt ON (nt.PartitaIva = b.PartitaIva)  " .
                "LEFT JOIN ts_piva_tmp_lock c ON (a.piva = c.piva) " .
                "WHERE lot IN $lotsph " .
                "AND tmklock = '{$pars["tmk"]}' " .
                "AND working = '1' " .
                "AND pipe IN ('1','2','3') " .
                "group by b.PartitaIva  " .
                "UNION " .
                "SELECT a.*,b.RagioneSociale,b.Localita,b.CodiceAteco, b.FormaGiuridica, " .
                "b.FasciaFatturato, b.ClasseDipendenti, " .
                "if(isnull(c.unlock_timestamp),0,c.unlock_timestamp) as unlock_timestamp, " .
                "if(isnull(c.static_lock),0,c.static_lock) as static_lock,if(isnull(GROUP_CONCAT(nt.Tel)),b.Telefono,CONCAT(b.Telefono, ',', GROUP_CONCAT(nt.Tel))) as Telefoni, " .
                "c.force as fff " .
                "FROM ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "LEFT JOIN Numeri_telefono nt ON (nt.PartitaIva = b.PartitaIva)  " .
                "LEFT JOIN ts_piva_tmp_lock c ON (a.piva = c.piva) " .
                "WHERE lot IN $lotsph " .
                "AND pipe < '4' " .
                "AND c.force = '{$pars["tmk"]}' " .
                "group by b.PartitaIva  " .
                "UNION " .
                "( " .
                "SELECT a.*,b.RagioneSociale,b.Localita,b.CodiceAteco, b.FormaGiuridica, " .
                "b.FasciaFatturato, b.ClasseDipendenti, " .
                "if(isnull(c.unlock_timestamp),0,c.unlock_timestamp) as unlock_timestamp, " .
                "if(isnull(c.static_lock),0,c.static_lock) as static_lock,if(isnull(GROUP_CONCAT(nt.Tel)),b.Telefono,CONCAT(b.Telefono, ',', GROUP_CONCAT(nt.Tel))) as Telefoni, " .
                "c.force as fff " .
                "FROM ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "LEFT JOIN Numeri_telefono nt ON (nt.PartitaIva = b.PartitaIva)  " .
                "LEFT JOIN ts_piva_tmp_lock c ON (a.piva = c.piva) " .
                "WHERE lot IN $lotsph " .
                "AND worker = '{$pars["worker"]}' " .
                "AND b.Localita IN $locsph " .
                "AND pipe < '4' " .
                "group by b.PartitaIva  " .
                "ORDER BY lot ASC, a.Punteggio_Azienda DESC " .
                "LIMIT 30 " .
                ") " .
                ") a " .
                "LIMIT 30";
//        echo $mainquery;
//        var_dump(array_merge(array($types), $params));
//        $mainquery = str_ireplace("\\'", "\'", $mainquery);
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
            echo json_encode("Error occurred in db connection!");
            return;
        }


        $sql = $mainquery;
        $stmt = $link->stmt_init();
//        var_dump($stmt);
        if (($stmt->prepare($sql)) === false)
        {
            $this->_setResponseStatus(500);
            echo json_encode($link->error);
            return;
        }

        $ref = new \ReflectionClass('mysqli_stmt');
        $method = $ref->getMethod("bind_param");
        if ($method->invokeArgs($stmt, array_merge(array($types), $params)) === false)
        {
            $this->_setResponseStatus(500);
            echo json_encode($stmt->error);
            return;
        }

        if ($stmt->execute() === false)
        {
            $this->_setResponseStatus(500);
            echo json_encode($stmt->error);
            return;
        }
        if (($result = $stmt->get_result()) === false)
        {
            $this->_setResponseStatus(500);
            echo json_encode($stmt->error);
            return;
        }
        if ($result)
        {
            $rr = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($rr);
            return;
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error . " query was " . $sql);
            return;
        }
    }

    public function ws___listaPersonaliTelemarketing() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $pars = $this->retrievePars(
                array(
                    array("tmk", "post", true),
                    array("lots", "post", true)
                )
        );
        $lots = array();
        if (!is_array($pars["lots"]))
        {
            $pars["lots"] = explode("|", $pars["lots"]);
        }
        foreach ($pars["lots"] as $k => $v) {
            $lots[] = "'" . $v . "'";
        }
        $lots = join(",", $lots);
        $mainquery = "SELECT piva, worker, Punteggio_Azienda, working, donotcallbefore, " .
                "RagioneSociale,Localita,CodiceAteco, FormaGiuridica, FasciaFatturato, ClasseDipendenti, unlock_timestamp, static_lock, " .
                "Telefoni, fff as 'force' FROM ( " .
                "SELECT a.*,b.RagioneSociale,b.Localita,b.CodiceAteco, b.FormaGiuridica, " .
                "b.FasciaFatturato, b.ClasseDipendenti, " .
                "if(isnull(c.unlock_timestamp),0,c.unlock_timestamp) as unlock_timestamp, " .
                "if(isnull(c.static_lock),0,c.static_lock) as static_lock,CONCAT(b.Telefono, ',', GROUP_CONCAT(nt.Tel)) as Telefoni, " .
                "c.force as fff " .
                "FROM ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "LEFT JOIN Numeri_telefono nt ON (nt.PartitaIva = b.PartitaIva)  " .
                "LEFT JOIN ts_piva_tmp_lock c ON (a.piva = c.piva) " .
                "WHERE lot IN ({$lots}) " .
                "AND tmklock = '{$pars["tmk"]}' " .
                "AND pipe = '6' " .
                "group by b.PartitaIva  " .
                ") a ";

        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error . " query was " . $sql);
            return;
        }
    }

    public function ws___listaRifissiTelemarketing() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $pars = $this->retrievePars(
                array(
                    array("lots", "post", true)
                )
        );
        $lots = array();
        if (!is_array($pars["lots"]))
        {
            $pars["lots"] = explode("|", $pars["lots"]);
        }
        foreach ($pars["lots"] as $k => $v) {
            $lots[] = "'" . $v . "'";
        }
        $lots = join(",", $lots);
        $mainquery = "SELECT piva, worker, Punteggio_Azienda, working, " .
                "RagioneSociale,Localita,CodiceAteco, FormaGiuridica, FasciaFatturato, ClasseDipendenti, unlock_timestamp, static_lock, " .
                "Telefoni, fff as 'force' FROM ( " .
                "SELECT a.*,b.RagioneSociale,b.Localita,b.CodiceAteco, b.FormaGiuridica, " .
                "b.FasciaFatturato, b.ClasseDipendenti, " .
                "if(isnull(c.unlock_timestamp),0,c.unlock_timestamp) as unlock_timestamp, " .
                "if(isnull(c.static_lock),0,c.static_lock) as static_lock,CONCAT(b.Telefono, ',', GROUP_CONCAT(nt.Tel)) as Telefoni, " .
                "c.force as fff " .
                "FROM ts_tmk_pipes a " .
                "JOIN aziende_all b ON (a.piva = b.PartitaIva) " .
                "LEFT JOIN Numeri_telefono nt ON (nt.PartitaIva = b.PartitaIva)  " .
                "LEFT JOIN ts_piva_tmp_lock c ON (a.piva = c.piva) " .
                "WHERE lot IN ({$lots}) " .
                "AND pipe = '4' " .
                "group by b.PartitaIva  " .
                ") a ";

        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error . " query was " . $sql);
            return;
        }
    }

    public function ws___searchLeadByPiva() {
        $this->setJsonWS();
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        $piva = \WolfMVC\RequestMethods::get("piva", false);
        $offset = \WolfMVC\RequestMethods::get("offset", false);
        if ($piva === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        try {
            $leads = \AR\Lead::all(array("conditions" => array("PartitaIva like '%$piva%'", $piva),
                        "include" => array("pipe", "numeritelefono", "leadamend"),
                        "limit" => 100,
                        'offset' => (isset($offset) && is_integer($offset) ? $offset : 0)
            ));
//        } catch (\ActiveRecord\RecordNotFound $e) {
        } catch (\Exception $e) {
            $this->_setResponseStatus(404);
            echo json_encode(array("No such piva", AR\Lead::table()->last_sql, $e->getMessage()));
            return;
        }
        $out = array();
        foreach ($leads as $k => $l) {
            $lead = $l->attributes(true);
            if (is_array($l->numeritelefono))
            {
                $lead["telefono2"] = array_map(function($x) {
                    return $x->telefono;
                }, $l->numeritelefono);
            }

            try {
                $lastCall = \AR\Tsnwlog::last(array("conditions" => array("piva = ? AND what_element = ?", $lead["partitaiva"], "call")));
            } catch (\ActiveRecord\RecordNotFound $e) {
                $lead["ultimachiamata"] = NULL;
            } catch (\Exception $e) {
                $lead["ultimachiamata"] = NULL;
            }
            if (isset($lastCall) && isset($lastCall->timestamp))
            {
                $lead["ultimachiamata"] = $lastCall->timestamp;
            }
            else
            {
                $lead["ultimachiamata"] = NULL;
            }
            if (count($l->pipe))
            {
                $lead["pipe"] = $l->pipe[0]->attributes(true);
            }
            if (count($l->leadamend))
            {
                $lead["amend"] = array_map(function($x) {
                    return $x->attributes();
                }, $l->leadamend);
            }
//            try {
//                $lastVisit = AR\Vtaccountscf::all(array(
//                            "conditions" => array("cf_641 = ?", $lead["partitaiva"]),
//                    "include" => array("acc" => "potentials","ent")
//                                )
//                );
//            } catch (\Exception $e) {
//                
//            }



            $out[] = $lead;
        }
        echo json_encode($out);
        return;
    }

    public function ws___cercaLead() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $pars = $this->retrievePars(
                array(
                    array("search", "get", true),
                    array("page", "get", false)
                )
        );

        if (!isset($pars["page"]) || $pars["page"] === "")
        {
            $pars["page"] = 1;
        }

        $mainquery = "SELECT a.*, " .
                "b.lot, b.pipe, b.piva, b.tmklock, b.worker, b.working, b.donotcallbefore, c.* FROM aziende_all a " .
                "LEFT JOIN ts_tmk_pipes b ON (a.PartitaIva = b.piva) " .
                "LEFT JOIN ts_piva_tmp_lock c ON (c.piva = b.piva) " .
                "LEFT JOIN (SELECT loc as lloc, vend as vvend, GROUP_CONCAT(tmk) as ttmk FROM ts_jobcoverage GROUP BY loc,vend ) d " .
                "ON (a.Localita = d.lloc AND b.worker = d.vvend) " .
                "WHERE " .
                "a.PartitaIva LIKE '%{$pars["search"]}%' " .
                "OR " .
                "a.RagioneSociale LIKE '%{$pars["search"]}%' " .
                "LIMIT " . (($pars["page"] - 1) * 20) . ", 20";
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error . " query was " . $sql);
            return;
        }
    }

    public function ws___getTelemarketing() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("vtiger");

        $mainquery = "SELECT a.id as USERID, a.first_name as NOME, a.last_name as COGNOME, a.email1 as EMAIL FROM vtiger_users a " .
                "LEFT JOIN vtiger_user2role b ON (a.id = b.userid) " .
                "WHERE b.roleid = 'H9' " .
                "AND a.status = 'Active'";
        $countquery = "SELECT COUNT(*) as 'count' FROM (" . $mainquery . ") pippo";

        $limitquery = $mainquery . " LIMIT %s,%s";

        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___CambiaCommerciale() {
        $this->setJsonWS();
        $pars = $this->retrievePars(
                array(
                    array("potentialid", "get", true),
                    array("commerciale", "get", true)
                )
        );
        $vtws = \WolfMVC\Registry::get("VTWS");
        $potential = $vtws->doRetrieve("13x" . $pars["potentialid"]);
        if (!is_array($potential) || !isset($potential["id"]))
        {
            echo json_encode("errore: non trovo la pot");
            $this->_setResponseStatus(404);
        }
        $account = $vtws->doRetrieve($potential["related_to"]);
        if (!is_array($account) || !isset($account["id"]))
        {
            echo json_encode("errore: non trovo l'account");
            $this->_setResponseStatus(404);
        }
        $activities = $vtws->doQuery("SELECT * FROM Events WHERE cf_702 ='{$pars["potentialid"]}'");
        foreach ($activities as $k => $a) {
            $a["subject"] = $pars["commerciale"] . " - " . $account["accountname"];
            $a["cf_650"] = $pars["commerciale"];
            $vtws->doUpdate($a);
            sleep(1);
        }
        $account["cf_643"] = $pars["commerciale"];
        $vtws->doUpdate($account);
        sleep(1);
        $potential["cf_647"] = $pars["commerciale"];
        $vtws->doUpdate($potential);
        sleep(1);
        echo json_encode(true);
        return;
    }

    public function ws___cambiaDataOra() {
        $this->setJsonWS();
        $pars = array(
            "potentialid" => WolfMVC\RequestMethods::get("potentialid", false),
            "data" => WolfMVC\RequestMethods::get("data", false),
            "ora" => WolfMVC\RequestMethods::get("ora", false),
            "minuti" => WolfMVC\RequestMethods::get("minuti", false)
        );

        if (!isset($pars["potentialid"]) || $pars["potentialid"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid potentialid value");
            return;
        }
        if (!isset($pars["data"]) || $pars["data"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid data value");
            return;
        }
        if (!isset($pars["ora"]) || $pars["ora"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid ora value");
            return;
        }
        if (!isset($pars["minuti"]) || $pars["minuti"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing or invalid minuti value");
            return;
        }


        $vtws = \WolfMVC\Registry::get("VTWS");
        $potential = $vtws->doRetrieve("13x" . $pars["potentialid"]);
        if (!is_array($potential) || !isset($potential["id"]))
        {
            echo json_encode("errore: non trovo la pot");
            $this->_setResponseStatus(404);
        }
//        $account = $vtws->doRetrieve($potential["related_to"]);
//        if (!is_array($account) || !isset($account["id"]))
//        {
//            echo json_encode("errore: non trovo l'account");
//            $this->_setResponseStatus(404);
//        }
        $potid = $pars["potentialid"];
        $potid = str_ireplace("13x", "", $potid);
        $activities = $vtws->doQuery("SELECT * FROM Events WHERE cf_702 = '{$potid}'");
        if (!count($activities))
        {
            $this->_setResponseStatus(500);
            echo json_encode("Non é possibile effettuare questo spostamento. Non trovo attività agganciate correttamente.");
            return;
        }
        foreach ($activities as $k => $a) {
            $a["date_start"] = $pars["data"];
            $a["due_date"] = $pars["data"];
            $ora = $pars["ora"];
            $a["time_start"] = $ora . ":" . $pars["minuti"] . ":00";
            $ora = 1 + (int) $ora;
            if ($ora < 0)
                $ora = "0" . $ora;
            $a["time_end"] = $ora . ":" . $pars["minuti"] . ":00";
            $vtws->doUpdate($a);
            sleep(1);
        }
        $potential["closingdate"] = $pars["data"];
        $vtws->doUpdate($potential);
        sleep(1);
        echo json_encode(true);
        return;
    }

    public function ws___getAppuntamenti2() {
        $this->setJsonWS();
        $linkmkt = $this->instantiateMysqli("mkt");
        $linkvt = $this->instantiateMysqli("vtiger");
        $pars = $this->retrievePars(
                array(
                    array("commerciali", "post", true),
                    array("startTime", "post", true),
                    array("endTime", "post", true)
                )
        );

        foreach ($pars["commerciali"] as $k => $v) {
            $pars["commerciali"][$k] = "'" . $v . "'";
        }

        $commerciali = join(", ", $pars["commerciali"]);
//        echo json_encode($pars)."\n\r";

        $this->setJsonWS();
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
//        $options = array_merge(array('conditions' => "pipe = '6' AND tmklock = '{$pars["tmk"]}'"), ));
        $options = array(
//            'joins' => array('vtiger_potentialscf'),
//            'select' => 'id_opportunita,potential_no,potentialname,amount,closingdate,nextstep,sales_stage,related_to',
            'conditions' => array("closingdate >= ? AND closingdate <= ? AND potentialname = 'Analisi'", $pars["startTime"], $pars["endTime"]),
            'include' => array('cf', 'ent', 'account' => array("cf", "ent", "contacts" => array("ent")))
        );
        $potentials = AR\Vtpotential::all($options);
        $out = array();
        foreach ($potentials as $k => $v) {
//filtro potent.deleted
            if ($v->ent->deleted == 1)
                continue;
//filtro accent.deleted
            if ($v->account->ent->deleted == 1)
                continue;
            $app = $v->get_values_for(array("id_opportunita", "numero_opportunita", "nome_opportunita", "ammontare", "data_chiusura_attesa",
                "prossimo_step", "stadio_vendita", "collegato_a"));
            $app = array_merge($app, $v->cf->get_values_for(array("telemarketing", "venditore", "esito_visita", "esito_conferma")));

            $app["errors"] = array();

            $app["account"] = $v->account->get_values_for(array("numero_cliente", "tipo_cliente", "id_cliente", "ragione_sociale", "fatturato",
                "dipendenti", "telefono", "sito"));
            $app["account"] = array_merge($app["account"], $v->account->cf->get_values_for(array("partita_iva", "venditore", "telemarketing", "fascia_fatturato", "forma_giuridica",
                        "classe_dipendenti")));
            $app["indirizzoaccount"] = $v->account->billads->get_values_for(array("citta", "via"));
            $app["contacts"] = array();
            $cont = $v->account->contacts;
            foreach ($cont as $ci => $cc) {
                if ($cc->ent->deleted === 1)
                    continue;
                $app["contacts"] = array_merge(array($cc->get_values_for(array("nome", "cognome", "ruolo", "telefono", "cellulare", "email"))));
            }
            $app["activities"] = array();
            $activities = $v->account->ent->activityrel;
            foreach ($activities as $ack => $acv) {
                if ($acv->activity->ent->deleted == 1)
                {
                    continue;
                }
                $activity = $acv->activity->attributes(true);
                if (!is_object($activity["data_inizio"]))
                {
                    continue;
//                    echo json_encode($activity);
//                    return;
                }
                if (!is_object($app["data_chiusura_attesa"]))
                {
                    continue;
//                    echo json_encode($app);
//                    return;
                }
                if ($activity["stato"] !== "Planned" || ($activity["data_inizio"]->format('Y-m-d') !== $app["data_chiusura_attesa"]->format('Y-m-d')))
                {
                    continue;
                }
                $activity = array_merge($activity, $acv->activity->cf->attributes(true));
                $activity["ent"] = $acv->ent->attributes();
                $app["activities"][] = $activity;
            }
            if (count($app["activities"]) !== 1)
            {
                $app["errors"][] = "ACTIVITY ERROR: COUNT " . count($app["activities"]);
            }
            $out[] = $app;
        }
        echo json_encode($out);

        return;

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
                "   pot.closingdate = '{$pars["startTime"]}' " .
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

        echo $query;
        return;
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

    public function ws___getCampaign() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        if (!isset($_GET["camp"]))
        {
            echo json_encode("Missing or wrong parameter 'camp'");
            return;
        }
        $camp = $this->anti_injection($_GET["camp"]);
        $mainquery = "SELECT * FROM campaigns";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result)
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            $row = array();
            $lots = array();

            foreach ($ret as $k => $v) {
                if ($v["id"] === $camp)
                {
                    $row = $ret[$k];
                }
                $lll = str_ireplace("[", "", str_ireplace("]", "", $v["lots"]));
                $lots = array_merge($lots, explode(",", $lll));
            }

            if ($row["lots"] === '[]')
            {
                if (($key = array_search("", $lots)) !== false)
                {
                    unset($lots[$key]);
                }
                sort($lots, SORT_NUMERIC);
                if (count($lots) === 0)
                    $newlot = 1;
                else
                    $newlot = intval($lots[count($lots) - 1]) + 1;
                $result2 = $link->query("UPDATE campaigns SET lots = '[{$newlot}]' WHERE id = '" . $row['id'] . "'");
                if ($result2)
                {
                    $row['lots'] = "[{$newlot}]";
                    echo json_encode(array($row));
                    return;
                }
            }
            else
            {
                echo json_encode(array($row));
                return;
            }
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___getCampaigns() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $link->set_charset("utf8");
        $mainquery = "SELECT * FROM campaigns";
        if ($link->connect_errno)
        {
            $this->_setResponseStatus(500);
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
            $this->_setResponseStatus(500);
            echo json_encode("An error occurred retrieving collections: " . $link->error);
            return;
        }
    }

    public function ws___amendPiva() {
        $this->setJsonWS();
        $link = $this->instantiateMysqli("mkt");
        $pars = $this->retrievePars(
                array(
                    array("piva", "post", true),
                    array("vm", "post", true),
                    array("cancella", "post", true),
                    array("user", "post", true)
                )
        );
        if (isset($pars["error"]) && $pars["error"])
        {
            echo json_encode($pars);
            return;
        }
        if (!(is_array($pars["vm"])))
        {
            echo json_encode("Missing or wrong parameter: vm must be an array-object");
            return;
        }
        $insertrow = array(
            "Cancella",
            "PartitaIva",
            "RagioneSociale",
            "Indirizzo",
            "Localita",
            "Cap",
            "Provincia",
            "Regione",
            "Email",
            "Sito",
            "FormaGiuridica",
            "FasciaFatturato",
            "ClasseDipendenti",
            "Telefono",
            "user"
        );

        $insert = array();

        foreach ($pars["vm"] as $k => $v) {
            if (in_array($k, $insertrow) && $v !== "")
            {
                $tmp = array();
                foreach ($insertrow as $c => $t) {
                    $tmp[$t] = "''";
                }
                $tmp["PartitaIva"] = "'" . $pars["piva"] . "'";
                $tmp["user"] = "'" . $pars["user"] . "'";
                $tmp[$k] = "'" . $v . "'";
                if (strtolower($pars["cancella"]) === "true")
                    $tmp["Cancella"] = "'1'";
                else
                {
                    $tmp["Cancella"] = "'0'";
                }
                $insert[] = "(" . join(",", $tmp) . ")";
            }
        }
        if (!count($insert))
        {
            echo json_encode(true);
            return;
        }
        $mainquery = "INSERT INTO "
                . "aziende_all_amend "
                . "(Cancella,PartitaIva,RagioneSociale,Indirizzo,Localita,Cap,Provincia,Regione,Email,Sito,FormaGiuridica,FasciaFatturato,"
                . "ClasseDipendenti,Telefono,user) VALUES ";
        $mainquery .= join(", ", $insert);
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $sql = $mainquery;
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode(true);
            return;
        }
        else
        {
            echo json_encode("An error occurred updating: " . $link->error . "  query was " . $sql);
            return;
        }
    }

    public function ws___mktLeadChangeStatus() {
        $this->setJsonWS(); //IMPOSTO GLI HEADER PER SEGNALARE CHE RISPONDERO CON UN JSON
        $link = $this->instantiateMysqli("mkt"); //CREO CONNETTORE MYSQL

        $pars = $this->retrievePars(array(//recuperto parameteri
            array("piva", "get", true), // nome par, metodo, mandatory
            array("newStatus", "get", true),
            array("tmk", "get", false),
            array("vend", "get", false),
            array("lot", "get", false)
        ));

//controllo che il cambiamento abbia senso:
//la piva è conosciuta?
        $aziende_all_map = false;
        $tubo_map = false;
        $query1 = "SELECT * FROM aziende_all WHERE PartitaIva = '{$pars["piva"]}' LIMIT 1";

        $result = $link->query($query1);
        if ($result)
        {
            if ($result->num_rows)
            {
                $aziende_all_map = $result->fetch_all(MYSQLI_ASSOC);
                $aziende_all_map = $aziende_all_map[0];
            }
            else
            {
                $this->_setResponseStatus(404);
                echo json_encode("La piva è sconosciuta");
                return;
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se la piva è conosciuta");
            return;
        }

//la piva è nel tubo?
        $result = 0;
        $query2 = "SELECT * FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";

        $result = $link->query($query2);

        if ($result)
        {
            if ($result->num_rows)
            {
                $tubo_map = $result->fetch_all(MYSQLI_ASSOC);
                $tubo_map = $tubo_map[0];
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se la piva è nel tubo");
            return;
        }

        if ($tubo_map && !(in_array($tubo_map["pipe"], array("1", "2", "3", "4", "6")) || $pars["newStatus"] == "libera"))
        {
            $this->_setResponseStatus(403);
            echo json_encode("Impossibile modificare scheda nel tubo attuale");
            return;
        }

        $deveEssereAggiuntaAlTubo = false;
        switch ($pars["newStatus"]) {

            case 'libera':
                if (!$tubo_map)
                { // è già libera
                    echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => array(
                            "Punteggio_Azienda" => null,
                            "donotcallbefore" => null,
                            "insert_timestamp" => null,
                            "loc" => null,
                            "lot" => null,
                            "pipe" => null,
                            "tmklock" => null,
                            "worker" => null,
                            "working" => null,
                            "piva" => null
                    )));
                    return;
                }
                if ($tubo_map)
                {
                    if ($tubo_map["working"] == "1")
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Impossibile liberare scheda attualmente in lavorazione");
                        return;
                    }
                    $queryLibera1 = "DELETE FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";
                    $queryLibera2 = "UPDATE aziende_all SET Esclusione = (Esclusione - 2) WHERE PartitaIva = '{$pars["piva"]}' "
                            . "AND ((Esclusione & 2) <> 0)";
                    $result = 0;
                    $result = $link->query($queryLibera1);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante l'eliminazione della lead. " . $link->error . " " . $queryLibera1);
                        return;
                    }
                    $result = 0;
                    $result = $link->query($queryLibera2);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante l'eliminazione della lead. " . $link->error . " " . $queryLibera2);
                        return;
                    }
                    $aziende_all_map["Esclusione"] = (($aziende_all_map["Esclusione"] & 2) <> 0 ? $aziende_all_map["Esclusione"] - 2 : $aziende_all_map["Esclusione"]);
                    echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => array(
                            "Punteggio_Azienda" => null,
                            "donotcallbefore" => null,
                            "insert_timestamp" => null,
                            "loc" => null,
                            "lot" => null,
                            "pipe" => null,
                            "tmklock" => null,
                            "worker" => null,
                            "working" => null,
                            "piva" => null
                    )));
                    return;
                }
                break;
            case 'coda':
                if (!isset($pars["vend"]) || ((int) $pars["vend"] <= 0))
                {
                    $this->_setResponseStatus(403);
                    echo json_encode("Venditore assente o non valido");
                    return;
                }
                if (!$tubo_map)
                {
                    if (!isset($pars["lot"]) || ((int) $pars["lot"] <= 0))
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Lotto assente o non valido");
                        return;
                    }
                    $aziende_all_map["Localita"] = addslashes($aziende_all_map["Localita"]);
                    $queryCoda1 = "INSERT INTO ts_tmk_pipes (piva, lot, pipe, worker, Punteggio_Azienda, working, loc) "
                            . "VALUES ('{$pars["piva"]}','{$pars["lot"]}','1','{$pars["vend"]}',"
                            . "'{$aziende_all_map["Punteggio_Azienda"]}','0','{$aziende_all_map["Localita"]}')";
                    $queryCoda2 = "UPDATE aziende_all SET Esclusione = (Esclusione | 2) WHERE PartitaIva = '{$pars["piva"]}'";
                    $result = 0;
                    $result = $link->query($queryCoda1);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante inserimento della lead nella coda. " . $link->error . " " . $queryCoda1);
                        return;
                    }
                    $aziende_all_map["Esclusione"] = ($aziende_all_map["Esclusione"] | 2);
                    $result = 0;
                    $result = $link->query($queryCoda2);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante l'inserimento della lead nella coda. " . $link->error);
                        return;
                    }
                    $query2 = "SELECT * FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";
                    $result = 0;
                    $result = $link->query($query2);

                    if ($result)
                    {
                        if ($result->num_rows)
                        {
                            $tubo_map = $result->fetch_all(MYSQLI_ASSOC);
                            $tubo_map = $tubo_map[0];
                        }
                    }
                    else
                    {
                        $this->_setResponseStatus(500);
                        echo json_encode("Si è verificato un errore, impossibile ricontrollare se la piva è nel tubo");
                        return;
                    }
                }
                if ($tubo_map["pipe"] == "6")
                {
                    $queryCoda3 = "UPDATE ts_tmk_pipes SET pipe = '1', tmklock = '0', working = '0' WHERE piva = '{$pars["piva"]}'";
                    $result = 0;
                    $result = $link->query($queryCoda3);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante inserimento della lead nella coda. " . $link->error);
                        return;
                    }
                }
                $tubo_map["pipe"] = '1';
                $tubo_map["tmklock"] = '0';
                $tubo_map["working"] = '0';
                echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map));
                return;
                break;
            case 'personale':
                if (!isset($pars["vend"]) || ((int) $pars["vend"] <= 0))
                {
                    $this->_setResponseStatus(403);
                    echo json_encode("Venditore assente o non valido");
                    return;
                }
                if (!isset($pars["tmk"]) || ((int) $pars["tmk"] <= 0))
                {
                    $this->_setResponseStatus(403);
                    echo json_encode("Tmk assente o non valido");
                    return;
                }
                if (!$tubo_map)
                {
                    if (!isset($pars["lot"]) || ((int) $pars["lot"] <= 0))
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Lotto assente o non valido");
                        return;
                    }
                    $aziende_all_map["Localita"] = addslashes($aziende_all_map["Localita"]);
                    $queryCoda1 = "INSERT INTO ts_tmk_pipes (piva, lot, pipe, worker, tmklock, Punteggio_Azienda, working, loc) "
                            . "VALUES ('{$pars["piva"]}','{$pars["lot"]}','6','{$pars["vend"]}', '{$pars["tmk"]}',"
                            . "'{$aziende_all_map["Punteggio_Azienda"]}','0','{$aziende_all_map["Localita"]}')";
                    $queryCoda2 = "UPDATE aziende_all SET Esclusione = (Esclusione | 2) WHERE PartitaIva = '{$pars["piva"]}'";
                    $result = 0;
                    $result = $link->query($queryCoda1);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante inserimento della lead tra i personali. " . $link->error);
                        return;
                    }
                    $aziende_all_map["Esclusione"] = ($aziende_all_map["Esclusione"] | 2);
                    $result = 0;
                    $result = $link->query($queryCoda2);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante l'inserimento della lead tra i personali. " . $link->error);
                        return;
                    }
                    $query2 = "SELECT * FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";
                    $result = 0;
                    $result = $link->query($query2);

                    if ($result)
                    {
                        if ($result->num_rows)
                        {
                            $tubo_map = $result->fetch_all(MYSQLI_ASSOC);
                            $tubo_map = $tubo_map[0];
                            $tubo_map["pipe"] = '6';
                            echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map));
                            return;
                        }
                    }
                    else
                    {
                        $this->_setResponseStatus(500);
                        echo json_encode("Si è verificato un errore, impossibile ricontrollare se la piva è nel tubo");
                        return;
                    }
                }
                if ($tubo_map["pipe"])
                {
                    $queryCoda3 = "UPDATE ts_tmk_pipes SET pipe = '6', tmklock = '{$pars["tmk"]}', working = '0' WHERE piva = '{$pars["piva"]}'";
                    $result = 0;
                    $result = $link->query($queryCoda3);
                    if (!$result)
                    {
                        $this->_setResponseStatus(403);
                        echo json_encode("Errore durante inserimento della lead tra le personali. " . $link->error);
                        return;
                    }
                }
                $tubo_map["pipe"] = '6';
                $tubo_map["tmklock"] = $pars["tmk"];
                $tubo_map["working"] = '0';
                echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map));
                return;
                break;
        }
    }

    public function ws___mktLeadDisable() {
        $this->setJsonWS(); //IMPOSTO GLI HEADER PER SEGNALARE CHE RISPONDERO CON UN JSON
        $link = $this->instantiateMysqli("mkt"); //CREO CONNETTORE MYSQL
        $pars = array(
            "piva" => WolfMVC\RequestMethods::get("piva", false),
            "disable" => WolfMVC\RequestMethods::get("disable", false)
        );
        if (!isset($pars["piva"]) || $pars["piva"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        if (!isset($pars["disable"]) || $pars["disable"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing disable value");
            return;
        }

//controllo che il cambiamento abbia senso:
//la piva è conosciuta?
        $aziende_all_map = false;
        $tubo_map = false;
        $query1 = "SELECT * FROM aziende_all WHERE PartitaIva = '{$pars["piva"]}' LIMIT 1";

        $result = $link->query($query1);
        if ($result)
        {
            if ($result->num_rows)
            {
                $aziende_all_map = $result->fetch_all(MYSQLI_ASSOC);
                $aziende_all_map = $aziende_all_map[0];
            }
            else
            {
                $this->_setResponseStatus(404);
                echo json_encode("La piva è sconosciuta");
                return;
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se la piva è conosciuta");
            return;
        }

//la piva è nel tubo?
        $result = 0;
        $query2 = "SELECT * FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";

        $result = $link->query($query2);

        if ($result)
        {
            if ($result->num_rows)
            {
                $tubo_map = $result->fetch_all(MYSQLI_ASSOC);
                $tubo_map = $tubo_map[0];
            }
            else
            {
                $this->_setResponseStatus(404);
                echo json_encode("La lead non è presente in nessun tubo");
                return;
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se la piva è nel tubo");
            return;
        }

        if ($tubo_map && !in_array($tubo_map["pipe"], array("1", "2", "3", "4", "6")))
        {
            $this->_setResponseStatus(403);
            echo json_encode("Impossibile modificare scheda nel tubo attuale");
            return;
        }

        $queryDis = "SELECT * FROM ts_piva_tmp_lock WHERE piva = '{$pars["piva"]}' LIMIT 1";
        $result = 0;
        $result = $link->query($queryDis);
        if ($result)
        {
            if ($result->num_rows)
            {
                $ganascia_map = $result->fetch_all(MYSQLI_ASSOC);
                $ganascia_map = $ganascia_map[0];
                $queryAct = "UPDATE ts_piva_tmp_lock SET static_lock = '{$pars["disable"]}' WHERE piva = '{$pars["piva"]}'";
                $result = 0;
                $result = $link->query($queryAct);
                if ($result)
                {
                    $ganascia_map["static_lock"] = $pars["disable"];
                    echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map, "ganascia_map" => $ganascia_map));
                    return;
                }
                else
                {
                    $this->_setResponseStatus(500);
                    echo json_encode("Si è verificato un errore, non é stato possibile [dis]abilitare la lead. " . $link->error);
                    return;
                }
            }
            else
            {
                $queryAct = "INSERT INTO ts_piva_tmp_lock (piva,static_lock) "
                        . "VALUES ('{$pars["piva"]}','{$pars["disable"]}')";
                $result = 0;
                $result = $link->query($queryAct);
                if ($result)
                {
                    $ganascia_map = array(
                        "static_lock" => $pars["disable"],
                        "piva" => $pars["piva"]
                    );
                    echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map, "ganascia_map" => $ganascia_map));
                    return;
                }
                else
                {
                    $this->_setResponseStatus(500);
                    echo json_encode("Si è verificato un errore, non é stato possibile [dis]abilitare la lead. " . $link->error . "  " . $queryAct);
                    return;
                }
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se l'azienda è attualmente disabilitata");
            return;
        }
    }

    public function ws___mktLeadForce() {
        $this->setJsonWS(); //IMPOSTO GLI HEADER PER SEGNALARE CHE RISPONDERO CON UN JSON
        $link = $this->instantiateMysqli("mkt"); //CREO CONNETTORE MYSQL

        $pars = $this->retrievePars(array(//recuperto parameteri
            array("piva", "get", true), // nome par, metodo, mandatory
            array("tmk", "get", true)
        ));
        $pars["tmk"] = (int) $pars["tmk"];
//controllo che il cambiamento abbia senso:
//la piva è conosciuta?
        $aziende_all_map = false;
        $tubo_map = false;
        $query1 = "SELECT * FROM aziende_all WHERE PartitaIva = '{$pars["piva"]}' LIMIT 1";

        $result = $link->query($query1);
        if ($result)
        {
            if ($result->num_rows)
            {
                $aziende_all_map = $result->fetch_all(MYSQLI_ASSOC);
                $aziende_all_map = $aziende_all_map[0];
            }
            else
            {
                $this->_setResponseStatus(404);
                echo json_encode("La piva è sconosciuta");
                return;
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se la piva è conosciuta");
            return;
        }

//la piva è nel tubo?
        $result = 0;
        $query2 = "SELECT * FROM ts_tmk_pipes WHERE piva = '{$pars["piva"]}'";

        $result = $link->query($query2);

        if ($result)
        {
            if ($result->num_rows)
            {
                $tubo_map = $result->fetch_all(MYSQLI_ASSOC);
                $tubo_map = $tubo_map[0];
            }
            else
            {
                $this->_setResponseStatus(404);
                echo json_encode("La lead non è presente in nessun tubo");
                return;
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se la piva è nel tubo");
            return;
        }

        if ($tubo_map && !in_array($tubo_map["pipe"], array("1", "2", "3", "4", "6")))
        {
            $this->_setResponseStatus(403);
            echo json_encode("Impossibile modificare scheda nel tubo attuale");
            return;
        }

        $queryDis = "SELECT * FROM ts_piva_tmp_lock WHERE piva = '{$pars["piva"]}' LIMIT 1";
        $result = 0;
        $result = $link->query($queryDis);
        if ($result)
        {
            if ($result->num_rows)
            {
                $ganascia_map = $result->fetch_all(MYSQLI_ASSOC);
                $ganascia_map = $ganascia_map[0];
                $queryAct = "UPDATE ts_piva_tmp_lock SET ts_piva_tmp_lock.force = '{$pars["tmk"]}' WHERE piva = '{$pars["piva"]}'";
                $result = 0;
                $result = $link->query($queryAct);
                if ($result)
                {
                    $ganascia_map["force"] = $pars["tmk"];
                    echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map, "ganascia_map" => $ganascia_map));
                    return;
                }
                else
                {
                    $this->_setResponseStatus(500);
                    echo json_encode("Si è verificato un errore, non é stato possibile [s]prenotare la lead. " . $link->error);
                    return;
                }
            }
            else
            {
                $queryAct = "INSERT INTO ts_piva_tmp_lock (piva,`force`) "
                        . "VALUES ('{$pars["piva"]}','{$pars["tmk"]}')";
                $result = 0;
                $result = $link->query($queryAct);
                if ($result)
                {
                    $ganascia_map = array(
                        "force" => $pars["tmk"],
                        "piva" => $pars["piva"]
                    );
                    echo json_encode(array("aziende_all_map" => $aziende_all_map, "tubo_map" => $tubo_map, "ganascia_map" => $ganascia_map));
                    return;
                }
                else
                {
                    $this->_setResponseStatus(500);
                    echo json_encode("Si è verificato un errore, non é stato possibile [s]prenotare la lead " . $queryAct);
                    return;
                }
            }
        }
        else
        {
            $this->_setResponseStatus(500);
            echo json_encode("Si è verificato un errore, impossibile controllare se l'azienda è attualmente prenotata");
            return;
        }
    }

    public function ws___sendComMail() {
        $this->setJsonWS();
        $payload = $this->retrievePars(array(
            array("azienda", "post", true),
            array("location", "post", true),
            array("tmk", "post", true),
            array("venditore", "post", true),
        ));
        $url = 'https://script.google.com/macros/s/AKfycbwoElCEGYmis3MXXRSwjWJMcQXoR1UmBARMLjTOembZaaO7E5o/exec';
        $fields = array();
        foreach ($payload as $k => $p) {
            $fields[$k] = urldecode($p);
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

    public static function setEsclusione32($piva, $disable) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $lead = \AR\Lead::all(array("conditions" => array("PartitaIva = ?", $piva)));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array($piva, 404, "No such lead");
        } catch (\Exception $e) {
            return array($piva, 500, "Error in retrieving lead");
        }
        $lead = $lead[0];
        $actual = ($lead->esclusione & 32);
        echo $actual;
        if ($disable)
        {
            if ($actual === 0)
            {
                $lead->esclusione += 32;
            }
        }
        else
        {
            if ($actual === 32)
            {
                $lead->esclusione -= 32;
            }
        }
        $lead->save();
        return array($lead->attributes(true), 200, true);
        return;
    }

    public static function setEsclusioneBit($piva, $disable, $bit) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $lead = \AR\Lead::all(array("conditions" => array("PartitaIva = ?", $piva)));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array($piva, 404, "No such lead");
        } catch (\Exception $e) {
            return array($piva, 500, "Error in retrieving lead");
        }
        if (!is_int($bit) || $bit < 0 || $bit > 7)
        {
            return array($piva, 404, "Invalid bit");
        }
        $val = (int) pow(2, $bit);
        $lead = $lead[0];
        $actual = ($lead->esclusione & $val);
        if ($disable)
        {
            if ($actual === 0)
            {
                $lead->esclusione += $val;
            }
        }
        else
        {
            if ($actual === $val)
            {
                $lead->esclusione -= $val;
            }
        }
        $lead->save();
        return array($lead->attributes(true), 200, $val);
        return;
    }

    public function ws___setEsclusioneBit() {
        $this->setJsonWS();
        $pars = array(
            "piva" => WolfMVC\RequestMethods::get("piva", false),
            "disable" => WolfMVC\RequestMethods::get("disable", false),
            "bit" => WolfMVC\RequestMethods::get("bit", "false")
        );
        if (!isset($pars["piva"]) || $pars["piva"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        if (!isset($pars["disable"]) || $pars["disable"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing disable value");
            return;
        }
        if (!isset($pars["bit"]) || $pars["bit"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing bit value");
            return;
        }
        if ($pars["disable"] == "true" || $pars["disable"] == "1")
            $pars["disable"] = true;
        else
            $pars["disable"] = false;
        $res = self::setEsclusioneBit($pars["piva"], $pars["disable"], (int) $pars["bit"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;
    }

    public function ws___setEsclusione32() {
        $this->setJsonWS();
        $pars = array(
            "piva" => WolfMVC\RequestMethods::get("piva", false),
            "disable" => WolfMVC\RequestMethods::get("disable", false)
        );
        if (!isset($pars["piva"]) || $pars["piva"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        if (!isset($pars["disable"]) || $pars["disable"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing disable value");
            return;
        }
        if ($pars["disable"] == "true" || $pars["disable"] == "1")
            $pars["disable"] = true;
        else
            $pars["disable"] = false;
        $res = self::setEsclusione32($pars["piva"], $pars["disable"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;
    }

    public static function setLeadPipeStatus($piva, $newpipe, $otherpars) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $lead = \AR\Lead::all(array("conditions" => array("PartitaIva = ?", $piva)));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array($piva, 404, "No such lead");
        } catch (\Exception $e) {
            return array($piva, 500, "Error in retrieving lead");
        }
        $lead = $lead[0];
        try {
            $pipelead = \AR\Pipelead::all(array("conditions" => array("piva = ?", $piva)));
        } catch (\ActiveRecord\RecordNotFound $e) {
            $pipelead = false;
        } catch (\Exception $e) {
            return array($piva, 500, "Error in retrieving lead in pipe");
        }
        if ($pipelead)
            $pipelead = $pipelead[0];
        else
            $pipelead = new AR\Pipelead(array(
                "piva" => $piva,
                "lotto" => 1,
                "punteggio_azienda" => $lead->punteggio_azienda,
                "localita" => $lead->localita
            ));
        $esclusione = (int) $lead->esclusione;
        switch ($newpipe) {
            case 0:
            case '0':
                try {
                    $pipelead->delete();
                    if (($esclusione & 2) === 2)
                    {
                        $lead->esclusione -= 2;
                    }
                    $lead->save();
                } catch (\ActiveRecord\ActiveRecordException $e) {
                    return array("Something went wrong updating piva " . $piva, 500, $e->getMessage());
                }
                break;
            case 2: case '2': case 3: case '3':
                return array("Something went wrong updating piva " . $piva, 400, "Pipe 2 and 3 are no longer used");
                break;
            default:
                if (!isset($otherpars["venditore"]))
                    return array($piva, 400, "Missing 'venditore' value");
                try {
                    $pipelead->tubo = $newpipe;
                    $pipelead->venditore = $otherpars["venditore"];
                    if (($esclusione & 2) !== 2)
                    {
                        $lead->esclusione += 2;
                    }
                    $pipelead->save();
                    $lead->save();
                } catch (\ActiveRecord\ActiveRecordException $e) {
                    return array("Something went wrong updating piva " . $piva, 500, $e->getMessage());
                }
                break;
        }

        return array(array(
                "lead" => $lead->attributes(true),
                "pipelead" => $pipelead ? $pipelead->attributes(true) : false
            ), 200, true);

        return;
    }

    public function ws___setLeadPipeStatus() {
        $this->setJsonWS();
        $pars = array(
            "piva" => WolfMVC\RequestMethods::post("piva", false),
            "newpipe" => WolfMVC\RequestMethods::post("newpipe", false),
            "otherpars" => WolfMVC\RequestMethods::post("otherpars", false)
        );
        if (!isset($pars["piva"]) || $pars["piva"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        if (!isset($pars["newpipe"]) || $pars["newpipe"] === FALSE)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing newpipe value " . $pars["newpipe"]);
            return;
        }

        $res = self::setLeadPipeStatus($pars["piva"], (int) $pars["newpipe"], $pars["otherpars"]);
        $this->_setResponseStatus($res[1]);
        echo json_encode(array($res[0], $res[2]));
        return;
    }

    public function ws___data() {
        $this->usePageComp("0.1");
        parent::ws___data();
    }

    public static function getLocalita($provincia, $regione) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $com = AR\Comuneitaliano::all(array(
                        "conditions" => array("regione = ? AND provincia = ?", $regione, $provincia)
            ));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(false, 404, false);
        }
        $out = array_map(function($c) {
            return $c->get_values_for(array("localita", "provincia_sigla", "provincia", "regione", "prefisso", "cap"));
        }, $com);
        return array($out, 200, true);
    }

    public function ws___getLocalita() {
        $this->setJsonWS();
        $pars = array(
            "regione" => WolfMVC\RequestMethods::get("regione", false),
            "provincia" => WolfMVC\RequestMethods::get("provincia", false)
        );
        if (!isset($pars["regione"]) || $pars["regione"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing regione");
        }
        if (!isset($pars["provincia"]) || $pars["provincia"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing provincia");
        }
        $results = self::getLocalita($pars["provincia"], $pars["regione"]);
        $this->_setResponseStatus($results[1]);
        echo json_encode(array($results[0], $results[2]));
        return;
    }

    public static function getAteco() {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $ateco = \AR\Ateco::all(array("select" => "desc_sezione, descriz, sottocategoria"));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(false, 404, false);
        }
        $out = array_map(function($c) {
            return $c->get_values_for(array("desc_sezione", "descriz", "sottocategoria"));
        }, $ateco);
        return array($out, 200, true);
    }

    public function ws___getAteco() {
        $this->setJsonWS();
        $results = self::getAteco();
        $this->_setResponseStatus($results[1]);
        echo json_encode(array($results[0], $results[2]));
        return;
    }

    public static function createLead($lead_vm) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        if (!isset($lead_vm["partitaiva"]) || !isset($lead_vm["ragionesociale"]) || !isset($lead_vm["indirizzo"]))
        {
            return array(false, 404, "Some mandatory value is missing, check passed vm");
        }
        try {
            $telefoni = explode(",", $lead_vm["telefono"]);
            foreach ($telefoni as $k => $t) {
                $telefoni[$k] = trim($t);
            }
            if (count($telefoni) === 0)
            {
                throw new \Exception("Missing phone number", 0, NULL);
            }
        } catch (\Exception $e) {
            return array(false, 404, $e->getMessage());
        }
        try {
            $fascia_fatturato = \AR\Fatturato::first(array("conditions" => array("fasciafatturato = ?", $lead_vm["fasciafatturato"])));
            if (!isset($lead_vm["localita"]) || !isset($lead_vm["provincia"]) || !isset($lead_vm["regione"]) || !isset($lead_vm["cap"]))
            {
                throw new \Exception("Missing some address data", 0, NULL);
            }
            $comune = \AR\Comuneitaliano::first(array("conditions" => array(
                            "localita = ? AND provincia = ? AND regione = ?",
                            $lead_vm["localita"],
                            $lead_vm["provincia"],
                            $lead_vm["regione"]
                        )
            ));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array(array(\AR\Fatturato::table()->last_sql, \AR\Comuneitaliano::table()->last_sql), 404, $e->getMessage());
        } catch (\Exception $e) {
            return array(array(\AR\Fatturato::table()->last_sql, \AR\Comuneitaliano::table()->last_sql), 404, $e->getMessage());
        }
        try {
            $lead = new \AR\Lead();
            $lead->partitaiva = $lead_vm["partitaiva"]; //***
            $lead->ragionesociale = $lead_vm["ragionesociale"]; //***
            $lead->indirizzo = $lead_vm["indirizzo"]; //***
            $lead->localita = $lead_vm["localita"]; //***
            $lead->cap = $lead_vm["cap"]; //***
            $lead->provincia = $lead_vm["provincia"]; //***
            $lead->regione = $lead_vm["regione"]; //***
            $lead->email_ai = $lead_vm["email"]; //***
            $lead->homepage_im = $lead_vm["homepage"]; //***
            $lead->formagiuridica = $lead_vm["formagiuridica"]; //***
            $lead->codicefiscale = $lead_vm["partitaiva"]; //***
            $lead->fasciafatturato = $lead_vm["fasciafatturato"]; //***
            $lead->classedipendenti = $lead_vm["classedipendenti"]; //***
            $lead->source = "MAN"; //***
            $lead->esclusione = "0"; //***
            $lead->punteggio_azienda_old = "0"; //***
            $lead->punteggio_azienda = $fascia_fatturato->fasciafatturato_voto; //***
            $lead->provincia_sigla = $comune->provincia_sigla;
            if (isset($lead_vm["attivita_ateco"]))
            {
                if (isset($lead_vm["attivita_ateco"]["sottocategoria"]))
                    $lead->codiceateco = $lead_vm["attivita_ateco"]["sottocategoria"];
                if (isset($lead_vm["attivita_ateco"]["descriz"]))
                    $lead->attivita_ateco = $lead_vm["attivita_ateco"]["descriz"];
            }
            $lead->telefono = $telefoni[0]; //***
            $lead->save();
        } catch (\ActiveRecord\ActiveRecordException $e) {
            return array(false, 404, $e->getMessage());
        }
        if (count($telefoni) > 1)
        {
            try {
                foreach ($telefoni as $k => $t) {
                    AR\Numerotelefono::create(array(
                        "partitaiva" => $lead_vm["partitaiva"],
                        "tel" => $t,
                        "sorgente" => "MAN"
                    ));
                }
            } catch (\ActiveRecord\ActiveRecordException $e) {
                return array(false, 404, $e->getMessage());
            } catch (\Exception $e) {
                return array(false, 404, $e->getMessage());
            }
        }
        return array($lead->attributes(), 200, true);
    }

    public function ws___createLead() {
        $this->setJsonWS();
        $pars = array(
            "lead_vm" => WolfMVC\RequestMethods::post("lead_vm", false),
        );
        if (!isset($pars["lead_vm"]) || $pars["lead_vm"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing lead_vm");
        }
        $results = self::createLead($pars["lead_vm"]);
        $this->_setResponseStatus($results[1]);
        echo json_encode(array($results[0], $results[2]));
        return;
    }

    public static function getLead($piva, $withAmends) {
        ActiveRecord\Config::initialize(
                \WolfMVC\Registry::get("activerecord_initializer")
        );
        try {
            $lead = AR\Lead::first(array(
                        "conditions" => array("partitaiva = ?", $piva),
                        "include" => array("pipe", "lock", "numeritelefono", "leadamend")
            ));
        } catch (\ActiveRecord\RecordNotFound $e) {
            return array($piva, 404, false);
        } catch (\Exception $e) {
            return array($piva, 500, false);
        }
        $out = array();
        $out["lead"] = $lead->attributes();
        if (count($lead->pipe))
        {
            $out["pipe"] = $lead->pipe[0]->attributes(true);
        }
        if (count($lead->lock))
        {
            $out["lock"] = $lead->lock[0]->attributes(true);
        }
        $out["lead"]["telefono"] = array($out["lead"]["telefono"]);
        if (count($lead->numeritelefono))
        {
            foreach ($lead->numeritelefono as $kk => $tt) {
                array_push($out["lead"]["telefono"], $tt->telefono);
            }
        }
        if (count($lead->leadamend))
        {
            foreach ($lead->leadamend as $kk => $ll) {
                if ($ll->cancella == 0 && !empty($ll->telefono)){
                    array_push($out["lead"]["telefono"], $ll->telefono);    
                }
                else if ($ll->cancella == 1 && !empty($ll->telefono)){
                    $key = array_search($ll->telefono, $out["lead"]["telefono"]);
                    if ($key !== FALSE){
                        array_splice($out["lead"]["telefono"],$key,1);
                    }
                }
                
            }
//            if ($withAmends){ // da implementare in futuro
//                foreach($lead->leadamend as $k => $l){
//                    if ($l->cancella == 0){
//                        
//                    }
//                    else {
//                        if (!empty($l->ragionesociale)){
//                            $out["lead"]["ragionesociale"] = $l->ragionesociale;
//                        }
//                        if (!empty($l->indirizzo)){
//                            $out["lead"]["indirizzo"] = $l->indirizzo;
//                        }
//                        if (!empty($l->localita)){
//                            $out["lead"]["localita"] = $l->localita;
//                        }
//                        if (!empty($l->cap)){
//                            $out["lead"]["cap"] = $l->cap;
//                        }
//                        if (!empty($l->provincia)){
//                            $out["lead"]["provincia"] = $l->provincia;
//                        }
//                        if (!empty($l->regione)){
//                            $out["lead"]["regione"] = $l->regione;
//                        }
//                        if (!empty($l->email)){
//                            $out["lead"]["email"] = $l->email;
//                        }
//                        if (!empty($l->sito)){
//                            $out["lead"]["sito"] = $l->sito;
//                        }
//                        if (!empty($l->formagiuridica)){
//                            $out["lead"]["formagiuridica"] = $l->formagiuridica;
//                        }
//                        if (!empty($l->fasciafatturato)){
//                            $out["lead"]["fasciafatturato"] = $l->fasciafatturato;
//                        }
//                        if (!empty($l->classedipendenti)){
//                            $out["lead"]["classedipendenti"] = $l->classedipendenti;
//                        }
//                    }
//                }
//            }

            $out["leadamend"] = array_map(function($x) {
                return $x->attributes(true);
            }, $lead->leadamend);
        }
        return array($out, 200, true);
    }

    public function ws___getLead() {
        $this->setJsonWS();
        $pars = array(
            "piva" => WolfMVC\RequestMethods::get("piva", false),
            "withAmends" => WolfMVC\RequestMethods::get("withAmends", false)
        );
        if (!isset($pars["piva"]) || $pars["piva"] === false)
        {
            $this->_setResponseStatus(400);
            echo json_encode("Missing piva");
            return;
        }
        $results = self::getLead($pars["piva"], $pars["withAmends"]);
        $this->_setResponseStatus($results[1]);
        echo json_encode(array($results[0], $results[2]));
        return;
    }

}
