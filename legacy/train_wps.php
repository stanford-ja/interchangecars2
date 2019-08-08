<?php
// trains_wps.php requires train_id. 
// Manages auto field contents in ichange_trains and writes back changes to auto field
// when changes saved.
//include('sessions.php');		
//include('vars.php');	
include('db_connect7465.php');
//include('query_functions.php');
//include('str_functions.php');

function dayOpts($dy=0){
	// build select options 1 thru 30.
	$dyOpts="";
	for($d=0;$d<31;$d++){
		$sel = "";
		if($dy == $d){$sel = " selected=\"selected\"";}
		$dyOpts .= "<option value=\"".$d."\"".$sel.">".$d."</option>\n";
	}
	return $dyOpts;
}

/*
function maxDay($arr){
	// Interegate json array and rturns highest day value.
	$jsonArr = @json_decode($arr, true);
	$kys = @array_keys($jsonArr);
	for($i=0;$i<count($kys);$i++){
		if($jsonArr[$kys[$i]] > $maxDy){$maxDy = $jsonArr[$kys[$i]];}
	}
	return $maxDy;
}
*/

if(!isset($_COOKIE['rr_sess'])){echo "You are not logged in!"; exit();} 
$wot2do = 0; if(isset($_REQUEST['wot2do'])){$wot2do = $_REQUEST['wot2do'];}
$jsonArr = "";
if(isset($_REQUEST['id'])){
	// Initial opener
	$sql = "SELECT `train_id`,`auto`,`destination` FROM `ichange_trains` WHERE `id` = '".$_REQUEST['id']."'";
	$qry = mysqli_query($sql);
	$res = mysqli_fetch_array($qry);
	$tid = $res['train_id'];
	$auto = @json_decode($res['auto'], true);
	@asort($auto);
	$jsonArr = $res['auto'];
	$dest = $res['destination'];
	//$max_days = maxDay($jsonArr);
}else{
	// Update of forms and creating of JSON array. Max=30 waypoints
	$dest = $_REQUEST['destination'];
	$tid = $_REQUEST['tid'];
	$auto = array();
	for($h=0;$h<30;$h++){
		if(isset($_REQUEST['location'.$h])){
			$_REQUEST['location'.$h] = str_replace(", ",",",strtoupper($_REQUEST['location'.$h]));
			if(strlen($_REQUEST['location'.$h]) > 0){
				$auto[$_REQUEST['location'.$h]] = $_REQUEST['days'.$h];
			}
		}
	}
	@asort($auto);
	$jsonArr = json_encode($auto);
	//$max_days = maxDay($jsonArr);
}

$kys = @array_keys($auto);
$cntr = count($kys) + 1;

$txt = "<span style=\"font-size: 9pt;\">Enter or change the location / name of the waypoint in the Location fields and select the relevant days it will take to travel from the train's origin to each waypoint, then click the Submit button.</span>";
?>
<html>
	<head>
		<title><?php echo $pgTitle; ?></title>
		<link REL="StyleSheet" HREF="../css/style.css" TYPE="text/css" MEDIA="screen">
		<link REL="StyleSheet" HREF="../css/print.css" TYPE="text/css" MEDIA="print">
		<link REL="StyleSheet" HREF="../css/mobile.css" TYPE="text/css" MEDIA="handheld">
		<meta name="generator" content="Bluefish 2.2.2" >
		<meta name="author" content="James" >
		<meta name="keywords" content="model, railroad, railway, freight, car, interchange, application, waybill, train sheet, rollingstock">
		<meta name="description" content="The MRICF is a Model Railroad Virtual Freight and Cars Interchange Application with Waybills, Industries, Train Sheets, Rollingstock management and more">
		<script language="javascript" type="text/javascript">
			var dest = '<?php echo $dest; ?>';
			var dys = new Array;
			<?php for($d=0;$d<count($kys);$d++){echo "dys[".$d."] = ".$auto[$kys[$d]].";\n";} ?>
						
		<?php if($wot2do == 1){ ?>
			function updTrainDetails(){
				window.opener.parent.document.form.auto.value = '<?php echo $jsonArr; ?>'; 
				window.close();
			}
		<?php } ?>
		</script>
	</head>
<body<?php if($wot2do == 1){ echo " onload=\"updTrainDetails();\"";} ?>>
<h2>Waypoints for Auto Train <?php echo $tid; ?></h2>
<form name="" method="post" action="train_wps.php">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<input type="hidden" name="max_days" value="<?php //echo $may_day; ?>" />
<input type="hidden" name="destination" id="destination" value="<?php echo $dest; ?>" />
<div style="height: 500px; overflow: auto;">
<table style="width: 495px;">
	<tr>
		<td class="td_title">Waypoints</td>
		<td class="td_title">Days<br /><span style="font-size: 9pt;">(to reach Waypoint)</span></td>
		<td>&nbsp;</td>
	</tr>
<?php
for($i=0;$i<count($kys);$i++){
	echo "<tr>";
	echo "<td><input name=\"location".$i."\" id=\"location".$i."\" value=\"".$kys[$i]."\" /></td>\n";
	echo "<td><select name=\"days".$i."\" id=\"days".$i."\">";
	echo dayOpts($auto[$kys[$i]]);
	echo "</select></td>";
	if($i==0){echo "<td rowspan=\"".(count($kys)+2)."\">".$txt."</td>";}
	echo "</tr>";
}
echo "<tr>";
echo "<td><input name=\"location".$i."\" id=\"location".$i."\" value=\"\" /></td></td>\n";
echo "<td><select name=\"days".$i."\" id=\"days".$i."\">";
echo dayOpts();
echo "</select></td>";
echo "</tr>";

?>
<tr>
	<td colspan="2">
	<select name="wot2do">
		<option value="0" selected="selected">Stay in window</option>
		<option value="1">Close and update</option>
	</select>&nbsp
	<input type="submit" name="submit" value="Submit" />
	</td>
</tr>
<tr>
	<td colspan="3"><span style="color: black; font-weight: bold; font-size: 11pt;"><?php echo "Make sure to include the DESTINATION ( <a href=\"javascript:{}\" onclick=\"document.getElementById('location".$i."').value = '".str_replace(", ",",",$dest)."';\">".$dest."</a> ) as the last entry with the MAXIMUM value for Days selector!<br /><span style=\"color: #777;\">".str_replace(",",", ",$jsonArr); ?></span></span>
</td>
</tr>
</table>
</div>
</form>
</body>
</html>
