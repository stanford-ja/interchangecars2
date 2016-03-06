<?php if($_SERVER['SERVER_NAME'] == "localhost"){ ?>
<div id="d<?php echo $line; ?>" style="z-index:20; position: absolute; bottom: 0px; left: 0px; display: fixed; width: 95%; border:1px solid #990000; background-color: yellow; padding: 5px;">
<span style="float: right;"><a href="javascript:{}" onclick="document.getElementById('d<?php echo $line; ?>').style.display = 'none';">[ Hide ]</a></span>
<h4>A PHP Error was encountered</h4>

Severity: <?php echo $severity; ?><br />
Message:  <?php echo $message; ?><br />
Filename: <?php echo $filepath; ?><br />
Line Number: <?php echo $line; ?><br />

</div>
<?php } ?>
