<div class='navbar'>
    <div class='navbar-inner'>
        <a class='brand'><?php echo $caption; ?></a>
        <span class='divider-vertical'></span>
        <ul class='nav'>
            <li<?php echo ($row_id > 0 ? "" : " class='active'"); ?>>
                <a href='<?php echo $controller; ?>/add/<?php echo $id; ?>'><i class='icon-plus'></i> Add record</a>
            </li>
            <li>
                <a href='<?php echo $controller; ?>/view/<?php echo $id; ?>'><i class='icon-list-alt'></i> View list</a>
            </li>

            <?php if (isset($_GET['q'])) : ?>

            <li>
                <a href='<?php echo $controller; ?>/view/<?php echo $id; ?>'><i class='icon-remove'></i> Clear search results</a>
            </li>

            <?php endif; ?>
        </ul>
        <form class="navbar-search pull-right" method="get" action="<?php echo $controller ."/view/". $id; ?>">
            <input type="text" name="q" class="search-query" placeholder='Search...'>
        </form>
    </div>
</div>
<ul class="nav nav-tabs" id='tabs-holder'>
    <li class="active"><a href="javascript://" activate='tab-0'>General</a></li>

    <?php foreach ($tabs as $key => $tab) : ?>

    <li><a href="javascript://" activate='tab-<?php echo $key + 1; ?>'><?php echo $tab['caption']; ?></a></li>

    <?php endforeach; ?>
</ul>
<form name='add-record' class='form-horizontal' method='post' action='<?php echo $form_action; ?>'>
<div class='add-content-holder' id='tabs-content-holder'>
    <div id='tab-0' class='tab-content'>

    <?php
        foreach ($fields as $field)
            add_input_row($field);
    ?>

    </div>

    <?php foreach ($tabs as $key => $tab) : ?>

    <div id='tab-<?php echo $key + 1; ?>' class='tab-content' style='display: none;'>
        <input type='hidden' name='tabs[<?php echo $tab['id']; ?>][dummy][]' value='true' />

        <?php
            if ($tab['multiple_upload'] == 'Yes') :
                $field_id = 0;
                foreach ($tab['fields'] as $field)
                    if ($field['type'] == 'image') {
                        $field_id = $field['id'];
                        break;
                    }
        ?>

        <div class='upload-multiple pull-right span5'>
            <div class='header'><i class='icon-plus-sign'></i> Upload multiple files</div>
            <div id='multiple-<?php echo $tab['id']; ?>'>

                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button btn-small">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <!-- The file input field used as target for the file upload widget -->
                    <input type="file" multiple="" name="tab<?php echo $tab['id']; ?>_files[]" class='multiple-upload' data-url='<?php echo $controller; ?>/upload_multiple/<?php echo $field_id; ?>' tabindex="-1" onclick="upload_multiple(this, '<?php echo ($key + 1); ?>', '<?php echo $tab['id']; ?>', '<?php echo $tab['tab_fk']; ?>', '<?php echo $tab['parent_id']; ?>')" />
                </span>
                <br>
                <br>
                <!-- The global progress bar -->
                <div class="progress progress-striped fileupload-progress">
                    <div class="bar"></div>
                </div>
            </div>

            <div id='files'></div>
        </div>

        <?php endif; ?>

        <script type='text/javascript'>
            tabs['<?php echo $key + 1?>'] = 0;
        </script>
        <div class='pull-left span7' style='margin-left: 0px;'>
            <a href='javascript://' class='add-fk-collection' id='add-fk-<?php echo ($key + 1); ?>' onclick="add_tab_item('<?php echo ($key + 1); ?>', '<?php echo $tab['id']; ?>', '<?php echo $tab['tab_fk']; ?>', '<?php echo $tab['parent_id']; ?>');"><i class='icon-plus'></i> Add record</a>
        </div>

        <?php
            if (count($tab['records'])) :
                foreach ($tab['records'] as $item) :
                    $settings = array(
                                "prefix" => "tabs[{$tab['id']}][{$item[$tab['pk']]}][",
                                "suffix" => "]",
                                "id_prefix" => "tab". $tab['id'] ."_". $item[$tab['pk']] ."_");
        ?>

        <div class='tab-item-add span7'>

        <?php
                    foreach ($tab['fields'] as $field) {
                        if ($field['name'] == $tab['tab_fk'])
                            continue ;
                        $field['value'] = ($field['type'] == 'fk' ? $item['select_id'] : $item[$field['name']]);

                        if ($item['wa_thumb'])
                            $field['wa_thumb'] = $item['wa_thumb'];

                        add_input_row($field, $settings);
                    }
        ?>

            <button type='button' class='close tab-item-delete' data-dismiss="alert">&times;</button>
        </div>

        <?php
                endforeach;
            endif;
        ?>

    </div>

    <?php endforeach; ?>

</div>
<br />
<button type='submit' name='save' value='yes' class='btn'>Save</button>
<button type='submit' name='save_add' value='yes' class='btn'>Save and add another</button>
<button type='submit' name='save_ret' value='yes' class='btn'>Save and return to list</button>
</form>
<script type='text/javascript'>check_link_field();</script>