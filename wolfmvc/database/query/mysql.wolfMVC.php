<?php

namespace WolfMVC\Database\Query {

    use WolfMVC\Database as Database;
use WolfMVC\Database\Exception as Exception;

    class Mysql extends Database\Query {

        public function all() {
            if (empty($this->_rawsql) || !(is_string($this->_rawsql))) {
                $sql = $this->_buildSelect();
            }
            else{
                $sql = $this->_rawsql;
            }
            $result = $this->connector->execute($sql);
            if ($result === false) {
                $error = $this->connector->lastError;
                throw new Exception\Sql("There was an error with your SQL query: {$error}");
            }

            $rows = array();

            for ($i = 0; $i < $result->num_rows; $i++) {
                $rows[] = $result->fetch_array(MYSQLI_ASSOC);
            }
            return $rows;
        }

    }

}