
var file_inputs = {};

$(document).ready(function() {

    if ($("a.gallery_view")[0]) {

        $("a.gallery_view").click(function(e) {
            e.preventDefault();
        });

        $("a.gallery_view").fancybox({
                        beforeShow: function () {
                            // Disable right click
                            $.fancybox.wrap.bind("contextmenu", function (e) {
                                    return false;
                            });
                        },
                        fitToView   : true,
                        width       : '900',
                        height      : 'auto',
                        autoSize    : false,
                        closeClick  : false,
                        openEffect  : 'none',
                        closeEffect : 'none'
                    });
    }

    if ($("#tabs-holder")[0] && $("#tabs-holder").length > 0) {
        $("#tabs-holder a").click(function() {
            if ($(this).parent().hasClass("active"))
                return ;

            $("#tabs-holder li").removeClass("active");
            $(this).parent().addClass("active");

            $("#tabs-content-holder .tab-content").hide();
            $("#"+ $(this).attr("activate")).show();
        });
    }

    // clicking a date input
    $("input.with-datepicker").datepicker({ dateFormat: "yy-mm-dd" });

    // clicking a datetime input
    $("input.with-datetimepicker").datetimepicker({ dateFormat: "yy-mm-dd" });

    // clicking a multiple upload btn
    // ;

    $(".fancy-add-collection").fancybox();

    $(".positions-holder a").click(function(e) {

        e.preventDefault();

        $nr = $(this).attr("data-pos");

        if ($(this).hasClass("active")) {
            $(".positions-holder a.pos-"+ $nr).removeClass("active");
            $("#tab_18_p-"+ $nr).remove();
        }
        else {
            $(".positions-holder a.pos-"+ $nr).addClass("active");
            $input = $("<input type='hidden' id='tab_18_p-"+ $nr +"' name='tabs[18][fk_"+ $nr +"][position]' value='"+ $nr +"' />");
            $("#selected_positions").append($input);
        }
    });

    $("input.file_input").each(function() {
        file_inputs[$(this).attr("name")] = $(this).val();
    });
});

function toogle_check(elem) {

    if ($(elem).is(":checked")) {
        $(elem).parent().parent().find("input[type=checkbox]").each(function() {
            $(this).prop('checked', true);
        });
    }
    else {
        // $(elem).parent().parent().find("input[type=checkbox]").attr("checked", false);
        $(elem).parent().parent().find("input[type=checkbox]").each(function() {
            $(this).prop('checked', false);
        });
    }
}

var tabs = [];
function add_tab_item(elem_index, cid, fk_name, fk_id, fields) {

    tabs[elem_index] += 1;
    $.get('ajax/get_tab_item', {cid: cid, fk_name: fk_name, fk_id: fk_id, elem_index: tabs[elem_index], fields: fields},
        function(data) {
            $("#add-fk-"+ elem_index).parent().after(data);

            $("#tab-"+ elem_index +" .tab-item-add").eq(0).find("input.with-datepicker").datepicker({ dateFormat: "yy-mm-dd" });
            $("#tab-"+ elem_index +" .tab-item-add").eq(0).find("input.with-datetimepicker").datetimepicker({ dateFormat: "yy-mm-dd" });
    });
}

function alert_delete(title) {

    if(window.confirm("Are you sure you wish to delete the record ? ["+ title +"]")) {
        return true;
    };

    return false;
}

function image_upload(id) {

    $id = id;

    $upload_input = $("input#" + $id);
    $progress_bar = $upload_input.siblings(".progress").children(".bar");
    $thumb = $upload_input.parent().parent().find(".file-thumb");
    $text_input = $upload_input.siblings("input[type=text]");

    $upload_input.fileupload({
        dataType: 'json',
        done: function (e, data) {
            if (data.result.error && data.result.error.length > 0) {
                alert(data.result.error);
                $progress_bar.css({ width: '0%' });
                return ;
            }
            var file = data.result.files[0];
            $thumb.attr("src", file['thumbnail_url']);
            $text_input.val(file['path'] + file['name']);
            $text_input.siblings("input.fch_switch").val("Yes");
            file_inputs[$text_input.attr("name")] = $text_input.val();
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $progress_bar.css(
                'width',
                progress + '%'
            );
        }
    });

    $upload_input.click();
}

