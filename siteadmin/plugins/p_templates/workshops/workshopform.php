<?php
$formMethod = 'updateworkshop';
$editForm = true;
if (empty($_GET['id'])) {
    $workshop = new Workshop();
    $formMethod = 'addworkshop';
    $editForm = false;
} else {
    $workshop = Workshop::get($_GET['id']);
}
?>

<?php
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.3/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/ui/1.11.3/jquery-ui.js"></script>

<form method="post" enctype="multipart/form-data" action="<?php echo $pluginURL.'?act='.$formMethod ?>">
    <?php if($editForm) { ?>
        <input type="hidden" name="id" value="<?php echo $workshop->id?>" />
    <?php } ?>

    <input type="hidden" id="actualDate" name="event_date" />

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <h1 class="navbar-left navbar-text">
                <?php if($editForm) { ?>
                    Change Workshop <?php echo $workshop->name?>
                <?php } else { ?>
                    Add New Workshop
                <?php } ?>
            </h1>

            <a class="btn btn-link navbar-btn navbar-right" href="<?php echo $pluginURL?>?act=workshopslist" role="button">Cancel</a>
            <button type="submit" class="btn btn-success navbar-btn navbar-right">SAVE</button>
        </div>
    </nav>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Public Part</h3>
        </div>

        <div class="panel-body">

            <div class="row">
                <div class="form-group col-sm-2">
                    <label for="reference">Reference</label>
                    <input type="text" class="form-control" name="reference" id="reference" value="<?php echo $workshop->reference?>" />
                </div>

                <div class="form-group col-sm-10">
                    <label for="name">Title</label>
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $workshop->name?>" />
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="datepicker">Date</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="datepicker" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="event_time">Time</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="event_time" id="event_time" value="<?php echo $workshop->event_time?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="max_students">Max Students</label>
                    <input type="text" class="form-control" name="max_students" id="max_students" value="<?php echo $workshop->max_students?>" />
                </div>

                <div class="form-group col-sm-3">
                    <label for="price">Price</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="price" id="price" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->price)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="teacher_id">Docent</label>
                    <select class="form-control" name="teacher_id" id="teacher_id">
                        <option value="0"> --- </option>
                        <?php foreach (Teacher::get() as $teacher) { ?>
                            <option value="<?php echo $teacher->id ?>" <?php echo $teacher->id == $workshop->teacher_id ? 'selected="selected"' : ''?>><?php echo $teacher->fullName() ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group col-sm-6">
                    <label for="location_id">Location</label>
                    <select class="form-control" name="location_id" id="location_id">
                        <option value="0"> --- </option>
                        <?php foreach (Location::get() as $location) { ?>
                            <option value="<?php echo $location->id ?>" <?php echo $location->id == $workshop->location_id ? 'selected="selected"' : ''?>><?php echo $location->name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="tt_link">TrioTicket Link</label>
                    <div class="input-group">
                        <span class="input-group-addon">http://</span>
                        <input type="text" class="form-control" name="tt_link" id="tt_link" value="<?php echo $workshop->tt_link?>" />
                    </div>
                </div>

                <div class="form-group col-sm-6">
                    <label for="extrapdf">Instructions pdf</label>
                    <div class="clearfix"></div>
                    <input class="pull-left" type="file" id="extrapdf" name="extrapdf" />
                    <?php if ($workshop->extrapdf) { ?>
                        <a class="pull-right" target="_blank" href="<?php echo URL.$workshop->extrapdf ?>">
                            <button type="button" class="btn btn-default">Preview</button>
                        </a>
                        <span class="pull-right">&nbsp;</span>
                        <a class="pull-right" href="<?php echo $pluginURL.'?act=workshopremovepdf&amp;id='.$workshop->id ?>">
                            <button type="button" class="btn btn-default">Remove</button>
                        </a>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" rows="7" id="description" class="form-control"><?php echo $workshop->description?></textarea>
            </div>

            <div class="form-group">
                <label for="instructions">Instruction</label>
                <textarea name="instructions" rows="7" id="instructions" class="form-control"><?php echo $workshop->instructions?></textarea>
            </div>

        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Admin-Only Part</h3>
        </div>
        <div class="panel-body">

            <p>If you leave price field empty, price set in docent / location details will be used as default</p>

            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="docent_fee">Docent Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="docent_fee" id="docent_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->docent_fee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="docent_travelfee">Docent Travel Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="docent_travelfee" id="docent_travelfee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->docent_travelfee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="vat">BTW</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="vat" id="vat" value="<?php echo empty($workshop->vat) ? 21 : $workshop->vat?>" />
                        <span class="input-group-addon">%</span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="extra_fee">Extra Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="extra_fee" id="extra_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->extra_fee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="form-group col-sm-3">
                    <label for="location_fee">Location Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="location_fee" id="location_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->location_fee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="parking_fee">Parking Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="parking_fee" id="parking_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->parking_fee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="marketing_fee">Marketing Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="marketing_fee" id="marketing_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->marketing_fee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="sales_fee">Sales Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="sales_fee" id="sales_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->sales_fee)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="staff_1">Staff 1</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="staff_1" id="staff_1" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->staff_1)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="staff_2">Staff 2</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="staff_2" id="staff_2" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->staff_2)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-sm-3">
                    <label for="staff_3">Staff 3</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="staff_3" id="staff_3" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->staff_3)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-sm-3">
                    <label for="sponsoring_income">Sponsoring (income)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="sponsoring_income" id="sponsoring_income" value="<?php echo money_format(MONEY_NOCURRENCY,$workshop->sponsoring_income)?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br />
    <br />
    <br />

    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="container-fluid">
            <a class="btn btn-link navbar-btn navbar-right" href="<?php echo $pluginURL?>?act=workshopslist" role="button">Cancel</a>
            <button type="submit" class="btn btn-success navbar-btn navbar-right">SAVE</button>
        </div>
    </nav>

</form>

<script type="text/javascript">
    $(function() {
        $('#tt_link').keyup(function() {
            var str = $(this).val();
            $(this).val(str.replace("http://", ""));
            $(this).val(str.replace("https://", ""));
        });

        $( "#datepicker" ).datepicker({
            dateFormat: "DD, d MM, yy",
            altField: "#actualDate",
            altFormat: "yy-mm-dd"
        });

        <?php if ($editForm) {
        $dateString = date('Y',strtotime($workshop->event_date)).','.(date('n',strtotime($workshop->event_date))-1).','.date('j',strtotime($workshop->event_date));
        ?>
        $( "#datepicker" ).datepicker( "setDate", new Date(<?php echo $dateString?>));
        <?php } ?>
    });
</script>