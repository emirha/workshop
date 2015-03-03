<?php
class Workshops extends AdminPlugin {

    private $displayHTML = TRUE;
    private $extraJS;
    private $url;
    private $extraCSS;
    private $extraJSCode = '';
    private $headerbuttons;

    private $pluginsInfo;


    function __construct() {
        error_reporting(E_ALL);
        ini_set('display_errors','1');

        MPS::init();
        $this->extraJS = array();
        $this->extraCSS = array();
        $this->url = URL.'siteadmin/plugins/workshops.php';
        Display::add('url',$this->url);

        $this->extraJSCode = '';

        Display::add('header_file','workshops/header.php');
        Display::add('bootstrap',true);
        Display::add('pluginURL',$this->url);

        $this->extraJS[] = 'siteadmin.js';
    }

    function index() {
        $this->workshopslist();
    }

    function attendeeslist() {
        if (!empty($_SESSION['filterWorkshops']['workshop'])) {
            Workshop::filter('id',$_SESSION['filterWorkshops']['workshop']);
        }
        if (!empty($_SESSION['filterWorkshops']['teacher'])) {
            Workshop::filter('teacher_id',$_SESSION['filterWorkshops']['teacher']);
        }

        $workshops = Workshop::get();
        foreach ($workshops as $workshop) {
            $workshop->teacher = Teacher::get($workshop->teacher_id);
            if (empty($_SESSION['filterWorkshops'])) {
                Attendee::filter('status_id',AttendeeStatus::$STATUS_NEW);
            }
            Attendee::orderby('date_added DESC');
            $workshop->attendees = Attendee::getBy('workshop_id',$workshop->id);
            foreach ($workshop->attendees as $attendee) {
                $attendee->log = AttendeeLog::get($attendee->id);
            }
        }

        Display::add('workshops',$workshops);
        Display::add('include_file','workshops/index.php');
    }

    function addfilter() {
        $this->clearfilter(false);

        if (!empty($_POST['filter_teacher'])) {
            $_SESSION['filterWorkshops']['teacher'] = $_POST['filter_teacher'];
        }

        if (!empty($_POST['filter_workshop'])) {
            $_SESSION['filterWorkshops']['workshop'] = $_POST['filter_workshop'];
        }

        if (!empty($_GET['filter_teacher'])) {
            $_SESSION['filterWorkshops']['teacher'] = $_GET['filter_teacher'];
        }

        if (!empty($_GET['filter_workshop'])) {
            $_SESSION['filterWorkshops']['workshop'] = $_GET['filter_workshop'];
        }

        Urlhelper::redirect($this->url.'?act=attendeeslist');
        exit();
    }

    function clearfilter($redirect = true) {
        $_SESSION['filterWorkshops']['teacher'] = null;
        unset($_SESSION['filterWorkshops']['teacher']);

        $_SESSION['filterWorkshops']['workshop'] = null;
        unset($_SESSION['filterWorkshops']['workshop']);

        unset($_SESSION['filterWorkshops']);

        if ($redirect) {
            Urlhelper::redirect($this->url.'?act=attendeeslist');
            exit();
        }
    }

    function __destruct() {
        if ($this->displayHTML) {
            Display::add('extraJS',$this->extraJS);
            Display::add('extraCSS',$this->extraCSS);
            Display::add('extraJSCode',$this->extraJSCode);
            Display::add('headerbuttons',$this->headerbuttons);
            Display::add('URL',$this->url);
            Display::add('pluginName','MYTRIOWEB PLUGIN');
            Display::add('helppageID',NULL);

            Display::display('header_html.php');
            Display::display('header.php');
            Display::display('footer_html.php');
        }
    }

    function changeStatus() {
        $this->displayHTML = false;
        $attendee  = Attendee::get(intval($_POST['id']));
        $attendee->status_id = intval($_POST['newVal']);
        $attendee->save();

        $status = AttendeeStatus::get(intval($_POST['newVal']));

        $attendeeLog = new AttendeeLog($attendee->id, 'New status : '.$status->name);
        $attendeeLog->save();

        echo 'Changed';
    }

