<?php 
?>
<?php 
$f21 = "[{}]"; if(strlen(@$fld21) > 0 && json_decode(@$fld21, TRUE)){$f21 = $fld21;}
$f21 = str_replace("{\"AAR_REQD\":\"UNDEFINED\",\"NUM\":\"UNDEFINED\",\"AAR\":\"UNDEFINED\",\"RR\":\"UNDEFINED\"},","",$f21); 
?>
	//var rr_ids = new Array;
	var rr_repmarks = new Array;
	<?php 
	for($r=0;$r<count($allRR);$r++){
		if(isset($allRR[$r]->id)){echo "rr_repmarks[".$allRR[$r]->id."] = '".$allRR[$r]->report_mark."';\n";}
	} ?>
	
	function explodeStr(delim,longstr){
		// Explodes a string into an array.
		// delim = the delimiter for the string (ie, the char to split the string at)
		// longstr = the string to split
		// str_arr is the array of data to return
		var str_arr=longstr.split(delim); 
		return str_arr;
	}	
	
	function copSel(fld1, fld2){
		// Copy data in fld1 to fld2.
		var cFld1 = document.getElementById(fld1).value;
		document.getElementById(fld2).value = cFld1;
	}
		
	function mess_win(type){
		win = window.open("message.php?type="+type, "", "width=400px, height=550px, resizable");
	}

	function app_win(type,id){
		// Only used for Edits, not New entries or Deletes
		win = window.open("edit.php?action=EDIT&type="+type+"&id="+id+"&win=Y", "", "width=800px, height=550px, resizable");
	}
	
	function updateOnStatChg(stat,prog,locn,ulLab){
		// Updates Progress report box when certain Status's selected.
		// stat = status selector dom
		// prog = progress text field dom
		// locn = progress location field dom
		// var stat = document.form1.fld7;
		// var prog = document.form1.pfld3_0;
		// var locn = document.form1.pfld6_0;
		var orig = document.getElementById('fld4').value;
		var dest = document.getElementById('fld5').value;
		var icPnts = new Array();
		var icLocs = new Array();
		var icPntx = '';
		
		if(dest == '' || dest == 0){dest = 'DESTINATION INDUSTRY';}
		if(orig == '' || orig == 0){orig = 'ORIGINATING INDUSTRY';}
		if(prog.value.length == 0){
			if(stat.value == 'CLOSED'){
				prog.value = 'WAYBILL CLOSED';
				document.getElementById('fld14').value = '';
			}
			if(stat.value == 'UNLOADING'){prog.value = 'UNLOADING AT ' + dest;}
			if(stat.value == 'LOADING'){prog.value = 'LOADING AT ' + orig;}
		}

		icPntx = '(RETURNING) ' + stat.value;
		//if(stat.value != "RETURNING"){
		if(stat.value.indexOf("AT ") > -1 && stat.value.indexOf("I-CHANGE") == -1){
			prog.value = 'Located ' + stat.value + ' - READY FOR TRANSIT / PICKUP';
			var stat_tmp = stat.value.replace("  "," ");
			locn.value = stat_tmp.replace("AT ","");
		}

		//document.getElementById('auto_ul').style.display = 'none';
		document.getElementById('auto_ul_lab').style.display = 'none';
		if(stat.value == 'UNLOADING'){ 
			//document.getElementById('auto_ul').style.display = 'block';
			//document.getElementById('auto_ul_lab').style.display = 'block';
			ulLab.style.display = 'block';
		}
	}

	function selTrain(tr){
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			document.getElementById('route_json').value = '';
			document.getElementById('train_disp_span2').style.display = 'none';
			document.getElementById('train_disp_span2').innerHTML = 'You need to click the Calc Route button to allocate the Auto schedule data to the waybill.<br />';
			document.getElementById('train_disp_span2').innerHTML += 'If you do not with to use the train as an Auto train do not enter anything into the Entry Waypoint field, or click the Calc Route button.<br />';
			document.getElementById('train_disp_span2').innerHTML += 'If the Calc Route button does not appear then the train is not currently able to be used as an Auto train.';
			var tds = document.getElementById('train_disp_span');
			var trd = tr.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			tds.style.display = 'none';
			tds.innerHTML = '&nbsp;';
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=selTrain&d=" + trd;
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){
					tds.innerHTML = fnd;
					tds.style.display = 'block';
					document.getElementById('train_disp_span2').style.display = 'block';
				}
			});
			<?php } ?>
		});
	}
	
	function selRoute(){
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			document.getElementById('train_disp_span2').innerHTML = '';
			var tds = document.getElementById('route_json');
			var tr = document.getElementById('fld14');
			var te = document.getElementById('entry_waypoint');
			var tx = document.getElementById('exit_waypoint');
			var ta = document.getElementById('auto_start_dt');
			var trd = tr.value.replace("&","[AMP]"); // Required to convert & to [AMP] so that the GET vars work properly in ajax script
			var tre = te.value.replace("&","[AMP]");
			var trx = tx.value.replace("&","[AMP]");
			var tra = ta.value;
			//tds.style.display = 'none';
			//tds.value = '&nbsp;';
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=selRoute&d=" + trd + "&s=" + tre + "&e=" + trx + "&g=" + tra;
			//$.getJSON(p,function(data){
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){
					tds.value = fnd;
					var routeJson = fnd;
					//tds.style.display = 'block';
					document.getElementById('train_disp_span2').innerHTML = '<strong>Route has been calculated. Its schedule will be added to the Auto data once you click the Save button.</strong><br />';
					document.getElementById('train_disp_span2').innerHTML += 'JSON data: ' + fnd;
					document.getElementById('train_disp_span2').style.display = 'block';
				}
			});
			<?php } ?>
		});
	}
	
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
	
	function trainAutoComp(a,c,d){
		document.getElementById(d+'_span').style.display = 'block';		
		a = a.toUpperCase();
		var fnd = '';
		
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			//alert('got here');
			var ad = a.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			//var bd = b.replace("&","[AMP]");
			var cd = c.replace("&","[AMP]");
			var dd = d.replace("&","[AMP]");
			//var p = "<?php echo JS_ROOT; ?>/ajax.php?f=trainAutoComp&a=" + ad + "&b=" + bd + "&c=" + cd + "&d=" + dd;
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=trainAutoComp&a=" + ad + "&c=" + cd + "&d=" + dd;
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
	
	function carsAutoFind(a,b){
		a = a.toUpperCase();
		var fnd = '';
			
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			//alert('got here');
			var ad = a.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=carsAutoFind&a=" + ad + "&b=" + b;
			//alert(p);
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){document.getElementById('mtcars_span').innerHTML = fnd;}
			});
			<?php } ?>
		});
	}
		
	function carUsed(cr){
		// Checks whether a car is already used and displays an alert if the car is already on another waybill
		cr = cr.toUpperCase();
		var fnd = '';
			
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			var crd = cr.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var p = "<?php echo JS_ROOT; ?>/ajax.php?f=carUsed&d=" + crd;
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){alert('Car '+cr+' is on waybill '+fnd);}
			});
			<?php } ?>
		});
	} 

	var carsJson = <?php echo $f21; ?>;
		
	function addCar(){
		// Add a car to the waybill (JSON array manipulation)
		i=carsJson.length;
		carsJson.push({
			"AAR_REQD" : document.getElementById('fld10').value,
			"NUM" : document.getElementById('fld21_car').value,
			"AAR" : document.getElementById('fld21_aar').value, 
			"RR" :  document.getElementById('fld21_rr').value
    	});
    	dispCars();
	}
		
	function delCar(i){
		// Delete a car from the waybill (JSON array manipulation)
		carsJson.splice(i , 1);
		dispCars();
	}
		
	//window.onload = function(){dispCars();}
		
	function dispCars(){
		// Display cars in a div.
		var rr_afil =  new Array;
		<?php
		for($a=0;$a<count($affil);$a++){echo "rr_afil[".$a."] = '".$affil[$a]."';\n";}
		?>

		var rr_found = 0;
		var rr_mark = '';
		document.getElementById('carsHTM').innerHTML = '';
		var fld21 = document.getElementById('fld21'); 
		var carsHTM = document.getElementById('carsHTM');
		fld21.value = '[';
		for(i=0;i<carsJson.length;i++){
			rr_mark = '';
			if(rr_repmarks[carsJson[i].RR]){rr_mark = rr_repmarks[carsJson[i].RR];}
			//carsHTM.innerHTML += "<strong>"+carsJson[i].NUM+"</strong> ("+carsJson[i].AAR+") <em>["+rr_mark+"]</em>";
			carsHTM.innerHTML += "<strong>"+carsJson[i].NUM+"</strong> ("+carsJson[i].AAR+") <em>["+rr_mark+"]</em>";
			rr_found = 0;
			for(r=0;r<rr_afil.length;r++){
				if(carsJson[i].RR == rr_afil[r]){rr_found++;};
			}
			if(rr_found > 0){
				carsHTM.innerHTML += '&nbsp;<a href="javascript:{}" onclick="delCar(\''+i+'\')">Del</a>';
			}
			carsHTM.innerHTML += "<br />";
			if(i>0){fld21.value += ',';}
			fld21.value += '{"AAR_REQD":"'+carsJson[i].AAR_REQD+'","NUM":"'+carsJson[i].NUM+'","AAR":"'+carsJson[i].AAR+'","RR":"'+carsJson[i].RR+'"}';
		}
		fld21.value += ']';
    	document.getElementById('fld21_car').value = '';
	}


	function chckFrm(){
	   var textMess = "ERRORS:\n";
	   var warnMess = "WARNING:\n";
	  	var errGen = 0;
	  	var warnGen = 0;

		var fld1 = document.getElementById('fld1');
		var fld2 = document.getElementById('fld2');
		var fld3 = document.getElementById('fld3');
		var fld4 = document.getElementById('fld4');
		var fld5 = document.getElementById('fld5');
		var fld6 = document.getElementById('fld6');
		var fld7 = document.getElementById('fld7');
		var fld8 = document.getElementById('fld8');
		var fld9 = document.getElementById('fld9');
		var fld10 = document.getElementById('fld10');
		var fld11 = document.getElementById('fld11');
		var fld12 = document.getElementById('fld12');
		var fld13 = document.getElementById('fld13');
		//var autoStartDate = document.getElementById('auto_start_date');
		//var isAuto = document.getElementById('is_auto');
			
		if(fld1.value == 0){textMess = textMess+" [Date] needs a value.\n"; errGen = errGen+1}; 
		if(fld2.value == 0){textMess = textMess+" [From Railroad] needs a value.\n"; errGen = errGen+1}; 
		if(fld3.value == 0){textMess = textMess+" [To Railroad] needs a value.\n"; errGen = errGen+1}; 
		if(fld2.value == fld3.value){warnMess = warnMess+" [From Railroad] and [To Railroad] are the same.\n"; warnGen = warnGen+1}; 
		if(fld4.value == 0){textMess = textMess+" [Origin Industry] needs a value.\n"; errGen = errGen+1}; 
		if(fld8.value == 0){textMess = textMess+" [Waybill Number] needs a value.\n"; errGen = errGen+1};
		//if(autoStartDate.value.length < 1 && isAuto.value > 0){textMess = textMess+" [Start Date From] needs a value.\n"; errGen = errGen+1;} 
	
		if (warnGen>0){
			alert(warnMess);
		}

		if (errGen>0){
			alert(textMess);
			return false;
		}else{
			return true;
		}

	}
	
	$(document).ready(function(){
		/*
		document.getElementById('pos').style.display = 'none';
	
		// JQuery events
		$("#po_expand").click(function(){
			$("#pos").slideDown("slow");
			document.getElementById('pos').style.display = 'block';
			document.getElementById('pos').style.width = '200px';
			return false;
		});
		*/
	});
	
	function route_valid8(){
		// Alerts user to validity of route selected for auto train.
		var tr_valid8 = document.getElementById('tr_valid8');
		var errs = 0;
		tr_valid8.innerHTML = '';
		if(document.getElementById('entry_waypoint').value.length < 1){tr_valid8.innerHTML += 'An Entry Waypoint selection must be made.<br />'; errs++;}
		if(document.getElementById('exit_waypoint').value.length < 1){tr_valid8.innerHTML += 'An Exit Waypoint selection must be made.<br />'; errs++;}
		if(errs == 0){
			selRoute();
			tr_valid8.innerHTML = 'The route for the Auto Train has been successfully set!';
		}
	}
	
	function set_human_date(i){
		//var fdt = document.getElementById('pfld2');
		//var y = document.getElementById('pfld2_y');
		//var m = document.getElementById('pfld2_m');
		//var d = document.getElementById('pfld2_d');

		var fdt = document.getElementById(i);
		var y = document.getElementById(i+'_y');
		var m = document.getElementById(i+'_m');
		var d = document.getElementById(i+'_d');
		
		fdt.value = y.value + "-" + m.value + "-" + d.value;
	}
	
	window.onload = function(){
		hideEle('auto_ul_lab');
		dispCars();
		set_human_date('pfld2_0');
		if(document.getElementById('fld4_indDesc').value.length > 0){document.getElementById('fld4_indDescDiv').style.display = 'block';}
		if(document.getElementById('fld5_indDesc').value.length > 0){document.getElementById('fld5_indDescDiv').style.display = 'block';}
	} 

	function addProgFrm(){
		$(document).ready(function(){
			<?php if($rr_sess > 0){ ?>
			var c = document.getElementById('prog_cntr');
			var w = document.getElementById('fld8');
			var t = document.getElementById('tzone_0');
			var td = t.value.replace("&","[AMP]"); // Require to convert & to [AMP] so that the GET vars work properly in ajax script
			var p = "<?php echo JS_ROOT; ?>/multi-prog.php?f=multiProg&i=" + c.value.length + "&w=" + w.value + "&t=" + td + "&r=<?php echo @$rr_sess; ?>";
			c.value = c.value + 'x';
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){
					var div1 = document.createElement('div');  
					div1.innerHTML = fnd;
					document.getElementById('prog_table').appendChild(div1);
				}
				 
			});
			<?php } ?>
		});
	}

	setInterval(function() {
			<?php if($rr_sess > 0){ ?>
			if(document.getElementById('wb_image_div')){
			var p = "<?php echo WEB_ROOT; ?>/ajax/wbImages/<?php echo $this->dat['id']; ?>";
			$.get(p,function(data){		
				fnd = data;
				if(fnd.length > 0){
					document.getElementById('wb_image_div').innerHTML = fnd;
				}				 
			});
			}
			<?php } ?>		
	}, 90000); // divide value by 1000 to work out seconds 
