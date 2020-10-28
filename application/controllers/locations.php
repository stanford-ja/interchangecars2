<?php
class Locations extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->helper('url');
		$this->load->library('mricf');
		
		$this->load->model('Locations_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
		$this->arr['pgTitle'] .= " - Locations";
		$this->arr['script_file'] = "js/".strtolower(get_class()).".php";
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}

	}

	public function index(){
		$this->lst();
	}
	
	public function lst(){
		$this->arr['jquery'] = "\$('.table1').DataTable({ 
			paging: false, 
			searching: true, 
			responsive: true, 
			info: false, 
			stateSave: false,
			order: [[ 1, 'asc' ]] });";
		$this->arr['pgTitle'] .= " - List";
		$randpos = array();
		$commodat = (array)$this->Locations_model->get_allSorted();
		//$this->dat = array();
		$this->dat['fields'] 			= array('id', 'fictional_location', 'real_location', 'latitude', 'longitude', 'modified');
		$this->dat['field_names'] 		= array("ID", "Fictional Location", "Real Location", "Latitude", "Longitude", "Added/Modified");
		$this->dat['options']			= array(
				'Edit' => "locations/edit/"
			); // Paths to options method, with trailling slash!
		$this->dat['links']				= array(
				'New' => "locations/edit/0"
			); // Paths for other links!
		
		for($i=0;$i<count($commodat);$i++){
			$this->dat['data'][$i]['id'] 							= $commodat[$i]->id;
			$this->dat['data'][$i]['fictional_location']	 	= $commodat[$i]->fictional_location;
			$this->dat['data'][$i]['real_location'] 				= $commodat[$i]->real_location;
			$this->dat['data'][$i]['latitude'] 				= $commodat[$i]->latitude;
			$this->dat['data'][$i]['longitude'] 				= $commodat[$i]->longitude;
			$this->dat['data'][$i]['modified']					= "";
			if($commodat[$i]->added > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$commodat[$i]->added);}
			if($commodat[$i]->modified > 0){$this->dat['data'][$i]['modified'] = date('Y-m-d H:i',$commodat[$i]->modified);}
		}

		// Load views
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		//$this->load->view('list', $this->dat);
		$this->load->view('table', $this->dat);
		$this->load->view('footer');
	}
	
	public function edit($id=0){
		// Used for editing existing (edit/[id]) and adding new (edit/0) records
		$this->load->helper('form');
		$this->dat['attribs'] = array('name' => "form"); // Attribs for form tag
		$this->dat['fields'] = array();
		$this->dat['field_names'] = array();
		if($id < 1){
			$this->arr['pgTitle'] .= " - New";
			$this->dat['data'][0] = array('id' => 0);
		}else{
			$this->arr['pgTitle'] .= " - Edit";
			$this->dat['data'] = (array)$this->Locations_model->get_single($id);
		}
		
		//echo "<pre>"; print_r($this->dat['data']); echo "</pre>";
		$this->setFieldSpecs(); // Set field specs
		for($i=0;$i<count($this->field_defs);$i++){
			$this->dat['field_names'][$i] = $this->field_defs[$i]['label'];
			if($this->field_defs[$i]['type'] == "checkbox"){
				$this->dat['fields'][$i] = form_checkbox($this->field_defs[$i]['def']).$this->dat['field_names'][$i];
				$this->dat['field_names'][$i] = "";
			}
			if($this->field_defs[$i]['type'] == "input"){$this->dat['fields'][$i] = "<br />".form_input($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "textarea"){$this->dat['fields'][$i] = "<br />".form_textarea($this->field_defs[$i]['def']);}
			if($this->field_defs[$i]['type'] == "select"){$this->dat['fields'][$i] = "<br />".form_dropdown($this->field_defs[$i]['name'],$this->field_defs[$i]['options'],$this->field_defs[$i]['value'],$this->field_defs[$i]['other']);}
			if($this->field_defs[$i]['type'] == "statictext"){$this->dat['fields'][$i] = "<br />".$this->field_defs[$i]['value'];}
		}
		$this->load->view('header', $this->arr);
		$this->load->view('menu', $this->arr);
		if($this->arr['rr_sess'] > 0){$this->load->view('edit', $this->dat);}
		else{
			$this->load->view('not_allowed');
		}
		$this->load->view('footer');
	}
	
	public function setFieldSpecs(){
		// Sets specific field definitions for the controller being used.
		$this->dat['fields'] = array();
		
		// Add custom model calls / queries under this line...
		//$this->load->model('Aar_model', '', TRUE);
		//$this->load->model('Railroad_model', '', TRUE);
		
		// Add other code for fields under this line...
		/*
		$aar_opts = array();
		$aar_tmp = (array)$this->Aar_model->get_allSorted();
		for($i=0;$i<count($aar_tmp);$i++){$aar_opts[$aar_tmp[$i]->aar_code] = $aar_tmp[$i]->aar_code." - ".substr($aar_tmp[$i]->desc,0,70);}
		*/
		
		/*
		$rr_opts = array();
		$rr_tmp = (array)$this->Railroad_model->get_allActive();
		for($i=0;$i<count($rr_tmp);$i++){$rr_opts[$rr_tmp[$i]->id] = $rr_tmp[$i]->report_mark." - ".substr($rr_tmp[$i]->rr_name,0,70);}
		*/
		
		// Add form and field definitions specific to this controller under this line... 
		$this->dat['hidden'] = array('tbl' => 'locations', 'id' => @$this->dat['data'][0]->id);
		$this->dat['form_url'] = "../save";
		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Fictional Location', 'def' => array(
              'name'        => 'fictional_location',
              'id'          => 'fictional_location',
              'value'       => @$this->dat['data'][0]->fictional_location,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Real Location', 'def' => array(
              'name'        => 'real_location',
              'id'          => 'real_location',
              'value'       => @$this->dat['data'][0]->real_location,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Latitude', 'def' => array(
              'name'        => 'latitude',
              'id'          => 'latitude',
              'value'       => @$this->dat['data'][0]->latitude,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);

		$this->field_defs[] =  array(
			'type' => "input", 'label' => 'Longitude', 'def' => array(
              'name'        => 'longitude',
              'id'          => 'longitude',
              'value'       => @$this->dat['data'][0]->longitude,
              'maxlength'   => '60',
              'size'        => '60'
			)
		);
		
		$this->field_defs[] = array(
			'type' => "statictext", 'label' => "Click to set Lat / Lon", 
				'value' => "<a href=\"javascript:{}\" onclick=\"getGeoLatLon();\">Get Lat Lon of Real Location</a>
				<div id=\"map\" style=\"height: 400px; width: 500px;\"></div>
				<script src=\"http://www.openlayers.org/api/OpenLayers.js\"></script>
				<script type=\"text/javascript\">
            OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {                
                defaultHandlerOptions: {
                    'single': true,
                    'double': false,
                    'pixelTolerance': 0,
                    'stopSingle': false,
                    'stopDouble': false
                },

                initialize: function(options) {
                    this.handlerOptions = OpenLayers.Util.extend(
                        {}, this.defaultHandlerOptions
                    );
                    OpenLayers.Control.prototype.initialize.apply(
                        this, arguments
                    ); 
                    this.handler = new OpenLayers.Handler.Click(
                        this, {
                            'click': this.trigger
                        }, this.handlerOptions
                    );
                }, 

                trigger: function(e) {
                    var lonlat = map.getLonLatFromViewPortPx(e.xy);
                    //alert(\"You clicked near \" + lonlat.lat + \" N, \" + lonlat.lon + \" E\");
                    document.getElementById('latitude').value = lonlat.lat;
                    document.getElementById('longitude').value = lonlat.lon;
                    alert('Latitude '+lonlat.lat+' and Longitude '+lonlat.lon+' fields populated!');
                    //alert('Latitude '+LatLon.Lat+' and Longitude '+LatLon.Lon+' fields populated!');
                }

            });
        
            function getGeoLatLon(){
					var p = \"http://nominatim.openstreetmap.org/search?q=\"+document.getElementById('real_location').value+\"&format=json&polygon=1&addressdetails=1\";
					\$.get(p,function(data){	
						fnd = data;
						if(fnd.length > 0){
							//alert(fnd[0].boundingbox[0]);
							document.getElementById('latitude').value = fnd[0].boundingbox[0]; //fnd[0].lat;
							document.getElementById('longitude').value = fnd[0].boundingbox[3]; //fnd[0].lon;
						}
					});		
            }
            
            var map;

			\$(document).ready(function(){
                map = new OpenLayers.Map('map');
                //map.addLayer(new OpenLayers.Layer.OSM());

                var ol_wms = new OpenLayers.Layer.WMS( \"OpenLayers WMS\", \"http://vmap0.tiles.osgeo.org/wms/vmap0?\", {layers: 'basic'} );
            var jpl_wms = new OpenLayers.Layer.WMS( \"NASA Global Mosaic\", \"http://t1.hypercube.telascience.org/cgi-bin/landsat7\", {layers: \"landsat7\"});
            var osm_wms = new OpenLayers.Layer.OSM(); // User mercator NOT LatLon!!

                jpl_wms.setVisibility(false);
					//ol_wms.setVisibility(false);

                map.addLayers([ol_wms, jpl_wms]);
                //map.addLayers([osm_wms]);
                map.addControl(new OpenLayers.Control.LayerSwitcher());
                // map.setCenter(new OpenLayers.LonLat(0, 0), 0);
                //map.zoomToMaxExtent();

	lonLatCentre = new OpenLayers.LonLat( -89 , 40 )
		.transform(
			new OpenLayers.Projection(\"EPSG:4326\"), // transform from WGS 1984
			map.getProjectionObject() // to Spherical Mercator Projection
		);

	var zoom=4;

	map.setCenter (lonLatCentre, zoom);                
                var click = new OpenLayers.Control.Click();
                map.addControl(click);
                click.activate();
			});
        </script>"
		);

		/*
		$this->field_defs[] =  array(
			'type' => "textarea", 'label' => 'Real Location', 'def' => array(
              'name'        => 'real_location',
              'id'          => 'real_location',
              'value'       => @$this->dat['data'][0]->real_location,
              'rows'			 => '3',
              'cols'        => '50'
			)
		);
		*/

		/*
		$this->field_defs[] =  array(
			'type' => "select", 'label' => 'AAR Type', 'name' => 'aar_type', 'value' => @$this->dat['data'][0]->aar_type, 
			'other' => 'id="aar_type"', 'options' => $aar_opts
		);
		*/

		/*
		$this->field_defs[] =  array(
			'type' => "statictext", 'label' => '<br />Freight Out Auto Generation',
			'value' => '<div style="border: 1px solid red; background-color: yellow; font-size: 9pt; padding: 5px;">To allow Generated Loads for a Commodity, the values in the Freight Out MUST be comma (,) separated and match exactly a semi-colon (;) separated value in the Commodities: Generates these Commods field.</div>'
		);
		*/

	}

}
?>
