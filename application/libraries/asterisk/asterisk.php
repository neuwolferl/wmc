<?php

namespace Asterisk {

    class Asterisk {

        var $address;
        var $port;
        var $userName;
        var $password;
        var $sock;
        var $db;
        var $log;
        var $queue;

        /**
         * this is the constructor of the class, it initializes the parameters of the class
         * @param resource $sock - a socket type
         * @param string $server - the asterisk server address
         * @param integer $port - the port number where to connect to the asterisk server
         */
        function __construct($sock, $server, $port) {
            $this->sock = $sock;
            $this->address = $server;
            $this->port = $port;
            $this->queue = array();
        }

        /**
         * this function sets the username and password for the asterisk object
         * @param string $userName - asterisk username
         * @param string $password - password for the user
         */
        function setUserInfo($userName, $password) {
            $this->userName = $userName;
            $this->password = $password;
        }

        /**
         * this function authenticates the user
         * @return - true on success else false
         */
        function authenticateUser() {
            $request = "Action: Login\r\n" .
                    "Username: " . $this->userName . "\r\n" .
                    "Secret: " . $this->password .
                    "\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function authenticateUser() Socket error.Cannot send.(function: fwrite)";
                exit(0);
            }
            sleep(1); //wait for the response to come
            $response = fread($this->sock, 4096); //read the response

            if (strstr($response, "Response") && (strstr($response, "Error") || strstr($response, "failed")))
            {
                return false;
            }
            else
            {
                return true;
            }
        }

        /**
         * create a call between from and to
         * @param string $from - the from number
         * @param sring $to - the to number
         * this function prepares the parameter $context and calls the createCall() function
         */
        function transfer($from, $to) {
//            $this->log->debug("in function transfer($from, $to)");
            if (empty($from) || empty($to))
            {
                
                echo "Not sufficient parameters to create the call: from".$from."  to".$to; 
//                $this->log->debug("Not sufficient parameters to create the call");
                return false;
            }

            //the caller would always be a SIP phone in our case
            if (!strstr($from, "SIP"))
            {
                $from = "SIP/$from";
            }
            $typeCalled = "";
            if (strpos($to, ":") !== FALSE)
            {
                $arr = explode(":", $to);
                if (is_array($arr))
                {
                    $typeCalled = $arr[0];
                    $to = trim($arr[1]);
                }
            }

            switch ($typeCalled) {
                case "SIP":
                    //$context = "default";
                    $context = "from-sip"; //"outbound-dialing";
                    break;
                case "PSTN":
                    //$context = "from-inside";//"outbound-dialing";
                    $context = "from-sip"; //"outbound-dialing";
                    break;
                default:
                    //$context = "default";
                    $context = "from-sip"; //"outbound-dialing";
            }
            $this->createCall($from, $to, $context);
        }

        /**
         * creates a call between $from and $to
         * @param string $from -the number from which to call
         * @param string $to - the number to which to call
         * @param string $context - the context of the call (e.g. local-extensions for local calls)
         */
        function createCall($from, $to, $context) {
            $arr = explode("/", $from);
            $request = "Action: Originate\r\n" .
                    "Channel: $from\r\n" .
                    "Exten: " . preg_replace('~[^0-9]~', "", $to) . "\r\n" .
                    "Context: $context\r\n" .
                    "Priority: 1\r\n" .
                    "Callerid: $arr[1]\r\n" .
                    "Async: yes\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function createcall() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }
        }

        function command($com, $arguments) {
            echo "<br><br>Comando richiesto:   " . $com . "<br><br>";
            switch ($com) {
                case 'listcommands':
                    $this->command_listcommands($arguments);
                    break;
                case 'events':
                    $this->command_events($arguments);
                    break;
                case 'sippeers':
                    $this->command_sippeers($arguments);
                    break;
                case 'status':
                    $this->command_status($arguments);
                    break;
                case 'agents':
                    $this->command_agents($arguments);
                    break;
                case 'ping':
                    $this->command_ping($arguments);
                    break;
                case 'command':
                    $this->command_command($arguments);
                    break;
            }
        }

        function command_events($arguments) {
            $request = "Action: Events\r\n" .
                    "Eventmask: {$arguments['eventmask']}\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_events() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
            echo "<pre>";
            $this->getAsteriskResponse();
            echo "</pre>";
        }

        function command_sippeers($arguments) {
            $request = "Action: Sippeers\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_events() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
        }

        function command_status($arguments) {
            $request = "Action: Status\r\n"
                    . "Channel: {$arguments['channel']}\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_events() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
        }

        function command_agents($arguments) {
            $request = "Action: Agents\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_agents() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
            sleep(1);
            $request = "Action: Agents\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_agents() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
        }

        function command_listcommands($arguments) {
            $request = "Action: ListCommands\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_ListCommands() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
        }

        function command_ping($arguments) {
            $request = "Action: Ping\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_ListCommands() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
        }
        function command_command($arguments) {
            $request = "Action: Command\r\n"
                    . "command: {$arguments["command"]}\r\n\r\n";
            if (!fwrite($this->sock, $request))
            {
                echo "in function command_ListCommands() Socket error.Cannot send.(function: fwrite)";
//                $this->log->debug("in function authenticateUser() Socket error.Cannot send.(function: fwrite)");
                exit(0);
            }

            sleep(1);
            echo "<pre>";
            $this->getAsteriskResponse();
            echo "</pre>";
        }

        /**
         * this is the destructor for the class :: it closes the opened socket
         */
        function __destruct() {
            fclose($this->sock);
        }

        /**
         * this function reads the socket for asterisk events and
         * creates a queue with the response arrays
         * 
         * @param boolean $echoFlag - if set no echos are performed (added since some ajax requests might use the function)
         * @return 	the event array present in the queue
         * 			if no array is present it returns a null
         */
        function getAsteriskResponse($echoFlag = true, $l = 4096) {
            if (sizeof($this->queue) == 0)
            {
                do {
                    $this->strData.=fread($this->sock, 10000);
                    $s = socket_get_status($this->sock);
                } while ($s['unread_bytes']);

                if ($echoFlag)
                {
                    echo $this->strData;
                }

//                $this->log->debug($this->strData);
                $arr = explode("\r\n\r\n", $this->strData);

                for ($i = 0; $i < sizeof($arr) - 1; $i++) {
                    $resp = $arr[$i];
                    $lines = explode("\r\n", $resp);
                    $obj = array();
                    foreach ($lines as $line) {
                        list($key, $value) = explode(":", $line);
                        $obj[$key] = trim($value);
                    }
                    $this->queue[] = $obj;
                }
                $this->strData = $arr[$i];
            }
            return array_shift($this->queue);
        }

//        function getAsteriskResponse($echoFlag = true,$l = 4096) {
//            if (sizeof($this->queue) == 0)
//            {
//                $this->strData.=fread($this->sock, $l);
//
//                if ($echoFlag)
//                {
//                    echo $this->strData;
//                }
//
////                $this->log->debug($this->strData);
//                $arr = explode("\r\n\r\n", $this->strData);
//
//                for ($i = 0; $i < sizeof($arr) - 1; $i++) {
//                    $resp = $arr[$i];
//                    $lines = explode("\r\n", $resp);
//                    $obj = array();
//                    foreach ($lines as $line) {
//                        list($key, $value) = explode(":", $line);
//                        $obj[$key] = trim($value);
//                    }
//                    $this->queue[] = $obj;
//                }
//                $this->strData = $arr[$i];
//            }
//            return array_shift($this->queue);
//        }
    }

}
?>