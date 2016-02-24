<?php
	if($this->rss_writer->writerss($output)){
		// If the document was generated successfully, you may now output it.
		header('Content-Type: text/xml; charset="'.$this->rss_writer->outputencoding.'"');
		header('Content-Length: '.strval(strlen($output)));
		echo $output;
	}else{	
		// If there was an error, output it as well.
		header('Content-Type: text/plain');
		echo ('Error: '.$this->rss_writer->error);
	}
?>