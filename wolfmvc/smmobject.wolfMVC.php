<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {

    /**
     * Semantic Multi Model
     */
    class Smm extends Base {

        /**
         * @readwrite
         */
        protected $_mmodname;

        /**
         * @readwrite
         */
        protected $_mname;

        /**
         * @readwrite
         */
        protected $_DBTables = array();

        /**
         * @readwrite
         */
        protected $_MODTables = array();

        /**
         * @readwrite
         */
        protected $_MODElements = array();

        /**
         * @readwrite
         */
        protected $_MODRelations = array();

        /**
         * @readwrite
         */
        protected $_maintable = array();

        /**
         * @readwrite
         */
        protected $_model = array();
        protected $_tmpmodel = array();
        protected $_selectStructure;
        protected $_hash = array();

        /**
         *
         * @readwrite
         */
        protected $_savedQuery = array();
        protected $_chunks = array();

        /**
         *
         * @readwrite
         */
        protected $_structure;

        private function hashing($len) {
            $letters = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

            while (true) {
                $string = "";
                for ($i = 0; $i < $len; $i++) {
                    $string .= $letters[rand(0, 25)];
                }
                if (!isset($this->_hash->{$string}))
                {
                    $this->_hash->{$string} = $string;
                    return $string;
                }
            }
        }

        public function resolveCalcField($calcField, $inserting_fields, $inserted_fields) {
            foreach ($calcField->fields as $i => $f) {
                echo "<br>Comp " . $f[0];
                if (!isset($inserted_fields[$i][0]))
                {
                    echo " non ancora inserito";
                    return -1;
                }
//                $t = $calcField->tables[$i];
            }
        }

        public function findTable($target_table_alias) {
            $struct = $this->_structure;
            $maintablealias = $struct->getMaintable();
            $relations = $struct->getRelations();
            $to = $target_table_alias;
            $from = $target_table_alias;
            $path = array($to);
            $i = 0;
            while ($from !== $maintablealias) {
                if (!isset($relations["byEnd"][$to]) || empty($relations["byEnd"][$to]))
                {
                    throw new \Exception("Some error occurred!", 0, NULL);
                }
                $from = $relations["byEnd"][$to][0]->from;
                array_unshift($path, $from);
                $to = $from;
            }
            echo join("->", $path);
        }

        public function extractSubQuery($starting_table_alias) {
            $substruct = new Smm\Smmstructure();
            $subqueryalias = Registry::hashing(8);
            $struct = $this->_structure;
            $substruct->setDefaultDb($struct->getDefaultDb());
            $all_target_tables_alias = $this->getChildrenTables($starting_table_alias);
            $all_tables = $struct->getTables();
            $all_entities = $struct->getEntities();
            $all_relations = $struct->getRelations();
            $all_fields = $struct->getFields();
            $all_calc_fields = $struct->getCalcFields();
            $parent_relation = $all_relations["byEnd"][$starting_table_alias][0];
            $parent_table_alias = $parent_relation->from;
            $parent_link_field = $parent_relation->fieldFrom;
            $link_field = $parent_relation->fieldTo;
            $tmpaliases = array();

            foreach ($all_target_tables_alias as $tabaliask => $tabalias) {
                $substruct->addTable($all_tables[$tabalias]->tableName, $tabalias);
                if (isset($all_tables[$tabalias]->filters) && (is_array($all_tables[$tabalias]->filters)))
                {
                    foreach ($all_tables[$tabalias]->filters as $filterk => $filter) {
                        $substruct->addFilterToTable($tabalias, $filter);
                    }
                }
                $substruct->setGroupingTable($tabalias, $link_field);
                if ($all_tables[$tabalias]->belongsToEntity && $all_tables[$tabalias]->belongsToEntity !== "")
                {
                    $struct->removeEntity($all_tables[$tabalias]->belongsToEntity);
                }
                foreach ($all_fields as $fieldk => $field) {
                    if ($field->table !== $tabalias)
                        continue;
                    $tmpaliases[$fieldk] = Registry::hashing(5);
                    $substruct->addField($field->fieldName, $tabalias, $tmpaliases[$fieldk]);
                    $fieldtable = $field->table;
                    if ($all_tables[$fieldtable]->hasKey)
                    {
                        $substruct->setGroupingField($tmpaliases[$fieldk], $all_tables[$fieldtable]->key);
                    }
                    $struct->changeFieldForSubquery($fieldk, $subqueryalias, $tmpaliases[$fieldk]);
                    unset($all_fields[$fieldk]);
                }

                foreach ($all_calc_fields as $calcfieldk => $calcfield) {
                    //se tutte le tabelle da cui il campo pesca sono nella sottostruttura allora sposto il campo
                    $checktables = true;
                    foreach ($calcfield->tables as $tab) {
                        if (!in_array($tab, $all_target_tables_alias))
                        {
                            $checktables = false;
                            break;
                        }
                    }
                    if ($checktables)
                    {
                        $fields = array();
                        $checkfields = true;
                        foreach ($calcfield->fields as $f) {
                            array_push($fields, $tmpaliases[$f[0]]);
                            if ($f[1] === "smooth" && !$substruct->fieldExists($tmpaliases[$f[0]]))
                            {
                                $checkfields = false;
                                break;
                            }
                        }
                        if ($checkfields)
                        {
                            $tmpaliases[$calcfieldk] = Registry::hashing(5);
                            $substruct->addCalculatedField($calcfield->expression, $fields, $tmpaliases[$calcfieldk], $calcfield->overwriteComps);
                            $substruct->setGroupingField($tmpaliases[$calcfieldk], $link_field);
                            $struct->changeCalcFieldToFieldForSubquery($calcfieldk, $subqueryalias, $tmpaliases[$calcfieldk]);
                            unset($all_calc_fields[$calcfieldk]);
                        }
                    }
                }

                $struct->removeTable($tabalias);
            }
            //aggiungo campo per join:
            $struct->addField("Pk_" . $starting_table_alias, $subqueryalias, "Pk_" . $starting_table_alias, true, $subqueryalias);
            $substruct->addField($link_field, $starting_table_alias, "joinfield");
            foreach ($all_relations["byEnd"] as $relk => $r) { //prendo tutte le relazioni elencate per tabella B
                foreach ($r as $k => $rel) {
                    if (in_array($rel->from, $all_target_tables_alias) && in_array($rel->to, $all_target_tables_alias)) //se sono relazioni interne alla sottostruttura
                    {
                        $substruct->setRelation($rel->from, $rel->to, $rel->mult, $rel->fieldFrom, $rel->fieldTo); //le copio nella sottostruttura
                    }
                }
            }
            $rel = $all_relations["byEnd"][$starting_table_alias];
            foreach ($all_target_tables_alias as $tab) {
                $struct->deleteRelations($tab); //cancello tutte le relazioni delle tabelle spostate nella sottostruttura
            }


            $smm = new Smm();
            $smm->setStructure($substruct);
            $struct->addTable("(" . $smm->selectAll(false, true) . ")", $subqueryalias, true, true, "joinfield");
            $struct->setRelation($parent_table_alias, $subqueryalias, "!", $parent_link_field, "joinfield", true);

            return;
        }

        public function getChildrenTables($tablealias) {
            $struct = $this->_structure;
            $relations = $struct->getRelations();
            $relations = $relations["byStart"];
            $children = array();
            if (isset($relations[$tablealias]) && is_array($relations[$tablealias]) && !empty($relations[$tablealias]))
            {
                foreach ($relations[$tablealias] as $relk => $rel) {
                    $ch = $this->getChildrenTables($rel->to);
                    foreach ($ch as $key => $value)
                        array_push($children, $value);
                }
            }
            else
            {
                return array($tablealias);
            }
            array_unshift($children, $tablealias);
            return $children;
        }

        public function searchFirstOnetoNChild($current_table_alias) {
            $struct = $this->_structure;
            $relations = $struct->getRelations();
            $relations = $relations["byStart"];
            if (!isset($relations[$current_table_alias]))
            {
//                echo "<br>" . $current_table_alias . " -- Foglia<br>";
                return;
            }
            else
            {
                $relations = $relations[$current_table_alias];
            }
            foreach ($relations as $relk => $rel) {
                if ($rel->mult === "+")
                {
//                    echo "<br>" . $current_table_alias . " -- 1:n :: " . $rel->to . "<br>";
                    $this->extractSubQuery($rel->to);
                }
                else
                {
//                    echo "<br>" . $current_table_alias . " -- Nodo, itero...:: " . $rel->to . "<br>";
                    $this->searchFirstOnetoNChild($rel->to);
                }
            }
        }

        
        
        public function selectAll($flag = true, $verbose = false) {
            ob_start();
            //da dove comincio? dall'inizio! ovvero dalla prima tabella elencata
            $struct = $this->_structure;

            $joined_tables = array();
            $subqueried_tables = array();
            $tobejoined_tables = array();
            $inserted_fields = array();
            $inserting_fields = array();

            $maintablealias = $struct->getMaintable();
            //sostituisco sottostrutture
            //cerco in ogni percorso il primo figlio con molteplicità + e chiamo la sostituzione.

            $this->searchFirstOnetoNChild($maintablealias);
            $all_fields = $struct->getFields();
            $all_tables = $struct->getTables();
            $all_relations = $struct->getRelations();
            echo '<pre>';
            print_r($struct);
            echo '</pre>';
            $tobejoined_tables = $struct->getTables();
            $current_table_alias = $struct->getMaintable();
            $current_table = $tobejoined_tables[$current_table_alias];
            unset($tobejoined_tables[$current_table_alias]);
            $joined_tables[$current_table_alias] = $current_table;
            $inserting_fields = array_merge($struct->getFields(), $struct->getCalcFields());
            $inserted_fields["Pk_" . $maintablealias] = new \stdClass();
            $inserted_fields["Pk_" . $maintablealias]->fieldName = $current_table_alias . "." . $current_table->key;
            $inserted_fields["Pk_" . $maintablealias]->fieldAlias = "Pk_" . $maintablealias;
            if (isset($current_table->grouping))
            {
                $inserted_fields["Pk_" . $maintablealias]->grouping = true;
            }
            $depth = 0;
            $actual_row_multiplicity = false;
            $actual_mult = "!";
//            print_r($inserting_fields);
            //inserisco tutti i campi di questa tabella
            $jjjj = 0;
            $include_a_table = false;
            $count = count($inserting_fields);
            echo "<br><BR>INIZIO";
            echo "<br><br>checkcount: " . ($count - count($inserting_fields));
            while (count($inserting_fields) > 0 || $include_a_table) {
                $count = count($inserting_fields);

                echo "<br>current_table_alias " . $current_table_alias;
                echo "<br>numero campi da inserire " . count($inserting_fields);
                echo "<br>campi da inserire <pre>";
                print_r($inserting_fields);
                echo "</pre>";
                echo "<br>tabelle inserite";
                print_r($joined_tables);
                echo "<br>include_a_table " . $include_a_table;

                if ($include_a_table)
                {
                    echo "<br>inclusione tabella";
                    echo '<br>prima provo a completare le entità';
                    if ($current_table->belongsToEntity !== FALSE)
                    {
                        $previous_table_alias = $current_table_alias;
                        $entity = $struct->getEntities();
                        $entity = $entity[$current_table->belongsToEntity];
                        for ($i = 0; $i < (int) $entity->numberOfTables; $i++) {
                            if (!isset($joined_tables[$entity->{"table_" . $i}]))
                            {
                                $current_table_alias = $entity->{"table_" . $i};
                                $current_table = $tobejoined_tables[$entity->{"table_" . $i}];
                                unset($tobejoined_tables[$entity->{"table_" . $i}]);
                                $joined_tables[$current_table_alias] = $current_table;
                                $rels = $struct->getRelations();
                                $relations = $rels["byEnd"];
                                if (!isset($relations[$current_table_alias]))
                                {
                                    throw new \Exception("Missing relation in entity", 0, NULL);
                                }
                                $rel = $relations[$current_table_alias][0];
                                $joined_tables[$current_table_alias]->from = $rel->from . "." . $rel->fieldFrom;
                                $joined_tables[$current_table_alias]->to = $current_table_alias . "." . $rel->fieldTo;
//                                echo "<br>tabella " . $current_table_alias . " inserita";
                                $include_a_table = false;
                                echo "<br>inclusa tabella " . $current_table_alias;
                                if ($current_table->hasKey)
                                {
                                    $inserted_fields["Pk_" . $current_table_alias] = new \stdClass();
                                    $inserted_fields["Pk_" . $current_table_alias]->fieldName = $current_table_alias . "." . $current_table->key;
                                    $inserted_fields["Pk_" . $current_table_alias]->fieldAlias = "Pk_" . $current_table_alias;
                                }
                                $actual_mult = "!";
                                break;
                            }
                        }
                    }


                    $rels = $struct->getRelations();
                    $relations = $rels["byStart"];
                    while ($include_a_table) {
                        echo "<br>altrimenti cerco tra le relazioni";
                        if (isset($relations[$current_table_alias]))
                        {
                            foreach ($relations[$current_table_alias] as $relk => $rel) { // cerco una nuova tabella tra le relazioni
                                if (isset($tobejoined_tables[$rel->to]))
                                {
                                    echo "<br><br><br><br><br><br><br><br><br><br>" . $rel->to . "<br><br><br><br><br><br><br><br><br>";

                                    $current_table_alias = $rel->to;
                                    $current_table = $tobejoined_tables[$rel->to];
                                    unset($tobejoined_tables[$rel->to]);
                                    $joined_tables[$current_table_alias] = $current_table;
                                    $include_a_table = false;
                                    echo "<br>inclusa tabella " . $current_table_alias;
                                    $actual_mult = $rel->mult;
                                    $joined_tables[$current_table_alias]->from = $rel->from . "." . $rel->fieldFrom;
                                    $joined_tables[$current_table_alias]->to = $current_table_alias . "." . $rel->fieldTo;
                                    if ($current_table->hasKey)
                                    {
                                        $inserted_fields["Pk_" . $current_table_alias] = new \stdClass();
                                        $inserted_fields["Pk_" . $current_table_alias]->fieldName = $current_table_alias . "." . $current_table->key;
                                        $inserted_fields["Pk_" . $current_table_alias]->fieldAlias = "Pk_" . $current_table_alias;
                                    }
                                    break;
                                }
                            }
                            if ($current_table_alias === $maintablealias)
                            {
                                $include_a_table = false;
                                break;
                            }
                        }
                        if ($include_a_table)
                        { // se non ha relazioni provo a risalire
                            $fromalias = $rels["byEnd"][$current_table_alias][0]->from;
                            $current_table_alias = $fromalias;
                            $current_table = $joined_tables[$fromalias];
                            continue;
                        }
                    }
                }
                echo "<br><br>asdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdasdassdasdasdasd   " . $jjjj . "<br>Campi da inserire:<pre>";
                print_r($inserting_fields);
                echo "</pre><br> campi inseriti: <pre>";
                print_r($inserted_fields);
                echo "</pre>";
                foreach ($inserting_fields as $fieldalias => $fielddetails) {
                    echo "<br><br><br>Provo a inserire: " . $fieldalias . "<br>";
                    //elimino dal conto dei campi da inserire i campi non select
                    if (!$fielddetails->select)
                    {
                        echo "<br> questo campo non &egrave; select--- lo elimino dal conto";
                        unset($inserting_fields[$fieldalias]);
                        $count--;
                        echo "<br><br>checkcount: " . ($count - count($inserting_fields));
                    }
                    if ($fielddetails->isCalc && $fielddetails->select)
                    { // posso inserire questo campo calcolato solo se le tabelle da cui pesca sono già state tutte incluse
                        echo "Campo calcolato<br>";
                        print_r($fielddetails);
                        echo "<br>";
//                    $field = $fielddetails->expression;
                        $comp_tables_havebeen_joined = true;
                        foreach ($fielddetails->tables as $s => $t) { //mi basta controllare che le tabelle dei campi che sono direttamente componenti di questo campo calcolato siano state inserite
                            if (!isset($joined_tables[$t]))
                            {
                                $comp_tables_havebeen_joined = false;
                            }
                        }
                        echo "<br>comp_tables_havebeen_joined " . $comp_tables_havebeen_joined;
                        $comp_fields_havebeen_inserted = true;
                        foreach ($fielddetails->fields as $s => $f) {//mi basta controllare che i campi che sono direttamente componenti di questo campo calcolato siano stati inseriti
                            if (!isset($inserted_fields[$f[0]]))
                            {
                                $comp_fields_havebeen_inserted = false;
                            }
                        }
                        $comp_fields_havebeen_inserted = true;
                        if ($comp_tables_havebeen_joined && $comp_fields_havebeen_inserted)
                        {
                            $field = $fielddetails->expression;
                            foreach ($fielddetails->fields as $index => $compfieldalias) {
                                print_r($compfieldalias);
                                $field = str_replace("##" . ($index + 1) . "##", $all_fields[$compfieldalias[0]]->table . "." . $all_fields[$compfieldalias[0]]->fieldName, $field);
                            }
                            $inserted_fields[$fieldalias] = new \stdClass();
                            $inserted_fields[$fieldalias]->fieldName = $field;
                            $inserted_fields[$fieldalias]->fieldAlias = $fieldalias;
                            if (isset($fielddetails->filters) && is_array($fielddetails->filters) && !empty($fielddetails->filters))
                            {
                                $inserted_fields[$fieldalias]->filters = $fielddetails->filters;
                            }
                            if (isset($fielddetails->grouping) && ($fielddetails->grouping !== ""))
                            {
                                $inserted_fields[$fieldalias]->grouping = $fielddetails->grouping;
                            }
                            unset($inserting_fields[$fieldalias]);
                            echo "<br>campo " . $fieldalias . " inserito";
                        }
                    }
                    else if ($fielddetails->select)
                    {

                        echo "Campo semplice<br>";
                        print_r($fielddetails);
                        echo "<br>controllo se la sua tabella &egrave; gi&agrave; stata inserita";

                        $table_havebeen_joined = isset($joined_tables[$fielddetails->table]);

                        if ($table_havebeen_joined)
                        {
                            echo "<br>la tabella &egrave; gi&agrave; stata inserita";
                            $inserted_fields[$fieldalias] = new \stdClass();
                            $inserted_fields[$fieldalias]->fieldName = $fielddetails->table . "." . $fielddetails->fieldName;
                            $inserted_fields[$fieldalias]->fieldAlias = $fieldalias;
                            if (isset($fielddetails->filters) && is_array($fielddetails->filters) && !empty($fielddetails->filters))
                            {
                                $inserted_fields[$fieldalias]->filters = $fielddetails->filters;
                            }
                            if (isset($fielddetails->grouping) && ($fielddetails->grouping !== ""))
                            {
                                $inserted_fields[$fieldalias]->grouping = $fielddetails->table . "." . $fielddetails->grouping;
                            }
                            unset($inserting_fields[$fieldalias]);
                            echo "<br>campo " . $fieldalias . " inserito";
                        }
                        else
                        {
                            echo "<br>la tabella non &egrave; gi&agrave; stata inserita";
                        }
                    }
                }
                echo "<br><br>checkcount: " . ($count - count($inserting_fields));
                if ($count - count($inserting_fields) === 0)
                {
                    $include_a_table = true;
                }
            }
            echo "<br>tabelle inserite: " . count($joined_tables);
            echo "<br>tabelle iniziali: " . count($all_tables);
            if (count($all_tables) !== count($joined_tables))
            {
                echo "<br>INCLUDO TABELLE MANCANTI";
                echo "<br><br><br><br> stato dell'arte";
                echo "<br><br>tobejoined_tables: ";
                print_r($tobejoined_tables);
                echo "<br><br>joined_tables: ";
                print_r($joined_tables);
                echo "<br><br>current table: ";
                print_r($current_table_alias);
                echo "<br><br><br><br>";
                print_r($joined_tables);
                echo "<br> qualche tabella non &egrave; stata inserita";
                $rel = $all_relations["byEnd"];
                foreach ($all_tables as $tablek => $table) {
                    if (!isset($joined_tables[$tablek]))
                    {
                        $r = $rel[$tablek][0];
                        unset($tobejoined_tables[$tablek]);
                        $joined_tables[$tablek] = $table;
                        $joined_tables[$tablek]->from = $r->from . "." . $r->fieldFrom;
                        $joined_tables[$tablek]->to = $tablek . "." . $r->fieldTo;
                        echo "<br> " . $tablek . " inserita";
                    }
                }
            }
            echo "<br><br>FINITO--- ultimo controllo:";
            echo "<br>tabelle inserite: " . count($joined_tables);
            echo "<br>tabelle iniziali: " . count($all_tables);
            $FIELDS = array();
            $TABLES = "";
            $CONDS = array();
            $GROUP = array();
            foreach ($inserted_fields as $fieldk => $field) {
                if (isset($field->grouping) && ($field->grouping === TRUE))
                {
                    $FIELDS [$fieldk] = "CONCAT('{',GROUP_CONCAT(" . $field->fieldName . "),'}') AS '" . $field->fieldAlias . "'";
                }
                else if (isset($field->grouping) && is_string($field->grouping))
                {
                    $FIELDS [$fieldk] = "CONCAT('{',GROUP_CONCAT(CONCAT(" . $field->grouping . ", ': ', " . $field->fieldName . ")),'}') AS '" . $field->fieldAlias . "'";
                }
                else
                {
                    $FIELDS [$fieldk] = $field->fieldName . " AS '" . $field->fieldAlias . "'";
                }

                if (isset($field->filters) && is_array($field->filters))
                {
                    $cond = "";
                    foreach ($field->filters as $filterk => $filter) {
                        $cond .= str_replace("##FIELD##", $field->fieldName, $filter);
                    }
                    array_push($CONDS, "( " . $cond . " )");
                }
            }
            foreach ($joined_tables as $tablek => $table) {
                if (isset($table->filters) && is_array($table->filters))
                {
                    $cond = "";
                    foreach ($table->filters as $filterk => $filter) {
                        $matches = array();
                        $volte = preg_match_all("/##([^#])*##/i", $filter, $matches);
                        if ($volte > 0)
                        {
                            foreach ($matches[0] as $k => $m) {
                                $filter = str_replace($m, $tablek . "." . str_replace("##", "", $m), $filter);
                            }
                        }
                        $cond .= $filter;
                    }
                    array_push($CONDS, "( " . $cond . " )");
                }
            }
            foreach ($joined_tables as $tablek => $table) {
                if ($tablek === $maintablealias)
                {
                    $TABLES[$tablek] = $table->tableName . " " . $table->alias;
                    if (isset($table->grouping) && ($table->grouping) && ($table->grouping !== ""))
                    {
                        array_push($GROUP, $table->grouping);
                    }
                }
                else
                    $TABLES[$tablek] = "LEFT JOIN " . $table->tableName . " " . $table->alias . " ON (" . $table->from . " = " . $table->to . ")";
            }

            echo "<br>filtri:<br>";
            print_r($CONDS);
            echo "<br>";
            echo "<br><br>";
            echo "tabelle non inserite:";
            print_r($tobejoined_tables);
            echo "<br><br>";
            $sql = "SELECT " . join(", ", $FIELDS) . " FROM " . join(" ", $TABLES);
            echo "<br>filtri:<br>";
            print_r($CONDS);
            echo "<br>";
            $COND = join(" AND ", $CONDS);
            echo "<br> " . $COND . "<br><br>";
            if ($COND !== "")
            {
                $sql .=" WHERE " . $COND;
            }
            if (!empty($GROUP))
            {
                $sql .=" GROUP BY " . join(", ", $GROUP);
            }
            echo $sql;
            echo "<br><br>count: " . $count;
            echo "<br>including_fields :" . count($inserting_fields);
            echo '<pre>';
            print_r($inserting_fields);
            echo '</pre>';

            echo "<br>included_fields :" . count($inserted_fields);
            echo '<pre>';
            print_r($inserted_fields);
            echo '</pre>';

            echo "<br>joined_tables :" . count($joined_tables);
            echo '<pre>';
            print_r($joined_tables);
            echo '</pre>';
            if ($verbose)
            {
                ob_flush();
            }
            ob_end_clean();
            if ($flag)
            {
                $database = Registry::get("database_" . $struct->getDefaultDb());
//            print_r($database);
                $labels = array();
                $link = new \mysqli($database->getHost(), $database->getUsername(), $database->getPassword(), $database->getSchema());
                $result = $link->query($sql);
                $fieldsext = mysqli_fetch_fields($result);
                foreach ($fieldsext as $key => $field) {
                    array_push($labels, $field->name);
                }
                return array("data" => $result->fetch_all(MYSQLI_ASSOC), "fields" => $labels);
            }
            else
            {
                return $sql;
            }
        }

        function updateField($fieldAlias, $keyValue, $newFieldValue) {
            $struct = $this->_structure;
            $all_fields = $struct->getFields();
            $all_tables = $struct->getTables();
            if (isset($all_fields[$fieldAlias]))
            {
                $field = $all_fields[$fieldAlias];
                $fieldName = $field->fieldName;
                if (isset($field->editable) && $field->editable)
                {
                    $table = $field->table;
                    $table = $all_tables[$table];
                    $tableName = $table->tableName;
                    if (isset($table->hasKey) && $table->hasKey && isset($table->key) && is_string($table->key) && !(empty($table->key)))
                    {
                        $sql = "UPDATE " . $tableName . " SET " . $fieldName . " = '" . $newFieldValue . "' WHERE " . $table->key . " = '" . $keyValue . "'";
                    }
                    else
                    {
                        throw new \Exception("Update error: " . $fieldAlias . " is in a table without key field.");
                    }
                }
                else
                {
                    throw new \Exception("Update error: Field " . $fieldAlias . " is not editable.");
                }
            }
            else
            {
                throw new \Exception("Update error: unknown field " . $fieldAlias);
            }
            return $sql;
        }

        function updateFields($fieldsAlias, $keyValue, $newFieldValues) {
            $struct = $this->_structure;
            $all_fields = $struct->getFields();
            $all_tables = $struct->getTables();
            //controlli: tutti i campi esistono e sono editabili, tutti i campi appartengono alla stessa tabella
            if (!is_array($fieldsAlias))
            {
                throw new \Exception("Update error: FieldsAlias must be an array.");
            }
            if (!is_array($newFieldValues))
            {
                throw new \Exception("Update error: newFieldValues must be an array.");
            }
            if (count($newFieldValues) !== count($fieldsAlias))
            {
                throw new \Exception("Update error: newFieldValues and fieldsAlias must have same lenght.");
            }
            $table = "";
            $fieldNames = array();
            foreach ($fieldsAlias as $fieldAliask => $fieldAlias) {
                if (!(isset($all_fields[$fieldAlias])) || !isset($all_fields[$fieldAlias]->editable) || !($all_fields[$fieldAlias]->editable))
                {
                    throw new \Exception("Update error: Field " . $fieldAlias . " in not valid or is not editable.");
                }
                if ($table === "")
                {
                    $table = $all_fields[$fieldAlias]->table;
                }
                else if ($table !== $all_fields[$fieldAlias]->table)
                {
                    throw new \Exception("Update error: All fields must belong to same table.");
                }
                $fieldNames[$fieldAliask] = $all_fields[$fieldAlias]->fieldName;
            }





            $table = $all_tables[$table];
            $tableName = $table->tableName;
            if (isset($table->hasKey) && $table->hasKey && isset($table->key) && is_string($table->key) && !(empty($table->key)))
            {
                $update = array();
                $sql = "UPDATE " . $tableName . " SET ";
                foreach ($fieldNames as $fieldk => $fieldName) {
                    array_push($update, $fieldName . " = '" . $newFieldValues[$fieldk] . "'");
                }
                $sql .= join(", ", $update) . " WHERE " . $table->key . " = '" . $keyValue . "'";
                ;
            }
            else
            {
                throw new \Exception("Update error: " . $tableName . " is a table without key field.");
            }

            return $sql;
        }

        public function getPicklistValues($fieldAlias) {
            $struct = $this->_structure;
            $all_fields = $struct->getFields();
            $all_tables = $struct->getTables();
            if (isset($all_fields[$fieldAlias]) && isset($all_fields[$fieldAlias]->picklist))
            {
                $type = $all_fields[$fieldAlias]->picklist->type;
                switch ($type) {
                    case 'static':
                        return json_encode($all_fields[$fieldAlias]->picklist->values);
                        break;
                    case 'dbdriven':
                        $picklistsmm = new Smm();
                        $picklistsmm->setStructure($all_fields[$fieldAlias]->picklist->structure);
                        $values = $picklistsmm->selectAll(true);
                        $linkFieldName = $all_fields[$fieldAlias]->picklist->linkFieldName;
                        $dataFieldName = $all_fields[$fieldAlias]->picklist->dataFieldName;
                        $out = array();
                        foreach ($values["data"] as $key => $val){
                            $out[$val[$linkFieldName]] = $val[$dataFieldName];
                        }
                        return json_encode($out);
                        break;
                }
            }
            else
            {
                throw new \Exception("Update error: Field " . $fieldAlias . " in not valid or there are no picklists associated with it.");
            }
        }

    }

}

