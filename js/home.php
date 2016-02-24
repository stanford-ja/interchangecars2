<?php
?>
<?php if($rr_sess > 0){ ?>
$(document).ready(function(){

	document.getElementById('pos').style.display = 'none';
	
	$("#po_expand").click(function(){
		$("#pos").slideDown("slow");
		document.getElementById('pos').style.display = 'block';
		document.getElementById('pos').style.width = '200px';
		return false;
	});

	$("#po_shrink").click(function(){
		$("#pos").slideUp("slow");
		return false;
	});

	document.getElementById('rrs').style.display = 'none';

	$("#rr_expand").click(function(){
		$("#rrs").slideDown("slow");
		document.getElementById('rrs').style.display = 'block';
		return false;
	});

	$("#rr_shrink").click(function(){
		$("#rrs").slideUp("slow");
		return false;
	});

	$("#tr_expand").click(function(){
		$("#trs").slideDown("slow");
		document.getElementById('trs').style.display = 'block';
		document.getElementById('trs').style.width = '250px';
		return false;
	});

	$("#tr_shrink").click(function(){
		$("#trs").slideUp("slow");
		return false;
	});

	$("#af_expand").click(function(){
		$("#afs").slideDown("slow");
		document.getElementById('afs').style.display = 'block';
		return false;
	});

	$("#af_shrink").click(function(){
		$("#afs").slideUp("slow");
		return false;
	});

	$("#me_expand").click(function(){
		$("#mes").slideDown("slow");
		document.getElementById('mes').style.display = 'block';
		return false;
	});

	$("#me_shrink").click(function(){
		$("#mes").slideUp("slow");
		return false;
	});

	$("#gl_expand").click(function(){
		$("#genl").slideDown("slow");
		document.getElementById('genl').style.display = 'block';
		return false;
	});

	$("#gl_shrink").click(function(){
		$("#genl").slideUp("slow");
		return false;
	});

});

	function home_update(f,tid,wid){
		// Updates train id (tid) for waybill (wid) using AJAX
		if(document.getElementById('do_bulk')){
			if(document.getElementById('do_bulk').checked == true){return;}
		}
		$(document).ready(function(){
			var trd = tid.replace("&","[AMP]");
			var wrd = wid;
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=" + f + "&w=" + wrd + "&t=" + trd;
			//alert('Handler for wb ' + wid + ' to change to train ' + tid + '\n' + p);
			$.get(p,function(data){
				//fnd = data;
				window.location = '<?php echo WEB_ROOT; ?>/index.php/home';
			});
		});
	}
	
	function gl_del(i){
		// i = generated load to delete
		$(document).ready(function(){
			var c = confirm('Are you sure you want to delete this Generated Load?');
			if(c){
				var p = "<?php echo JS_ROOT; ?>/ajax.php?f=glDel&i=" + i;
				$.get(p,function(data){
					//fnd = data;
					window.location = '<?php echo WEB_ROOT; ?>/index.php/home';
				});
			}
		});
	}
	
	function gl_cre(i){
		// i = generated load to convert to a waybill
		$(document).ready(function(){
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=glCreate&i=" + i;
			$.get(p,function(data){
				//fnd = data;
				window.location = '<?php echo WEB_ROOT; ?>/index.php/home';
			});
		});
	}
<?php } ?>
</script>
<script src="<?php echo JS_ROOT; ?>/masonry.pkgd.min.js"></script>
<script type="text/javascript">
