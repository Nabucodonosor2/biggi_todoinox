<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
class informe_oc_cx_pago extends reporte {	
	function informe_oc_cx_pago($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}

	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		
		$y=-15;
		$pdf->Image(dirname(__FILE__)."/../../images_appl/TODOINOX/logo_reporte_2.jpg", 20, 8, 460, 620);
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(380, $y+25+15);
		$pdf->Cell(47, 15, 'Santiago, '.$result[0]['FECHA_CX_CARTA_OP'] , 0 , 0, 'L');
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',15);
		$pdf->SetXY(210, $y+65+15);
		$pdf->Cell(47, 15, 'CARTA ORDEN DE PAGO' , 0 , 0, 'L');
		
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(52, $y+86+15);
		$pdf->Cell(47, 15, 'Señores' , 0 , 0, 'L');
		$pdf->SetXY(52, $y+100+15);
		$pdf->Cell(47, 15, 'ITAU' , 0 , 0, 'L');
		$pdf->SetXY(52, $y+114+15);
		//$pdf->Cell(47, 15, 'Huérfanos N°1072, 5° Piso' , 0 , 0, 'L');
		//$pdf->SetXY(52, $y+127+15);
		$pdf->Cell(47, 15, 'PRESENTE' , 0 , 0, 'L');
		$pdf->Line(55,128,110,128);
		
		$pdf->SetXY(52, $y+155+15);
		$pdf->Cell(47, 15, 'At.: Srita. Yasmina Guajardo ', 0 , 0, 'L');
		//$pdf->Cell(47, 15, 'At.: Srita. '.$result[0]['NOM_ATENCION'] , 0 , 0, 'L');
		//$pdf->SetXY(52, $y+168+15);
		//$pdf->Cell(47, 15, 'CC : Sr. '.$result[0]['NOM_ATENCION_CC'] , 0 , 0, 'L');
		//$pdf->SetXY(52, $y+181+15);
		//$pdf->Cell(47, 15, 'Ref: '.$result[0]['REFERENCIA'] , 0 , 0, 'L');
		
		$pdf->SetXY(52, $y+208+15);
		$pdf->Cell(47, 15, 'Estimada Yasmina: ', 0 , 0, 'L');
		//$pdf->Cell(47, 15, 'Estimada Yasmina:'.$result[0]['NOM_ATENCION_CORTO'].' :' , 0 , 0, 'L');
		
		$pdf->SetXY(52, $y+234+15);
		$pdf->MultiCell(450, 14, 'Por la presente autorizo debitar de cta. cte. US$ N°1200-2122-57, COMERCIAL TODOINOX LTDA., (Rut: 89.257.000-0) la suma de US$ '.$result[0]['MONTO_PAGO'].' y enviar transferencia por pago anticipado de importacion a:' , 0 , 'J', false);
	
		$pdf->SetXY(52, $y+284+15);
		$pdf->Cell(47, 15, 'NOMBRE BENEFICIARIO:' , 0 , 0, 'L');
		$pdf->SetXY(85, $y+304+15);
		//$pdf->Cell(47, 15, $result[0]['BENEFICIARY_NAMEEMP'] , 0 , 0, 'L');
		$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_NAMEEMP'], 0 , 'J', false);	
		
		$pdf->SetXY(85, $y+319+15);
		//$pdf->Cell(418, 15, $result[0]['BENEFICIARY_DIREMP'] , 0 , 0, 'L');
		$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_DIREMP'], 0 , 'J', false);
		
		$pdf->SetXY(52, $y+390+15);
		$pdf->Cell(47, 15, 'BANCO BENEFICIARIO:' , 0 , 0, 'L');
		$pdf->SetXY(85, $y+410+15);
		//$pdf->Cell(47, 15, $result[0]['BENEFICIARY_NAMEBANK'] , 0 , 0, 'L');
		$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_NAMEBANK'], 0 , 'J', false);
	
		$pdf->SetXY(85, $y+425+15);
		//$pdf->Cell(47, 15, $result[0]['BENEFICIARY_DIRBANK'] , 0 , 0, 'L');
		$pdf->MultiCell(418, 15, $result[0]['BENEFICIARY_DIRBANK'], 0 , 'J', false);
		
		$pdf->SetXY(85, $y+490+15);
		$pdf->Cell(47, 15, 'ACCOUNT N°' , 0 , 0, 'L');
		$pdf->SetXY(185, $y+490+15);
		$pdf->Cell(47, 15, $result[0]['BP_ACCOUNT_NUMBER'] , 0 , 0, 'L');
		$pdf->SetXY(85, $y+505+15);
		$pdf->Cell(47, 15, 'SWIFT' , 0 , 0, 'L');
		$pdf->SetXY(185, $y+505+15);
		$pdf->Cell(47, 15, $result[0]['BP_SWIFT'] , 0 , 0, 'L');
		
		$pdf->SetXY(52, $y+563+15);
		$pdf->Cell(47, 15, 'GASTOS  OUR:' , 0 , 0, 'L');
		
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY(145, $y+563+15);
		$pdf->Cell(47, 15, 'VALUTA 24 HORAS.' , 0 , 0, 'L');
		
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY(52, $y+578+15);
		//$pdf->MultiCell(400, 14, 'Los gastos que genere esta operación cargarlos a nuestra cta. cte. nro. 0211-3432-51 Favor enviarnos copia de Swift al correo electrónico '.$result[0]['MAIL'].'.' , 0 , 'J', false);
		$pdf->MultiCell(400, 14, 'Los gastos que genere esta operación cargarlos a nuestra cta. cte. nro. 0211-3432-51 Favor enviarnos copia de Swift al correo electrónico mscianca@todoinox.cl .-', 0 , 'J', false);
		
		$pdf->SetXY(52, $y+630+15);
		$pdf->Cell(47, 15, 'Atentamente,' , 0 , 0, 'L');
		
		$pdf->Line(55,700,230,700);
		$pdf->SetXY(65, $y+705+15);
		//$pdf->Cell(155, 15, $result[0]['NOM_USUARIO'] , 0 , 0, 'C');
		//$pdf->SetXY(69, $y+720+15);
		$pdf->Cell(47, 15, 'COMERCIAL TODOINOX LTDA.' , 0 , 0, 'L');
	}	
}	
?>