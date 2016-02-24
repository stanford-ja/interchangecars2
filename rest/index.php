<?php
/*****
 * Nic's First Restler Gateway
 * Only Working with Gateway Name ATM... ie /rest/index.php/crap/sum/4/5/
*****/

require_once('restler.php');
//require_once('db.php');
spl_autoload_register('spl_autoload');
//require_once('crap.php');

$r = new Restler();
//$r->setSupportedFormats('JsonFormat','XmlFormat');
$r->setSupportedFormats('JsonFormat');
$r->addAPIClass('login');
$r->addAPIClass('rr');
$r->addAPIClass('waybill');
$r->addAPIClass('indust');
$r->handle();

?>
