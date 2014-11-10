<body>
<div class='mainwrap'>
    <div class='row-fluid'>
        <div class='span6'><img src='img/logo.png' alt='logo' /></div>
        <div class='span6'>
            <div class="logged_user">
                <span>welcome, <?php echo $this->session->userdata("firstname"); ?> </span> |
                <a href="users/account"><i class='icon-user'></i> settings</a> |
                <a href="users/logout"><i class='icon-off'></i> logout</a>
            </div>
        </div>
    </div>
    <div class='container-fluid content'>
        <div class="navbar">
            <div class="navbar-inner">
                <a class="brand">Edit</a>
                <span class="divider-vertical"></span>
                <ul class="nav">
                    <li class='active'>
                        <a href="javascript://">Collections</a>
                    </li>
                    <li>
                        <a href="config/categories">Categories</a>
                    </li>
                </ul>
                <ul class='nav pull-right'>
                    <li class='divider-vertical'></li>
                    <li><a href='<?php echo site_url(); ?>'><i class='icon-home'></i> Admin home</a></li>
                </ul>
            </div>
        </div>
        <form name='collection-update' method='post'>
        <table class='table table-striped table-bordered table-hover table-condensed config-tables'>
            <tr>
                <th>Table name</th>
                <th>Caption</th>
                <th>PK</th>
                <th>Name equiv for PK</th>
                <th class='rpp'>RPP</th>
                <th>Category</th>
                <th class='rpp'>Order</th>
                <th>Visible</th>
                <th>Order by</th>
                <th>Order how</th>
                <th>Display Fields</th>
                <th>Custom controller</th>
                <th>Tree</th>
                <th>Tree parent</th>
            </tr>

            <?php foreach ($collections as $table) : ?>

            <tr>
                <td><a href='config/edit_table/<?php echo $table['id']; ?>'><?php echo $table['name']; ?></a></td>
                <td><input type='text' name='tables[<?php echo $table['id']; ?>][caption]' value='<?php echo $table['caption']; ?>' /></td>
                <td>
                    <select name='tables[<?php echo $table['id']; ?>][pk]'>

                    <?php foreach ($table['fields'] as $item) : ?>

                        <option value='<?php echo $item['name']; ?>'<?php echo ($table['pk'] == $item['name'] ? " selected='selected'" : ""); ?>><?php echo $item['name']; ?></option>

                    <?php endforeach ; ?>

                    </select>
                </td>
                <td>
                    <select name='tables[<?php echo $table['id']; ?>][name_equiv_for_pk]'>

                    <?php foreach ($table['fields'] as $item) : ?>

                        <option value='<?php echo $item['name']; ?>'<?php echo ($table['name_equiv_for_pk'] == $item['name'] ? " selected='selected'" : ""); ?>><?php echo $item['name']; ?></option>

                    <?php endforeach ; ?>

                    </select>
                </td>
                <td class='rpp'><input type='text' name='tables[<?php echo $table['id']; ?>][per_page]' value='<?php echo $table['per_page']; ?>' /></td>
                <td>
                    <select name='tables[<?php echo $table['id']; ?>][category_id]'>
                        <option value='0'>None</option>

                        <?php foreach ($categories as $category) : ?>

                        <option value='<?php echo $category['id']; ?>'<?php echo ($category['id'] == $table['category_id'] ? " selected='selected'" : ""); ?>><?php echo $category['name']; ?></option>

                        <?php endforeach; ?>

                    </select>
                </td>
                <td class='rpp'><input type='text' name='tables[<?php echo $table['id']; ?>][order]' value='<?php echo $table['order']; ?>' /></td>
                <td>
                    <label><input type='radio' name='tables[<?php echo $table['id']; ?>][visible]' value='Yes'<?php echo ($table['visible'] == 'Yes' ? " checked='checked'" : ""); ?> /> Yes</label>
                    <label><input type='radio' name='tables[<?php echo $table['id']; ?>][visible]' value='No'<?php echo ($table['visible'] == 'Yes' ? "" : " checked='checked'"); ?> /> No</label>
                </td>
                <td><input type='text' name='tables[<?php echo $table['id']; ?>][order_by]' value='<?php echo $table['order_by']; ?>' /></td>
                <td>
                    <label><input type='radio' name='tables[<?php echo $table['id']; ?>][order_how]' value='asc'<?php echo ($table['order_how'] == 'asc' ? " checked='checked'" : ""); ?> /> Asc</label>
                    <label><input type='radio' name='tables[<?php echo $table['id']; ?>][order_how]' value='desc'<?php echo ($table['order_how'] == 'asc' ? "" : " checked='checked'"); ?> /> Desc</label>
                </td>
                <td><input type='text' name='tables[<?php echo $table['id']; ?>][display_fields]' value='<?php echo $table['display_fields']; ?>' /></td>
                <td><input type='text' name='tables[<?php echo $table['id']; ?>][custom_controller]' value='<?php echo $table['custom_controller']; ?>' /></td>
                <td>
                    <label><input type='radio' name='tables[<?php echo $table['id']; ?>][tree]' value='Yes'<?php echo ($table['tree'] == 'Yes' ? " checked='checked'" : ""); ?> /> Yes</label>
                    <label><input type='radio' name='tables[<?php echo $table['id']; ?>][tree]' value='No'<?php echo ($table['tree'] == 'Yes' ? "" : " checked='checked'"); ?> /> No</label>
                </td>
                <td>
                    <select name='tables[<?php echo $table['id']; ?>][tree_parent]'>
                        <option value=''>None</option>

                    <?php foreach ($table['fields'] as $item) : ?>

                        <option value='<?php echo $item['name']; ?>'<?php echo ($table['tree_parent'] == $item['name'] ? " selected='selected'" : ""); ?>><?php echo $item['name']; ?></option>

                    <?php endforeach ; ?>

                    </select>
                </td>
            </tr>

            <?php endforeach ; ?>

            <tr><td colspan='15'><button class="btn" value="update" name="update" type="submit">Save</button></td></tr>
        </table>
        </form>
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Add table <span class="caret"></span></a>
            <ul class="dropdown-menu">

            <?php foreach ($add_tables as $table) : ?>

                <li><a href='config/add_table/<?php echo $table; ?>'><?php echo $table; ?></a></li>

            <?php endforeach ; ?>

            </ul>
        </div>
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Remove table <span class="caret"></span></a>
            <ul class="dropdown-menu">

            <?php foreach ($collections as $table) : ?>

                <li><a href='config/remove_table/<?php echo $table['id']; ?>'><?php echo $table['name']; ?></a></li>

            <?php endforeach ; ?>

            </ul>
        </div>
    </div>
</div>
</body>
</html>