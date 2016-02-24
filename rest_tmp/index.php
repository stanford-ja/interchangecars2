<?php
require_once('restler.php');
//require_once('db.php');
spl_autoload_register('spl_autoload');
//require_once('crap.php');

$r = new Restler();
//$r->setSupportedFormats('JsonFormat','XmlFormat');
$r->setSupportedFormats('JsonFormat');
//$r->addAPIClass('login');
if(strpos($_SERVER['REQUEST_URI'],"/rr") > 0){$r->addAPIClass('rr');}
if(strpos($_SERVER['REQUEST_URI'],"/waybill") > 0){$r->addAPIClass('waybill');}
if(strpos($_SERVER['REQUEST_URI'],"/indust") > 0){$r->addAPIClass('indust');}
if(strpos($_SERVER['REQUEST_URI'],"/jmri") > 0){$r->addAPIClass('jmri');}
$r->handle();

?>
