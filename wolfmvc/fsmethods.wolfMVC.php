<?php

namespace WolfMVC {

    /**
     * Fornisce metodi derivati per trattare array
     */
    class FsMethods {

        private function __construct() {
            // do nothing
        }

        private function __clone() {
            // do nothing
        }

        public static function pathToArray($path, $with_stats = false) {
            $out = array();
            $path = rtrim(trim($path),"/");
            if (is_dir($path))
            {
                $dout = array();
                $dir = opendir($path);
                while (($file = readdir($dir)) !== false) {
                    $ft = filetype($path . "/" . $file);
                    if ($ft === "file"){
                        if ($with_stats){
                            $dout[] = $file;
                        }
                        else {
                            $dout[] = $file;
                        }
                    }
                    else if ($ft === "dir" && $file !== "." && $file !== ".."){
                        $dout[] = static::pathToArray($path . "/" . $file, $with_stats);
                    }
                }
                $out[$path] = $dout;
            }
            else if (is_file($path))
            {
                $out[] = $path;
            }
            return $out;
        }
        public static function pathLinearize($path, $with_stats = false) {
            $out = array();
            $path = rtrim(trim($path),"/");
            if (is_dir($path))
            {
                $dout = array();
                $dir = opendir($path);
                while (($file = readdir($dir)) !== false) {
                    $ft = filetype($path . "/" . $file);
                    if ($ft === "file"){
                        if ($with_stats){
                            $dout[] = array($file,$path);
                        }
                        else {
                            $dout[] = array($file,$path);
                        }
                    }
                    else if ($ft === "dir" && $file !== "." && $file !== ".."){
                        $dout = array_merge($dout,static::pathLinearize($path . "/" . $file, $with_stats));
                    }
                }
                $out = array_merge($out,$dout);
            }
            else if (is_file($path))
            {
                $out[] = $path;
            }
            return $out;
        }

    }

}