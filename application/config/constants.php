<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

$doc_path = "/apps";
$idp = "";
if($_SERVER['SERVER_NAME'] == "localhost" || strpos("a".$_SERVER['SERVER_NAME'],"10.0.0.") > 0){
	$doc_path = "/Applications";
	$idp = "/index.php";
}
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT'].$doc_path."/interchangecars2");
define('WEB_ROOT', 'http://'.$_SERVER['SERVER_NAME'].$doc_path."/interchangecars2");
define('INDEX_PAGE', $idp);

/* USED IF YOU USE A THEME */
/*
define('TEMPLATE_ROOT', DOC_ROOT.'/application/views/information'); // Used for including files: include(TEMPLATE_ROOT."/path/to/file.php")
define('THEME_ROOT', WEB_ROOT.'/themes/information');
define('CSS_ROOT', THEME_ROOT.'/css'); // Location of CSS files
define('JS_ROOT', THEME_ROOT.'/js'); // Location of Javascript files
define('IMAGE_ROOT', THEME_ROOT.'/images'); // Location of image / graphic files
*/

/* USED IF YOU DON'T USE A THEME */
define('TEMPLATE_ROOT', DOC_ROOT.'/application/views/information'); // Used for including files: include(TEMPLATE_ROOT."/path/to/file.php")
define('CSS_ROOT', WEB_ROOT.'/css'); // Location of CSS files
define('JS_ROOT', WEB_ROOT.'/js'); // Location of Javascript files
define('IMAGE_ROOT', WEB_ROOT.'/images'); // Location of image / graphic files

/* End of file constants.php */
/* Location: ./application/config/constants.php */