    function sendemail() {
        $this->displayHTML = false;
        $attendee = Attendee::get(intval($_GET['id']));
        $attendee->status = AttendeeStatus::get($attendee->status_id);

        $workshop = Workshop::get($attendee->workshop_id);
        $workshop->teacher = Teacher::get($workshop->teacher_id);

        $mailContent = '';
        $mailSubject = $workshop->name.' '.$workshop->teacher->fullName().' - '.$attendee->status->name;
        $footerContent = '';
        switch ($attendee->status_id) {
            case AttendeeStatus::$STATUS_NEW:
                $mailContent = getPageID(81);
                $mailSubject = $workshop->name.' '.$workshop->teacher->fullName();
                break;
            case AttendeeStatus::$STATUS_REQUESTPAYMENT:
                $mailContent = getPageID(80);
                $footerContent = getPageID(77);
                break;
            case AttendeeStatus::$STATUS_ACCEPTED:
                $mailContent = getPageID(75);
                $footerContent = getPageID(77);
                break;
            case AttendeeStatus::$STATUS_HOLD:
                $mailContent = getPageID(73);
                $footerContent = getPageID(77);
                break;
            case AttendeeStatus::$STATUS_REJECTED:
                $mailContent = getPageID(74);
                $footerContent = getPageID(77);
                break;
        }

        include LOCATION.'class/ci_email.php';
        $email = new ci_email();
        $email->initialize($GLOBALS['mailer']);
        $email->from('workshops@musical20.nl', 'Musical 2.0 Workshops');
        $email->to($attendee->emailadress);
        $email->bcc('support@triomedia.nl');
        $email->subject($mailSubject);

        $mailContentToSend = $attendee->replaceMailContent($mailContent['html']).(empty($footerContent['html']) ? '' : $footerContent['html']);
        $email->message($mailContentToSend);
        $email->set_alt_message(strip_tags($mailContentToSend));

        $mailSent = $email->send();

        if ($mailSent) {
            $attendeeLog = new AttendeeLog($attendee->id, 'Mailed status : '.$attendee->status->name);
            $attendeeLog->save();
            echo 'Sent';
        } else {
            echo 'Error';

        }
    }

    function docentslist() {
        Display::add('include_file','workshops/docentslist.php');
    }

    function adddocentform() {
        Display::add('extraBodyCSS','navbarbottom');
        Display::add('include_file','workshops/docentform.php');
    }

    function editdocentform() {
        $docent = Teacher::get($_GET['id']);
        Display::add('docent',$docent);

        Display::add('extraBodyCSS','navbarbottom');
        Display::add('include_file','workshops/docentform.php');
    }

    function updatedocent() {
        $docent = Teacher::get($_POST['id']);

        $docent->set($_POST);

        if (is_file($_FILES['photo']['tmp_name'])) {
            if (is_file(LOCATION.'resources/Image/'.$docent->photo))
                @unlink(LOCATION.'resources/Image/'.$docent->photo);

            $image_lib = new CI_Image_lib();
            $imageFilename = date(DATETIMEFORMAT_FILENAME).'.jpg';
            $config['image_library'] = 'gd2';
            $config['source_image'] = $_FILES['photo']['tmp_name'];
            $config['new_image'] = LOCATION.'resources/Image/'.$imageFilename;
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 160;
            $config['height'] = 160;
            $image_lib->initialize($config);
            $image_lib->resize();

            $docent->photo = '/resources/Image/'.$imageFilename;
        }

        if (is_file($_FILES['taxdocument']['tmp_name'])) {
            if (is_file(LOCATION.'resources/File/'.$docent->taxdocument))
                @unlink(LOCATION.'resources/File/'.$docent->taxdocument);

            $ext = pathinfo($_FILES['taxdocument']['name'], PATHINFO_EXTENSION);
            $taxDocumentFilename = date(DATETIMEFORMAT_FILENAME).'.'.$ext;
            move_uploaded_file($_FILES['taxdocument']['tmp_name'],LOCATION.'resources/File/'.$taxDocumentFilename);
            $docent->taxdocument = '/resources/File/'.$taxDocumentFilename;
        }

        $docent->save();
        Urlhelper::redirect($this->url.'?act=docentslist');
    }

