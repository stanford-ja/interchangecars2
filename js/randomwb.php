<?php 
?>
	function autoComp(a,b,c,d){
		document.getElementById(d+'_span').style.display = 'block';		
		a = a.toUpperCase();
		var fnd = '';
		
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			//alert('got here');
			var ad = a.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var bd = b.replace("&","[AMP]");
			var cd = c.replace("&","[AMP]");
			var dd = d.replace("&","[AMP]");
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=autoComp&a=" + ad + "&b=" + bd + "&c=" + cd + "&d=" + dd;
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){
					document.getElementById(d+'_span').innerHTML = fnd;
					document.getElementById(d+'_span').style.display = 'block';
				}
			});
			<?php } ?>
		});
	}

	function industAutoComp(a,b,c,d,e){
		document.getElementById(d+'_span').style.display = 'block';		
		a = a.toUpperCase();
		var fnd = '';

		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			var ad = a.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var bd = b.replace("&","[AMP]");
			var cd = c.replace("&","[AMP]");
			var dd = d.replace("&","[AMP]");
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=industAutoComp&a=" + ad + "&b=" + bd + "&c=" + cd + "&d=" + dd + "&e=" + e;
			//alert(p);
			$.get(p,function(data){
				fnd = data;
				if(fnd.length > 0){
					document.getElementById(d+'_span').innerHTML = fnd;
					document.getElementById(d+'_span').style.display = 'block';
				}
			});
			<?php } ?>
		});
	}
	
		
	$(document).ready(function(){
	});
	
	window.onload = function(){
	} 
