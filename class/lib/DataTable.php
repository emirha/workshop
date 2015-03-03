<?php

class DataTable {

    public $headerColumns;

    public $data;

    /* @var $buttons BootstrapButton[] */
    public $buttons;

    public $deleteButton;
    public $editButton;

    public $baseUrl;
    /**
     * @return BootstrapButton[]
     */
    public function getButtons() {
        return $this->buttons;
    }

    /**
     * @param BootstrapButton[] $buttons
     */
    public function setButtons(array $buttons) {
        $this->buttons = $buttons;
    }

    /**
     * @param BootstrapButton $button
     */
    public function addButton(BootstrapButton $button) {
        $this->buttons[] = $button;
    }


    public function __construct($baseUrl) {
        $this->headerColumns = array();
        $this->buttons = array();
        $this->baseUrl = $baseUrl;
    }

    public function init(array $config) {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }
    }

    public function displayTable(array $config = null) {
        if ($config) $this->init($config);

        Display::add('headerColumns',$this->headerColumns);
        Display::add('data',$this->data);
        Display::add('buttons',$this->buttons);
        Display::add('baseURL',$this->baseUrl);

        Display::display('lib/datatable.php',true);

    }

}