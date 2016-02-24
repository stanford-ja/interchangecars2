<h1>Database List</h1>
<h3>Last <?php echo $this->last_num; ?></h3>
<ul>
<?php 
foreach ($query as $item):
	echo "<li> <a href=\"../example/a_form/".$item->id."\">[ Edit ]</a> ".$item->report_mark." - ".strtoupper($item->rr_name)." </li>";
endforeach;
?>
</ul>
<hr/>
<h3>All records</h3>
<ul>
<?php 
foreach ($query2 as $item2):
	echo "<li> <a href=\"../example/a_form/".$item2->id."\">[ Edit ]</a> ".$item2->report_mark." - ".strtoupper($item2->rr_name)." </li>";
endforeach;
?>
</ul>
