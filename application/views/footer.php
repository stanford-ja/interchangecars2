<?php
// Footer view
?>
<hr style="color: #777;" />
<?php if(isset($_COOKIE['rr_sess']) && $_COOKIE['rr_sess'] > 0 && file_exists(".git/config")){
	echo "<span style=\"font-size: 10pt; color: red; float: right;\">GIT REPO available! To clone: <strong>git clone https://github.com/stanford-ja/interchangecars2.git</strong></span>";
} ?>
<span style="color: #777">&copy; <?php echo date('Y'); ?> J. Stanford.</span>
</body>
	<?php if(strpos($_SERVER['REQUEST_URI'],"waybill/edit") < 1){ ?>
	<script type="text/javascript">
		//var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		//document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		//try {
		//var pageTracker = _gat._getTracker("UA-10404441-1");
		//pageTracker._trackPageview();
		//} catch(err) {}
	</script>
	<script type="text/javascript">
	  //window.onload = (function() {
	   // var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	   // po.src = 'https://apis.google.com/js/plusone.js';
	   // var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  //})();
	</script>
<?php } ?>
</html>
