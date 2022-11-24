<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

class carta_compra_usd extends reporte {	
	function carta_compra_usd($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	
	function dibuja_pdf(&$pdf, $result){
		
		$margen= 0;
		$pdf->SetTextColor(0,0,10);//TEXTOS azul
		$pdf->SetFont('Arial','',14);
		$pdf->SetXY(420, 100+(15*$i));
		$pdf->Cell(47, 15, 'Santiago,'.$result[0]['DIA'].' de '.$result[0]['MES'].' '.$result[0]['ANO'] , 0 , 0, 'C');
		$pdf->SetXY(335, 100+(15*$i));
		
		$pdf->SetFont('Arial','',14);
		$pdf->SetXY(60, 165+(15*$i));
		$pdf->Cell(47, 15, 'Señores' , 0 , 0, 'L');
		$pdf->SetXY(60, 180+(15*$i));
		$pdf->Cell(47, 15, 'ITAU' , 0 , 0, 'L');
		$pdf->SetFont('Arial','U',14);
		$pdf->SetXY(60, 193+(15*$i));
		$pdf->Cell(47, 15, 'PRESENTE' , 0 , 0, 'L');
		
		$pdf->SetFont('Arial','',14);
		$pdf->SetXY(60, 237+(15*$i));
		$pdf->Cell(47, 15, 'At.: '.$result[0]['ATENCION'] , 0 , 0, 'L');
		$pdf->SetXY(60, 253+(15*$i));
		$pdf->Cell(47, 15, 'Ref.: '.$result[0]['REFERENCIA'] , 0 , 0, 'L');
		$pdf->SetXY(60, 293+(15*$i));
		$pdf->MultiCell(480, 15, 'Autorizo debitar de nuestra cuenta corriente CLP N° 211 343 251, el total de' , 0 ,'J');
		$pdf->SetXY(60, 310+(15*$i));
		$pdf->MultiCell(470, 15, '$'.number_format($result[0]['TOTAL_DEBITO_PESOS'],0,',','.').'.- equivalente a la compra de US$'.number_format($result[0]['CANT_COMPRA_USD'],0,',','.').'.- (T/C $'.number_format($result[0]['TIPO_CAMBIO_USD'],2,',','.').'); acordado con la mesa de dinero de ITAU.' , 0 ,'J');
		//$pdf->SetXY(159, 357+(15*$i));
		//$pdf->Cell(47, 15, 'con la mesa de dinero de CORPBANCA.' , 0 , 0, 'C');
		$pdf->SetXY(60, 370+(15*$i));
		$pdf->Cell(47, 15, 'Favor abonar estos US$'.number_format($result[0]['CANT_COMPRA_USD'],0,',','.').'.- a la cuenta corriente USD N° 1200-2122-57' , 0 , 0, 'J');
		$pdf->SetXY(60, 387+(15*$i));
		$pdf->Cell(47, 15, 'de Comercial Todoinox Limitada.' , 0 , 0, 'J');
		$pdf->SetXY(60, 425+(15*$i));
		$pdf->Cell(47, 15, 'Estos dólares serán utilizados en futuras coberturas de Comercio Exterior' , 0 , 0, 'J');
		$pdf->SetXY(60, 442+(15*$i));
		$pdf->Cell(47, 15, '(Anexo N° 20090).' , 0 , 0, 'J');
		$pdf->SetXY(60, 510+(15*$i));
		$pdf->Cell(47, 15, 'Atentamente, ' , 0 , 0, 'L');
		
		$pdf->SetXY(60, 600+(15*$i));
		$pdf->Cell(47, 15, '___________________________' , 0 , 0, 'L');
		$pdf->SetXY(60, 620+(15*$i));
		$pdf->Cell(47, 15, 'COMERCIAL TODOINOX LTDA.' , 0 , 0, 'L');
		
	}
	function modifica_pdf(&$pdf) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		$this->dibuja_pdf($pdf, $result);

	}	
}

