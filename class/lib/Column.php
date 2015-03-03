<?php
class Column {

    public $content,
        $class;

    function __construct($content = '', $class = '') {
        $this->content = $content;
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getClass () {
        return $this->class;
    }


    function __toString() {
        return $this->content;
    }

    function toString() {
        return $this->content;
    }

}