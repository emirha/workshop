<?php

class Display {

    private function __construct() {}

    private static $data = null;

    static function add($key, $val, $append = 1) {
        if (self::$data === null)
            self::$data = array();
        if (array_key_exists($key,self::$data) && $append) {
            if (is_array(self::$data[$key])) {
                self::$data[$key][] = $val;
            } else {
                $temp = self::$data[$key];
                self::$data[$key] = array();
                self::$data[$key][] = $temp;
                self::$data[$key][] = $val;
            }
        } else {
            self::$data[$key] = $val;
        }
    }

    static function clear() {
        self::$data = array();
    }

    static function display($file,$siteadmin = TRUE) {
        extract(self::$data);
        if ($siteadmin)
            include LOCATION.'siteadmin/plugins/p_templates/'.$file;
    }

    static function fetch($file,$siteadmin = TRUE) {
        ob_start();

        extract(self::$data);
        if ($siteadmin)
            include LOCATION.'siteadmin/plugins/p_templates/'.$file;
        else
            include LOCATION.'templates/'.$file;

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

    static function get($key,$separator = ' ') {
        $string = '';
        if (!empty(self::$data[$key])) {
            if (is_array(self::$data[$key])) {
                $string = implode($separator,self::$data[$key]);
            } else {
                $string = self::$data[$key];
            }
        }

        return $string;
    }
}