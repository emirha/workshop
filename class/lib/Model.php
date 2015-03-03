<?php

abstract class Model {

    public $id;

    protected $insertData;

    public static $table;

    abstract function dataColumns();
    abstract function numericColumns();

    public function __construct() {
    }

    public function save() {
        if ($this->id) {
            return MPS::$db->into(static::$table)->set($this->toArray())->pdoWhere(array('id' => $this->id))->update();
        } else
            return MPS::$db->into(static::$table)->set($this->toArray())->insert();
    }

    public function delete() {
        MPS::$db->from(static::$table)->pdoWhere(array('id' => $this->id))->delete();
    }

    public function canDelete() {
        return true;
    }

    public function set(array $data) {
        foreach ($this->numericColumns() as $numeric) {
            if (isset($data[$numeric])) $data[$numeric] = str_replace(array(',',' '),array('.',''),$data[$numeric]);
        }

        foreach ($data as $key => $val) {
            if ($key == 'id') continue;
            if (property_exists($this,$key))
                $this->$key = $val;
        }
    }


    public function toArray () {
        $array = array();
        $columns = $this->dataColumns();
        foreach ($columns as $column) {
            if (property_exists($this,$column))
                $array[$column] = $this->$column;
        }

        return $array;
    }

}