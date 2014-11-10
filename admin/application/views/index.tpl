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
        <div class='row-fluid'>
            <div class='span2 well'>
                <ul class='nav nav-list main-nav'>
                <?php
                    $category = "";
                    foreach ($collections as $item) :
                        if ($category != $item['category']) {
                            $category = $item['category'];
                            echo "<li class='nav-header'>$category</li>";
                        }

                        $link = ($item['custom_controller'] ? $item['custom_controller'] : "collections") . "/view/". $item['id'];

                        if ($item['name'] == 'editions') {
                            echo "
                    <li". ($selected_id == "editia_tiparita" ? " class='active'" : "") ."><a href='editions/view_slide'>Editia tiparita</a></li>
                    <li". ($selected_id == "andografia" ? " class='active'" : "") ."><a href='editions/view_ando'>Andografia zilei</a></li>";
                        }
                ?>
                    <li<?php echo ($selected_id == $item['id'] ? " class='active'" : ""); ?>>
                        <a href='<?php echo $link; ?>'><?php echo $item['caption']; ?></a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <div class='span10 col-right'>
            <?php echo $content; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>