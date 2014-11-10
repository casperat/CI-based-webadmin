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
                    <li>
                        <a href="config">Collections</a>
                    </li>
                    <li class='active'>
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
                <th>Category name</th>
                <th>Order</th>
            </tr>

            <?php foreach ($categories as $item) : ?>

            <tr>
                <td><input type='text' name='categs[<?php echo $item['id']; ?>][name]' value='<?php echo $item['name']; ?>' /></td>
                <td class='rpp'><input type='text' name='categs[<?php echo $item['id']; ?>][order]' value='<?php echo $item['order']; ?>' /></td>
            </tr>

            <?php endforeach ; ?>

            <tr><td colspan='15'><button class="btn" value="update" name="save" type="submit">Save</button></td></tr>
        </table>
        </form>
        <form name='add-categ' method="post" action='config/add_category' class='pull-left' style='margin: 0px 15px 0px 0px;'>
        <div class="input-append">
            <input type="text" name='category' class="span2" id="appendedInputButtons" />
            <button class="btn" type="submit">Add</button>
        </div>
        </form>
        <div class="btn-group">
            <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Remove category <span class="caret"></span></a>
            <ul class="dropdown-menu">

            <?php foreach ($categories as $item) : ?>

                <li><a href='config/remove_category/<?php echo $item['id']; ?>'><?php echo $item['name']; ?></a></li>

            <?php endforeach ; ?>

            </ul>
        </div>
    </div>
</div>
</body>
</html>