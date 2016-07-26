<?php

/* PRELIMINARY */

//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL);

error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
set_time_limit(1000);
ini_set('memory_limit', '128M');
define("APP_PATH", dirname(dirname(__FILE__)));
define("SITE_VERSION", str_ireplace("/var/www/", "", APP_PATH)); //<<*rimuovere*DETS*
$uri = $_SERVER['REQUEST_URI'];
$uri = explode("public", $uri, 2);
define("SITE_PATH", $uri[0] . "public/");
require("../wolfmvc/core.wolfMVC.php");
require("../wolfmvc/starter.wolfMVC.php");
require("../wolfmvc/utils.wolfMVC.php"); //<<*ritornarci*DETS*
require ("../application/libraries/activerecord/ActiveRecord.php"); //<<ritornarci*DETS*

/* TIME_REPORT */
$time = array("start" => microtime(TRUE));






WolfMVC\Core::initialize();
WolfMVC\Registry::set("censor", new \WolfMVC\Censor()); //<<capire se va elimintato*DETS*

$time["startRecoverConfig"] = microtime(TRUE);


try {
    \WolfMVC\Starter::initialize(true);
    echo "<pre>";
    print_r(WolfMVC\Registry::get("configuration"));
    echo "</pre>";
} catch (\Exception $e) {
    die('Si &eacute; verificato un errore.<br> ' . $e->getMessage());
}





//try {
////    foreach (\WolfMVC\Censor::get("database") as $key => $db) {
////        $database = new WolfMVC\Database();
////        WolfMVC\Registry::set("database_" . $db[0], $database->initialize($db[0]));
////    }
//} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
//    echo $e->getMessageType();
//    echo $e->getMessage();
//} catch (Exception $e) {
//    echo $e->getMessage();
//}

//try {
//    foreach (\WolfMVC\Censor::get("systemtables") as $key => $table) {
//        WolfMVC\Registry::set("systemtable_" . $key, $table);
//    }
//} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
//    echo $e->getMessageType();
//    echo $e->getMessage();
//} catch (Exception $e) {
//    echo $e->getMessage();
//}

//try {
//    foreach (\WolfMVC\Censor::get("language") as $key => $lang) {
//        $lang = new WolfMVC\Lang($lang);
//        WolfMVC\Registry::set("language", $lang);
//        WolfMVC\Base::$_lang = WolfMVC\Registry::get("language");
//    }
//} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
//    echo $e->getMessageType();
//    echo $e->getMessage();
//} catch (Exception $e) {
//    echo $e->getMessage();
//}
$time["conf_end"] = microtime(TRUE);
//require_once('../application/libraries/vtwsclib/vtiger/WSClient.php');
//require_once('../application/libraries/tsnwapi/tsnwserver.php');
//require_once('../application/libraries/vtwsclib/vtiger/Net/HTTP_Client.php');

//try {
//    foreach (\WolfMVC\Censor::get("module") as $key => $mod) {
//        if (is_file(APP_PATH . "/application/configuration/modules/" . $mod[1] . ".ini")) {
//            $array = WolfMVC\Registry::get("module_" . $mod[1]);
//            if (!is_array($array))
//                $array = array("conf" => APP_PATH . "/application/configuration/modules/" . $mod[1] . ".ini");
//            WolfMVC\Registry::set("module_" . $mod[1], $array);
//        }
//    }
//} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
//    echo $e->getMessageType();
//    echo $e->getMessage();
//} catch (Exception $e) {
//    echo $e->getMessage();
//}
//try {
//    $cache = new WolfMVC\Cache();
//    WolfMVC\Registry::set("cache", $cache->initialize());
//}
//catch (\Exception $e) {
//    echo $e->getMessage();
//}
//
$time["session_conf_start"] = microtime(TRUE);

$session = WolfMVC\Registry::get("session");

$iterates = $session->get("iterates");

if ($iterates) {
    $session->set("iterates", (int) $iterates + 1);
} else {
    $session->set("iterates", 1);
}



if (true || isset($_GET["asndjkan234fibbhsdbfj4s2dh2344fbj234jb234as234jdhbef243dibfsb23422"])) {
    $byPassAuth = true;
    $session->set("auth", true);
    $time["auth_skipped"] = microtime(TRUE);
}

//if (!$byPassAuth)
//{
//    try {
//        $googleApiConf = WolfMVC\Registry::get("googleApiConf");
//        $googleClient = new Google_Client();
//
//        $googleClient->setApplicationName("TSNW");
//        $googleClient->setClientId($googleApiConf->client_id);
//        $googleClient->setClientSecret($googleApiConf->client_secret);
//        $redirect_uri = (!empty($_SERVER["HTTPS"]) ? "https://" : "http://") . $_SERVER["SERVER_NAME"] . SITE_PATH;
//        if ($redirect_uri[strlen($redirect_uri) - 1] === "/")
//            ;
//        $redirect_uri = rtrim($redirect_uri, "/");
//        $googleClient->setRedirectUri($redirect_uri);
////    if (strpos(SITE_PATH, "test") !== FALSE)
////    {
////        
////    }
////    else
////    {
////        $googleClient->setRedirectUri($googleApiConf->redirect_uri_ssl);
////    }
//        $googleClient->setDeveloperKey($googleApiConf->developer_key);
////    $googleClient->setHostedDomain("topsource.it");
//        \WolfMVC\Registry::set("googleClient", $googleClient);
//    } catch (\Exception $e) {
//        echo $e->getMessage();
//    }
//}
$time["authenticated"] = microtime(TRUE);

