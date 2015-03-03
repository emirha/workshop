<?php if (!empty($bootstrap)) { ?>
    <div class="container-fluid">
        <?php if (!empty($header_file)) { ?>
            <?php include LOCATION.'siteadmin/plugins/p_templates/'.$header_file; ?>
        <?php }

        if (isset($include_file)) {
            if (!is_array($include_file)) {
                include LOCATION.'siteadmin/plugins/p_templates/'.$include_file;
            } else {
                foreach ($include_file as $file) {
                    include LOCATION.'siteadmin/plugins/p_templates/'.$file;
                }
            }
        } ?>
    </div>
<?php } else { ?>

    <table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td width="8" height="29" class="td_head_main_left">&nbsp;</td>
            <td height="29" class="td_head_main_top">
                <?php echo $pluginName ?>
                <?php if ($helppageID) { ?>
                    | <a href="http://conxsys.nl/page/help/<?php echo $helppageID ?>" rel="shadowbox; player=iframe">HELP</a>
                <?php } ?>
            </td>
            <td width="8" height="29"  class="td_head_main_right">&nbsp;</td>
        </tr>
        <?php if (!isset($hideHeader) || $hideHeader == true) {?>
            <tr>
                <td class="td_sub_left">&nbsp;</td>
                <td class="td_sub_middle" height="40" valign="middle">
                    <?php if (isset($header_file_left)) { ?>
                        <div style="float:left; padding-left:10px">
                            <?php include LOCATION.'siteadmin/plugins/p_templates/'.$header_file_left; ?>
                        </div>
                    <?php } ?>

                    <?php if (isset($header_file_right)) { ?>
                        <div style="float:right; padding-right:10px">
                            <?php include LOCATION.'siteadmin/plugins/p_templates/'.$header_file_right; ?>
                        </div>
                    <?php } ?>

                </td>
                <td class="td_sub_right">&nbsp;</td>
            </tr>
        <?php } ?>
    </table>

    <table width="98%" border="0" cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td colspan="2">
                <div style="border:1px solid #999999; border-top:none; min-height:100px">
                    <div style="padding:10px;">
                        <div>&nbsp;</div>
                        <?php
                        if (isset($include_file)) {
                            if (!is_array($include_file)) {
                                include LOCATION.'siteadmin/plugins/p_templates/'.$include_file;
                            } else {
                                foreach ($include_file as $file) {
                                    include LOCATION.'siteadmin/plugins/p_templates/'.$file;
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>

<?php } ?>