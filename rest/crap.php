<?
class crap {
	var $html=1; // get from useragent or pass
	//var $EOLCHAR="<br />";
	var $EOLCHAR="";
	/* FUNCTION TO SET THE EOLCHAR if ANY
	if($html){ $EOLCHAR="<br />"; }
	else{ $EOLCHAR="\n"; }
	*/

	function index(){
		return "CrapClassIndexFile".$this->EOLCHAR;
	}

	/* Print Hello "string" */
	function hello($who="World"){
		echo "Hello $who".$this->EOLCHAR;
		return true;
	}

	/* SUM TWO INTEGERS */
	function sum($a=0,$b=0){
		return intval($a) + intval($b);
	}

	/* MULTIPLY TWO INTEGERS */
	function multiply($a=0,$b=0){
		return intval($a) * intval($b);
	}
}
?>
