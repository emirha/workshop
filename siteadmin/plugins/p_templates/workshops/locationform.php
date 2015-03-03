<?php
$formMethod = 'updatelocation';
$editForm = true;
if (empty($_GET['id'])) {
    $location = new Location();
    $formMethod = 'addlocation';
    $editForm = false;
} else {
    $location = Location::get($_GET['id']);
}
?>
<form method="post" enctype="multipart/form-data" action="<?php echo $pluginURL.'?act='.$formMethod ?>">
    <?php if($editForm) { ?>
        <input type="hidden" name="id" value="<?php echo $location->id ?>" />
    <?php } ?>

    <nav class="navbar navbar-default">
        <div class="container-fluid">

            <h1 class="navbar-left navbar-text">
                <?php if($editForm) { ?>
                    Change Location <?php echo $location->name ?>
                <?php } else { ?>
                    Add New Location
                <?php } ?>
            </h1>

            <a class="btn btn-link navbar-btn navbar-right" href="<?php echo $pluginURL?>?act=locationslist" role="button">Cancel</a>
            <button type="submit" class="btn btn-success navbar-btn navbar-right">SAVE</button>
        </div>
    </nav>
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Public Part</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <label for="name">Title</label>
                <input type="text" class="form-control" name="name" id="name" value="<?php echo $location->name ?>" />
            </div>

            <div class="row">
                <div class="form-group col-xs-3">
                    <label for="street">Street</label>
                    <input type="text" class="form-control" name="street" id="street" value="<?php echo $location->street ?>" />
                </div>

                <div class="form-group col-xs-3">
                    <label for="houseno">Houseno</label>
                    <input type="text" class="form-control" name="houseno" id="houseno" value="<?php echo $location->houseno ?>" />
                </div>

                <div class="form-group col-xs-3">
                    <label for="postcode">Postcode</label>
                    <input type="text" class="form-control" name="postcode" id="postcode" value="<?php echo $location->postcode ?>" />
                </div>

                <div class="form-group col-xs-3">
                    <label for="city">City</label>
                    <input type="text" class="form-control" name="city" id="city" value="<?php echo $location->city ?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="siteurl">Website URL</label>
                <div class="input-group">
                    <span class="input-group-addon">http://</span>
                    <input type="text" class="form-control" name="siteurl" id="siteurl" value="<?php echo $location->siteurl?>" />
                </div>
            </div>

            <div class="form-group">
                <label for="extrainfo">Extra Info</label>
                <textarea class="form-control" name="extrainfo" id="extrainfo" rows="5"><?php echo $location->extrainfo ?></textarea>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Admin-Only Part</h3>
        </div>
        <div class="panel-body">

            <div class="row">
                <div class="form-group col-xs-6">
                    <label for="contactname">Contact Name</label>
                    <input type="text" class="form-control" name="contactname" id="contactname" value="<?php echo $location->contactname ?>" />
                </div>

                <div class="form-group col-xs-6">
                    <label for="contactemail">Contact E-mail</label>
                    <input type="text" class="form-control" name="contactemail" id="contactemail" value="<?php echo $location->contactemail ?>" />
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-4">
                    <label for="fee">Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="fee" id="fee" value="<?php echo money_format(MONEY_NOCURRENCY,$location->fee) ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-xs-4">
                    <label for="parking_fee">Parking Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="parking_fee" id="parking_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$location->parking_fee) ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>

                <div class="form-group col-xs-4">
                    <label for="extra_fee">Extra Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="extra_fee" id="extra_fee" value="<?php echo money_format(MONEY_NOCURRENCY,$location->extra_fee) ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="container-fluid">
            <a class="btn btn-link navbar-btn navbar-right" href="<?php echo $pluginURL?>?act=locationslist" role="button">Cancel</a>
            <button type="submit" class="btn btn-success navbar-btn navbar-right">SAVE</button>
        </div>
    </nav>
</form>


<script type="text/javascript">
    $(function() {
        $('#siteurl').keyup(function() {
            var str = $(this).val();
            $(this).val(str.replace("http://", ""));
            $(this).val(str.replace("https://", ""));
        });
    });
</script>