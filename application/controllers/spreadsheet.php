<?php
class Spreadsheet extends CI_Controller {
	// The $this->arr['pgTitle'], model references and contents of
	// the setFieldSpecs() method are probably all that needs to be changed
	// to make this usable for another view / controller combo!

	function __construct(){
		// Auto ran method.
		parent::__construct(); // Necessary in all __construct methods in CodeIgniter!!
		if(isset($_COOKIE['rr_sess'])){$this->arr['rr_sess'] = $_COOKIE['rr_sess'];}
		else{header("Location:".WEB_ROOT."/login"); exit();}

		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->helper('download');
		$this->load->library('mricf');
		$this->load->library('PHPExcel');
		
		$this->load->model('Generic_model','',TRUE); // Database connection! TRUE means connect to db.
		$this->dat = array();

		$this->load->library('settings');
		$this->arr = $this->settings->setVars();
	}

	public function index(){
		exit();
	}
	

	public function waybills(){
		$this->vars['Creator'] = "James Stanford";
		$this->vars['LastModifiedBy'] = "James Stanford";
		$this->vars['Title'] = "Office 2007 XLSX Test Document";
		$this->vars['Subject'] = "Office 2007 XLSX Test Document";
		$this->vars['Description'] = "Data generated using PHP classes.";
		$this->vars['Keywords'] = "Office 2007 openxml";
		$this->vars['Category'] = "Test result file";
		$this->vars['SqlResult'] = (array)$this->Generic_model->qry("SELECT * FROM `ichange_waybill` WHERE `status` != 'CLOSED' ORDER BY `waybill_num` DESC");
		$this->vars['PageTitle'][0] = "Summary of Waybills Open";
		$this->vars['PageTitle'][1] = "Waybill Destinations";
		$this->vars['Columns'][0] = array("waybill_num","indust_origin_name","status","routing");
		$this->vars['Columns'][1] = array("waybill_num","indust_dest_name");
		$this->vars['ColumnTitles'][0] = array("Waybill Num","Origin Industry","Status","Routing");
		$this->vars['ColumnTitles'][1] = array("Waybill Num","Destination Industry");
		$this->vars['SheetIndex'] = 0;
		$this->vars['SheetNames'] = array("WBSummary","WBDestinations");
		$this->generate();
	}

	function csv_export(){
		$this->load->dbutil();
		//$qry = $this->Generic_model->qry($this->sql);
		$qry = $this->db->query("SELECT * FROM `ichange_indust` WHERE `rr` = '".$this->arr['rr_sess']."'");
		$delimiter = ",";
		$newline = "\r\n";
		force_download('cars.csv', $this->dbutil->csv_from_result($qry, $delimiter, $newline));

	}

	function generate(){
		// Create new PHPExcel object
		$col_labs = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
		$this->objPHPExcel = new PHPExcel();

		// Set styles array2
		$styleArrayTitle = array(
			'font' => array(
				'bold' => true,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			),
			'borders' => array(
				'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
		);
		$styleArrayCell1 = array(
			'font' => array(
				'bold' => false,
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
			),
			'borders' => array(
				'top' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN,
				),
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'argb' => 'FFA0A0A0',
				),
			),
		);
		$styleArrayCell2 = $styleArrayCell1;
		$styleArrayCell2['fill']['startcolor']['argb'] = 'FFAABBCC';

		// Set document properties
		$this->objPHPExcel->getProperties()->setCreator($this->vars['Creator'])
			->setLastModifiedBy($this->vars['LastModifiedBy'])
			->setTitle($this->vars['Title'])
			->setSubject($this->vars['Subject'])
			->setDescription($this->vars['Description'])
			->setKeywords($this->vars['Keywords'])
			->setCategory($this->vars['Category']);

		// Iterate through sheet names
		for($pg_cntr=0;$pg_cntr<count($this->vars['SheetNames']);$pg_cntr++){
			// merge first line for sheet title
			$cntr=1;
			if($pg_cntr>0){$this->objPHPExcel->createSheet();}

			$this->objPHPExcel->setActiveSheetIndex($pg_cntr)->setCellValue('A'.$cntr, $this->vars['PageTitle'][$pg_cntr]);
			$this->objPHPExcel->getActiveSheet()->mergeCells('A'.$cntr.':D'.$cntr);
			$this->objPHPExcel->getActiveSheet()->getStyle('A'.$cntr)->applyFromArray($styleArrayTitle);

			// Set column titles
			$cntr++;
			for($co=0;$co<count($this->vars['ColumnTitles'][$pg_cntr]);$co++){
				$this->objPHPExcel->setActiveSheetIndex($pg_cntr)->setCellValue($col_labs[$co].$cntr, $this->vars['ColumnTitles'][$pg_cntr][$co]); // Set title for column
				$this->objPHPExcel->getActiveSheet()->getColumnDimension($col_labs[$co])->setAutoSize(true); // Set auto column width
			}
			$this->objPHPExcel->getActiveSheet()->getStyle($col_labs[0].$cntr.':'.$col_labs[$co-1].$cntr)->applyFromArray($styleArrayTitle);

			// Add some data
			for($qr=0;$qr<count($this->vars['SqlResult']);$qr++){
				$cntr++;
				$tmp = (array)$this->vars['SqlResult'][$qr];
				for($fl=0;$fl<count($this->vars['Columns'][$pg_cntr]);$fl++){
					$this->objPHPExcel->setActiveSheetIndex($pg_cntr)->setCellValue($col_labs[$fl].$cntr, $tmp[$this->vars['Columns'][$pg_cntr][$fl]]);
				}
				if(intval($qr/2) == floatval($qr/2)){$this->objPHPExcel->getActiveSheet()->getStyle($col_labs[0].$cntr.':'.$col_labs[$fl-1].$cntr)->applyFromArray($styleArrayCell1);}
				else{$this->objPHPExcel->getActiveSheet()->getStyle($col_labs[0].$cntr.':'.$col_labs[$fl-1].$cntr)->applyFromArray($styleArrayCell2);}
			}

			// Rename worksheet
			$this->objPHPExcel->getActiveSheet()->setTitle($this->vars['SheetNames'][$pg_cntr]);

		}
		
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$this->objPHPExcel->setActiveSheetIndex($this->vars['SheetIndex']);

		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple'.date('Ymd').'.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
	
}
?>