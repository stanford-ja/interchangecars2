<?php
class Backup extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
	}

	public function index(){
		echo "<h1>MRICF Code and Data Download</h1>
This area outlines how to proceed should something really bad happen to the MRICF coder. In this event you will normally have about 30 days to download and install the code and database on another server before the server maintained by the MRICF Coder decomissions.<br />
<br />
The MRICF application coderbase is officially bigger than the Yahoo Groups maximum file upload size! 
So a backup can not be included in the Yahoo group Files folder.<br />
<br />
To get a backup of the current codebase for the MRICF, do the following:<br />
<ol>
	<li>If you have a GitHub account, go to <strong>https://github.com/stanford-ja/interchangecars2</strong> in your browser and click the Download ZIP button.</li>
	<li>If you don't have a GitHub account but have Git on your computer or server, or you know how to use git, you can do a git clone of the codebase from the repository address https://github.com/stanford-ja/interchangecars2.git.</li> 
</ol>
When installing the code files on your computer, you will need to make sure they are located in the /apps directory in your domain's root directory (eg, /var/www/html/apps/interchangecars2 if your domain points to the /var/www/html directory on your server).<br /><br />
Once you have done that you will need to change the application/config/database.php file variables...<br />
<ul>
<li>\$db['default']['hostname']</li>
<li>\$db['default']['username']</li>
<li>\$db['default']['password']</li>
<li>\$db['default']['database']</li>
</ul>
<br />... so that they point to your SQL server. Note that the settings in there may only be available on the <strong>".$_SERVER['SERVER_NAME']."</strong> server and not from elswhere so you must change these settings if you are going to use the MRICF application on a different server.
";
		echo "To download the database click the links below.<br /><br />";
		echo "<a href=\"".WEB_ROOT."/index.php/backup/databaseDownload\">Download Database</a><br /><br />";
		echo "To install the database onto your server, you will need to CREATE the database using a MySQL database management utility (eg, PHPMyAdmin)
		then import the SQL file downloaded by clicking the Download Database link above into the database. 
		The \$db['default']['database'] value will need to be the same as the database you created and imported the SQL file into in order for the application to work.";
	}
	
	public function databaseDownload(){
		$prefs = array(
                'format'      => 'txt',             // gzip, zip, txt
                'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
                'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
                'newline'     => "\n"               // Newline character used in backup file
              );
		$this->load->dbutil();
		$this->load->helper('file');
		$fil_nam = "/uploaded_files/mricf-".date('Ymd-His').".sql";
		$backup =& $this->dbutil->backup($prefs); 
		write_file(DOC_ROOT.$fil_nam, $backup); 
		/*
		$this->load->helper('download');
		$backup =& $this->dbutil->backup($prefs); 
		force_download('../../uploaded_files/mricf-'.date('Ymd-His').'.sql', $backup);
		*/
		header("Location:".WEB_ROOT.$fil_nam); 
		
		// Load the file helper and write the file to your server
	}	
}
?>