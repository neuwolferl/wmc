<?php

namespace WolfMVC {

    class FileMethods {

        public static function getStaticPropertiesRecursive($class) {
            $currentClass = $class;
            $joinedProperties = array();
            do {
                $reflection = new \ReflectionClass($class);
                $staticProperties = $reflection->getStaticProperties();
                foreach ($staticProperties as $name => $value) {
                    if (is_array($value))
                    {
                        if (isset($joinedProperties[$name]))
                        {
                            $joinedProperties[$name] = array_merge($value, $joinedProperties[$name]);
                        }
                        else
                        {
                            $joinedProperties[$name] = $value;
                        }
                    }
                    else
                    {
                        if (isset($joinedProperties[$name]))
                        {
                            $joinedProperties[$name][] = $value;
                        }
                        else
                        {
                            $joinedProperties[$name] = array($value);
                        }
                    }
                }
            } while ($class = get_parent_class($class));
            return $joinedProperties;
        }

        public static function searchPhpCode($in, array $pattern, $start, &$end, $withIndexes = false) {
            if (is_string($in))
            {
                $tokens = token_get_all($in);
            }
            else if (is_array($in))
            {
                $tokens = $in;
            }
            if (!is_array($tokens))
            {
                return array();
            }
            $found = array();
            $count = count($tokens);
            $tmpstart = $start;
            foreach ($pattern as $li => $l) {
                $look_up_found = false;
                for ($i = $tmpstart; $i < $count; $i++) {
                    $length = count($l);
                    $flag = true;
                    for ($j = 0; $j < $length; $j++) {
                        if (is_string($l[$j]))
                        {
                            if ($tokens[$i + $j] !== $l[$j])
                            {
                                $flag = false;
                                break;
                            }
                        }
                        else
                        {
                            if ($tokens[$i + $j][0] !== $l[$j])
                            {
                                $flag = false;
                                break;
                            }
                        }
                    }
                    if ($flag)
                    {
                        $ff = array();
                        for ($j = 0; $j < $length; $j++) {
                            if ($withIndexes)
                            {
                                if (is_string($l[$j]))
                                    $ff[] = array($tokens[$i + $j], $i + $j);
                                else
                                    $ff[] = array($tokens[$i + $j][1], $i + $j);
                            }
                            else
                            {
                                if (is_string($l[$j]))
                                    $ff[] = $tokens[$i + $j];
                                else
                                    $ff[] = $tokens[$i + $j][1];
                            }
                        }

                        $found[] = $ff;
                        $look_up_found = true;
                        $tmpstart = $i + $j + 1;
                    }
                }
            }
            if (count($found))
                $end = $tmpstart;
            return $found;
        }

        /**
         * @deprecated
         */
        public static function recDir($path, $searchforfilename = "", $searchforextension = "") {
            $out = array();
            if (!is_string($path))
                return false;
            if ($path[strlen($path) - 1] !== "/")
                $path .= "/";
            if (is_file($path))
            {
                return array($path);
            }
            elseif (is_dir($path))
            {
                $di = new \DirectoryIterator($path);
                foreach ($di as $fileinfo) {
                    if (!$fileinfo->isDot())
                    {
                        if ($fileinfo->isDir())
                        {
                            $out [$fileinfo->getFilename()] = self::recDir($path . $fileinfo->getFilename(), $searchforfilename, $searchforextension);
                        }
                        else
                        {
                            $out [$fileinfo->getFilename()] = FALSE;
                        }
                    }
                }
                return $out;
            }
            else
                return false;
        }

    }

}