<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../empresa/class_dw_help_empresa.php");

class dw_item_orden_despacho extends datawindow{
	function dw_item_orden_despacho(){	

		$sql = "SELECT COD_ITEM_ORDEN_DESPACHO
					  ,COD_ORDEN_DESPACHO
					  ,ORDEN
					  ,ITEM
					  ,COD_PRODUCTO
					  ,NOM_PRODUCTO
					  ,CANTIDAD
					  ,CANTIDAD_RECIBIDA
				FROM ITEM_ORDEN_DESPACHO
				WHERE COD_ORDEN_DESPACHO = {KEY1}";
							
		parent::datawindow($sql, 'ITEM_ORDEN_DESPACHO', true, true);
		
		$this->add_control(new static_num('CANTIDAD'));
		$this->add_control(new edit_num('CANTIDAD_RECIBIDA', 10, 10));
		
	}
	
	function update($db){
		$sp = 'spu_item_orden_despacho';
		
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
			
			$COD_ITEM_ORDEN_DESPACHO	= $this->get_item($i, 'COD_ITEM_ORDEN_DESPACHO');
			$COD_ORDEN_DESPACHO			= $this->get_item($i, 'COD_ORDEN_DESPACHO');			
			$ORDEN 						= $this->get_item($i, 'ORDEN');
			$ITEM 						= $this->get_item($i, 'ITEM');
			$COD_PRODUCTO 				= $this->get_item($i, 'COD_PRODUCTO');
			$NOM_PRODUCTO 				= $this->get_item($i, 'NOM_PRODUCTO');
			$CANTIDAD 					= $this->get_item($i, 'CANTIDAD');
			$CANTIDAD_RECIBIDA			= $this->get_item($i, 'CANTIDAD_RECIBIDA');			
			
			$COD_ITEM_ORDEN_DESPACHO	= ($COD_ITEM_ORDEN_DESPACHO=='') ? "null" : $COD_ITEM_ORDEN_DESPACHO;
			$CANTIDAD_RECIBID			= ($CANTIDAD_RECIBID=='') ? 0 : $CANTIDAD_RECIBID;
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
		
			$param = "'$operacion'
					 ,$COD_ITEM_ORDEN_DESPACHO
					 ,$COD_ORDEN_DESPACHO
					 ,$ORDEN
					 ,'$ITEM'
					 ,'$COD_PRODUCTO'
					 ,'$NOM_PRODUCTO'
					 ,$CANTIDAD
					 ,$CANTIDAD_RECIBIDA"; 
			
			if (!$db->EXECUTE_SP($sp, $param))
				return false;
			else {
				if ($statuts == K_ROW_NEW_MODIFIED) {
					$COD_ITEM_ORDEN_DESPACHO = $db->GET_IDENTITY();
					$this->set_item($i, 'COD_ITEM_ORDEN_DESPACHO', $COD_ITEM_ORDEN_DESPACHO);		
				}
			}
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$COD_ITEM_ORDEN_DESPACHO = $this->get_item($i, 'COD_ITEM_ORDEN_DESPACHO', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_ITEM_ORDEN_DESPACHO")){
				return false;				
			}			
		}
		return true;
	}
}

