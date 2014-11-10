<?php

function add_input_row($field, $settings = array()) {

    $prefix = $settings['prefix'];
    $suffix = $settings['suffix'];
    $id_prefix = $settings['id_prefix'];

    if ($field['visible'] == 'No') :
        return ;

    elseif ($field['read_only'] == 'Yes') :
?>

    <div class='control-group'>
        <label class="control-label"><?php echo $field['caption']; ?></label>
        <div class='controls'><?php echo $field['value']; ?></div>
    </div>

<?php
        return ;

    endif;
?>

    <div class='control-group'>
        <label class='control-label' for='<?php echo (in_array($field['type'], array('image', 'file')) ? "" : $id_prefix . $field['name']); ?>'><?php echo $field['caption']; ?></label>
        <div class='controls'>

<?php
        switch($field['type']) {
            case "text" :   echo "<input type='text' name='{$prefix}{$field['name']}{$suffix}' value='{$field['value']}' id='{$id_prefix}{$field['name']}' />";
                            break;

            case "textarea" : echo "<textarea name='{$prefix}{$field['name']}{$suffix}' id='{$id_prefix}{$field['name']}'>{$field['value']}</textarea>";
                            if ($field['html_editor'] == "Yes") {
            ?>
                <script type='text/javascript'>
                    var editor = CKEDITOR.replace( '<?php echo $id_prefix . $field['name']; ?>' );
                    CKFinder.setupCKEditor( editor, '/admin/ckfinder/' );
                </script>
            <?php
                            }
                            break;

            case "image" :
            case "file" :
            ?>

            <div class='file-upload'>
                <div class='fu-left'>
                    <img src='<?php echo (isset($field['wa_thumb']) ? $field['wa_thumb'] : "../files/na.gif"); ?>' alt='thumb' class='file-thumb img-polaroid' />
                    <a href='javascript://' class='btn btn-primary btn-mini btn-upload-image' onclick="image_upload('<?php echo $id_prefix . $field['name']; ?>')">Browse</a>
                </div>
                <div class='fu-right'>
                    <div class='progress progress-striped'>
                        <div class='bar' style='width: 0%'></div>
                    </div>
                    <input type='text' name='<?php echo $prefix . $field['name'] . $suffix; ?>' value='<?php echo $field['value']; ?>' class='file_input' onblur='update_file_input(this);' />
                    <input type='file' name='fu_<?php echo $id_prefix . $field['name']; ?>' class='fu-input' id='<?php echo $id_prefix . $field['name']; ?>' data-url='collections/upload/<?php echo $field['id']; ?>' tabindex="-1" />
                    <input type='hidden' name='<?php echo $prefix . $field['name'] ."_fs". $suffix; ?>' class='fch_switch' value='No' />
                </div>
                <div class='clearfix'></div>
            </div>

            <?php
                            break;

            case "enum" :   foreach ($field['values'] as $value) {
                                $value = trim($value);
                                echo "<label class='radio inline'><input type='radio' name='{$prefix}{$field['name']}{$suffix}' value='{$value}'". ($value == $field['value'] ? " checked='checked'" : "") ." />{$value}</label>";
                            }
                            break;

            case "checklist" : echo "<input type='hidden' name='". str_replace("__fk__", 'dummy', $prefix) . $field['name'] . $suffix ."' value='true' />";
                            echo "<div class='data-check-group'>
                                <label class='checkbox inline'><input type='checkbox' onclick='toogle_check(this);' />All</label>";
                            foreach ($field['values'] as $key => $item) {
                                $value = trim($item[$field['name_equiv']]);
                                $tmp_prefix = str_replace("__fk__", $key, $prefix);
                                echo "<label class='checkbox inline'><input type='checkbox' name='{$tmp_prefix}{$field['name']}{$suffix}' value='{$item[$field['pk']]}'". (substr($key, 0, 3) == "fk_" ? "" : " checked=''") ." />{$item[$field['name_equiv']]}</label>";
                            }
                            echo "</div>";
                            break;

            case "fk" :     echo "<div id='{$id_prefix}{$field['name']}' style='display: inline-block;'>
                            <select name='{$prefix}{$field['name']}{$suffix}'>";
                            if ($field['not_null'] == 'No')
                                echo "<option value=''>None</option>";
                            foreach ($field['values'] as $item)
                                echo "<option value='{$item[$field['fk_table']['pk']]}'". ($item[$field['fk_table']['pk']] == $field['value'] ? " selected='selected'" : "") . ($item['depth'] > 0 ? " style='padding-left: ". ($item['depth'] * 15) ."px;'" : "") .">{$item[$field['fk_table']['name_equiv_for_pk']]}</option>";

                            echo "</select>
                            </div>";

                            break;

            case "date" :   echo "<input type='text' name='{$prefix}{$field['name']}{$suffix}' value='{$field['value']}' class='with-datepicker' />";
                            break;

            case "datetime":echo "<input type='text' name='{$prefix}{$field['name']}{$suffix}' value='{$field['value']}' class='with-datetimepicker' />";
                            break;
        }

        if ($field['custom_links'])
            echo $field['custom_links'];

        if ($field['hint'])
            echo "<span class='hint'><i class='icon-info-sign'></i>{$field['hint']}</span>";

        echo "
        </div>
    </div>";
}

function move_upload_file($field, $file) {

    // move file to correct folder
    $path_parts = pathinfo($file);

    if (is_file('../'. $file)) {
        $k = 1;
        while(is_file('../'. $path_parts['dirname'] ."/". $path_parts['filename'] ."-". $k .".". $path_parts['extension']))
            $k++;
        $file = $path_parts['dirname'] .'/'. $path_parts['filename'] .'-'. $k .".". $path_parts['extension'];
    }

    @copy('../files/upload/temp/'. $path_parts['basename'], '../'. $file);
    unlink('../files/upload/temp/'. $path_parts['basename']);
    unlink('../files/upload/temp/thumbs75x75/'. $path_parts['basename']);

    return $file;
}

?>