class wi_carta_compra_usd extends w_input {
	function wi_carta_compra_usd($cod_item_menu) {
		parent::w_input('carta_compra_usd', $cod_item_menu);

		$sql = "SELECT CC.COD_CX_CARTA_COMPRA_USD
					,EC.COD_ESTADO_CARTA_COMPRA
					,CC.ATENCION
					,CC.TIPO_CAMBIO_USD
					,CC.CANT_COMPRA_USD
					,CC.TOTAL_DEBITO_PESOS
					,CC.REFERENCIA
					,CONVERT(VARCHAR,CC.FECHA_CX_CARTA_COMPRA_USD,103) FECHA_CX_CARTA_COMPRA_USD
					,'' DISPLAY_CODIGO
					,'' DISPLAY_WI_PRINT
					,U.COD_USUARIO
					,U.NOM_USUARIO
					FROM CX_CARTA_COMPRA_USD CC, ESTADO_CARTA_USD EC, USUARIO U
					WHERE CC.COD_ESTADO_CARTA_COMPRA = EC.COD_ESTADO_CARTA_COMPRA
					AND CC.COD_USUARIO = U.COD_USUARIO
					AND CC.COD_CX_CARTA_COMPRA_USD = {KEY1}";
						
		$this->dws['wi_carta_compra_usd'] = new datawindow($sql);
		

		$this->dws['wi_carta_compra_usd']->add_control(new static_text('COD_CX_CARTA_COMPRA_USD'));
		$sql_origen= "SELECT COD_ESTADO_CARTA_COMPRA,NOM_ESTADO_CARTA_COMPRA FROM ESTADO_CARTA_USD";
		
		$this->dws['wi_carta_compra_usd']->add_control(new drop_down_dw('COD_ESTADO_CARTA_COMPRA',$sql_origen,150,false,false));
		$this->dws['wi_carta_compra_usd']->add_control(new edit_text_upper('ATENCION', 80, 80));
		
		$this->dws['wi_carta_compra_usd']->add_control(new edit_num('TIPO_CAMBIO_USD', 30, 80, 4));
		$this->dws['wi_carta_compra_usd']->add_control($control = new edit_num('CANT_COMPRA_USD', 30, 80));
		$control->set_onChange("calcula_total()");
		$this->dws['wi_carta_compra_usd']->add_control($control = new edit_num('TOTAL_DEBITO_PESOS', 30, 80));	
		$control->set_readonly(true);
		$this->dws['wi_carta_compra_usd']->add_control(new edit_text('COD_USUARIO', 80, 80,'hidden'));
		$this->dws['wi_carta_compra_usd']->add_control(new edit_text('NOM_USUARIO', 80, 80,'hidden'));
		
		$this->dws['wi_carta_compra_usd']->add_control(new edit_text_upper('REFERENCIA', 80, 80));
		$this->dws['wi_carta_compra_usd']->add_control(new edit_date('FECHA_CX_CARTA_COMPRA_USD', 21, 80));
		
		      
	      $this->dws['wi_carta_compra_usd']->set_mandatory('NOM_ESTADO_CARTA_COMPRA', 'Estado');
	      $this->dws['wi_carta_compra_usd']->set_mandatory('ATENCION', 'Atencion');
	      $this->dws['wi_carta_compra_usd']->set_mandatory('TIPO_CAMBIO_USD', 'Valor dolar CLP');
	      $this->dws['wi_carta_compra_usd']->set_mandatory('CANT_COMPRA_USD', 'Compra USD');
	      $this->dws['wi_carta_compra_usd']->set_mandatory('TOTAL_DEBITO_PESOS', 'Total CLP');
	      $this->dws['wi_carta_compra_usd']->set_mandatory('REFERENCIA', 'Referencia');
	      $this->dws['wi_carta_compra_usd']->set_mandatory('FECHA_CX_CARTA_COMPRA_USD', 'Fecha');
	     
			
	}
	function new_record() {
		//$this->dws['wi_proveedor_ext']->add_control(new edit_num('COD_PROVEEDOR_EXT',3,3));
		$this->dws['wi_carta_compra_usd']->insert_row();
		$this->dws['wi_carta_compra_usd']->set_item(0, 'DISPLAY_CODIGO','none');
		$this->dws['wi_carta_compra_usd']->set_item(0, 'DISPLAY_WI_PRINT','none');
		
		$this->dws['wi_carta_compra_usd']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['wi_carta_compra_usd']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
	}
	function load_record() {
		$cod_carta_compra_usd = $this->get_item_wo($this->current_record, 'COD_CX_CARTA_COMPRA_USD');
		$this->dws['wi_carta_compra_usd']->retrieve($cod_carta_compra_usd);
	}
	function get_key() {
		return $this->dws['wi_carta_compra_usd']->get_item(0, 'COD_CX_CARTA_COMPRA_USD');
	}
	
