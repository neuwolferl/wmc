<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Backoffice extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    /**
     * @protected
     */
    public function script_including() {

        /*
         * temporaneamente appoggio qui l'autorizzazione sul controller
         */
        $session = \WolfMVC\Registry::get("session");
        $user = $session->get("user");
        if (!in_array($user, array("Alberto Brudaglio", "Vincenzo Cervadoro")))
        {
            header("Location: " . SITE_PATH);
        }

        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngsortable.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/data.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/tsnwclient.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/filteredtable.js\"></script>";

        $view = $this->getLayoutView();
        $view->set("moduleName", "BACKOFFICE");
    }

    public function index() {
        $view = $this->getActionView();
        $view->set("actions", array(
            "so2proj" => SITE_PATH . $this->nameofthiscontroller() . "/so2proj",
//            "Autorizzazione pagine" => SITE_PATH . $this->nameofthiscontroller() . "/autorizzazione_pagine",
//            "Gestione utenti" => SITE_PATH . $this->nameofthiscontroller() . "/gestione_utenti"
        ));
    }

    public function so2proj() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("BACKOFFICE" => "backoffice", "So2Proj" => "last")));
        $view = $this->getActionView();
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function ws___so2projgetsolist() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
        }
        $sql = "SELECT " .
                "a.salesorderid, a.subject as 'Soggetto SO', a.salesorder_no as 'Numero SO', a.sostatus as 'Stato SO', c.accountname as 'Nome Azienda', if (isnull(f.projectname), 'Progetto non presente',f.projectname) as 'Progetto associato', h.cf_696 as 'trattato' " .
                "from vtiger_salesorder a " .
                "LEFT JOIN vtiger_salesordercf h ON (h.salesorderid = a.salesorderid) " .
                "LEFT JOIN vtiger_crmentity b ON (b.crmid = a.salesorderid) " .
                "LEFT JOIN vtiger_account c ON (c.accountid = a.accountid) " .
                "LEFT JOIN vtiger_crmentity d ON (d.crmid = c.accountid) " .
                "LEFT JOIN vtiger_projectcf e ON (e.cf_691 = a.salesorderid) " .
                "LEFT JOIN vtiger_project f ON (f.projectid = e.projectid) " .
                "LEFT JOIN vtiger_crmentity g ON (g.crmid = f.projectid) " .
                "where a.subject LIKE '%PROGETTO%' AND b.deleted = '0' AND d.deleted = '0' AND (isnull(e.projectid) OR g.deleted = '0') " .
                "ORDER BY a.salesorderid";

        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error");
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
        }
    }

    public function ws___so2projgetso() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $salesorderid = $this->anti_injection($_GET["salesorderid"]);

        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
        }
        $sql = "SELECT a.subject as 'Soggetto', a.salesorder_no as 'Numero SO', a.accountid, a.sostatus as 'Stato', c.cf_667 as 'Capo progetto'" .
                "FROM vtiger_salesorder a " .
                "LEFT JOIN vtiger_quotescf c ON (c.quoteid = a.quoteid) " .
                "WHERE a.salesorderid = '{$salesorderid}'";
        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error");
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
        }
    }

    public function ws___so2projgetcustomer() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $accountid = $this->anti_injection($_GET["accountid"]);

        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
        }
        $sql = "SELECT a.accountname as 'Azienda', a.account_no as 'Num Azienda' FROM vtiger_account a " .
                "WHERE a.accountid = '{$accountid}'";
        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error");
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
        }
    }

    public function ws___so2projgetassproj() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $salesorderid = $this->anti_injection($_GET["salesorderid"]);

        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
        }
        $sql = "SELECT a.projectid, a.projectname as 'Nome progetto', a.project_no as 'Numero progetto', " .
                "a.projecturl as 'url', concat(d.first_name,' ',d.last_name) as 'Assegnato a', " .
                "a.projectstatus as 'Stato progetto', a.projecttype as 'Tipo progetto' " .
                "FROM vtiger_project a LEFT JOIN vtiger_projectcf b ON (a.projectid = b.projectid) " .
                "LEFT JOIN vtiger_crmentity c ON (a.projectid = c.crmid) " .
                "LEFT JOIN vtiger_users d ON (c.smownerid = d.id) " .
                "WHERE cf_691 = '{$salesorderid}' AND c.deleted = '0'";
        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error");
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
        }
    }

    public function ws___so2projgetotherproj() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $salesorderid = $this->anti_injection($_GET["salesorderid"]);
        $accountid = $this->anti_injection($_GET["accountid"]);

        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
        }
        $sql = "SELECT a.projectid, a.projectname as 'Nome progetto', a.project_no as 'Numero progetto', " .
                "a.projecturl as 'url', concat(d.first_name,' ',d.last_name) as 'Assegnato a', " .
                "a.projectstatus as 'Stato progetto', a.projecttype as 'Tipo progetto' " .
                "FROM vtiger_project a LEFT JOIN vtiger_projectcf b ON (a.projectid = b.projectid) " .
                "LEFT JOIN vtiger_crmentity c ON (a.projectid = c.crmid) " .
                "LEFT JOIN vtiger_users d ON (c.smownerid = d.id) " .
                "WHERE cf_691 <> '{$salesorderid}' AND a.linktoaccountscontacts = '{$accountid}' AND c.deleted = '0'";
        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error");
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
        }
    }

    public function ws___so2projgetinv() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $salesorderid = $this->anti_injection($_GET["salesorderid"]);

        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
        }
        $sql = "SELECT subject as 'Soggetto', invoicedate as 'Data fattura', invoice_no as 'numero', total as 'Ammontare' " .
                "FROM vtiger_invoice a LEFT JOIN vtiger_invoicecf b ON (a.invoiceid = b.invoiceid) " .
                "LEFT JOIN vtiger_crmentity c ON (a.invoiceid = c.crmid) " .
                "WHERE a.salesorderid = '{$salesorderid}' AND c.deleted = '0'";
        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error");
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($ret);
        }
    }

    public function ws___so2projretrieveproj() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $projectid = $this->anti_injection($_GET["projectid"]);
        $client = WolfMVC\Registry::get("VTWS");
        $result = $client->doRetrieve("31x" . $projectid);
        if (!$result)
            echo json_encode($client->lastError());
        else
            echo json_encode($result);
        return;
    }

    public function ws___so2projgetusers() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $client = WolfMVC\Registry::get("VTWS");
        $result = $client->doQuery("SELECT id, first_name, last_name FROM Users WHERE roleid IN ('H12','H13') OR first_name = 'Mirko'");
        if (!$result)
            echo json_encode($client->lastError());
        else
            echo json_encode($result);
        return;
    }

    public function ws___so2projupdate() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_POST["data"];
        $data = json_decode($data, true);
        $client = WolfMVC\Registry::get("VTWS");
        $result = $client->doUpdate($data);
        if (!$result)
            echo json_encode($client->lastError());
        else
        {
            $db = WolfMVC\Registry::get("database_vtiger");
            $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
            if ($link->connect_errno)
            {
                echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
            }
            $sql = "UPDATE vtiger_salesordercf SET cf_696 = '1' WHERE salesorderid = '" . str_ireplace("6x", "", $data["cf_691"]) . "';";
            $result = $link->query($sql);
            echo json_encode($result);
        }
        return;
    }

    public function ws___so2projcreate() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_POST["data"];
        $data = json_decode($data, true);
        foreach ($data as $k => $d) {
            if (empty($d))
                unset($data[$k]);
        }
