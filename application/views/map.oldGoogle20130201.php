<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>MRICF - Model Rail Interchange Cars Facility - Map</title>
		<link REL="StyleSheet" HREF="../css/style.css" TYPE="text/css" MEDIA="screen">
		<link REL="StyleSheet" HREF="../css/print.css" TYPE="text/css" MEDIA="print">
		<link REL="StyleSheet" HREF="../css/mobile.css" TYPE="text/css" MEDIA="handheld">
		<meta name="generator" content="Bluefish 2.2.2" >
		<meta name="author" content="James Stanford" >
		<meta name="keywords" content="model, railroad, railway, freight, car, interchange, application, waybill, train sheet, rollingstock">
		<meta name="description" content="The MRICF is a Model Railroad Virtual Freight and Cars Interchange Application with Waybills, Industries, Train Sheets, Rollingstock management and more">
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="content-style-type" content="text/css"/>
		<style>
			.txtdiv {padding-left: 5px; padding-right: 5px; width: 90%; font-size: 9pt; height: 15px; overflow: hidden; display: block; margin-bottom: 4px;}
		</style>
	</head>
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAm_VCXnhfjstxzItlLyN4mBTquJ3XAnB-O2ZhZYp82wRvGEX5YxQbuUOxizYh2Df1SzfR-wjHVa5-HQ" type="text/javascript"></script>
	<!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=AIzaSyC4gwZytGbnCaLt_dTEsBWuIeqXK03_Dr0" type="text/javascript"></script> // -->
	<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
	</script>
	<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-10404441-1");
		pageTracker._trackPageview();
		} catch(err) {}
	</script>
	<script type="text/javascript">
	var map;

		function mess_win(id){
			win = window.open("message.php?id="+id, "", "width=400px, height=400px, resizable");
		}

function divToggle(d){
	// dvs = array of divs to close.
	dvs = new Array();
	<?php echo $divJS; ?>
	
	for(i=0;i<dvs.length;i++){
		dnam = dvs[i];
		document.getElementById(dnam).style.display = 'none';
	}
	
	if(d.length > 0){eval("document.getElementById('" + d + "').style.display = 'block';");}
}

function divTxtToggle(d){
	<?php echo $divTxtJS; ?>
	document.getElementById(d).style.height = 'auto';
	document.getElementById(d).style.backgroundColor = 'yellow';
	document.getElementById(d).style.border = '1px solid gray';
}

function setAddr(addr){
	//addr = document.getElementById('street_number').value + ' ';
	//addr = addr + document.getElementById('street').value + ', ';
	//addr = addr + document.getElementById('suburb').value + ', ';
	//addr = addr + document.getElementById('state').value + ', ';
	//addr = addr + document.getElementById('postode').value;
	return addr;
}
 /*
if (GBrowserIsCompatible()) { 

  function createMarker(point,html) {
	var marker = new GMarker(point);
	GEvent.addListener(marker, "click", function() {
	  marker.openInfoWindowHtml(html);
	});
	return marker;
  }

	// Set the center of the  map.
	  // Display the map, with some controls and set the initial location 
	var map = new GMap2(document.getElementById("map_canvas"));
	map.addMapType();
	map.setCenter(new GLatLng(-34.89877192,150.5013202),8);
	map.addControl(new GLargeMapControl());
	map.addControl(new GMapTypeControl());
	map.enableContinuousZoom();
	map.enableScrollWheelZoom();

}
*/

function errNotice(m){
	var er = document.getElementById('errs');
	er.innerHtml += m; 
}

