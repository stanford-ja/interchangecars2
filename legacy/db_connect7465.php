<?php
// LIVE SERVER
$dbhost="localhost"; //"db72d.pair.com";
$dbusername="apps"; //"jstan_6_w";
$dbpassword="1C4nDo3s5tuff"; //"Js120767";
$dbname="interchangecars"; //"jstan_general";

// TESTING
$LocTst = $_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'];
if(strpos($LocTst,"www/Applications/") > 0){
	$dbhost="localhost";
	$dbusername="admin";
	$dbpassword="admin";
}
$dbname="jstan2_general";

$dbcnx = mysql_connect($dbhost, $dbusername, $dbpassword) or die(mysql_error());
$seldb = mysql_select_db($dbname);

// BUILD PARAMETER ARRAY.
$param_s = "SELECT `param_name`,`value` FROM `ichange_parameters`";
$param_q = @mysql_query($param_s);
$paras = array();
while($param_r = mysql_fetch_array($param_q)){
	$paras[$param_r['param_name']] = $param_r['value'];
}
?>
