<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover({
            container: 'body'
        });

        $('#changeworkshop').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            var attendeeid = button.data('attendeeid');
            var workshopid = button.data('workshopid');

            modal.find('#attendee_id').val(attendeeid);
            modal.find('#new_workshop').val(workshopid);
        });
    });

</script>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th>#</th>
            <th>Docent</th>
            <th>Workshop</th>
            <th></th>
            <th>Name</th>
            <th>E-mail</th>
            <th>Age</th>
            <th>City</th>
            <th>Status</th>
            <th></th>
            <th>Log</th>
            <th></th>
        </tr>

        <?php $i = 0;
        /* @var $workshops Workshop[] */
        foreach ($workshops as $workshop) {

            /* @var $attendee Attendee */
            foreach ($workshop->attendees as $attendee) {
                $rowClass = '';
                switch ($attendee->status_id) {
                    case 2:
                        $rowClass = 'success';
                        break;
                    case 3:
                        $rowClass = 'danger';
                        break;
                    case 4:
                        $rowClass = 'warning';
                        break;
                    case 5:
                        $rowClass = 'info';
                        break;
                }
                ?>
                <tr style="height:34px" class="<?php echo $rowClass ?>">
                    <td><?php echo $attendee->id ?></td>
                    <td>
                        <?php echo $workshop->teacher->fullName(); ?><br />
                        <?php $workshop->activeFuture(); ?>
                    </td>
                    <td>
                        <strong><?php echo $workshop->name?></strong><br />
                        <?php echo date("d.m.'y",strtotime($workshop->event_date)).' '.$workshop->event_time ?>
                    </td>
                    <td>
                        <?php if ($attendee->status_id == Attendee::$STATUS_NEW) { ?>
                            <button data-workshopid="<?php echo $workshop->id ?>" data-attendeeid="<?php echo $attendee->id ?>" type="button" data-toggle="modal" data-target="#changeworkshop" class="btn btn-default">
                                Change
                            </button>
                        <?php } ?>
                    </td>
                    <td class="withpopup" title="<?php echo $attendee->experience?>">
                        <strong><?php echo $attendee->fullname() ?></strong>
                        <br />
                        <?php echo $attendee->getPhoneNo() ?>
                    </td>

                    <td><a href="mailto:<?php echo $attendee->emailadress ?>"><?php echo $attendee->emailadress ?></a></td>
                    <td>
                        <?php
                        $birthDate = new DateTime($attendee->birthdate);
                        $eventDate = new DateTime($workshop->event_date);
                        $interval = $eventDate->diff($birthDate);
                        echo '<strong>'.$interval->format('%y').'</strong><br />';
                        echo '<em>'.date("d.m.Y",strtotime($attendee->birthdate)).'</em>';
                        ?>
                    </td>
                    <td class="withpopup" title="<?php echo $attendee->postcode.' '.$attendee->fullstreet()."\n".$attendee->phone ?>"><?php echo $attendee->city ?></td>
                    <td>
                        <select data-ajaxhref="<?php echo $url ?>?act=changeStatus" class="ajaxChange" data-id="<?php echo $attendee->id ?>" data-column="status_id">
                            <?php foreach (AttendeeStatus::get() as $attendeeStatus) { ?>
                                <option value="<?php echo $attendeeStatus->id?>" <?php echo $attendeeStatus->id == $attendee->status_id ? 'selected' : ''?>><?php echo $attendeeStatus->name?></option>
                            <?php } ?>
                        </select>
                        <span></span>
                    </td>
                    <td>
                        <a href="#" data-ajaxhref="<?php echo $url ?>?act=sendemail&amp;id=<?php echo $attendee->id ?>" class="ajaxAction" >
                            <button type="button" class="btn btn-default">
                                Mail
                            </button>
                        </a>
                        <span></span>
                    </td>

                    <?php
                    $attendeeLog = '';
                    foreach ($attendee->log as $log) {
                        $attendeeLog .= date(DATEFORMAT_PHP,strtotime($log->date_added)).' '.$log->logline."<br />";
                    } ?>
                    <td>
                        <?php if($attendeeLog) {?>
                            <button type="button" class="btn btn-default" data-html="true" data-container="body" data-toggle="popover" data-placement="bottom" data-content="<?php echo $attendeeLog?>">
                                View Log
                            </button>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($attendee->status_id == AttendeeStatus::$STATUS_NEW || $attendee->status_id == AttendeeStatus::$STATUS_REJECTED) { ?>
                            <a href="<?php echo $url ?>?act=attendeedelete&amp;id=<?php echo $attendee->id ?>" onclick="return confirm('This will delete attendee <?php echo htmlspecialchars($attendee->fullname()) ?>')">
                                <button type="button" class="btn btn-danger">Delete</button>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
                <?php $i++;
            }
        } ?>

    </table>
</div>

<div class="modal fade" id="changeworkshop" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" action="<?php echo $url ?>?act=changeworkshop&amp;id=<?php echo $attendee->id ?>">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="exampleModalLabel">Change Attendee Workshop</h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id" id="attendee_id" value="" />

                    <div class="form-group">
                        <label for="new_workshop" class="control-label">New Workshop</label>
                        <select class="form-control" name="new_workshop" id="new_workshop">
                            <?php
                            foreach (Workshop::get() as $workshop) {
                                if (!$workshop->activeFuture()) continue;
                                ?>
                                <option value="<?php echo $workshop->id ?>"><?php echo $workshop->reference.' '.$workshop->name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Change</button>
                </div>

            </form>
        </div>
    </div>
</div>