	function print_record(){
		//////////////Temporal//////////////
		$sql = "SELECT CC.COD_CX_CARTA_COMPRA_USD
					,EC.COD_ESTADO_CARTA_COMPRA
					,CC.ATENCION
					,CC.TIPO_CAMBIO_USD
					,CC.CANT_COMPRA_USD
					,CC.TOTAL_DEBITO_PESOS
					,CC.REFERENCIA
					,CONVERT(VARCHAR,CC.FECHA_CX_CARTA_COMPRA_USD,103) FECHA_CX_CARTA_COMPRA_USD
					,DATENAME(DAY,FECHA_CX_CARTA_COMPRA_USD) DIA
					,dbo.f_get_nom_mes(MONTH(FECHA_CX_CARTA_COMPRA_USD)) MES
					,DATENAME(YEAR,FECHA_CX_CARTA_COMPRA_USD) ANO
					,'' DISPLAY_CODIGO
					,'' DISPLAY_WI_PRINT
					,U.COD_USUARIO
					,U.NOM_USUARIO
					FROM CX_CARTA_COMPRA_USD CC, ESTADO_CARTA_USD EC, USUARIO U
					WHERE CC.COD_ESTADO_CARTA_COMPRA = EC.COD_ESTADO_CARTA_COMPRA
					AND CC.COD_USUARIO = U.COD_USUARIO
					AND CC.COD_CX_CARTA_COMPRA_USD =".$this->get_key();
		  
		////////////////////////////////////
		$file_name = $this->find_file('carta_compra_usd', 'carta_compra_usd.xml');
		$rpt = new carta_compra_usd($sql, $file_name, $labels, "Carta Compra USD", 1);												
		$this->_load_record();
	}
	
	function save_record($db) {
		$COD_CX_CARTA_COMPRA_USD 		= $this->dws['wi_carta_compra_usd']->get_item(0, 'COD_CX_CARTA_COMPRA_USD');
		$COD_USUARIO			 		= $this->dws['wi_carta_compra_usd']->get_item(0, 'COD_ESTADO_CARTA_COMPRA');
		$COD_ESTADO_CARTA_COMPRA		= $this->dws['wi_carta_compra_usd']->get_item(0, 'COD_ESTADO_CARTA_COMPRA');
		$ATENCION 						= $this->dws['wi_carta_compra_usd']->get_item(0, 'ATENCION');
		$REFERENCIA 					= $this->dws['wi_carta_compra_usd']->get_item(0, 'REFERENCIA');
		$TIPO_CAMBIO_USD				= $this->dws['wi_carta_compra_usd']->get_item(0, 'TIPO_CAMBIO_USD');
		$CANT_COMPRA_USD				= $this->dws['wi_carta_compra_usd']->get_item(0, 'CANT_COMPRA_USD');
		$TOTAL_DEBITO_PESOS 			= $this->dws['wi_carta_compra_usd']->get_item(0, 'TOTAL_DEBITO_PESOS');
		$FECHA_CX_CARTA_COMPRA_USD 		= $this->dws['wi_carta_compra_usd']->get_item(0, 'FECHA_CX_CARTA_COMPRA_USD');
		
		$COD_CX_CARTA_COMPRA_USD = ($COD_CX_CARTA_COMPRA_USD=='') ? "null" : $COD_CX_CARTA_COMPRA_USD;
		$FECHA_CX_CARTA_COMPRA_USD = $this->str2date($FECHA_CX_CARTA_COMPRA_USD);
		
		$sp = 'spu_carta_compra_usd';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
				
	    $param	= "'$operacion'
	    			,$COD_CX_CARTA_COMPRA_USD
	    			,$COD_USUARIO
	    			,$COD_ESTADO_CARTA_COMPRA
	    			,'$ATENCION'
	    			,'$REFERENCIA'
	    			,$TIPO_CAMBIO_USD
	    			,$CANT_COMPRA_USD
	    			,$TOTAL_DEBITO_PESOS
	    			,$FECHA_CX_CARTA_COMPRA_USD";
	    			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				
				$cod_carta_compra_usd = $db->GET_IDENTITY();
				$this->dws['wi_carta_compra_usd']->set_item(0, 'COD_CX_CARTA_COMPRA_USD', $cod_carta_compra_usd);				
			}
			return true;
		}
		return false;
	}
}
?>