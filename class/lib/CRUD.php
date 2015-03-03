<?php
interface CRUD {

    public static function headers();
    public function columns($baseURL = null);
    public function rowClass();
}