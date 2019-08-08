<?php
// trains_wps.php requires train_id. 
// Manages auto field contents in ichange_trains and writes back changes to auto field
// when changes saved.
//include('sessions.php');		
//include('vars.php');	
include('db_connect7465.php');
//include('query_functions.php');
//include('str_functions.php');

/*
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
*/

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
if(isset($_REQUEST['id']) && !isset($_REQUEST['tid'])){
	// Initial opener
	$sql = "SELECT `train_id`,`waypoints`,`origin`,`destination` FROM `ichange_trains` WHERE `id` = '".$_REQUEST['id']."'";
	$qry = $sqli->query($sql);
	$res = $qry->fetch_assoc(); //mysqli_fetch_array($qry);
	$tid = $res['train_id'];
	$wayp4tr = @json_decode($res['waypoints'], true);
	//@asort($wayp4tr);
	$jsonArr = $res['waypoints'];
	//$max_days = maxDay($jsonArr);
}else{
	// Update of forms and creating of JSON array. Max=30 waypoints
	$tid = $_REQUEST['tid'];
	$wayp4tr = array();
	for($h=0;$h<30;$h++){
		if(isset($_REQUEST['location'.$h])){
			$_REQUEST['location'.$h] = str_replace(", ",",",strtoupper($_REQUEST['location'.$h]));
			if(strlen($_REQUEST['location'.$h]) > 0){
				$tmp = array(
					'LOCATION' => $_REQUEST['location'.$h],
					'TIME' => $_REQUEST['time'.$h]
				);
				//$wayp4tr[$h]['LOCATION'] = $_REQUEST['location'.$h];
				//$wayp4tr[$h]['TIME'] = $_REQUEST['time'.$h];
				$wayp4tr[] = $tmp;
			}
		}
	}
	//@asort($wayp4tr);
	$jsonArr = json_encode($wayp4tr);
	//$max_days = maxDay($jsonArr);
}

$cntr = count($wayp4tr) + 1;
$pgTitle = "Waypoints for train ".$tid;

$txt = "<span style=\"font-size: 9pt;\">Enter or change the location / name of the waypoint in the Location fields and select the relevant times for each location, then click the Submit button.</span>";
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
			var dys = new Array;
						
		<?php if($wot2do == 1){ ?>
			function updTrainDetails(){
				window.opener.parent.document.form.waypoints.value = '<?php echo $jsonArr; ?>'; 
				window.opener.parent.document.getElementById('waypointHTM').innerHTML = '';
				<?php for($wps=0;$wps<count($wayp4tr);$wps++){ 
					echo "window.opener.parent.document.getElementById('waypointHTM').innerHTML += '<div class=\"wb_btn\" style=\"width: auto;\">".$wayp4tr[$wps]['LOCATION']." (".$wayp4tr[$wps]['TIME'].")</div>';\n"; 
				} ?>
				window.close();
			}
		<?php } ?>
		</script>
	</head>
<body<?php if($wot2do == 1){ echo " onload=\"updTrainDetails();\"";} ?>>
<h2><?php echo $pgTitle; ?></h2>
<div style="background-color: antiquewhite; border: 1px solid red; padding: 5px; font-size: 9pt;">
Train Times can be either an integer train sheet order, or a time in 24 hours format such as HHMM or HH:MM. All trains for your railroad need to have the same format for this field for them to be displayed / printed in the proper order.
<hr />
To remove a waypoint from the timetable remove the text in the "Waypoints" field that you want to delete.
</div>
<form name="" method="post" action="train_wps2.php?id=<?php echo $_REQUEST['id']; ?>">
<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
<div style="height: 500px; overflow: auto;">
<table style="width: 495px;">
	<tr>
		<td class="td_title" style="width: 200px;">Waypoints</td>
		<td class="td_title" style="width: 200px;">Times</td>
		<td>&nbsp;</td>
	</tr>
<?php
for($i=0;$i<count($wayp4tr);$i++){
	echo "<tr>";
	echo "<td style=\"width: 200px;\"><input style=\"width: 195px;\" type=\"text\" name=\"location".$i."\" id=\"location".$i."\" value=\"".$wayp4tr[$i]['LOCATION']."\" /></td>\n";
	echo "<td style=\"width: 200px;\"><input style=\"width: 195px;\" type=\"text\" name=\"time".$i."\" id=\"time".$i."\" value=\"".$wayp4tr[$i]['TIME']."\">";
	echo "</td>";
	//if($i==0){echo "<td rowspan=\"".(count($kys)+2)."\">".$txt."</td>";}
	echo "<td><a href=\"javascript:{}\" onclick=\"if(confirm('Are you sure you want to delete this?')){ window.location = '".$_SERVER['PHP_SELF']."?id=\"".$tid."'; }\">Del</a></td>";
	echo "</tr>";
}
echo "<tr>";
echo "<td style=\"width: 200px;\"><input style=\"width: 195px;\" type=\"text\" name=\"location".$i."\" id=\"location".$i."\" value=\"\" /></td></td>\n";
echo "<td style=\"width: 200px;\"><input style=\"width: 195px;\" type=\"text\" name=\"time".$i."\" id=\"time".$i."\" value=\"\">";
echo "</td>";
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
	<td colspan="3"><span style="color: black; font-weight: bold; font-size: 11pt;"><?php echo "<span style=\"color: #777;\">".str_replace(",",", ",$jsonArr); ?></span></span>
</td>
</tr>
</table>
</div>
</form>
</body>
</html>
