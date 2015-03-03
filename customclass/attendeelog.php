<?php

class AttendeeLog {

    public $id 	         ;
    public $attendee_id ;
    public $logline ;
    public $date_added;

    public function __construct($attendee_id = null, $logline = null) {
        if ($attendee_id)
            $this->attendee_id = $attendee_id;

        if ($logline)
            $this->logline = $logline;
    }
    /**
     * @param null $id
     *
     * @return AttendeeLog|AttendeeLog[]
     */
    public static function get($attendeeID) {
        MPS::init();
        return MPS::$db->from(self::$_table)->pdoWhere(array('attendee_id' => $attendeeID))->orderby('date_added')->result(__CLASS__);
    }

    public function save() {
        $this->fillData();

        MPS::init();
        MPS::$db->into(self::$_table)->set($this->data)->insert();
    }

    private function fillData() {
        $this->data = array();

        $this->data['attendee_id']  = $this->attendee_id;
        $this->data['logline']      = $this->logline;
    }

    public static $_table = 'w_attendees_log';
}