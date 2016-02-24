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
?>
			<div style="width: 100%;" class="tbl1">
			<?php if($rr_sess != 9999){ ?>
			<div style="display: table-row;">
				<div style="display: table-cell; width: 30%; padding: 0px;" class="td_menu_title">
				<?php if(isset($_COOKIE['_tz'])){ ?>
					<span class="small_txt" style="font-weight: bold;">Navigation Menu&nbsp;&nbsp;&nbsp;Timezone: <?php echo @$_COOKIE['_tz']; ?></span>
				<?php } ?>
				&nbsp;
				</div>
				<div style="display: table-cell; padding: 0px" class="td_menu_title"><span class="small_txt"><a href="http://www.stanfordhosting.net/interchangecars2/index.php/map/usa">Map</a></span></div>
				<div style="display: table-cell; padding: 0px" class="td_menu_title"><span class="small_txt"><a href="http://www.stanfordhosting.net/interchangecars2/files/MRICF_Manual.pdf">Manual</a></span></div>
				<div style="display: table-cell; padding: 0px" class="td_menu_title"><span class="small_txt"><a href="#" onClick="window.open('http://www.stanfordhosting.net/interchangecars2/legacy/charts.php', '', 'width=600px, height=600px, resizable');">Charts</a></span></div>
				<div style="display: table-cell;" class="td_menu">
				<?php if($rr_sess > 0){
					echo anchor("../rss_xml/".$rr_sess, "RSS", 'title="RSS"'); 
				?>
				<?php }else{echo "&nbsp;";} ?>
				</div>
				<div style="display: table-cell;" class="td_menu">
				<?php if($rr_sess > 0){
					echo anchor("../pages", "Help / FAQs", 'title="Help / FAQs"'); 
				}else{echo "&nbsp;";} ?>
				</div>
				<div style="display: table-cell; padding: 3px; text-align: right; fonr-size: 9pt;" class="td_menu_title">
					<?php if(strpos($_SERVER['REQUEST_URI'],"/home") > 0){ ?>
						<!-- <form id="men_form" name="men_form" action="sessions.php" method="POST" style="display: inline;"> // -->
						<?php echo $whatThe; ?>: 
						<?php 
						if($rr_sess > 0){
							echo anchor("../login/logout", "Logout", 'title="Logout"');
						?>
						<!-- 
						<form id="men_form" name="men_form" action="login/logout" method="POST" style="display: inline;">
						<input type="hidden" name="logout" value="y" />
						<input name="submit" value="Logout" type="submit" class="submit" />
						</form>
						// -->
						<?php
							}else{
						?>
						<?php echo anchor("../login", "Login", 'title="Login"'); ?>
						
						<?php } ?>
						<div id="nwpd" style="display: none;">
							<a href="http://groups.yahoo.com/group/MRICC/msearch?query=NEW+RR+PASSWORD+<?php echo @$paras['new_user_pass_date']; ?>&submit=Search&charset=ISO-8859-1" target="_blank">Click here to see New RR Password</a>
						</div>
					<?php } ?>
				</div>
			</div>
			</div>
			<div style="width: 100%; margin-bottom: 3px;" class="tbl1">
			<div style="display: table-row;">
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../home", "Home", 'title="Home"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../".$wb_lnk, "New WB", 'title="New WB"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../".$rwb_lnk, "Customer POs", 'title="Customer POs"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../".$ca_lnk, "Cars Pool", 'title="Cars Pool"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../aar", "AAR Codes", 'title="AAR Codes"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../".$in_lnk, "Industries", 'title="Industries"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../commod", "Commodities", 'title="Commodities"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../".$tr_lnk, "Trains", 'title="Trains"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../locations", "Locations", 'title="Locations"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../blocks", "Blocks", 'title="Blocks"'); ?></div>
				<div style="display: table-cell;" class="td_menu">
					<?php echo anchor("../rr/edit/0", "Create RR", 'title="Create a new RR" '); ?>
					<!-- 
					<a href="edit.php?type=RR&action=NEW" onMouseOver="document.getElementById('new_rr_info').style.display='block'" onmouseout="document.getElementById('new_rr_info').style.display='none'">
					Create RR
					</a>
					// -->
				</div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../dat/csv_file", "Upload Data", 'title="Upload Data"'); ?></div>
				<div style="display: table-cell;" class="td_menu"><?php echo anchor("../ind40k", "OpSig 40k Indust. DB", 'title="OpSig 40k Indust. DB"'); ?></div>
			</div>
			<?php }else{ ?>
			<div style="display: table-row;">
				<div style="display: table-cell" class="td_menu_title" colspan="4">
					<strong>Application is in <u>New Member</u> mode and is limited to creating a railroad / login, access to Manual and Charts, and Logging Out.<br />
					To get full access you need to create your first railroad, then Logout, then Login as that railroad.</strong>
				</div>
			</div>
			<div style="display: table-row;">
				<div style="display: table-cell;" class="td_menu">
					<?php echo anchor("../rr/edit/0", "Create RR", 'title="Create a new RR" '); ?>
					<!-- 
					<a href="edit.php?type=RR&action=NEW" onMouseOver="document.getElementById('new_rr_info').style.display='block'" onmouseout="document.getElementById('new_rr_info').style.display='none'">
					Create RR
					</a>
					// -->
				</div>
				<div style="display: table-cell;" class="td_menu"><span class="small_txt"><a href="files/MRICF_Manual.pdf">Manual</a></span></div>
				<div style="display: table-cell;" class="td_menu"><a href="#" onClick="window.open('http://www.stanfordhosting.net/interchangecars2/index.php/charts', '', 'width=600px, height=600px, resizable');">Charts</a></div>
				<div style="display: table-cell;" class="td_menu"><a href="sessions.php?logout=1">Logout</a></div>
			</div>
			<?php } ?>
			<?php if($q_select == 1 || isset($_COOKIE['rr_admin'])){ ?>
			<div style="display: table-row;">
				<div style="display: table-cell; width: 100%;" class="td_menu"> 
			<?php if($q_select == 1){
				echo "Quick Select:";
				$q1s = "SELECT `ichange_trains`.`train_id` FROM `ichange_trains` WHERE `ichange_trains`.`railroad_id` = '".$rr_sess."' ORDER BY `ichange_trains`.`train_id`";
				$q1q = mysql_query($q1s);
				echo "<select name=\"qs1\" onChange=\"window.location = 'view.php?type=SWITCHLIST&id=' + this.value;\" style=\"font-size: 7pt;\">";
				echo "<option value=\"\">S/list for train</option>";
				while($q1r = mysql_fetch_array($q1q)){
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
				$q2q = mysql_query($q2s);
				echo "<select name=\"qs2\" onChange=\"window.location = 'edit.php?type=WAYBILL&action=EDIT&id=' + this.value;\" style=\"font-size: 7pt; width: 100px;\">";
				echo "<option value=\"\">Edit Waybill</option>";
				while($q2r = mysql_fetch_array($q2q)){echo "<option value=\"".$q2r['waybill_num']."\">".$q2r['waybill_num']." - ".$q2r['status']."</option>";}
				echo "</select>&nbsp;";

				$q4s = "SELECT `id`,`indust_name` FROM `ichange_indust` WHERE `rr` = '".$rr_sess."' ORDER BY `indust_name`";
				$q4q = mysql_query($q4s);
				echo "<select name=\"qs2\" onChange=\"window.location = 'manage.php?view=indust&trans=EDIT&id=' + this.value;\" style=\"font-size: 7pt; width: 100px;\">";
				echo "<option value=\"\">Industry</option>";
				while($q4r = mysql_fetch_array($q4q)){echo "<option value=\"".$q4r['id']."\">".substr($q4r['indust_name'],0,45)."</option>";}
				echo "</select>&nbsp;";
			}
			?>

			<?php if(isset($_COOKIE['rr_admin'])){
				$rr_adm_lst = "";
				$q_rr_adm = mysql_query("SELECT `id`,`report_mark` FROM `ichange_rr` ORDER BY `report_mark`");
				while($r_rr_adm = mysql_fetch_array($q_rr_adm)){
					$rr_adm_lst .= "<option value=\"".$r_rr_adm['id']."\">".$r_rr_adm['report_mark']."</option> ";
				}
				echo "&nbsp;Admin Direct Links:&nbsp;<select name=\"jumpTo\" onchange=\"window.location = 'index.php?rr_sess=' + this.value;\" style=\"font-size: 7pt;\"><option value=\"\">Railroad</option>".$rr_adm_lst."</select>";
				echo "&nbsp;<a href=\"settings/manage_settings.php\">Settings</a>";
				echo "&nbsp;&nbsp;Current (New RR) PW = ".@$paras['new_user_pass']." (".@$paras['new_user_pass_date'].")"; 
			?>
			<?php } ?>

				</div>
			</div>
			<?php } ?>
			</div>
			
			<div style="display: table; width: 100%; margin-bottom: 3px;" class="tbl1">
			<div style="display: table-row;"><div style="display:table-cell; width: 100%;">
			</div></div>
			</div>
			<?php if(isset($_COOKIE['_mricfadmin']) && isset($this->Generic_model)){
				$rr_adm_lst = "";
				$q_rr_adm = $this->Generic_model->qry("SELECT `id`,`report_mark` FROM `ichange_rr` ORDER BY `report_mark`");
				//while($r_rr_adm = mysql_fetch_array($q_rr_adm)){
				for($rrid=0;$rrid<count($q_rr_adm);$rrid++){
					$rr_adm_lst .= "<a href=\"".WEB_ROOT."/index.php/login/switch_to/".$q_rr_adm[$rrid]->id."\">".$q_rr_adm[$rrid]->report_mark."</a> ";
				}
				echo "<div style=\"display: block; background-color: #7FFFD4; font-size: 9pt; padding: 4px;\">Admin Direct Links:<br />".$rr_adm_lst."</div>";
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
