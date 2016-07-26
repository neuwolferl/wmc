<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

/**
 * @site PANNELLO_DI_AMMINISTRAZIONE
 */
class Sonosuperfigo extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    /**
     * @protected
     */
    public function script_including() {
        $this->loadScript("jquery.js");
        $this->loadScript("angular.min.js");
        $this->loadScript("ngbootstrap.min.js");
        $this->loadScript("ng-ui-bootstrap-tpls-0.2.0.js");
        $this->loadScript("core/data.js");


        $view = $this->getLayoutView();
        $view->set("moduleName", "PANNELLO DI AMMINISTRAZIONE");
    }

    public function index() {
        $view = $this->getActionView();
        $view->set("action", array(
            "Autorizzazione accesso pagine" => SITE_PATH . $this->nameofthiscontroller() . "/riepilogo_pagine",
        ));
    }

    public function riepilogo_pagine() {

        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("AMMINISTRAZIONE" => "sonosuperfigo", "Riepilogo pagine" => "last")));
        $view = $this->getActionView();
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function struttura_pagine() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("AMMINISTRAZIONE" => "sonosuperfigo", "Riepilogo pagine" => "last")));
        $view = $this->getActionView();
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function gestione_utenti() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("AMMINISTRAZIONE" => "sonosuperfigo", "Gestione utenti" => "last")));
        $view = $this->getActionView();
        if (!isset($_SERVER["HTTPS"]) || empty($_SERVER["HTTPS"]) || $_SERVER['SERVER_PORT'] !== '443')
        {
            $redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header("Location: $redirect");
        }
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
        $view->set("dirty", '$dirty');
        $view->set("invalid", '$invalid');
    }

    public function ws___configurazioni() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $config = array(
            "services" => array(
                "getSites" => SITE_PATH . $this->nameofthiscontroller() . "/getSites.ws",
                "getAllConfs" => SITE_PATH . $this->nameofthiscontroller() . "/getAllConfs.ws"
            )
        );
        echo json_encode($config, JSON_FORCE_OBJECT);
        return;
    }

    public function ws___getSites() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $sites = WolfMVC\Registry::get("sites");
//        $sites_all = explode("|",$sites->sites->all);
        echo json_encode($sites);
        return;
    }

    public function ws___getAllConfs() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $out = array();
        $dir = APP_PATH . '/application/configuration';
        $out = \WolfMVC\FileMethods::recDir($dir);
