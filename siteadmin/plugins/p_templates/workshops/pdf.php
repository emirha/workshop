<?php
/** @var Attendee[] $attendees */
/** @var Workshop $workshop */
$teacher = Teacher::get($workshop->teacher_id);
$location = Location::get($workshop->location_id);
?><html>
<head>
    <style type="text/css">
        th {
            font-weight: bold;
            border-bottom: 1px solid #000000;
            padding-bottom: 5px;
            line-height: 2;
            margin-bottom: 5px;
        }

        td {
            padding: 5px 0;
            margin: 5px 0;
        }

        .border-bottom {
            border-bottom: 1px solid #CCCCCC;
        }

        table#header {
            font-size: 22px;
        }

        #workshopTitle {
            font-size: 26px;
        }
    </style>
</head>
<body>

<table cellpadding="0" cellspacing="0" border="0" width="100%" id="header">
    <tr>
        <td width="50%">
            <div id="workshopTitle"><strong><?php echo $workshop->reference.' '.$workshop->name?></strong></div>
            <div><strong>Docent:</strong> <?php echo $teacher->fullName()?></div>
            <div><strong>Location:</strong> <?php echo $location ?></div>
        </td>
        <td width="50%">
            <div><strong>Date:</strong> <?php echo date("d.m.Y",strtotime($workshop->event_date)) ?></div>
            <div><strong>Time:</strong> <?php echo $workshop->event_time?></div>
        </td>
    </tr>
</table>

<br />
<br />
<br />

<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <th width="110">Name<br /></th>
        <th width="40">Gender</th>
        <th width="30">Age</th>
        <th width="110">City</th>
        <th width="110">Phone</th>
        <th width="100">Status</th>
    </tr>
    <tr><td>&nbsp;</td></tr>

    <?php
    /** @var Attendee[] $attendees */
    /** @var Workshop $workshop */
    foreach ($attendees as $attendee) {
        $status = AttendeeStatus::get($attendee->status_id);

        $birthDate = new DateTime($attendee->birthdate);
        $eventDate = new DateTime($workshop->event_date);
        $interval = $eventDate->diff($birthDate);
        $age = $interval->format('%y');
        ?>

        <tr>
            <td><?php echo $attendee->firstname.' '.$attendee->lastname ?></td>
            <td><?php echo $attendee->gender ?></td>
            <td><?php echo $age ?></td>
            <td><?php echo $attendee->city ?></td>
            <td><?php echo $attendee->phone ?></td>
            <td><?php echo $status->name ?></td>
        </tr>

        <?php if ($attendee->experience) { ?>
            <tr>
                <td></td>
                <td colspan="5">
                    <br />
                    <br />
                    Experience: <?php echo $attendee->experience ?></td>
            </tr>
        <?php } ?>

        <tr><td class="border-bottom" colspan="6">&nbsp;</td></tr>
        <tr><td>&nbsp;</td></tr>
    <?php } ?>
</table>

</body>
</html>
