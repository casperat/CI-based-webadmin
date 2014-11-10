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
                        <a href="javascript://"><?php echo $collection['caption']; ?></a>
                    </li>
                    <li>
                        <a href="config"><i class='icon-list-alt'></i> Collection list</a>
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
                <th>Name</th>
                <th>Type</th>
                <th>Caption</th>
                <th>Relevant</th>
                <th>Visible</th>
                <th>Read only</th>
                <th>Not NULL</th>

                <th>FK in</th>
                <th>HTML editor</th>
                <th>Default value</th>

                <th>Order</th>
                <th>Image path</th>
                <th>Max width</th>
                <th>Max height</th>
                <th>Is PK</th>
            </tr>

            <?php foreach ($collection['fields'] as $field) : ?>

            <tr>
                <td><?php echo $field['name']; ?></td>
                <td>
                    <select name='fields[<?php echo $field['id']; ?>][type]'>

                    <?php foreach ($field_types as $item) : ?>

                        <option value='<?php echo $item; ?>'<?php echo ($item == $field['type'] ? " selected='selected'" : ""); ?>><?php echo $item; ?></option>

                    <?php endforeach; ?>

                    </select>
                </td>
                <td>
                    <input type='text' name='fields[<?php echo $field['id']; ?>][caption]' value='<?php echo $field['caption']; ?>' />
                </td>
                <td class='rpp'>
                    <select name='fields[<?php echo $field['id']; ?>][relevant]'>
                        <option value='Yes'<?php echo ($field['relevant'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                        <option value='No'<?php echo ($field['relevant'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                    </select>
                </td>
                <td class='rpp'>
                    <select name='fields[<?php echo $field['id']; ?>][visible]'>
                        <option value='Yes'<?php echo ($field['visible'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                        <option value='No'<?php echo ($field['visible'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                    </select>
                </td>
                <td class='rpp'>
                    <select name='fields[<?php echo $field['id']; ?>][read_only]'>
                        <option value='Yes'<?php echo ($field['read_only'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                        <option value='No'<?php echo ($field['read_only'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                    </select>
                </td>
                <td class='rpp'>
                    <select name='fields[<?php echo $field['id']; ?>][not_null]'>
                        <option value='Yes'<?php echo ($field['not_null'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                        <option value='No'<?php echo ($field['not_null'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                    </select>
                </td>
                <td>
                    <select name='fields[<?php echo $field['id']; ?>][fk_in]'>
                        <option value='0'>None</option>

                    <?php foreach ($foreign_keys as $id => $key): ?>

                        <option value='<?php echo $id; ?>'<?php echo ($id == $field['fk_in'] ? " selected='selected'" : ""); ?>><?php echo $key; ?></option>

                    <?php endforeach; ?>

                    </select>
                </td>
                <td class='rpp'>
                    <select name='fields[<?php echo $field['id']; ?>][html_editor]'>
                        <option value='Yes'<?php echo ($field['html_editor'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                        <option value='No'<?php echo ($field['html_editor'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                    </select>
                </td>
                <td>
                    <input type='text' name='fields[<?php echo $field['id']; ?>][default_value]' value='<?php echo ($field['default_value']); ?>' />
                </td>
                <td class='rpp'>
                    <input type='text' name='fields[<?php echo $field['id']; ?>][order]' value='<?php echo ($field['order']); ?>' />
                </td>
                <td>
                    <input type='text' name='fields[<?php echo $field['id']; ?>][image_rel_path]' value='<?php echo ($field['image_rel_path']); ?>' />
                </td>
                <td class='rpp'>
                    <input type='text' name='fields[<?php echo $field['id']; ?>][max_width]' value='<?php echo ($field['max_width']); ?>' />
                </td>
                <td class='rpp'>
                    <input type='text' name='fields[<?php echo $field['id']; ?>][max_height]' value='<?php echo ($field['max_height']); ?>' />
                </td>
                <td class='rpp'>
                    <select name='fields[<?php echo $field['id']; ?>][is_pk]'>
                        <option value='Yes'<?php echo ($field['is_pk'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                        <option value='No'<?php echo ($field['is_pk'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                    </select>
                </td>
            </tr>

            <?php endforeach ; ?>

            <tr><td colspan='15'><button class="btn" value="update" name="update" type="submit">Save</button></td></tr>
        </table>
        </form>

        <h4>Collection Tabs</h4>
        <form name='add-tab' method='post' action='config/update_tabs'>
            <table class='table table-striped table-bordered table-hover table-condensed config-tables pull-left'>
                <tr>
                    <th>Collection</th>
                    <th>Caption</th>
                    <th>Parent PK</th>
                    <th>Tab FK</th>
                    <th>Multiple upload</th>
                    <th>Order</th>
                    <th>Del</th>
                </tr>

                <?php if (count($tabs) == 0) : ?>

                <tr>
                    <td colspan='7'><em>no records</em></td>
                </tr>

                <?php else : ?>
                <?php foreach ($tabs as $item) : ?>

                <tr>
                    <td>
                        <select name='tabs[<?php echo $item['id']; ?>][tab_id]'>

                        <?php foreach ($collections as $c_item) : ?>

                            <option value='<?php echo $c_item['id']; ?>'<?php echo ($c_item['id'] == $item['tab_id'] ? " selected='selected'" : ""); ?>><?php echo $c_item['name']; ?></option>

                        <?php endforeach; ?>

                        </select>
                    </td>
                    <td><input type='text' name='tabs[<?php echo $item['id']; ?>][caption]' value='<?php echo $item['caption']; ?>' /></td>
                    <td><input type='text' name='tabs[<?php echo $item['id']; ?>][parent_pk]' value='<?php echo $item['parent_pk']; ?>' /></td>
                    <td><input type='text' name='tabs[<?php echo $item['id']; ?>][tab_fk]' value='<?php echo $item['tab_fk']; ?>' /></td>
                    <td class='rpp'>
                        <select name='tabs[<?php echo $item['id']; ?>][multiple_upload]'>
                            <option value='Yes'<?php echo ($item['multiple_upload'] == 'Yes' ? " selected='selected'" : ""); ?>>Yes</option>
                            <option value='No'<?php echo ($item['multiple_upload'] == 'No' ? " selected='selected'" : ""); ?>>No</option>
                        </select>
                    </td>
                    <td class='rpp'><input type='text' name='tabs[<?php echo $item['id']; ?>][order]' value='<?php echo $item['order']; ?>' /></td>
                    <td class='rpp'><a href='config/delete_tab/<?php echo $item['id']; ?>'><i class='icon-remove'></i></a></td>
                </tr>

                <?php endforeach ; ?>
                <?php endif; ?>

                <tr>
                    <td colspan='7'>
                        <button class="btn" value="update" name="update-tabs" type="submit">Update</button>
                        <div class='pull-right form-add-new'>
                            <div class='pull-left'>
                                <label>Select Tab:
                                <select name='tab_id' placeholder='select collection' onchange='changeOptions("tab_fk", this.value)'>
                                    <?php
                                        $cid = 0;
                                        foreach ($collections as $c_item):
                                            if ($cid == 0)
                                                $cid = $c_item['id'];
                                    ?>

                                    <option value='<?php echo $c_item['id']; ?>'><?php echo $c_item['caption']; ?></option>

                                    <?php endforeach; ?>
                                </select></label>
                            </div>
                            <div class='pull-left'>
                                <label>Select FK:
                                <select name='tab_fk' id='tab_fk'>
                                <?php foreach ($fields as $col) : ?>

                                    <option value='<?php echo $col['name']; ?>' tid='<?php echo $col['id_table']; ?>'<?php echo ((int) $col['id_table'] !== (int) $cid ? " style='display: none;'" : "'"); ?>><?php echo $col['name']; ?></option>

                                <?php endforeach; ?>
                                </select></label>
                            </div>
                            <input type='hidden' name='parent_id' value='<?php echo $collection['id']; ?>' />
                            <input type='hidden' name='parent_pk' value='<?php echo $collection['pk']; ?>' />
                            <div class='input-append pull-left'>
                                <label>Tab Caption:
                                    <input type='text' name='caption' class='span2' /><button class='btn btn-small' type='submit' name='add-tab' value='true'>Add new</button>
                                </label>
                            </div>
                            <div class='clear'></div>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>
<script type='text/javascript'>

function changeOptions(tid, value) {
    $("#"+ tid +" option").hide();
    $("#"+ tid +" option[tid="+ value +"]").show();
}

</script>
</body>
</html>