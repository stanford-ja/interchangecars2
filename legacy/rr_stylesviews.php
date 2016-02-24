<?php
// trains_wps.php requires train_id. 
// Manages auto field contents in ichange_trains and writes back changes to auto field
// when changes saved.
include('db_connect7465.php');

$pgTitle = "Custom Styles and Views for RR";
$kys = array('td','notes','routing','lading','rr_tr','progress'); //@array_keys($auto);
$labels = array("Boxes","Notes","Routing","Lading","Selectors","Progress");
$attribs = array("border","background-color","font-weight","font-size","text-decoration","color","padding","margin");
$elements = 			array("waybill_num","indust_origin_name","indust_dest_name","return_to","status","routing","notes","lading","cars");
$elements_labels = array("Waybill #","Origin Industry","Destination Industry","Return To","Status","Routing","Notes","Lading","Cars");

if(!isset($_COOKIE['rr_sess'])){echo "You are not logged in!"; exit();} 
$wot2do = 0; if(isset($_REQUEST['wot2do'])){$wot2do = $_REQUEST['wot2do'];}
$jsonArr = "";
if(!isset($_REQUEST['submit'])){
	// Initial opener
	$sql = "SELECT `home_disp_v2` FROM `ichange_rr` WHERE `id` = '".$_COOKIE['rr_sess']."'";
	$qry = mysql_query($sql);
	$res = mysql_fetch_array($qry);
	$jsonArr = $res['home_disp_v2'];
}else{
	// Update of forms and creating of JSON array.
	$wot2do = 1;
	$arr = array('styles'=>array(),'elements'=>array());
	//echo "<pre>"; print_r($_POST); echo "</pre>";
	for($i=0;$i<count($_POST['ky']);$i++){
		$arr['styles'][$_POST['ky'][$i]] = array();
		for($k=0;$k<count($attribs);$k++){
			//echo $attribs[$k]."=".(print_r($_POST[$attribs[$k]]))."<br />";
			if(strlen($_POST[$attribs[$k]][$i]) > 0){$arr['styles'][$_POST['ky'][$i]][$attribs[$k]] = $_POST[$attribs[$k]][$i];}
		}
	}
	for($e=0;$e<count($elements);$e++){
		if(isset($_POST[$elements[$e]])){$arr['elements'][] = $elements[$e];}
	}
	$jsonArr = json_encode($arr);
	//echo "<pre>"; print_r($arr); echo "</pre>";
	//exit();
}

// Convert JSON to PHP array
$arr = @json_decode($jsonArr,true);
if(!isset($arr['elements'])){$arr['elements'] = $elements;}
//echo "<pre>"; print_r($arr); echo "</pre>";

// Start selectors + options
$border_arr = array(
	"none",
	"1px solid black",
	"1px solid brown",
	"1px solid #aaa",
	"1px solid red",
	"2px solid yellow"
);
$border_opts = "";
for($i=0;$i<count($border_arr);$i++){$border_opts .= "<option value=\"".$border_arr[$i]."\">".$border_arr[$i]."</option>";}

$bg_arr = array("Transparent","BurlyWood","Brown","Chocolate","Coral","Darkorange","DarkSalmon","Gold","LightGreen","LightPink","Moccasin","PeachPuff","SandyBrown","Wheat");
$bg_opts = "";
for($i=0;$i<count($bg_arr);$i++){$bg_opts .= "<option value=\"".$bg_arr[$i]."\" style=\"background-color:".$bg_arr[$i]."\">".$bg_arr[$i]."</option>";}

$weight_arr = array("normal","bold");
$weight_opts = "";
for($i=0;$i<count($weight_arr);$i++){$weight_opts .= "<option value=\"".$weight_arr[$i]."\">".$weight_arr[$i]."</option>";}

$size_arr = array("8pt","9pt","10pt","11pt","12pt","13pt","14pt","15pt","16pt");
$size_opts = "";
for($i=0;$i<count($size_arr);$i++){$size_opts .= "<option value=\"".$size_arr[$i]."\">".$size_arr[$i]."</option>";}

$decor_arr = array("none","underline","blink");
$decor_opts = "";
for($i=0;$i<count($decor_arr);$i++){$decor_opts .= "<option value=\"".$decor_arr[$i]."\">".$decor_arr[$i]."</option>";}