//function gawkAtIt(strAddr,wb,inf){
function gawkAtIt(){
	var dvOp = 'mapcanvas';
	//eval("document.getElementById('" + dvOp + "').style.display = 'inline';");
	if (GBrowserIsCompatible()) { 

      // A function to create the marker and set up the event window
      // Dont try to unroll this function. It has to be here for the function closure
      // Each instance of the function preserves the contends of a different instance
      // of the "marker" and "html" variables which will be needed later when the event triggers.    
		  function createMarker(point,html) {
			var marker = new GMarker(point);
			GEvent.addListener(marker, "click", function() {
			  marker.openInfoWindowHtml(html);
			});
			return marker;
		  }


		
		  // Display the map, with some controls and set the initial location 
		var map = new GMap2(document.getElementById(dvOp));
		var geocoder = new GClientGeocoder();
		map.addControl(new GLargeMapControl());
		map.addControl(new GMapTypeControl());
		//map.setCenter(new GLatLng(-34.89877192,150.5013202),17);

		// Start pointer code!
		var all_not_fnd = '';
		<?php for($i=0;$i<count($pnt);$i++){ if(strlen($pnt[$i]) > 0){ ?>
		  geocoder.getLatLng(
			'<?php echo $pnt[$i]; ?>',
			function(point<?php echo $i; ?>) {
			  if(!point<?php echo $i; ?>){
			  	document.getElementById('errs').style.display = 'block';
			  document.getElementById('errs').innerHTML += 'Location <?php echo $pnt[$i]; ?> on waybill <?php echo $wb[$i]; ?> not found.<br />';
			  }
			  //if (point<?php echo $i; ?>) {
				map.setCenter(point<?php echo $i; ?>, 4);
				var marker<?php echo $i; ?> = new GMarker(point<?php echo $i; ?>, {title: '<?php echo strip_tags($crs[$i]); ?>', draggable: true});
				GEvent.addListener(marker<?php echo $i; ?>, "click", function(){divTxtToggle('<?php echo "wb".$wb[$i]; ?>')});
				<?php if(strlen($g[$i]) > 0){ ?>
				marker<?php echo $i; ?>.icon = 'http://www.stanfordhosting.net/interchangecars/images/<?php echo $g[$i]; ?>';
				<?php } ?>
				map.addOverlay(marker<?php echo $i; ?>);
				<?php if($wb[$i] == $wid){ ?>
				marker<?php echo $i; ?>.openInfoWindowHtml('<strong>Details</strong>' + '<br /><?php echo $crs[$i].str_replace("<br />"," ",$g[$i]); ?>');
				<?php }else{ ?>
				marker<?php echo $i; ?>.bindInfoWindow('<strong>Details</strong>' + '<br /><?php echo "<span style=\"float: right;\">".$crs[$i].nl2br($g[$i])."</span>".$inf[$i]; ?>') ;
				<?php } ?>
			  //}
			}
		  );
		 <?php } } ?>
		// end Pointer code!

		/* Start original pointer code
		  geocoder.getLatLng(
			address,
			function(point) {
			  if (!point) {
				alert(address + " not found");
			  } else {
				map.setCenter(point, 13);
				var marker = new GMarker(point);
				map.addOverlay(marker);
				marker.openInfoWindowHtml(address);
			  }
			}
		  );
		End original pointer code */


		//map.setCenter(new GLatLng(-34.89877192,150.5013202),15);
		map.enableContinuousZoom();
		map.enableScrollWheelZoom();
	 }
    
    // display a warning if the browser was not compatible
    else {
      alert("Sorry, the Google Maps API is not compatible with this browser");
    }
}

	</script>
	<script language="javascript" type="text/javascript" src="http://www.stanfordhosting.net/interchangecars2/js/common.js"></script>
	<script type='text/javascript' src='http://www.stanfordhosting.net/interchangecars2/js/jquery-1.8.2.min.js'></script>
	<script type='text/javascript' src='http://www.stanfordhosting.net/interchangecars2/js/jquery.simplemodal.js'></script>
	<script type='text/javascript' src='http://www.stanfordhosting.net/interchangecars2/js/basic.js'></script>
	<body <?php if($wid > 0){echo "onLoad=\"divTxtToggle('wb".$wid."')\"";} ?>>
	<h2>MRICF - Model Rail Interchange Cars Facility - Map</h2>
	<div style="display: table;" style="width: 100%">
		<div style="display: table-row">
			<div style="display: table-cell; width: 75%;">
				<a href="home">Home</a><br />
				<div id="mapcanvas" style="width: 770px; height: 500px; display:block; font-size: 10pt;"></div>
			</div>
			<div style="display: table-cell;">
				<div style="display: block; height: 500px; width: 100%; overflow: auto;">
				<?php for ($t=0;$t<count($txt);$t++){echo $txt[$t]; } ?>
				</div>
			</div>
		</div>
	</div>
	<span class="small_txt" id="stuff1">		
<div id="nothing" style="display: none;"></div>	
<script language="javascript" type="text/javascript">
	gawkAtIt();
</script>
<div id="errs" style="display: none; background-color: yellow; max-height: 120px; overflow: auto; padding: 5px; border: 1px solid red;">&nbsp;</div>
</body>
</html>
