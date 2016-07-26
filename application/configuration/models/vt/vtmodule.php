<?php

/**
 * @package VT
 * Wrapper per WS Vtiger
 */

namespace VT {
    require_once APP_PATH . '/application/configuration/models/vt/exceptions.php';

    use \WolfMVC\Model as ModelInt;

    /**
     * Classe base dei servizi di VT
     */
    class VtModule implements ModelInt {

        /**
         * Nome interno del modulo
         * @var string
         */
        protected static $_moduleName;

        /**
         * Chiave primaria del modulo
         * @var string
         */
        protected static $_primary_key;

        /**
         * Value map del modulo
         * @var array
         */
        protected static $_vm;

        /**
         * Relazioni --->1 con altri moduli
         * @var array
         */
        protected static $has_one;

        /**
         * Relazioni --->n con altri moduli
         * @var array
         */
        protected static $has_many;

        /**
         * Hash degli alias
         * @var array
         */
        protected static $alias_attribute;

        /**
         * Intero che identifica la stringa da anteporre negli id di questo modulo (della forma idPrefix x id)
         * @var int
         */
        protected static $_idPrefix;

        /**
         * Le condizioni usabili nel find
         * @var array
         */
        static $VALID_OPTIONS = array('conditions', 'limit', 'offset', 'order');

        public function getVm() {
            return static::$_vm;
        }

        public function get_primary_key() {
            return static::$_primary_key;
        }

        public function attributes($aliased) {
            
        }

        public function getFieldByName($fieldname) {
            
        }

        /**
         * Interrogazione WS
         * Accetta parametri variabili.
         * Ricerca per id
         *
         * <code>
         * # Ricerca dell'oggetto con id=5
         * Nomemodello::find(5);
         *
         * # Ricerca degli oggetti con id=5 o 27 o 32
         * Nomemodello::find(5,27,32);
         *
         * # La ricerca per id accetta un array options
         * Nomemodello::find(5,array('order' => 'name desc'));
         * </code>
         * Altrimenti:
         * options è un array:
         * La prima cella può contenere "all" oppure "first"
         */
        public static function find(/* $type, $options */) {
            $class = get_called_class();
            if (func_num_args() <= 0)
                throw new RecordNotFound("Couldn't find $class without an ID");
            $args = func_get_args();
            $options = static::extract_and_validate_options($args);
            $num_args = count($args);
            $single = true;
            echo "<pre>";
            print_r($args);
            echo "</pre>";
            echo "<pre>";
            print_r($options);
            echo "</pre>";

            if ($num_args > 0 && ($args[0] === 'all' || $args[0] === 'first'))
            {
                switch ($args[0]) {
                    case 'all':
                        $single = false;
                        break;
                    case 'first':
                        $options['limit'] = 1;
                        $options['offset'] = 0;
                        break;
                }
                $args = array_slice($args, 1);
                $num_args--;
            }
            elseif (count($args) === 1 && $num_args === 1) //ricerca per pk
            {
                $args = $args[0];
            }
            if ($num_args > 0 && !isset($options['conditions'])) //ricerca per pk
                return static::find_by_pk($args, $options);

            $options['mapped_names'] = static::$alias_attribute;
            echo "<pre>";
            print_r($args);
            echo "</pre>";
            echo $single ? "SINGLE" : "NO SINGLE";
            echo "<pre>";
            print_r($options);
            echo "</pre>";
//            $list = static::table()->find($options);
            $sql = static::options_to_sql($options);
            echo "<pre>";
            print_r($sql);
            echo "</pre>";
            $query = $sql->to_s();
            echo $sql;

            $countQM = substr_count($query, "?");
            echo $countQM;
            $parameters = $sql->get_where_values();
            if ($countQM < count($parameters))
            {
                throw new VTException("Invalid conditions");
            }
            for ($i = 0; $i < $countQM; $i++) {
                $pattern = "/(?:')(?:%)(\?)(?:%)(?:')/";
                $match = array();
                preg_match($pattern, $query, $match, PREG_OFFSET_CAPTURE);
                echo "<pre>";
                print_r($match);
                echo "</pre>";
            }
            echo "<pre>";
            print_r($query);
            echo "</pre>";
            return $single ? (!empty($list) ? $list[0] : null) : $list;
        }

