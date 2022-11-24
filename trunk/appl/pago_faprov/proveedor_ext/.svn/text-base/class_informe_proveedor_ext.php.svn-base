<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
class informe_proveedor_ext extends reporte {	
	function informe_proveedor_ext($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	
	function dibuja_uno(&$pdf, $result){
		////TITULO///
		$margen= 0;
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(295, 100+(15*$i));
		$pdf->Cell(47, 15, 'FICHA PROVEEDOR EXTRANJERO' , 0 , 0, 'C');
		$pdf->SetXY(335, 100+(15*$i));
		$pdf->SetFont('Arial','B',10);
		////FIN TITULO///
		
		////DATOS PROVEEDOR///
		$pdf->SetTextColor(0,0,10);
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY(30, 130+(15*$i));
		$pdf->Cell(47, 15, 'ALIAS ' , 0 , 0, 'L');
		$pdf->SetXY(100, 130+(15*$i));
		$pdf->Cell(47, 15, $result['ALIAS_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(100,145,230,145);
		$pdf->SetXY(355, 130+(15*$i));
		$pdf->Cell(47, 15, 'CODIGO' , 0 , 0, 'R');
		$pdf->SetXY(525, 130+(15*$i));
		$pdf->Cell(47, 15, $result['COD_PROVEEDOR_EXT'] , 0 , 0, 'R');
		$pdf->Line(483,145,570,145);
		
		$pdf->SetXY(28, 150+(15*$i));
		$pdf->Cell(47, 15, 'NOMBRE' , 0 , 0, 'R');
		$pdf->SetXY(100, 150+(15*$i));
		$pdf->Cell(47, 15, $result['NOM_PROVEEDOR_EXT'] , 0 , 0, 'L');
		$pdf->Line(100,164,570,164);
		$pdf->SetXY(36, 170+(15*$i));
		$pdf->Cell(47, 15, 'TELEFONO' , 0 , 0, 'R');
		$pdf->Line(100,184,340,184);
		$pdf->SetXY(100, 170+(15*$i));
		$pdf->Cell(147, 15, $result['TELEFONO'] , 0 , 'L');
		$pdf->SetXY(335, 170+(15*$i));
		$pdf->Cell(47, 15, 'FAX' , 0 , 0, 'R');
		$pdf->SetXY(420, 170+(15*$i));
		$pdf->Cell(147, 15, $result['FAX'] , 0 , 'L');
		$pdf->Line(423,184,570,184);
		$pdf->SetXY(31, 190+(15*$i));
		$pdf->Cell(47, 15, 'WEB SITE' , 0 , 0, 'R');
		$pdf->SetXY(100, 190+(15*$i));
		$pdf->Cell(147, 15, $result['WEB_SITE'] , 0 , 'L');
		$pdf->Line(100,204,340,204);
		$pdf->SetXY(38, 210+(15*$i));
		$pdf->Cell(47, 15, 'DIRECCION' , 0 , 0, 'R');
		$pdf->Line(100,224,570,224);
		$pdf->SetXY(100, 210+(15*$i));
		$pdf->Cell(147, 15, $result['DIRECCION'] , 0 , 'L');
		$pdf->SetXY(30, 230+(15*$i));
		$pdf->Cell(39, 15, 'CIUDAD' , 0 , 0, 'R');
		$pdf->Line(100,244,570,244);
		$pdf->SetXY(100, 230+(15*$i));
		$pdf->Cell(147, 15, $result['NOM_CIUDAD_4D'] , 0 , 'L');
		$pdf->SetXY(38, 250+(15*$i));
		$pdf->Cell(17, 15, 'PAIS' , 0 , 0, 'R');
		$pdf->Line(100,264,570,264);
		$pdf->SetXY(100, 250+(15*$i));
		$pdf->Cell(147, 15, $result['NOM_PAIS_4D'] , 0 , 'L');
		$pdf->SetXY(38, 270+(15*$i));
		$pdf->Cell(56, 15, 'POST OFFICE' , 0 , 0, 'R');
		$pdf->Line(100,284,570,284);
		$pdf->SetXY(100, 270+(15*$i));
		$pdf->Cell(147, 15, $result['POST_OFFICE_BOX'] , 0 , 'L');
		
		$pdf->SetFont('Arial','B',14);
		$pdf->SetXY(295, 300+(15*$i));
		$pdf->Cell(47, 15, 'CONTACTOS PROVEEDOR' , 0 , 0, 'C');
		$pdf->Line(225,316,410,316);
		
		/////tabla////
		$pdf->Line(300,330,300,405);
		$pdf->Line(440,330,440,405);
		$pdf->Rect(30, 330, 540, 25);
		$pdf->Rect(30, 355, 540, 25);
		$pdf->Rect(30, 380, 540, 25);
		//////////////
		
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY(35, 336+(15*$i));
		$pdf->Cell(265, 15, "Nombre" , 0 , 'L');
		$pdf->Cell(140, 15, "E-Mail" , 0 , 'L');
		$pdf->Cell(100, 15, "Teléfono" , 0 , 'L');
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY(35, 361+(15*$i));
		$pdf->Cell(265, 15, "ALBERTO BRIGA" , 0 , 'L');
		$pdf->Cell(140, 15, "alberto.briga@comenda.e" , 0 , 'L');
		$pdf->Cell(100, 15, "39-02-95228 226" , 0 , 'L');
		$pdf->SetXY(440, 386+(15*$i));
		$pdf->Cell(100, 15, "39-02-952281" , 0 , 'L');
		
		$pdf->SetXY(28, 440+(15*$i));
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(100, 15, "BENEFICIARY ENTERPRISE:" , 0 , 'L');
		$pdf->SetXY(50, 460+(15*$i));
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(100, 15, "NAME          COMENDA ALI S.P.A." , 0 , 'L');
		$pdf->SetXY(50, 490+(15*$i));
		$pdf->Cell(100, 15, "ADRESS      VIA GALILEO GALILEI, 81-20060 CASSINA DE PECCHI - MILANO - ITALIA" , 0 , 'L');
		
		$pdf->SetXY(28, 530+(15*$i));
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(100, 15, "BENEFICIARY BANK:" , 0 , 'L');
		$pdf->SetXY(50, 550+(15*$i));
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(100, 15, "NAME          BANCA INTESA - SAN PAOLO - SPA" , 0 , 'L');
		$pdf->SetXY(50, 580+(15*$i));
		$pdf->Cell(100, 15, "ADRESS      VIA MILANO, 82 FILIALE DI PIOLTELLO - 20096 MILANO - ITALIA" , 0 , 'L');
		
		$pdf->SetXY(28, 610+(15*$i));
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(110, 15, "ACCOUNT NUMBER:" , 0 , 'L');
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(100, 15, " 1400166" , 0 , 'L');
		
		$pdf->SetXY(28, 630+(15*$i));
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(110, 15, "SWIFT CODE:" , 0 , 'L');
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(100, 15, " BCITIT33709" , 0 , 'L');
		
		$pdf->SetXY(28, 650+(15*$i));
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(110, 15, "IBAN:" , 0 , 'L');
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(100, 15, " IT43 A030 6933 5900 0000 1400 166" , 0 , 'L');
		
		$pdf->SetXY(28, 670+(15*$i));
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(110, 15, "ABI:" , 0 , 'L');
		$pdf->SetFont('Arial','',11);
		$pdf->Cell(100, 15, " 03069" , 0 , 'L');
		////FIN ///

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