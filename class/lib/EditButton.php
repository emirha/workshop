<?php

class EditButton extends Column {

    public $action;
    public $onclick;
    function __construct($action = 'editform') {
        $this->action = $action;
    }

    function toString($baseURL) {
        $onclick = empty($this->onclick) ? 'onclick="'.$this->onclick.'"' : '';
        return '<a href="'.$baseURL.'?act='.$this->action.'" '.$onclick.'><button type="button" class="btn btn-info">Edit</button></a>';
    }

}