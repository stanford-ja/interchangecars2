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
<h3>Test of openMap API - Seems to work ok.</h3>
LatLon co-ord is working, but may need replacing with hard coded array of values to speed up loading! <br />
Switch the view map to map.php to see the old map system!<br />
Working on the Dynamic Text Box system - functions in openMap.php, but needs code to generate textfile-n.txt contents.
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
	// For data syntax to be used in textfile, see /var/www/server/test/openlayers_mapApi/OpenLayers-2.12/examples/textfile.txt
	/* Example of textfile contents - segments are TAB delimited.
point	title	description	icon
10,20	my orange title	my orange description	
2,4	my aqua title	my aqua description	
42,-71	my purple title	my purple description<br/>is great.	http://www.openlayers.org/api/img/zoom-world-mini.png
	*/

            var map;
        
            //function init(){
                map = new OpenLayers.Map('mapdiv');
                var wms = new OpenLayers.Layer.WMS(
                    "OpenLayers WMS", "http://vmap0.tiles.osgeo.org/wms/vmap0",
                    {layers: 'basic'}
                );

                var layer = new OpenLayers.Layer.Vector("POIs", {
                    strategies: [new OpenLayers.Strategy.BBOX({resFactor: 1.1})],
                    protocol: new OpenLayers.Protocol.HTTP({
                        url: "<?php echo WEB_ROOT; ?>/map_files/textfile-<?php if(isset($_COOKIE['rr_sess'])){ echo $_COOKIE['rr_sess']; }else{ echo '0'; } ?>.txt",
                        format: new OpenLayers.Format.Text()
                    })
                });

                map.addLayers([wms, layer]);
                //map.zoomToMaxExtent();
                map.zoom = 4;
			
					lonLatCentre = new OpenLayers.LonLat( -89 , 40 );
					//	.transform(
					//		new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
					//		map.getProjectionObject() // to Spherical Mercator Projection
					//	);

					map.setCenter (lonLatCentre, zoom);

                // Interaction; not needed for initial display.
                selectControl = new OpenLayers.Control.SelectFeature(layer);
                map.addControl(selectControl);
                selectControl.activate();
                layer.events.on({
                    'featureselected': onFeatureSelect,
                    'featureunselected': onFeatureUnselect
                });
            //}

	// END DYNAMIC TEXT SECTION

	<?php for($lo=0;$lo<count($states_fnd);$lo++){ /* START LOOP THRU STATES FOUND */ $stco = explode(",",$states_fnd[$lo]); ?>
		<?php for($lp=0;$lp<count($locations[$states_fnd[$lo]]);$lp++){ /* START LOOP THRU LOCATIONS x STATE */ ?>
			document.getElementById('wb_details').innerHTML += '<?php echo $locations[$states_fnd[$lo]][$lp]; ?><ul style="margin: 5px; margin-left: 25px;">';
			<?php for($wb=0;$wb<count($waybills[$locations[$states_fnd[$lo]][$lp]]);$wb++){ /* START LOOP THRU WBs FOR LOCATION */ ?>
			document.getElementById('wb_details').innerHTML += '<li><a href="<?php echo WEB_ROOT; ?>/waybill/edit/<?php echo $waybills[$locations[$states_fnd[$lo]][$lp]][$wb]['id']; ?>"><?php echo $waybills[$locations[$states_fnd[$lo]][$lp]][$wb]['wb_num']; ?></a></li>';
			<?php } /* END LOOP THRU WBs FOR LOCATION */ ?>
			document.getElementById('wb_details').innerHTML += "</ul><br />";
		<?php } /* END LOOP THRU LOCATIONS x STATE */ ?>
		// End of <?php echo $states_fnd[$lo]; ?> calc.

	<?php }/* END LOOP THRU STATES FOUND */ ?>

	//lonLatCentre = new OpenLayers.LonLat( -89 , 40 )
	//	.transform(
	//		new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
	//		map.getProjectionObject() // to Spherical Mercator Projection
	//	);

	//map.setCenter (lonLatCentre, zoom);

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
			//     features have been destroyed by the Strategy.BBOX
			this.destroy();
		}
	}

	function onFeatureSelect(evt) {
		feature = evt.feature;
		popup = new OpenLayers.Popup.FramedCloud("featurePopup",
			feature.geometry.getBounds().getCenterLonLat(),
			new OpenLayers.Size(300,400),
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
