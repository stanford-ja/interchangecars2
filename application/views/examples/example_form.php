<h1>Example Form</h1>
<?php
$attributes = array('name' => 'myform'); // Form attributes
$action = "NEW"; if($id > 0){$action = "EDIT";}
$hidden = array('id' => $id, 'tbl' => 'ichange_rr', 'action' => $action); // Hidden fields definitions
echo form_open_multipart('example_save/rr',$attributes,$hidden); // form_open() is identical syntax!

$frm_data = array(
	'rr_name' => array(
		'name'        => 'rr_name',
		'id'          => 'rr_name',
		'value'       => $data[0]['rr_name'],
		'maxlength'   => '50',
		'size'        => '50',
		'style'       => 'background-color: #ccc;', 
		'onchange'	=> "alert('ok');"
	), 
	'report_mark' => array(
		'name'        => 'report_mark',
		'id'          => 'report_mark',
		'value'       => $data[0]['report_mark'],
		'maxlength'   => '10',
		'size'        => '10',
		'style'       => 'background-color: lightskyblue;'
	),
	'rr_desc' => array(
		'name'        => 'rr_desc',
		'id'          => 'rr_desc',
		'value'       => $data[0]['rr_desc'],
		'rows'   => '3',
		'cols'        => '50',
		'style'       => 'border: 1px solid maroon;'
	),
	'submit' => array(
		'name'        => 'submit',
		'id'          => 'submit',
		'value'       => 'Submit'
	)
	       
);

echo form_label('Name', 'rr_name')."<br />".form_input($frm_data['rr_name'])."<br />";
echo form_label('Reporting Mark', 'report_mark')."<br />".form_input($frm_data['report_mark'])."<br />";
echo form_label('Description', 'rr_desc')."<br />".form_textarea($frm_data['rr_desc'])."<br />";

$options = array(
	'0' => 'View 0',
	'1' => 'View 1'
);
$js = ' onchange="alert(\'Display type changed!\');"';

echo form_label('Home Display', 'home_disp')."<br />".form_dropdown('home_disp', $options, $data[0]['home_disp'], $js); // Params: 0=name/id, 1=options, 2=value selected, 3=other params for the field
echo form_submit($frm_data['submit'])
?>