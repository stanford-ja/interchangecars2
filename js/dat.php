<?php 
$url_path = "http://www.stanfordhosting.net/interchangecars2/"; 
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$url_path = "http://localhost/Applications/interchangecars2/";
}
?>

	function disp_info(){
		var tbl = document.getElementById('tbl');
		var wb_info = document.getElementById('wb_info');
		var tr_info = document.getElementById('tr_info'); 
		var ca_info = document.getElementById('ca_info');
		wb_info.style.display = 'none';		
		tr_info.style.display = 'none';		
		ca_info.style.display = 'none';
		
		if(tbl.value == "ichange_waybill"){wb_info.style.display = 'block';}		
		if(tbl.value == "ichange_trains"){tr_info.style.display = 'block';}		
		if(tbl.value == "ichange_cars"){ca_info.style.display = 'block';}		
	}
