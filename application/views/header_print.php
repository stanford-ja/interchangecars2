<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
?>
<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" itemscope itemtype="http://schema.org/Article">
	<head>
		<title><?php echo $pgTitle; ?></title>
		<meta charset="utf-8">
		<meta name="generator" content="Bluefish 2.2.2" >
		<meta name="author" content="James" >
		<link rel="stylesheet" href="<?=CSS_ROOT?>/print.css" type="text/css">
	<style>
		table { width: 100%; margin: 0px; }
	</style>
	</head>
	<body>
		<div class="dontprint" style="background-color: yellow; padding: 10px; border-radius: 8px;">
		<input type="button" onclick="window.print()" value="Print" />
		<p>Note: the way the page is rendered below may not be the same as how the page will print.</p>
		<p>Please use the Print Preview feature to check how the print output will look.</p>
		</div>
