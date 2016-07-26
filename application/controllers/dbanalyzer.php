<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;
use WolfMVC\Registry as Registry;
use WolfMVC\RequestMethods as RequestMethods;
use WolfMVC\Template\Component\Formcomponent as FC;

class Dbanalyzer extends Controller {

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
    public function checkdir() {
        if (is_dir(APP_PATH . "/application/configuration/database")) {
//            echo "Cartella configurazione presente<br>";
            $this->_dirpresent = true;
        } else {
            echo "Cartella configurazione non presente<br>";
            mkdir(APP_PATH . "/application/configuration/database");
            if (is_dir(APP_PATH . "/application/configuration/database")) {
                echo "Cartella configurazione creata<br>";
                chmod(APP_PATH . "/application/configuration/database", 0775);
                $this->_dirpresent = true;
            } else {
                echo "Errore creazione cartella configurazione.<br>";
            }
        }
    }

    public function checkfile($dbname) {
        if (is_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini")) {
            $this->_filepresent = true;
        } else {

            touch(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
            if (is_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini")) {
                chmod(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", 0775);
                $this->_filepresent = true;
            } else {
                
            }
        }
        if (is_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini")) {
            $this->_filepresent = true;
        } else {

            touch(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini");
            if (is_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini")) {
                chmod(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini", 0775);
                $this->_filepresent = true;
            } else {
                
            }
        }
    }

    /**
     * @protected
     */
    public function script_including() {

        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
    }

    /**
     * @before script_including
     */
    public function index() {

//questa istruzione dovrà dipendere dalla configurazione
        $view = $this->getActionView();
        $html = "<table border=\"1\" width=\"100%\"><tr>"
                . "<th>Nome database</th>"
                . "<th>Analisi db</th>"
                . "</tr>";
        $dbs = \WolfMVC\Censor::get("database");
        foreach ($dbs as $key => $db) {
            $html .="<tr>"
                    . "<td>" . $db[0] . "</td>"
                    . "<td><a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/analyze/" . $key . "\">Analisi</a></td>"
                    . "</tr>\n";
        }
        $html .="</table>";
        $view->set("html", $html);
    }

    /**
     * @before script_including
     */
    public function analyze() {
        $view = $this->getActionView();

        $dbn = $this->_parameters[0];
        $dbs = \WolfMVC\Censor::get("database");
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            die('<br>Impossibile continuare!');
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        if (isset($ini[$dbname . '.knowntables'])) {
            $data = $ini[$dbname . '.knowntables'];
        } else {
            $data = "";
        }
        $data = explode("|", $data);
        if (isset($ini[$dbname . ".tables"])) {
            $tables = $ini[$dbname . ".tables"];
            $tables = explode("|", $tables);
        }
        else {
            $tables = array();
        }
        if (isset($ini[$dbname . '.last_sync'])) {
            $last_sync = $ini[$dbname . '.last_sync'];
        } else {
            $last_sync = false;
        }
        $view->set("dbname", "Nome del db: " . $dbname);
        $view->set("last_sync", "Ultima sincronizzazione completa: " . (($last_sync) ? $last_sync : "mai effettuata"));
        $view->set("sync", "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/syncdb/" . $dbn . "\">Sincronizza DB</a>");
        $back = "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "\">Torna a visualizzazione DBs</a>";
        $view->set("navigator", $back);
        $html = "<table border=\"1\" width=\"100%\"><tr>"
                . "<th>Nome tabella</th>"
                . "<th>Dettagli tabella</th>"
                . "<th>Ultima sincronizzazione</th>"
                . "</tr>";
        $table_sync = true;
        foreach ($data as $key => $tab) {
            $html .= "<tr><td";
            if (array_search($tab, $tables) !== FALSE) {
                $html .= " style=\"color: #00ff00;\"";
            }
            $html .= "><a name=\"" . $tab . "\">" . $tab . "</a></td>"
                    . "<td><a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/tabledetails/" . $dbn . "/" . $tab . "\">Analisi</a>&nbsp;&nbsp;"
                    . "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/synctable/" . $dbn . "/" . $tab . "\">Sync</a>&nbsp;&nbsp;"
                    . "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/unsynctable/" . $dbn . "/" . $tab . "\">Unsync</a>"
                    . "</td>"
                    . "<td>";
            if (isset($ini[$dbname . "." . $tab . '.last_sync'])) {
                $html .= $ini[$dbname . "." . $tab . '.last_sync'];
            } else {
                $html .= "mai effettuata";
                $table_sync = false;
            }
            $html .="</td></tr>\n";
        }
        $html .="</table>";
        $view->set("html", $html);
    }

    /**
     * @before script_including
     */
    public function setlink() {
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/dbanalyzer/setlink.js\"></script>";
        $this->_system_js_including .="<style type=\"text/css\">\n"
                . ".previoustable {\n"
                . "align: center;\n"
                . "text-align: center;\n"
                . "width: 100%;\n"
                . "}\n"
                . "</style>\n";
        $view = $this->getActionView();
        $dbn = $this->_parameters[0]; //database
        $tab = $this->_parameters[1]; // tabella
        $campo1 = $this->_parameters[2]; // campo1
        $view->set("campo", $campo1);
        $dbs = \WolfMVC\Censor::get("database");
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini");
        $fviewd = array();
        $fviewi = array();

        if (isset($ini[$dbname . "." . $tab . "." . $campo1])) {
            $f = $ini[$dbname . "." . $tab . "." . $campo1];
            $f = explode("|", $f);
            foreach ($f as $key => $ff) {
                $matches = array();
                preg_match("/(!|\+|\*)(([^#?\^\.]*)\.([^#?\^\.]*)\.([^#?\^\.]*))(#|\?|\^)(.*)/i", $ff, $matches);
                if (count($matches) == 8) {
                    $fviewd[$key] = array($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6], $matches[7]);
                }
            }
        } else {
            $f = array();
        }

        if (isset($_POST['insertlink'])) {
            if (isset($_POST['mult']) && $_POST['mult'] != -1 && $_POST['mult'] != '-1') {
                if (isset($_POST['table']) && $_POST['table'] != -1 && $_POST['table'] != '-1') {
                    if (isset($_POST['field']) && $_POST['field'] != -1 && $_POST['field'] != '-1') {
                        if (isset($_POST['view']) && $_POST['view'] != -1 && $_POST['view'] != '-1') {
                            if (($_POST['view'] == 0 || $_POST['view'] == 1 || $_POST['view'] == '0' || $_POST['view'] == '1') || ($_POST['view'] == 2 || $_POST['view'] == '2' && isset($_POST['al']) && $_POST['al'] != -1 && $_POST['al'] != '-1')) {
                                if (isset($ini[$_POST['field']])) {
                                    $fi = $ini[$_POST['field']];
                                    $fi = explode("|", $fi);
                                    foreach ($fi as $key => $ffi) {
                                        $matches = array();
                                        preg_match("/(!|\+|\*)(([^#?\^\.]*)\.([^#?\^\.]*)\.([^#?\^\.]*))(#|\?|\^)(.*)/i", $ffi, $matches);
                                        if (count($matches) == 8) {
                                            $fviewi[$key] = array($matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6], $matches[7]);
                                        }
                                    }
                                } else {
                                    $fi = array();
                                }
                                $flagd = false;
                                $flagi = false;

                                foreach ($fviewd as $key => $fff) {
                                    if ($fff[1] == $_POST['field']) {
                                        $flagd = $key;
                                        break;
                                    }
                                }

                                foreach ($fviewi as $key => $fffi) {
                                    echo $key . "  " . $fffi[1] . "  " . $dbname . "." . $tab . "." . $campo1 . "<br>";
                                    if ($fffi[1] == $dbname . "." . $tab . "." . $campo1) {
                                        $flagi = $key;
                                        break;
                                    }
                                }
                                $link = "";
                                $reverselink = "";
                                switch ($_POST['mult']) {
                                    case 0:
                                        $link .= "!";
                                        $reverselink .= "!";
                                        break;
                                    case 1:
                                        $link .= "+";
                                        $reverselink .= "*";
                                        break;
                                    case 2:
                                        $link .= "*";
                                        $reverselink .= "+";
                                        break;
                                }
                                $link .= $_POST['field'];
                                $reverselink .= $dbname . "." . $tab . "." . $campo1;
                                switch ($_POST['view']) {
                                    case 0:
                                        $link .= "#";
                                        $reverselink .= "#";
                                        break;
                                    case 1:
                                        $link .= "^";
                                        $reverselink .= "^";
                                        break;
                                    case 2:
                                        $link .= "?" . $_POST['al'];
                                        $reverselink .= "#";
                                        break;
                                }
                                echo $link;
                                echo $reverselink;
                                if ($flagd === FALSE) {
                                    array_push($f, $link);
                                } else {
                                    $f[$flagd] = $link;
                                }
                                if ($flagi === FALSE) {
                                    array_push($fi, $reverselink);
                                } else {
                                    $fi[$flagi] = $reverselink;
                                }
                                $data[$dbname . "." . $tab . "." . $campo1] = join("|", $f);
                                $data[$_POST['field']] = join("|", $fi);
                                $this->writeini(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini", $data);
                                header("location: " . SITE_PATH . $this->nameofthiscontroller() . "/setlink/" . $dbn . "/" . $tab . "/" . $campo1);
                            }
                        }
                    }
                }
            }
        }


        $back = "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/tabledetails/" . $dbn . "/" . $tab . "\">Torna ad analisi tabella</a>";
        $view->set("navigator", $back);
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            die('<br>Impossibile continuare!');
        }
        $view->set("formaction", SITE_PATH . $this->nameofthiscontroller() . "/setlink/" . $dbn . "/" . $tab . "/" . $campo1);
        $link = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini");
        $molt = array("!" => "1:1", "+" => "1:n", "*" => "n:1");
        $vie = array("#" => "self", "^" => "record", "?" => "Campo alias");
        $previous = "<table class=\"previoustable\">\n<tr>\n"
                . "<th>Molteplicit&agrave;</th><th>Nome campo</th><th>Visualizzazione</th><th>Campo alias</th></tr>\n";

        foreach ($fviewd as $key => $ffd) {

            $previous .="<tr><td>" . $molt[$ffd[0]] . "</td><td>" . $ffd[1] . "</td><td>" . $vie[$ffd[5]] . "</td><td>" . $ffd[6] . "</td></tr>\n";
        }
        $previous .= "</table>\n";
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        $tables = $ini[$dbname . '.tables'];
        $tables = explode("|", $tables);
        $tableoptions = "<option value=\"-1\">Scegli</option>\n";
        $fieldoptions = "<option value=\"-1\">Scegli</option>\n";
        foreach ($tables as $table) {
            if ($table == $tab)
                continue;
            $tableoptions .="<option value=\"" . $dbname . "." . $table . "\">" . $table . "</option>\n";
            $fields = $ini[$dbname . "." . $table . ".fields"];
            $fields = explode("|", $fields);
//            $fieldoptions .="<optgroup style=\"display: none;\" label=\"".$dbname.".".$table."\">\n";
            foreach ($fields as $field) {
                $fieldoptions .="<option value=\"" . $dbname . "." . $table . "." . $field . "\">" . $field . "</option>\n";
            }
//            $fieldoptions .="</optgroup>\n";
        }
        $view->set("tableoptions", $tableoptions);
        $view->set("fieldoptions", $fieldoptions);
        $view->set("tabella1", $tab);
        $view->set("campo1", $campo1);
        $view->set("table", $previous);
    }

    /**
     * @before script_including
     */
    public function tabledetails() {
        $view = $this->getActionView();
        $dbn = $this->_parameters[0];
        $tab = $this->_parameters[1];
        $dbs = \WolfMVC\Censor::get("database");
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $back = "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/analyze/" . $dbn . "\">Torna a elenco tabelle</a>";
        $view->set("navigator", $back);
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            die('<br>Impossibile continuare!');
        }

        $script = "<script>\n"
                . "function ()</script>";
        $links = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini");
//        recupero form post
        if (isset($_REQUEST['addlink']) && isset($_REQUEST['fromlink'])) {
            if (isset($links[$_REQUEST['fromlink'] . " >> " . $_REQUEST['fromlink']])) {
                $campilink1 = $links[$_REQUEST['fromlink']];
            } else {
                $campilink1 = "";
            }
            if (isset($links[$_REQUEST['addlink']])) {
                $campilink2 = $links[$_REQUEST['addlink']];
            } else {
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
            $this->writeini(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini", $data);
        }
        $links = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini");


        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        $fields = $this->initoarray($ini, $dbname . "." . $tab);
        $tables = $ini[$dbname . ".tables"];
        $tables = explode("|", $tables);
        $allfields = array();
        foreach ($tables as $table) {
            if ($table == $tab)
                continue;
            $allfields[$table] = $ini[$dbname . "." . $table . ".fields"];
            $allfields[$table] = explode("|", $allfields[$table]);
        }
        $view->set("dbname", "Nome del db: " . $dbname);
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
                    . "<a href=\"" . SITE_PATH . $this->nameofthiscontroller() . "/setlink/" . $dbn . "/" . $tab . "/" . $key . "\">Aggiungi nuova relazione</a>";
            if (isset($links[$dbname . "." . $tab . "." . $key])) {
                $lnk = $links[$dbname . "." . $tab . "." . $key];
                $lnk = explode("|", $lnk);
                if (count($lnk) > 0) {
                    $html .= "<ul>";
                    foreach ($lnk as $l) {
                        if ($l == "")
                            continue;
                        $html .= "<li>" . $l . "</li>";
                    }
                    $html .= "</ul>";
                }
            }
            else {
                $lnk = array();
            }



            $html .= "</td>";
            $html .= "</tr>";
        }


        $html .= "</table>";
        $view->set("html", $html);
    }

    public function syncdb() {
        $view = $this->getActionView();
        $dbn = $this->_parameters[0];
        $dbs = \WolfMVC\Censor::get("database");
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            die('<br>Impossibile continuare!');
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        $db = Registry::get("database_" . $dbname);
        $db->connect();
        $result = $db->execute("SHOW TABLES");
        $res = $result->fetch_all();
        $join = array();
        foreach ($res as $r) {
            array_push($join, $r[0]);
        }
        $data[$dbname . ".knowntables"] = join("|", $join);
        $data[$dbname . ".last_sync"] = date("Y-m-d H:i:s");

        uksort($data, array($this, "cmp"));

        $db->disconnect();

        $this->writeini(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", $data);
        header("location: " . SITE_PATH . $this->nameofthiscontroller() . "/analyze/" . $dbn);
    }

    public function synctable() {
        $view = $this->getActionView();
        $dbn = $this->_parameters[0];
        $tab = $this->_parameters[1];
        $dbs = \WolfMVC\Censor::get("database");
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            die('<br>Impossibile continuare!');
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        $db = Registry::get("database_" . $dbname);
        $db->connect();
        $result = $db->execute("DESCRIBE " . $tab);
        $data = $result->fetch_all(MYSQLI_ASSOC);
        $fields = array();
        foreach ($data as $key => $value) {
            $fields [$key] = $value['Field'];
            $matches = null;
            preg_match("/^(?:bit|tinyint|smallint|mediumint|int|bigint)(\(\d+\))/i", $value['Type'], $matches);
            if (count($matches) > 0) {
// è un intero 
                if ($value['Extra'] == "auto_increment") {
                    $data[$dbname . "." . $tab . "." . $value['Field'] . "." . "type"] = "autonumber";
                } else {
                    $data[$dbname . "." . $tab . "." . $value['Field'] . "." . "type"] = "integer";
                }
            } else {
                $matches = null;
                preg_match("/^(?:float|double|decimal)(.*)/i", $value['Type'], $matches);
                if (count($matches) > 0) {
// è un float 
                    $data[$dbname . "." . $tab . "." . $value['Field'] . "." . "type"] = "decimal";
                } else {
                    $matches = null;
                    preg_match("/^(?:DATETIME|DATE|TIME|YEAR|TIMESTAMP)(.*)/i", $value['Type'], $matches);
                    if (count($matches) > 0) {
// è un float 
                        $data[$dbname . "." . $tab . "." . $value['Field'] . "." . "type"] = "datetime";
                    } else {
                        $matches = null;
                        preg_match("/^(?:CHAR|VARCHAR|TINYTEXT|TINYBLOB|TEXT|BLOB|MEDIUMTEXT|MEDIUMBLOB|LONGTEXT|LONGBLOB)(.*)/i", $value['Type'], $matches);
                        if (count($matches) > 0) {
// è un float 
                            $data[$dbname . "." . $tab . "." . $value['Field'] . "." . "type"] = "text";
                        } else {
                            $matches = null;
                            preg_match("/^(?:bool|boolean)(.*)/i", $value['Type'], $matches);
                            if (count($matches) > 0) {
// è un float 
                                $data[$dbname . "." . $tab . "." . $value['Field'] . "." . "type"] = "boolean";
                            }
                        }
                    }
                }
            }
            $data[$dbname . "." . $tab . "." . $value['Field'] . ".null"] = $value['Null'];
            $data[$dbname . "." . $tab . "." . $value['Field'] . ".key"] = $value['Key'];
            $data[$dbname . "." . $tab . "." . $value['Field'] . ".extra"] = $value['Extra'];
            unset($data[$key]);
        }
        $data[$dbname . "." . $tab . ".fields"] = join("|", $fields);
        $data[$dbname . "." . $tab . '.last_sync'] = date("Y-m-d H:i:s");

        if (isset($ini[$dbname . ".tables"])) {
            $tables = $ini[$dbname . ".tables"];
            $tables = explode("|", $tables);
            if (array_search($tab, $tables) === FALSE) {
                array_push($tables, $tab);
            }
            sort($tables, SORT_STRING);
            $tables = join("|", $tables);
        } else {
            $tables = $tab;
        }
        $data[$dbname . ".tables"] = $tables;
        uksort($data, array($this, "cmp"));

        $db->disconnect();
        $this->writeini(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", $data);
        header("location: " . SITE_PATH . $this->nameofthiscontroller() . "/analyze/" . $dbn . "#" . $tab);
    }

    public function unsynctable() {
        $view = $this->getActionView();
        $dbn = $this->_parameters[0];
        $tab = $this->_parameters[1];
        $dbs = \WolfMVC\Censor::get("database");
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            die('<br>Impossibile continuare!');
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        $data = array();
        foreach ($ini as $key => $value) {
            if (strpos($key, $dbname . "." . $tab . ".") !== false) {
                $data[$key] = $value;
            }
        }

        if (isset($ini[$dbname . "." . $tab . ".fields"])) {
            $data[$dbname . "." . $tab . ".fields"] = $ini[$dbname . "." . $tab . ".fields"];
        }
        if (isset($ini[$dbname . "." . $tab . '.last_sync'])) {
            $data[$dbname . "." . $tab . '.last_sync'] = $ini[$dbname . "." . $tab . '.last_sync'];
        }
        $data2 = array();
        if (isset($ini[$dbname . ".tables"])) {
            $tables = $ini[$dbname . ".tables"];
            $tables = explode("|", $tables);
            $ind = array_search($tab, $tables);
            if ($ind !== FALSE) {
                unset($tables[$ind]);
            }
            $tables = join("|", $tables);
        } else {
            $tables = "";
        }
        if ($tables === "") {
            $data[$dbname . ".tables"] = $tables;
        } else {
            $data2[$dbname . ".tables"] = $tables;
        }
        $this->removefromini(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", $data);
        $this->writeini(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", $data2);
        header("location: " . SITE_PATH . $this->nameofthiscontroller() . "/analyze/" . $dbn);
    }

    /**
     * @before disablerender
     */
    public function ws___getdbs() {
        header('Content-type: application/json');
        $dbs = \WolfMVC\Censor::get("database");
        $out = array();
        foreach ($dbs as $key => $db) {
            $out[$key] = $db[0];
        }
        $out["count"] = count($out);
        $out["status"] = "ok";
        $out = json_encode($out, JSON_FORCE_OBJECT);
        echo $out;
        exit;
    }

    /**
     * @before disablerender
     */
    public function ws___gettabs() {
        header('Content-type: application/json');
        $out = array();
        if (!isset($this->_parameters[0])) {
            $out = array("status" => "error", "error" => "unspecified db code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $dbn = $this->_parameters[0];
//        $tab = $this->_parameters[1];
        $dbs = \WolfMVC\Censor::get("database");
        if ($dbn > (count($dbs) - 1)) {
            $out = array("status" => "error", "error" => "invalid db code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            $out = array("status" => "error", "error" => "missing conf");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        if (!isset($ini) || !isset($ini[$dbname . ".tables"])) {
            $out = array("status" => "error", "error" => "no tables information on this db");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $tables = $ini[$dbname . ".tables"];
        $tables = explode("|", $tables);
        $tables['count'] = count($tables);
        $tables['status'] = "ok";
        echo json_encode($tables, JSON_FORCE_OBJECT);
        exit;

//      
//        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
//        $data = array();
//        foreach ($ini as $key => $value) {
//            if (strpos($key, $dbname . "." . $tab) !== false) {
//                $data[$key] = $value;
//            }
//        }
//
//        if (isset($ini[$dbname . "." . $tab . ".fields"])) {
//            $data[$dbname . "." . $tab . ".fields"] = $ini[$dbname . "." . $tab . ".fields"];
//        }
//        if (isset($ini[$dbname . "." . $tab . '.last_sync'])) {
//            $data[$dbname . "." . $tab . '.last_sync'] = $ini[$dbname . "." . $tab . '.last_sync'];
//        }
//
//        $this->removefromini(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", $data);
    }

    /**
     * @before disablerender
     */
    public function ws___getfields() {
        header('Content-type: application/json');
        $out = array();
        if (!isset($this->_parameters[0])) {
            $out = array("status" => "error", "error" => "unspecified db code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $dbn = $this->_parameters[0];
        if (!isset($this->_parameters[1])) {
            $out = array("status" => "error", "error" => "unspecified table code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $tab = $this->_parameters[1];
        $dbs = \WolfMVC\Censor::get("database");
        if ($dbn > (count($dbs) - 1)) {
            $out = array("status" => "error", "error" => "invalid db code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            $out = array("status" => "error", "error" => "missing conf");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
        if (!isset($ini) || !isset($ini[$dbname . ".tables"])) {
            $out = array("status" => "error", "error" => "no tables information on this db");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $data = $this->initoarray($ini, $dbname . "." . $tab);
        if (!isset($data['fields'])) {
            $out = array("status" => "error", "error" => "Can't find fields for table " . $tab);
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $data['fields'] = explode("|", $data['fields']);
        foreach ($data['fields'] as $key => $field) {
            $data[$key] = array("name" => $field, "details" => $data[$field]);
            unset($data[$field]);
        }
        unset($data["fields"]);
        unset($data["last_sync"]);
        $data["count"] = count($data);
        $data["status"] = "ok";
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit;
    }

    /**
     * @before disablerender
     */
    public function ws___getlinks() {
        header('Content-type: application/json');
        $out = array();
        if (!isset($this->_parameters[0])) {
            $out = array("status" => "error", "error" => "unspecified db code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $dbn = $this->_parameters[0];
        if (!isset($this->_parameters[1])) {
            $out = array("status" => "error", "error" => "unspecified table code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $tab = $this->_parameters[1];
        if (!isset($this->_parameters[2])) {
            $out = array("status" => "error", "error" => "unspecified field");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $campo1 = $this->_parameters[2];
        $dbs = \WolfMVC\Censor::get("database");
        if ($dbn > (count($dbs) - 1)) {
            $out = array("status" => "error", "error" => "invalid db code");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $dbname = $dbs[$dbn];
        $dbname = $dbname [0];
        $this->checkfile($dbname);
        if (!($this->_filepresent)) {
            $out = array("status" => "error", "error" => "missing conf");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . "_link.ini");
        if (!isset($ini) || !isset($ini[$dbname . "." . $tab . "." . $campo1])) {
            $out = array("status" => "error", "error" => "no links information about this field");
            echo json_encode($out, JSON_FORCE_OBJECT);
            exit;
        }
        $data = $ini[$dbname . "." . $tab . "." . $campo1];
        $data = explode("|", $data); //qui ho tutti i link

        foreach ($data as $key => $field) {
            $link = $this->decodelink($field);
            $linkdbn = -1;
            foreach ($dbs as $kkkk => $dbbbb) {
                if ($dbbbb[0] == $link[3]) {
                    $linkdbn = $kkkk;
                    break;
                }
            }
            $out[$key] = array(
                "name" => $link[2],
                "mult" => $link[1],
                "db" => $link[3],
                "table" => $link[4],
                "dbtable" => $link[3] . "." . $link[4],
                "tablefield" => $link[4] . "." . $link[5],
                "fieldname" => $link[5],
                "view" => $link[6],
                "alias" => $link[7],
                "dbn" => $linkdbn
            );
        }
        $out["count"] = count($out);
        $out["status"] = "ok";
        echo json_encode($out, JSON_FORCE_OBJECT);
        exit;

//      
//        $ini = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbname . ".ini");
//        $data = array();
//        foreach ($ini as $key => $value) {
//            if (strpos($key, $dbname . "." . $tab) !== false) {
//                $data[$key] = $value;
//            }
//        }
//
//        if (isset($ini[$dbname . "." . $tab . ".fields"])) {
//            $data[$dbname . "." . $tab . ".fields"] = $ini[$dbname . "." . $tab . ".fields"];
//        }
//        if (isset($ini[$dbname . "." . $tab . '.last_sync'])) {
//            $data[$dbname . "." . $tab . '.last_sync'] = $ini[$dbname . "." . $tab . '.last_sync'];
//        }
//
//        $this->removefromini(APP_PATH . "/application/configuration/database/" . $dbname . ".ini", $data);
    }

    public function cmp($a, $b) {

        $asplit = explode(".", $a);
        $bsplit = explode(".", $b);
//        echo "comp " . $a . "(" . count($asplit) . ")" . $b . "(" . count($bsplit) . ")" . "<br>";
        for ($i = 0; $i < min(count($asplit), count($bsplit)); $i++) {
//            echo "comp " . $asplit[$i] . ">" . $bsplit[$i] . "<br>";
            if ($asplit[$i] > $bsplit[$i]) {
//                echo "maggiore<br>";
                return 1;
            } elseif ($asplit[$i] < $bsplit[$i]) {
                return -1;
            }
        }
    }

    public function writeini($filename, $data) {
        if (is_writable($filename)) {
// In questo esempio apriamo $filename in append mode.
// Il puntatore del file è posizionato in fondo al file
// è qui che verrà posizionato $somecontent quando eseguiremo fwrite().
            $present = parse_ini_file($filename);
            $data = array_merge($present, $data);
            uksort($data, array($this, "cmp"));
            if (!$handle = fopen($filename, 'w')) {
                exit;
            }
            foreach ($data as $key => $value) {
                if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE) {
                    exit;
                }
            }
            fclose($handle);
        } else {
            die("Errore: impossibile scrivere il file di configurazione.");
        }
    }

    public function removefromini($filename, $data) {
        if (is_writable($filename)) {

// In questo esempio apriamo $filename in append mode.
// Il puntatore del file è posizionato in fondo al file
// è qui che verrà posizionato $somecontent quando eseguiremo fwrite().
            $present = parse_ini_file($filename);
//            $present_keys = array_keys($present);
//            $data_keys = array_keys($data);
            $data = array_diff_key($present, $data);
            uksort($data, array($this, "cmp"));
            if (!$handle = fopen($filename, 'w')) {
                exit;
            }
            foreach ($data as $key => $value) {
                if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE) {
                    exit;
                }
            }
            fclose($handle);
        }
    }

    public function removeexactlyfromini($filename, $data) {
        if (is_writable($filename)) {

// In questo esempio apriamo $filename in append mode.
// Il puntatore del file è posizionato in fondo al file
// è qui che verrà posizionato $somecontent quando eseguiremo fwrite().
            $present = parse_ini_file($filename);
//            $present_keys = array_keys($present);
//            $data_keys = array_keys($data);
            foreach ($data as $keytoremove => $valuetoremove) {
                foreach ($present as $key => $value) {
                    if (($keytoremove == $key) && ($valuetoremove == $value)) {
                        unset($present[$key]);
                    }
                }
            }
            if (!$handle = fopen($filename, 'w')) {
                exit;
            }
            foreach ($present as $key => $value) {
                if (fwrite($handle, $key . " = \"" . $value . "\"\n") === FALSE) {
                    exit;
                }
            }
            fclose($handle);
        }
    }

    public function decodelink($link) {
        $matches = array();
        preg_match("/(!|\+|\*)(([^#?\^\.]*)\.([^#?\^\.]*)\.([^#?\^\.]*))(#|\?|\^)(.*)/i", $link, $matches);
        return $matches;
    }

    public function initoarray($ini, $prefix) {
        /* trasforma $ini contenente 
         * prefix.chiave1.sottochiave1 => val
         * prefix.chiave1.sottochiave2 => val
         * ...
         * prefix.chiave2.sottochiave1 => val
         * ...
         * 
         * in un vettore 
         * chiave1 => [sottochiave1 => val, sottochiave2 => val]
         * ...
         * chiave2 => [sottochiave2 => val]
         * ...
         * (#)se trovo una chiave uguale al prefisso in output ottengo
         * la chiave 0 con quel valore
         */
        $output = array();
        foreach ($ini as $key => $value) {
            if ($key === $prefix) { //(#)
                $output[0] = $value;
            } else {
                if (strpos($key, $prefix . ".") === 0) {
                    $kk = str_ireplace($prefix . ".", "", $key);
                    $kk = explode(".", $kk);
                    $out = &$output;
                    for ($i = 0; $i < count($kk); $i++) {
//                        print_r($output);
                        if (!is_array($out)) {
                            $out = array();
                        }
                        $out = &$out[$kk[$i]];
                    }
                    $out = $value;
                }
            }
        }

        return $output;
    }

}
