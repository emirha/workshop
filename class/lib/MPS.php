<?php

class MPS {

    /**
     * @var Db
     */
    static $db = null;

    private function __construct () {

    }

    public static function init() {
        if (self::$db == null)
            self::$db = new Db();
    }

    public static $mainSite = null;
    public static $mysqli_conn = null;
    public static $site = null;
}