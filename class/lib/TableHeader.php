<?php

class TableHeader {

    public $width;
    public $content;

    public function __construct($content, $width = null) {
        $this->content = $content;
        $this->width = $width;
    }

}