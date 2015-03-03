<?php

$dataTable = new DataTable($pluginURL);
$dataTable->headerColumns = Location::headers();
MPS::$db->orderby('id DESC');
$dataTable->data = Location::get();

$dataTable->addButton(new BootstrapButton('Add New Location','addlocationform','btn-primary'));

$dataTable->displayTable();