//        $sites_all = explode("|",$sites->sites->all);
        echo json_encode($out);
        return;
    }

    public function ws___getConfByPath() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($_GET["path"]))
        {
            echo json_encode("Missing path");
            return;
        }
        $path = $this->anti_injection($_GET["path"]);
        if (is_file(APP_PATH . '/application/configuration/' . $path))
        {
            echo json_encode(file_get_contents(APP_PATH . '/application/configuration/' . $path));
            return;
        }
        else
        {
            echo json_encode("Missing file");
            return;
        }
        return;
    }

    public function ws___addUser() {
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

    public function ws___riepilogo_pagine() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $config = array(
            "services" => array(
                "getElencoPagine" => SITE_PATH . $this->nameofthiscontroller() . "/getElencoPagine.ws",
                "getUtenti" => $_SERVER["HTTP_HOST"] . SITE_PATH . $this->nameofthiscontroller() . "/getUtenti.ws",
                "checkPage" => SITE_PATH . $this->nameofthiscontroller() . "/checkPage.ws",
                "addPages" => SITE_PATH . $this->nameofthiscontroller() . "/addPages.ws",
                "enablePage" => SITE_PATH . $this->nameofthiscontroller() . "/enablePage.ws",
                "disablePage" => SITE_PATH . $this->nameofthiscontroller() . "/disablePage.ws",
                "getGoogleUsers" => SITE_PATH . $this->nameofthiscontroller() . "/getGoogleUsers.ws",
                "getAuthorizedUsersForPages" => SITE_PATH . $this->nameofthiscontroller() . "/getAuthorizedUsersForPages.ws",
                "authUser" => SITE_PATH . $this->nameofthiscontroller() . "/authUser.ws",
                "authAll" => SITE_PATH . $this->nameofthiscontroller() . "/authAll.ws"
            )
        );
        echo json_encode($config, JSON_FORCE_OBJECT);
        return;
    }

    public function ws___gestione_utenti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $config = array(
            "services" => array(
                "getUtenti" => SITE_PATH . $this->nameofthiscontroller() . "/getUtenti.ws",
                "getUtentiVT" => SITE_PATH . $this->nameofthiscontroller() . "/getUtentiVT.ws",
                "addUser" => SITE_PATH . $this->nameofthiscontroller() . "/addUser.ws",
                "activateUser" => SITE_PATH . $this->nameofthiscontroller() . "/activateUser.ws",
                "associateVT" => SITE_PATH . $this->nameofthiscontroller() . "/associateVT.ws"
            ),
            "resuri" => array(
                "img" => array(
                    "plus" => SITE_PATH . "img/plus.png"
                )
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___getElencoPagine() {
        $this->setJsonWS();

        $dir = APP_PATH . "/application/controllers";
        $controllers = array();
        try {
            if (is_dir($dir))
            {
                $files = scandir($dir);
                foreach ($files as $file) {
                    $split = explode(".", $file);
                    if ($split[count($split) - 1] === "php" && (array_pop($split) !== NULL))
                    {
//
                        $ctrlname = join(".", $split);
                        $ctrlclass = ucfirst($ctrlname);

                        if (class_exists($ctrlclass))
                        {
                            $meth = array();
                            $methods = get_class_methods($ctrlclass);
                            $vars = get_class_vars($ctrlclass);
                            $dirview = APP_PATH . "/{$vars["_defaultPath"]}/{$ctrlname}/";
                            if (is_dir($dirview))
                            {
                                $viewfiles = scandir($dirview);
                            }
                            else
                            {
                                $viewfiles = array();
                            }
                            foreach ($methods as $k => $m) {
                                $index = array_search($m . ".html", $viewfiles);
                                if ($index !== FALSE)
                                {
                                    $v = $viewfiles[$index];
                                }
                                else
                                {
                                    $v = "";
                                }
                                array_push($meth, array("method" => $m, "view" => $v));
                            }
                        }
                        $controllers[$ctrlname] = $meth;
                    }
                }
            }
            echo json_encode($controllers);
            return;
        } catch (Exception $e) {
            $this->_setResponseStatus(500);
//            echo "Errore";
            echo json_encode(print_r($e, true));
            return;
        }
    }

    public function ws___getUtenti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT a.id,user_name,first_name,last_name,active, IF(ISNULL(vtuserid), false, true) as vt, IF(ISNULL(vtuserid), '', vtuserid) as vtuid FROM users a LEFT JOIN user2vtuser b ON (a.id = b.userid)";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving users: " . $link->error);
            return;
        }
    }

    public function ws___getGoogleUsers() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT id, googleid, display_name FROM googleusers";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving users: " . $link->error);
            return;
        }
    }

    public function ws___getAuthorizedUsersForPages() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT b.controller, b.action, a.userid FROM pageuserauth a LEFT JOIN pages b ON (a.pageid = b.id)";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving users: " . $link->error);
            return;
        }
    }

    public function ws___checkPage() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $sql = "SELECT id, controller, action, isactive FROM pages";
        if (isset($_GET["controller"]))
        {
            $controller = $this->anti_injection($_GET["controller"]);
            $sql .= " WHERE controller = '{$controller}'";
        }
        if (isset($_GET["action"]))
        {
            $action = $this->anti_injection($_GET["action"]);
            $sql .= " AND action = '{$action}'";
        }
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving users: " . $link->error);
            return;
        }
    }

    public function ws___addPages() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "success";
        if (!isset($_POST["data"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing arguments";
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "INSERT INTO pages (controller,action) VALUES ";
        $insert = array();
        foreach ($data as $key => $value) {
            if (!isset($value["controller"]) || !isset($value["action"]))
                continue;
            $insert[] = "('{$value["controller"]}','{$value["action"]}')";
        }
        if (count($insert) === 0)
        {
            $ret->result = "failure";
            $ret->error = "Missing or wrong arguments";
            echo json_encode($ret);
            return;
        }
        else
        {
            $sql .= join(",", $insert);
        }
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "An error occurred";
            echo json_encode($ret);
            return;
        }
        echo json_encode($ret);
        return;
    }

    public function ws___enablePage() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "success";
        if (!isset($_POST["data"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing arguments";
            echo json_encode($ret);
            return;
        }

        $data = json_decode($_POST["data"], true);
        if (!isset($data["controller"]) || !isset($data["action"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing or wrong arguments";
            echo json_encode($ret);
            return;
        }
        $controller = $this->anti_injection($data["controller"]);
        $action = $this->anti_injection($data["action"]);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "UPDATE pages SET isactive = '1' WHERE controller = '{$controller}' AND action = '{$action}'";
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "An error occurred";
            echo json_encode($ret);
            return;
        }
        echo json_encode($ret);
        return;
    }

    public function ws___disablePage() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "success";
        if (!isset($_POST["data"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing arguments";
            echo json_encode($ret);
            return;
        }

        $data = json_decode($_POST["data"], true);
        if (!isset($data["controller"]) || !isset($data["action"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing or wrong arguments";
            echo json_encode($ret);
            return;
        }
        $controller = $this->anti_injection($data["controller"]);
        $action = $this->anti_injection($data["action"]);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "UPDATE pages SET isactive = '0' WHERE controller = '{$controller}' AND action = '{$action}'";
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "An error occurred";
            echo json_encode($ret);
            return;
        }
        echo json_encode($ret);
        return;
    }

    public function ws___activateUser() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "success";
        if (!isset($_POST["data"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing argument: userid";
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["userid"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing argument: userid";
            echo json_encode($ret);
            return;
        }
        if (!isset($data["active"]) || ($data["active"] !== '1' && $data["active"] !== '0'))
        {
            $ret->result = "failure";
            $ret->error = "Missing or invalid argument: new active value";
            echo json_encode($ret);
            return;
        }
        $userid = $this->anti_injection($data["userid"]);
        $active = $this->anti_injection($data["active"]);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT COUNT(*) FROM users WHERE id = '{$userid}'";
        $result = $link->query($sql);
        $ass = $result->fetch_array(MYSQLI_NUM);
        if (!isset($ass[0]) || $ass[0] !== '1')
        {
            $ret->result = "failure";
            $ret->error = "Error occurred: the specified user {$userid} is not valid or there is some problem in its definition";
            echo json_encode($ret);
            return;
        }
        $sql = "UPDATE users SET active = '{$active}' WHERE id='{$userid}'";
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "Error occurred in updating infos";
            echo json_encode($ret);
            return;
        }
        echo json_encode($ret);
        return;
    }

    public function ws___authUser() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "success";
        if (!isset($_POST["data"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing argument: userid";
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["controller"]) || !isset($data["action"]) || !isset($data["what"]) || !isset($data["userid"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing or invalid arguments.";
            echo json_encode($ret);
            return;
        }
        $controller = $this->anti_injection($data["controller"]);
        $action = $this->anti_injection($data["action"]);
        $what = $this->anti_injection($data["what"]);
        if (!isset($data["active"]))
        {
            $active = '1';
        }
        else
        {
            $active = $this->anti_injection($data["active"]);
        }
        $userid = $this->anti_injection($data["userid"]);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT id FROM pages WHERE controller = '{$controller}' AND action = '{$action}'";
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "Error occurred. Controller/action invalid.";
            echo json_encode($ret);
            return;
        }
        $ass = $result->fetch_array(MYSQLI_NUM);
        if (!isset($ass[0]))
        {
            $ret->result = "failure";
            $ret->error = "Error occurred. Controller/action invalid.";
            echo json_encode($ret);
            return;
        }
        if ($what === "auth")
        {
            $sql = "INSERT INTO pageuserauth (userid, pageid) VALUES ('{$userid}','{$ass[0]}')";
        }
        else if ($what === "unauth")
        {
            $sql = "DELETE FROM pageuserauth WHERE userid = '{$userid}' AND pageid = '{$ass[0]}'";
        }
        else
        {
            $ret->result = "failure";
            $ret->error = "Error occurred. Auth command invalid.";
            echo json_encode($ret);
            return;
        }
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "Error occurred in updating infos";
            echo json_encode($ret);
            return;
        }
        echo json_encode($ret);
        return;
    }

    public function ws___associateVT() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $ret = new stdClass();
        $ret->result = "success";
        if (!isset($_POST["data"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing arguments";
            echo json_encode($ret);
            return;
        }
        $data = json_decode($_POST["data"], true);
        if (!isset($data["userid"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing argument: userid";
            echo json_encode($ret);
            return;
        }
        if (!isset($data["vtuserid"]))
        {
            $ret->result = "failure";
            $ret->error = "Missing argument: vtuserid";
            echo json_encode($ret);
            return;
        }
        $userid = $this->anti_injection($data["userid"]);
        $vtuserid = $this->anti_injection($data["vtuserid"]);
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "DELETE FROM user2vtuser WHERE userid = '{$userid}'";
        $result = $link->query($sql);
        if (!$result)
        {
            $ret->result = "failure";
            $ret->error = "Error occurred: the specified user {$userid} is not valid or there is some problem in its definition";
            echo json_encode($ret);
            return;
        }
        if (is_numeric($vtuserid))
        {
            $sql = "INSERT INTO user2vtuser (userid, vtuserid) VALUES ('{$userid}','{$vtuserid}')";
            $result = $link->query($sql);
            if (!$result)
            {
                $ret->result = "failure";
                $ret->error = "Error occurred in updating infos";
                echo json_encode($ret);
                return;
            }
        }
        echo json_encode($ret);
        return;
    }

    public function ws___getUtentiVT() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT a.id, a.user_name, a.first_name, a.last_name,a.status, c.rolename FROM vtiger_users a "
                . "LEFT JOIN vtiger_user2role b ON (a.id = b.userid) LEFT JOIN vtiger_role c ON (b.roleid = c.roleid);";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving users: " . $link->error);
            return;
        }
    }

    public function configurazioni() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("Amministrazione" => "sonosuperfigo", "Configurazioni" => "last")));
        $view = $this->getActionView();
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
        $view->set("dirty", '$dirty');
        $view->set("invalid", '$invalid');
    }

}
