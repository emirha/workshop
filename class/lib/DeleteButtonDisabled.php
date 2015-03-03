<?php

class DeleteButtonDisabled extends Column {

    public $action;
    public $onclick;

    function __construct($action = 'delete', $onclick = 'This will delete selected. Continue?') {
        $this->action = $action;
        $this->onclick = $onclick;
    }

    function toString($baseURL) {
        return '<button type="button" class="btn btn-default" disabled="disabled">Delete</button>';
    }
}