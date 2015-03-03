<?php
/* @var $docent Teacher */
$formMethod = 'updatedocent';
$editForm = true;
if (empty($docent)) {
    $docent = new Teacher();
    $formMethod = 'adddocent';
    $editForm = false;
}
?>

<form method="post" enctype="multipart/form-data" action="<?php echo $pluginURL.'?act='.$formMethod ?>">
    <?php if($editForm) { ?>
        <input type="hidden" name="id" value="<?php echo $docent->id ?>">
    <?php } ?>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <h1 class="navbar-left navbar-text">
                <?php if($editForm) { ?>
                    Change Docent <?php echo $docent->fullName() ?>
                <?php } else { ?>
                    Add New Docent
                <?php } ?>
            </h1>

            <a class="btn btn-link navbar-btn navbar-right" href="<?php echo $pluginURL?>?act=docentslist" role="button">Cancel</a>
            <button type="submit" class="btn btn-success navbar-btn navbar-right">SAVE</button>
        </div>
    </nav>

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Public Part</h3>
        </div>

        <div class="panel-body">

            <div class="row">
                <div class="form-group col-xs-6">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" name="firstname" id="firstname" value="<?php echo $docent->firstname ?>" />
                </div>

                <div class="form-group col-xs-6">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" name="lastname" id="lastname" value="<?php echo $docent->lastname ?>" />
                </div>
            </div>

            <?php if (!empty($docent->photo)) { ?>
                <div class="form-group">
                    <img class="pull-right" src="<?php echo $docent->photo ?>" alt="" />
                </div>
            <?php } ?>

            <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" id="photo" name="photo" />
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="form-group col-xs-4">
                    <label for="email">E-mail</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="email" id="email" value="<?php echo $docent->email ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="phone">Phone</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="phone" id="phone" value="<?php echo $docent->phone ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></span>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="agency">Agency</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="agency" id="agency" value="<?php echo $docent->agency ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-briefcase"></span></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-4">
                    <label for="contact">Agency Contact</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="contact" id="contact" value="<?php echo $docent->contact ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="agent_phone">Agent Phone</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="agent_phone" id="agent_phone" value="<?php echo $docent->agent_phone ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-phone"></span></span>
                    </div>
                </div>
                <div class="form-group col-xs-4">
                    <label for="agent_email">Agent E-mail</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="agent_email" id="agent_email" value="<?php echo $docent->agent_email ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-envelope"></span></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="siteurl">Website URL</label>
                <div class="input-group">
                    <span class="input-group-addon">http://</span>
                    <input type="text" class="form-control" name="siteurl" id="siteurl" value="<?php echo $docent->siteurl?>" />
                </div>
            </div>


            <div class="form-group">
                <label for="bio">Bio</label>
                <textarea class="form-control" name="bio" id="bio" rows="5"><?php echo $docent->bio ?></textarea>
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
                    <label for="taxno">BSN / BTW / KVK</label>
                    <input type="text" class="form-control" name="taxno" id="taxno" value="<?php echo $docent->taxno ?>" />
                </div>
                <div class="form-group col-xs-6">
                    <label for="taxdocument">Tax Document</label>
                    <div class="clearfix"></div>
                    <input class="pull-left" type="file" id="taxdocument" name="taxdocument" />

                    <?php if ($docent->taxdocument) { ?>
                        <a class="pull-right" target="_blank" href="<?php echo URL.$docent->taxdocument ?>">
                            <button type="button" class="btn btn-default">Preview</button>
                        </a>
                        <span class="pull-right">&nbsp;</span>
                        <a class="pull-right" href="<?php echo $pluginURL.'?act=docentremovetaxdocument&amp;id='.$docent->id ?>">
                            <button type="button" class="btn btn-default">Remove</button>
                        </a>
                    <?php } ?>

                </div>
            </div>

            <div class="row">
                <div class="form-group col-xs-6">
                    <label for="fee">Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="fee" id="fee" value="<?php echo money_format(MONEY_NOCURRENCY,$docent->fee) ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label for="travelfee">Travel Fee</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="travelfee" id="travelfee" value="<?php echo money_format(MONEY_NOCURRENCY,$docent->travelfee) ?>" />
                        <span class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="additionalinfo">Additional Info</label>
                <textarea class="form-control" name="additionalinfo" id="additionalinfo" rows="5"><?php echo $docent->additionalinfo ?></textarea>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-default navbar-fixed-bottom">
        <div class="container-fluid">
            <a class="btn btn-link navbar-btn navbar-right" href="<?php echo $pluginURL?>?act=docentslist" role="button">Cancel</a>
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