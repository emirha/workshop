<?php
class AttendeeStatus {

    public $id 	;
    public $name;

    /**
     * @param null $id
     *
     * @return AttendeeStatus|AttendeeStatus[]
     */
    public static function get($id = null) {
        MPS::init();
        MPS::$db->from(self::$_table);
        if ($id) {
            return MPS::$db->pdoWhere(array('id' => $id))->row(__CLASS__);
        } else {
            return MPS::$db->orderby('sortorder')->result(__CLASS__);
        }
    }

    public static $STATUS_NEW = 1;
    public static $STATUS_ACCEPTED = 2;
    public static $STATUS_REJECTED = 3;
    public static $STATUS_HOLD = 4;
    public static $STATUS_REQUESTPAYMENT = 5;

    public static $_table = 'w_attendees_status';
}