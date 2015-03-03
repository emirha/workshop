<?php

class Db {

    private $pdoWhere = null, $pdoData = null,$pdoDataNoEscape = null,
        $pdoSet = null;

    public $pdo = null;

    var $where;
    var $from;
    var $select = '*';
    var $inner_join;
    var $query = '';
    var $debug = FALSE;
    var $set = '';
    var $orderby;
    var $groupby;
    var $limit = null;
    var $lastQuery = '';
    var $updatedRows = null;
    private $ignore = '';

    public static $ESCAPE_NORMAL = 1;
    public static $NO_ESCAPE = 2;
    public static $ESCAPE_FCK = 3;


    function __construct() {
        if (!is_object($this->pdo)) {
            $this->pdo = new PDO('mysql:host=localhost;dbname='.DB_DATABASE, DB_LOGIN, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8 COLLATE utf8_general_ci"));
        }

        $this->_clear();
    }

    function connect($db_login, $db_pass, $database) {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname='.$database, $db_login, $db_pass);
        } catch (PDOException $e) {
            return false;
        }

        return false;
    }

    function where($where) {
        $this->where[] = $where;
        return $this;
    }

    function pdoWhere(array $where) {
        foreach ($where as $k => $v)
            $this->pdoWhere[$k] = $v;
        return $this;
    }

    function orderby($order) {
        $this->orderby[] = $order;
        return $this;
    }

    function groupby($groupby) {
        $this->groupby = $groupby;

        return $this;
    }

    function limit($limit) {
        $this->limit = $limit;

        return $this;
    }

    function row($clasName = null) {
        $select = $this->_sql_select($clasName);
        if ($this->debug) return $select;
        return isset($select[0]) ? $select[0] : NULL;
    }

    function result($clasName = null) {
        return $this->_sql_select($clasName);
    }


    function delete() {
        return $this->_sql_delete();
    }

    function debug() {
        $this->debug = TRUE;
        return $this;
    }

    function inner_join($join) {
        $this->inner_join[] = $join;
        return $this;
    }

    function join($table, $on, $type = 'INNER JOIN') {
        $this->inner_join[] = $type.' '.$table.' ON '.$on;
        return $this;
    }

    function set($data, $escape = false) {
        switch ($escape) {
            case self::$NO_ESCAPE:
                foreach ($data as $k => $v) {
                    $this->pdoDataNoEscape[$k] = $v;
                }
                break;
            default:
                foreach ($data as $k => $v) {
                    $this->pdoData[$k] = $v;
                    $this->pdoSet[] = $k.' = :'.$k;
                }
        }

        switch ($escape) {
            case self::$ESCAPE_NORMAL:
                $key = '$key = \"".mysql_real_escape_string(stripslashes($value))."\"';
                break;
            case self::$NO_ESCAPE:
                $key = '$key = ".($value)."';
                break;
            case self::$ESCAPE_FCK:
            default:
                $key = '$key = \"".$value."\"';
                break;
        }

        $new_array = array_map(
            create_function('$key, $value', 'return "'.$key.'";'),
            array_keys($data),
            array_values($data)
        );

        if ($this->set == '') {
            $this->set = implode(', ',$new_array);
        } else {
            $this->set .= ', '.implode(', ',$new_array);
        }
        return $this;
    }

    function select($select) {
        $this->select = $select;
        return $this;
    }


    function into($table) {
        return $this->from($table);
    }

    function from($table) {
        $this->from = $table;
        return $this;
    }

    function ignore() {
        $this->ignore = ' IGNORE ';
        return $this;
    }

    function insert() {
        $queryString = 'INSERT '.$this->ignore.' INTO '.$this->from.'
                                        ('.implode(',',array_keys($this->pdoData)).($this->pdoDataNoEscape ? ','.implode(',',array_keys($this->pdoDataNoEscape)) : '').')
                                        VALUES (:'.implode(', :',array_keys($this->pdoData)).($this->pdoDataNoEscape ? ','.implode(',',array_values($this->pdoDataNoEscape)) : '').')';
        $query = $this->pdo->prepare($queryString);
        foreach ($this->pdoData as $k => $v) {
            $query->bindParam(':'.$k,$this->pdoData[$k]);
        }

        $this->lastQuery = $queryString;

        if ($query->execute()) {
            $this->_clear();
            return $this->pdo->lastInsertId();
        } else {
            $msg = var_export($query->errorInfo(),1)."\n";
            die($msg);
        }
    }

