<?php
// Script file for class with same name as file name (ucwords)
$url_path = "http://".$_SERVER['SERVER_NAME']."/apps/interchangecars2/"; 
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$url_path = "http://localhost/Applications/interchangecars2/";
}
?>
		function expandDiv(d){
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			var ad = d.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var p = "<?php echo $url_path; ?>js/ajax.php?f=mapDetails&s=" + ad;
			$.get(p,function(data){
				fnd = data;
				if(fnd.length > 0){
					var spl_d = d.split("-"); 
					var dd = document.getElementById(d);
					dd.innerHTML = '<a href="javascript:{}" onclick="expandDiv(\'' + d + '\')" style="color: #777; text-decoration: none;">' + spl_d[0] + '</a><br />';
					dd.innerHTML += '<span style="font-size:9pt;"' + fnd + '</span>';
					dd.style.zIndex = 20;
					dd.style.width = '180px';
					dd.style.overflow = 'auto';
					dd.style.height = 'auto';
					dd.style.maxHeight = '200px';
					dd.style.border = '1px solid blue';
					dd.style.backgroundColor = 'lightskyblue';
					dd.innerHTML += '<br /><a href="javascript:{}" onclick="collapseDiv(\'' + d + '\');" style="color: black; text-decoration: none\">Close</a><br />';
				}
			});
			<?php } ?>
		});

		}
		
		function collapseDiv(d){
			var spl_d = d.split("-"); 
			var dd = document.getElementById(d);
			dd.style.zIndex = 10;
			dd.style.width = 'auto';
			dd.style.overflow = 'hidden';
			dd.style.height = '20px';
			dd.style.border = 'none';
			dd.style.backgroundColor = 'transparent';
			dd.innerHTML = '<a href="javascript:{}" onclick="expandDiv(\'' + d + '\')" style="color: #444; text-decoration: none;">' + spl_d[0] + '</a>';
		}

		function wbDetails(w){
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			var ad = w.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var p = "<?php echo $url_path; ?>js/ajax.php?f=mapWBDetails&w=" + ad;
			$.get(p,function(data){
				fnd = data;
				if(fnd.length > 0){
					var dd = document.getElementById('wb_details');
					dd.innerHTML = '<span style="font-size:10pt;"><span style="float: right;"><a href="javascript:{}" onclick="document.getElementById(\'wb_details\').innerHTML = \'\'; document.getElementById(\'wb_details\').style.display = \'none\';">Close</a></span>' + fnd + '</span>';
					dd.style.display = 'block';
				}
			});
			<?php } ?>
		});
		
		}

		// OpenMAP convert address to latLon
		function getLat(a){
		var z = 0;
		//$(document).ready(function(){
			// format can be json, xml, html
			// See : http://wiki.openstreetmap.org/wiki/Nominatim
			var p = "http://nominatim.openstreetmap.org/search?q="+a+"&format=json&polygon=1&addressdetails=1";
			z = $.get(p,function(data){		
				fnd = data;
				z = fnd[0].lat;
				return z;
			});
		//});
		}

		function getLon(a){
		var z = 0;
		//$(document).ready(function(){
			// format can be json, xml, html
			// See : http://wiki.openstreetmap.org/wiki/Nominatim
			var p = "http://nominatim.openstreetmap.org/search?q="+a+"&format=json&polygon=1&addressdetails=1";
			z = $.get(p,function(data){		
				fnd = data;
				z = fnd[0].lon;
				return z;
			});
		//});
		}

