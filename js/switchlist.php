<?php 
$url_path = "http://www.stanfordhosting.net/interchangecars2/"; 
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$url_path = "http://localhost/Applications/interchangecars2/";
}
?>
<?php 
$f21 = "[{}]"; if(strlen(@$fld21) > 0 && json_decode(@$fld21, TRUE)){$f21 = $fld21;}
$f21 = str_replace("{\"AAR_REQD\":\"UNDEFINED\",\"NUM\":\"UNDEFINED\",\"AAR\":\"UNDEFINED\",\"RR\":\"UNDEFINED\"},","",$f21); 
?>
	
	function confirm_remove(){
		var v = confirm('Are you sure you want to remove\nthis waybill from the switchlist');
		return v;
	}
	
	function add2SW(id){
		// Adding a waybill to a switchlist.
		document.getElementById('add2SWLst').style.display = 'block';
		document.getElementById('loco_sel_div').style.display = 'none';

		$(document).ready(function(){
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=add2SW&s=" + id;
			$.get(p,function(data){
				document.getElementById('add2SWLst2').innerHTML = data;
			});
		});


	}
	
