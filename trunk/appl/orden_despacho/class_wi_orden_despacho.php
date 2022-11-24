<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../empresa/class_dw_help_empresa.php");
require_once(dirname(__FILE__)."/../ws_client_biggi/class_client_biggi.php");

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
					  ,COD_ORDEN_DESPACHO COD_ORDEN_DESPACHO_H
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
					  ,OD.RECIBIDO_POR
					  ,CONVERT(VARCHAR, FECHA_RECEPCION, 103) FECHA_RECEPCION
					  ,(select 'N° '+convert(varchar,F.NRO_FACTURA)+' del '+CONVERT(varchar,F.FECHA_FACTURA,103)+' creada por '+UF.NOM_USUARIO 
						from FACTURA F, USUARIO UF
						WHERE F.COD_FACTURA = OD.COD_DOC_ORIGEN
						AND UF.COD_USUARIO = F.COD_USUARIO_VENDEDOR1)FACTURA
					  ,ES_RECIBIDO_POR	
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
		
		$this->add_control(new edit_check_box('ES_RECIBIDO_POR', 'S', 'N'));
		
		$sql = "SELECT COD_USUARIO COD_USUARIO_VENDEDOR1
					  ,NOM_USUARIO
				FROM USUARIO";
										
		$this->add_control(new drop_down_dw('COD_USUARIO_VENDEDOR1',$sql,145));
		
		$sql = "select P.COD_PERSONA RECIBIDO_POR, 
						P.NOM_PERSONA 
				from PERSONA P, FACTURA F,ORDEN_DESPACHO O
				WHERE  O.COD_ORDEN_DESPACHO = {KEY1}
				AND F.COD_FACTURA = O.COD_DOC_ORIGEN
				AND P.COD_SUCURSAL = F.COD_SUCURSAL_FACTURA";
										
		$this->add_control(new drop_down_dw('RECIBIDO_POR',$sql,135));
		$this->add_control(new edit_text_multiline('OBS',54,4));
		$this->add_control(new edit_text_upper('REFERENCIA',60,150));
		
		$this->add_control(new edit_text('MOTIVO_ANULA',90,150));
		$this->add_control(new static_text('FECHA_ANULA'));
		$this->add_control(new static_text('FACTURA'));
		$this->add_control(new static_text('NOM_USUARIO_ANULA'));
		
		$sql = "SELECT COD_USUARIO COD_USUARIO_DESPACHA
					  ,NOM_USUARIO
				FROM USUARIO
				WHERE ES_DESPACHADOR = 'S'";
										
		$this->add_control(new drop_down_dw('COD_USUARIO_DESPACHA',$sql,145));
		$this->add_control(new edit_text_hidden('COD_ORDEN_DESPACHO_H'));
		$this->add_control(new edit_date('FECHA_RECEPCION'));
	}
}

class wi_orden_despacho extends w_input {
	function wi_orden_despacho($cod_item_menu){
		parent::w_input('orden_despacho', $cod_item_menu);
		
		$this->dws['dw_orden_despacho'] = new dw_orden_despacho();
		$this->dws['dw_item_orden_despacho'] = new dw_item_orden_despacho();
		
		$this->dws['dw_etiqueta'] = new datawindow("exec spdw_ventana_item_etiqueta {KEY1}");
	}
	
	function new_record() {
		$this->dws['dw_orden_despacho']->insert_row();
	}
	
