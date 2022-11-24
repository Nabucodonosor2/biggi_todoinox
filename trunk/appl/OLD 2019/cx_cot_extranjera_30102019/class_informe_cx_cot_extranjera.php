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
		$pdf->SetXY($x+66, $y+140);
		$pdf->Cell(30, 10, 'SHIPP MAR' , 0 , 0, 'C');
		$pdf->SetXY($x+51, $y+135);
		$pdf->Cell(60, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+135, $y+140);
		$pdf->Cell(30, 10, 'CODE' , 0 , 0, 'C');
		$pdf->SetXY($x+111, $y+135);
		$pdf->Cell(80, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+275, $y+140);
		$pdf->Cell(30, 10, 'DESCRIPTION' , 0 , 0, 'C');
		$pdf->SetXY($x+191, $y+135);
		$pdf->Cell(198, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+383, $y+140);
		$pdf->Cell(45, 10, 'QTY' , 0 , 0, 'C');
		$pdf->SetXY($x+389, $y+135);
		$pdf->Cell(33, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+441, $y+140);
		$pdf->Cell(30, 10, 'UNIT PRICE' , 0 , 0, 'C');
		$pdf->SetXY($x+422, $y+135);
		$pdf->Cell(67, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+513, $y+140);
		$pdf->Cell(30, 10, 'TOTAL' , 0 , 0, 'C');
		$pdf->SetXY($x+489, $y+135);
		$pdf->Cell(74, 18, '' , 1 , 1, 'C');
	}
	
	function dibuja_uno(&$pdf, $result){
		////TITULO///
		$margen= 0;
		$cod_cx_cot_extranjera = $result['COD_CX_COT_EXTRANJERA'];	
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY($x+295, $y+100+(15*$i));
		$pdf->Cell(47, 15, 'QUOTATION N° ' , 0 , 0, 'R');
		$pdf->SetXY($x+335, $y+100+(15*$i));
		$pdf->Cell(47, 15, $result['COD_CX_COT_EXTRANJERA'] , 0 , 0, 'L');
		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($x+490, $y+100+(15*$i));
		$pdf->Cell(47, 15, 'ID N° ' , 0 , 0, 'R');
		$pdf->SetXY($x+525, $y+100+(15*$i));
		$pdf->Cell(47, 15, $result['COD_PROVEEDOR_EXT'] , 0 , 0, 'R');
		////FIN TITULO///
		
		////DATOS PROVEEDOR///
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',13);
		$pdf->SetXY($x+60, $y+130+(15*$i));
		$pdf->Cell(47, 15, 'PROVIDER ' , 0 , 0, 'R');
		$pdf->SetXY($x+118, $y+130+(15*$i));
		$pdf->Cell(47, 15, $result['ALIAS_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(120,145,230,145);
		$pdf->SetXY($x+430, $y+130+(15*$i));
		$pdf->Cell(47, 15, 'DATE' , 0 , 0, 'R');
		$pdf->SetXY($x+505, $y+130+(15*$i));
		$pdf->Cell(47, 15, $result['FECHA_CX_COT_EXTRANJERA'] , 0 , 0, 'R');
		$pdf->Line(483,145,570,145);
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+34, $y+150+(15*$i));
		$pdf->Cell(47, 15, 'COMPANY' , 0 , 0, 'R');
		$pdf->SetXY($x+118, $y+150+(15*$i));
		$pdf->Cell(47, 15, $result['NOM_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(120,164,570,164);
		$pdf->SetXY($x+31, $y+170+(15*$i));
		$pdf->Cell(47, 15, 'ADDRESS' , 0 , 0, 'R');
		$pdf->SetXY($x+118, $y+170+(15*$i));
		$pdf->MultiCell(147, 15, $result['DIRECCION'] , 0 , 'L');
		$pdf->SetXY($x+350, $y+170+(15*$i));
		$pdf->Cell(47, 15, 'PHONE' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+170+(15*$i));
		$pdf->MultiCell(147, 15, $result['TELEFONO'] , 0 , 'L');
		$pdf->Line(423,184,570,184);
		$pdf->SetXY($x+9, $y+210+(15*$i));
		$pdf->Cell(47, 15, 'CITY' , 0 , 0, 'R');
		$pdf->SetXY($x+118, $y+210+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CIUDAD_4D'] , 0 , 'L');
		$pdf->Line(120,224,340,224);
		$pdf->SetXY($x+336, $y+190+(15*$i));
		$pdf->Cell(47, 15, 'FAX' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+190+(15*$i));
		$pdf->MultiCell(147, 15, $result['POST_OFFICE_BOX'], 0 , 'L');
		$pdf->Line(423,203,570,203);
		$pdf->SetXY($x+362, $y+210+(15*$i));
		$pdf->Cell(47, 15, 'COUNTRY' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+210+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_PAIS_4D'] , 0 , 'L');
		$pdf->Line(423,223,570,223);
		////FIN ///
		
		////DATOS CONTACTO///
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',13);
		$pdf->SetXY($x+55, $y+240+(15*$i));
		$pdf->Cell(47, 15, 'CONTACT ' , 0 , 0, 'R');
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+21, $y+260+(15*$i));
		$pdf->Cell(47, 15, 'PHONE' , 0 , 0, 'R');
		$pdf->SetXY($x+138, $y+260+(15*$i));
		$pdf->Cell(47, 15, '' , 0 , 0, 'R');
		/*$pdf->Line(120,164,570,164);*/
		$pdf->SetXY($x+350, $y+260+(15*$i));
		$pdf->Cell(47, 15, 'E-MAIL' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+260+(15*$i));
		$pdf->MultiCell(147, 15, $result['MAIL'] , 0 , 'L');
		$pdf->SetXY($x+72, $y+280+(15*$i));
		$pdf->Cell(47, 15, 'PORT OF LOADING' , 0 , 0, 'R');
		$pdf->SetXY($x+130, $y+280+(15*$i));
		$pdf->MultiCell(147, 15, '' , 0 , 'L');
		$pdf->Line(130,293,290,293);
		$pdf->SetXY($x+360, $y+280+(15*$i));
		$pdf->Cell(47, 15, 'PORT OF DISCHARGE' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+280+(15*$i));
		$pdf->MultiCell(147, 15, '' , 0 , 'L');
		$pdf->Line(420,293,570,293);
		$pdf->SetXY($x+77, $y+300+(15*$i));
		$pdf->Cell(47, 15, 'PURCHASE CLAUSE' , 0 , 0, 'R');
		$pdf->SetXY($x+130, $y+300+(15*$i));
		$pdf->MultiCell(147, 15,$result['NOM_CX_CLAUSULA_COMPRA'], 0 , 'L');
		$pdf->Line(130,313,290,313);
		$pdf->SetXY($x+335, $y+300+(15*$i));
		$pdf->Cell(47, 15, 'DELIVERY DATE' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+300+(15*$i));
		$pdf->MultiCell(147, 15, '' , 0 , 'L');
		$pdf->Line(420,313,570,313);
		$pdf->SetXY($x+49, $y+320+(15*$i));
		$pdf->Cell(47, 15, 'T. PAYMENTS' , 0 , 0, 'R');
		$pdf->SetXY($x+130, $y+320+(15*$i));
		$pdf->MultiCell(147, 15,$result['NOM_CX_PUERTO_SALIDA'], 0 , 'L');
		$pdf->Line(130,332,340,332);
		$pdf->SetXY($x+370, $y+320+(15*$i));
		$pdf->Cell(47, 15, 'CURRENCY' , 0 , 0, 'R');
		$pdf->SetXY($x+420, $y+320+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CX_MONEDA'] , 0 , 'L');
		$pdf->Line(420,332,570,332);
		$pdf->SetXY($x+44, $y+340+(15*$i));
		$pdf->Cell(47, 15, 'REFERENCE' , 0 , 0, 'R');
		$pdf->SetXY($x+72, $y+340+(15*$i));
		$pdf->MultiCell(147, 15,'', 0 , 'L');
		$pdf->Line(130,352,570,352);
		////FIN///
		////*****CUADRO*****/////
		$pdf->SetFont('Arial','',8);
		$pdf->Line(430,365,571,365);
		$pdf->Line(430,365,430,398);
		$pdf->Line(571,365,571,398);
		$pdf->Line(430,398,571,398);
		$pdf->SetXY($x+428, $y+366+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CX_MONEDA'] , 0 , 'C');
		$pdf->SetXY($x+388, $y+385+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CX_CLAUSULA_COMPRA'] , 0 , 'C');
		$pdf->SetXY($x+456, $y+385+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CX_PUERTO_SALIDA'] , 0 , 'C');
		////***FIN CUADRO****///
	
		//***TABLA DE ITEMS***//
		$this->header_items($pdf, $margen + 3 + 245 + 15);
		//$pdf->SetFont('Arial','',9);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT ice.COD_CX_ITEM_COT_EXTRANJERA
						,ice.COD_EQUIPO_OC_EX
						,ice.COD_CX_COT_EXTRANJERA
						,ice.DESC_EQUIPO_OC_EX 
						,ice.CANTIDAD
						,ice.PRECIO
						,ice.COD_PRODUCTO
						,ice.CANTIDAD * ice.PRECIO TOTAL
						,(SELECT SUM (ice.CANTIDAD * ice.PRECIO) 
							FROM CX_ITEM_COT_EXTRANJERA ice
							WHERE ice.COD_CX_COT_EXTRANJERA=1) TOTAL_FINAL
				FROM CX_ITEM_COT_EXTRANJERA ice
				WHERE ice.COD_CX_COT_EXTRANJERA=$cod_cx_cot_extranjera";
		$result2 = $db->build_results($sql);
		$i = 1;
		$y = $pdf->GetY() - 24; 
		$y = $y + 24;
		foreach($result2 as $row){
			$pdf->SetFont('Arial','',8);
			$y_antes = $pdf->GetY();
			$pdf->SetXY($x+199, $y);
			$pdf->MultiCell(198, 12,$row['DESC_EQUIPO_OC_EX'].$row['DESC_EQUIPO_OC_EX'], 1, 'L');
			$y_despues = $pdf->GetY();
			$pdf->SetXY($x+32,$y);
			$pdf->Cell(27,$y_despues - $y_antes,$row['COD_CX_ITEM_COT_EXTRANJERA'] , 1 ,1, 'R');
			$pdf->SetXY($x+59,$y);
			$pdf->Cell(60,$y_despues - $y_antes,$row['COD_PRODUCTO'], 1 , 1, 'R');
			$pdf->SetXY($x+119,$y);
			$pdf->Cell(80,$y_despues - $y_antes,$row['COD_EQUIPO_OC_EX'], 1 , 1, 'R');
			$pdf->SetXY($x+397,$y);
			$pdf->Cell(33,$y_despues - $y_antes,number_format($row['CANTIDAD'],0), 1 , 1, 'R');
			$pdf->SetXY($x+430,$y);
			$pdf->Cell(67,$y_despues - $y_antes,number_format($row['PRECIO'],2), 1 , 1, 'R');
			$pdf->SetXY($x+497,$y);
			$pdf->Cell(74,$y_despues - $y_antes,number_format($row['TOTAL'],2), 1 , 1, 'R');
			$i++;
			
			$y += $y_despues - $y_antes;	
			$ypos = $pdf->GetY();
			if ($ypos >= 630) {
				$pdf->AddPage();                 
				$this->header_items($pdf, -22);
				$y = $pdf->GetY(); 		
				$i = 1;
			}
		}
		/////TOTAL ////
		$i = 1;		
		$x = 8;
		$ypos = $pdf->GetY()-300;
		///////TOTOAL////////
		$pdf->SetFont('Arial','B',11);
		$pdf->SetXY($x+200, $ypos+312);
		$pdf->Cell(47, 15, 'TOTAL' , 0 , 0, 'R');
		$pdf->SetXY($x+160, $ypos+312);
		$pdf->MultiCell(240, 15,$result['NOM_CX_CLAUSULA_COMPRA'], 0, 'C');
		$pdf->SetXY($x+250, $ypos+312);
		$pdf->MultiCell(240, 15,$result['NOM_CX_PUERTO_SALIDA'], 0, 'C');
		$pdf->SetXY($x+325, $ypos+312);
		$pdf->MultiCell(240, 15,$result['NOM_CX_MONEDA'], 0, 'C');
		$pdf->SetXY($x+462, $ypos+309);
		$pdf->MultiCell(102, 17,number_format($row['TOTAL_FINAL'],2), 1, 'R');
		/////FIN TOTAL////
		$pdf->Line(350,474+$ypos,571,474+$ypos);
		$pdf->SetXY($x+335, $ypos+475);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(240, 15,$result['NOM_USUARIO'], 0, 'C');
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+335, $ypos+497);
		$pdf->MultiCell(240, 15,'COMERCIAL TODOINOX LTDA.', 0, 'C');
		////////FIN FIRMA////////
		////COMMENTS////
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+10, $ypos+370);
		$pdf->Cell(80, 11, 'COMMENTS' , 0 , 0, 'C');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+23, $ypos+389);
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