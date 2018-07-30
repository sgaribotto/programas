<?php

	require 'fuentes/conexion.php';
	
	$query = "SELECT * FROM docente
				LIMIT 100";
	$result = $mysqli->query($query);
	
	$html = "<style>
		table {
			border-collapse: collapse;
		}
		
		td {
			border-bottom: 1px solid black;
		}
	</style>";
	
	$html .= "<table style='border-collapse: collapse;'>";
	
	while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
		$html .= "<tr style='border-bottom: 1px solid black;'>";
		foreach ($row as $value) {
			$html .= "<td style='border-bottom: 1px solid black;'>{$value}</td>";
		}
		$html .= '</tr>';
	}
	$html .= '</table>';
	
	//echo $html;
		
	//TCPDF TEST
	require_once('libs/tcpdf/tcpdf.php');
	
	class MYPDF extends TCPDF {
		//header
		public function Header() {
			$image_file = 'images/logo.jpg';
			$this->Image($image_file, 5, 5, 60, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
			
			$this->SetFont('helvetica', 'B', 20);
			
			$this->Cell(0, 15, 'EEYN - UNSAM TEST', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}
		
		public function Footer() {
			
			$this->SetY(-15);
			
			$this->SetFont('helvetica', 'I', 8);
			
			$this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0 , false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}
	
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Test Author');
	$pdf->SetTitle('TCPDF Example tabla html');
	$pdf->SetSubject('TCPDF Tutorial');
	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
	
	
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	
	$pdf->SetHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->SetFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	
	
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
	$pdf->SetImageScale(PDF_IMAGE_SCALE_RATIO);
	
	
	$pdf->SetFont('dejavusans', '', 8);
	
	$pdf->AddPage();
	
	$pdf->WriteHTML($html, true, false, true, false, '');

	$pdf->Output('example.pdf', 'I');
?>
