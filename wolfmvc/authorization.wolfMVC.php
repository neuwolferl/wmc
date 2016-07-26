<?php
namespace WolfMVC {

    use WolfMVC\Base as Base;
    use WolfMVC\View as View;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Template as Template;
    use WolfMVC\Controller\Exception as Exception;
    
    /**
     * Fornisce metodi di autenticazione
     * @todo: rivedere completamente
     * @todo: incapsulare, spostare
     * @todo: produrre esempi
     */
    class Authorization extends Base {

        public static function isActive($controller, $action) {
            if (SITE_VERSION === 'tsnwtest'){
                return TRUE;
            }
            $sql = "SELECT isactive FROM pages WHERE controller = '" . self::anti_injection($controller) . "' AND action = '" . self::anti_injection($action) . "'";
            $db = Registry::get("database_tsnw");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }

            $result = $link->query($sql);
            if ($result)
            {
                if ($result->num_rows === 1)
                {
                    $res = $result->fetch_all(MYSQLI_ASSOC);
                    
                    if ($res[0]["isactive"] === "1")
                    {
                        return TRUE;
                    }
                    else
                    {
                        header("Location: " . self::pathTo("error", "unavailablePage", array($controller . " " . $action)));
                    }
                }
                else
                {
                    header("Location: " . self::pathTo("error", "unavailablePage", array($controller . " " . $action)));
                }
            }
            else
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }
        }

        public static function isAuthorized($controller, $action, $lock = false) {
            if (SITE_VERSION === 'tsnwtest'){
                return TRUE;
            }
            $sql = "SELECT id FROM pages WHERE controller = '" . self::anti_injection($controller) . "' AND action = '" . self::anti_injection($action) . "'";
//            echo $sql;
            $db = Registry::get("database_tsnw");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }
            $result = $link->query($sql);
            if ($result)
            {
                if ($result->num_rows === 1)
                {
                    $res = $result->fetch_all(MYSQLI_ASSOC);
                    $pageid = $res[0]["id"];
                }
                else
                {
                    header("Location: " . self::pathTo("error", "unavailablePage", array($controller . " " . $action)));
                }
            }
            else
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }
            $session = \WolfMVC\Registry::get("session");
            if (!$session->get("googleUser"))
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }
            $googleId = $session->get("googleUser");
            $googleId = $googleId["id"];
            $sql = "SELECT id FROM googleusers WHERE googleid = '{$googleId}'";
            $result = $link->query($sql);
            if ($result)
            {
                if ($result->num_rows === 1)
                {
                    $res = $result->fetch_all(MYSQLI_ASSOC);
                    $userid = $res[0]["id"];
                }
                else
                {
                    header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
                }
            }
            else
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }
            $sql = "SELECT id FROM pageuserauth WHERE userid = '{$userid}' AND pageid = '{$pageid}'";
            $result = $link->query($sql);
            if ($result)
            {
                if ($result->num_rows === 1)
                {
                    return TRUE;
                }
                else
                {
                    header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
                }
            }
            else
            {
                header("Location: " . self::pathTo("error", "", array($controller . " " . $action)));
            }
        }

        public static function anti_injection($input) {
            $pulito = strip_tags(addslashes(trim($input)));
            $pulito = str_replace("'", "\'", $pulito);
            $pulito = str_replace('"', '\"', $pulito);
            $pulito = str_replace(';', '\;', $pulito);
            $pulito = str_replace('--', '\--', $pulito);
            $pulito = str_replace('+', '\+', $pulito);
            $pulito = str_replace('(', '\(', $pulito);
            $pulito = str_replace(')', '\)', $pulito);
            $pulito = str_replace('=', '\=', $pulito);
            $pulito = str_replace('>', '\>', $pulito);
            $pulito = str_replace('<', '\<', $pulito);
            return $pulito;
        }

        public static function pathTo($controller = "", $action = "", $parameters = array()) {
            $path = SITE_PATH;
            ;
            if ($controller !== "")
            {
                $path .= $controller . "/";
                if ($action !== "")
                {
                    $path .= $action . "/";
                    if (is_array($parameters))
                    {
                        $path .= join("/", $parameters);
                    }
                }
            }
            return $path;
        }

    }

}
?>