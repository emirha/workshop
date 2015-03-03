<?php
/* @var $row CRUD */
/* @var $buttons BootstrapButton[] */
?>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <?php foreach ($buttons as $button) { ?>
            <a class="btn navbar-btn <?php echo $button->extraClass ?>" href="<?php echo $pluginURL.'?act='.$button->act?>" role="button"><?php echo $button->label?></a>
        <?php } ?>
    </div>
</nav>


<div class="table-responsive">
    <table class="table table-striped table-hover">
        <tr>
            <th width="30">#</th>
            <?php foreach ($headerColumns as $headerColumn) { ?>
                <?php if ($headerColumn instanceof TableHeader) { ?>
                    <th <?php echo $headerColumn->width ? 'width="'.$headerColumn->width.'"' : '' ?>><?php echo $headerColumn->content ? $headerColumn->content : '' ?></th>
                <?php } else { ?>
                    <th><?php echo $headerColumn ?></th>
                <?php } ?>
            <?php } ?>
        </tr>

        <?php foreach ($data as $row) { ?>
            <tr <?php echo $row->rowClass() ? 'class="'.$row->rowClass().'"' : '' ?>>
                <td><?php echo $row->id ?></td>
                <?php foreach ($row->columns() as $dataColumn) { ?>
                    <?php
                    if (empty($dataColumn)) {
                        echo '<td></td>';
                        continue;
                    }
                    if ($dataColumn instanceof Column) {
                        echo '<td class="'.$dataColumn->getClass().'">'.$dataColumn->toString($baseURL).'</td>';
                        continue;
                    }

                    if ($dataColumn instanceof BootstrapButton) {
                        echo '<td>'.$dataColumn->toString($baseURL).'</td>';
                        continue;
                    }

                    echo '<td>'.$row->$dataColumn.'</td>';
                    ?>
                <?php } ?>
            </tr>
        <?php } ?>

    </table>
</div>