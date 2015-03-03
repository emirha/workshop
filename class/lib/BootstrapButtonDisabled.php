<?php
class BootstrapButtonDisabled extends BootstrapButton {

    function toString($baseURL) {
        return '<button type="button" disabled="disabled" class="btn '.$this->extraClass.'">'.$this->label.'</button>';
    }

}