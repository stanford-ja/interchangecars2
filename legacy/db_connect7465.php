<?php
// LIVE SERVER
$dbhost="db150c.pair.com"; // localhost"; //"db72d.pair.com";
$dbusername="jstan2_2_w"; //"apps"; //"jstan_6_w";
$dbpassword="Js120767"; //"1C4nDo3s5tuff"; //"Js120767";
//$dbname="interchangecars"; //"jstan_general";

// TESTING
$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
if(strpos($LocTst,"www/html/Applications/") > 0){
	$dbhost="localhost";
	$dbusername="admin";
	$dbpassword="admin";
}
$dbname="jstan2_general";

//$dbcnx = mysql_connect($dbhost, $dbusername, $dbpassword) or die(mysql_error());
//$seldb = mysql_select_db($dbname);
$dbcnx = mysqli_connect($dbhost, $dbusername, $dbpassword,$dbname);
$sqli = new mysqli($dbhost, $dbusername, $dbpassword,$dbname);

// BUILD PARAMETER ARRAY.
$param_s = "SELECT `param_name`,`value` FROM `ichange_parameters`";
$param_q = $sqli->query($param_s); //mysqli_query($param_s);
$paras = array();
//while($param_r = mysqli_fetch_assoc($param_q)){
while($param_r = $param_q->fetch_assoc()){
	$paras[$param_r['param_name']] = $param_r['value'];
}
?>
