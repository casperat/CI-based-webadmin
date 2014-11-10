<body>
<?php echo $header; ?>
<div class='content'>
    <h1>Edit Tables</h1>
    <table class='table table-striped table-bordered'>
        <tr>
            <th>Table name</th>
            <th>Caption</th>
            <th>PK</th>
            <th>Name equiv for PK</th>
            <th>RPP</th>
            <th>Order</th>
        </tr>

        <?php foreach ($tables as $table) : ?>

        <tr>
            <td><a href='config/table/<?php echo $table['id']; ?>'><?php echo $table['name']; ?></a></td>
            <td><input type='text' name='caption' value='<?php echo $table['caption']; ?>' /></td>
            <td>
                <select name='pk'>

                <?php foreach ($table['fields'] as $item) : ?>

                    <option value='<?php echo $item['id']; ?>'><?php echo $item['name']; ?></option>

                <?php endforeach ; ?>

                </select>
            </td>
            <td>
                <select name='name_equiv_for_pk'>

                <?php foreach ($table['fields'] as $item) : ?>

                    <option value='<?php echo $item['id']; ?>'><?php echo $item['name']; ?></option>

                <?php endforeach ; ?>

                </select>
            </td>
            <td><input type='text' name='rpp' value='<?php echo $table['rpp']; ?>' /></td>
            <td><input type='text' name='order' value='<?php echo $table['order']; ?>' /></td>
        </tr>

        <?php endforeach ; ?>

    </table>
</div>
</body>