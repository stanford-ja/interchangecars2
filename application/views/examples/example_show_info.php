<?php
		echo "Record saved<br />";
		echo "<a href=\"index.php\">Home<a>";
		if(strlen(@$link['url']) > 0){echo " | <a href=\"".$link['url']."\">".$link['label']."<a>";}
?>