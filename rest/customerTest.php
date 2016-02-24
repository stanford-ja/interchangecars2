<?php
include('customer.php');
//echo "FART";
$c=new customer();
echo $c->index();
echo "<hr />";
echo $c->greeting();
?>
