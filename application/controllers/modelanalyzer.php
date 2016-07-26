<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;
use WolfMVC\Registry as Registry;
use WolfMVC\RequestMethods as RequestMethods;
use WolfMVC\Template\Component\Formcomponent as FC;

class Modelanalyzer extends Controller {

    protected $_conf;

    /**
     *
     * @var boolean
     */
    protected $_dirpresent = false;

    /**
     *
     * @var boolean
     */
    protected $_filepresent = false;

    public function __construct($options = array()) {
        parent::__construct($options);
        $this->checkdir();
    }

    /**
     * 
     */
    public function checkdir($dirname = "") {
        if (is_dir(APP_PATH . "/application/configuration/model/" . $dirname))
        {
//            echo "Cartella configurazione presente<br>";
            $this->_dirpresent = true;
        }
        else
        {
//            echo "Cartella configurazione non presente<br>";
            mkdir(APP_PATH . "/application/configuration/model/" . $dirname);
            if (is_dir(APP_PATH . "/application/configuration/model/" . $dirname))
            {
//                echo "Cartella configurazione creata<br>";
                chmod(APP_PATH . "/application/configuration/model/" . $dirname, 0775);
                $this->_dirpresent = true;
            }
            else
            {
//                echo "Errore creazione cartella configurazione.<br>";
            }
        }
    }

    public function checkfile($filename = "") {
        if ($filename === "")
        {
            $filename = "model";
        }
        if (is_file(APP_PATH . "/application/configuration/model/" . $filename . ".ini"))
        {
            $this->_filepresent = true;
        }
        else
        {

            touch(APP_PATH . "/application/configuration/model/" . $filename . ".ini");
            if (is_file(APP_PATH . "/application/configuration/model/" . $filename . ".ini"))
            {
                chmod(APP_PATH . "/application/configuration/model/" . $filename . ".ini", 0775);
                $this->_filepresent = true;
            }
            else
            {
                
            }
        }
    }

    /**
     * @protected
     */
    public function script_including() {

//        $this->loadScript("utils.js");
//        
//        $this->loadScript("ngbootstrap.min.js");
//        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
//        $this->loadScript("ngsortable.js");
//        $this->loadScript("core/datamodule/data-0.3.js");
//        $this->loadScript("core/filteredtable.js");
//        $this->loadScript("core/themask.js");
//        $this->loadScript("core/filteredtable.css");
//        $this->loadScript("core/angdad.js");
//        $this->loadScript("core/tsnwclient.js");
//        $this->loadScript("core/sinottico/sinottico-0.1.js");
//        $this->loadScript("core/logger/logger-0.1.js");
//        $this->loadScript("core/smartButton/smartButton-0.1.js");
//        $this->loadScript("lodash.js");
//        $this->loadScript("core/RGraph/libraries/RGraph.common.core.js");
//        $this->loadScript("core/RGraph/libraries/RGraph.gantt.js");
//        $this->loadScript("core/RGraph/libraries/RGraph.common.dynamic.js");
//        $this->loadScript("angular-google-maps.min.js");
//        $this->loadScript("core/common/common-0.1.js");
//        $this->loadScript("core/common/commonInclude.js");

        $view = $this->getLayoutView();
        $view->set("moduleName", "MODELLI");
    }

    /**
     * @before script_including
     */
    public function index() {

        $view = $this->getActionView();
        $view->set("actions", array(
            "Elenco modelli" => $this->pathTo($this->nameofthiscontroller(), "analyze", array()),
//            "Copertura lavoro" => $this->pathTo($this->nameofthiscontroller(), "jobcoverage", array()),
//            "Situazione liste" => $this->pathTo($this->nameofthiscontroller(), "situazioneliste", array())
        ));
    }

