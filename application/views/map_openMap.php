<?php	
$state_kys = array_keys($states);
$url_path = "http://".$_SERVER['SERVER_NAME']."/apps/interchangecars2/"; 
if(strpos($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'],"www/Applications/") > 0){
	$url_path = "http://localhost/Applications/interchangecars2/";
}
//echo "<pre>"; print_r($wbs); echo "</pre>";
//echo "<pre>"; print_r($state_kys); echo "</pre>";
$pgTitle = "MRICF - Model Rail Interchange Cars Facility v2.0";
?>
<span style="float: right;">
	<a href="https://mapicons.mapsmarker.com" target="_blank">
	Maps Icons Collection<br />
	<img src="<?php echo IMAGE_ROOT; ?>/mapiconlogo.gif" />
	</a>
</span>
<h3>openMap View</h3>
Testing view. Not yet linked in Menu.
<div style="display: block; vertical-align: top;">
	<div id="mapdiv" style="width: 1000px; height: 700px; display: inline-block;"></div>
	<div id="wb_details" style="display: inline-block; padding: 10px; background-color: antiquewhite; max-width: 600px; color: black; height: 700px; overflow: auto; border: 1px solid red;"></div>
</div>

<div id="latlons">
</div>

<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
<script>
$(document).ready(function(){
	jQuery.ajaxSetup({async:false}); // Turn JQuery AJAX to sync (synconous)! Required so that rest of script isn't executed before $.get's.
    map = new OpenLayers.Map("mapdiv");
    map.addLayer(new OpenLayers.Layer.OSM());

	var pois = new OpenLayers.Layer.Text( "Text Boxes",
		{ location:"<?php echo WEB_ROOT; ?>/map_files/textfile-<?php if(isset($_COOKIE['rr_sess'])){ echo $_COOKIE['rr_sess']; }else{ echo '0'; } ?>.txt",
			projection: map.displayProjection
		}
	);

	map.addLayer(pois); 
	// create layer switcher widget in top right corner of map.
	var layer_switcher= new OpenLayers.Control.LayerSwitcher({});
	map.addControl(layer_switcher);
	//getLatLon(); // Got to figure out how this formats the returned data!

	var lonLat = new Array();
	/*
    lonLat[0] = new OpenLayers.LonLat( -0.1279688 ,51.5077286 )
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );

	*/

	// Example of real location -> lat/lon co-ords
	// format can be json, xml, html
	// See : http://wiki.openstreetmap.org/wiki/Nominatim
	// Can have just state and country to find centre of state / country.
	/*
	var p = "http://nominatim.openstreetmap.org/search?town=culcairn&state=NSW&country=Australia&format=json&polygon=1&addressdetails=1";
	var a = 0;
	var b = 0;
	$.get(p,function(data){		
		fnd = data;
		if(fnd.length > 0){
			a = fnd[0].lat; // Should be lat of address in p URL
			b = fnd[0].lon; // Should be lon of address in p URL
		}
	});
    lonLat[3] = new OpenLayers.LonLat( b , a )
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
	*/

	/* to get Lat Lon of states not in array.
	<?php for($lo=0;$lo<count($states);$lo++){ if($states_arr[$states[$lo]]['lat'] == 0){ $stco = explode(",",$states[$lo]); ?>
	var p = "http://nominatim.openstreetmap.org/search?state=<?php echo @$stco[0]; ?>&country=<?php echo @$stco[1]; ?>&format=json&polygon=1&addressdetails=1";
	$.get(p,function(data){		
		fnd = data;
		if(fnd.length > 0){
			document.getElementById('latlons').innerHTML += '<?php echo $states[$lo]; ?> - Lat = '+fnd[0].lat+' - Lon = '+fnd[0].lon+'<br />';
		}
	});		
	<?php } } ?>
	*/


	<?php for($lo=0;$lo<count($states_fnd);$lo++){ /* START LOOP THRU STATES FOUND */ $stco = explode(",",$states_fnd[$lo]); ?>
		<?php for($lp=0;$lp<count($locations[$states_fnd[$lo]]);$lp++){ /* START LOOP THRU LOCATIONS x STATE */ ?>
			document.getElementById('wb_details').innerHTML += '<?php echo $locations[$states_fnd[$lo]][$lp]; ?><ul style="margin: 5px; margin-left: 25px;">';
			<?php for($wb=0;$wb<count($waybills[$locations[$states_fnd[$lo]][$lp]]);$wb++){ /* START LOOP THRU WBs FOR LOCATION */ ?>
			document.getElementById('wb_details').innerHTML += '<li><a href="<?php echo WEB_ROOT; ?>/waybill/edit/<?php echo $waybills[$locations[$states_fnd[$lo]][$lp]][$wb]['id']; ?>"><?php echo $waybills[$locations[$states_fnd[$lo]][$lp]][$wb]['wb_num']; ?></a></li>';
			<?php } /* END LOOP THRU WBs FOR LOCATION */ ?>
			document.getElementById('wb_details').innerHTML += "</ul><br />";
		<?php } /* END LOOP THRU LOCAATIONS x STATE */ ?>
	lonLat[<?php echo intval($lo); ?>] = new OpenLayers.LonLat( <?php echo @$states_arr[$states_fnd[$lo]]['lon']; ?> , <?php echo @$states_arr[$states_fnd[$lo]]['lat']; ?> )
		.transform(
			new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			map.getProjectionObject() // to Spherical Mercator Projection
		);
		// End of <?php echo $states_fnd[$lo]; ?> calc.

	<?php }/* END LOOP THRU STATES FOUND */ ?>

	lonLatCentre = new OpenLayers.LonLat( -89 , 40 )
		.transform(
			new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
			map.getProjectionObject() // to Spherical Mercator Projection
		);

	var zoom=4;
 
	/* NOT NEEDED IF USING POIs AS POIs textfile HAS LOCATIONS AND ALLOWS SETTING OF PINS
	var markers = new OpenLayers.Layer.Markers( "Markers" );
	map.addLayer(markers);

	for(i=0;i<<?php echo intval($lo); ?>;i++){
		//alert(i + ' done');
		markers.addMarker(new OpenLayers.Marker(lonLat[i]));
	}
	*/
 
	// map.setCenter (lonLat[0], zoom); // zoom can be 0 to 16
	map.setCenter (lonLatCentre, zoom); // zoom can be 0 to 16. Centered on ILLINOIS,USA
    
	//alert('Latitude='+a+'\nLongitude='+b); // Here it shows as ok.
	jQuery.ajaxSetup({async:true}); // Turn JQuery AJAX to async again!

});

	// START FUNCTIONS FOR DYNAMIC TEXT POP=UP ON MAP
	// Needed only for interaction, not for the display.
	function onPopupClose(evt) {
		// 'this' is the popup.
		var feature = this.feature;
		if (feature.layer) { // The feature is not destroyed
			selectControl.unselect(feature);
		} else { // After "moveend" or "refresh" events on POIs layer all 
			// features have been destroyed by the Strategy.BBOX
			this.destroy();
		}
	}

	function onFeatureSelect(evt) {
		feature = evt.feature;
		popup = new OpenLayers.Popup.FramedCloud("featurePopup",
			feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(200,400),
			"<strong>"+feature.attributes.title + "</strong><hr />" +
			feature.attributes.description,
			null, true, onPopupClose);
		feature.popup = popup;
		popup.feature = feature;
		map.addPopup(popup, true);
	}
            
	function onFeatureUnselect(evt) {
		feature = evt.feature;
		if (feature.popup) {
			popup.feature = null;
			map.removePopup(feature.popup);
			feature.popup.destroy();
			feature.popup = null;
		}
	}
	// END FUNCTIONS FOR DYNAMIC TEXT POP=UP ON MAP

</script>
</body>
</html>
