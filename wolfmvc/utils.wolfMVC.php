<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {


    class Utils {

        /**
         * Esegue stripslashes ricorsivamente sugli elementi degli array passati
         * @param array $array
         * @return mixed
         */
        public static function _strips($array) {
            if (is_array($array)) {
                return array_map(__CLASS__ . "::_strips", $array);
            }
            return stripslashes($array);
        }
        /**
         * Produces an array of the type
         * 0 => {"message" => $mesage,"time"=>time of fcn call}
         * 1 => {"message" => $mesage,"time"=>time of fcn call}
         * @param mixed $message
         * @param mixed $log
         * @return array
         */
        public static function _simpleLog($message, $log = null) {
            if (is_null($message)) {
                return $log;
            }
            $time = microtime(true);
            if (is_null($log) || !is_array($log)) {
                $log = array();
            }
            if (!is_string($message)){
                $message = self::_publishSimpleLog($message);
            }
            $log[] = array("time" => $time, "message" => $message);
            return $log;
        }
       /**
        * Utility to print a simpleLog
        * @param array $log
        */
        public static function _publishSimpleLog($log,$echoFlag=FALSE) {
            ob_start();
            if (is_null($log) || !is_array($log)) {
                return "";
            }
            foreach($log as $k=>$l){
                echo "\n".$k.".  ".$l["time"]."  ".$l["message"];
            }
            $out = ob_get_contents();
            ob_end_clean();
            if ($echoFlag){
                echo $out;
            }
            else {
                return $out;
            }
        }

    }

}