    /**
     * @before script_including
     */
    public function analyze() {
        $this->loadScript("jquery.js");
        $this->loadScript("lodash.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("angular-animate.min.js");
        $this->loadScript("core/smartButton/smartButton-0.1.js");
        $this->loadScript("core/datamodule/data-0.4.js");
        $this->loadScript("core/themask/themask-0.3.js");
        $this->loadScript("core/common/common-0.1.js");
        $this->loadScript("core/common/commonInclude.js");
        $this->loadScript("component/treeMenu/treeMenu-0.1.js");
        $this->loadScript("component/treeMenu/treeMenu.css");
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("Gestione modelli" => "modelanalyzer", "Elenco modelli" => "last")));
        $view = $this->getActionView();
        $view->set("root", '$root');
        $view->set("index", '$index');

        return;


        $view = $this->getActionView();

        $mmodn = $this->_parameters[0];
        $mmods = \WolfMVC\Censor::get("model");
        $mmodname = $mmods[$mmodn];
        $mmodname = $mmodname [0];
        $this->checkfile($mmodname);
        if (!($this->_filepresent))
        {
            die('<br>Impossibile continuare!');
        }
        $link = new \mysqli("localhost", "root", "root", "vtiger540"); //            $link = new \mysqli("vtiger-rec.cjybxwcd2ntt.us-west-2.rds.amazonaws.com", "vtiger", "144Lisboa", "vtiger540");
        $sql = "SELECT DISTINCT `value` FROM external_tsnw_model WHERE `macromodel` = '" . $mmodname . "' AND `model` = 'models' AND `key` = 'models'";
        echo $sql;
        $result = $link->query($sql);
        echo $link->error;
        if ($result->num_rows > 0)
        {
            $riga = mysqli_fetch_assoc($result);
            $models = explode("|", $riga["value"]);
        }
        else
        {
            $models = array();
        }