        public static function options_to_sql($options) {
            $table = static::$_moduleName;
            $sql = new \ActiveRecord\SQLBuilder("dumb", $table);

            if (array_key_exists('select', $options))
                $sql->select($options['select']);

            if (array_key_exists('conditions', $options))
            {
                if (!self::is_hash($options['conditions']))
                {
                    if (is_string($options['conditions']))
                        $options['conditions'] = array($options['conditions']);

                    call_user_func_array(array($sql, 'where'), $options['conditions']);
                }
                else
                {
                    if (!empty($options['mapped_names']))
                        $options['conditions'] = $this->map_names($options['conditions'], $options['mapped_names']);

                    $sql->where($options['conditions']);
                }
            }

            if (array_key_exists('order', $options))
                $sql->order($options['order']);

            if (array_key_exists('limit', $options))
                $sql->limit($options['limit']);

            if (array_key_exists('offset', $options))
                $sql->offset($options['offset']);

            return $sql;
        }

        /**
         * Finder method which will find by a single or array of primary keys for this model.
         *
         * @param array $values An array containing values for the pk
         * @param array $options An options array
         * @return Model
         * @throws {@link RecordNotFound} if a record could not be found
         * @TODO includere $options
         */
        public static function find_by_pk($values, $options) {
            $options['conditions'] = static::pk_condition($values);
            echo "FIND BY PK<pre>";
            print_r($options);
            echo "</pre>";
            //a questo punto so che ho 2 possibilità per options - id singolo o id multiplo
            $vtws = \WolfMVC\Registry::get("VTWS");
            if (count($options["conditions"][static::$_primary_key]) === 1)
            {
                $list = array($vtws->doRetrieve($options["conditions"][static::$_primary_key]));
            }
            else
            {
                $sql = "SELECT * FROM Accounts WHERE " . static::$_primary_key . " IN (";
                $sql .= join(", ", array_map(function($x) {
                            return "'" . $x . "'";
                        }, $options["conditions"][static::$_primary_key]));
                $sql .= ");";
                echo $sql;
                $list = $vtws->doQuery($sql);
            }
            $results = count($list);
            echo "Risultato intermedio<pre>";
            print_r($list);
            echo "</pre>";
            if ($results != ($expected = count($values)))
            {
                $class = get_called_class();

                if ($expected == 1)
                {
                    if (!is_array($values))
                        $values = array($values);

                    throw new RecordNotFound("Couldn't find $class with ID=" . join(',', $values));
                }

                $values = join(',', $values);
                throw new RecordNotFound("Couldn't find all $class with IDs ($values) (found $results, but was looking for $expected)");
            }
            return $expected == 1 ? $list[0] : $list;
        }

        public static function extract_and_validate_options(array &$array) {
            $options = array();

            if ($array)
            {
                $last = &$array[count($array) - 1];

                try {
                    if (self::is_options_hash($last))
                    {
                        array_pop($array);
                        $options = $last;
                    }
                } catch (VTException $e) {
                    if (!self::is_hash($last))
                        throw $e;

                    $options = array('conditions' => $last);
                }
            }
            return $options;
        }

        public static function is_options_hash($array, $throw = true) {
            if (self::is_hash($array))
            {
                $keys = array_keys($array);
                $diff = array_diff($keys, self::$VALID_OPTIONS);

                if (!empty($diff) && $throw)
                    throw new VTException("Unknown key(s): " . join(', ', $diff));

                $intersect = array_intersect($keys, self::$VALID_OPTIONS);

                if (!empty($intersect))
                    return true;
            }
            return false;
        }

        /**
         * Somewhat naive way to determine if an array is a hash.
         */
        public static function is_hash(&$array) {
            if (!is_array($array))
                return false;

            $keys = array_keys($array);
            return @is_string($keys[0]) ? true : false;
        }

        public static function pk_condition($args) {

            if (static::$_idPrefix)
            {
                if (is_array($args))
                {
                    foreach ($args as $k => $v) {
                        $args[$k] = static::$_idPrefix . "x" . $v;
                    }
                }
                else
                {
                    $args = static::$_idPrefix . "x" . $args;
                }
            }
            return array(static::$_primary_key => $args);
        }

        protected static function build_select($conditions) {
            echo "<pre>";
            print_r($conditions);
            echo "</pre>";
            $sql = "SELECT $this->select FROM $this->table";

            if ($this->where)
                $sql .= " WHERE $this->where";

            if ($this->group)
                $sql .= " GROUP BY $this->group";

            if ($this->having)
                $sql .= " HAVING $this->having";

            if ($this->order)
                $sql .= " ORDER BY $this->order";

            if ($this->limit || $this->offset)
                $sql = $this->connection->limit($sql, $this->offset, $this->limit);

            return $sql;
        }

    }

}