    function adddocent() {
        $docent = new Teacher();
        $docent->set($_POST);

        if (is_file($_FILES['photo']['tmp_name'])) {
            $image_lib = new CI_Image_lib();
            $imageFilename = date(DATETIMEFORMAT_FILENAME).'.jpg';
            $config['image_library'] = 'gd2';
            $config['source_image'] = $_FILES['photo']['tmp_name'];
            $config['new_image'] = LOCATION.'resources/Image/'.$imageFilename;
            $config['create_thumb'] = FALSE;
            $config['maintain_ratio'] = TRUE;
            $config['width'] = 160;
            $config['height'] = 160;
            $image_lib->initialize($config);
            $image_lib->resize();

            $docent->photo = '/resources/Image/'.$imageFilename;
        }

        if (is_file($_FILES['taxdocument']['tmp_name'])) {
            $ext = pathinfo($_FILES['taxdocument']['name'], PATHINFO_EXTENSION);
            $taxDocumentFilename = date(DATETIMEFORMAT_FILENAME).'.'.$ext;

            move_uploaded_file($_FILES['taxdocument']['tmp_name'],LOCATION.'resources/File/'.$taxDocumentFilename);
            $docent->taxdocument = '/resources/File/'.$taxDocumentFilename;
        }

        $docent->save();

        Urlhelper::redirect($this->url.'?act=docentslist');
    }

    function deletedocent() {
        $teacher = Teacher::get($_GET['id']);
        $teacher->delete();

        Urlhelper::redirect($this->url.'?act=docentslist');
    }

