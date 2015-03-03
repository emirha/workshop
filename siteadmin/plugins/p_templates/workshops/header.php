<nav class="navbar navbar-default">
    <div class="container-fluid">

        <a class="btn navbar-btn btn-default" href="<?php echo $pluginURL?>?act=workshopslist" role="button">WORKSHOPS</a>
        <a class="btn navbar-btn btn-default" href="<?php echo $pluginURL?>?act=attendeeslist" role="button">ATTENDEES</a>
        <a class="btn navbar-btn btn-default" href="<?php echo $pluginURL?>?act=docentslist" role="button">DOCENTS</a>
        <a class="btn navbar-btn btn-default" href="<?php echo $pluginURL?>?act=locationslist" role="button">LOCATIONS</a>


        <form method="post" id="filterform" action="<?php echo $pluginURL?>?act=addfilter" class="navbar-form navbar-right form-inline" role="search">
            <div class="form-group">
                <label for="filter_teacher">Filter by docent</label>
                <select class="form-control" id="filter_teacher" name="filter_teacher" onchange="$('#workshop').val('0'); $('#filterform').submit();">
                    <option value="0">---</option>
                    <?php foreach (Teacher::get() as $teacher) { ?>
                        <option <?php echo (!empty($_SESSION['filterWorkshops']['teacher']) && $teacher->id == $_SESSION['filterWorkshops']['teacher']) ? 'selected="selected"' : ''?>  value="<?php echo $teacher->id?>"><?php echo $teacher->fullName()?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="workshop">Filter by workshop</label>
                <select class="form-control" id="workshop" name="filter_workshop" onchange="$('#filterform').submit();">
                    <option value="0">---</option>
                    <?php
                    if (!empty($_SESSION['filterWorkshops']['teacher'])) {
                        Workshop::filter('teacher_id',$_SESSION['filterWorkshops']['teacher']);
                    }
                    foreach (Workshop::get() as $_workshop) { ?>
                        <option <?php echo (!empty($_SESSION['filterWorkshops']['workshop']) && $_workshop->id == $_SESSION['filterWorkshops']['workshop']) ? 'selected="selected"' : ''?>  value="<?php echo $_workshop->id?>"><?php echo $_workshop->reference.' '.$_workshop->name?></option>
                    <?php } ?>
                </select>
            </div>

            <a class="btn btn-link" href="<?php echo $pluginURL?>?act=clearfilter" role="button">clear filter</a>
        </form>


    </div>
</nav>