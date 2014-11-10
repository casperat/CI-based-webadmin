<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?=$meta_title?></title>
    <base href='<?=base_url()?>' />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--<link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />-->

    <!-- JQUERY MAIN FILE AND COOKIES -->
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-cookie.js"></script>

    <!-- JQUERY FANCYBOX FILES -->
    <script type="text/javascript" src="js/fancybox/jquery.fancybox.pack.js?v=2.1.4"></script>
    <link rel="stylesheet" href="js/fancybox/jquery.fancybox.css?v=2.1.4" type="text/css" media="all" />

    <!-- TWITTER BOOTSTRAP FILES -->
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />

    <!-- CKEDITOR FILES -->
    <script type='text/javascript' src="ckeditor/ckeditor.js"><!-- --></script>
    <script type='text/javascript' src="ckfinder/ckfinder.js"><!-- --></script>

    <!-- JQUERY UI FILES -->
    <script type='text/javascript' src="js/jquery-ui/jquery-ui-1.10.2.custom.min.js"><!-- --></script>
    <link rel="stylesheet" href="js/jquery-ui/jquery-ui-1.10.2.custom.min.css" type='text/css' />
    <script type='text/javascript' src="js/jquery-ui/jquery-ui-timepicker.js"><!-- --></script>
    <link rel="stylesheet" href="js/jquery-ui/jquery-ui-timepicker.css" type='text/css' />

    <!-- JQUERY FILEUPLOADER FILES -->
    <link rel="stylesheet" href="css/jquery.fileupload-ui.css" type="text/css" />
    <script src="js/fileupload/vendor/jquery.ui.widget.js"></script>
    <script src="js/fileupload/jquery.iframe-transport.js"></script>
    <script src="js/fileupload/jquery.fileupload.js"></script>

    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <script type="text/javascript" src="js/scripts.js"></script>
<?
    if (isset($css_files) && count($css_files)) {
        foreach ($css_files as $item) {
?>
    <link rel="stylesheet" type='text/css' href="<?=$item?>" />
<?
        }
    }

    if (isset($js_files) && count($js_files)) {
        foreach ($js_files as $item) {
?>
    <script type='text/javascript' src="<?=$item?>"><!-- --></script>
<?
        }
    }

    if (isset($custom) && count($custom)) {
        foreach ($custom as $item)
            echo $item;
    }
?>
</head>