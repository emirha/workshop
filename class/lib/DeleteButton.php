<?php

class DeleteButton extends Column {

    public $action;
    public $onclick;

    function __construct($action = 'delete', $onclick = 'This will delete selected. Continue?') {
        $this->action = $action;
        $this->onclick = $onclick;
    }

    function toString($baseURL) {
        return '<a href="'.$baseURL.'?act='.$this->action.'" onclick="'.$this->onclick.'"><button type="button" class="btn btn-danger">Delete</button></a>';
    }
}