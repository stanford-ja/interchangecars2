<?php 

require_once('maxChart.class.php'); 
include('db_connect7465.php');
include('query_functions.php');

$chart_typ = 0;
if(isset($_GET['chart'])){$chart_typ = $_GET['chart'];}
$legend = "Legend: "; //"<tr><td>Legend:</td></tr>";

class functions {
	function __construct(){
	}

	function createDBObj(){
		$this->sqli = new mysqli($this->dbhost,$this->dbusername,$this->dbpassword,$this->dbname);
	}

	function sel_fld($fldNam, $table, $id){
		$sql = "SELECT `".$fldNam."` FROM `".$table."` WHERE `".$fldNam."` = '".$id."'";
		$dosql = $this->sqli->query($sql); //mysql_query($sql);
		$result = $dosql->fetch_assoc(); //mysql_fetch_array($dosql);
		// return $result[$fldNam];\
		return $dosql->num_rows; //mysql_num_rows($dosql);
	}

	function get_arr($fldNam, $table, $other = ''){
		$sql = "SELECT DISTINCT `".$fldNam."` FROM `".$table."`";
		if(strlen($other) > 0){$sql .= " WHERE ".$other;}
		$dosql = $this->sqli->query($sql); //mysql_query($sql);
		$stuff = ""; //array();
		$cntr = 0;
		while($result = $dosql->fetch_assoc()){ //mysql_fetch_array($dosql)){
			$cntr = $cntr+1;
			$stuff .= ",".$result[$fldNam];
		}
		// return $result[$fldNam];\
		return $stuff;
	
	}

	function get_rows($fldNam, $table){
		//$sql = "SELECT DISTINCT `".$fldNam."` FROM `".$table."`";
		$sql = "SELECT MAX(`".$fldNam."`) AS `cntr` FROM `".$table."`";
		$dosql = $this->sqli->query($sql); //mysql_query($sql);
		$res = $dosql->fetch_assoc();
		//$stuff = $dosql->num_rows; //mysql_num_rows($dosql);
		$stuff = $res['cntr'];
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

}

$func = new functions;
//$func->dbhost = $dbhost;
//$func->dbusername = $dbusername;
//$func->dbpassword = $dbpassword;
//$func->dbname = $dbname;
//$func->createDBObj();
$func->sqli = $sqli;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
<head>
   <title>Charts</title>
    <link REL="StyleSheet" HREF="../css/style.css" TYPE="text/css" MEDIA="screen">
   <link href="maxChartStyle/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="links" style="width: 100%; text-align: center; padding: 10px;">
	<h2>MRICF Charts and Statistics</h2>
	<div class="td_menu" style="display: inline-block;"><a href="#" onClick="window.close();">Close</a></div><br />
	<div class="td_menu" style="display: inline-block; width: 200px;"><a href="charts.php">Waybills x Originating RR</a></div> 
	<div class="td_menu" style="display: inline-block; width: 200px;"><a href="charts.php?chart=1">Waybills x Destination RR</a></div>
	<div class="td_menu" style="display: inline-block;"><a href="charts.php?chart=2">Industries x RR</a></div>
	<div class="td_menu" style="display: inline-block;"><a href="charts.php?chart=3">Car Pool x RR</a></div>
	<div class="td_menu" style="display: inline-block;"><a href="charts.php?chart=4">Trains x RR</a></div>
	<br />
	<!-- <a href="charts.php?chart=5">Most Used Cars</a><br /> // -->
    </div>
<!--
    <div id="container" style="width: 720px;">
	<div id="header">
	    <div id="header_left"></div>
	    <div id="header_main">MRICF Charts</div>
	    <div id="header_right"></div>
	</div>

