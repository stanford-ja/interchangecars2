<?php 
?>
	function explodeStr(delim,longstr){
		// Explodes a string into an array.
		// delim = the delimiter for the string (ie, the char to split the string at)
		// longstr = the string to split
		// str_arr is the array of data to return
		var str_arr=longstr.split(delim); 
		return str_arr;
	}	
	
	function copSel(fld1, fld2){
		// Copy data in fld1 to fld2.
		var cFld1 = document.getElementById(fld1).value;
		document.getElementById(fld2).value = cFld1;
	}
		
	function mess_win(type){
		win = window.open("message.php?type="+type, "", "width=400px, height=550px, resizable");
	}

	function chckFrm(){
	   var textMess = "ERRORS:\n";
	   var warnMess = "WARNING:\n";
	  	var errGen = 0;
	  	var warnGen = 0;

		var rr_name = document.getElementById('rr_name');
		if(document.getElementById('owner_name')){var owner_name = document.getElementById('owner_name');}
		var report_mark = document.getElementById('report_mark');
		var pw = document.getElementById('pw');
			
		if(rr_name.value.length < 2){textMess = textMess+" [RR Name] is not long enough.\n"; errGen = errGen+1}; 
		if(owner_name){
			if(owner_name.value == 0){textMess = textMess+" [Owner Name] needs a value.\n"; errGen = errGen+1};
		} 
		if(report_mark.value.length < 2){textMess = textMess+" [Report Mark] value is not long enough.\n"; errGen = errGen+1}; 
		if(pw.value.length > 0 && pw.value.length < 5){textMess = textMess+" [Password] is not long enough.\n"; errGen = errGen+1}; 
	
		if (warnGen>0){
			alert(warnMess);
		}

		if (errGen>0){
			alert(textMess);
			return false;
		}else{
			return true;
		}

	}
	
	$(document).ready(function(){
		/*
		document.getElementById('pos').style.display = 'none';
	
		// JQuery events
		$("#po_expand").click(function(){
			$("#pos").slideDown("slow");
			document.getElementById('pos').style.display = 'block';
			document.getElementById('pos').style.width = '200px';
			return false;
		});
		*/
	});
	
