<div class='ajax-pop'>

<?php

foreach ($fields as $field) {
    add_input_row($field);
}

?>

<button class="btn btn-primary" id="submit_aar" type="button">Save</button>
<button class="btn" id="cancel_aar" type="button">Cancel</button>

<script type='text/javascript'>
    $(".ajax-pop :input").each(function() {
        if ($(this).hasClass('with-datepicker')) {
            $(this).datepicker({ dateFormat: "yy-mm-dd" });
        }
    });

    $("#cancel_aar").click(function() { $.fancybox.close(); });

    $("#submit_aar").click(function() {

        var data = new Object();
        var fields = new Object();
        data['collection'] = '<?php echo $name; ?>';
        data['collection_id'] = '<?php echo $id; ?>';

        $(".ajax-pop :input").each(function() {
            if ($(this).attr("name") != undefined)
                fields[$(this).attr("name")] = $(this).val();
        });

        data['fields'] = fields;
        data['return_all'] = true;

        $("a.fancy-add-collection").each(function() {
            if ($(this).attr("data-collection-id") == '<?php echo $id; ?>') {
                $sibling = $(this).siblings("div").eq(0);
                data['select_name'] = $sibling.attr("id");
                return ;
            }
        });

        $.post("ajax/save_record", data, function(result) {
            $("#"+ data['select_name']).html(result);
            $.fancybox.close();
        });
    });
</script>

</div>