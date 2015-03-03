<?php

class Attendee {

    public $id          = null;
    public $workshop_id = null;
    public $firstname   = null;
    public $lastname    = null;
    public $gender      = null;
    public $birthdate   = null;
    public $city        = null;
    public $street      = null;
    public $houseno     = null;
    public $postcode    = null;
    public $emailadress = null;
    public $phone       = null;
    public $experience  = null;
    public $status_id   = 1;
    public $price       = null;

    public static $STATUS_NEW = 1;
    public static $STATUS_DEFINITIEVE = 2;
    public static $STATUS_BEVESTIGING = 5;
    /**
     * @var AttendeeLog[]
     */
    public $log      = null;

    /**
     * @var AttendeeStatus
     */
    public $status      = null;

    /**
     * @var Workshop
     */
    public $workshop = null;

    public function init($data = null) {
        $this->id          = empty($data['id']          ) ? null :  $data['id']          ;
        $this->workshop_id = empty($data['workshop_id'] ) ? null :  $data['workshop_id'] ;
        $this->status_id      = empty($data['status_id']      ) ? null :  $data['status_id'] ;
        $this->firstname   = empty($data['firstname']   ) ? null :  $data['firstname']   ;
        $this->lastname    = empty($data['lastname']    ) ? null :  $data['lastname']    ;
        $this->gender      = empty($data['gender']      ) ? null :  $data['gender']      ;
        $this->birthdate   = empty($data['birthdate']   ) ? null :  $data['birthdate']   ;
        $this->city        = empty($data['city']        ) ? null :  $data['city']        ;
        $this->street      = empty($data['street']      ) ? null :  $data['street']      ;
        $this->houseno     = empty($data['houseno']     ) ? null :  $data['houseno']     ;
        $this->postcode    = empty($data['postcode']    ) ? null :  $data['postcode']    ;
        $this->emailadress = empty($data['emailadress'] ) ? null :  $data['emailadress'] ;
        $this->phone       = empty($data['phone']       ) ? null :  $data['phone']       ;
        $this->experience  = empty($data['experience']  ) ? null :  $data['experience']  ;

        if (!empty($data['workshop_id'] )) {
            $workshop = Workshop::get($this->workshop_id);
            if ($workshop) {
                $this->price = $workshop->price;
            }
        }

        $this->fillData();
    }

    public function save() {
        if ($this->status_id == null) $this->status_id = 1;

        $this->fillData();

        MPS::init();
        MPS::$db->into(self::$_table)->set($this->data);
        if (empty($this->id)) {
            $this->id = MPS::$db->insert();
        } else {
            MPS::$db->pdoWhere(array('id' => $this->id))->update();
        }
    }

    public function delete() {
        MPS::$db->from(self::$_table)->pdoWhere(array('id' => $this->id))->delete();
    }


    public function fullname() {
        return $this->firstname.' '.$this->lastname;
    }

    public function fullstreet() {
        return $this->street.' '.$this->houseno;
    }

    public function replaceMailContent($mailContent) {
        $workshop = Workshop::get($this->workshop_id);
        $workshop->location = Location::get($workshop->location_id);
        $workshop->teacher = Teacher::get($workshop->teacher_id);
        $replaces = array(
            '%firstname%' => $this->firstname,
            '%workshop%' => $workshop->name,
            '%workshopinstructions%' => '',
            '%workshopextrapdf%' => '',
            '%ttlink%' => $workshop->tt_link ? Urlhelper::prep_url($workshop->tt_link) : 'http://www.trioticket.nl/Musical-20.html',
            '%workshopdetails%' => '
                <hr />
                <p><strong>'.$workshop->name.' - '.$workshop->teacher->fullName().'</strong></p>

                <p>'.$workshop->description.'</p>

                <strong>Wanneer</strong>
                <p>'.date('l d F Y',strtotime($workshop->event_date)).'</p>

                <strong>Tijd</strong>
                <p>'.$workshop->event_time.'</p>

                <strong>Locatie</strong>
                <p>
                '.$workshop->location->name.'<br />
                '.$workshop->location->street.' '.$workshop->location->houseno.'<br />
                '.$workshop->location->postcode.' '.$workshop->location->city.'
                </p>

                <strong>Kosten</strong>
                <p>
                &euro; '.number_format($workshop->price,2,',','.').' *
                </p>

                <hr />
            '
        );

        if ($this->status_id == self::$STATUS_DEFINITIEVE) {
            $replaces['%workshopinstructions%'] = '<p>'.$workshop->instructions.'</p>';
            $replaces['%workshopextrapdf%'] = empty($workshop->extrapdf) ? '' : '<p><a href="'.URL.$workshop->extrapdf.'">Klik hier voor extra informatie over deze workshop</a></p>';
        }

        $from = array();
        $to = array();
        foreach ($replaces as $key => $val) {
            $from[] = $key;
            $to[] = $val;
        }

        return str_replace($from,$to,$mailContent);
    }

