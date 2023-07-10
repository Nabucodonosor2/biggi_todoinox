<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

class informe_oc_extranjera_es extends reporte {	
	function informe_oc_extranjera_es($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
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
		$pdf->SetXY($x+305, $y+140);
		$pdf->Cell(30, 10, 'DESCRIPTION' , 0 , 0, 'C');
		$pdf->SetXY($x+191, $y+135);
		$pdf->Cell(249, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+435, $y+140);
		$pdf->Cell(45, 10, 'QTY' , 0 , 0, 'C');
		$pdf->SetXY($x+440, $y+135);
		$pdf->Cell(33, 18, '' , 1 , 1, 'C');
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($x+480, $y+140);
		$pdf->Cell(30, 10, 'UNITPRICE' , 0 , 0, 'C');
		$pdf->SetXY($x+473, $y+135);
		$pdf->Cell(45, 18, '' , 1 , 1, 'C');
		$pdf->SetXY($x+525, $y+140);
		$pdf->Cell(30, 10, 'TOTAL' , 0 , 0, 'C');
		$pdf->SetXY($x+518, $y+135);
		$pdf->Cell(45, 18, '' , 1 , 1, 'C');
	}
	
	function dibuja_uno(&$pdf, $result){


		$pdf->SetDrawColor(255, 255, 255);
		$pdf->SetFillColor(255, 255, 255);
		$pdf->Rect(10, 65, 580, 13, 'DF');
		$pdf->Rect(160, 43, 330, 13, 'DF');
		$pdf->SetDrawColor(0, 0, 0);

		/// CABECERA
		$margen= -0;
		$x = 0;
		$cod_cx_oc_extranjera = $result['COD_CX_OC_EXTRANJERA'];	
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(140, 60);
		$pdf->Cell(47, 15, 'PURCHASE ORDER N°' , 0 , 0, 'L');
		$pdf->SetXY(295, 60	);
		$pdf->Cell(100, 15, $result['CORRELATIVO_OC'], 0 , 0, 'L');


		////DATOS PROVEEDOR///
		$pdf->SetTextColor(0,0,10);	/// TEXTOS AZUL
		$pdf->SetFont('Arial','B',12);
		
		$pdf->SetXY(54, 85);
		$pdf->Cell(47, 15, 'PROVIDER ' , 0 , 0, 'R');
		$pdf->SetXY($x+117, 85);
		$pdf->Cell(110, 15, $result['ALIAS_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(120,98,230,98);
		$pdf->SetXY($x+240, 85);
		$pdf->Cell(100, 15, 'N° QUOTATION' ,0 , 0, 'L');
		$pdf->SetXY($x+350, 85);
		$pdf->Cell(100, 15, $result['COD_CX_COT_EXTRANJERA'] , 0 , 0, 'L');
		$pdf->Line(347,98,430,98);
		$pdf->SetXY($x+430, 85);
		$pdf->Cell(47, 15, 'DATE' , 0 , 0, 'R');
		$pdf->SetXY($x+480, 85);
		$pdf->Cell(100, 15, $result['FECHA_CX_OC_EXTRANJERA'] , 0 , 0, 'L');
		$pdf->Line(483,98,570,98);
		
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+25, $y+130);
		$pdf->Cell(47, 15, 'COMPANY' , 0 , 0, 'L');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+90, $y+130);
		$pdf->Cell(47, 15, $result['NOM_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(90,145,430,145);
		$pdf->SetXY($x+25, $y+150);
		$pdf->Cell(47, 15, 'ADDRESS' , 0 , 0, 'L');
		$pdf->SetXY($x+90, $y+150);
		$pdf->Cell(47, 15, $result['DIRECCION'] , 0 , 'L');
		$pdf->SetXY($x+25, $y+170);
		$pdf->Cell(47, 15, 'CITY' , 0 , 0, 'L');
		$pdf->SetXY($x+90, $y+170);
		$pdf->MultiCell(147, 15, $result['NOM_CIUDAD_4D'] , 0 , 'L');
		//$pdf->Line(120,224,340,224);
		$pdf->SetXY($x+430, $y+170);
		$pdf->Cell(47, 15, 'COUNTRY' , 0 , 0, 'L');
		$pdf->SetXY($x+505, $y+170);
		$pdf->MultiCell(47, 15, $result['NOM_PAIS_4D'] , 0 , 'R');
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+25, $y+190);
		$pdf->Cell(47, 15, 'CONTACT' , 0 , 0, 'L');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+90, $y+190);
		$pdf->MultiCell(147, 15, $result['NOM_CONTACTO_PROVEEDOR_EXT'] , 0 , 'L');
		//$pdf->Line($x+90,210,340,210);	
		$pdf->SetXY($x+230, $y+190);
		$pdf->Cell(47, 15, 'PHONE' , 0 , 0, 'L');
		$pdf->SetXY($x+280, $y+190);
		$pdf->MultiCell(147, 15, $result['TELEFONO'] , 0 , 'L');
		$pdf->Line(x+25,210,550,210);
		$pdf->Line(x+25,212,550,212);
		$pdf->SetXY($x+410, $y+190);
		$pdf->Cell(47, 15, 'EMAIL' , 0 , 0, 'L');
		$pdf->SetXY($x+435, $y+190);
		$pdf->MultiCell(120, 15, $result['MAIL'], 0 , 'R');	
		//$pdf->SetFont('Arial','',9);
		//$pdf->SetXY($x+34, 105);
		//$pdf->Cell(47, 15, 'COMPANY' , 0 , 0, 'R');
		//$pdf->SetXY($x+118, 105);
		//$pdf->Cell(47, 15, $result['NOM_PROVEEDOR_EXT'] , 0 , 0, 'L');
		//$pdf->Line(120,118,570,118);
		//$pdf->SetXY($x+31, 128);
		//$pdf->Cell(47, 15, 'ADDRESS' , 0 , 0, 'R');
		//$pdf->SetXY($x+118, 128);
		//$pdf->MultiCell(147, 15, $result['DIRECCION'] , 0 , 'L');
		//$pdf->SetXY($x+350, 128);
		//$pdf->Cell(47, 15, 'PHONE' , 0 , 0, 'R');
		//$pdf->SetXY($x+420, 128);
		//$pdf->MultiCell(147, 15, $result['TELEFONO_PROVEEDOR'] , 0 , 'L');
		//$pdf->Line(423,142,570,142);
		//$pdf->SetXY($x+9, 168);
		//$pdf->Cell(47, 15, 'CITY' , 0 , 0, 'R');
		//$pdf->SetXY($x+118, 168);
		//$pdf->MultiCell(147, 15, $result['NOM_CIUDAD_4D'] , 0 , 'L');
		//$pdf->Line(120,182,340,182);
		//$pdf->SetXY($x+336, 148);
		//$pdf->Cell(47, 15, 'FAX' , 0 , 0, 'R');
		//$pdf->SetXY($x+420, 148);
		//$pdf->MultiCell(147, 15, $result['FAX_PROVEEDOR'] , 0 , 'L');
		//$pdf->Line(423,162,570,162);
		//$pdf->SetXY($x+362, 168);
		//$pdf->Cell(47, 15, 'COUNTRY' , 0 , 0, 'R');
		//$pdf->SetXY($x+420, 168);
		//$pdf->MultiCell(147, 15, $result['NOM_PAIS_4D'] , 0 , 'L');
		//$pdf->Line(423,182,570,182);
		
		////FIN ///
		
		////DATOS CONTACTO///
		$pos = 4;
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

		////*****INI CUADRO*****/////
		$pdf->SetFont('Arial','',7);
		$pdf->Line(481,310,571,310); //LINEA SUPERIOR
		$pdf->Line(481,310,481,398); //LINEA LATERAL IZQUIERDA
		$pdf->Line(571,310,571,398); //LINEA LATERAL DERECHA
		//$pdf->Line(430,398,571,398);
		$pdf->SetXY($x+526, $y+320+(15*$i));
		$pdf->MultiCell(45, 15, $result['NOM_CX_CLAUSULA_COMPRA'] , 0 , 'C');
		$pdf->SetXY($x+481, $y+320+(15*$i));
		$pdf->MultiCell(45, 15, $result['NOM_CX_MONEDA'] , 0 , 'C');
		////*****FIN CUADRO*****/////


		//***TABLA DE ITEMS***//
		//$this->header_items($pdf, $margen + 3 + 245 + 15);
		$this->header_items($pdf, $margen + 200);
		//$pdf->SetFont('Arial','',9);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT ice.COD_CX_ITEM_OC_EXTRANJERA
						,ice.ITEM
						,ice.COD_EQUIPO_OC_EX
						,ice.COD_CX_OC_EXTRANJERA
						,ice.DESC_EQUIPO_OC_EX 
						,ice.CANTIDAD
						,ice.PRECIO
						,ice.COD_PRODUCTO
						,ice.CANTIDAD * ice.PRECIO TOTAL
						,c.MONTO_FLETE_INTERNO
						,c.MONTO_EMBALAJE
						,c.MONTO_DESCUENTO
						,c.SUBTOTAL
						,c.MONTO_TOTAL
			FROM CX_ITEM_OC_EXTRANJERA ice,CX_OC_EXTRANJERA c
				WHERE ice.COD_CX_OC_EXTRANJERA=c.COD_CX_OC_EXTRANJERA 
					AND ice.COD_CX_OC_EXTRANJERA=$cod_cx_oc_extranjera";
		$result2 = $db->build_results($sql);
		
		$i = 1;
		$y = $pdf->GetY()-24;
		$y = $y + 24;
		
		//// INI DESPLIEGE DE ITEMS
		foreach($result2 as $row){
			$pdf->SetFont('Arial','',8);
			
			$pdf->SetXY($x+199, $y);
			$y_antes = $pdf->GetY();
			$pdf->MultiCell(249, 12,$row['DESC_EQUIPO_OC_EX'], 1 , 'L');
			$y_despues = $pdf->GetY();
			$pdf->SetXY($x+32, $y);
			$pdf->Cell(27, $y_despues - $y_antes,$row['ITEM'] , 1 ,1, 'L');
			$pdf->SetXY($x+59,	$y);
			$pdf->Cell(60,  $y_despues - $y_antes,$row['COD_PRODUCTO'], 1 , 1, 'L');
			$pdf->SetXY($x+119, $y);
			$pdf->Cell(80,  $y_despues - $y_antes,$row['COD_EQUIPO_OC_EX'], 1 , 1, 'L');
			$pdf->SetXY($x+448, $y);
			$pdf->Cell(33,  $y_despues - $y_antes,number_format($row['CANTIDAD'],0), 1 , 1, 'R');
			$pdf->SetXY($x+481, $y);
			
			//$pdf->Cell(45,  $y_despues - $y_antes,$row['PRECIO'], 1 , 1, 'R');
			$pdf->Cell(45,  $y_despues - $y_antes,number_format($row['PRECIO'],2, ',', '.'), 1 , 1, 'R');
			
			$pdf->SetXY($x+526, $y);
			$pdf->Cell(45,  $y_despues - $y_antes,number_format($row['TOTAL'],2, ',', '.'), 1 , 1, 'R');
			$i++;

			$y += $y_despues - $y_antes;
			$ypos = $pdf->GetY();
			if ($ypos >= 600) {
				$pdf->AddPage();   
				//$this->header_items($pdf, -28);
				$y = $pdf->GetY()+84;		
				$i = 1;
			}	
		}
		//// FIN  DESPLIEGE DE ITEMS

		$i = 1;		
		$x = 8;

		//// INI TOTALES	
		$ypos = $pdf->GetY(); 		
		$pdf->SetFont('Arial','B',11);
		$pdf->SetXY($x+375, $ypos+15);
		$pdf->Cell(47, 15, 'Sub Total' , 0 , 0, 'R');
		$pdf->SetXY($x+395, $ypos+15);
		$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
		$pdf->SetXY($x+462, $ypos+15);
		$pdf->MultiCell(102, 15,number_format($row['SUBTOTAL'],2, ',', '.'), 1, 'R');
		$pdf->SetXY($x+375, $ypos+33);
		$pdf->Cell(47, 15, 'Inland Freight' , 0 , 0, 'R');
		$pdf->SetXY($x+395, $ypos+33);
		$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
		$pdf->SetXY($x+462, $ypos+33);
		$pdf->MultiCell(102, 15,number_format($result['MONTO_FLETE_INTERNO'],2, ',', '.'), 1, 'R');
		$pdf->SetXY($x+375, $ypos+51);
		$pdf->Cell(47, 15, 'Packing' , 0 , 0, 'R');
		$pdf->SetXY($x+395, $ypos+51);
		$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
		$pdf->SetXY($x+462, $ypos+51);
		$pdf->MultiCell(102, 15,number_format($result['MONTO_EMBALAJE'],2, ',', '.'), 1, 'R');
		$pdf->SetXY($x+375, $ypos+69);
		$pdf->Cell(47, 15, 'Discount '.$result['PORCENTAJE_PO'].'%', 0 , 0, 'R');
		$pdf->SetXY($x+395, $ypos+69);
		$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
		$pdf->SetXY($x+462, $ypos+69);
		$pdf->MultiCell(102, 15,number_format($result['MONTO_DESCUENTO'],2, ',', '.'), 1, 'R');
		$pdf->SetXY($x+315, $ypos+87);
		$pdf->Cell(47, 15, 'TOTAL' , 0 , 0, 'R');
		$pdf->SetXY($x+360, $ypos+87);
		$pdf->MultiCell(60, 15,$result['NOM_CX_CLAUSULA_COMPRA'], 0, 'R');	
		$pdf->SetXY($x+419, $ypos+87);
		$pdf->MultiCell(40, 15,$result['NOM_CX_MONEDA'], 0, 'R');
		$pdf->SetXY($x+462, $ypos+87);
		$pdf->MultiCell(102, 15,number_format(	$result['MONTO_TOTAL'],	2, ',', '.'), 1,'R');
		//// FIN TOTALES
			
		$ypos = $pdf->GetY();
		if($pdf->GetY() >= 610){
			$pdf->AddPage();
			$ypos = 100;		
		}

		//// INI COMMENTS
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+10, $ypos-10);
		$pdf->Cell(80, 11, 'COMMENTS' , 0 , 0, 'C');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+23, $ypos+3);
		$pdf->MultiCell(540, 13,$result['OBSERVACIONES'], 1, 'L');
		//// FIN COMMENTS
			
		////////INI FIRMA////////
		$pdf->Line(435,$ypos+80,570,$ypos+80);
		$pdf->SetXY($x+413, $ypos+83);
		$pdf->MultiCell(150, 15,$result['NOM_USUARIO'], 0, 'R');
		$pdf->SetFont('Arial','B',9);
		$pdf->SetXY($x+413, $ypos+95);
		$pdf->MultiCell(150, 15,'COMERCIAL TODOINOX LTDA.', 0, 'R');
		////////FIN FIRMA////////
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
		
		require_once(dirname(__FILE__)."/../cx_cot_extranjera/class_PDF2.php");
		$pdf = PDF2::makePDF(array($p), array($this->labels), array($rdata), $this->con_logo,$this->orientation,$this->unit,$this->format);		
		
		$pdf->SetTitle($this->titulo);
		$this->modifica_pdf($pdf);
		$pdf->Output($this->titulo, 'I');
	}
}	
?>