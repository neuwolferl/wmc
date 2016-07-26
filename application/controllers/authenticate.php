<?php

use WolfMVC\Controller as Controller;

class Authenticate extends Controller {

    public function script_including() {

        $this->_system_js_including .="<link href=\"" . SITE_PATH . "css/authenticate/authenticate.css\" rel=\"stylesheet\">";
//        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/bs/bootstrap.min.css\">";
    }

    private function encrypt_password($user_password, $username, $crypt_type) {
// encrypt the password.
        $salt = substr($username, 0, 2);
// For more details on salt format look at: http://in.php.net/crypt
        if ($crypt_type == 'MD5')
        {
            $salt = '$1$' . $salt . '$';
        }
        elseif ($crypt_type == 'BLOWFISH')
        {
            $salt = '$2$' . $salt . '$';
        }
        elseif ($crypt_type == 'PHP5.3MD5')
        {
//only change salt for php 5.3 or higher version for backward
//compactibility.
//crypt API is lot stricter in taking the value for salt.
            $salt = '$1$' . str_pad($salt, 9, '0');
        }

        $encrypted_password = crypt($user_password, $salt);
        return $encrypted_password;
    }

    public function index() {
        
    }

    public function logout() {
        $_SESSION = array();
        $layout = $this->getLayoutView();
        $layout->erase("user");
        $session = \WolfMVC\Registry::get("session");
        $session->set("auth", false);
        header("Location: " . SITE_PATH . "authenticate/logoutPerformed");
    }

    public function logoutPerformed() {
        $layout = $this->getLayoutView();
        $layout->set("noUser");
        $layout->erase("user");
        $session = \WolfMVC\Registry::get("session");
        $session->set("auth", false);
        session_destroy();
    }

    private function googleRequest() {
        $googleClient = \WolfMVC\Registry::get("googleClient");
        $session = \WolfMVC\Registry::get("session");
        $googleOauth = new \Google_Auth_OAuth2($googleClient);
        $googleClient->setScopes(array('email', 'profile', 'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/admin.directory.user.readonly'));
        if (isset($_GET["code"]))
        {
            $googleClient->authenticate($_GET["code"]);
            $session->set("googleToken", $googleClient->getAccessToken());
            $session->set("redirect_after_code", $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"]);
            header("Location: " . filter_var(SITE_PATH, FILTER_SANITIZE_URL));
        }
        if ($session->get("googleToken"))
        {
            $googleClient->setAccessToken($session->get("googleToken"));
        }
        if ($googleClient->getAccessToken())
        {
            $ticket = $googleClient->verifyIdToken();
            $googlePlus = new \Google_Service_Plus($googleClient);
            $user = array();
            $person = $googlePlus->people->get("me");
            $user["displayName"] = $person->getDisplayName();
            $user["name"] = $person->getName();

            if ($ticket)
            {
                $data = $ticket->getAttributes();
                $user["id"] = $data["payload"]["sub"];
                $user["email"] = $data["payload"]["email"];
                $user["hd"] = $data["payload"]["hd"];
                $user["verified"] = $data["payload"]["verified_email"];
            }
            else
            {
                
            }
            $session->set("googleToken", $googleClient->getAccessToken());
            $session->set("googleUser", $user);
        }
        else
        {
            $authUrl = $googleClient->createAuthUrl();
            header("Location: " . filter_var($authUrl, FILTER_SANITIZE_URL));
        }
    }

    public function googleAuthenticate() {

        $googleClient = \WolfMVC\Registry::get("googleClient");
        $session = \WolfMVC\Registry::get("session");
        $googleOauth = new \Google_Auth_OAuth2($googleClient);
        $googleClient->setScopes(array('email', 'profile', 'https://www.googleapis.com/auth/userinfo.profile'));

        if (!$session->get("googleUser"))
        {
            $this->googleRequest();
        }
        else
        {
            $this->isFeasibleUser(); // controllo che l'utente vada bene
            $internalUser = $this->isInternalUser(); // cerco l'utente tra quelli già registrati a sistema
            if ($internalUser) // se l'ho trovato autorizzo e vado avanti
            {
//                $session->set("auth", true);
//                $session->set("gLogged", true);
//                $session->set("user", $internalUser['first_name'] . " " . $internalUser['last_name']);
//                $session->set("userid", $internalUser['id']);
            }
            else //altrimenti lo creo
            {
                $create = $this->createInternalUser();
                $internalUser = $this->isInternalUser(); // ricontrollo e uniformo le variabili
            }
            if (!$internalUser)
            { //se sono qui deve esserci l'utente interno, altrimenti houston we've got a problem!
                throw new \Exception("Error occurred during user info mastering", 0, NULL);
            }
            else
            {

                $session->set("auth", true);
                $session->set("gLogged", true);
                $session->set("user", $internalUser['first_name'] . " " . $internalUser['last_name']);
                $session->set("userid", $internalUser['id']);
            }
//cerco utente vtiger
            if (isset($create))
            {// nel caso di utente appena creato cerco direttamente in vt
                $vtUser = $this->searchInVT();
                if ($vtUser) // c'è un utente VT che corrisponde
                {
                    if (!$this->searchPreviousVTAssociation($vtUser["id"]))// controllo se è già associato
                    {
                        // se non c'è l'associazione la creo
                        $this->createVTASS($vtUser["id"]);
                    }
                    $session->set("vtiger_logged_user_id", $vtUser["id"]); // e uso l'id
                }
                else
                { // non c'è un utente vt, non faccio nulla
                }
            }
            else
            { //non l'ho creato adesso, c'era già
                $vtuserid = $this->searchVTAssociation();
                if ($vtuserid)
                {
                    $session->set("vtiger_logged_user_id", $vtuserid);
                    
                }
            }
            header("Location: " . filter_var(SITE_PATH, FILTER_SANITIZE_URL));
        }
    }

    private function isFeasibleUser() {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");
        if (!isset($gUser["hd"]) || $gUser["hd"] !== "topsource.it")
        {
            header("Location: " . filter_var(SITE_PATH . "error/noAuth", FILTER_SANITIZE_URL));
            exit;
        }
    }

    private function isInternalUser() {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");

        $db = WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema) or die("");
        $sql = "SELECT * FROM googleusers WHERE googleid = '{$gUser["id"]}' "
                . "AND first_name = '{$gUser["name"]["givenName"]}' "
                . "AND last_name = '{$gUser["name"]["familyName"]}'";
        $result = $link->query($sql);
        if ($result)
        {
            $rows = $result->num_rows;
            if ($rows > 0)
            { // c'è già un utente
                $row = $result->fetch_array(MYSQLI_ASSOC);
                return $row;
            }
            else
            {
                return null;
            }
        }
        else
        {
            throw new \Exception("Error occurred during user info retrieve", 0, NULL);
        }
    }

