<script language="javascript" type="text/javascript">
	//window.onload = (function() {
	  	// Start screen size checker / redirect
		//var scr0 = screen.width;
		//var scr1 = screen.height;

		<?php
		$this_pg = explode("/",$_SERVER['PHP_SELF']);
		if($this_pg[count($this_pg)-1] == "home"){
		?>
		//var pslf = '<?php echo $this_pg[count($this_pg)-1]; ?>';
		//if(scr0 < 900 || scr1 < 560){
		//	window.location = '<?php echo WEB_ROOT; ?>/index.php/home/small';
		//}
		<?php } ?>
		// End screen size checker / redirect
	//)};
	
	function toggleMenuType(m,r){
		// Updates menu type for user using AJAX
		// m = menu type digit
		// r = railroad id
		alert('got there 2');
		//$(document).ready(function(){
		//	var p = "<?php echo JS_ROOT; ?>/ajax.php?f=menutoggle&m=" + m + "&r=" + r;
		//	$.get(p,function(data){
				//fnd = data;
		//		alert('got there');
		//		window.location = '<?php echo $_SERVER['PHP_SELF']; ?>';
		//	});
		//});
		
	}
</script>