$color_arr = array("Black","Brown","Chocolate","Green","Maroon","Coral","White");
$color_opts = "";
for($i=0;$i<count($color_arr);$i++){$color_opts .= "<option value=\"".$color_arr[$i]."\" style=\"background-color:".$color_arr[$i]."\">".$color_arr[$i]."</option>";}

$px_arr = array("1px","2px","3px","5px","8px","11px","14px");
$pad_opts = "";
for($i=0;$i<count($px_arr);$i++){$pad_opts .= "<option value=\"".$px_arr[$i]."\">".$px_arr[$i]."</option>";}
$marg_opts = "";
for($i=0;$i<count($px_arr);$i++){$marg_opts .= "<option value=\"".$px_arr[$i]."\">".$px_arr[$i]."</option>";}
// End selectors + options

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
		<?php if($wot2do == 1){ ?>
			function updDetails(){
				window.opener.parent.document.form.home_disp_v2.value = '<?php echo $jsonArr; ?>'; 
				window.close();
			}
		<?php } ?>
		</script>
	</head>
<body<?php if($wot2do == 1){ echo " onload=\"updDetails();\"";} ?>>
<h2><?php echo $pgTitle; ?></h2>
<form name="" method="post" action="rr_stylesviews.php">
<table style="width: 495px;">
	<tr>
		<td class="td_title">Element</td>
		<td class="td_title">Border</td>
		<td class="td_title">BG Color</td>
		<td class="td_title">Font Weight</td>
		<td class="td_title">Font Size</td>
		<td class="td_title">Text Decoration</td>
		<td class="td_title">Text Color</td>
		<td class="td_title">Padding</td>
		<td class="td_title">Margin</td>
		<td>&nbsp;</td>
	</tr>
<?php
for($i=0;$i<count($kys);$i++){
	echo "<tr>";
	echo "<td>".$labels[$i]."<input type=\"hidden\" name=\"ky[]\" value=\"".$kys[$i]."\" /></td>\n";
	echo "<td><select name=\"border[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['border']."\">".@$arr['styles'][$kys[$i]]['border']."</option>".$border_opts."</select></td>\n";
	echo "<td><select name=\"background-color[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['background-color']."\" style=\"background-color:".@$arr['styles'][$kys[$i]]['background-color']."\">".@$arr['styles'][$kys[$i]]['background-color']."</option>".$bg_opts."</select></td>\n";
	echo "<td><select name=\"font-weight[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['font-weight']."\">".@$arr['styles'][$kys[$i]]['font-weight']."</option>".$weight_opts."</select></td>\n";
	echo "<td><select name=\"font-size[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['font-size']."\">".@$arr['styles'][$kys[$i]]['font-size']."</option>".$size_opts."</select></td>\n";
	echo "<td><select name=\"text-decoration[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['text-decoration']."\">".@$arr['styles'][$kys[$i]]['text-decoration']."</option>".$decor_opts."</select></td>\n";
	echo "<td><select name=\"color[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['color']."\" style=\"background-color:".@$arr['styles'][$kys[$i]]['color']."\">".@$arr['styles'][$kys[$i]]['color']."</option>".$color_opts."</select></td>\n";
	echo "<td><select name=\"padding[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['padding']."\">".@$arr['styles'][$kys[$i]]['padding']."</option>".$pad_opts."</select></td>\n";
	echo "<td><select name=\"margin[]\" style=\"font-size: 8pt;\"><option value=\"\">(default)</option><option selected=\"selected\" value=\"".@$arr['styles'][$kys[$i]]['margin']."\">".@$arr['styles'][$kys[$i]]['margin']."</option>".$marg_opts."</select></td>\n";
	echo "</tr>";
}

?>
<tr>
	<td>
</tr>
</table>
<strong>Field display options (a tick or X means display the field)</strong><br />
<table>
<?php for($e=0;$e<count($elements);$e++){ ?>
<tr>
	<td><?php echo $elements_labels[$e]; ?></td>
	<td><input type="checkbox" name="<?php echo $elements[$e]; ?>" <?php if(in_array($elements[$e],@$arr['elements'])){echo "checked=\"checked\" ";} ?>value="1" /></td>
	<?php if($e == 0){echo "<td rowspan=\"".(count($elements)-1)."\"><input type=\"submit\" name=\"submit\" value=\"Submit\" /></td>";} ?>
</tr>
<?php } ?>
</table>
</form>
</body>
</html>