    function docentpreviewtaxdocument() {
        $this->displayHTML = false;
        $teacher = Teacher::get($_GET['id']);

        if (empty($teacher)) die('Docent does not exist');
        $ext = pathinfo(basename($teacher->taxdocument), PATHINFO_EXTENSION);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.Urlhelper::url_title($teacher->fullName()).'_taxdocument.'.$ext);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize(LOCATION.$teacher->taxdocument));

        readfile(LOCATION.$teacher->taxdocument);
    }

    function locationslist() {
        Display::add('include_file','workshops/locationslist.php');
    }

    function addlocationform() {
        Display::add('extraBodyCSS','navbarbottom');
        Display::add('include_file','workshops/locationform.php');
    }

    function addlocation() {
        $location = new Location();
        $location->set($_POST);
        $location->save();
        Urlhelper::redirect($this->url.'?act=locationslist');
    }

    function deletelocation() {
        $location = Location::get($_GET['id']);
        $location->delete();
        Urlhelper::redirect($this->url.'?act=locationslist');
    }

    function editlocationform() {
        Display::add('include_file','workshops/locationform.php');
    }

    function updatelocation() {
        $location = Location::get($_POST['id']);
        $location->set($_POST);
        $location->save();
        Urlhelper::redirect($this->url.'?act=locationslist');
    }

    function workshopslist() {
        Display::add('include_file','workshops/workshopslist.php');
    }

    function addworkshopform() {
        Display::add('extraBodyCSS','navbarbottom');
        Display::add('include_file','workshops/workshopform.php');
    }

    function addworkshop() {
        $workshop = new Workshop();

        if (!empty($_POST['teacher_id'])) {
            $teacher = new Teacher();
            if (!empty($_POST['teacher_id'])) {
                $teacher = Teacher::get($_POST['teacher_id']);
            }
            if (empty($_POST['docent_fee'])) {
                $_POST['docent_fee'] = $teacher->fee;
            }
            if (empty($_POST['docent_travelfee'])) {
                $_POST['docent_travelfee'] = $teacher->travelfee;
            }
        }

        if (!empty($_POST['location_id'])) {
            $location = new Location();
            if (!empty($_POST['location_id'])) {
                $location = Location::get($_POST['location_id']);
            }
            if (empty($_POST['location_fee'])) {
                $_POST['location_fee'] = $location->fee;
            }
            if (empty($_POST['parking_fee'])) {
                $_POST['parking_fee'] = $location->parking_fee;
            }
        }

        if (is_file($_FILES['extrapdf']['tmp_name'])) {
            $ext = pathinfo($_FILES['extrapdf']['name'], PATHINFO_EXTENSION);
            $pdfDocumentFilename = date(DATETIMEFORMAT_FILENAME).'.'.$ext;

            move_uploaded_file($_FILES['extrapdf']['tmp_name'],LOCATION.'resources/File/'.$pdfDocumentFilename);
            $workshop->extrapdf = '/resources/File/'.$pdfDocumentFilename;
        }

        $workshop->set($_POST);
        $workshop->save();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    function deleteworkshop() {
        $workshop = Workshop::get($_GET['id']);
        $workshop->delete();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    function editworkshopform() {
        Display::add('include_file','workshops/workshopform.php');
    }

    function updateworkshop() {
        $workshop = Workshop::get($_POST['id']);

        $teacher = new Teacher();
        if (!empty($_POST['teacher_id'])) {
            $teacher = Teacher::get($_POST['teacher_id']);
        }
        if (empty($_POST['docent_fee'])) {
            $_POST['docent_fee'] = $teacher->fee;
        }
        if (empty($_POST['docent_travelfee'])) {
            $_POST['docent_travelfee'] = $teacher->travelfee;
        }

        $location = new Location();
        if (!empty($_POST['location_id'])) {
            $location = Location::get($_POST['location_id']);
        }
        if (empty($_POST['location_fee'])) {
            $_POST['location_fee'] = $location->fee;
        }
        if (empty($_POST['parking_fee'])) {
            $_POST['parking_fee'] = $location->parking_fee;
        }

        if (is_file($_FILES['extrapdf']['tmp_name'])) {
            if (is_file(LOCATION.'resources/File/'.$workshop->extrapdf))
                @unlink(LOCATION.'resources/File/'.$workshop->extrapdf);

            $ext = pathinfo($_FILES['extrapdf']['name'], PATHINFO_EXTENSION);
            $pdfDocumentFilename = date(DATETIMEFORMAT_FILENAME).'.'.$ext;
            move_uploaded_file($_FILES['extrapdf']['tmp_name'],LOCATION.'resources/File/'.$pdfDocumentFilename);
            $workshop->extrapdf = '/resources/File/'.$pdfDocumentFilename;
        }

        $workshop->set($_POST);
        $workshop->save();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    function deactivateworkshop() {
        $workshop = Workshop::get($_GET['id']);
        $workshop->active = 0;
        $workshop->save();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    function activateworkshop() {
        $workshop = Workshop::get($_GET['id']);
        $workshop->active = 1;
        $workshop->save();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    function changeworkshop() {
        $attendee = Attendee::get($_POST['id']);
        $attendee->workshop_id = $_POST['new_workshop'];
        $attendee->save();
        Urlhelper::redirect($this->url.'?act=attendeeslist');
    }

    function attendeedelete() {
        $attendee = Attendee::get($_GET['id']);
        $attendee->delete();
        Urlhelper::redirect($this->url.'?act=attendeeslist');
    }

    function fewleftdeactivateworkshop() {
        $workshop = Workshop::get($_GET['id']);
        $workshop->few_left = 0;
        $workshop->save();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    function fewleftactivateworkshop() {
        $workshop = Workshop::get($_GET['id']);
        $workshop->few_left = 1;
        $workshop->save();
        Urlhelper::redirect($this->url.'?act=workshopslist');
    }

    public function setCookie($cookieName, $cookieValue, $httpOnly = true, $secure = false ) {
        setcookie(
            $cookieName,
            $cookieValue,
            2147483647,
            "/",
            $_SERVER["HTTP_HOST"],
            $secure,
            $httpOnly
        );
    }

    function exportworkshoppdf() {
        $this->displayHTML = false;
        $workshop = Workshop::get($_POST['workshop_id']);
        $fileName = $workshop->exportPDF($_POST['attendeegroup']);

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.basename($fileName));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileName));
        ob_clean();
        flush();

        $this->setCookie( 'downloadToken', 'workshopExportReady', false );

        readfile($fileName);
        unlink($fileName);
    }

    function exportworkshopexcel() {
        $this->displayHTML = false;
        $workshop = Workshop::get($_POST['workshop_id']);
        $fileName = $workshop->exportExcel($_POST['attendeegroup']);

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.basename($fileName));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fileName));
        ob_clean();
        flush();

        $this->setCookie( 'downloadToken', 'workshopExportReady', false );

        readfile($fileName);
        unlink($fileName);
    }

    function workshopremovepdf() {
        $workshop = Workshop::get($_GET['id']);
        $workshop->removePdf();
        Urlhelper::redirect($_SERVER['HTTP_REFERER']);
    }

    function docentremovetaxdocument() {
        $docent = Teacher::get($_GET['id']);
        $docent->removeTaxDocument();
        Urlhelper::redirect($_SERVER['HTTP_REFERER']);
    }
}