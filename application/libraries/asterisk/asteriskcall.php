<?php

namespace Asterisk {

    class Asteriskcall {
        /**
         * 
         * @param type $id è l'id dell'utente VT che chiama
         * @param type $number è il numero da chiamare
         * @param type $extension è il nome utente usato per loggarsi alla barra telefonica
         */
        
        function startCall($id, $number, $extension) { //ext = alberto_brudaglio
//	
//	global $current_user, $adb,$log;
//	require_once 'include/utils/utils.php';
//	require_once 'modules/PBXManager/utils/AsteriskClass.php';
//	require_once('modules/PBXManager/AsteriskUtils.php');
//	$id = $current_user->id;
//	$number = $_REQUEST['number'];
//	$record = $_REQUEST['recordid'];
//	$result = $adb->query("select * from vtiger_asteriskextensions where userid=".$current_user->id);
//	$extension = $adb->query_result($result, 0, "asterisk_extension");
            $data = array(
                "server" => "95.110.157.99",
                "port" => "5038",
                "username" => "xcallycrm",
                "password" => "899LdcallyCRMofevT1234",
                "version" => "1.6"
            );
            if (!empty($data))
            {
                $server = $data['server'];
                $port = $data['port'];
                $username = $data['username'];
                $password = $data['password'];
                $version = $data['version'];
                $errno = $errstr = NULL;
                $sock = fsockopen($server, $port, $errno, $errstr, 1);
                stream_set_blocking($sock, false);
                if ($sock === false)
                {
                    echo "Socket cannot be created due to error: $errno:  $errstr\n";
//			$log->debug("Socket cannot be created due to error:   $errno:  $errstr\n");
                    exit(0);
                }
                $asterisk = new Asterisk($sock, $server, $port);
                $this->loginUser($username, $password, $asterisk);
//                echo "<pre>";
//                print_r($asterisk);
//                echo "</pre>";
                
                $asterisk->transfer($extension, $number);
            }
        }

        function loginUser($username, $password, $asterisk) {
            if (!empty($username) && !empty($password))
            {
                $asterisk->setUserInfo($username, $password);
                if (!$asterisk->authenticateUser())
                {
                    echo "Cannot login to asterisk using\n
					User: $username\n
					Password: $password\n
					Please check your configuration details.\n";
                    echo "cant' authenticate";
                    exit(0);
                }
                else
                {
//                    echo "authenticate ok";
                    return true;
                }
            }
            else
            {
                echo "Missing username and/or password";
                return false;
            }
        }

    }

}
?>
