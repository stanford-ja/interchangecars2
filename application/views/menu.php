<?php
	@session_start();
	/*	
	$rr_sess = 0;
	$sess_option = "";
	$q_select = 0;
	$qry_string = str_replace("loginerr=1","",$_SERVER['QUERY_STRING']);
	*/
	/*
	if(isset($_SESSION['rr_sess'])){
		$rr_sess = $_SESSION['rr_sess'];
	*/

	$sess_option = "";
	$q_select = 0;
	//if(isset($_COOKIE['rr_sess'])){
	if($rr_sess > 0){
		//$rr_sess = $_COOKIE['rr_sess'];
		// $sess_option = "<option selected value=\"".$rr_sess."\">".qry("ichange_rr", $rr_sess, "id", "report_mark")."</option>\n";	
		$sess_option = @$myRR->report_mark; //qry("ichange_rr", $this->rr_sess, "id", "report_mark");
		$q_select = @$myRR->quick_select; //qry("ichange_rr", $this->rr_sess, "id", "quick_select");
		$whatThe = "Logged in as ".@$allRR[$this->input->cookie('rr_sess')]->report_mark;	
		if($rr_sess == 9999){$whatThe = "Logged in as (New RR)";}
	}else{
		$whatThe = "Login to RR";
		for($tmp=0;$tmp<count(@$allRRKys);$tmp++){
			$sess_option .= "<option value=\"".@$allRRKys[$tmp]."\">".@$allRR[$allRRKys[$tmp]]->report_mark." - ".strtoupper(@$allRR[$allRRKys[$tmp]]->owner_name)."</option>\n";
		}
	}
		
	// Link declarations:
	$wb_lnk = "waybill/edit/0";
	$rwb_lnk = "randomwb";
	$in_lnk = "indust";
	$tr_lnk = "trains";
	$ca_lnk = "cars";
	$mv_lnk = "locos";