class dw_orden_despacho extends dw_help_empresa{
	function dw_orden_despacho(){	

		$sql = "SELECT COD_ORDEN_DESPACHO
					  ,OD.COD_USUARIO
					  ,U.NOM_USUARIO
				      ,CONVERT(VARCHAR, OD.FECHA_REGISTRO, 103) FECHA_REGISTRO
				      ,OD.COD_DOC_ORIGEN
				      ,OD.TIPO_DOC_ORIGEN
				      ,CONVERT(VARCHAR, OD.FECHA_ORDEN_DESPACHO, 103) FECHA_ORDEN_DESPACHO
				      ,CONVERT(VARCHAR, OD.FECHA_ORDEN_DESPACHO, 103) FECHA_ORDEN_DESPACHO_D
				      ,OD.REFERENCIA
				      ,OD.OBS
				      ,OD.COD_USUARIO_ANULA
				      ,CONVERT(VARCHAR, OD.FECHA_ANULA, 103) FECHA_ANULA
				      ,OD.MOTIVO_ANULA
				      ,OD.COD_EMPRESA
				      ,OD.RUT
				      ,OD.DIG_VERIF
				      ,OD.NOM_EMPRESA
				      ,OD.GIRO
				      ,E.ALIAS
				      ,OD.COD_USUARIO_IMPRESION
				      ,OD.COD_USUARIO_VENDEDOR1
				      ,OD.COD_USUARIO_VENDEDOR2
				      ,CASE
				      	WHEN OD.COD_ESTADO_ORDEN_DESPACHO = 4 THEN ''
				      	ELSE 'none'
				      END TR_DISPLAY
				      ,OD.COD_ESTADO_ORDEN_DESPACHO
				      ,NOM_ESTADO_ORDEN_DESPACHO
				      ,F.NRO_ORDEN_COMPRA
				      ,CONVERT(VARCHAR, FECHA_ORDEN_COMPRA_CLIENTE, 103) FECHA_ORDEN_COMPRA_CLIENTE
				      ,U1.NOM_USUARIO	NOM_USUARIO_ANULA
					  ,OD.NOM_SUCURSAL
					  ,OD.NOM_PERSONA
					  ,OD.DIRECCION
					  ,OD.TELEFONO
					  ,OD.FAX
					  ,OD.NOM_COMUNA
					  ,OD.NOM_CIUDAD
					  ,OD.COD_USUARIO_DESPACHA
				FROM ORDEN_DESPACHO OD LEFT OUTER JOIN FACTURA F ON OD.COD_DOC_ORIGEN = F.COD_FACTURA AND TIPO_DOC_ORIGEN = 'FACTURA'
									   LEFT OUTER JOIN USUARIO U1 ON OD.COD_USUARIO_ANULA = U1.COD_USUARIO
					,EMPRESA E
					,USUARIO U
					,ESTADO_ORDEN_DESPACHO EOD
				WHERE COD_ORDEN_DESPACHO = {KEY1}
				AND E.COD_EMPRESA = OD.COD_EMPRESA
				AND U.COD_USUARIO = OD.COD_USUARIO
				AND EOD.COD_ESTADO_ORDEN_DESPACHO = OD.COD_ESTADO_ORDEN_DESPACHO";
							
		parent::dw_help_empresa($sql);
		
		$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
					  ,NOM_ESTADO_ORDEN_DESPACHO
				FROM ESTADO_ORDEN_DESPACHO";
										
		$this->add_control($control = new drop_down_dw('COD_ESTADO_ORDEN_DESPACHO',$sql,145));
		$control->set_onChange('display_anula();');
		
		$sql = "SELECT COD_USUARIO COD_USUARIO_VENDEDOR1
					  ,NOM_USUARIO
				FROM USUARIO";
										
		$this->add_control(new drop_down_dw('COD_USUARIO_VENDEDOR1',$sql,145));
		$this->add_control(new edit_text_multiline('OBS',54,4));
		$this->add_control(new edit_text_upper('REFERENCIA',60,150));
		
		$this->add_control(new edit_text('MOTIVO_ANULA',90,150));
		$this->add_control(new static_text('FECHA_ANULA'));
		$this->add_control(new static_text('NOM_USUARIO_ANULA'));
		
		$sql = "SELECT COD_USUARIO COD_USUARIO_DESPACHA
					  ,NOM_USUARIO
				FROM USUARIO
				WHERE ES_DESPACHADOR = 'S'";
										
		$this->add_control(new drop_down_dw('COD_USUARIO_DESPACHA',$sql,145));
	}
}

class wi_orden_despacho extends w_input {
	function wi_orden_despacho($cod_item_menu){
		parent::w_input('orden_despacho', $cod_item_menu);
		
		$this->dws['dw_orden_despacho'] = new dw_orden_despacho();
		$this->dws['dw_item_orden_despacho'] = new dw_item_orden_despacho();
		
	}
	
