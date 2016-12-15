<?php
// Home view
//echo $_SERVER['REQUEST_URI'];
//echo "<pre>"; print_r($myRR); echo "</pre>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- <!DOCTYPE html> // -->
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
		<!-- 
		<script type="text/javascript" src="<?php echo JS_ROOT; ?>/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="<?php echo JS_ROOT; ?>/jquery.autocomplete.min.js"></script>
		// -->
		<!-- 
	<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=true&amp;key=ABQIAAAAm_VCXnhfjstxzItlLyN4mBTquJ3XAnB-O2ZhZYp82wRvGEX5YxQbuUOxizYh2Df1SzfR-wjHVa5-HQ" type="text/javascript"></script>
	// -->
	<?php 
	
	if(strpos($_SERVER['REQUEST_URI'],"/edit") > 0 && isset($tinymce_flds)){
		$tiny_mce_arr['flds'] = $tinymce_flds; // comma separated list of fields to use TinyMCE in.
		$tiny_mce_arr['back_path'] = str_replace(array("/apps/interchangecars2","/Applications/"),"/",WEB_ROOT);
		$tiny_mce_arr['files_dir'] = DOC_ROOT."/images";
		//$tiny_mce_arr['mode'] = Mode for window (textareas OR exact) (OPTIONAL).
		/*
		$tiny_mce_arr['templates'] = multi dim array of templates to include (OPTIONAL):
			[#]['title'] = title of template
			[#]['src'] = Source file relative to INCLUDING file, not TinyMCE!
			[#]['description'] = Description of template
		*/
		/*
		$tiny_mce_arr['theme_advanced_buttons'][0] : Theme Buttons 1 (OPTIONAL)
		$tiny_mce_arr['theme_advanced_buttons'][1] : Theme Buttons 2 (OPTIONAL)
		$tiny_mce_arr['theme_advanced_buttons'][2] : Theme Buttons 3 (OPTIONAL)
		$tiny_mce_arr['theme_advanced_buttons'][3] : Theme Buttons 4 (OPTIONAL)
		*/
		include(str_replace("/apps/interchangecars2","/common",DOC_ROOT)."/tiny_mce/tinymce_init.php");
	}
	?>	
	<?php if(strpos($_SERVER['REQUEST_URI'],"waybill/edit") < 1){ ?>
	<?php } ?>
	<!-- <script type='text/javascript' src='http://code.jquery.com/jquery-latest.js'></script> // -->
	<!-- <script type='text/javascript' src='<?=JS_ROOT?>/jquery-1.8.2.min.js'></script> // -->
	<script type="text/javascript">
	function mess_win(id){
		win = window.open("messaging/email/"+id, "", "width=400px, height=400px, resizable");
	}

	function divToggle(d){
		// dvs = array of divs to close.
		dvs = new Array();
		<?php //echo $divJS; ?>
	
		for(i=0;i<dvs.length;i++){
			dnam = dvs[i];
			document.getElementById(dnam).style.display = 'none';
		}
	
		if(d.length > 0){eval("document.getElementById('" + d + "').style.display = 'block';");}
	}

	function setAddr(addr){
		//addr = document.getElementById('street_number').value + ' ';
		//addr = addr + document.getElementById('street').value + ', ';
		//addr = addr + document.getElementById('suburb').value + ', ';
		//addr = addr + document.getElementById('state').value + ', ';
		//addr = addr + document.getElementById('postode').value;
		return addr;
	}

	function hideEle(e){
		document.getElementById(e).style.display = 'none';
	}

	function showEle(e){
		document.getElementById(e).style.display = 'block';
	}

	function winOpn(u,w,h){
		// u = url, w=width, h=height
		var opt = "width="+w+",height="+h;
		//alert(u+' '+opt);
		window.open(u,"MRICFWindow",opt);
	}

	<?php if(file_exists(@$script_file)){include($script_file); } ?>

	$(document).ready(function(){
		$("#search_expand").click(function(){
			$("#search").slideDown("slow");
			document.getElementById('search').style.display = 'block';
			document.getElementById('search').style.width = '280px';
			return false;
		});

		$("#search_shrink").click(function(){
			$("#search").slideUp("slow");
			return false;
		});
	});
	
	</script>
	<?php
	?>
	<script language="javascript" type="text/javascript" src="<?=JS_ROOT?>/common.js"></script>
	<!-- 
	<script type='text/javascript' src='js/jquery/jquery.simplemodal.js'></script>
	<script type='text/javascript' src='js/jquery/basic.js'></script>
	// -->
	<?php if(strpos($_SERVER['REQUEST_URI'],"waybill/edit") < 1){ ?>
	<script>
	$(document).ready(function(){
	(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	});
	
	$(document).ready(function(){
		!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
	});
	</script>
	<?php } ?>
	<style>
	<?php if(isset($home_view_settings['styles'])){
		//echo "<style>";
		echo ".td1, .td2 {";
		$td_arr = array_keys($home_view_settings['styles']['td']);
		for($hsi=0;$hsi<count($td_arr);$hsi++){echo $td_arr[$hsi].": ".$home_view_settings['styles']['td'][$td_arr[$hsi]].";";}
		echo "}\n";
		echo ".notes {";
		$nt_arr = array_keys($home_view_settings['styles']['notes']);
		for($hsi=0;$hsi<count($nt_arr);$hsi++){echo $nt_arr[$hsi].": ".$home_view_settings['styles']['notes'][$nt_arr[$hsi]].";";}	
		echo "}\n";	
		echo ".routing {";
		$rt_arr = array_keys($home_view_settings['styles']['routing']);
		for($hsi=0;$hsi<count($rt_arr);$hsi++){echo $rt_arr[$hsi].": ".$home_view_settings['styles']['routing'][$rt_arr[$hsi]].";";}	
		echo "}\n";	
		echo ".lading {";
		$la_arr = array_keys($home_view_settings['styles']['lading']);
		for($hsi=0;$hsi<count($la_arr);$hsi++){echo $la_arr[$hsi].": ".$home_view_settings['styles']['lading'][$la_arr[$hsi]].";";}	
		echo "}\n";	
		echo ".rr_tr {";
		$rr_arr = array_keys($home_view_settings['styles']['rr_tr']);
		for($hsi=0;$hsi<count($rr_arr);$hsi++){echo $rr_arr[$hsi].": ".$home_view_settings['styles']['rr_tr'][$rr_arr[$hsi]].";";}	
		echo "}\n";	
		echo ".progress {";
		$pr_arr = array_keys($home_view_settings['styles']['progress']);
		for($hsi=0;$hsi<count($pr_arr);$hsi++){echo $pr_arr[$hsi].": ".$home_view_settings['styles']['progress'][$pr_arr[$hsi]].";";}	
		echo "}\n";	
		//echo "</style>";
	} ?>
	</style>
	</head>
	<body>
	<?php if(isset($_COOKIE['rr_sess']) && $_COOKIE['rr_sess'] > 0 && file_exists(".git/config") && $_SERVER['SERVER_NAME'] == "localhost"){
		echo "<br /><span style=\"font-size: 14pt; color: red; font-weight: bold;\">GIT REPO in use! Make sure to commit changes to repo after changes made.</span>";
	} ?>
	<div id="fb-root"></div>
	<script>
	/*
	$(document).ready(function(){
	(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
	});
	
		$(document).ready(function(){
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
			});
	*/
	</script>
		<?php if(strpos($_SERVER['REQUEST_URI'],"waybill/edit") < 1){ ?>
		<span style="float: left;">
		<?php if(strpos("a".@$myRR[0]->social,"facebook") > 0){ ?>
			<fb:like href="<?php echo WEB_ROOT; ?>/index.php/home" send="false" layout="button_count" width="100" show_faces="true">
			</fb:like><br />
		<?php } ?>
		<?php if(strpos("a".@$myRR[0]->social,"twitter") > 0){ ?>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo WEB_ROOT; ?>/index.php/home" data-text="MRICF Interchange Cars Application V2.0" style="padding: none; margin: 0px; width: 100px; display: inline;">Tweet</a>
		<?php } ?>
			<script>
			/*
			$(document).ready(function(){
			!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
			});
			*/
			</script>
		</span>
		<!-- 
		<span style="float: right;">
		<g:plusone size="small" annotation="inline" width="100" href="<?php echo WEB_ROOT; ?>/index.php/home"></g:plusone>
		</span>
		// -->
		<?php } ?>
	<?php if(isset($pgTitle)){ ?><h2 style="text-align: right;"><?php echo $pgTitle; ?></h2><?php } ?>
