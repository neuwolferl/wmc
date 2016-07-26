<?php

/*
 * Questo software è stato creato da Alberto Brudaglio. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio. All rights reserved.
 */

namespace WolfMVC {


    class Simplelog {

        /**
         *
         * @var int Useful for indentation
         */
        private $_level = 0;
        private $_log = array();

        /**
         * Produces an array of the type
         * 0 => {"message" => $mesage,"time"=>time of fcn call}
         * 1 => {"message" => $mesage,"time"=>time of fcn call}
         * @param mixed $message
         * @param mixed $log
         * @return array
         */
        public function __construct($log = array(), $level = 0) {
            $this->_level = $level;
            $this->_log = $log;
        }
        
  
        public function cloneLog($level = 0) {
            return new Simplelog($this->_log, $level);
        }
        
        public function log($message) {
            if (is_null($message)) {
                return;
            }
            if (!is_string($message)) 
                if (get_class($message) !== "WolfMVC\Simplelog")
                    return;
            $time = microtime(true);
            if (is_object($message) && get_class($message)== "WolfMVC\Simplelog") {
                $message = $message->cloneLog(1+$this->_level);
            }   
            $this->_log[] = array("time" => $time, "message" => $message);
            
        }

        /**
         * Utility to print 
         * @param bool $echoFlag toggle print/return
         */
        public function publish( $echoFlag = FALSE) {
            
            ob_start();
           
            foreach ($this->_log as $k => $l) {
                $row = "\n";
                for ($i=0;$i<$this->_level;$i++)
                    $row .="\t";
                if (is_object($l["message"]) && get_class($l["message"])=== "WolfMVC\Simplelog"){
                    echo $row . $k . ".  " . $l["time"] . "  ";
                    $l["message"]->publish(TRUE);
                }
                else 
                    echo $row . $k . ".  " . $l["time"] . "  " . $l["message"];
            }
            $out = ob_get_contents();
            ob_end_clean();
            if ($echoFlag) {
                echo $out;
            } else {
                return $out;
            }
        }

    }

}