        $view->set("mmodname", "Nome del macro-modello: " . $mmodname);
        $back = "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "\">Torna a visualizzazione Macro-modelli</a>";
        $newmodel = "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/newmodel/" . $mmodn . "\">Inserisci nuovo modello</a>";
        $view->set("navigator", $back . "<br>" . $newmodel);
        $html = "<table border=\"1\" width=\"100%\"><tr>"
                . "<th>Nome modello</th>"
                . "<th>Modifica modello</th>"
                . "<th>Tabelle coinvolte</th>"
                . "<th>Riferimento ad altri modelli</th>"
                . "</tr>";
        foreach ($models as $key => $mod) {
            $html .= "<tr><td>";
            $html .= "<a name=\"" . $mod . "\">" . $mod . "</a></td>"
                    . "<td><a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/newmodel/" . $mmodn . "/" . $mod . "\">Modifica</a>&nbsp;&nbsp;"
                    . "</td>"
                    . "<td>";
//            if (isset($ini[$mmodname . "." . $mod . '.last_sync'])) {
//                $html .= $ini[$mmodname . "." . $mod . '.last_sync'];
//            } else {
//                $tables = $ini[$mmodname . "." . $mod . '.DBTables'];
//                $tables = trim($tables, "[]");
//                $tables = str_replace(["\"", ","], ["", ", "], $tables);
//                $html .= $tables;
//                $table_sync = false;
//            }
            $html .="</td></tr>\n";
        }
        $html .="</table>";
        $view->set("html", $html);
    }

    public function makeSelectAll() {
        if (!isset($this->_parameters[0]))
        {
            $out = array("status" => "error", "error" => "unspecified macro-model code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $mmodn = $this->_parameters[0]; //macro-modello
        if (!isset($this->_parameters[1]))
        {
            $out = array("status" => "error", "error" => "unspecified model name");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $mname = $this->_parameters[1]; //modello
        $mmods = \WolfMVC\Censor::get("model");
        $mmodname = $mmods[$mmodn];
        $mmodname = $mmodname [0];
        $smm = new WolfMVC\Smm(array("mmodname" => $mmodname, "mname" => $mname));
        $smm->makeSelectAll();
        echo $smm->savedQuery["selectAll"];
        return;
    }

    public function test() {
        ActiveRecord\Config::initialize(
                function($cfg) {
            $cfg->set_model_directory('../application/configuration/models');
            $cfg->set_connections_from_system(array("vtiger" => "vtiger"),"vtiger");
        }
        );
        
        echo "<pre>";
//        $account = \AR\Vtaccount::all(array("limit" => 10, 'include' => array('cf', 'ent')));
//        ActiveRecord\SQLBuilder("vtiger",  \AR\Vtaccount::table());
//        $account_out = array_map(function($x){
//            $vett = $x->attributes();
//            $vett["cf"] = $x->cf->attributes();
//            $vett["ent"] = $x->ent->attributes();
//            return $vett;
//        }, $account);
//        echo json_encode($account_out);
        
        echo "</pre>";
    }

    /**
     * @before script_including
     */
    public function newmodel() {
        $this->loadScript("jquery.js");
        $this->loadScript("jquery-ui.js");
        $this->loadScript("utils.js");
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/modelanalyzer/newmodelinteraction.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/modelanalyzer/newmodelclasses.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/modelanalyzer/newmodel2.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/modelanalyzer/newmodel.js\"></script>";

        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/modelanalyzer/newmodelaux.js\"></script>";
        $this->_system_js_including .="<link href=\"" . SITE_PATH . "css/modelanalyzer/newmodel.css\" rel=\"stylesheet\">";
//        $this->_system_js_including .="<style type=\"text/css\">\n"
//          . ".previoustable {\n"
//          . "align: center;\n"
//          . "text-align: center;\n"
//          . "width: 100%;\n"
//          . "}\n"
//          . "</style>\n";
        $view = $this->getActionView();
        $mmodn = $this->_parameters[0]; //macro-modello
//        $view->set("campo", $campo1);
        $mmods = \WolfMVC\Censor::get("model");
        $mmodname = $mmods[$mmodn];
        $mmodname = $mmodname [0];
        $ini = parse_ini_file(APP_PATH . "/application/configuration/model/" . $mmodname . ".ini");
    }

    /**
     * @before disablerender
     */
    public function ws___retrievemodel() {
        header('Content-type: application/json');
        $out = array();
        if (!isset($this->_parameters[0]))
        {
            $out = array("status" => "error", "error" => "unspecified macro-model code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $mmodn = $this->_parameters[0]; //macro-modello
        if (!isset($this->_parameters[1]))
        {
            $out = array("status" => "error", "error" => "unspecified model name");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $mname = $this->_parameters[1]; //modello
        $mmods = \WolfMVC\Censor::get("model");
        $mmodname = $mmods[$mmodn];
        $mmodname = $mmodname [0];

        $link = new \mysqli("localhost", "root", "root", "vtiger540"); //            $link = new \mysqli("vtiger-rec.cjybxwcd2ntt.us-west-2.rds.amazonaws.com", "vtiger", "144Lisboa", "vtiger540");
        $sql = "SELECT `macromodel`,`model`,`key`,`value` FROM external_tsnw_model WHERE macromodel = '" . $mmodname . "' AND model = '" . $mname . "'";
        $out = array();
        $result = $link->query($sql);
        while ($riga = mysqli_fetch_assoc($result)) {
            $out[$riga["key"]] = $riga["value"];
        }
        echo json_encode($out, JSON_FORCE_OBJECT);
    }

    /**
     * @before disablerender
     */
    public function ws___savenewmodel() {
        header('Content-type: application/json');
        $out = array();
        if (!isset($this->_parameters[0]))
        {
            $out = array("status" => "error", "error" => "unspecified macro-model code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $mmodn = $this->_parameters[0]; //macro-modello
        $mmods = \WolfMVC\Censor::get("model");
        $mmodname = $mmods[$mmodn];
        $mmodname = $mmodname [0];
        $ini = array();
        $link = new \mysqli("localhost", "root", "root", "vtiger540"); //            $link = new \mysqli("vtiger-rec.cjybxwcd2ntt.us-west-2.rds.amazonaws.com", "vtiger", "144Lisboa", "vtiger540");
        $sql = "SELECT `macromodel`,`model`,`key`,`value` FROM external_tsnw_model WHERE macromodel = '" . $mmodname . "'";
        $result = $link->query($sql);
        while ($riga = mysqli_fetch_assoc($result)) {
            $ini[$riga["macromodel"] . "." . $riga["model"] . "." . $riga["key"]] = $riga["value"];
        }
        if (isset($ini[$mmodname . ".models.models"]))
        {
            $models = $ini[$mmodname . ".models.models"];
            $models = explode("|", $models);
        }
        else
        {
            $models = array();
        }
        $data = array();
//        print_r($_POST);
        $mname = $_POST["modelname"];
        if (array_search($mname, $models) === FALSE)
            array_push($models, $mname);
        $dbtables = json_decode($_POST["DBTables"]);
        foreach ($dbtables as $key => $dbtable) {
            $table = $_POST["DBTable_" . str_replace(".", "_", $dbtable)];
            $tabledec = json_decode($table);
            $modtables = $tabledec->MODTables;
            foreach ($modtables as $key2 => $modtable) {
                $mtable = $_POST["MODTable_" . $modtable];
                $mtabledec = json_decode($mtable);
                foreach ($mtabledec->MODElements as $key3 => $modelement) {
                    $melement = $_POST["MODElement_" . $modelement];
                    $melementdec = json_decode($melement);
                    $data[$mmodname . "." . $mname . "." . "MODElement_" . $modelement] = str_ireplace("\"", "\\\"", $melement);
                }
                foreach ($mtabledec->MODRelations as $key4 => $modrelation) {
                    $mrelation = $_POST["MODRelation_" . $modrelation];
                    $mrelationdec = json_decode($mrelation);
                    $data[$mmodname . "." . $mname . "." . "MODRelation_" . $modrelation] = str_ireplace("\"", "\\\"", $mrelation);
                }
                $data[$mmodname . "." . $mname . "." . "MODTable_" . $modtable] = str_ireplace("\"", "\\\"", $mtable);
            }
            $data[$mmodname . "." . $mname . "." . "DBTable_" . $dbtable] = str_ireplace("\"", "\\\"", $_POST["DBTable_" . str_replace(".", "_", $dbtable)]);
        }
        $data[$mmodname . "." . $mname . "." . "hash"] = str_ireplace("\"", "\\\"", $_POST["hash"]);
        $data[$mmodname . "." . $mname . "." . "maintable"] = str_ireplace("\"", "\\\"", $_POST["maintable"]);
        $data[$mmodname . "." . $mname . "." . "DBTables"] = str_ireplace("\"", "\\\"", $_POST["DBTables"]);
        $data[$mmodname . "." . $mname . "." . "model"] = str_ireplace("\"", "\\\"", $_POST["model"]);
        $data[$mmodname . "." . $mname . "." . "first-form"] = str_ireplace("\"", "\\\"", $_POST["first-form"]);
        $data[$mmodname . ".models"] = join("|", $models);
//        echo "cancello le chiavi che iniziano con ".$mmodname . "." . $mname;


        $sql = "DELETE FROM external_tsnw_model WHERE macromodel = '" . $mmodname . "' AND model = '" . $mname . "'";
        $result = $link->query($sql);
        if ($link->error !== "")
        {
            $out = array("status" => "error", "error" => $link->error);
            echo json_encode($out, JSON_FORCE_OBJECT);
            return;
        }
        $sql = "INSERT INTO external_tsnw_model (`macromodel`,`model`,`key`,`value`) VALUES";
        $row = "({macromodel},{model},{key},{value})";
        foreach ($data as $k => $value) {
            $macromodel = "model";
            $kk = str_replace($macromodel . ".", "", $k);
            $kkexp = explode(".", $kk, 2);
            $model = $kkexp[0];
            $key = str_replace($model . ".", "", $kk);
            $val = trim(str_replace("'", "\'", $value));
//                $sql .= "<br><br><br>";
            $sql .= " " . str_replace(array("{macromodel}", "{model}", "{key}", "{value}"), array("'" . $macromodel . "'", "'" . $model . "'", "'" . $key . "'", "'" . $val . "'"), $row) . ",";
        }
        $sql[strlen($sql) - 1] = " ";
        $result = $link->query($sql);
        if ($link->error !== "")
        {
            $out = array("status" => "error", "error" => $link->error);
            echo json_encode($out, JSON_FORCE_OBJECT);
            return;
        }
        $out = array("status" => "ok", "changedrows" => $link->affected_rows);
        echo json_encode($out, JSON_FORCE_OBJECT);
        exit;
    }

    /**
     * @before script_including
     */
    public function modeldetails() {
        $view = $this->getActionView();
        $modn = $this->_parameters[0];
        $tab = $this->_parameters[1];
        $dbs = \WolfMVC\Censor::get("database");
        $modname = $dbs[$modn];
        $modname = $modname [0];
        $back = "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/analyze/" . $modn . "\">Torna a elenco tabelle</a>";
        $view->set("navigator", $back);
        $this->checkfile($modname);
        if (!($this->_filepresent))
        {
            die('<br>Impossibile continuare!');
        }

        $script = "<script>\n"
                . "function ()</script>";
        $links = parse_ini_file(APP_PATH . "/application/configuration/database/" . $modname . "_link.ini");
//        recupero form post
        if (isset($_REQUEST['addlink']) && isset($_REQUEST['fromlink']))
        {
            if (isset($links[$_REQUEST['fromlink'] . " >> " . $_REQUEST['fromlink']]))
            {
                $campilink1 = $links[$_REQUEST['fromlink']];
            }
            else
            {
                $campilink1 = "";
            }
            if (isset($links[$_REQUEST['addlink']]))
            {
                $campilink2 = $links[$_REQUEST['addlink']];
            }
            else
            {
                $campilink2 = "";
            }
            $campilink1 = explode("|", $campilink1);
            $campilink2 = explode("|", $campilink2);
            if (array_search($_REQUEST['addlink'], $campilink1) === FALSE)
                array_push($campilink1, $_REQUEST['addlink']);
            if (array_search($_REQUEST['fromlink'], $campilink2) === FALSE)
                array_push($campilink2, $_REQUEST['fromlink']);
            $campilink1 = join("|", $campilink1);
            $campilink2 = join("|", $campilink2);
            $data = array($_REQUEST['fromlink'] => $campilink1, $_REQUEST['addlink'] => $campilink2);
            $this->writeini(APP_PATH . "/application/configuration/database/" . $modname . "_link.ini", $data);
        }
        $links = parse_ini_file(APP_PATH . "/application/configuration/database/" . $modname . "_link.ini");


        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $modname . ".ini");
        $fields = $this->initoarray($ini, $modname . "." . $tab);
        $tables = $ini[$modname . ".tables"];
        $tables = explode("|", $tables);
        $allfields = array();
        foreach ($tables as $table) {
            if ($table == $tab)
                continue;
            $allfields[$table] = $ini[$modname . "." . $table . ".fields"];
            $allfields[$table] = explode("|", $allfields[$table]);
        }
        $view->set("dbname", "Nome del db: " . $modname);
        $view->set("tabname", "<br>Nome del db: " . $tab . "<br><br>");
        $html = "<table border=\"1\" width=\"100%\"><tr>";
        $html .= "<th>Campo</th>";
        $html .= "<th>Tipo</th>";
        $html .= "<th>Key</th>";
        $html .= "<th>Null</th>";
        $html .= "<th>Extra</th>";
        $html .= "<th>Collega a:</th>";
        $html .= "<th>Campo</th>";
        $html .= "</tr>";
        foreach ($fields as $key => $field) {
            if ($key === "fields" || $key === "last_sync")
                continue;
            $html .= "<tr>";
            $html .= "<td>" . $key . "</td>";
            $html .= "<td>" . $field['type'] . "</td>";
            $html .= "<td>" . $field['key'] . "</td>";
            $html .= "<td>" . $field['null'] . "</td>";
            $html .= "<td>" . $field['extra'] . "</td>";
            $html .= "<td>"
                    . "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/setlink/" . $modn . "/" . $tab . "/" . $key . "\">Aggiungi nuova relazione</a>";
            if (isset($links[$modname . "." . $tab . "." . $key]))
            {
                $lnk = $links[$modname . "." . $tab . "." . $key];
                $lnk = explode("|", $lnk);
                if (count($lnk) > 0)
                {
                    $html .= "<ul>";
                    foreach ($lnk as $l) {
                        if ($l == "")
                            continue;
                        $html .= "<li>" . $l . "</li>";
                    }
                    $html .= "</ul>";
                }
            }
            else
            {
                $lnk = array();
            }



            $html .= "</td>";
            $html .= "</tr>";
        }


        $html .= "</table>";
        $view->set("html", $html);
    }

    public function cmp($a, $b) {

        $asplit = explode(".", $a);
        $bsplit = explode(".", $b);
//        echo "comp " . $a . "(" . count($asplit) . ")" . $b . "(" . count($bsplit) . ")" . "<br>";
        for ($i = 0; $i < min(count($asplit), count($bsplit)); $i++) {
//            echo "comp " . $asplit[$i] . ">" . $bsplit[$i] . "<br>";
            if ($asplit[$i] > $bsplit[$i])
            {
//                echo "maggiore<br>";
                return 1;
            }
            elseif ($asplit[$i] < $bsplit[$i])
            {
                return -1;
            }
        }
    }

    public function migrate() {
        $link = new \mysqli("localhost", "root", "root", "vtiger540"); //            $link = new \mysqli("vtiger-rec.cjybxwcd2ntt.us-west-2.rds.amazonaws.com", "vtiger", "144Lisboa", "vtiger540");
        $sql = "INSERT INTO external_tsnw_model (`macromodel`,`model`,`key`,`value`) VALUES";
        $row = "({macromodel},{model},{key},{value})";
        $ini = parse_ini_file(APP_PATH . "/application/configuration/model/model.ini");
        $out = array();
        foreach ($ini as $k => $value) {
            $macromodel = "model";
            $kk = str_replace($macromodel . ".", "", $k);
            $kkexp = explode(".", $kk, 2);
            $model = $kkexp[0];
            $key = str_replace($model . ".", "", $kk);
            $val = trim(str_replace("'", "\'", $value));
//                $sql .= "<br><br><br>";
            $sql .= " " . str_replace(array("{macromodel}", "{model}", "{key}", "{value}"), array("'" . $macromodel . "'", "'" . $model . "'", "'" . $key . "'", "'" . $val . "'"), $row) . ",";
        }
        $sql[strlen($sql) - 1] = " ";

//        $result = $link->query($sql);
//        print_r($result);
        echo $link->affected_rows;
        echo $link->error;
        echo "<br><br><br>";
//        echo $sql;
        $sql = "SELECT * FROM external_tsnw_model";
        $result2 = $link->query($sql);
        while ($riga = mysqli_fetch_assoc($result2)) {
            echo "<br>";
            echo $riga["model"];
            echo "<br>";
            echo $riga["key"];
            echo "<br>";
            echo $riga["value"];
        }
    }

    public function writeini($filename, $data) {
        if (is_writable($filename))
        {

// In questo esempio apriamo $filename in append mode.
// Il puntatore del file è posizionato in fondo al file
// è qui che verrà posizionato $somecontent quando eseguiremo fwrite().
            echo "scrivo su " . $filename;
            $present = array();
            $i = 1;
            $handle = fopen($filename, "r");
            if ($handle)
            {
                while (($line = fgets($handle)) !== false) {
                    echo $i++;
//                    echo $line;
                    $line = str_replace(array("{", "}", "|", "&", "~", "!", "[", "(", ")", '"op":"="'), array("###ESCAPE1###", "###ESCAPE2###", "###ESCAPE3###", "###ESCAPE4###", "###ESCAPE5###", "###ESCAPE6###", "###ESCAPE7###", "###ESCAPE8###", "###ESCAPE9###", "###ESCAPE10###"), $line);
                    $arr = parse_ini_string($line);
                    foreach ($arr as $k => $a) {
                        $present[$k] = str_replace(array("###ESCAPE1###", "###ESCAPE2###", "###ESCAPE3###", "###ESCAPE4###", "###ESCAPE5###", "###ESCAPE6###", "###ESCAPE7###", "###ESCAPE8###", "###ESCAPE9###", "###ESCAPE10###"), array("{", "}", "|", "&", "~", "!", "[", "(", ")", '"op":"="'), $a);
                    }
                }
            }
            fclose($handle);
            echo "c'era questo:";
            print_r($present);
            echo "arriva questo:";
            print_r($data);
            $data = array_merge($present, $data);
            echo "Scrivo questo:";
            print_r($data);
            uksort($data, array($this, "cmp"));
            if (!$handle = fopen($filename, 'w'))
            {
                exit;
            }
            foreach ($data as $key => $value) {
                $value = str_ireplace("\n", "", $value);
                if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE)
                {
                    exit;
                }
            }
            fclose($handle);
        }
    }

    public function removefromini($filename, $data) {
        if (is_writable($filename))
        {

// In questo esempio apriamo $filename in append mode.
// Il puntatore del file è posizionato in fondo al file
// è qui che verrà posizionato $somecontent quando eseguiremo fwrite().
            $present = parse_ini_file($filename);
//            $present_keys = array_keys($present);
//            $data_keys = array_keys($data);
            $data = array_diff_key($present, $data);
            uksort($data, array($this, "cmp"));
            if (!$handle = fopen($filename, 'w'))
            {
                exit;
            }
            foreach ($data as $key => $value) {
                if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE)
                {
                    exit;
                }
            }
            fclose($handle);
        }
    }

    public function removeexactlyfromini($filename, $data) {
        if (is_writable($filename))
        {

// In questo esempio apriamo $filename in append mode.
// Il puntatore del file è posizionato in fondo al file
// è qui che verrà posizionato $somecontent quando eseguiremo fwrite().
            $present = parse_ini_file($filename);
//            $present_keys = array_keys($present);
//            $data_keys = array_keys($data);
            foreach ($data as $keytoremove => $valuetoremove) {
                foreach ($present as $key => $value) {
                    if (($keytoremove == $key) && ($valuetoremove == $value))
                    {
                        unset($present[$key]);
                    }
                }
            }
            if (!$handle = fopen($filename, 'w'))
            {
                exit;
            }
            foreach ($present as $key => $value) {
                if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE)
                {
                    exit;
                }
            }
            fclose($handle);
        }
    }

    public function initoarray($ini, $prefix) {
        $output = array();
        foreach ($ini as $key => $value) {
            if ($key === $prefix)
            { //(#)
                $output[0] = $value;
            }
            else
            {
                if (strpos($key, $prefix . ".") === 0)
                {
                    $kk = str_ireplace($prefix . ".", "", $key);
                    $out = $value;
                    $output[$kk] = $value;
                }
            }
        }

        return $output;
    }

    public function removeinifromprefix($filename, $prefix) {
        $ini = parse_ini_file($filename);
//            $present_keys = array_keys($present);
//            $data_keys = array_keys($data);
        foreach ($ini as $key => $value) {
            if (($key === $prefix) || (strpos($key, $prefix . ".") === 0))
            {
                unset($ini[$key]);
            }
        }
        if (!$handle = fopen($filename, 'w'))
        {
            exit;
        }
        foreach ($ini as $key => $value) {
            if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE)
            {
                exit;
            }
        }
        fclose($handle);
    }

}
