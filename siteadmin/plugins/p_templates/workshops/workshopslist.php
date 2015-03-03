<?php

$dataTable = new DataTable($pluginURL);
$dataTable->headerColumns = Workshop::headers();
MPS::$db->orderby('id DESC');
$workshops = Workshop::get();
foreach ($workshops as $workshop) {
    $workshop->teacher = Teacher::get($workshop->teacher_id);
    $workshop->location = Location::get($workshop->location_id);
}
$dataTable->data = $workshops;

$dataTable->addButton(new BootstrapButton('Add New Workshop','addworkshopform','btn-primary'));

$dataTable->displayTable();

?>

<div class="modal fade" id="exportworkshop" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Select Export Type</h4>
            </div>

            <div class="modal-body">
                <form method="post" id="exportworkshopform">
                    <input type="hidden" id="workshop_id" name="workshop_id" value="" />

                    <div id="progressbar" class="progress hidden">
                        <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                            <span class="sr-only"></span>
                        </div>
                    </div>

                    <div id="file_generated" class="hidden">Export has been generated and download started. Please check your download folder</div>

                    <div class="row">
                        <div class="col-sm-5">
                            <h5>Check statuses</h5>
                            <?php foreach (AttendeeStatus::get() as $attendeeStatus) { ?>
                                <div class="checkbox">
                                    <label>
                                        <input name="attendeegroup[]" type="checkbox" <?php echo $attendeeStatus->id == AttendeeStatus::$STATUS_ACCEPTED ? 'checked="checked"' : '' ?> value="<?php echo $attendeeStatus->id?>">
                                        <?php echo $attendeeStatus->name?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="pull-right col-sm-7">
                            <h5>Choose format</h5>
                            <a id="exportworkshopexcel" href="#"><img src="/siteadmin/images/workshop/excel.png" width="128" height="128" alt="" class="col-xs-6 img-responsive" /></a>
                            <a id="exportworkshoppdf" href="#"><img src="/siteadmin/images/workshop/pdf.png" width="128" height="128" alt="" class="col-xs-6 img-responsive" /></a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('#exportworkshop').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            var workshopid = button.data('workshopid');
            $('#progressbar').addClass('hidden');
            $('#workshop_id').val(workshopid);
            $('#file_generated').addClass('hidden');
        });

        $('#exportworkshopexcel').click(function(e) {
            e.preventDefault();
            var form = $('#exportworkshopform');
            form.attr('action','<?php echo $url ?>?act=exportworkshopexcel');
            form.submit();
        });

        $('#exportworkshoppdf').click(function(e) {
            e.preventDefault();
            var form = $('#exportworkshopform');
            form.attr('action','<?php echo $url ?>?act=exportworkshoppdf');
            form.submit();
        });

        $('.modal-body a').click(function() {
            $('#progressbar').removeClass('hidden');
            blockResubmit();
        });

        function getCookie(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for(var i=0; i<ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1);
                if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
            }
            return "";
        }

        function expireCookie( cName ) {
            var cookie = encodeURIComponent( cName ) + "=; expires=" + new Date( 0 ).toUTCString();
            document.cookie = cookie;
        }

        function setCursor( docStyle ) {
            $( "*" ).css({cursor:docStyle});
        }

        var downloadTimer;
        var attempts = 30;

        function blockResubmit() {
            var cookieName = 'downloadToken';
            var downloadToken = 'workshopExportReady';
            document.cookie = cookieName + "=empty; path=/";
            $('#file_generated').addClass('hidden');

            setCursor( "wait");

            downloadTimer = window.setInterval( function() {
                var token = getCookie(cookieName);

                if( (token == downloadToken) || (attempts == 0) ) {
                    unblockSubmit();
                }

                attempts--;
            }, 1000 );
        }

        function unblockSubmit() {
            setCursor("");
            window.clearInterval( downloadTimer );
            expireCookie( "downloadToken" );
            $('#progressbar').addClass('hidden');
            $('#file_generated').removeClass('hidden');
        }

    });
</script>
