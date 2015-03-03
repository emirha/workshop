<?php

class Location extends Model implements CRUD {

    public  $id 	    ,
        $name 	        ,
        $street 	    ,
        $houseno        ,
        $postcode       ,
        $city 	        ,
        $extrainfo      ,
        $fee            ,
        $parking_fee    ,
        $extra_fee      ,
        $contactname    ,
        $contactemail   ,
        $siteurl
    ;

    public $googlemaps ;

    /**
     * @param null $id
     *
     * @return Location[]|Location
     */
    public static function get($id = null) {
        MPS::init();
        MPS::$db->from(self::$table);
        if ($id) {
            return MPS::$db->pdoWhere(array('id' => $id))->row(__CLASS__);
        } else {
            return MPS::$db->result(__CLASS__);
        }
    }

    public static $table = 'w_location';

    public static function headers () {
        return array(
            'Title',
            'Street',
            'Postcode',
            'City',
            'Fee',
            'Parking Fee',
            'Extra Fee',
            'Contact',
            'Contact mail',
            new TableHeader('',30),
            new TableHeader('',30),
        );
    }

    public function columns ($baseURL = null) {
        return array(
            $this->siteurl ? new Column('<a href="'.$this->siteurl.'" target="_blank">'.$this->name.'</a>') : new Column($this->name),
            new Column($this->street.' '.$this->houseno),
            'postcode',
            'city',
            new Column(money_format(MONEY_FORMAT, $this->fee)),
            new Column(money_format(MONEY_FORMAT, $this->parking_fee)),
            new Column(money_format(MONEY_FORMAT, $this->extra_fee)),
            new Column($this->contactname),
            new Column('<a href="mailto:'.$this->contactemail.'">'.$this->contactemail.'<a>'),
            new EditButton('editlocationform&amp;id='.$this->id),
            $this->canDelete() ? new DeleteButton('deletelocation&amp;id='.$this->id,"return confirm('This will delete location ".$this->name.". Continue?')") : null,
        );
    }

    function dataColumns () {
        return array(
            'name' 	     ,
            'street' 	 ,
            'houseno'    ,
            'postcode'   ,
            'city' 	     ,
            'extrainfo'  ,
            'fee'        ,
            'parking_fee',
            'extra_fee'  ,
            'contactname' ,
            'contactemail',
            'siteurl'   ,
        );
    }

    public function numericColumns() {
        return array(
            'fee',
            'parking_fee',
            'extra_fee',
        );
    }


    public function rowClass () {
        // TODO: Implement rowClass() method.
    }

    public function canDelete() {
        $count = MPS::$db->from(Workshop::$table)->select('COUNT(*) as cnt')->pdoWhere(array('location_id' => $this->id))->row();
        return ($count['cnt'] == 0);
    }

    public function __toString() {
        return $this->street.' '.$this->houseno.', '.$this->postcode.' '.$this->city;
    }
}