	function new_record() {
		$this->dws['dw_orden_despacho']->insert_row();
	}
	
	function load_record() {
		$cod_orden_despacho = $this->get_item_wo($this->current_record, 'COD_ORDEN_DESPACHO');
		$this->dws['dw_orden_despacho']->retrieve($cod_orden_despacho);
		$this->dws['dw_item_orden_despacho']->retrieve($cod_orden_despacho);
		
		$cod_estado_orden_despacho = $this->dws['dw_orden_despacho']->get_item(0, 'COD_ESTADO_ORDEN_DESPACHO');
		
		$this->dws['dw_orden_despacho']->set_entrable('COD_USUARIO_VENDEDOR1', false);
		$this->dws['dw_orden_despacho']->set_entrable('REFERENCIA', false);
		$this->dws['dw_orden_despacho']->set_entrable('RUT', false);
		$this->dws['dw_orden_despacho']->set_entrable('ALIAS', false);
		$this->dws['dw_orden_despacho']->set_entrable('COD_EMPRESA', false);
		$this->dws['dw_orden_despacho']->set_entrable('NOM_EMPRESA', false);
		$this->dws['dw_item_orden_despacho']->set_entrable('CANTIDAD_RECIBIDA', false);
		
		$this->b_print_visible 	 = true;
		$this->b_no_save_visible = true;
		$this->b_save_visible 	 = true;
		$this->b_modify_visible	 = true;
		
		$cod_usu_anula = $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO_ANULA');
		if($cod_usu_anula == ''){
			$this->dws['dw_orden_despacho']->set_item(0, 'NOM_USUARIO_ANULA', $this->nom_usuario);
			$this->dws['dw_orden_despacho']->set_item(0, 'FECHA_ANULA', $this->current_date());
		}
		
		$priv = $this->get_privilegio_opcion_usuario('999005', $this->cod_usuario);
		
		unset($this->dws['dw_orden_despacho']->controls['COD_ESTADO_ORDEN_DESPACHO']);
		
		if($cod_estado_orden_despacho == 1){ //EMITIDA
			
			if($priv == 'N')
				$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
						  	  ,NOM_ESTADO_ORDEN_DESPACHO
						FROM ESTADO_ORDEN_DESPACHO
						WHERE COD_ESTADO_ORDEN_DESPACHO in (1, 2)";
			else
				$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
						  	  ,NOM_ESTADO_ORDEN_DESPACHO
						FROM ESTADO_ORDEN_DESPACHO
						WHERE COD_ESTADO_ORDEN_DESPACHO in (1, 2, 4)";	
										
			$this->dws['dw_orden_despacho']->add_control($control = new drop_down_dw('COD_ESTADO_ORDEN_DESPACHO',$sql,145));
			$control->set_onChange('display_anula();');

		}else if($cod_estado_orden_despacho == 2){ //PREPARANDO
			
			if($priv == 'N')
				$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
						  	  ,NOM_ESTADO_ORDEN_DESPACHO
						FROM ESTADO_ORDEN_DESPACHO
						WHERE COD_ESTADO_ORDEN_DESPACHO in (2, 3)";
			else
				$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
						  	  ,NOM_ESTADO_ORDEN_DESPACHO
						FROM ESTADO_ORDEN_DESPACHO
						WHERE COD_ESTADO_ORDEN_DESPACHO in (2, 3, 4)";	
										
			$this->dws['dw_orden_despacho']->add_control($control = new drop_down_dw('COD_ESTADO_ORDEN_DESPACHO',$sql,145));
			$control->set_onChange('display_anula();');
			$this->dws['dw_item_orden_despacho']->set_entrable('CANTIDAD_RECIBIDA', true);
			
		}else if($cod_estado_orden_despacho == 3){ //ENTREGADA
			
			if($priv == 'N')
				$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
						  	  ,NOM_ESTADO_ORDEN_DESPACHO
						FROM ESTADO_ORDEN_DESPACHO
						WHERE COD_ESTADO_ORDEN_DESPACHO = 3";
			else
				$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
						  	  ,NOM_ESTADO_ORDEN_DESPACHO
						FROM ESTADO_ORDEN_DESPACHO
						WHERE COD_ESTADO_ORDEN_DESPACHO in (3, 4)";
										
			$this->dws['dw_orden_despacho']->add_control($control = new drop_down_dw('COD_ESTADO_ORDEN_DESPACHO',$sql,145));
			$control->set_onChange('display_anula();');
			$this->dws['dw_orden_despacho']->set_entrable('OBS', false);
			
		}else if($cod_estado_orden_despacho == 4){ //ANULADA
			
			$sql = "SELECT COD_ESTADO_ORDEN_DESPACHO
					  	  ,NOM_ESTADO_ORDEN_DESPACHO
					FROM ESTADO_ORDEN_DESPACHO
					WHERE COD_ESTADO_ORDEN_DESPACHO = 4";
										
			$this->dws['dw_orden_despacho']->add_control(new drop_down_dw('COD_ESTADO_ORDEN_DESPACHO',$sql,145));
			
			$this->dws['dw_orden_despacho']->set_entrable('OBS', false);
			
			$this->b_print_visible 	 = false;
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible	 = false;
		}	
	}
	
