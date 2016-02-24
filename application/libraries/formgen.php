<?php

// Form element generator library

class Formgen {
// Class for holding all the functions used in MRICF
// Re-activate methods as needed.

function __construct(){
	$this->CI =& get_instance(); // Global CodeIgniter object for use in methods.
}

function setFormElements(){
	// Uses $this->field_defs array from calling class to generate form elements
		for($i=0;$i<count($this->CI->field_defs);$i++){
			$this->CI->dat['field_names'][$i] = $this->CI->field_defs[$i]['label'];
			if($this->CI->field_defs[$i]['type'] == "checkbox"){
				$this->CI->dat['fields'][$i] = form_checkbox($this->CI->field_defs[$i]['def']).$this->CI->dat['field_names'][$i];
				$this->CI->dat['field_names'][$i] = "";
			}
			if($this->CI->field_defs[$i]['type'] == "radio"){
				$this->CI->dat['fields'][$i] = form_radio($this->CI->field_defs[$i]['def']).$this->CI->dat['field_names'][$i];
				$this->CI->dat['field_names'][$i] = "";
			}
			if($this->CI->field_defs[$i]['type'] == "input"){$this->CI->dat['fields'][$i] = "<br />".form_input($this->CI->field_defs[$i]['def']);}
			if($this->CI->field_defs[$i]['type'] == "textarea"){$this->CI->dat['fields'][$i] = "<br />".form_textarea($this->CI->field_defs[$i]['def']);}
			if($this->CI->field_defs[$i]['type'] == "select"){$this->CI->dat['fields'][$i] = "<br />".form_dropdown($this->CI->field_defs[$i]['name'],$this->CI->field_defs[$i]['options'],$this->CI->field_defs[$i]['value'],$this->CI->field_defs[$i]['other']);}
			if($this->CI->field_defs[$i]['type'] == "statictext"){$this->CI->dat['fields'][$i] = "<br />".$this->CI->field_defs[$i]['value'];}
		}
}

}
?>
