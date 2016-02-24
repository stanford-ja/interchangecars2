<?
include('crap.php');

$c = new crap();
$c->index();
$c->hello();
echo "Sum of 3,7 = ".$c->sum(3,7)."<br />";
echo "Multiple of x,x =".$c->multiply()."<br />";
echo "End of Gateway File";

?>
