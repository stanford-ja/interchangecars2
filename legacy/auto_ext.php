<?php
// Reads ichange_auto table and generates entries in progress table when necessary.
// Also randomly chooses a waybill from the ichange_randomwb records, weights $probablity:1 against choosing one. 

	function qry($tbl, $data, $ky, $fld){
		// Suitable to return ONE field of the db table, where the field name and data to search for are provided.
		// $tbl = the table to search in.		
		// $data = the data string to search for.
		// $ky = the name of the field to search in.
		// $fld = Field name to return value of.
		// $ret = Returned value of the function.
		$sql_com = "SELECT * FROM `".$tbl."` WHERE `".$ky."` = '".$data."' LIMIT 1";
		$dosql_com = mysql_query($sql_com);
		$ret = "";
		while($resultcom = mysql_fetch_array($dosql_com)){			
			$ret = $resultcom[$fld];				
		}
		
		return $ret; //Value to return.
	}

	include('db_connect7465.php');	
	$mess = "";

	// Start removal of waybill_images/ files greater than 6 months old.
	$mths = 6;
	$dt_now = date('u');
	$dir = "..";
	$wb_files = scandir ( $dir.'/waybill_images/'); //, SCANDIR_SORT_ASCENDING);// [, resource $context ]] );
	for($i=0;$i<count($wb_files);$i++){
		if($wb_files[$i] != "." && $wb_files[$i] != ".."){
			if(filemtime ( $dir.'/waybill_images/'.$wb_files[$i]) < ($dt_now - (60*60*24*30*$mths))){
				$mess .= $wb_files[$i]." - ".date('Y-m-d H:i',filemtime ( $dir.'/waybill_images/'.$wb_files[$i]))." DELETED <br />";
			}
		}
	}
	// Start removal of waybill_images/ files.

	// Start Auto Train Updater
	$t_day = date('Y-m-d');
	//$t_day = "2010-01-02";
	$sql = "SELECT `ichange_auto`.*, 
		`ichange_waybill`.`indust_origin_name` AS `w_indust_origin_name`,  
		`ichange_waybill`.`indust_dest_name` AS `w_indust_dest_name`,  
		`ichange_waybill`.`return_to` AS `w_return_to`, 
		`ichange_waybill`.`rr_id_to` AS `w_rr_id_to`, 
		`ichange_waybill`.`rr_id_from` AS `w_rr_id_from`, 
		`ichange_waybill`.`status` AS `w_status`, 
		`ichange_waybill`.`progress` AS `w_progress`, 
		`ichange_waybill`.`other_data` AS `w_other_data` 
		FROM `ichange_auto` 
		LEFT JOIN `ichange_waybill` ON `ichange_auto`.`waybill_num` = `ichange_waybill`.`waybill_num` 
		WHERE `ichange_auto`.`act_date` <= '".$t_day."' ORDER BY `ichange_auto`.`description`, `ichange_auto`.`id`";

	$qry = mysql_query($sql);
	$render_fld = "";
	$prev_spotted_wb = 0;
	while($res = mysql_fetch_array($qry)){

		$dt = $res['act_date'];
		$wp = $res['waypoint'];
		$ti = $res['train_id'];
		$wb = $res['waybill_num'];
		$desc = $res['description'];
		$ret_to = $res['w_return_to'];
		$oth_data_j = $res['w_other_data'];
		$prog = array(); //json_decode($res['w_progress'], true); - COMMENTED OUT 2016-03-02 JS
		if(strlen($ret_to) < 1){$ret_to = $res['w_indust_origin_name'];}
		
		// Set progress and waybill data.
		//$t = "*AUTO GENERATED* - CARS ON WAYBILL ".$wb." PASSED THROUGH <strong>".$wp."</strong> IN TRAIN <strong>".$ti."</strong>.";
		$t = "*AUTO GENERATED* - CARS ON WAYBILL ".$wb." ARE ALLOCATED TO / IN TRAIN <strong>".$ti."</strong> WHICH PASSED THROUGH <strong>".$wp."</strong> TODAY.";
		$wb_stat = $res['w_status']; //"IN TRANSIT";
		if($wb_stat != "IN TRANSIT"){$wb_stat = "AT ".$wp;}
		$train = "AUTO TRAIN"; 
		if($desc == "UNLOADED"){
			$oth_data = @json_decode($oth_data_j,true);
			if(isset($oth_data['orig_ind_op']) && isset($oth_data['dest_ind_op'])){$oth_data['orig_ind_op'] = $oth_data['dest_ind_op'];}
			if(isset($oth_data['dest_ind_op'])){unset($oth_data['dest_ind_op']);}
			$train = $ti;
			$t = "*AUTO GENERATED* - CAR/S ON WAYBILL ".$wb." <strong>UNLOADED</strong> AND READY FOR NEXT PART OF JOURNEY. ORIGIN / DESTINATION swapped, LADING set to MT.";
			$wb_stat = $desc;
			// Swap sending and receiving rr & indust. set lading to empty.
			$oth_flds = ", `indust_origin_name` = '".$res['w_indust_dest_name']."'";
			//$oth_flds .= ", `indust_dest_name` = '".$res['w_indust_origin_name']."'";
			$oth_flds .= ", `indust_dest_name` = '".$ret_to."'";
			$oth_flds .= ", `lading` = 'MT'";
			$oth_flds .= ", `rr_id_from` = '".$res['w_rr_id_to']."'";
			$oth_flds .= ", `rr_id_to` = '".$res['w_rr_id_from']."'";
			$oth_flds .= ", `other_data` = '".json_encode($oth_data)."'";
		}
		if($desc == "SPOTTED"){
			$train = "NOT ALLOCATED"; 
			$auto_rem_qry = mysql_query("SELECT COUNT(waybill_num) AS cntr FROM ichange_auto WHERE waybill_num = '".$wb."' AND train_id != '".$ti."' LIMIT 1");
			while($auto_rem_res = mysql_fetch_array($auto_rem_qry)){
				if($auto_rem_res['cntr'] > 0){ $train = "AUTO TRAIN"; } 
			}

			$t = "*AUTO GENERATED* - CARS ON WAYBILL ".$wb." SPOTTED AT <strong>".$wp."</strong> BY TRAIN <strong>".$ti."</strong>.";
		}
		$oth_flds .= ", `train_id` = '".$train."'";
		$t .= " (added by cron)";

		/* DISABLED 2016-03-04 AS NOW IN ichange_progress TABLE!
		// Addint progress to the JSON array for updating in ichange_waybill.progress
		$prog[] = array(
			'date' => $dt,	
			'time' => date('H:i'), 
			'text' => $t, 
			'waybill_num' => $wb, 
			'map_location' => $wp, 
			'train' => str_replace("NOT ALLOCATED","",$ti), 
			'status' => $wb_stat, 
			'tzone' => "America/Chicago"
		);
		*/

		// Added 2016-03-02 - The $prog[] creation above can be changed to single (ie, taken out of this FOR loop) after 2016-06-02				
		$prog_sql = "INSERT INTO `ichange_progress` SET 
			`date` = '".$dt."', 
			`time` = '".date('H:i')."', 
			`text` = '".$t."', 
			`waybill_num` = '".$wb."', 
			`map_location` = '".$wp."', 
			`status` = '".$wb_stat."', 
			`train` = '".str_replace("NOT ALLOCATED","",$ti)."', 
			`tzone` = 'America/Chicago', 
			`added` = '".date('U')."'";
		mysql_query($prog_sql);
		
		$jprog = json_encode($prog);
		//echo $jprog."<br />";
		//if(strlen($ti.$wp) > 1){$oth_flds .= ", `progress` = '".$jprog."'";}
		$oth_flds .= ", `progress` = '[]'"; //.$jprog."'";
		
		// Update waybill table
		$wb_upd = "UPDATE `ichange_waybill` SET `status` = '".$wb_stat."'".$oth_flds." WHERE `waybill_num` = '".$wb."'";
		mysql_query($wb_upd);

		// Update trains table
		$wb_upd = "UPDATE `ichange_trains` SET `location` = '".$wp."' WHERE `train_id` = '".str_replace("NOT ALLOCATED","",$ti)."'";
		mysql_query($wb_upd);
		//echo "<pre>"; print_r($prog); echo "</pre>";
		
		$render_fld .= $t."\n";
	}
	//echo "Execution stopped!"; exit();

	if(strlen($render_fld) > 0){
		$wise_sayings = array(
				"Heaven sees as my people see; Heaven hears as my people hear (Chinese Classics)", 
				"Everything is permissible, not everything is beneficial (St Paul)", 
				"The kind of ancestors you have is not as important as the ones your children will have (Amish proverb)", 
				"An investment in knowledge always pays the best interest (Benjamin Franklin)" , 
				"The truth will set you free (Jesus)",
				"Forget injuries, never forget kindnesses (Confucius)",  
				"The best things in life are not things (Amish proverb)", 
				"Hold faithfulness and sincerity as first principles (The Confucian Analects)",
				"It is far more impressive when others discover your good qualities without your help (Judith Martin)", 
				"I have often regretted my speech, never my silence (Publilius Syrus)", 
				"Very few burdens are heavy if everyone lifts (Amish proverb)", 
				"I am a firm believer in the people. If given the truth, they can be depended upon to meet any national crises. The great point is to bring them the real facts (Abraham Lincoln)", 
				"Truth has no special time of its own. Its hour is now -- always (Albert Schweitzer)", 
				"In the multitude of counsellors there is safety (Solomon)", 
				"Painless poverty is better than embittered wealth (Greek)", 
				"A happy memory never wears out (Amish proverb)", 
				"Do to others as you would have them do to you (Jesus)", 
				"Faithfulness is the way of Heaven, to be faithful is a mans way (Chinese Classics)", 
				"I think people want peace so much that one of these days governments had better get out of the way and let them have it (Eisenhower)", 
				"I have held many things in my hands, and I have lost them all; but whatever I have placed in God's hands, that I still possess (Martin Luther)", 
				"Pray for a good harvest and continue to hoe (Amish proverb)", 
				"The fewer the words, the better the prayer (Martin Luther)", 
				"Whatever your heart clings to and confides in, that is really your God (Martin Luther)", 
				"A soft answer turns away wrath (Proverbs)", 
				"He who lives by the sword, dies by the sword (Jesus)", 
				"Teamwork divides the effort and multiplies the effect (Amish proverb)", 
				"The path of the just is as the shining light, that shines more and more until the perfect day (Proverbs)",
				"He who angers you conquers you (Unknown)", 
				"Keep your words soft and sweet just in case you have to eat them (Amish proverb)", 
				"Love is the condition in which the happiness of another person is essential to your own (Unknown)", 
				"A good head and a good heart are always a formidable combination (Nelson Mandela)", 
				"A friend is one who knows you and loves you just the same (Elbert Hubbard)", 
				"No one ever built a statue to a critic! (Unknown)", 
				"Peace is seeing a sunset and knowing who to thank (Amish proverb)", 
				"When the people fear the government there is tyranny. When the government fears the people there is liberty (Thomas Jeffersen)", 
				"I believe that banking institutions are more dangerous to our liberty than standing armies (Thomas Jeffersen)", 
				"Having heard all this you may choose to look the other way but you can never say again that you did not know (William Wilberforce)",
				"He is no fool who gives what he cannot keep to gain what he cannot lose (Jim Elliot)",
				"He who forgives first, wins (William Penn)", 
				"My job is to take care of the possible and trust God with the impossible (Amish proverb)", 
				"If you are patient in one moment of anger you will escape a hundred days of sorrow (Chinese proverb)",
				"Most men who have really lived have had, in some share, their great adventure. This railway is mine (James J. Hill)",
				"Railway termini are our gates to the glorious and the unknown. Through them we pass out into adventure and sunshine, to them, alas! we return (E M Forster)", 
				"Instead of putting others in their place put yourself in their place (Amish proverb)", 
				"Justice is like a train that is nearly always late (Yevgeny Yevtushenko)",
				"Precaution is better than cure (Johann Wolfgang von Goethe)", 
				"People dont care how much you know until they know how much you care (Amish proverb)", 
				"Most truths are so naked that people feel sorry for them and cover them up, at least a little (Edward R. Murrow)",
				"No Government ought to be without censors and where the press is free, no one ever will (Thomas Jefferson)",
				""   
			);
		$max_sayings = intval(count($wise_sayings) - 1);
		$saying = rand(0,$max_sayings);
		$render_fld .= "\n".$wise_sayings[$saying]."\n\n";
			
		$mail_alert_msg = "";	
		//$email = "james@stanfordhosting.net";
		$email = "MRICC@yahoogroups.com";
		//$email = "james@stanforhosting.net";
		$subject = "AUTOMATIC ACTIVITY COMPLETED!";
		$headers = "From: mricf@stanfordhosting.net";
		$url = "http://".$_SERVER['SERVER_NAME']."/apps/interchangecars2/index.php/home";

		$subject = "AUTOMATIC ACTIVITY COMPLETED!";
		$mailbody = strip_tags($render_fld)."\n\n(This email generated by cron through auto_ext.php).";
		
		mail($email, $subject, $mailbody, $headers);
		//echo $email."<br />".$subject."<br />".nl2br($mailbody)."<br />".$headers;

		mysql_query("DELETE FROM `ichange_auto` WHERE `act_date` = '".$t_day."'");
	}		
	// End Auto Train Updater
	
	// Start Random Waybill Generator
	// Test for number of P_ORDER waybills. If more than 25, dont make any!
	$create_po = 0;
	$st = "SELECT COUNT(`id`) AS `cntr` FROM `ichange_waybill` WHERE `status` = 'P_ORDER'";
	$qt = mysql_query($st);
	$rt = @mysql_fetch_array($qt);
	if(@$rt['cntr'] < 20){$create_po = 1;$render_fld .= @$rt['cntr']." PORDERS in system.\n";}
	else{$render_fld .= "Maximum number of PORDERS in system - none created today!\n";}
	
	// select a random waybill num.
	$sw = "SELECT `id` FROM `ichange_randomwb` WHERE `regularity` IS NULL OR LENGTH(`regularity`) < 1 ORDER BY `id` DESC LIMIT 1";
	$qw = mysql_query($sw);
	while($rwes = mysql_fetch_array($qw)){
		$rws = $rwes['id'];
	}
	$probablity = $rws + 25;
	$rec = rand(1,$probablity);
	//$rec = 12; // Testing only!
	//echo $rec."<br />";
	
	$render_fld .= "Probability = ".$probablity.", Random Num = ".$rec.".\n";
	if($rec <= $rws && $create_po == 1){
		$render_fld .= "PORDERS will be created today.\n";
		$max_wbs = 4;
		$wbnum = date('Ymd');
		$wbdate = date('Y-m-d');
		$no_2_gen = rand(1,$max_wbs); //intval($rws/2);
		//if($no_2_gen < 1){$no_2_gen = 1;}	
		//if($no_2_gen > $max_wbs){$no_2_gen = $max_wbs;}
		//echo "# to gen = ".$no_2_gen."<br />";
		
		for($i=0;$i<$no_2_gen;$i++){	
			//echo "cntr = ".$i."<br />";
			$rw_chose = rand(1,$rws);
			//echo "wb chosen: ".$rw_chose."<br />";
			$sw = "SELECT * FROM `ichange_randomwb` WHERE `id` = '".$rw_chose."' AND (`regularity` IS NULL OR LENGTH(`regularity`) < 1)";
			$qw = mysql_query($sw);
			$rw = mysql_fetch_array($qw);
		
			if(isset($rw['rr_id_from'])){
				$r_mark = qry("ichange_rr", $rw['rr_id_from'], "id", "report_mark");
				$mailbody = "The following Purchase has been generated by a customer:\n";
				$nrw = "INSERT INTO `ichange_waybill` SET 
				`date` = '".$wbdate."', 
				`status` = 'P_ORDER', 
				`waybill_num` = '".$wbnum."-".$i."', 
				`rr_id_from` = '".$rw['rr_id_from']."', 
				`rr_id_to` = '".$rw['rr_id_to']."', 
				`rr_id_handling` = '".$rw['rr_id_from']."', 
				`indust_origin_name` = '".$rw['indust_origin_name']."', 
				`indust_dest_name` = '".$rw['indust_dest_name']."', 
				`return_to` = '".$r_mark."', 
				`routing` = '".$rw['routing']."', 
				`car_aar` = '".$rw['car_aar']."', 
				`lading` = '".$rw['lading']."', 
				`alias_aar` = '".$rw['car_aar']."', 
				`notes` = '".$rw['notes']."'";
				//echo $wbnum."-".$i." inserted";
				mysql_query($nrw);
				$mailbody .= "Waybill #: ".$wbnum."-".$i."\n";
				$mailbody .= "Origin Industry: ".$rw['indust_origin_name']."\n";
				$mailbody .= "Destination Industry: ".$rw['indust_dest_name']."\n";
				$mailbody .= "Return To: ".$r_mark."\n";
				$mailbody .= "Route: ".$rw['routing']."\n";
				$mailbody .= "Lading: ".$rw['lading']."\n";
				$mailbody .= "Car AAR Type: ".$rw['car_aar']."\n";
				$mailbody .= "Notes:\n".$rw['notes']."\n";
				$mailbody .= "\n\n(This email generated by cron through auto_ext.php).";

				$email = "MRICC@yahoogroups.com";
				//$email = "james@stanfordhosting.net";
				$headers = "From: mricf@stanfordhosting.net";
				$subject = " PURCHASE ORDER ".$wbnum."-".$i." GENERATED";
				mail($email, $subject, $mailbody, $headers);
				//echo nl2br($mailbody);
				$render_fld .= $mailbody;
			}
		}
	}
	// End Random Waybill Generator
	
	// Start create regular waybills generator	
	// regularity field syntax:
	// NULL or empty - randomly generated
	$wbnum = date('Ymd');
	$wbdate = date('Y-m-d');	
	$swr = "SELECT * FROM `ichange_randomwb` WHERE `regularity` LIKE '".date('d')."-%".date('m')."%' AND `rr_id_from` > 0 AND `rr_id_to` > 0";
	$qwr = mysql_query($swr);
	$cntr=100;
	$render_fld .= "\n";
	while($resr = mysql_fetch_array($qwr)){
		$rws = $resr['id'];
		$r_mark = qry("ichange_rr", $resr['rr_id_from'], "id", "report_mark");
		$nrwr = "INSERT INTO `ichange_waybill` SET 
			`date` = '".$wbdate."', 
			`status` = 'WAYBILL', 
			`waybill_num` = '".$wbnum."-".$cntr."', 
			`rr_id_from` = '".$resr['rr_id_from']."', 
			`rr_id_to` = '".$resr['rr_id_to']."', 
			`rr_id_handling` = '".$resr['rr_id_from']."', 
			`indust_origin_name` = '".$resr['indust_origin_name']."', 
			`indust_dest_name` = '".$resr['indust_dest_name']."', 
			`return_to` = '".$r_mark."', 
			`routing` = '".$resr['routing']."', 
			`car_aar` = '".$resr['car_aar']."', 
			`lading` = '".$resr['lading']."', 
			`alias_aar` = '".$resr['car_aar']."', 
			`notes` = '".$resr['notes'].". *REGULAR WAYBILL - ".$resr['regularity']."*'";
		$render_fld .= "<br />Regular Waybill ".$wbnum."-".$cntr." inserted";
		mysql_query($nrwr);
		$cntr++;
	}
	
	// End create regular waybills generator
	
	// Start purge old waybills and progress data
	//echo "Interchange Cars Data Purge Test<br />";
	$mths = 2;
	$mth_unix = 60*60*24*30*$mths;
	$po_unix = date('U') - (60*60*24*30*2); // POs more than 2 mnths old deleted!
	$po_dt = date('Y-m-d',$po_unix);
	$yr_unix = date('U') - (60*60*24*30*12); // 12 months ago
	$rr_rem_unix = date('U') - (60*60*24*30*12*3); // 3 years ago
	$dt_unix = date('U') - $mth_unix;
	$dt = date('Y-m-d', $dt_unix);
	$yr = date('Y-m-d', $yr_unix);
	$act_unix = date('U') - (60*60*24*30); // 1 month of activity kept!

	$s = array();
	//$s[] = "SELECT `status`,`date` FROM `ichange_waybill` WHERE `status` = 'CLOSED' AND `date` < '".$dt."'";
	//$s[] = "SELECT `date` FROM `ichange_progress` WHERE `date` < '".$dt."'";
	//$s[] = "SELECT `date_avail` FROM `ichange_availcars` WHERE `date_avail` < '".$dt."' OR LENGTH(`date_avail`) < 10 OR `date_avail` NOT LIKE '%-%'";

	$s[] = "DELETE FROM `ichange_waybill` WHERE `status` = 'CLOSED' AND `date` < '".$dt."'";
	$s[] = "DELETE FROM `ichange_waybill` WHERE `date` < '".$yr."'";
	$s[] = "DELETE FROM `ichange_waybill` WHERE `date` < '".$po_dt."' AND `status` = 'P_ORDER'";
	$s[] = "DELETE FROM `ichange_availcars` WHERE `date_avail` < '".$dt."' OR LENGTH(`date_avail`) < 10 OR `date_avail` NOT LIKE '%-%'";
	$s[] = "DELETE FROM `ichange_activity` WHERE `added` < ".$act_unix." OR `added` IS NULL";
	$s[] = "DELETE FROM `ichange_auto` WHERE `act_date` < '".date('Y-m-d')."'";
	$s[] = "DELETE FROM `ichange_generated_loads` WHERE `added` < '".$act_unix."'";
	$s[] = "DELETE FROM `ichange_carsused_index` WHERE `added` < '".$yr_unix."'";
	$s[] = "UPDATE `ichange_rr` SET `inactive` = 1 WHERE `last_act` > 0 AND `last_act` < '".$yr_unix."' AND `common_flag` != 1";

	for($i=0;$i<count($s);$i++){
		$q = mysql_query($s[$i]);
	}	
	$wb_purge = "Closed waybills, progress reports (old system) and available cars entries older than ".$dt." has been purged from the database.\n";
	$wb_purge .= "Waybills created before ".$yr." have been purged from the database.\n";
	$wb_purge .= "Purchase Orders created before ".$po_dt." have been purged from the database.\n";
	$wb_purge .= "Auto activity dated before ".date('Y-m-d')." has been purged from the Auto activity table.\n";
	$wb_purge .= "Generated Loads older than ".date('Y-m-d',$act_unix)." has been purged from the Auto activity table.\n";
	$wb_purge .= "Cars data older than ".date('Y-m-d',$yr_unix)." has been purged from the Cars Used Index table.\n";
	$wb_purge .= "Railroads with no activity since ".date('Y-m-d', $yr_unix)." have been set to inactive status.\n\n";

	// Remove rr and associated data not used since $rr_rem_unix.
	$rrs = "SELECT `id`,`report_mark` FROM `ichange_rr` WHERE `last_act` IS NOT NULL AND `last_act` > 0 AND `last_act` < ".$rr_rem_unix." AND `common_flag` != 1";
	$rrq = mysql_query($rrs) or die(mysql_error());
	$rr_arr = array();
	while($rrr = mysql_fetch_array($rrq)){
		$rr_arr[] = "DELETE FROM `ichange_cars` WHERE `rr` = '".$rrr['id']."'";	
		$rr_arr[] = "DELETE FROM `ichange_indust` WHERE `rr` = '".$rrr['id']."'";	
		$rr_arr[] = "DELETE FROM `ichange_trains` WHERE `railroad_id` = '".$rrr['id']."'";	
		$rr_arr[] = "DELETE FROM `ichange_rr` WHERE `id` = '".$rrr['id']."'";	
		$wb_purge .= "Data for ".$rrr['report_mark']." has been REMOVED from the RR, Indust, Cars and Trains data tables (no activity since ".date('Y-m-d', $rr_rem_unix).").\n";
	}
	for($ri=0;$ri<count($rr_arr);$ri++){mysql_query($rr_arr[$ri]);}

	$dt_today = date('U');
	$wb_purge .= "\n\n".$render_fld;
	$email = "james@stanfordhosting.net";
	$headers = "From: mricf@stanfordhosting.net";
	$subject = "MRICF WAYBILL DATA PURGE / ACTIVITY GENERATED";
	mail($email, $subject, $wb_purge, $headers);
	// End purge old waybills and progress data

	// Alert James to login to confirm status if last login more than 1 week ago.
	$sql = "SELECT `last_act` FROM `ichange_rr` WHERE `admin_flag` = '1' ORDER BY `id` LIMIT 1";
	$qry = mysql_query($sql);
	$res = mysql_fetch_array($qry);
	$now = date('U');
	$week_ago = intval($now - (60*60*24*2));
	$cut_off = intval($now - (60*60*24*15));
	$vi_email = $email; //"virtual_ops@yahoogroups.com";
	if($res['last_act'] < $cut_off){
		// Add the alert in here once the week_ago alert works!
		$vi_body = "The last login of the MRICF CODER / HOSTER was ".date('Y-m-d H:i:s',$res['last_act']).". This may just be because he is not able to log in due to being away, or from being too busy to play trains.\n\nPlease contact him directly via ".$email." to confirm that he is still viable.\n\nIf he does not respond within 7 days, it would be a good idea to activate a contingency plan to set-up the MRICF on another server as his server will go done within the next month or so if he is no longer viable and unable to pay the server bill to keep it going.\n\nThe latest codebase is located in the git repo at https://github.com/stanford-ja/interchangecars2.git - use the git clone command to create this codebase on a new server. Set up of the MRICF application will require some knowledge of PHP and preferably Code Igniter. A copy of the database is available on the Virtual Ops Yahoo group in the Files / MRICF / MRICF Backups folder. You will need to download the SQL file and use the mysql command on the server to create a database called jstan2_general , then use the mysql command to create the database tables in that new database. You will also need to change the application/config/database.php file to use the mysql server username, password and hostname.\n\nThis was generated by the MRICF auto_ext.php script.";
		//echo nl2br($vi_body);
		mail($vi_email, "MRICF CODER INACTIVE", $vi_body, $headers);
	}else
	if($res['last_act'] < $week_ago){
		$subject = "MRICF - PLEASE RESPOND ASAP";
		$body = "Your last login to the MRICF was ".date('Y-m-d H:i:s',$res['last_act'])." so you need to login to MRICF within the next few days! Please do this as a matter of URGENCY so that no CUT OFF event is triggered and the group notified that you have not logged in for too long a time!!
			
			This was generated by the auto_ext.php script.";
		mail($email, $subject, $body, $headers);
		echo "<h3>Please respond alert</h3>SUBJECT: ".$subject."<br />BODY: ".nl2br($body)."<br /><br />";;
	}else{ echo "Last login to MRICF was ".date('Y-m-d H:i:s',$res['last_act'])." - ALL GOOD!<br /><br />"; }
	
	mysql_close();
	echo "finished ".date('Y-m-d H:i:s',$dt_today)."!<br /><br />".nl2br($wb_purge);
?>
