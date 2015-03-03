<?php

class Workshop extends Model implements CRUD {

    public $id 	              ,
        $reference        ,
        $name 	          ,
        $description      ,
        $event_date       ,
        $event_time       ,
        $teacher_id       ,
        $location_id      ,
        $price 	          ,
        $max_students     ,
        $tt_link          ,
        $docent_fee       ,
        $docent_travelfee ,
        $vat              ,
        $location_fee     ,
        $parking_fee      ,
        $marketing_fee    ,
        $extra_fee        ,
        $sales_fee        ,
        $active = 1       ,
        $instructions     ,
        $extrapdf         ,
        $staff_1         ,
        $staff_2         ,
        $staff_3         ,
        $sponsoring_income,
        $few_left = 0
    ;

    /**
     * @var Teacher
     */
    public $teacher ;

    /**
     * @var Location
     */
    public $location ;

    public function __construct() {
        parent::__construct();
        $this->insertData = array();
    }

    /**
     * @param null $id
     *
     * @return Workshop[]|Workshop
     */
    public static function get($id = null) {
        MPS::init();
        self::$filterString  = array();
        MPS::$db->from(self::$table);

        if (!empty(self::$filterString)) {
            MPS::$db->where(implode(' AND ',self::$filterString));
        }

        if (!empty(self::$filterString)) {
            MPS::$db->where(implode(' AND ',self::$filterString));
        }

        if ($id) {
            self::$filter['id'] = $id;
            $data = MPS::$db->pdoWhere(empty(self::$filter)?array() : self::$filter)->row(__CLASS__);
            self::$filter = array();
            return $data;
        } else {
            $data = MPS::$db->pdoWhere(empty(self::$filter)?array() : self::$filter)->result(__CLASS__);
            self::$filter = array();
            return $data;
        }

    }

    public static function filter($key, $val) {
        self::$filter[$key] = $val;
    }

    public static function filterString($string) {
        self::$filterString[] = $string;
    }

    /**
     * @param null $id
     *
     * @return Workshop[]
     */
    public static function getBy($key, $val) {
        MPS::init();
        self::$filter[$key] = $val;

        MPS::$db->from(self::$table);

        if (!empty(self::$filterString)) {
            MPS::$db->where(implode(' AND ',self::$filterString));
        }
        return MPS::$db->pdoWhere(self::$filter)->result(__CLASS__);
    }

    public static $table = 'w_workshop';
    private static $filter;
    private static $filterString;

    public static function headers () {
        return array(
            'Reference',
            'Title',
            'Date/Time',
            'Teacher',
            'Location',
            'Sudents',
            'Price',
            'Sales',
            'Expenses',
            'Profit',
            new TableHeader('',30),
            new TableHeader('',30),
            new TableHeader('',30),
            new TableHeader('',30),
            new TableHeader('',30),
        );
    }

    public function columns ($baseURL = null) {
        $totalSales = $this->totalSales();
        $totalExpenses = $this->totalExpenses();
        return array(
            'reference',
            new Column('<a href="'.$baseURL.'?act=addfilter&amp;filter_workshop='.$this->id.'">'.$this->name.'</a>'),
            new Column(date(DATEFORMAT_PHP,strtotime($this->event_date)).'<br />'.$this->event_time),
            new Column(is_object($this->teacher) ? '<a href="'.$baseURL.'?act=addfilter&amp;filter_teacher='.$this->teacher_id.'">'.$this->teacher->fullName().'</a>' : '' ),
            new Column(is_object($this->location) ? $this->location->name : ''),
            new Column($this->reservedPlaces().' / '.$this->max_students.'<br />'.$this->newAttendeesCount().' new'),
            new Column(money_format(MONEY_FORMAT, doubleval($this->price))),
            new Column(money_format(MONEY_FORMAT, doubleval($totalSales))),
            new Column(money_format(MONEY_FORMAT, doubleval($totalExpenses))),
            new Column(money_format(MONEY_FORMAT, doubleval($totalSales-$totalExpenses)),$totalSales > $totalExpenses ? 'success' : 'danger'),
            $this->few_left ? new BootstrapButton('Few Left','fewleftdeactivateworkshop&amp;id='.$this->id,'btn-warning') : new BootstrapButton('Few Left','fewleftactivateworkshop&amp;id='.$this->id,'btn-default'),
            $this->activeWorkshop() ? new BootstrapButton('VOL','deactivateworkshop&amp;id='.$this->id,'btn-default') : new BootstrapButton('VOL','activateworkshop&amp;id='.$this->id,'btn-warning'),
            new BootstrapButton('Export','#','btn-info',array('data-toggle' => 'modal',  'data-target' => '#exportworkshop', 'data-workshopid' => $this->id)),
            new EditButton('editworkshopform&amp;id='.$this->id),
            $this->canDelete() ? new DeleteButton('deleteworkshop&amp;id='.$this->id,"return confirm('This will delete workshop ".$this->name.". Continue?')") : '',
        );
    }

