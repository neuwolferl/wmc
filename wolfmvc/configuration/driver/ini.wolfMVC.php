<?php

namespace WolfMVC\Configuration\Driver {

    use WolfMVC\ArrayMethods as ArrayMethods;
    use WolfMVC\Configuration as Configuration;
    use WolfMVC\Configuration\Exception as Exception;

    class Ini extends Configuration\Driver {

        /**
         * Ricorsivamente spezzetta le chiavi in dot.notation trasformandole in profondità di array
         * @param mixed $config La configurazione in fase di gerarchizzazione
         * @param mixed $key
         * @param mixed $value
         * @return mixed
         */
        protected function _pair($config, $key, $value) {
            if (strstr($key, ".")) {
                $parts = explode(".", $key, 2);

                if (empty($config[$parts[0]])) {
                    $config[$parts[0]] = array();
                }

                $config[$parts[0]] = $this->_pair($config[$parts[0]], $parts[1], $value);
            } else {
                $config[$key] = $value;
            }

            return $config;
        }

        /**
         * Controlla che path sia non vuoto. Poi controlla che il file indicato non sia già stato parsato.
         * Se non è stato parsato usa ob (output buffering) per catturare il risultato di un include sul
         * file di configurazione indicato in modo da poter inserire la configurazione in qualunque
         * parte relativamente al percorso include di PHP. Il file ini viene parsato e le coppie chiave valore
         * sono salvate in $pairs. Se $pairs risulta vuoto lanciamo eccezione di sintassi altrimenti usa la funzione 
         * _pair per generare la corretta gerarchia e infine convertiamo l'array associativo in oggetto e lo cachamo 
         * oppure lo restituiamo.
         * @param string $path
         * @return mixed/boolean???
         * @throws Exception\Argument
         * @throws Exception\Syntax
         */
        public function parse($path) {
//            echo "<br>Tento di parsare il file ".$path."<br>";
            if (empty($path)) {
                echo $path;
                throw new Exception\Argument("\$path argument is not valid");
            }

            if (!isset($this->_parsed[$path])) {
                $config = array();

                ob_start();
                include("{$path}.ini");
                $string = ob_get_contents();
                ob_end_clean();
                $pairs = parse_ini_string($string,false,INI_SCANNER_RAW);
                if ($pairs == false) {
                    echo $path;
                    print_r(file($path.".ini"));
                    echo file_get_contents($path . ".ini");
                    throw new Exception\Syntax("Could not parse configuration file " . $path);
                }

                foreach ($pairs as $key => $value) {
                    $config = $this->_pair($config, $key, $value);
                }

                $this->_parsed[$path] = ArrayMethods::toObject($config);
            }


            return $this->_parsed[$path];
        }

    }

}