	function get_key() {
		return $this->dws['dw_orden_despacho']->get_item(0, 'COD_ORDEN_DESPACHO');
	}
	
	function print_record() {
		$cod_orden_despacho = $this->get_key();
	    $nom_estado_orden_despacho = $this->dws['dw_orden_despacho']->get_item(0, 'NOM_ESTADO_ORDEN_DESPACHO');
	    
		$sql= "SELECT OD.COD_ORDEN_DESPACHO
					  ,OD.COD_USUARIO
					  ,U.NOM_USUARIO
				      ,CONVERT(VARCHAR, OD.FECHA_REGISTRO, 103) FECHA_REGISTRO
				      ,OD.COD_DOC_ORIGEN
				      ,OD.TIPO_DOC_ORIGEN
				      ,dbo.f_format_date(OD.FECHA_ORDEN_DESPACHO,3) FECHA_ORDEN_DESPACHO
				      ,OD.REFERENCIA
				      ,OD.OBS
				      ,OD.COD_USUARIO_ANULA
				      ,CONVERT(VARCHAR, OD.FECHA_ANULA, 103) FECHA_ANULA
				      ,OD.MOTIVO_ANULA
				      ,OD.COD_EMPRESA
				      ,OD.RUT
				      ,OD.DIG_VERIF
				      ,OD.NOM_EMPRESA
				      ,OD.GIRO
				      ,E.ALIAS
				      ,OD.COD_USUARIO_IMPRESION
				      ,OD.COD_USUARIO_VENDEDOR1
				      ,OD.COD_USUARIO_VENDEDOR2
				      ,CASE
				      	WHEN OD.COD_ESTADO_ORDEN_DESPACHO = 4 THEN ''
				      	ELSE 'none'
				      END TR_DISPLAY
				      ,OD.COD_ESTADO_ORDEN_DESPACHO
				      ,F.NRO_ORDEN_COMPRA
				      ,CONVERT(VARCHAR, FECHA_ORDEN_COMPRA_CLIENTE, 103) FECHA_ORDEN_COMPRA_CLIENTE
				      ,U1.NOM_USUARIO	NOM_USUARIO_ANULA
				      ,ITEM
				      ,NOM_PRODUCTO
				      ,COD_PRODUCTO
				      ,CANTIDAD
				      ,CANTIDAD_RECIBIDA
				      ,OD.NOM_SUCURSAL
					  ,OD.NOM_PERSONA
					  ,(SELECT NOM_USUARIO FROM USUARIO WHERE COD_USUARIO = COD_USUARIO_DESPACHA)NOM_USUARIO_DESPACHA
					  ,OD.DIRECCION
					  ,OD.TELEFONO
					  ,OD.FAX
					  ,OD.NOM_COMUNA
					  ,OD.NOM_CIUDAD
				FROM ORDEN_DESPACHO OD LEFT OUTER JOIN FACTURA F ON OD.COD_DOC_ORIGEN = F.COD_FACTURA AND TIPO_DOC_ORIGEN = 'FACTURA'
									   LEFT OUTER JOIN USUARIO U1 ON OD.COD_USUARIO_ANULA = U1.COD_USUARIO
					,EMPRESA E
					,USUARIO U
					,ITEM_ORDEN_DESPACHO IOD
				WHERE OD.COD_ORDEN_DESPACHO = $cod_orden_despacho
				AND E.COD_EMPRESA = OD.COD_EMPRESA
				AND U.COD_USUARIO = OD.COD_USUARIO
				AND IOD.COD_ORDEN_DESPACHO = OD.COD_ORDEN_DESPACHO";
		
		$labels = array();
		$labels['strCOD_ORDEN_DESPACHO'] = $cod_orden_despacho;
		$labels['strNOM_ESTADO_ORDEN_DESPACHO'] = $nom_estado_orden_despacho;
		$rpt = new print_guia_recepcion($sql, $this->root_dir.'appl/orden_despacho/orden_despacho.xml', $labels, "Orden Despacho.pdf", 1);
		$this->_load_record();
		return true;		
	}
	