    /**
     * @param null $id
     *
     * @return Attendee|Attendee[]
     */
    public static function get($id = null) {
        MPS::init();
        MPS::$db->from(self::$_table);
        if ($id) {
            if (self::$orderByString) {
                MPS::$db->orderby(self::$orderByString);
            }
            self::$orderByString = null;
            return MPS::$db->pdoWhere(array('id' => $id))->row(__CLASS__);
        } else {
            $data = MPS::$db->pdoWhere(empty(self::$filter) ? array() : self::$filter)->result(__CLASS__);
            self::$filter = array();
            return $data;
        }
    }

    public static function getBy($key, $val) {
        MPS::init();

        self::filter($key,$val);

        MPS::$db->pdoWhere(self::$filter);

        if (self::$orderByString) {
            MPS::$db->orderby(self::$orderByString);
        }

        $data =  MPS::$db->from(self::$_table)->result(__CLASS__);

        self::$orderByString = null;
        self::$filter = null;

        return $data;
    }

    public static function orderby($orderString) {
        self::$orderByString = $orderString;
    }

    public static function filter($key, $val) {
        self::$filter[$key] = $val;
    }

    public static function filterString($string) {
        self::$filterString[] = $string;
    }

    public function getPrice() {
        if ($this->over21()) {
            return $this->price;
        } else {
            return $this->price;
        }
    }

    public function over21() {
        $now = new DateTime("now");
        $birthyear = new DateTime($this->birthdate);
    }

    private function fillData() {
        $this->data = array();

        $this->data['workshop_id'] = $this->workshop_id;
        $this->data['status_id']   = $this->status_id;
        $this->data['firstname']   = $this->firstname;
        $this->data['lastname']    = $this->lastname;
        $this->data['gender']      = $this->gender;
        $this->data['birthdate']   = $this->birthdate;
        $this->data['city']        = $this->city;
        $this->data['street']      = $this->street;
        $this->data['houseno']     = $this->houseno;
        $this->data['postcode']    = $this->postcode;
        $this->data['emailadress'] = $this->emailadress;
        $this->data['phone']       = $this->phone;
        $this->data['experience']  = $this->experience;

        $this->data['price']  = $this->price;

    }

    public function getPhoneNo() {
        $areas = array('06','023');
        $phone = $this->phone;
        foreach ($areas as $area) {
            $phone = preg_replace('/'.$area.'/', $area.'-', $phone, 1);
        }
        return $phone;
    }

    public static function excelExportHeader($excelObject) {
        $columnRow = 1;
        $columnIndex = 0;
        $columnHeaders = array(
            'First Name',
            'Last Name',
            'Gender',
            'Age',
            'City',
            'Street',
            'House No.',
            'Postcode',
            'E-mail',
            'Phone',
            'Status',
        );

        foreach ($columnHeaders as $columnHeader) {
            $excelObject->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex++).$columnRow, $columnHeader);
        }
    }

    public function addAsRowToExcel($excelObject, $columnRow, Workshop $workshop) {
        $columnIndex = 0;
        $columns = array(
            'firstname',
            'lastname',
            'gender',
            'age',
            'city',
            'street',
            'houseno',
            'postcode',
            'emailadress',
            'phone',
            'status_id',
        );

        $status = AttendeeStatus::get($this->status_id);

        foreach ($columns as $column) {
            switch($column) {
                case 'status_id':
                    $columnValue = $status->name;
                    break;
                case 'age':
                    $birthDate = new DateTime($this->birthdate);
                    $eventDate = new DateTime($workshop->event_date);
                    $interval = $eventDate->diff($birthDate);
                    $columnValue = $interval->format('%y');
                    break;
                default:
                    $columnValue = $this->$column;
            }

            $excelObject->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex++).$columnRow, $columnValue);
        }
    }

    private $data = null;
    public static $_table = 'w_attendees';
    private static $orderByString;

    private static $filter;
    private static $filterString;
}