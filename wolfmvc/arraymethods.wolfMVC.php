<?php

namespace WolfMVC {

    /**
     * Fornisce metodi derivati per trattare array
     */
    class ArrayMethods {

        private function __construct() {
            // do nothing
        }

        private function __clone() {
            // do nothing
        }

        /**
         * restituisce l'array di input privato degli elementi considerati empty.
         * @param array $array
         * @return array
         */
        public static function clean($array) {
            return array_filter($array, function($item) {
                return !empty($item);
            });
        }

        /**
         * Esegue trim su tutti gli elementi dell'array e restituisce l'array così modificato.
         * @param array $array
         * @return array
         */
        public static function trim($array) {
            return array_map(function($item) {
                return trim($item);
            }, $array);
        }

        /**
         * Restituisce il primo elemento dell'array passato, ovvero l'elemento con la chiave di indice 0.
         * @param array $array
         * @return mixed
         */
        public static function first($array) {
            if (sizeof($array) == 0)
            {
                return null;
            }

            $keys = array_keys($array);
            return $array[$keys[0]];
        }

        /**
         * Restituisce l'ultimo elemento dell'array passato, ovvero l'elemento con la chiave di indice length-1.
         * @param array $array
         * @return mixed
         */
        public static function last($array) {
            if (sizeof($array) == 0)
            {
                return null;
            }

            $keys = array_keys($array);
            return $array[$keys[sizeof($keys) - 1]];
        }

        /**
         * Trasforma un array in oggetto.
         * @param array $array
         * @return \stdClass
         */
        public static function toObject($array) {
            $result = new \stdClass();

            foreach ($array as $key => $value) {
                if (is_array($value))
                {
                    $result->{$key} = self::toObject($value);
                }
                else
                {
                    $result->{$key} = $value;
                }
            }

            return $result;
        }

        /**
         * Appiattisce un array multidimensionale su uno monodimensionale
         * @param array $array
         * @param array $return
         * @return array
         */
        public function flatten($array, $return = array()) {
            foreach ($array as $key => $value) {
                if (is_array($value) || is_object($value))
                {
                    $return = self::flatten($value, $return);
                }
                else
                {
                    $return[] = $value;
                }
            }

            return $return;
        }

        public function toQueryString($array) {
            return http_build_query(
                    self::clean(
                            $array
                    )
            );
        }

        /**
         * Trasforma un array php nella stringa di definizione e dichiarazione di quell'array in Js.
         * Funziona con array monodimensionali i cui elementi sono stringhe.
         * @param array $array
         * @return string Una stringa del tipo "new Array(item1,item2,...,itemN)"
         */
        public static function toJSArray($array) {
            if (empty($array))
            {
                return "new Array()";
            }
            $string = "new Array(";
            foreach ($array as $item) {
                $string .= "'" . $item . "', ";
            }
            $string[strlen($string) - 2] = ")";
            return $string;
        }

        /**
         * Dato un vettore array(chiave => valore) e un secondo vettore array(labelPrima => labelDopo)
         * modifica le chiavi del primo vettore usando il secondo come traduzione.
         * Le chiavi non tradotte vengono eliminate se $keepNotTranslated è false (default = true)

         * @param type $input
         * @param type $translateVM
         * @param type $keepNotTranslated
         * @return string
         * @throws Exception
         */
        public static function translateVMLabels($input, $translateVM, $keepNotTranslated = true) {
            if (count($translateVM) !== count(array_unique($translateVM)))
            {
                throw new \Exception("translateVMLabels found colliding translations");
                return false;
            }
            if (empty($input))
            {
                return $input;
            }

            $output = array();
            foreach ($input as $k => $v) {
                if (isset($translateVM[$k]))
                {
                    if (!is_string($translateVM[$k]) && !is_int($translateVM[$k]))
                    {
                        print_r($translateVM[$k]);
                        throw new \Exception("translateVMLabels found invalid type in translations vm. ");
                        return false;
                    }
                    $output[$translateVM[$k]] = $v;
                }
                else if ($keepNotTranslated)
                {
                    $output[$k] = $v;
                }
            }
            return $output;
        }

        private static function match_questioned_string($str, $instr) {
//            echo $str . " IN " . $instr;
            if (!is_string($str) || !is_string($instr) || !strlen($instr))
            {
                return false;
            }
            if ($instr[0] === "?" && $instr[strlen($instr) - 1] === "?")
            {
                $exp = explode(",", str_ireplace("?", "", $instr));
                return in_array($str, $exp);
            }
            else
            {
                return false;
            }
        }

        /**
         * Data una value map (array) e un array di analoghe value map, confronta la prima con le seconde, restituendo l'indice della vm 
         * che corrisponde all'input dato o false se non viene trovata corrispondenza.
         * Se multiMatch è TRUE (default = FALSE ), nelle vm di cases i campi che iniziano e finiscono con ? sono considerati degli elenchi
         * di possibili valori accettabili, separati da virgole e il match viene riconosciuto se nell'omologo campo in vm c'è uno dei valori 
         * elencati
         * @param array $vm
         * @param array $cases
         * @throws Exception
         */
        public static function matchVMAmong($vm, $cases, $multiMatch = false) {
            if (!is_array($vm) || !is_array($cases))
            {
                throw new Exception("Invalid input in matchVM", 0, NULL);
            }
            foreach ($cases as $k => $c) {
//                echo "provo " . $k . "\n\r";
                $int = array_intersect_assoc($vm, $c);
                if (count($int) === count($vm))
                {
                    return $k;
                }
                else if ($multiMatch)
                {
//                    echo "qualcosa non quadra \n\r";
                    $check = true;
                    foreach ($c as $t => $v) {
                        if (!isset($vm[$t]))
                        {
                            $check = false;
                            break;
                        }
                        if (!isset($int[$t]) && strlen($v) && $v[0] === "?")
                        {
//                            echo "controllo {$t} \n\r";

                            $check = self::match_questioned_string($vm[$t], $v);
                        }
                        else if (!isset($int[$t]))
                        {
//                            echo "controllo {$t} << non è questioned\n\r";
                            $check = false;
                            break;
                        }
                    }
                    if ($check)
                    {
                        return $k;
                    }
                }
            }
            return FALSE;
        }

        public static function array_explore(array $arr, $fcn, array &$result = array(), array &$aux = array()) {
            foreach ($arr as $k => $v) {
                $fcn($k, $v, $result, $aux);
                if (is_array($v))
                {
                    static::array_explore($v, $fcn, $result, $aux);
                }
            }
            return $result;
        }

        public static function contained_keys(array $arr) {
            $out = array();
            foreach ($arr as $k => $v) {
                if (is_array($v))
                {
                    $out[] = $k;
                }
            }
            return $out;
        }

    }

}