//        print_r($data);
//        return;
        $client = WolfMVC\Registry::get("VTWS");
        $result = $client->doCreate("Project", $data);
        if (!$result)
            echo json_encode($client->lastError());
        else
        {
            $db = WolfMVC\Registry::get("database_vtiger");
            $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
            if ($link->connect_errno)
            {
                echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
            }
            $sql = "UPDATE vtiger_salesordercf SET cf_696 = '1' WHERE salesorderid = '" . str_ireplace("6x", "", $data["cf_691"]) . "';";
            $result = $link->query($sql);
            echo json_encode($result);
        }
        return;
    }

    public function ws___bla() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = \WolfMVC\RequestMethods::post("data");
        $data = json_decode($data, true);
        $user_name = $this->anti_injection($data["user_name"]);
        $user_password = $this->anti_injection($data["user_password"]);
        $confirm_user_password = $this->anti_injection($data["confirm_user_password"]);
        $first_name = $this->anti_injection($data["first_name"]);
        $last_name = $this->anti_injection($data["last_name"]);
        $active = $this->anti_injection($data["active"]);
//        echo "ho ricevuto";
//        print_r(array($user_name, $user_password, $confirm_user_password, $first_name, $last_name, $active));
//        exit;
        $ret = new stdClass();
        $ret->result = "success";
        //validazione
        if ($user_password !== $confirm_user_password)
        {
            $ret->result = "failure";
            $ret->error = "passwords don't match";
        }

        if (strlen($user_password) < 8)
        {
            $ret->result = "failure";
            $ret->error = "password must have 8 chars or more";
        }
        $matches = array();
        preg_match("/^[a-z][a-z0-9]{2,}/i", $user_name, $matches);
        if (count($matches) !== 1 || $matches[0] !== $user_name)
        {
            $ret->result = "failure";
            $ret->error = "user_name must have 3 chars or more, the first must be a letter";
        }
        $matches = array();
        preg_match("/^[a-z]{3,}/i", $first_name, $matches);
        if (count($matches) !== 1 || $matches[0] !== $first_name)
        {
            $ret->result = "failure";
            $ret->error = "first_name must have 3 chars or more, only letters";
        }
        $matches = array();
        preg_match("/^[a-z]{3,}/i", $last_name, $matches);
        if (count($matches) !== 1 || $matches[0] !== $last_name)
        {
            $ret->result = "failure";
            $ret->error = "last_name must have 3 chars or more, only letters";
        }
        if ($ret->result === "failure")
        {
            echo json_encode($ret);
            return;
        }
        $user_password = md5($user_password);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $retrieve = "SELECT count(*) FROM users WHERE user_name = '{$user_name}'";
        $result = $link->query($retrieve);
        $ass = $result->fetch_array(MYSQLI_NUM);
        if ($ass[0] > 0)
        {
            $ret->result = "failure";
            $ret->error = "Error occurred during db upgrade. Error: Duplicate user_name {$user_name}. ";
            echo json_encode($ret);
            return;
        }
        $insert = "INSERT INTO users (user_name, user_password, first_name, last_name, active) VALUES ('{$user_name}','{$user_password}','{$first_name}','{$last_name}','{$active}')";
        $result = $link->query($insert);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "Error occurred during db upgrade. Error: " . $link->error;
        }
        echo json_encode($ret);
        return;
    }

    public function ws___cercaAzienda() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "Error";
        if (!isset($_REQUEST["azienda"]))
        {
            $ret->error = "Missing or wrong parameters";
            echo "<pre>" . print_r($ret, true) . "</pre>";
            return;
        }
        $azienda = $_REQUEST["azienda"];
        $fs = $_REQUEST["fs"];
        if (!isset($_REQUEST["fs"]))
        {
            $ret->error = "Missing or wrong parameters";
            echo "<pre>" . print_r($ret, true) . "</pre>";
            return;
        }
        $fs = $_REQUEST["fs"];
        $db = WolfMVC\Registry::get("database_mkt");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema);
        if ($link->connect_errno)
        {
            echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
            return;
        }
        $ricerca = trim(str_ireplace($fs, "", $azienda));
        $ffs = "";
        for ($i = 0; $i < strlen($fs); $i++) {
            $ffs .= '%';
            $ffs.= $fs[$i];
        }
        $ffs .= "%";
        $subricerca = array();
        for ($i = floor(strlen($ricerca) / 2); $i < strlen($ricerca); $i++) {
            $subricerca [] = substr($ricerca, 0, $i);
        }
        $wh = "(MATCH (RagioneSociale) AGAINST ('+{$ricerca}' IN BOOLEAN MODE) ";
        foreach ($subricerca as $k => $v) {
            $wh .= "+ {$k}*MATCH (RagioneSociale) AGAINST ('+{$v}' IN BOOLEAN MODE) ";
        }
        $wh .= ") ";
        $sql = "SELECT internal_id, PartitaIva, RagioneSociale, Regione " .
                "FROM aziende_all " .
