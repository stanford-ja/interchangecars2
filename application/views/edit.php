<?php
// Edit view, generic

if(strlen(@$error) > 0){echo "<span class=\"error\">".$error."</span>";}

$lnk_kys = array();
if(isset($links)){$lnk_kys = array_keys($links);}
//echo "<pre>"; print_r($lnk_kys[0]); echo "</pre>";
for($i=0;$i<count($lnk_kys);$i++){
	echo "<a href=\"".$links[$lnk_kys[$i]]."\">".$lnk_kys[$i]."</a>&nbsp;";
}
echo "<div style=\"padding: 10px; margin; 0px; margin-left: 10%; margin-right: 10%;\">";
echo form_open_multipart(@$form_url,@$attribs,@$hidden);
for($i=0;$i<count($fields);$i++){
	echo "<span class=\"small_txt\">".$field_names[$i]."</span>";
	echo $fields[$i]."<br />";
}
echo form_submit('submit','Update');
echo form_close();
if(!isset($no_delete_form)){
	echo "<hr />\n<span style=\"color: #8B0000;\">Click the Delete checkbox below then click the Delete button that appears to delete this record.</span>";
	$delattr = array('name' => "dfrm");
	echo form_open_multipart("../delete",$delattr,@$hidden);
	echo "Delete: <input type=\"checkbox\" name=\"confdel\" onclick=\"hideEle('delrec');if(this.checked == true){showEle('delrec');}\" />";
	echo "<input style=\"display:none;\" type=\"submit\" name=\"delrec\" id=\"delrec\" value=\"Delete\" />";
	echo form_close();
}
echo "</div>";
?>
