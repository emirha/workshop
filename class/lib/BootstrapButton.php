<?php
class BootstrapButton {

    public $label;
    public $act;
    public $extraClass;
    public $extraInfo;

    function __construct($label, $act, $extraClass = 'btn-default', array $extraInfo = array()) {
        $this->label = $label;
        $this->act = $act;
        $this->extraClass = $extraClass;
        $this->extraInfo = $extraInfo;
    }

    function toString($baseURL) {
        $extraInfo = '';

        if (is_array($this->extraInfo)) {
            foreach ($this->extraInfo as $k => $v) {
                $extraInfo .= ' '.$k.' = "'.$v.'" ';
            }
        }

        return '<a '.$extraInfo.' href="'.$baseURL.'?act='.$this->act.'"><button type="button" class="btn '.$this->extraClass.'">'.$this->label.'</button></a>';
    }

}