    public function canDelete() {
        $count = MPS::$db->from(Attendee::$_table)->select('COUNT(*) as cnt')->pdoWhere(array('workshop_id' => $this->id))->row();
        return ($count['cnt'] == 0);
    }


    public function dataColumns() {
        return array(
            'reference'         ,
            'name' 	            ,
            'description'       ,
            'event_date'        ,
            'event_time'        ,
            'teacher_id'        ,
            'location_id'       ,
            'price' 	        ,
            'max_students'      ,
            'tt_link'           ,
            'docent_fee'        ,
            'docent_travelfee'  ,
            'vat'               ,
            'location_fee'      ,
            'parking_fee'       ,
            'marketing_fee'     ,
            'extra_fee'         ,
            'sales_fee'         ,
            'active'            ,
            'instructions'      ,
            'extrapdf'          ,
            'staff_1'           ,
            'staff_2'           ,
            'staff_3'           ,
            'sponsoring_income' ,
            'few_left' ,
        );
    }

    public function activeFuture() {
        if (!$this->active) return false;
        $now = new DateTime("now");
        $workshopDate = new DateTime($this->event_date);

        return ($workshopDate > $now);
    }

    public function totalSales() {
        $totalSales = 0;
        MPS::$db->pdoWhere(array('workshop_id' => $this->id, 'status_id' => Attendee::$STATUS_DEFINITIEVE));

        foreach (Attendee::get() as $attendee) {
            $totalSales += $attendee->getPrice();
        }

        return $totalSales;
    }

    public function numericColumns() {
        return array(
            'price'             ,
            'docent_fee'        ,
            'docent_travelfee'  ,
            'location_fee'      ,
            'parking_fee'       ,
            'marketing_fee'     ,
            'extra_fee'         ,
            'sales_fee'         ,
            'staff_1'           ,
            'staff_2'           ,
            'staff_3'           ,
            'sponsoring_income' ,
        );
    }

    public function totalExpenses() {
        return (
            doubleval($this->docent_fee)
            + doubleval($this->extra_fee)
            + doubleval($this->location_fee)
            + doubleval($this->marketing_fee)
            + doubleval($this->parking_fee)
            + doubleval($this->sales_fee)
            + doubleval($this->docent_travelfee)

            + doubleval($this->staff_1)
            + doubleval($this->staff_2)
            + doubleval($this->staff_3)

            - doubleval($this->sponsoring_income)
        );
    }

    public function rowClass () {
        //        return $this->totalSales() > $this->totalExpenses() ? 'success' : 'danger';
    }

    public function newAttendeesCount() {
        $attendees = MPS::$db->select('count(*) as cnt')->from(Attendee::$_table)->pdoWhere(array('workshop_id' => $this->id, 'status_id' => Attendee::$STATUS_NEW))->row();
        return $attendees['cnt'];
    }

