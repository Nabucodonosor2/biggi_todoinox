<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../cx_cot_extranjera/class_PDF2.php");

class informe_cot_extranjera extends reporte {	
	function informe_cot_extranjera($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	
	function header_items(&$pdf, $y0) {
		$pdf->SetFont('Arial','B',8);
		$x = 8;
		$y = $y0;
		$pdf->SetTextColor(0,0,10);//TEXTOS negros
		$pdf->SetXY($x+26, $y+140);
		$pdf->Cell(23, 10, 'IT' , 0 , 0, 'C');
		$pdf->SetXY($x+24, $y+135);
		$pdf->Cell(27, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+51, $y+140);
		$pdf->Cell(70, 10, 'SHIPP MAR' , 0 , 0, 'C');
		$pdf->SetXY($x+51, $y+135);
		$pdf->Cell(70, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+121, $y+140);
		$pdf->Cell(99, 10, 'CODE' , 0 , 0, 'C');
		$pdf->SetXY($x+121, $y+135);
		$pdf->Cell(99, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+220, $y+140);
		$pdf->Cell(208, 10, 'DESCRIPTION' , 0 , 0, 'C');
		$pdf->SetXY($x+220, $y+135);
		$pdf->Cell(208, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+428, $y+140);
		$pdf->Cell(31, 10, 'QTY' , 0 , 0, 'C');
		$pdf->SetXY($x+428, $y+135);
		$pdf->Cell(31, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+459, $y+140);
		$pdf->Cell(52, 10, 'UNIT PRICE' , 0 , 0, 'C');
		$pdf->SetXY($x+459, $y+135);
		$pdf->Cell(52, 18, '' , 1, 1, 'C');
		$pdf->SetXY($x+511, $y+140);
		$pdf->Cell(52, 10, 'TOTAL' , 0 , 0, 'C');
		$pdf->SetXY($x+511, $y+135);
		$pdf->Cell(52, 18, '' , 1 , 1, 'C');
	}
	
	function dibuja_uno(&$pdf, $result){

		$margen= 0;
		$cod_cx_cot_extranjera = $result['COD_CX_COT_EXTRANJERA'];	
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY($x+20, $y+90);
		
		//MH 27052022 AL PARECER YA NO USAN EL CORRELATIVO EN FORMATO GLOSA, CUANDO NO LO LLENAN QUEDA CON VALOR 'null' por eso hace la validacion
		$USA_CORRELATIVO_INTERNO = $result['CORRELATIVO_COT_EXTRANJERA'];
		//$aux_titulo_quote = 'QUOTATION '.$result['ALIAS_PROVEEDOR_EXT'].' N° '.$result['CORRELATIVO_COT_EXTRANJERA'];
		if ($USA_CORRELATIVO_INTERNO == 'null') {
			$aux_titulo_quote = 'QUOTATION '.$result['ALIAS_PROVEEDOR_EXT'].' N° '.$result['COD_CX_COT_EXTRANJERA'];
		}
		ELSE{
			$aux_titulo_quote = 'QUOTATION '.$result['ALIAS_PROVEEDOR_EXT'].' N° '.$result['CORRELATIVO_COT_EXTRANJERA'];
			}
			
		
		$pdf->Cell(565, 15,$aux_titulo_quote, 0, 0, 'C');
		$pdf->SetXY($x+335, $y+90);
		$pdf->SetFont('Arial','B',4);
		$pdf->SetXY($x+540, $y+93);
		$pdf->Cell(47, 15, $result['COD_PROVEEDOR_EXT'] , 0 , 0, 'R');
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+430, $y+120);
		$pdf->Cell(47, 15, 'DATE' , 0 , 0, 'R');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+505, $y+120);
		$pdf->Cell(47, 15, $result['FECHA_CX_COT_EXTRANJERA'] , 0 , 0, 'R');
		$pdf->Line(483,135,550,135);
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+25, $y+120);
		$pdf->Cell(47, 15, 'COMPANY' , 0 , 0, 'L');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+90, $y+120);
		$pdf->Cell(47, 15, $result['NOM_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(90,135,430,135);
		$pdf->SetXY($x+25, $y+145);
		$pdf->Cell(47, 15, 'ADDRESS' , 0 , 0, 'L');
		$pdf->SetXY($x+90, $y+145);
		$pdf->Cell(47, 15, $result['DIRECCION'] , 0 , 'L');
		$pdf->SetXY($x+25, $y+160);
		$pdf->Cell(47, 15, 'CITY' , 0 , 0, 'L');
		$pdf->SetXY($x+90, $y+160);
		$pdf->MultiCell(250, 15, $result['NOM_CIUDAD_4D'] , 0 , 'L');
		//$pdf->Line(120,224,340,224);
		$pdf->SetXY($x+430, $y+160);
		$pdf->Cell(47, 15, 'COUNTRY' , 0 , 0, 'L');
		$pdf->SetXY($x+505, $y+160);
		$pdf->MultiCell(47, 15, $result['NOM_PAIS_4D'] , 0 , 'R');
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+25, $y+185);
		$pdf->Cell(47, 15, 'CONTACT' , 0 , 0, 'L');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+90, $y+185);
		$pdf->MultiCell(147, 15, $result['NOM_CONTACTO_PROVEEDOR_EXT'] , 0 , 'L');
		//$pdf->Line($x+90,210,340,210);	
		$pdf->SetXY($x+210, $y+185);
		$pdf->Cell(47, 15, 'PHONE:' , 0 , 0, 'L');
		$pdf->SetXY($x+245, $y+185);
		$pdf->MultiCell(147, 15, $result['TELEFONO'] , 0 , 'L');
		$pdf->Line(x+25,210,550,210);
		$pdf->Line(x+25,212,550,212);
		//$pdf->SetXY($x+400, $y+190);
		//$pdf->Cell(47, 15, 'EMAIL' , 0 , 0, 'L');
		$pdf->SetXY($x+400, $y+185);
		$pdf->MultiCell(180, 15, $result['MAIL'], 0 , 'L');	

		$pdf->SetXY($x+210, $y+195);
		$pdf->Cell(47, 15, 'CEL:' , 0 , 0, 'L');
		$pdf->SetXY($x+245, $y+195);
		$pdf->MultiCell(147, 15, $result['TELEFONO_MOVIL'] , 0 , 'L');		
		
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',13);
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+25, $y+220);
		$pdf->Cell(47, 15, 'PORT OF LOADING' , 0 , 0, 'L');
		$pdf->SetXY($x+130, $y+220);
		$pdf->MultiCell(147, 15, $result['NOM_CX_PUERTO_SALIDA'], 0 , 'L');
		$pdf->Line(130,235,290,235);
		$pdf->SetXY($x+360, $y+220);
		$pdf->Cell(47, 15, 'PORT OF DISCHARGE' , 0 , 0, 'R');
		$pdf->SetXY($x+405, $y+220);
		$pdf->MultiCell(147, 15,$result['NOM_CX_PUERTO_ARRIBO'], 0 , 'R');
		$pdf->Line(420,235,550,235);
		
		$pdf->SetXY($x+25, $y+235);
		$pdf->Cell(47, 15, 'PURCHASE CLAUSE' , 0 , 0, 'L');
		$pdf->SetXY($x+130, $y+235);
		$pdf->MultiCell(147, 15, $result['NOM_CX_CLAUSULA_COMPRA'], 0 , 'L');
		$pdf->Line(130,250,290,250);
		$pdf->SetXY($x+360, $y+235);
		$pdf->Cell(47, 15, 'DELIVERY DATE' , 0 , 0, 'R');
		$pdf->SetXY($x+405, $y+235);
		$pdf->MultiCell(147, 15,$result['DELIVERY_DATE'], 0 , 'R');
		$pdf->Line(420,250,550,250);
	
		$pdf->SetXY($x+25, $y+250);
		$pdf->Cell(47, 15, 'T. PAYMENTS' , 0 , 0, 'L');
		$pdf->SetXY($x+130, $y+250);
		$pdf->MultiCell(221, 15, $result['NOM_CX_TERMINO_PAGO'], 0 , 'L');
		$pdf->Line(130,265,290,265);
		$pdf->SetXY($x+360, $y+250);
		$pdf->Cell(47, 15, 'CURRENCY' , 0 , 0, 'R');
		$pdf->SetXY($x+405, $y+250);
		$pdf->MultiCell(147, 15,$result['NOM_CX_MONEDA'], 0 , 'R');
		$pdf->Line(420,265,550,265);	
		
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+25, $y+280);
		$pdf->Cell(47, 15, 'REFERENCE' , 0 , 0, 'L');
		$pdf->SetXY($x+130, $y+280);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(420, 15,$result['REFERENCIA'], 0, 'L');
		$pdf->Line(130,295,550,295);
		$pdf->SetFont('Arial','',9);
		////FIN///
		////*****CUADRO*****/////
		$pdf->SetFont('Arial','B',8);
		//$pdf->Line(430,300,571,300);
		//$pdf->Line(430,300,430,323);
		//$pdf->Line(571,300,571,323);
		//$pdf->Line(430,323,571,323);
		
		$pdf->SetXY($x+436, $y+302);
		$pdf->Cell(135, 21, '' , 1 , 1, 'C');
		
		$pdf->SetXY($x+428, $y+300);
		$pdf->MultiCell(147, 15, $result['NOM_CX_MONEDA'] , 0 , 'C');
		$pdf->SetXY($x+430, $y+310);
		$pdf->MultiCell(45, 15, $result['NOM_CX_CLAUSULA_COMPRA'] , 0 , 'C');
		$pdf->SetXY($x+475, $y+310);
		$pdf->MultiCell(97, 15, $result['NOM_CX_PUERTO_SALIDA'] , 0 , 'C');
		$pdf->SetFont('Arial','',8);
		////***FIN CUADRO****///
	
		//***TABLA DE ITEMS***//
		$this->header_items($pdf, $margen + 3 + 185 + 0);
		//$pdf->SetFont('Arial','',9);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT ice.COD_CX_ITEM_COT_EXTRANJERA
						,ice.ITEM
						,ice.COD_EQUIPO_OC_EX
						,ice.COD_CX_COT_EXTRANJERA
						,ice.DESC_EQUIPO_OC_EX 
						,ice.CANTIDAD
						,ice.PRECIO
						,ice.COD_PRODUCTO
						,ice.CANTIDAD * ice.PRECIO TOTAL
						,(SELECT SUM (ice.CANTIDAD * ice.PRECIO) 
							FROM CX_ITEM_COT_EXTRANJERA ice
							WHERE ice.COD_CX_COT_EXTRANJERA=$cod_cx_cot_extranjera) TOTAL_FINAL
				FROM CX_ITEM_COT_EXTRANJERA ice
				WHERE ice.COD_CX_COT_EXTRANJERA=$cod_cx_cot_extranjera";
		$result2 = $db->build_results($sql);
		$i = 1;
		$y = $pdf->GetY() - 24; 
		$y = $y + 24;
		foreach($result2 as $row){
			$pdf->SetFont('Arial','',10);
			$y_antes = $pdf->GetY();
			
			//199
			$pdf->SetXY($x+228, $y);
			$pdf->MultiCell(208, 14,$row['DESC_EQUIPO_OC_EX'], 1, 'L');
			
			$y_despues = $pdf->GetY();
			
			$pdf->SetFont('Arial','',8);
			$pdf->SetXY($x+32,$y);
			$pdf->SetFont('Arial','',10);
			$pdf->Cell(27,$y_despues - $y_antes,$row['ITEM'] , 1 ,1, 'L');
			$pdf->SetXY($x+59,$y);
			$pdf->Cell(70,$y_despues - $y_antes,$row['COD_PRODUCTO'], 1 , 1, 'L');
			$pdf->SetXY($x+129,$y);
			$pdf->Cell(99,$y_despues - $y_antes,$row['COD_EQUIPO_OC_EX'], 1 , 1, 'L');
			$pdf->SetXY($x+436,$y);
			$pdf->Cell(31,$y_despues - $y_antes,number_format($row['CANTIDAD'],0), 1 , 1, 'R');
			$pdf->SetXY($x+467,$y);
			$pdf->Cell(52,$y_despues - $y_antes,number_format($row['PRECIO'],2, ',', '.'), 1 , 1, 'R');
			$pdf->SetXY($x+519,$y);
			$pdf->Cell(52,$y_despues - $y_antes,number_format($row['TOTAL'],2, ',', '.'), 1 , 1, 'R');
			$i++;
			
			$y += $y_despues - $y_antes;	
			$ypos = $pdf->GetY();
			if ($ypos >= 680) {
				$pdf->AddPage();                 
				$this->header_items($pdf, -22);
				$y = $pdf->GetY(); 		
				$i = 1;
			}
		}
		/////TOTAL ////
		$i = 1;		
		$x = 8;
		$ypos = $pdf->GetY();
		if ($ypos >= 680) {
			$pdf->AddPage();                 
			$ypos = $pdf->GetY()+30; 		
		}else{
			$ypos = $pdf->GetY()+10;
		}
		///////TOTOAL////////
		$pdf->SetFont('Arial','B',11);
		$pdf->SetXY($x+200, $ypos);
		$pdf->Cell(47, 15, 'TOTAL' , 0 , 0, 'R');
		$pdf->SetXY($x+160, $ypos);
		$pdf->MultiCell(240, 15,$result['NOM_CX_CLAUSULA_COMPRA'], 0, 'C');
		$pdf->SetXY($x+250, $ypos);
		$pdf->MultiCell(240, 15,$result['NOM_CX_PUERTO_SALIDA'], 0, 'C');
		$pdf->SetXY($x+325, $ypos);
		$pdf->MultiCell(240, 15,$result['NOM_CX_MONEDA'], 0, 'C');
		$pdf->SetXY($x+462, $ypos);
		$pdf->MultiCell(102, 17,number_format($row['TOTAL_FINAL'],2, ',', '.'), 1, 'R');
		/////FIN TOTAL////
	
		$i = 1;		
		$x = 8;
		$ypos = $pdf->GetY();
		if ($ypos >= 660) {
			$pdf->AddPage();                 
			$ypos = $pdf->GetY()+30; 		
		}else{
			$ypos = $pdf->GetY()+10;
		}

	
		$pdf->Line(350,$ypos+45,571,$ypos+45);
		$pdf->SetXY($x+335, $ypos+50);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(240, 15,$result['NOM_USUARIO'], 0, 'C');
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+335, $ypos+65);
		$pdf->MultiCell(240, 15,'COMERCIAL TODOINOX LTDA.', 0, 'C');
		////////FIN FIRMA////////
		$i = 1;		
		$x = 8;
		$ypos = $pdf->GetY();
		if ($ypos >= 680) {
			$pdf->AddPage();                 
			$ypos = $pdf->GetY()+30; 		
		}else{
			$ypos = $pdf->GetY()+10;
		}
		////COMMENTS////
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+10, $ypos);
		$pdf->Cell(80, 11, 'COMMENTS' , 0 , 0, 'C');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+23, $ypos+10);
		$pdf->MultiCell(540, 45,$result['OBSERVACIONES'], 1, 'J');
		/////FIN COMMENTS///
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
	
	function make_reporte() {
		$p = new ReportParser();
		$p->parseRP($this->xml);
		$rdata = new MySQLRD($this->sql);

		$pdf = PDF2::makePDF(array($p), array($this->labels), array($rdata), $this->con_logo,$this->orientation,$this->unit,$this->format);		
		
		$pdf->SetTitle($this->titulo);
		$this->modifica_pdf($pdf);
		$pdf->Output($this->titulo, 'I');
	}
}	
?>