function upload_multiple(elem, elem_index, cid, fk_name, fk_id) {

    $upload_input = $(elem);

    $progress_bar = $upload_input.parent().siblings(".fileupload-progress").children(".bar");
    //$thumb = $upload_input.parent().parent().find(".file-thumb");
    //$text_input = $upload_input.siblings("input[type=text]");

    $upload_input.fileupload({
        url: $upload_input.attr("data-url"),
        dataType: 'json',
        done: function (e, data) {
            var fields = [];
            $.each(data.result.files, function (index, file) {
                fields[index] = { 'name' : file['field_name'], 'value' : file['path'] + file['name'], 'wa_thumb' : file['thumbnail_url']};
                add_tab_item(elem_index, cid, fk_name, fk_id, fields);
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $progress_bar.css(
                'width',
                progress + '%'
            );
        }
    });
}

function update_file_input(elem) {

    if (file_inputs[elem.attr("name")] != elem.val()) {
        file_inputs[elem.attr("name")] = elem.val();
        $("input[name="+ elem.attr("name") +"]").siblings(".fch_switch").val("No");
    }
}

function check_title_plain() {
    var find = ['ă', 'î', 'ș', 'ț', 'â', 'Ă', 'Î', 'Ș', 'Ț', 'Â', 'ş', 'Ş', 'ţ', 'Ţ', 'ǎ', 'Ǎ'];
    var replace = ['a', 'i', 's', 't', 'a', 'A', 'I', 'S', 'T', 'A', 's', 'S', 't', 'T', 'a', 'A' ];
    $("textarea#title").change(function() {
        var val = $(this).val();

        var result = chr = '';
        for (var i = 0; i < val.length; i++) {
            chr = val[i];
            for (var j = 0; j < find.length; j++)
                if (val[i] == find[j]) {
                    chr = replace[j];
                }

            result += chr;
        }

        $("input#title_plain").val(result);
    });
}

function check_article_category() {

    $.post("ajax/get_editorialist_div", function(data) {
        $("#category_id").parent().parent().after(data);
        if ($("#category_id :input").val() == 3)
            $("#editorialist_holder").show();
    });

    $("#category_id :input").change(function() {
        if ($(this).val() == 3)
            $("#editorialist_holder").show();
        else
            $("#editorialist_holder").hide();
    });
}

function check_link_field() {

    var update = '';
    if ($("#title")[0]) {
        if ($("#link")[0])
            update = 'link';
        else if ($("#url")[0])
            update = 'url';

    }

    if (update.length > 0) {
        $("input#title").blur(function() {
            var nice_title = nice_url($(this).val());

            $("input#"+ update).val("/"+ nice_title);
        });
    }
}

function nice_url(val) {

    var find = [' ', 'ă', 'î', 'ș', 'ț', 'â', 'Ă', 'Î', 'Ș', 'Ț', 'Â', 'ş', 'Ş', 'ţ', 'Ţ', 'ǎ', 'Ǎ'];
    var replace = ['-', 'a', 'i', 's', 't', 'a', 'A', 'I', 'S', 'T', 'A', 's', 'S', 't', 'T', 'a', 'A' ];

    var result = chr = '';
    for (var i = 0; i < val.length; i++) {
        chr = val[i];
        for (var j = 0; j < find.length; j++)
            if (val[i] == find[j]) {
                chr = replace[j];
            }

        result += chr;
    }

    result = result.replace(/[^a-z0-9-]/gi, '');

    return result.toLowerCase();
}