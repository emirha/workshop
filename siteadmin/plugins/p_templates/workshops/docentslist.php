<?php

$dataTable = new DataTable($pluginURL);
MPS::$db->orderby('id DESC');
$dataTable->headerColumns = Teacher::headers();
$dataTable->data = Teacher::get();

$dataTable->addButton(new BootstrapButton('Add New Docent','adddocentform','btn-primary'));

$dataTable->displayTable();