//                "JOIN " .
//                "( " .
//                "SELECT " .
//                "internal_id as iid, RagioneSociale as RS " .
//                "FROM " .
//                "aziende_all " .
                "WHERE " .
                $wh .
                "ORDER BY {$wh} DESC ";// .
//                ") prova " .
//                "ON (aziende_all.internal_id = prova.iid) "; // .
//                "and RagioneSociale LIKE '{$ffs}'";
        echo $sql;
        $result = $link->query($sql);
        if (!$result)
        {
            echo json_encode("error " + $sql);
            return;
        }
        else
        {
            $ret = $result->fetch_all(MYSQLI_ASSOC);
            foreach ($ret as $k => $v) {
                $ret[$k]["extra_common_digits"] = abs(similar_text(strtolower($azienda), strtolower($v["RagioneSociale"])) - strlen($azienda));
                $ret[$k]["levenshtein"] = levenshtein(strtolower($azienda), strtolower($v["RagioneSociale"]));
            }

            usort($ret, function($a, $b) {
//                if ($a["extra_common_digits"] < 0 || $b["extra_common_digits"] < 0)
//                {
//                    if ($a["extra_common_digits"] !== $b["extra_common_digits"])
//                        return $b["extra_common_digits"] - $a["extra_common_digits"];
//                    else
//                    {
//                        return $a["levenshtein"] - $b["levenshtein"];
//                    }
//                }
//                else
//                {
                if ($a["levenshtein"] !== $b["levenshtein"])
                    return $a["levenshtein"] - $b["levenshtein"];
                else
                {
                    return $b["extra_common_digits"] - $a["extra_common_digits"];
                }
//                }
            });

            ob_start();
            foreach ($ret as $k => $v) {
                echo "\n\t" . $v["internal_id"] . "\t\t" . $v["RagioneSociale"];
            }
            echo "\n\n\n\n\n\n";
            $out = ob_get_contents();
            ob_end_clean();

            echo $out . $sql . "<br><br><pre>" . print_r($ret, true) . "</pre>";
        }
    }

}
