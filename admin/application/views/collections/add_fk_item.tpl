<div class='tab-item-add span7'>
<?php
    foreach ($fields as $field) {
        if ($field['name'] == $fk_name)
            continue ;

        add_input_row($field, $settings);
    }
?>
    <button type='button' class='close tab-item-delete' data-dismiss="alert">&times;</button>
</div>