<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<title><?php echo @$title; ?></title>
<!-- <script src="http://code.jquery.com/jquery-latest.js"></script> // -->
<script src="../js/jquery-1.8.2.min.js"></script>
<script>
$(document).ready(function(){
<?php 
	// echo $this->javascript->click('#trigger', $this->javascript->fadeOut('#biff', 'normal')); 
	echo $this->javascript->click('#biff1', $this->javascript->fadeOut('#biff1', 'normal',''));
	//echo $this->jquery->slideUp(target, optional speed, optional extra information);
	echo $this->javascript->click('#link2', $this->javascript->slideUp('#biff2', 'slow'));
	echo $this->javascript->click('#link3', $this->javascript->slideDown('#biff2', 'slow'));
	
	$param1 = array('width' => "70%", 'opacity' => 0.7, 'marginLeft' => "0.6in", 'fontSize' => "2em", 'borderWidth' => "10px");
	$param2 = array('width' => "auto", 'opacity' => 1.0, 'marginLeft' => "0.0in", 'fontSize' => "1em", 'borderWidth' => "0px");
	echo $this->javascript->hover('#block_anime', $this->javascript->animate('#block_anime', $param1, 1500), $this->javascript->animate('#block_anime', $param2, 1500));
?>
});
</script>
<style>
	#biff1 {cursor: pointer;}
</style> 
</head>
<body>
<div style="font-size: 24pt; color: maroon; padding: 30px; background-color: lightskyblue;">
<?php echo @$header; ?>
</div>