?>
	<div id="mobMenu" class="td_menu" style="margin-bottom: 5px;">
		<a href="javascript:{}" onclick="document.getElementById('mainMenu').style.display = 'block'; document.getElementById('mobMenu2').style.display = 'block'; document.getElementById('mobMenu').style.display = 'none';">Show Menu</a>
	</div>
	<div id="mobMenu2" class="td_menu" style="margin-bottom: 5px; display: none;">
		<a href="javascript:{}" onclick="document.getElementById('mainMenu').style.display = 'none'; document.getElementById('mobMenu2').style.display = 'none'; document.getElementById('mobMenu').style.display = 'block';">Hide Menu</a>
	</div>
	<div id="mainMenu"> <!-- START OF mainMenu MENU DIV // -->
			<div class="tbl1" style="width: 100%;"> <!-- START OF tbl1 MENU TABLE // -->
			<?php if($rr_sess != 9999){ /* START LOGIN FOR EXISTING MEMBER */ ?>
			<div style="display: table-row;">
				<div style="display: table-cell; width: 100px; padding: 10px;" class="td_menu td_menu_title">
					<?php echo @$phtml.@$rhtml.@$thtml.@$shtml.@$mhtml; /* - TEST OF DROPDOWN FOR RAILROADS AS MOBILE-FRIENDLY RE-DESIGN */ ?>
					<?php if(isset($next_trains) && count($next_trains) > 0){ ?>
					<a href="javascript:{}" onclick="document.getElementById('next_trains_contain').style.display = 'block';">Next Trains</a>
					<div id="next_trains_contain" style="display: none; position: fixed; left: 10px; top: 25px; z-index:99; max-height: 300px; max-width: 100px; overflow: auto;">
						<a href="javascript:{}" onclick="document.getElementById('next_trains_contain').style.display = 'none';">Shrink</a><br />
						Next <?php echo count($next_trains); ?> trains, on Switchlist not Completed: 
						<?php echo "<div class=\"nextTrains\">".implode("</div><div class=\"nextTrains\">",$this->arr['next_trains'])."</div>"; ?>
					</div>
					<?php } ?>
				</div>
				<div style="display: table-cell; padding: 10px; font-size: 13pt;" class="td_menu_title">
					<div style="display: block;">

						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Go to Home page" data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/home", "Home"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Create a new waybill / purchase order." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/".$wb_lnk, "New WB"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Customer P/Orders List." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/".$rwb_lnk, "Cust. POs"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage your Cars Pool." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/".$ca_lnk, "Cars Pool"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage your Motive Power." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/".$mv_lnk, "Locos"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage AAR Car Types Codes." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/aar", "AAR Codes"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage your Industries." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/".$in_lnk, "Industries"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage Commodities." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/commod", "Commodities"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage your Trains." data-balloon-pos="left" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/".$tr_lnk, "Trains"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Manage Locations." data-balloon-pos="left" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/locations", "Locations"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Industry, Motive Power, Car Pool and Train allocations for your Affiliates." data-balloon-pos="left" data-balloon-length="xlarge">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/affiliates/mv", "Affiliates"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Information about the MRICF." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/about", "About"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="The rather clunky old Maps. A new series of maps is being designed using OpenStreetMap. Stay tuned!" data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/map/usa", "Map"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Help and Information Pages." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/pages", "Pages"); ?></div>
						<div class="td_menu" style="display: inline-block;"><!-- <a href="javascript:{}" onclick="window.open = ('<?php echo WEB_ROOT."/legacy/charts.php"; ?>', 'Charts', 'width=600px, height=600px, resizable');">Charts</a> // --><a href="<?php echo WEB_ROOT."/legacy/charts.php"; ?>" target="Charts">Charts</a></div> 
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="Upload a CSV file to update your railroads data." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/dat/csv_file", "Upload"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="View Waybill Images." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/graphics/wbviewall", "WB Images"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="View Railroads List." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/rr", "Railroads"); ?></div>
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="View the Currently Stored Freight List." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT.INDEX_PAGE."/storedfreight", "Stored"); ?></div>
		
						<?php if($rr_sess > 0){ ?>			
						<div class="td_menu" style="display: inline-block;"><span style="float: right;" data-balloon="View and post MRICF Forums and Messaging topics." data-balloon-pos="right" data-balloon-length="large">[?]</span> <?php echo anchor(WEB_ROOT."/forum/", "Forum"); ?></div>
						<div class="td_menu" style="display: inline-block;"><?php echo anchor(WEB_ROOT.INDEX_PAGE."/rr/edit/0", "Create RR"); ?></div>
						<?php } ?>
						<div class="td_menu" style="display: inline-block;"><?php echo anchor(WEB_ROOT.INDEX_PAGE."/ind40k", "OpSig 40k"); ?></div>

						<?php if(strpos($_SERVER['REQUEST_URI'],"/home") > 0){ ?>
						<?php if($rr_sess > 0){ ?>
							<div class="td_menu" style="display: inline-block; background-color: red;"><?php echo anchor(WEB_ROOT.INDEX_PAGE."/login/logout", "Logout", 'style="color: white;"'); ?></div>
						<?php	}else{ ?>
							<div class="td_menu" style="display: inline-block; background-color: red;"><?php echo anchor(WEB_ROOT.INDEX_PAGE."/login", "Login", 'style="color: white;"'); ?></div>				
						<?php } ?>
						<?php } ?>
				
						<?php if($rr_sess > 0){ ?>			
						<select name="rss_sel" onchange="window.location = '<?php echo WEB_ROOT; ?>/rss/' + this.value" style="font-size: 10pt; padding: 4px; border: 1px solid #ccc; border-radius: 4px; background-color: ivory;">
							<option selected="selected">RSS</option>
							<option value="waybills">Waybills</option>
							<option value="porders">P/Orders</option>
						</select>
						<?php } ?>
					</div>
				</div>


			</div>
			
			<?php /* END LOGIN FOR EXISTING MEMBER */ }else{ /* START LOGIN FOR NEW MEMBER (no RR's) */ ?>
			<div style="display: table-row;">
				<div style="display: table-cell" class="td_menu_title" colspan="4">
					<strong>Application is in <u>New Member</u> mode and is limited to creating a railroad / login, access to Manual and Charts, and Logging Out.<br />
					To get full access you need to create your first railroad, then Logout, then Login as that railroad.</strong>
				</div>
			</div>
			<div style="display: table-row;">
				<div style="display: table-cell;" class="td_menu">
					<?php echo anchor(WEB_ROOT.INDEX_PAGE."/rr/edit/0", "Create RR", 'title="Create a new RR" '); ?>
					<!-- 
					<a href="edit.php?type=RR&action=NEW" onMouseOver="document.getElementById('new_rr_info').style.display='block'" onmouseout="document.getElementById('new_rr_info').style.display='none'">
					Create RR
					</a>
					// -->
				</div>
				<div style="display: table-cell;" class="td_menu"><span class="small_txt"><a href="files/MRICF_Manual.pdf">Manual</a></span></div>
				<div style="display: table-cell;" class="td_menu"><a href="#" onClick="window.open('<?php echo WEB_ROOT; ?>/index.php/charts', '', 'width=600px, height=600px, resizable');">Charts</a></div>
				<div style="display: table-cell;" class="td_menu"><a href="sessions.php?logout=1">Logout</a></div>
			</div>
			<?php } ?>
			<?php if($q_select == 1 || isset($_COOKIE['rr_admin'])){ ?>
			<div style="display: table-row;">
				<div style="display: table-cell; width: 100%;" class="td_menu"> 
			<?php if($q_select == 1){
				echo "Quick Select:";
				$q1s = "SELECT `ichange_trains`.`train_id` FROM `ichange_trains` WHERE `ichange_trains`.`railroad_id` = '".$rr_sess."' ORDER BY `ichange_trains`.`train_id`";
				$q1q = mysqli_query($q1s);
				echo "<select name=\"qs1\" onChange=\"window.location = 'view.php?type=SWITCHLIST&id=' + this.value;\" style=\"font-size: 7pt;\">";
				echo "<option value=\"\">S/list for train</option>";
				while($q1r = mysqli_fetch_array($q1q)){
					$tr_cntr = @q_cntr("ichange_waybill", "`train_id` = '".$q1r['train_id']."' AND `status` != 'CLOSED' AND `train_id` != 'NOT ALLOCATED'");
					$wb_on_tr = ""; $sw_styl = "";
					if($tr_cntr > 0){
						$wb_on_tr = " (".$tr_cntr.")";
						$sw_styl = "style=\"font-weight: bold; background-color: #D3D3D3;\" ";
					}
					echo "<option ".$sw_styl."value=\"".$q1r['train_id']."\">".$q1r['train_id'].$wb_on_tr."</option>";
				}
				echo "</select>&nbsp;";
				
				$q2s = "SELECT `status`,`waybill_num` FROM `ichange_waybill` WHERE `status` != 'CLOSED' AND (`rr_id_to` = '".$rr_sess."' OR `rr_id_from` = '".$rr_sess."' OR `rr_id_handling` = '".$rr_sess."' OR `routing` LIKE '%".$sess_option."%') ORDER BY `waybill_num`";
				$q2q = mysqli_query($q2s);
				echo "<select name=\"qs2\" onChange=\"window.location = 'edit.php?type=WAYBILL&action=EDIT&id=' + this.value;\" style=\"font-size: 7pt; width: 100px;\">";
				echo "<option value=\"\">Edit Waybill</option>";
				while($q2r = mysqli_fetch_array($q2q)){echo "<option value=\"".$q2r['waybill_num']."\">".$q2r['waybill_num']." - ".$q2r['status']."</option>";}
				echo "</select>&nbsp;";

				$q4s = "SELECT `id`,`indust_name` FROM `ichange_indust` WHERE `rr` = '".$rr_sess."' ORDER BY `indust_name`";
				$q4q = mysqli_query($q4s);
				echo "<select name=\"qs2\" onChange=\"window.location = 'manage.php?view=indust&trans=EDIT&id=' + this.value;\" style=\"font-size: 7pt; width: 100px;\">";
				echo "<option value=\"\">Industry</option>";
				while($q4r = mysqli_fetch_array($q4q)){echo "<option value=\"".$q4r['id']."\">".substr($q4r['indust_name'],0,45)."</option>";}
				echo "</select>&nbsp;";
			}
			?>

			<?php if(isset($_COOKIE['rr_admin'])){
				$rr_adm_lst = "";
				$q_rr_adm = mysqli_query("SELECT `id`,`report_mark` FROM `ichange_rr` ORDER BY `report_mark`");
				while($r_rr_adm = mysqli_fetch_array($q_rr_adm)){
					$rr_adm_lst .= "<option value=\"".$r_rr_adm['id']."\">".$r_rr_adm['report_mark']."</option> ";
				}
				echo "&nbsp;Admin Direct Links:&nbsp;<select name=\"jumpTo\" onchange=\"window.location = 'index.php?rr_sess=' + this.value;\" style=\"font-size: 7pt;\"><option selected=\"selected\" value=\"0\">Railroad</option>".$rr_adm_lst."</select>";
				echo "&nbsp;<a href=\"settings/manage_settings.php\">Settings</a>";
				echo "&nbsp;&nbsp;Current (New RR) PW = ".@$paras['new_user_pass']." (".@$paras['new_user_pass_date'].")"; 
			?>
			<?php } ?>

				</div>
			</div>
			<?php } ?>
			</div> <!-- END OF tbl1 MENU TABLE // -->
	</div> <!-- START OF mainMenu MENU DIV // -->
				<?php if($rr_sess > 0 && isset($this->Generic_model)){
					$latestts = intval(date('U')-(86400*21));
					$topic_ids = array();
					$forrecent = "";
					$frmsql = "SELECT `ichange_fluxbb_posts`.*, `ichange_fluxbb_topics`.`subject` 
						FROM `ichange_fluxbb_posts` 
						LEFT JOIN `ichange_fluxbb_topics` ON `ichange_fluxbb_posts`.`topic_id` = `ichange_fluxbb_topics`.`id` 
						WHERE `ichange_fluxbb_posts`.`posted` > ".intval(date('U')-(86400*21))." 
						ORDER BY `ichange_fluxbb_posts`.`posted` DESC";
					$frmqry = $this->Generic_model->qry($frmsql);
					for($frmid=0;$frmid<count($frmqry);$frmid++){
						if(!in_array($frmqry[$frmid]->topic_id,$topic_ids)){
							$forrecent .= "<div style=\"display: inline-block; padding: 4px; margin: 1px; background-color: ivory; border: 1px solid #888; border-radius: 4px; max-width: 380px; height: 40px; overflow: hidden;\">
								<span style=\"float: right;\">&nbsp;<a href=\"".WEB_ROOT."/forum/viewtopic.php?id=".$frmqry[$frmid]->topic_id."\" target=\"forumTopicView\">View</a></span><div style=\"display: inline-block; max-width: 320px; max-height: 17px; overflow: hidden;\"><strong>".substr($frmqry[$frmid]->subject,0,60)."</strong></div><br /> 
								".date('Y-m-d H:i',$frmqry[$frmid]->posted)." - <span style=\" max-width: 320px;\">".$this->BBCode->bbcode_to_html($frmqry[$frmid]->message)."</span> (".$frmqry[$frmid]->poster.")</div>";
							$topic_ids[] = $frmqry[$frmid]->topic_id;
						}
					}
					if(strlen($forrecent) > 0){
						echo "<div style=\"display: block; text-align: top; background-color: antiquewhite; border: 1px solid #888; padding: 4px; border-radius: 4px; margin-top: 2px;\">Recent Forum Posts:<br />".$forrecent."</div>";
					}
				}
				?>
			
			<div style="display: table; width: 100%; margin-bottom: 3px;" class="tbl1">
			<div style="display: table-row;"><div style="display:table-cell; width: 100%;">
			</div></div>
			</div>
			<?php if(isset($_COOKIE['_mricfadmin']) && isset($this->Generic_model)){
				$rr_adm_lst = "<select name=\"adm_lst\" onchange=\"window.location = '".WEB_ROOT.INDEX_PAGE."/login/switch_to/' + this.value;\"><option selected=\"selected\" value=\"0\">Select</option>";
				$q_rr_adm = $this->Generic_model->qry("SELECT `id`,`report_mark` FROM `ichange_rr` ORDER BY `report_mark`");
				//while($r_rr_adm = mysqli_fetch_array($q_rr_adm)){
				for($rrid=0;$rrid<count($q_rr_adm);$rrid++){
					//$rr_adm_lst .= "<a href=\"".WEB_ROOT."/index.php/login/switch_to/".$q_rr_adm[$rrid]->id."\">".$q_rr_adm[$rrid]->report_mark."</a> ";
					$rr_adm_lst .= "<option value=\"".$q_rr_adm[$rrid]->id."\">".$q_rr_adm[$rrid]->report_mark."</option>";
				}
				$rr_adm_lst .= "</select>";
				echo "<div style=\"display: block; background-color: #7FFFD4; font-size: 9pt; padding: 4px;\">Admin Direct Links: ".$rr_adm_lst."</div>";
			?>
			<?php } ?>
			<?php if(date('md') == "0609"){ // 0609
				echo "<div style=\"border: 3px solid red; background-color: yellow; padding: 5px; color: red; text-align: center;\">MRICF Anniversary today! ".(date('Y') - 2009)." years.</div>";
				}
			?>
			<div id="new_rr_info" style="z-index: 10; width: 90%; position: absolute; top: 185px; left: 20px; padding: 10px; border: 2px solid black; background-color: yellow; font-size: 8pt; text-align: left; display: none;">
				<!-- <span style="float: right; padding-left: 5px;"><a href="javascript:{}" onclick="document.getElementById('new_rr_info').style.display='none'">[ Close ]</a></span> // -->
				<strong>Creating a New RR</strong><br />
				<?php if($rr_sess == 9999){ ?>
				Click the Create RR link above to create a new railroad, then once the railroad has been saved, click the Logout link to log off. Then use the Login form to log in as your new railroad. 
				Don't forget to read through the Manual available from the main menu so that you know how the application works!  
				<?php }else{ ?>
				To create a new Railroad when you don't already have one set up, you need to log in using the log in form above by selecting the <strong>(New RR)</strong> 
				drop down option, and entering the password (send an email to <strong>james@stanfordhosting.net</strong> to request it), then clicking the <strong>Select</strong> button. 
				Note that after each (New RR) login attempt the New RR login password changes, so it is advisable that you copy and paste the password in the email you receive into the password field in the login form. 
				Then, once you have created the new railroad, log out and then log in as that railroad to add industries, trains, waybills, etc.
				<?php } ?> 
			</div>