	function load_record() {
		$cod_orden_despacho = $this->get_item_wo($this->current_record, 'COD_ORDEN_DESPACHO');
		$this->dws['dw_orden_despacho']->retrieve($cod_orden_despacho);
		$this->dws['dw_item_orden_despacho']->retrieve($cod_orden_despacho);
		$this->dws['dw_orden_despacho']->controls['RECIBIDO_POR']->retrieve($cod_orden_despacho);
		$this->dws['dw_etiqueta']->retrieve($cod_orden_despacho);
		
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
			
			$this->b_print_visible 	 = false;
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible	 = false;
		}
		$recibido_por = $this->dws['dw_orden_despacho']->get_item(0, 'RECIBIDO_POR');
		if($recibido_por == ''){
			$COD_DOC_ORIGEN = $this->dws['dw_orden_despacho']->get_item(0, 'COD_DOC_ORIGEN');
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "SELECT P.COD_PERSONA FROM PERSONA P, FACTURA F
				WHERE F.COD_FACTURA = $COD_DOC_ORIGEN
				AND P.COD_PERSONA = F.COD_PERSONA";
			$result = $db->build_results($sql);
			$this->dws['dw_orden_despacho']->set_item(0, 'RECIBIDO_POR', $result[0]['COD_PERSONA']);
		}
	}
	
	function get_key() {
		return $this->dws['dw_orden_despacho']->get_item(0, 'COD_ORDEN_DESPACHO');
	}
	
	function print_record() {
		$cod_orden_despacho = $this->get_key();
	    $nom_estado_orden_despacho = $this->dws['dw_orden_despacho']->get_item(0, 'NOM_ESTADO_ORDEN_DESPACHO');
	    
	    $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	    $db->BEGIN_TRANSACTION();
	    $sp = 'spu_orden_despacho';
		$param = "'PRINT', $cod_orden_despacho, null, ".$this->cod_usuario;
	    
		if ($db->EXECUTE_SP($sp, $param)){
		
			$sql = "SELECT NRO_ORDEN_COMPRA
						 ,WS_ORIGEN
					FROM ORDEN_DESPACHO OD
						,FACTURA F
					WHERE OD.COD_ORDEN_DESPACHO = $cod_orden_despacho
					AND F.COD_FACTURA = OD.COD_DOC_ORIGEN";
			$result = $db->build_results($sql);
			$nro_orden_compra = $result[0]['NRO_ORDEN_COMPRA'];

			if($result[0]['WS_ORIGEN'] == 'COMERCIAL'){
				$empresa = 'COMERCIAL';
				
				$sql = "SELECT U.INI_USUARIO+'|'+CONVERT(VARCHAR, OC.COD_ORDEN_COMPRA)+'|'+CONVERT(VARCHAR, NV.COD_NOTA_VENTA)+'|'+NV.REFERENCIA RESULTADO
    					FROM BIGGI.dbo.ORDEN_COMPRA OC
    						,BIGGI.dbo.NOTA_VENTA NV
    						,BIGGI.dbo.USUARIO U
    						,BIGGI.dbo.EMPRESA E
    					WHERE OC.COD_ORDEN_COMPRA = $nro_orden_compra
    					AND NV.COD_NOTA_VENTA = OC.COD_NOTA_VENTA
    					AND U.COD_USUARIO = NV.COD_USUARIO_VENDEDOR1
    					AND E.COD_EMPRESA = NV.COD_EMPRESA";
				$result     = $db->build_results($sql);
				$result_ws  = $result[0]['RESULTADO'];
				
			}else if($result[0]['WS_ORIGEN'] == 'BODEGA'){
				$empresa = 'BODEGA';
				
				$sql = "SELECT U.INI_USUARIO+'|'+CONVERT(VARCHAR, OC.COD_ORDEN_COMPRA)+'|'+CONVERT(VARCHAR, SC.COD_SOLICITUD_COMPRA)+'|'+SC.REFERENCIA RESULTADO
    					FROM BODEGA_BIGGI.dbo.ORDEN_COMPRA OC
    						,BODEGA_BIGGI.dbo.SOLICITUD_COMPRA SC
    						,BODEGA_BIGGI.dbo.USUARIO U
    					WHERE OC.COD_ORDEN_COMPRA = $nro_orden_compra
    					AND SC.COD_SOLICITUD_COMPRA = OC.COD_DOC
    					AND U.COD_USUARIO = SC.COD_USUARIO";

				$result = $db->build_results($sql);
				$result_ws  = $result[0]['RESULTADO'];
				
			}else if(strpos($result[0]['WS_ORIGEN'], 'RENTAL') !== false){
				$empresa = 'RENTAL';
				
				$sql = "SELECT U.INI_USUARIO+'|'+CONVERT(VARCHAR, OC.COD_ORDEN_COMPRA)+'|'+ISNULL(CONVERT(VARCHAR, A.COD_ARRIENDO), 'NO ASOCIADO')+'|'+A.REFERENCIA RESULTADO
    					FROM RENTAL.dbo.ORDEN_COMPRA OC LEFT OUTER JOIN RENTAL.dbo.ARRIENDO A ON A.COD_ARRIENDO = OC.COD_DOC
    						,RENTAL.dbo.USUARIO U
    					WHERE OC.COD_ORDEN_COMPRA = $nro_orden_compra
    					AND U.COD_USUARIO = A.COD_USUARIO_VENDEDOR1";
				$result = $db->build_results($sql);
				$result_ws  = $result[0]['RESULTADO'];
				
			}else{
				$empresa = '';
			}
			
			/*if($empresa <> ''){
				$nro_orden_compra = $result[0]['NRO_ORDEN_COMPRA'];
				$sql = "select SISTEMA, URL_WS, USER_WS,PASSWROD_WS  from PARAMETRO_WS
						where SISTEMA = '$empresa'";
				$result = $db->build_results($sql);
				
				$user_ws		= $result[0]['USER_WS'];
				$passwrod_ws	= $result[0]['PASSWROD_WS'];
				$url_ws			= $result[0]['URL_WS'];
		
				$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
				$result_ws = $biggi->cli_print_adicional($empresa, $nro_orden_compra);
			}*/
			
			$result_arr = explode("|", $result_ws);
			
			if(strpos($result_ws, '|') !== false){
				$ini_usuario		= "'".$result_arr[0]."'";
				$nro_orden_compra	= $result_arr[1];
				$cod_doc			= $result_arr[2];
				$referencia			= "'".$result_arr[3]."'";	
			}else{
				$ini_usuario		= "NULL";
				$nro_orden_compra	= "NULL";
				$cod_doc			= "NULL";
				$referencia			= "NULL";
			}
			/* SE TRUNCA A 60 CARACTERES NOM_PRODUCTO EN IMPRESO DE OD MH 22-01-2020 */
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
					      ,LEFT(IOD.NOM_PRODUCTO,58) NOM_PRODUCTO
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
						  ,F.NRO_FACTURA
						  ,(SELECT UD.NOM_USUARIO FROM USUARIO UD WHERE UD.COD_USUARIO = OD.COD_USUARIO_VENDEDOR1) VENDEDOR_1
						  ,ES_RECIBIDO_POR
						  ,$ini_usuario INI_USUARIO
						  ,$nro_orden_compra NRO_ORDEN_COMPRA_WS
						  ,$cod_doc COD_DOC
						  ,'$empresa' EMPRESA
						  ,$referencia REFERENCIA
					FROM ORDEN_DESPACHO OD LEFT OUTER JOIN FACTURA F ON OD.COD_DOC_ORIGEN = F.COD_FACTURA AND TIPO_DOC_ORIGEN = 'FACTURA'
										   LEFT OUTER JOIN USUARIO U1 ON OD.COD_USUARIO_ANULA = U1.COD_USUARIO
						,EMPRESA E
						,USUARIO U
						,ITEM_ORDEN_DESPACHO IOD
					WHERE OD.COD_ORDEN_DESPACHO = $cod_orden_despacho
					AND E.COD_EMPRESA = OD.COD_EMPRESA
					AND U.COD_USUARIO = OD.COD_USUARIO
					AND IOD.COD_ORDEN_DESPACHO = OD.COD_ORDEN_DESPACHO
					ORDER BY IOD.ORDEN ASC";
			
			$labels = array();
			$labels['strCOD_ORDEN_DESPACHO'] = $cod_orden_despacho;
			$labels['strNOM_ESTADO_ORDEN_DESPACHO'] = $nom_estado_orden_despacho;
			$rpt = new print_guia_recepcion($sql, $this->root_dir.'appl/orden_despacho/orden_despacho.xml', $labels, "Orden Despacho.pdf", 1);
			$this->_load_record();
			$db->COMMIT_TRANSACTION();
			return true;
		}else{
			$this->_load_record();
			$this->alert('Ha ocurrido un error inesperado, favor contacte a IntegraSystem.');
			$db->ROLLBACK_TRANSACTION();
			return false;
		}
	}
	
	function print_etiqueta(){
		$cod_orden_despacho = $this->get_key();
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql = "SELECT NRO_ORDEN_COMPRA
					 ,WS_ORIGEN
				FROM ORDEN_DESPACHO OD
					,FACTURA F
				WHERE OD.COD_ORDEN_DESPACHO = $cod_orden_despacho
				AND F.COD_FACTURA = OD.COD_DOC_ORIGEN";
		$result = $db->build_results($sql);
		
		if($result[0]['WS_ORIGEN'] == 'COMERCIAL'){
			$empresa = 'COMERCIAL';
		}else if($result[0]['WS_ORIGEN'] == 'BODEGA'){
			$empresa = 'BODEGA';
		}else if(strpos($result[0]['WS_ORIGEN'], 'RENTAL') !== false){
			$empresa = 'RENTAL';
		}
		
		$nro_orden_compra = $result[0]['NRO_ORDEN_COMPRA'];
		$sql = "select SISTEMA, URL_WS, USER_WS,PASSWROD_WS  from PARAMETRO_WS
				where SISTEMA = '$empresa'";
		$result = $db->build_results($sql);
		
		$user_ws		= $result[0]['USER_WS'];
		$passwrod_ws	= $result[0]['PASSWROD_WS'];
		$url_ws			= $result[0]['URL_WS'];

		$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
		$result = $biggi->cli_print_etiqueta($nro_orden_compra);

		$result_arr = explode("|", $result);
		
		$ini_vendedor = $result_arr[0];
		$nro_orden_compra = $result_arr[1];
		$cliente = $result_arr[2];
		$cod_doc = $result_arr[3];
	    $estructura_bulto = $_POST['wi_hidden'];
		
		$sql= "SELECT '$ini_vendedor' INI_VENDEDOR
					 ,$nro_orden_compra NRO_ORDEN_COMPRA
					 ,'$cliente' CLIENTE
					 ,".$this->get_key()." COD_ORDEN_DESPACHO
					 ,$cod_doc COD_DOC
					 ,CASE
					 	WHEN '$empresa' = 'COMERCIAL' THEN 'NOTA DE VENTA'
					 	WHEN '$empresa' = 'BODEGA' THEN 'SOLICITUD OC'
					 	ELSE 'CONTRATO'
					 END TIPO_DOC";
		 
		$labels = array();
		$labels['strINI_VENDEDOR'] = $ini_vendedor;
		$rpt = new print_etiqueta($sql, $this->root_dir.'appl/orden_despacho/od_print_etiqueta.xml', $labels, "Print_Etiqueta.pdf", 0, $false);
		$this->_load_record();
	}
	
	function habilita_boton(&$temp, $boton, $habilita) {
		parent::habilita_boton($temp, $boton, $habilita);
		
		if($boton == 'print_etiqueta'){
			if($habilita){
				$ruta_over = "'../../images_appl/b_print_etiqueta_over.jpg'";
				$ruta_out = "'../../images_appl/b_print_etiqueta.jpg'";
				$ruta_click = "'../../images_appl/b_print_etiqueta_click.jpg'";
				$control = '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
							'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../images_appl/b_print_etiqueta.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
							'onClick="var vl_tab = document.getElementById(\'wi_current_tab_page\'); if (TabbedPanels1 && vl_tab) vl_tab.value =TabbedPanels1.getCurrentTabIndex(); dlg_print_etiqueta();"/>';
				
				
			}else{
				$control = '<img src="../../images_appl/b_'.$boton.'_d.jpg">';
			}
			
			$temp->setVar("WI_".strtoupper($boton), $control);
		}
	}
	
	function procesa_event() {		
		if(isset($_POST['b_print_etiqueta_x'])){
			$this->print_etiqueta();
		}else
			parent::procesa_event();
	}
	
	function navegacion(&$temp){
		parent::navegacion($temp);
		
		$priv = $this->get_privilegio_opcion_usuario('999010', $this->cod_usuario);
		
		if($priv == 'E' && $this->is_new_record() == false){
			$COD_EMPRESA				= $this->dws['dw_orden_despacho']->get_item(0, 'COD_EMPRESA');
			$COD_ESTADO_ORDEN_DESPACHO	= $this->dws['dw_orden_despacho']->get_item(0, 'COD_ESTADO_ORDEN_DESPACHO');
			
			if(($COD_EMPRESA == 1 || $COD_EMPRESA == 37) && $COD_ESTADO_ORDEN_DESPACHO <> 4){
			
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				
				$sql = "SELECT WS_ORIGEN 
						FROM ORDEN_DESPACHO OD
							,FACTURA F
						WHERE COD_ORDEN_DESPACHO = ".$this->get_key()."
						AND F.COD_FACTURA = OD.COD_DOC_ORIGEN
						AND OD.TIPO_DOC_ORIGEN = 'FACTURA'";
				$result = $db->build_results($sql);
				
				if(count($result) > 0){
					$this->habilita_boton($temp, 'print_etiqueta', true);
				}else{
					$this->habilita_boton($temp, 'print_etiqueta', false);
				}
			
			}else
				$this->habilita_boton($temp, 'print_etiqueta', false);
				
		}else
			$this->habilita_boton($temp, 'print_etiqueta', false);
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
		$COD_USUARIO_DESPACHA 		= $this->dws['dw_orden_despacho']->get_item(0, 'COD_USUARIO_DESPACHA');
		$RECIBIDO_POR				= $this->dws['dw_orden_despacho']->get_item(0, 'RECIBIDO_POR');
		$ES_RECIBIDO_POR			= $this->dws['dw_orden_despacho']->get_item(0, 'ES_RECIBIDO_POR');
		$FECHA_RECEPCION			= $this->dws['dw_orden_despacho']->get_item(0, 'FECHA_RECEPCION');
		
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
		$FECHA_RECEPCION			= ($FECHA_RECEPCION=='') ? "null" : $this->str2date($FECHA_RECEPCION);
		
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
				  ,$COD_USUARIO_DESPACHA
				  ,$RECIBIDO_POR
				  ,'$ES_RECIBIDO_POR'
				  ,$FECHA_RECEPCION"; 
		
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

class print_etiqueta extends reporte{	
	function print_etiqueta($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	
	function modifica_pdf(&$pdf){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result_sql = $db->build_results($this->sql);
		
		$result = session::get('_AJAX_ETIQUETA_');
		session::un_set('_AJAX_ETIQUETA_');
	
		// normales
		for($i=0, $ind=0 ; $i < count($result) ; $i++){		
			if($result[$i]['NORMAL_CHECK'] == 'O')
				continue;
		
			if($result[$i]['NORMAL_CHECK'] == 'S') {
				if($ind % 2 == 0){
					$offset = -50;
					if($ind != 0){
						$pdf->AddPage();
					}
				}else{
					$offset = 330;
				}
				
				$pdf->SetFont('Arial', 'B', 30);
				$pdf->SetXY(130, 100 + $offset);
				$pdf->Cell(370, 40, $result_sql[0]['TIPO_DOC'].' '.$result_sql[0]['COD_DOC'], 0, '','L');
				
				$pdf->SetFont('Arial', '', 15);
				$pdf->SetXY(50, 160 + $offset);
				$pdf->Cell(130, 35, 'Vendedor', 1, '','L');
				$pdf->Cell(130, 35, $result_sql[0]['INI_VENDEDOR'], 1, '','L');
				$pdf->Cell(130, 35, 'N° OC', 1, '','R');
				$pdf->Cell(130, 35, $result_sql[0]['NRO_ORDEN_COMPRA'], 1, '','R');
				
				$pdf->SetXY(50, 195 + $offset);
				$pdf->Cell(130, 35, 'Modelo', 0, '','L');
				$pdf->MultiCell(390, 35, $result[$i]['COD_PRODUCTO'], 0, '','C');
				
				$pdf->Rect(50, 195 + $offset, 130, 95);
				$pdf->Rect(180, 195 + $offset, 390, 95);
				
				$pdf->SetXY(50, 290 + $offset);
				$pdf->Cell(130, 35, 'Cliente', 0, '','L');
				$pdf->MultiCell(390, 35, $result_sql[0]['CLIENTE'], 0, '','L');
				
				$pdf->Rect(50, 290 + $offset, 130, 65);
				$pdf->Rect(180, 290 + $offset, 390, 65);
				
				$pdf->SetFont('Arial', '', 10);
				$pdf->SetXY(48, 360 + $offset);
				$pdf->Cell(145, 40, '(*) Etiqueta sólo para uso interno.', 0, '','L');
					
				$ind++;
			}
		}
		
		
		// bultos
		for($i=1 ; $i <= 6 ; $i++){
			$modelos = "";
			
			for($j = 0 ; $j < count($result) ; $j++){
				if($result[$j]['BULTO'.$i] > 0){
					$modelos = $modelos.$result[$j]['COD_PRODUCTO']."(".$result[$j]['BULTO'.$i]." und) / ";
				}
			} 
			
			if ($modelos != "") {
				if($ind % 2 == 0){
					$offset = -50;
					if($ind != 0){
						$pdf->AddPage();
					}
				}else{
					$offset = 330;
				}
				$modelos = substr($modelos, 0, strlen($modelos)-2);
			
				$pdf->SetFont('Arial', 'B', 30);
				$pdf->SetXY(130, 100 + $offset);
				$pdf->Cell(370, 40, $result_sql[0]['TIPO_DOC'].' '.$result_sql[0]['COD_DOC'], 0, '','L');
				
				$pdf->SetFont('Arial', '', 15);
				$pdf->SetXY(50, 160 + $offset);
				$pdf->Cell(130, 35, 'Vendedor', 1, '','L');
				$pdf->Cell(130, 35, $result_sql[0]['INI_VENDEDOR'], 1, '','L');
				$pdf->Cell(130, 35, 'N° OC', 1, '','R');
				$pdf->Cell(130, 35, $result_sql[0]['NRO_ORDEN_COMPRA'], 1, '','R');
				
				$pdf->SetXY(50, 195 + $offset);
				$pdf->Cell(130, 35, 'Modelo', 0, '','L');
				$pdf->MultiCell(390, 35, $modelos, 0, '','C');
				
				$pdf->Rect(50, 195 + $offset, 130, 95);
				$pdf->Rect(180, 195 + $offset, 390, 95);
				
				$pdf->SetXY(50, 290 + $offset);
				$pdf->Cell(130, 35, 'Cliente', 0, '','L');
				$pdf->MultiCell(390, 35, $result_sql[0]['CLIENTE'], 0, '','L');
				
				$pdf->Rect(50, 290 + $offset, 130, 65);
				$pdf->Rect(180, 290 + $offset, 390, 65);
				
				$pdf->SetFont('Arial', '', 10);
				$pdf->SetXY(48, 360 + $offset);
				$pdf->Cell(145, 40, '(*) Etiqueta sólo para uso interno.', 0, '','L');
					
				$ind++;
			}
		}
			
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
		$y = $pdf->GetY();
		
		
		$cod_orden_despacho = $result[0]['COD_ORDEN_DESPACHO'];
		
		$sql_valida = "select count(*)VALIDA 
                        from ITEM_ORDEN_DESPACHO 
                        WHERE COD_ORDEN_DESPACHO = $cod_orden_despacho and COD_PRODUCTO like 'SET-%'";
		
		$result_v = $db->build_results($sql_valida);
		if($result_v[0]['VALIDA'] > 0){
		    $sql_hijos = "SELECT i.COD_ITEM_ORDEN_DESPACHO
                		  ,i.COD_ORDEN_DESPACHO
                		  ,p.COD_PRODUCTO 
                		  --,p.NOM_PRODUCTO
						  ,LEFT(p.NOM_PRODUCTO,58) NOM_PRODUCTO
                		  ,i.CANTIDAD * pc.CANTIDAD CANTIDAD
                		
                	FROM ITEM_ORDEN_DESPACHO i, PRODUCTO_COMPUESTO pc, PRODUCTO p
                	WHERE i.COD_ORDEN_DESPACHO = $cod_orden_despacho
                	and pc.COD_PRODUCTO = i.COD_PRODUCTO
                	and p.COD_PRODUCTO = pc.COD_PRODUCTO_HIJO";
		    $result_h = $db->build_results($sql_hijos);
		    
		    $pdf->SetFont('Arial','B',11);
		    $pdf->Text(30, $y+16, 'DETALLE '.$result[0]['COD_PRODUCTO']);
		    $pdf->SetXY(30,$y+21);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->SetTextColor(0,0,128);
		    $pdf->Cell(343, 15, 'Producto', '1', '','C');
		    $pdf->Cell(89, 15, 'Modelo', '1', '','C');
		    $pdf->Cell(68, 15, 'Cantidad', '1', '','C');
		    
		    $pdf->SetFont('Arial','',12);
		    $pdf->SetTextColor(0,0,0);
		    $y = $pdf->GetY()+15;
		    
		    for($i = 0; $i < count($result_h); $i++) {
		      $pdf->SetXY(30,$y);  
		      $pdf->Cell(432,15, $result_h[$i]['NOM_PRODUCTO'], '1', '','L');
		      $pdf->Cell(89, 15, $result_h[$i]['COD_PRODUCTO'], '1', '','L');
		      $pdf->Cell(30, 15, $result_h[$i]['CANTIDAD'], '1', '','R');
		      $y= $y+15;
		    }
		}
		
		

		$pdf->SetFont('Arial','',8.5);
		$pdf->SetXY(30,$pdf->GetY()+30);
		$pdf->Cell(555, 15, 'OBSERVACION:', '', '','L');


		$pdf->SetXY(30,$pdf->GetY()+15);
		$pdf->SetFont('Arial','',15);
		$pdf->MultiCell(554, 15,$result[0]['OBS'], '1', 'T');
		
		$y_ini = 700;
		
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY(50,$y_ini-10);
		$pdf->Cell(200, 15, '----------------------------------------------------', '', '','L');
		$pdf->SetXY(50,$y_ini);
		$pdf->Cell(200, 15, 'Entrega: ', '', '','L');
		
		$pdf->SetFont('Arial','B',11);
		$pdf->SetTextColor(255, 0, 0);
		$pdf->SetXY(100,$y_ini);
		$pdf->Cell(200, 15, $result[0]['NOM_USUARIO_DESPACHA'], '', '','L');//NOM_USUARIO_DESPACHA
		
		$pdf->SetXY(50,$y_ini+15);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(200, 15, 'COMERCIAL TODOINOX', '', '','L');
		
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY(370,$y_ini-10);
		$pdf->Cell(200, 15, '----------------------------------------------------', '', '','L');
		$pdf->SetXY(370,$y_ini);
		$pdf->Cell(200, 15, 'Recibe:', '', '','L');
		
		$pdf->SetFont('Arial','B',11);
		$pdf->SetXY(365,$y_ini);
		$pdf->SetTextColor(255, 0, 0);
		
		if($result[0]['ES_RECIBIDO_POR'] == 'S')
			$pdf->Cell(200, 15, $result[0]['NOM_PERSONA'], '', '','R');//NOM_USUARIO_RECIBE
		else{
			$pdf->SetFont('Arial','',11);
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetXY(366,$y_ini+5);
			$pdf->Cell(200, 15, '-----------------------------------------', '', '','R');
		}
		
		
		$pdf->SetXY(369,$y_ini+15);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(200, 15,$result[0]['NOM_EMPRESA'], '', '','L');
		
		$pdf->SetFont('Arial','',8.5);		
	}
}
?>
