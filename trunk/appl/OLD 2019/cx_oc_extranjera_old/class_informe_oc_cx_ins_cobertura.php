<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
class informe_oc_cx_ins_cobertura extends reporte {	
	function informe_oc_cx_ins_cobertura($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}

	function dibuja_uno(&$pdf, $result){
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(140, 100+15);
		$pdf->Cell(47, 15, 'Hola Mundo (informe_oc_cx_ins_cobertura)' , 0 , 0, 'L');
		
	}
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		for($i=0; $i<count($result); $i++) {
			$this->dibuja_uno($pdf, $result[$i]);
			if ($i < count($result) - 1)
				$pdf->AddPage();
		}
	}	
}	
?>