    function update($table = '') {
        $this->updatedRows = null;
        if ($table)
            $this->from($table);

        if (empty($this->set) && empty($this->pdoData))  {
            $this->_clear();
            return false;
        }
        if (empty($this->where) && empty($this->pdoWhere)) {
            $this->_clear();
            return false;
        }

        foreach ($this->pdoData as $k => $v) {
            $updateFields[] = $k.' = :'.$k;
        }
        foreach ($this->pdoDataNoEscape as $k => $v) {
            $updateFields[] = $k.' = '.$v;
        }

        if ($this->pdoWhere) {
            foreach ($this->pdoWhere as $k => $v) {
                $this->where[] = $k.' = :_'.$k;
            }
        }

        $queryString = 'UPDATE '.$this->from.' SET '.implode(',',$updateFields).' WHERE '.implode(' AND ',$this->where);
        $query = $this->pdo->prepare($queryString);
        foreach ($this->pdoData as $k => $v) {
            $query->bindParam(':'.$k,$this->pdoData[$k]);
        }

        if ($this->pdoWhere) {
            foreach ($this->pdoWhere as $k => $v ) {
                $query->bindParam(':_'.$k,$this->pdoWhere[$k]);
            }
        }

        $updateSucc = $query->execute();
        $this->updatedRows = $query->rowCount();
        $this->lastQuery = $queryString;
        $this->_clear();

        if ($updateSucc) {
            return true;
        }
    }

    function _sql_select($fetchClass = false, $debug = FALSE) {
        $queryString = 'SELECT '.$this->select.' FROM '.$this->from.' ';

        if ($this->inner_join)
            $queryString .= ' '.implode(' ',$this->inner_join).' ';

        if ($this->pdoWhere) {
            foreach ($this->pdoWhere as $k => $v) {
                $this->where[] = $k.' = :'.$k;
            }
        }

        if ($this->where)
            $queryString .= ' WHERE '.implode(' AND ',$this->where);

        if ($this->groupby)
            $queryString .= ' GROUP BY '.$this->groupby;

        if ($this->orderby)
            $queryString .= ' ORDER BY '.implode(' , ',$this->orderby);

        if ($this->limit != null)
            $queryString .= ' LIMIT '.$this->limit;

        $query = $this->pdo->prepare($queryString);
        $this->lastQuery = $queryString;

        if (count($this->pdoWhere)) {
            foreach ($this->pdoWhere as $k => $v ) {
                $query->bindParam(':'.$k,$this->pdoWhere[$k]);
            }
        }

        $execute = $query->execute();
        $this->_clear();

        if ($execute) {
            if ($fetchClass)
                $result = $query->fetchAll(PDO::FETCH_CLASS,$fetchClass);
            else
                $result = $query->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        }
    }

    function _sql_delete($debug = FALSE) {
        if (empty($this->where) && empty($this->pdoWhere)) return false;

        if ($this->pdoWhere) {
            foreach ($this->pdoWhere as $k => $v) {
                $this->where[] = $k.' = :'.$k;
            }
        }

        $queryString = 'DELETE FROM '.$this->from.' WHERE '.implode(' AND ',$this->where);
        $query = $this->pdo->prepare($queryString);

        if (count($this->pdoWhere)) {
            foreach ($this->pdoWhere as $k => $v ) {
                $query->bindParam(':'.$k,$this->pdoWhere[$k]);
            }
        }

        $execute = $query->execute();
        $this->_clear();
        return $execute;
    }

    function last_query() {
        return $this->lastQuery;
    }

    function query($query) {
        return select_mysql($query);
    }

    function _clear() {
        $this->where = array();
        $this->inner_join = array();
        $this->select = '*';
        $this->query = '';
        $this->set = null;
        $this->orderby = array();
        $this->limit = '';
        $this->groupby = '';
        $this->limit = '';
        $this->having = '';
        $this->ignore = '';


        $this->pdoWhere = array();
        $this->pdoData = array();
        $this->pdoSet = array();
        $this->pdoDataNoEscape = array();

    }

    function __destruct() {
        $this->_clear();
    }

}