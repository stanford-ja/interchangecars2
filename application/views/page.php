<?php
echo "<div style=\"padding: 10px; margin; 0px; margin-left: 10%; margin-right: 10%;\">";
echo "<h3>Pages list</h3>";
for($i=0;$i<count($fils);$i++){
	$t_arr = explode(".",$fils[$i]);
	$t = ucwords(str_replace("_"," ",$t_arr[0]));
	echo "<a href=\"pages/view/".$t_arr[0]."\">".$t."</a><br />";
}
echo "</div>";
?>