	<div id="main">
// -->
	<?php
	if($chart_typ == 5){
	    $dat = $func->get_arr("car_num","ichange_carsused_index","`id` > 0 ORDER BY COUNT(`car_num`) DESC");
	    $data1 = explode(",",$dat);
	    $cntr = 0;
	    $maxRows = count($data1); //get_rows("id","ichange_carsused_index");
	}else{
	    $dat = $func->get_arr("id","ichange_rr");
	    $data1 = explode(",",$dat);
	    $cntr = 0;
	    $maxRows = $func->get_rows("id","ichange_rr");
	}
			
	if($chart_typ == 0){
	    while($cntr < $maxRows){
		$cntr=$cntr+1;
		if(isset($data1[$cntr])){
		    $t = $func->myTruncate2($data1[$cntr], 5);
		    if(strlen($t) > 0){
			if($func->sel_fld("rr_id_from","ichange_waybill",$data1[$cntr]) > 0){ $data[$t] = $func->sel_fld("rr_id_from","ichange_waybill",$data1[$cntr]); }
		    }
		}
	    }
	    //echo "<pre>"; print_r($data); echo "</pre>";
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
	    $lab = 'Waybills Produced x Originating RR';
/*
	    $wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
	    $mc = new maxChart($data);
	    $mc->displayChart('Waybills Produced x Originating RR',1,$wid,150);

	    echo "<br/><br/>";            
	    //echo "<table style=\"text-align: left;\">".$legend."<tr>";
	    echo "<div style=\"text-align: left;\">".$legend."</div>";
	    for($cntr=1;$cntr<=$maxRows;$cntr++){
		$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
		//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
		if(strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
		if(floatval($cntr/4) == intval($cntr/4)){
		    //echo "</tr><tr>";
		}
	    }
*/
	}
 
	if($chart_typ == 1){
	    while($cntr < $maxRows){
		$cntr=$cntr+1;
		if(isset($data1[$cntr])){
		    $t = $func->myTruncate2($data1[$cntr], 5);
		    if(strlen($t) > 0){
			if($func->sel_fld("rr_id_to","ichange_waybill",$data1[$cntr]) > 0){ 
			    $data[$t] = $func->sel_fld("rr_id_to","ichange_waybill",$data1[$cntr]); 
			}
		    }
		}
	    }
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
	    $lab = 'Waybills Produced x Destination RR';
/*
	    $wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
            $mc = new maxChart($data);
            $mc->displayChart('Waybills Produced x Destination RR',1,$wid,150);

            echo "<br/><br/>";            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
		$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
		//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
		if(strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
		if(floatval($cntr/4) == intval($cntr/4)){
		    //echo "</tr><tr>";
		}
	    }
*/
	}
           
	if($chart_typ == 2){
	    while($cntr < $maxRows){
		$cntr=$cntr+1;
		if(isset($data1[$cntr])){
		    $t = $func->myTruncate2($data1[$cntr], 5);
		    if(strlen($t) > 0){
			if($func->sel_fld("rr","ichange_indust",$data1[$cntr]) > 0){ $data[$t] = $func->sel_fld("rr","ichange_indust",$data1[$cntr]); }
		    }
		}
	    }
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
	    $lab = 'Industries x RR';
/*
 	    $wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
	    $mc = new maxChart($data);
            $mc->displayChart('Industries x RR',1,$wid,150);

            echo "<br/><br/>";            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
		$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
		//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
		if(strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right: 10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
		if(floatval($cntr/4) == intval($cntr/4)){
		    //echo "</tr><tr>";
		}
            }
*/
	}

	if($chart_typ == 3){
	    while($cntr < $maxRows){
		$cntr=$cntr+1;
		if(isset($data1[$cntr])){
		    $t = $func->myTruncate2($data1[$cntr], 5);
		    if(strlen($t) > 0){
			if($func->sel_fld("rr","ichange_cars",$data1[$cntr]) > 0){ $data[$t] = $func->sel_fld("rr","ichange_cars",$data1[$cntr]); }
		    }
		}
	    }
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
	    $lab = 'Car Pool x RR';
/*
	    $wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
            $mc = new maxChart($data);
            $mc->displayChart('Car Pool x RR',1,$wid,150);

            echo "<br/><br/>";
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
		$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
		//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
		if(strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
		if(floatval($cntr/4) == intval($cntr/4)){
		    //echo "</tr><tr>";
		}
	    }
*/
	}