    public function reservedPlaces() {
        $attendees = MPS::$db->select('count(*) as cnt')->from(Attendee::$_table)->where('status_id IN ('.Attendee::$STATUS_DEFINITIEVE.','.Attendee::$STATUS_BEVESTIGING.')')->pdoWhere(array('workshop_id' => $this->id))->row();
        return $attendees['cnt'];
    }

    public function seatsLeft() {
        return ($this->max_students - $this->reservedPlaces());
    }

    public function activeWorkshop() {
        if (!$this->active) return false;
        if (!$this->seatsLeft()) return false;
        return true;
    }

    function fewLeft() {
        if ($this->few_left) return true;
        if ($this->activeWorkshop() && $this->seatsLeft() <= 3)  return true;
        return false;
    }

    function exportExcel(array $statuses) {
        $filename = 'ws_'.$this->reference.'.xlsx';
        require_once LOCATION.'class/PHPExcel.php';
        require_once LOCATION.'class/PHPExcel/Writer/Excel2007.php';

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Conxsys BV")
                    ->setLastModifiedBy("Conxsys BV")
                    ->setTitle($filename.' workshop export')
                    ->setSubject($filename.' workshop export')
                    ->setDescription($filename.' workshop export')
                    ->setKeywords($filename.' workshop export')
                    ->setCategory('workshop export');
        $objPHPExcel->setActiveSheetIndex(0);

        Attendee::excelExportHeader($objPHPExcel);

        $exportStatuses = array();
        foreach ($statuses as $status) {
            $exportStatuses[] = intval($status);
        }

        MPS::$db->where('status_id IN ('.implode(',',$exportStatuses).')');
        MPS::$db->pdoWhere(array('workshop_id' => $this->id))->orderby('status_id');
        $attendees = Attendee::get();

        $rowIndex = 2;
        foreach ($attendees as $attendee) {
                $attendee->addAsRowToExcel($objPHPExcel,$rowIndex++,$this);
        }

        $columnIndex = 1;
        $rowIndex++;
        $totalSales = $this->totalSales();
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex++).$rowIndex, 'Total sales');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex).$rowIndex, $totalSales);

        $columnIndex = 1;
        $rowIndex++;
        $totalExpenses = $this->totalExpenses();
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex++).$rowIndex, 'Total expenses');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex).$rowIndex, $totalExpenses);

        $columnIndex = 1;
        $rowIndex++;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex++).$rowIndex, 'Total income');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue(PHPExcel_Cell::stringFromColumnIndex($columnIndex).$rowIndex, $totalSales-$totalExpenses);

        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save(LOCATION.$filename);

        return LOCATION.$filename;
    }

    function exportPDF(array $statuses) {
        $filename = 'ws_'.$this->reference.'.pdf';

        require_once(LOCATION.'tcpdf/config/lang/eng.php');
        require_once(LOCATION.'tcpdf/tcpdf.php');
        require_once(LOCATION.'tcpdf/mypdf.php');

        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator('Conxsys');
        $pdf->SetAuthor('Conxsys');
        $pdf->SetTitle('Conxsys');
        $pdf->SetSubject('Conxsys');
        $pdf->SetKeywords('Conxsys');

        $pdf->setPrintHeader(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetFont('dejavusans', '', 6, '', true);

        $pdf->addHeader(null);
        $pdf->addFooter(null);
        $pdf->setPrintHeader(true);
        $pdf->setPrintHeader(true);
        $pdf->AddPage();

        $exportStatuses = array();
        foreach ($statuses as $status) {
            $exportStatuses[] = intval($status);
        }

        MPS::$db->where('status_id IN ('.implode(',',$exportStatuses).')');
        MPS::$db->pdoWhere(array('workshop_id' => $this->id))->orderby('status_id');
        $attendees = Attendee::get();

        Display::add('attendees',$attendees);
        Display::add('workshop',$this);
        $html = Display::fetch('workshops/pdf.php');

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->lastPage();

        $pdf->Output($filename, 'F');

        return $filename;
    }

    function removePdf() {
        @unlink(LOCATION.$this->extrapdf);
        $this->extrapdf = null;
        $this->save();
    }
}