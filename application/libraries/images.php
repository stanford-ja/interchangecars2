<?php
require_once("mysqli_class.php");

class Images {

	function __construct(){
		$this->CI =& get_instance(); // Global CodeIgniter object for use in methods.
	}

	function sqli_instance(){
		$sqli = new sqli;
		$sqli->select_db("jstan_general");
		return $sqli;
	}

	function create_map_routes($map){
		// Creates route lines for file images/routes.png

		// Set the image
		$img = imagecreatetruecolor(1200,1200);
		imagesavealpha($img, true);

		// Fill the image with transparent color
		$color = imagecolorallocatealpha($img,0x00,0x00,0x00,127);
		imagefill($img, 0, 0, $color);

		// Fill with other stuff
		$grey = imagecolorallocate ($img, 230, 230, 230);
		$black = imagecolorallocate ($img, 0, 0, 0);

		/*
		imageline($img, 0, 30, 150, 150, $black);
		imageline($img, 0, 150, 150, 30, $black);
		imageline($img, 0, 30, 150, 30, $black);
		*/
		$s = "SELECT * FROM `ichange_maproutes` WHERE `map` = '".$map."'";
		$sqli = $this->sqli_instance();
		$q = $sqli->query($s);
		while($r = $q->fetch_array()){
			imageline($img,$r['coord1a'],$r['coord2a'],$r['coord1b'],$r['coord2b'],$black);
			imagestring($img, 3, intval(($r['coord1a'] + $r['coord1b'])/2), intval(($r['coord2a'] + $r['coord2b'])/2), $r['route_name'], $black);
		}
		
		// Save the image to file.png
		$img_nam = $_SERVER['DOCUMENT_ROOT'].str_replace("index.php","",$_SERVER['SCRIPT_NAME'])."images/routes_".$map.".png";
		imagepng($img, $img_nam);

		// Destroy image
		imagedestroy($img);
	}

}
?>