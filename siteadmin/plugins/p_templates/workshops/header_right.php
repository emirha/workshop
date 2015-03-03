<form method="post" id="filterform" action="<?php echo $pluginURL?>?act=addfilter">
    Filter by docent
    <select name="filter_teacher" onchange="$('#workshop').val('0'); $('#filterform').submit();">
        <option value="0">---</option>
        <?php foreach (Teacher::get() as $teacher) { ?>
            <option <?php echo (!empty($_SESSION['filterWorkshops']['teacher']) && $teacher->id == $_SESSION['filterWorkshops']['teacher']) ? 'selected="selected"' : ''?>  value="<?php echo $teacher->id?>"><?php echo $teacher->fullName()?></option>
        <?php } ?>
    </select>

    Filter by workshop
    <select id="workshop" name="filter_workshop" onchange="$('#filterform').submit();">
        <option value="0">---</option>

        <?php
        if (!empty($_SESSION['filterWorkshops']['teacher'])) {
            Workshop::filter('teacher_id',$_SESSION['filterWorkshops']['teacher']);
        }
        foreach (Workshop::get() as $_workshop) { ?>
            <option <?php echo (!empty($_SESSION['filterWorkshops']['workshop']) && $_workshop->id == $_SESSION['filterWorkshops']['workshop']) ? 'selected="selected"' : ''?>  value="<?php echo $_workshop->id?>"><?php echo $_workshop->name?></option>
        <?php } ?>
    </select>


    <a href="<?php echo $pluginURL?>?act=clearfilter">clear filter</a>
</form>


