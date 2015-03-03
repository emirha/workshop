<?php

class Teacher extends Model implements CRUD {

    public  $id 	       ,
        $firstname     ,
        $lastname      ,
        $bio 	       ,
        $photo         ,
        $taxno         ,
        $additionalinfo,
        $taxdocument   ,
        $fee           ,
        $travelfee     ,
        $email         ,
        $phone         ,
        $agency        ,
        $contact       ,
        $agent_phone   ,
        $agent_email   ,
        $siteurl
    ;

    public $workshops;

    public static $table = 'w_teacher';

    /**
     * @param null $id
     *
     * @return Teacher[]|Teacher
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

    public function fullName() {
        return $this->firstname.' '.$this->lastname;
    }


    public static function headers () {
        return array(
            'Name',
            'Photo',
            'Fee',
            'Travel Fee',
            'E-mail',
            'Agent E-mail',
            new TableHeader('Tax Document',120),
            new TableHeader('',30),
            new TableHeader('',30),
        );
    }

    public function columns ($baseURL = null) {
        return array(
            $this->siteurl ? new Column('<a href="'.Urlhelper::prep_url($this->siteurl).'" target="_blank">'.$this->fullName().'</a>') : new Column($this->fullName()),
            new Column('<img src="'.$this->photo.'" style="max-height:50px; max-width:50px" />'),
            new Column(money_format(MONEY_FORMAT, $this->fee)),
            new Column(money_format(MONEY_FORMAT, $this->travelfee)),
            new Column('<a href="mailto:'.$this->email.'">'.$this->email.'</a>'),
            new Column('<a href="mailto:'.$this->agent_email.'">'.$this->agent_email.'</a>'),
            $this->taxdocument ? new BootstrapButton('Download','docentpreviewtaxdocument&amp;id='.$this->id, 'btn-default', array('target' => '_blank')) : null,
            new EditButton('editdocentform&amp;id='.$this->id),
            $this->canDelete() ? new DeleteButton('deletedocent&amp;id='.$this->id,"return confirm('This will delete docent ".$this->fullName().". Continue?')") : null,
        );
    }

    public function delete () {
        @unlink(LOCATION.$this->photo);
        @unlink(LOCATION.$this->taxdocument);
        parent::delete();
    }

    public function canDelete() {
        $count = MPS::$db->from(Workshop::$table)->select('COUNT(*) as cnt')->pdoWhere(array('teacher_id' => $this->id))->row();
        return ($count['cnt'] == 0);
    }

    function dataColumns () {
        return array(
            'firstname',
            'lastname',
            'bio',
            'photo',
            'taxno',
            'additionalinfo',
            'taxdocument',
            'fee',
            'travelfee',
            'email',
            'phone',
            'agency',
            'contact',
            'agent_phone',
            'agent_email',
            'siteurl'
        );
    }

    public function numericColumns() {
        return array(
            'fee',
            'travelfee',
        );
    }

    public function rowClass () {
        // TODO: Implement rowClass() method.
    }

    function removeTaxDocument() {
        @unlink(LOCATION.$this->taxdocument);
        $this->taxdocument = null;
        $this->save();
    }
}