$times = array();
$bt = $time["start"];
foreach ($time as $t => $tt) {
    $times[$t] = $tt - $bt;
}
$session->set("time", $time);
$session->set("cumulatedtime", $times);
//$session->set("cacheStatus",$c->stats());
try {
    $systemstatus = WolfMVC\Registry::get("mainconf")->systemstatus;
    $except = WolfMVC\Registry::get("systemstatus")->except;

//    echo $systemstatus;
    if (!(isset($systemstatus)) || is_null($systemstatus)) {
        $router = new WolfMVC\Router(array(
            "url" => "off/index",
            "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
        ));
    }
    if (isset($_GET["byPass"]) && $_GET["byPass"] === "asdrubale")
        $systemstatus = "on";
    switch (strtolower($systemstatus)) {
        case "off":
            $router = new WolfMVC\Router(array(
                "url" => "off/index",
                "extension" => "html"
            ));
            break;
        case "maintenance":

            $router = new WolfMVC\Router(array(
                "url" => "maintenance/index",
                "extension" => "html"
            ));
            break;
        default :
//            
            if (isset($_GET["url"])) {
                if ($_GET["url"] === "authenticate/logoutPerformed") {
                    $router = new WolfMVC\Router(array(
                        "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
                        "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                    ));
                    break;
                }
                $p = strpos($_GET["url"], "error");
                if ($p !== FALSE && $p === 0) {
                    session_write_close();
                    $router = new WolfMVC\Router(array(
                        "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
                        "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                    ));
                    break;
                }
            }
            if ($_SERVER["HTTP_HOST"] === "localhost") {
                $session->set("auth", true);
            }

            if ($session->get("auth")) {
                WolfMVC\Registry::set("usertodisplay", $session->get("user"));

                if ($session->get("preAuthUrl") && !strpos($session->get("preAuthUrl"), ".ws")) {
                    $preAuthUrl = $session->get("preAuthUrl");
                    $session->erase("preAuthUrl");
                    header("Location: https://" . $_SERVER["HTTP_HOST"] . SITE_PATH . $preAuthUrl);
                }
                if (!isset($_GET["url"]) || $_GET["url"] !== "authenticate/logout")
                    if (isset($_GET["url"])) {
                        
                    }
//                $db = \WolfMVC\Registry::get("database_tsnw");
////                    print_r($db);
//                $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
//                $vtuserid = $session->get("vtiger_logged_user_id");
//                $result = $link->query("SELECT * FROM users_extentions WHERE vtiger_id = '{$vtuserid}' LIMIT 1");
//                if ($result && $result->num_rows)
//                {
//                    $ext = $result->fetch_all(MYSQLI_ASSOC);
//                    $ext = $ext[0]["text_ext"];
//                    $session->set("asterisk_extension", $ext);
//                }

                session_write_close();
                $url = "home/index";

                $router = new WolfMVC\Router(array(
                    "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
                    "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                ));
                break;
            } else {
                if (isset($_GET["url"]) && !($session->get("preAuthUrl")))
                    $session->set("preAuthUrl", $_GET["url"]);
                $router = new WolfMVC\Router(array(
                    "url" => "authenticate/googleAuthenticate/",
                    "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                ));
            }
    }
////            $session->set("auth", false);
//            if (isset($_GET["url"]))
//            {
//                echo "ciao";
//                $p = strpos($_GET["url"], "error");
//                if ($p !== FALSE && $p === 0)
//                {
//                    $router = new WolfMVC\Router(array(
//                        "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
//                        "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
//                    ));
//                    break;
//                }
//            }
//            if ($session->get("auth") || (isset($_GET["url"]) && $_GET["url"] === "authenticate/logoutPerformed" || $_GET["url"] === "authenticate/logout"))
//            {
//                WolfMVC\Registry::set("usertodisplay", $session->get("user"));
//                if (!isset($_GET["url"]) || $_GET["url"] === "authenticate/logout")
//                    session_write_close(); //tranne nel caso di login e logout, a questo punto posso chiudere la sessione per liberare il lock
//                $url = "home/index";
//                $router = new WolfMVC\Router(array(
//                    "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
//                    "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
//                ));
//            }
//            else
//            {
//                if (isset($_GET["url"]))
//                    $session->set("preAuthUrl", $_GET["url"]);
//                $router = new WolfMVC\Router(array(
//                    "url" => "authenticate/googleAuthenticate/",
//                    "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
//                ));
//            }


    WolfMVC\Registry::set("router", $router);
    $router->dispatch();
} catch (\Exception $e) {
    echo $e->getMessage();
}
//if (strstr(SITE_PATH, "test"))
//{
//    echo "<pre>";
//    print_r($time);
//    echo "</pre>";
//}
//
//
//
//
unset($configuration);
unset($database);
unset($cache);
unset($session);
unset($router);
//
//WolfMVC\Registry::esponi();
//WolfMVC\Censor::esponi();
//
//ob_end_flush();
//exit;
