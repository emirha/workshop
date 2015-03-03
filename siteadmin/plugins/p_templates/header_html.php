<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <?php if (!empty($bootstrap)) { ?>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
    <?php } ?>
    <style type='text/css' media='all'>@import url('<?php echo URL ?>siteadmin/index.css');</style>

    <?php
    if (!empty($extraCSS)) {
        foreach ($extraCSS as $css) {
            if (substr($css,0,5) == 'http:') { ?>
                <style type='text/css' media='all'>@import url('<?php echo $css ?>');</style>
            <?php } else { ?>
                <style type='text/css' media='all'>@import url('<?php echo URL.'includes/'.$css ?>');</style>
            <?php }
        }
    } ?>

    <?php if (!empty($bootstrap)) { ?>
        <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <?php } ?>

    <?php
    if (!empty($extraJS)) {
        foreach ($extraJS as $js) {
            if ($js != '') { ?>
                <script type="text/javascript" src="<?php echo URL.'js/'.$js ?>"></script>
            <?php  }
        }
    } ?>


    <?php if ($extraJSCode) { ?>
        <script type="text/javascript" language="javascript">
            <?php echo $extraJSCode ?>
        </script>
    <?php } ?>
</head>
<?php
?>
<body class="<?php echo Display::get('extraBodyCSS') ?>">