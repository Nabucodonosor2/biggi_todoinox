<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../cx_cot_extranjera/class_PDF2.php");

class informe_oc_extranjera extends reporte {	
	function informe_oc_extranjera($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
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
		$pdf->SetXY($x+344, 110);
		$pdf->Cell(100, 15, $result['COD_CX_COT_EXTRANJERA'] , 0 , 0, 'L');
		$pdf->Line(347,98,430,98);
		$pdf->SetXY($x+430, 85);
		$pdf->Cell(47, 15, 'DATE' , 0 , 0, 'R');
		$pdf->SetXY($x+480, 85);
		$pdf->Cell(100, 15, $result['FECHA_CX_OC_EXTRANJERA'] , 0 , 0, 'L');
		$pdf->Line(483,98,570,98);
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+34, 105);
		$pdf->Cell(47, 15, 'COMPANY' , 0 , 0, 'R');
		$pdf->SetXY($x+118, 105);
		$pdf->Cell(47, 15, $result['NOM_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(120,118,570,118);
		$pdf->SetXY($x+31, 128);
		$pdf->Cell(47, 15, 'ADDRESS' , 0 , 0, 'R');
		$pdf->SetXY($x+118, 128);
		$pdf->MultiCell(147, 15, $result['DIRECCION'] , 0 , 'L');
		$pdf->SetXY($x+350, 128);
		$pdf->Cell(47, 15, 'PHONE' , 0 , 0, 'R');
		$pdf->SetXY($x+420, 128);
		$pdf->MultiCell(147, 15, $result['TELEFONO_PROVEEDOR'] , 0 , 'L');
		$pdf->Line(423,142,570,142);
		$pdf->SetXY($x+9, 168);
		$pdf->Cell(47, 15, 'CITY' , 0 , 0, 'R');
		$pdf->SetXY($x+118, 168);
		$pdf->MultiCell(147, 15, $result['NOM_CIUDAD_4D'] , 0 , 'L');
		$pdf->Line(120,182,340,182);
		$pdf->SetXY($x+336, 148);
		$pdf->Cell(47, 15, 'FAX' , 0 , 0, 'R');
		$pdf->SetXY($x+420, 148);
		$pdf->MultiCell(147, 15, $result['FAX_PROVEEDOR'] , 0 , 'L');
		$pdf->Line(423,162,570,162);
		$pdf->SetXY($x+362, 168);
		$pdf->Cell(47, 15, 'COUNTRY' , 0 , 0, 'R');
		$pdf->SetXY($x+420, 168);
		$pdf->MultiCell(147, 15, $result['NOM_PAIS_4D'] , 0 , 'L');
		$pdf->Line(423,182,570,182);
		////FIN ///
		
		////DATOS CONTACTO///
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',13);
		$pdf->SetXY($x+55, 196);
		$pdf->Cell(47, 15, 'CONTACT ' , 0 , 0, 'R');
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x+21, 215);
		$pdf->Cell(47, 15, 'PHONE' , 0 , 0, 'R');
		$pdf->SetXY($x+130, 215);
		$pdf->Cell(120, 15, $result['TELEFONO'] , 0 , 0, 'L');
		/*$pdf->Line(120,164,570,164);*/
		$pdf->SetXY($x+360, 215);
		$pdf->Cell(47, 15, 'E-MAIL' , 0 , 0, 'R');
		$pdf->SetXY($x+420, 215);
		$pdf->MultiCell(200, 15, $result['MAIL'] , 0 , 'L');
		$pdf->SetXY($x+72, 229);
		$pdf->Cell(47, 15, 'PORT OF LOADING' , 0 , 0, 'R');
		$pdf->SetXY($x+130, 229);
		$pdf->MultiCell(147, 15,$result['NOM_CX_PUERTO_SALIDA'] , 0 , 'L');
		$pdf->Line(130,243,290,243);
		$pdf->SetXY($x+360, 229);
		$pdf->Cell(47, 15, 'PORT OF DISCHARGE' , 0 , 0, 'R');
		$pdf->SetXY($x+420, 229);
		$pdf->MultiCell(147, 15,$result['NOM_CX_PUERTO_ARRIBO'] , 0 , 'L');
		$pdf->Line(420,243,570,243);
		$pdf->SetXY($x+77, 249);
		$pdf->Cell(47, 15, 'PURCHASE CLAUSE' , 0 , 0, 'R');
		$pdf->SetXY($x+130, 249);
		$pdf->MultiCell(147, 15,$result['NOM_CX_CLAUSULA_COMPRA'], 0 , 'L');
		$pdf->Line(130,263,290,263);
		$pdf->SetXY($x+360, 249);
		$pdf->Cell(47, 15,'DELIVERY DATE', 0 , 0, 'R');
		$pdf->SetXY($x+419, 249);
		$pdf->MultiCell(147, 15, $result['DELIVERY_DATE'] , 0 , 'L');
		$pdf->Line(420,263,570,263);
		$pdf->SetXY($x+49, 269);
		$pdf->Cell(47, 15, 'T. PAYMENTS' , 0 , 0, 'R');
		$pdf->SetXY($x+130, 269);
		$pdf->MultiCell(147, 15,$result['NOM_CX_TERMINO_PAGO'], 0 , 'L');
		$pdf->Line(130,312,340,312);
		$pdf->SetXY($x+360, 269);
		$pdf->Cell(47, 15,'CURRENCY', 0 , 0, 'R');
		$pdf->SetXY($x+420, 269);
		$pdf->MultiCell(147, 15,$result['NOM_CX_MONEDA'] , 0 , 'L');
		$pdf->Line(420,283,570,283);
		$pdf->SetXY($x+44, 322);
		$pdf->Cell(47, 15, 'REFERENCE' , 0 , 0, 'R');
		$pdf->SetXY($x+130, 322);
		$pdf->MultiCell(650, 15,$result['REFERENCIA'], 0 , 'L');
		$pdf->Line(130,336,570,336);
		////FIN///
		////*****CUADRO*****/////
		$pdf->SetFont('Arial','',7);
		$pdf->Line(481,365,571,365);
		$pdf->Line(481,365,481,398);
		$pdf->Line(571,365,571,398);
		//$pdf->Line(430,398,571,398);
		$pdf->SetXY($x+476, $y+385+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CX_CLAUSULA_COMPRA'] , 0 , 'C');
		$pdf->SetXY($x+430, $y+385+(15*$i));
		$pdf->MultiCell(147, 15, $result['NOM_CX_MONEDA'] , 0 , 'C');
		$pdf->SetXY($x+464, $y+385+(15*$i));
		//$pdf->MultiCell(147, 15, $result['NOM_CX_PUERTO_SALIDA'] , 0 , 'C');
		////***FIN CUADRO****///


		//***TABLA DE ITEMS***//
		$this->header_items($pdf, $margen + 3 + 245 + 15);
		//$pdf->SetFont('Arial','',9);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT ice.COD_CX_ITEM_OC_EXTRANJERA
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
		foreach($result2 as $row){
			$pdf->SetFont('Arial','',8);
			
			$pdf->SetXY($x+199, $y);
			$y_antes = $pdf->GetY();
			$pdf->MultiCell(249, 12,$row['DESC_EQUIPO_OC_EX'], 1 , 'L');
			$y_despues = $pdf->GetY();
			$pdf->SetXY($x+32, $y);
			$pdf->Cell(27, $y_despues - $y_antes,$row['COD_CX_ITEM_OC_EXTRANJERA'] , 1 ,1, 'L');
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

			$i = 1;		
			$x = 8;
			$ypos = $pdf->GetY()+50; 		
			$pdf->SetFont('Arial','B',11);
			$pdf->SetXY($x+375, $ypos+20);
			$pdf->Cell(47, 15, 'Sub Total' , 0 , 0, 'R');
			$pdf->SetXY($x+395, $ypos+20);
			$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
			$pdf->SetXY($x+462, $ypos+20);
			$pdf->MultiCell(102, 15,number_format($row['SUBTOTAL'],2, ',', '.'), 1, 'R');
			$pdf->SetXY($x+375, $ypos+38);
			$pdf->Cell(47, 15, 'Inland Freight' , 0 , 0, 'R');
			$pdf->SetXY($x+395, $ypos+38);
			$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
			$pdf->SetXY($x+462, $ypos+38);
			$pdf->MultiCell(102, 15,number_format($result['MONTO_FLETE_INTERNO'],2, ',', '.'), 1, 'R');
			$pdf->SetXY($x+375, $ypos+56);
			$pdf->Cell(47, 15, 'Packing' , 0 , 0, 'R');
			$pdf->SetXY($x+395, $ypos+56);
			$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
			$pdf->SetXY($x+462, $ypos+56);
			$pdf->MultiCell(102, 15,number_format($result['MONTO_EMBALAJE'],2, ',', '.'), 1, 'R');
			$pdf->SetXY($x+375, $ypos+74);
			$pdf->Cell(47, 15, 'Discount' , 0 , 0, 'R');
			$pdf->SetXY($x+395, $ypos+74);
			$pdf->MultiCell(100, 15,$result['NOM_CX_MONEDA'], 0, 'C');
			$pdf->SetXY($x+462, $ypos+74);
			$pdf->MultiCell(102, 15,number_format($result['MONTO_DESCUENTO'],2, ',', '.'), 1, 'R');
			$pdf->SetXY($x+315, $ypos+92);
			$pdf->Cell(47, 15, 'TOTAL' , 0 , 0, 'R');
			$pdf->SetXY($x+360, $ypos+92);
			$pdf->MultiCell(60, 15,$result['NOM_CX_CLAUSULA_COMPRA'], 0, 'R');
			//$pdf->SetXY($x+315, $ypos+92);
			//$pdf->MultiCell(240, 15,$result['NOM_CX_PUERTO_SALIDA'], 0, 'L');
			$pdf->SetXY($x+419, $ypos+92);
			$pdf->MultiCell(40, 15,$result['NOM_CX_MONEDA'], 0, 'R');
			$pdf->SetXY($x+462, $ypos+92);
			$pdf->MultiCell(102, 15,number_format(	$result['MONTO_TOTAL'],	2, ',', '.'), 1,'R');
			 						//number_format( 	float $number 		  , int $decimals = 0 , string $dec_point = "." , string $thousands_sep = "," ) : string
			/////FIN TOTAL////
			////COMMENTS////
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY($x+10, $ypos+122);
			$pdf->Cell(80, 11, 'COMMENTS' , 0 , 0, 'C');
			$pdf->SetFont('Arial','',9);
			$pdf->SetXY($x+23, $ypos+140);
			$pdf->MultiCell(540, 45,$result['OBSERVACIONES'], 1, 'J');
			/////firma///
			$pdf->Line(350,$ypos+210,571,$ypos+210);
			$pdf->SetXY($x+335, $ypos+212);
			$pdf->MultiCell(240, 15,$result['NOM_USUARIO'], 0, 'C');
			$pdf->SetFont('Arial','B',9);
			$pdf->SetXY($x+335, $ypos+226);
			$pdf->MultiCell(240, 15,'COMERCIAL TODOINOX LTDA.', 0, 'C');
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

		$pdf = PDF2::makePDF(array($p), array($this->labels), array($rdata), $this->con_logo,$this->orientation,$this->unit,$this->format);		
		
		$pdf->SetTitle($this->titulo);
		$this->modifica_pdf($pdf);
		$pdf->Output($this->titulo, 'I');
	}
}	
?>