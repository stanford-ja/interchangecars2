<?php 

require_once('maxChart.class.php'); 
include('db_connect7465.php');
include('query_functions.php');

$chart_typ = 0;
if(isset($_GET['chart'])){$chart_typ = $_GET['chart'];}
$legend = "<tr><td>Legend:</td></tr>";

function sel_fld($fldNam, $table, $id){
	$sql = "SELECT `".$fldNam."` FROM `".$table."` WHERE `".$fldNam."` = '".$id."'";
	$dosql = mysql_query($sql);
	$result = mysql_fetch_array($dosql);
	// return $result[$fldNam];\
	return mysql_num_rows($dosql);
}

function get_arr($fldNam, $table, $other = ''){
	$sql = "SELECT DISTINCT `".$fldNam."` FROM `".$table."`";
	if(strlen($other) > 0){$sql .= "WHERE ".$other;}
	$dosql = mysql_query($sql);
	$stuff = array();
	$cntr = 0;
	while($result = mysql_fetch_array($dosql)){
		$cntr = $cntr+1;
		$stuff = $stuff.",".$result[$fldNam];
	}
	// return $result[$fldNam];\
	return $stuff;
	
}

function get_rows($fldNam, $table){
	$sql = "SELECT DISTINCT `".$fldNam."` FROM `".$table."`";
	$dosql = mysql_query($sql);
	$stuff = mysql_num_rows($dosql);
	return $stuff;
	
}

function myTruncate2($string, $limit, $break=" ", $pad="...") {
	// return with no change if string is shorter than $limit  
	if(strlen($string) <= $limit){
		return $string; 
	}
	$string = substr($string, 0, $limit); 
	if(false !== ($breakpoint = strrpos($string, $break))){
		$string = substr($string, 0, $breakpoint);
	}
	return $string.$pad; 
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
   <title>Charts</title>
	<link REL="StyleSheet" HREF="style.css" TYPE="text/css" MEDIA="screen">
   <link href="maxChartStyle/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div id="links" style="width: 100%; text-align: center; padding: 10px;">
		<h2>MRICF Charts and Statistics</h2>
		<a href="#" onClick="window.close();">Close</a><br />
		<a href="charts.php">Waybills x Originating RR</a> | 
		<a href="charts.php?chart=1">Waybills x Destination RR</a><br />
		<a href="charts.php?chart=2">Industries x RR</a> | 
		<a href="charts.php?chart=3">Car Pool x RR</a> | 
		<a href="charts.php?chart=4">Trains x RR</a><br />
		<!-- <a href="charts.php?chart=5">Most Used Cars</a><br /> // -->
	</div>
   <div id="container">
      <div id="header">
      <div id="header_left"></div>
      <div id="header_main">MRICF Charts</div>
      <div id="header_right"></div>
	</div>
      <div id="main">
         <?php
         if($chart_typ == 5){
				$dat = get_arr("car_num","ichange_carsused_index","`id` > 0 ORDER BY COUNT(`car_num`) DESC");
				$data1 = explode(",",$dat);
            $cntr = 0;
				$maxRows = count($data1); //get_rows("id","ichange_carsused_index");
         }else{
				$dat = get_arr("id","ichange_rr");
				$data1 = explode(",",$dat);
            $cntr = 0;
				$maxRows = get_rows("id","ichange_rr");
			}
			
			if($chart_typ == 0){
				while($cntr < $maxRows){
					$cntr=$cntr+1;
					$t = myTruncate2($data1[$cntr], 5);
					if(strlen($t) > 0){
						if(sel_fld("rr_id_from","ichange_waybill",$data1[$cntr]) > 0){ $data[$t] = sel_fld("rr_id_from","ichange_waybill",$data1[$cntr]); }
					}
				}
				$no_tds = 4; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart('Waybills Produced x Originating RR',1,500,150);
            echo "<br/><br/>";
            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
					$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
					//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
					if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
					if(floatval($cntr/4) == intval($cntr/4)){
						//echo "</tr><tr>";
					}
            }
			}
 
			if($chart_typ == 1){
				while($cntr < $maxRows){
					$cntr=$cntr+1;
					$t = myTruncate2($data1[$cntr], 5);
					if(strlen($t) > 0){
						if(sel_fld("rr_id_to","ichange_waybill",$data1[$cntr]) > 0){ $data[$t] = sel_fld("rr_id_to","ichange_waybill",$data1[$cntr]); }
					}
				}
				$no_tds = 4; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart('Waybills Produced x Destination RR',1,500,150);
            echo "<br/><br/>";
            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
					$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
					//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
					if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
					if(floatval($cntr/4) == intval($cntr/4)){
						//echo "</tr><tr>";
					}
            }
			}
           
			if($chart_typ == 2){
				while($cntr < $maxRows){
					$cntr=$cntr+1;
					$t = myTruncate2($data1[$cntr], 5);
					if(strlen($t) > 0){
						if(sel_fld("rr","ichange_indust",$data1[$cntr]) > 0){ $data[$t] = sel_fld("rr","ichange_indust",$data1[$cntr]); }
					}
				}
				$no_tds = 4; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart('Industries x RR',1,500,150);
            echo "<br/><br/>";
            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
					$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
					//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
					if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right: 10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
					if(floatval($cntr/4) == intval($cntr/4)){
						//echo "</tr><tr>";
					}
            }
			}

			if($chart_typ == 3){
				while($cntr < $maxRows){
					$cntr=$cntr+1;
					$t = myTruncate2($data1[$cntr], 5);
					if(strlen($t) > 0){
						if(sel_fld("rr","ichange_cars",$data1[$cntr]) > 0){ $data[$t] = sel_fld("rr","ichange_cars",$data1[$cntr]); }
					}
				}
				$no_tds = 4; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart('Car Pool x RR',1,500,150);
            echo "<br/><br/>";
            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
					$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
					//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
					if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
					if(floatval($cntr/4) == intval($cntr/4)){
						//echo "</tr><tr>";
					}
            }
			}

			if($chart_typ == 4){
				while($cntr < $maxRows){
					$cntr=$cntr+1;
					$t = myTruncate2($data1[$cntr], 5);
					if(strlen($t) > 0){
						if(sel_fld("railroad_id","ichange_trains",$data1[$cntr]) > 0){ $data[$t] = sel_fld("railroad_id","ichange_trains",$data1[$cntr]); }
					}
				}
				$no_tds = 4; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart('Trains x RR',1,500,150);
            echo "<br/><br/>";
            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
					$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
					//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
					if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
					if(floatval($cntr/4) == intval($cntr/4)){
						//echo "</tr><tr>";
					}
            }
			}

			if($chart_typ == 5){
				while($cntr < $maxRows){
					$cntr=$cntr+1;
					$t = myTruncate2($data1[$cntr], 5);
					if(strlen($t) > 0){
						if(sel_fld("car_num","ichange_carsused_index",$data1[$cntr]) > 0){ $data[$t] = sel_fld("car_num","ichange_carsused_index",$data1[$cntr]); }
					}
				}
				$no_tds = 4; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart('Most Used Cars',1,500,150);
            echo "<br/><br/>";
            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
					$td_cs = "background-color: lightgrey;"; //	if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
					//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
					if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
					if(floatval($cntr/4) == intval($cntr/4)){
						//echo "</tr><tr>";
					}
            }
			}
         ?>
         
      </div>
      <div id="footer"><a href="http://www.phpf1.com">Powered by PHP F1</a></div>
   </div>
   
</body>