    private function createInternalUser() {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema) or die("");
        $sql = "INSERT INTO googleusers (googleid,display_name, first_name, last_name, email, hd, verified) "
                . "VALUES ('{$gUser["id"]}', '{$gUser["displayName"]}', '{$gUser["name"]["givenName"]}', '{$gUser["name"]["familyName"]}',"
                . "'{$gUser["email"]}', '{$gUser["hd"]}', '{$gUser["verified"]}')";
        $result = $link->query($sql);
        if (!$result)
        {
            throw new \Exception("Error occurred during user info update. " . $link->error, 0, NULL);
        }
        $affrows = $link->affected_rows;
        if ($affrows > 0)
        {
            return true;
        }
        else
        {
            throw new \Exception("Error occurred during user info update: something missing...", 0, NULL);
        }
    }

    private function searchInVT() {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");
        $db = WolfMVC\Registry::get("database_vtiger");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema) or die("");
        $sql = "SELECT * FROM vtiger_users WHERE "
                . "(email1 = '{$gUser["email"]}' OR email2 = '{$gUser["email"]}') "
                . "AND first_name = '{$gUser["name"]["givenName"]}' "
                . "AND last_name = '{$gUser["name"]["familyName"]}'";
        $result = $link->query($sql);
        if (!$result)
        {
            throw new \Exception("Error occurred during VTuser info retrieve <br>" . $sql . "<br>" . $link->error, 0, NULL);
        }
        else
        {
            $numrows = $result->num_rows;
            if ($numrows > 0)
            {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                return $row;
            }
            else
            {
                return null;
            }
        }
    }

    private function searchPreviousVTAssociation($vtuserid) {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");
        $db = WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema) or die("");
        $sql = "SELECT * FROM googleuser2vtuser WHERE "
                . "userid = '{$gUser["id"]}' "
                . "AND vtuserid = '{$vtuserid}'";
        $result = $link->query($sql);
        if (!$result)
        {
            throw new \Exception("Error occurred during VTuser info retrieve <br>" . $sql . "<br>" . $link->error, 0, NULL);
        }
        else
        {
            $numrows = $result->num_rows;
            if ($numrows > 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    private function searchVTAssociation() {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");
        $db = WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema) or die("");
        $sql = "SELECT * FROM googleuser2vtuser WHERE "
                . "userid = '{$gUser["id"]}'";
        $result = $link->query($sql);
        if (!$result)
        {
            throw new \Exception("Error occurred during VTuser info retrieve <br>", 0, NULL);
        }
        else
        {
            $numrows = $result->num_rows;
            if ($numrows > 0)
            {
                $row = $result->fetch_array(MYSQL_ASSOC);
                return $row["vtuserid"];
            }
            else
            {
                return FALSE;
            }
        }
    }

    private function createVTASS($vtuserid) {
        $session = \WolfMVC\Registry::get("session");
        $gUser = $session->get("googleUser");
        $db = WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->host, $db->username, $db->password, $db->schema) or die("");

        $insert = "INSERT INTO googleuser2vtuser (userid, vtuserid) VALUES ('{$gUser["id"]}','{$vtuserid}')";
        $result = $link->query($insert);
        if ($result)
        {
            return true;
        }
        else
        {
            throw new \Exception("Error occurred during VTuser association <br>", 0, NULL);
        }
    }

}

?>