	if($chart_typ == 4){
	    while($cntr < $maxRows){
		$cntr=$cntr+1;
		if(isset($data1[$cntr])){
		    $t = $func->myTruncate2($data1[$cntr], 5);
		    if(strlen($t) > 0){
			if($func->sel_fld("railroad_id","ichange_trains",$data1[$cntr]) > 0){ $data[$t] = $func->sel_fld("railroad_id","ichange_trains",$data1[$cntr]); }
		    }
		}
	    }
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
	    $lab = 'Trains x RR';
/*
	    $wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
            $mc = new maxChart($data);
            $mc->displayChart('Trains x RR',1,$wid,150);

            echo "<br/><br/>";            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
		$td_cs = "background-color: lightgrey;"; 	//if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
		//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
		if(strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
		if(floatval($cntr/4) == intval($cntr/4)){
		    //echo "</tr><tr>";
		}
	    }
*/
	}

	if($chart_typ == 5){
	    while($cntr < $maxRows){
		$cntr=$cntr+1;
		if(isset($data1[$cntr])){
		    $t = $func->myTruncate2($data1[$cntr], 5);
		    if(strlen($t) > 0){
			if($func->sel_fld("car_num","ichange_carsused_index",$data1[$cntr]) > 0){ $data[$t] = $func->sel_fld("car_num","ichange_carsused_index",$data1[$cntr]); }
		    }
		}
	    }
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
	    $lab = 'Most Used Cars';
/*
	    $wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
            $mc = new maxChart($data);
            $mc->displayChart('Most Used Cars',1,$wid,150);

            echo "<br/><br/>";            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left;\">".$legend."</div>";
            for($cntr=1;$cntr<=$maxRows;$cntr++){
		$td_cs = "background-color: lightgrey;"; //	if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
		//if(strlen(qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<td style=\" padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".qry("ichange_rr", $cntr, "id", "report_mark")."</td>"; }
		if(strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; }
		if(floatval($cntr/4) == intval($cntr/4)){
		    //echo "</tr><tr>";
		}
	    }
*/
	}

	$wid = (count(array_keys($data)) > 5 ? intval(count(array_keys($data))*75) : 700);
	if($wid > 1100){ $wid = 1100; }
	?>
    <div id="container" style="width: <?php echo intval($wid+25); ?>px;">
	<div id="header">
	    <div id="header_left"></div>
	    <div id="header_main">MRICF Charts</div>
	    <div id="header_right"></div>
	</div>

	<div id="main">

	<?php 
	    $no_tds = 5; // Number of TDs to display in the Legend part of the Chart.
            $mc = new maxChart($data);
            $mc->displayChart($lab,1,$wid,150);

            echo "<br/><br/>";            
            //echo "<table style=\"text-align: left;\">".$legend."<tr>";
            echo "<div style=\"text-align: left; width: ".$wid."px;\">".$legend."<br />"; //."</div>";
	    $td_cs = "background-color: lightgrey; padding: 5px; border-radius: 6px;"; //	if(floatval($cntr/2) == intval($cntr/2)){ 	$td_cs = "background-color: moccasin;"; }
            for($cntr=1;$cntr<=$maxRows;$cntr++){
		if(in_array($cntr,array_keys($data)) && strlen($qfunc->qry("ichange_rr", $cntr, "id", "report_mark")) > 0){ 
		    echo "<div style=\"display: inline-block; margin: 4px; width: 110px; padding-right:10px; font-size: 10pt; ".$td_cs."\">".$cntr." : ".$qfunc->qry("ichange_rr", $cntr, "id", "report_mark")."</div>"; 
		}
	    }
	    echo "</div>";
	?>
      </div>
      <div id="footer"><a href="http://www.phpf1.com">Powered by PHP F1</a></div>
   </div>
   
</body>
