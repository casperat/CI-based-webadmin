<div class='navbar'>
    <div class='navbar-inner'>
        <a class='brand'><?php echo $caption; ?></a>
        <span class='divider-vertical'></span>
        <ul class='nav'>
            <li>
                <a href='<?php echo $controller; ?>/add/<?php echo $id; ?>'><i class='icon-plus'></i> Add record</a>
            </li>
            <?php if (isset($_GET['q'])) : ?>

            <li>
                <a href='<?php echo $controller; ?>/view/<?php echo $id; ?>'><i class='icon-remove'></i> Clear search results</a>
            </li>

            <?php endif; ?>
        </ul>
        <form class="navbar-search pull-right" method="get" action="<?php echo $controller ."/view/". $id; ?>">
            <input type="text" name="q" class="search-query" placeholder='Search...'>
            <!--<button type="submit" class="btn">Search</button>-->
        </form>
    </div>
</div>
<table class='table collection-list table-bordered table-hover table-condensed'>
    <tr>
        <th>#</th>

    <?php
        $cols = array();
        foreach ($fields as $item) :
            if ($item['relevant'] == 'Yes') :
                $cols[] = $item['name'];

            $order_by = (strlen($order_by) ? $order_by : $name_equiv_for_pk);
    ?>

        <th><?php echo ($item['name'] == $order_by ? "<i class='icon-chevron-". ($order_how == 'asc' ? "up" : "down") ."'></i> " : "") . $item['caption']; ?></th>

    <?php
            endif;
        endforeach;
    ?>
        <th class='actions' colspan='2'>Actions</th>

    </tr>

    <?php
        if (count($records)) :
            $i = ($page - 1) * $per_page;
            foreach ($records as $item) :
                $i++;
    ?>

    <tr>
        <td><?php echo $i; ?>.</td>

    <?php
        foreach ($cols as $col) :
            $cell = strip_tags($item[$col]);
            if (strlen($cell) > 200)
                $cell = substr($cell, 0, 200)."[...]";
    ?>

        <td><span<?php echo (($col == $name_equiv_for_pk) && ((int) $item['depth'] > 0) ? " style='padding-left: ". ($item['depth'] * 15) ."px;'" : ""); ?>><?php echo $cell; ?></span></td>

    <?php endforeach; ?>

        <td class='actions'><a href='<?php echo $controller; ?>/edit/<?php echo $id ."/". $item[$pk]; ?>'><i class='icon-edit'></i> <span>Edit</span></a></td>
        <td class='actions'><a href='<?php echo $controller; ?>/delete/<?php echo $id ."/". $item[$pk]; ?>' onclick='return alert_delete("<?php echo $i .". ". strip_tags($item[$name_equiv_for_pk]); ?>")'><i class='icon-trash'></i> <span>Delete</span></a></td>
    </tr>

    <?php
            endforeach;
        else :
    ?>

    <tr>
        <td colspan='<?php echo count($cols) + 3; ?>'><em>no records</em></td>
    </tr>

    <?php endif; ?>

</table>
<?php echo $pagination; ?>