	function save_record($db){	
		$COD_ORDEN_DESPACHO 		= $this->get_key();
		$FECHA_ORDEN_DESPACHO		= $this->dws['dw_orden_despacho']->get_item(0, 'FECHA_ORDEN_DESPACHO');	
		$COD_USUARIO				= $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO');
		$COD_DOC_ORIGEN 			= $this->dws['dw_orden_despacho']->get_item(0, 'COD_DOC_ORIGEN');
		$TIPO_DOC_ORIGEN 			= $this->dws['dw_orden_despacho']->get_item(0, 'TIPO_DOC_ORIGEN');
		$REFERENCIA 				= $this->dws['dw_orden_despacho']->get_item(0, 'REFERENCIA');
		$OBS 						= $this->dws['dw_orden_despacho']->get_item(0, 'OBS');
		$MOTIVO_ANULA 				= $this->dws['dw_orden_despacho']->get_item(0, 'MOTIVO_ANULA');
		$COD_EMPRESA 				= $this->dws['dw_orden_despacho']->get_item(0, 'COD_EMPRESA');
		$RUT 						= $this->dws['dw_orden_despacho']->get_item(0, 'RUT');
		$DIG_VERIF 					= $this->dws['dw_orden_despacho']->get_item(0, 'DIG_VERIF');
		$NOM_EMPRESA 				= $this->dws['dw_orden_despacho']->get_item(0, 'NOM_EMPRESA');
		$GIRO 						= $this->dws['dw_orden_despacho']->get_item(0, 'GIRO');
		$COD_USUARIO_IMPRESION 		= $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO_IMPRESION');
		$COD_USUARIO_VENDEDOR1 		= $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO_VENDEDOR1');
		$COD_USUARIO_VENDEDOR2 		= $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO_VENDEDOR2');
		$COD_ESTADO_ORDEN_DESPACHO 	= $this->dws['dw_orden_despacho']->get_item(0, 'COD_ESTADO_ORDEN_DESPACHO');
		$COD_USUARIO_DESPACHA 		= $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO_DESPACHA');
		
		if($MOTIVO_ANULA <> '')
			$COD_USUARIO_ANULA = $this->cod_usuario;
		
		$COD_ORDEN_DESPACHO			= ($COD_ORDEN_DESPACHO=='') ? "null" : $COD_ORDEN_DESPACHO;
		$COD_DOC_ORIGEN				= ($COD_DOC_ORIGEN=='') ? "null" : $COD_DOC_ORIGEN;
		$MOTIVO_ANULA				= ($MOTIVO_ANULA=='') ? "null" : "'$MOTIVO_ANULA'";
		$COD_USUARIO_ANULA			= ($COD_USUARIO_ANULA=='') ? "null" : $COD_USUARIO_ANULA;
		$COD_USUARIO_IMPRESION		= ($COD_USUARIO_IMPRESION=='') ? "null" : $COD_USUARIO_IMPRESION;
		$FECHA_ORDEN_DESPACHO		= ($FECHA_ORDEN_DESPACHO=='') ? "null" : $this->str2date($FECHA_ORDEN_DESPACHO);
		$TIPO_DOC_ORIGEN			= ($TIPO_DOC_ORIGEN=='') ? "null" : $TIPO_DOC_ORIGEN;
		$COD_USUARIO_VENDEDOR2		= ($COD_USUARIO_VENDEDOR2=='') ? "null" : $COD_USUARIO_VENDEDOR2;
		$COD_USUARIO_DESPACHA		= ($COD_USUARIO_DESPACHA=='') ? "null" : $COD_USUARIO_DESPACHA;
		
		$sp = 'spu_orden_despacho';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion'
	    		  ,$COD_ORDEN_DESPACHO
	    		  ,$FECHA_ORDEN_DESPACHO	
				  ,$COD_USUARIO
				  ,$COD_DOC_ORIGEN
				  ,$TIPO_DOC_ORIGEN
				  ,'$REFERENCIA'
				  ,'$OBS'
				  ,$COD_USUARIO_ANULA
				  ,$MOTIVO_ANULA
				  ,$COD_EMPRESA
				  ,$RUT
				  ,'$DIG_VERIF'
				  ,'$NOM_EMPRESA'
				  ,'$GIRO'
				  ,$COD_USUARIO_IMPRESION
				  ,$COD_USUARIO_VENDEDOR1
				  ,$COD_USUARIO_VENDEDOR2
				  ,$COD_ESTADO_ORDEN_DESPACHO
				  ,$COD_USUARIO_DESPACHA"; 
		
		if($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()){
				$COD_ORDEN_DESPACHO = $db->GET_IDENTITY();
				$this->dws['dw_orden_despacho']->set_item(0, 'COD_ORDEN_DESPACHO', $COD_ORDEN_DESPACHO);
			}
			
			for($i=0; $i<$this->dws['dw_item_orden_despacho']->row_count(); $i++)
				$this->dws['dw_item_orden_despacho']->set_item($i, 'COD_ORDEN_DESPACHO', $COD_ORDEN_DESPACHO);				
			
			if(!$this->dws['dw_item_orden_despacho']->update($db))			
			 	return false;
			
			
			return true;
		}
		return false;			
	}
}

class print_guia_recepcion extends reporte{	
	function print_guia_recepcion($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	
	function modifica_pdf(&$pdf){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		$y_ini = $pdf->GetY() + 50;

		$pdf->SetFont('Arial','',8.5);
		$pdf->SetXY(30,$y_ini-15);
		$pdf->Cell(555, 15, 'OBSERVACION:', '', '','L');

		$pdf->SetXY(30,$y_ini+5);
		
		$pdf->MultiCell(554, 15, $result[0]['OBS'], '1', 'T');
		
		$y_ini = $pdf->GetY() + 50;
		
		$pdf->SetFont('Arial','B',11);
		$pdf->SetTextColor(255, 0, 0);
		$pdf->SetXY(50,$y_ini);
		$pdf->Cell(200, 15, $result[0]['NOM_USUARIO_DESPACHA'], '', '','L');//NOM_USUARIO_DESPACHA
		
		$pdf->SetXY(365,$y_ini);
		$pdf->Cell(200, 15, $result[0]['NOM_PERSONA'], '', '','R');//NOM_USUARIO_RECIBE
		
		$pdf->SetFont('Arial','',8.5);		
	}
}
?>