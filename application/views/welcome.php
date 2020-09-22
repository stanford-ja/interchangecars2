<?php
// Home view
//echo $_SERVER['REQUEST_URI'];
//echo "<pre>"; print_r($myRR); echo "</pre>";
/* 
	$msCntr and $poCntr are set via post_controller_constructor hook 
	NotificationHook::getMessageCntr() &
	NotificationHook::getPOCntr()
	in application/hooks/messages.php 
*/

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
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<?php if(isset($redirect)){ echo "<meta http-equiv=\"refresh\" content=\"0;URL='".$redirect."'\" />"; } ?>
		<!--
		<link rel="stylesheet" href="<?=CSS_ROOT?>/skeleton/layout.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?=CSS_ROOT?>/skeleton/base.css" type="text/css" media="screen">
		// -->
		<link rel="stylesheet" href="<?=CSS_ROOT?>/skeleton/skeleton.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?=CSS_ROOT?>/style.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?=CSS_ROOT?>/balloon.css" type="text/css" media="screen">
		<link rel="stylesheet" href="<?=CSS_ROOT?>/print.css" type="text/css" media="print">
		<!-- <link rel="stylesheet" href="<?=CSS_ROOT?>/mobile.css" type="text/css" media="handheld"> // -->
		<meta name="generator" content="Bluefish 2.2.2" >
		<meta name="author" content="James" >
		<meta name="keywords" content="model, railroad, railway, freight, car, freight, forwarding, interchange, application, waybill, train sheet, rollingstock, management">
		<meta name="description" content="The MRICF is a Model Railroad Virtual Freight and Cars Forwarding Application with Waybills, Industries, Train Sheets, Rollingstock management and more">
		<script type="text/javascript" src="<?php echo JS_ROOT; ?>/jquery-1.8.2.min.js"></script>
	<script language="javascript" type="text/javascript">

	$(document).ready(function(){
	});
	
	</script>

	<style>
		body {
			padding: 50px;
		}
	</style>
	</head>
	<body>
	<?php if(isset($pgTitle)){ ?>
	<h2 style="text-align: right;">
	<span class="small_txt" style="float:left; font-weight: bold; text-align: left;">
		<?php if(isset($_COOKIE['_tz'])){ ?>
		<?php echo @$_COOKIE['_tz']; ?>
		<?php } ?>
	</span>
	<?php echo $pgTitle; ?>
	</h2>
	<?php } ?>
	<h3>Virtual Model Railway Waybill Interchanging Made Easy</h3>
	<p>
		If you are looking for a way to virtually interchange waybills and freight between your 
		model railroad and other model railroads without having to physically send you expensive model cars 
		to another person (which, let's face it, is a <strong>really bad</strong> idea) then the 
		MRICF could be exactly what you are looking for.
	</p>
	<p>
		The MRICF allows model railroad owners to interactively and virtually send and receive waybill and freight 
		shipments between their model railroad and any other railroads that is in the MRICF application. 
	</p>
	<p>
		The MRICF is a Web based application, so there is no application or program to download to 
		your computer, and you can use it anywhere you have an internet connection. 
		If can be used on a tablet or phone (although it displays best on a tablet or bigger screen). 
	</p>
	<p>
		The application includes the following features:
		<ul>
			<li>Waybills</li>
			<li>Switchlists</li>
			<li>Management of Industries</li>
			<li>Management of Cars</li>
			<li>Management of Trains</li>
			<li>Management of Commodities for waybills</li>
			<li>Management of Member Railroad settings</li>
			<li>Forum for use by Member Railroads</li>
		</ul>
	</p>
	<p>
		Access to this application's various features requires allowing other member railroads to virtually interchange freight with your modeol railroad and joining the following Groups.io groups:
		<ul>
			<li><a href="https://groups.io/g/MRICC-Virtual-Interchange-Ops1/topics" target="mricc1">MRICC-Virtual-Interchange-Ops1</a> - the group for general virtual interchange messaging.</li>
			<li><a href="https://groups.io/g/MRICC/topics" target="mricc2">MRICC-2</a> - the group for virtual waybill activity.</li>
		</ul>
		Joining these 2 groups and remaining in them, and access to the MRICF, is subject to the policies and rules of the two groups and Groups.io.
	</p>
	<input type="button" value="Go to MRICF" style="background-color: red; color: yellow; font-weight: bold; padding: 15px; border-radius: 10px;" onclick="window.location = '<?php echo WEB_ROOT.INDEX_PAGE; ?>/home';" />
	

</body>
</html>
