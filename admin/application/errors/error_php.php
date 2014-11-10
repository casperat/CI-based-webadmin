<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>A PHP Error was encountered</h4>

<p>Severity: <?php echo $severity; ?></p>
<p>Message:  <?php echo $message; ?></p>
<p>Filename: <?php echo $filepath; ?></p>
<p>Line Number: <?php echo $line; ?></p>

</div>
<?php
    $to = 'contact@alext.ro';
    $subject = 'BZB - '. $heading;
    $message = $severity ."<br />". $message ."<br />". $filepath ."<br />". line ."<br /><br />". $_SERVER['REQUEST_URI'];
    $headers = array(
                "From: contact@alext.ro",
                "Content-type: text/html; charset=utf-8");
    //@mail($to, $subject, $message, implode("\r\n", $headers));
?>