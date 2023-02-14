<?php
////////////////////////////////////////
/////////// TODOINOX //////////////////
////////////////////////////////////////
class input_file extends edit_control {
	function input_file($field) {
		parent::edit_control($field);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		return '<input type="file" name="'.$field.'" id="'.$field.'" class="Button"/>';
	}
	function draw_no_entrable($dato, $record) {
		return '';
	}
}
class dw_docs extends datawindow {
	function dw_docs() {
		$sql = "SELECT FD.COD_FACTURA_DOCS		D_COD_FACTURA_DOCS  
						,NULL					D_COD_ENCRIPT
						,FD.COD_FACTURA			D_COD_FACTURA
						,FD.COD_USUARIO         D_COD_USUARIO
						,U.NOM_USUARIO          D_NOM_USUARIO
						,FD.RUTA_ARCHIVO        D_RUTA_ARCHIVO
						,FD.NOM_ARCHIVO         D_NOM_ARCHIVO
						,''						ELIMINA_DOC
						,FD.NOM_ARCHIVO         D_NOM_ARCHIVO_REF
						,CONVERT(VARCHAR, FD.FECHA_REGISTRO, 103)       D_FECHA_REGISTRO
						,FD.OBS                  D_OBS
						,NULL          	  		 D_FILE
						,''          	  		 D_DIV_LINK
						,'NONE'          	  	 D_DIV_FILE
						,FD.ES_OC				 D_ES_OC
						,''						 D_VALUE_OPTION
				FROM FACTURA_DOCS FD, USUARIO U
				WHERE COD_FACTURA = {KEY1}
				AND U.COD_USUARIO = FD.COD_USUARIO"; 
		parent::datawindow($sql, 'FA_DOCS', true, true);
		$this->add_control(new edit_text_upper('D_OBS',100, 50));
		$this->add_control(new static_text('D_NOM_ARCHIVO'));
		$this->add_control(new input_file('D_FILE'));
		$this->add_control($control = new edit_radio_button('D_ES_OC', 'S', 'N','','OC'));
		$control->set_onChange("change_option();");
		$this->add_control(new edit_text_hidden('D_VALUE_OPTION'));

		$this->set_mandatory('D_FILE', 'Archivo');
	}
	function draw_field($field, $record) {
		if ($field=='D_FILE') {
			$status = $this->get_status_row($record);
			if ($status==K_ROW_NEW || $status==K_ROW_NEW_MODIFIED) {
				$row = $this->redirect($record);
				$dato = $this->get_item($record, $field);
				return $this->controls[$field]->draw_entrable($dato, $row);
			}
			else 
				return $this->controls[$field]->draw_no_entrable($dato, $row);
		}
		else
			return parent::draw_field($field, $record);
	}
	function retrieve($cod_factura) {
		parent::retrieve($cod_factura);
		for($i=0; $i<$this->row_count(); $i++) {
			$cod_factura_docs = $this->get_item($i, 'D_COD_FACTURA_DOCS');
			$this->set_item($i, 'D_COD_ENCRIPT', base64_encode($cod_factura_docs));
		}
	}
	function insert_row($row = -1) {
		$row = parent::insert_row($row);
		$this->set_item($row, 'D_COD_USUARIO', $this->cod_usuario);
		$this->set_item($row, 'D_NOM_USUARIO', $this->nom_usuario);
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->set_item($row, 'D_FECHA_REGISTRO', $db->current_date());
		$this->set_item($row, 'D_FILE', 'FA_ARCHIVO_'.$this->redirect($row));
		$this->set_item($row, 'D_DIV_LINK', 'none');
		$this->set_item($row, 'D_DIV_FILE', '');
		$this->set_item($row, 'D_VALUE_OPTION', 'N');
		return $row;
	}
	
	function get_ruta($cod_factura) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT YEAR(FA.FECHA_FACTURA) ANO
						,UPPER(M.NOM_MES) NOM_MES
						,REPLACE(CONVERT(VARCHAR(100),FA.FECHA_FACTURA,103), '/', '-') FECHA
						,P.VALOR RUTA
				FROM FACTURA FA, MES M, PARAMETRO P
				WHERE FA.COD_FACTURA = $cod_factura
				AND M.COD_MES = MONTH(FA.FECHA_FACTURA)
				AND P.COD_PARAMETRO = 63";	// RUTA DOCS
      	$result = $db->build_results($sql);
      	$folder = $result[0]['RUTA'].$result[0]['ANO']."/".$result[0]['NOM_MES']."/".$result[0]['FECHA']."/".$cod_factura."/";
		if (!file_exists($folder))	
			$res = mkdir($folder, 0777 , true);	// recursive = true		
			
		return $folder;
	}
	function update($db, $cod_factura)	{
		$sp = 'spu_factura_docs';

		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;			

			if ($statuts == K_ROW_NEW_MODIFIED) {
				$operacion = 'INSERT';
				$cod_factura_docs = 'null';
				$cod_usuario = $this->cod_usuario;

				// subir archivo
				$ruta_archivo = $this->get_ruta($cod_factura);	// obtiene la ruta donde debe quesdar 

				// direccion absoluta
				$row = $this->redirect($i);
				$file = 'D_FILE_'.$row;
				$nom_archivo = $_FILES[$file]['name'];
				$char = '';
				$pos  = 0;
				$nom_archivo_s='';
				/*
				 * Si el nombre del archivo tiene mas de 94 caracteres
				 * busca el ultimo punto para extraer los caracteres antes de la extension para
				 * acortar el nombre del archivo
				 */
				if(strlen($nom_archivo) > 94){
					for($j=0 ; $j < strlen($nom_archivo) ; $j++){
						$char = substr($nom_archivo, $j, 1);
						if($char == '.')
							$pos = $j;
					}
					$nom_archivo_s = substr($nom_archivo, 0, 90); //nombre archivo sin extension truncado
					$nom_archivo   = substr($nom_archivo, $pos, strlen($nom_archivo)); //la extension
					
					$nom_archivo = $nom_archivo_s.$nom_archivo;
				}
				$e		= array(archivo::getTipoArchivo($nom_archivo));
				$t		= $_FILES[$file]['size'];
				$tmp	= $_FILES[$file]['tmp_name'];

				$archivo = new archivo($nom_archivo, $ruta_archivo, $e,$t,$tmp);
			 	$u = $archivo->upLoadFile();	// sube el archivo al directorio definitivo
			}
			elseif ($statuts == K_ROW_MODIFIED) {
				$operacion = 'UPDATE';
				$cod_factura_docs = $this->get_item($i, 'D_COD_FACTURA_DOCS');
				$cod_usuario = 'null';
				$nom_archivo = 'null';
				$ruta_archivo = 'null';
			}			
			$obs = $this->get_item($i, 'D_OBS');
			$obs = $obs =='' ? 'null' : "'$obs'";
			$es_oc = $this->get_item($i, 'D_VALUE_OPTION');

			$param = "'$operacion'
					,$cod_factura_docs 
					,$cod_factura 
					,$cod_usuario
					,'$ruta_archivo'
					,'$nom_archivo'
					,$obs
					,'$es_oc'";
					
			if (!$db->EXECUTE_SP($sp, $param))
				return false;	
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');			
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
			
			$cod_factura_docs = $this->get_item($i, 'D_COD_FACTURA_DOCS', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_factura_docs"))
				return false;
				
			$ruta_archivo = $this->get_item($i, 'D_RUTA_ARCHIVO', 'delete');
			$nom_archivo = $this->get_item($i, 'D_NOM_ARCHIVO', 'delete');
			
			if (file_exists($ruta_archivo.$nom_archivo))
				unlink($ruta_archivo.$nom_archivo);		
		}			
		return true;
	}
}
class wi_factura extends wi_factura_base {
	const K_BODEGA_TODOINOX			= 1;
	const K_PUEDE_MODIFICAR_CC		= '992041';
	const K_PUEDE_MODIFICAR_TF		= '992045';
	var $cod_cotizacion = '';
	var $verifica_registro_bd;
	
	
	function wi_factura($cod_item_menu) {
		parent::wi_factura_base($cod_item_menu);
		
		$this->dws['dw_factura']->add_control($control = new edit_text_upper('NRO_ORDEN_COMPRA', 18, 18));
		$control->set_onChange("valida_nro_oc();");
		$this->dws['dw_factura']->set_mandatory('COD_CENTRO_COSTO', 'Centro Costo');
		
		
		$js = $this->dws['dw_item_factura']->controls['CANTIDAD']->get_onChange();
		$js ="valida_cantidad(this);".$js;
		$this->dws['dw_item_factura']->controls['CANTIDAD']->set_onChange($js);
		$js = $this->dws['dw_item_factura']->add_control(new edit_text('COD_ITEM_DOC',10, 10, 'hidden'));
		
		$this->add_auditoria('COD_CENTRO_COSTO');
		
		// documentos relacionados a la NV
		$this->dws['dw_docs'] = new dw_docs();
		
		$priv = $this->get_privilegio_opcion_usuario('992065', $this->cod_usuario);
		if($priv == 'E')
			$this->dws['dw_factura']->set_entrable('GENERA_ORDEN_DESPACHO', true);
		else	
			$this->dws['dw_factura']->set_entrable('GENERA_ORDEN_DESPACHO', false);
	}
	function new_record() {
		parent::new_record();
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$this->dws['dw_factura']->set_item(0, 'COD_BODEGA', self::K_BODEGA_TODOINOX);
		$this->dws['dw_factura']->set_item(0, 'GENERA_SALIDA', 'S');
		$this->dws['dw_factura']->set_entrable('GENERA_SALIDA', false);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA', self::K_TIPO_FACTURA_VENTA);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA_H', self::K_TIPO_FACTURA_VENTA);
		$this->dws['dw_factura']->controls['NOM_FORMA_PAGO_OTRO']->set_type('hidden');
		$this->dws['dw_factura']->set_item(0, 'DISPLAY_DESCARGA','none');
		$this->dws['dw_factura']->set_item(0, 'GENERA_ORDEN_DESPACHO', 'S');
		
		
		$NRO_FACTURA = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		IF($NRO_FACTURA == ''){
			$this->dws['dw_docs']->set_entrable_dw(false);
		}
	/*	$sql	= "select 	 null COD_CENTRO_COSTO
							,null	NOM_CENTRO_COSTO";
		$this->dws['dw_factura']->add_control(new drop_down_dw('COD_CENTRO_COSTO',$sql,150));*/
			
		if (session::is_set('FA_CREADA_DESDE')) {
			$cod_cotizacion = session::get('FA_CREADA_DESDE');	
			if(session::is_set('PRECIO_PRODUCTO_ORIGINAL'))
				$this->alert('La cotizaciï¿½n Nï¿½ '.$cod_cotizacion.' excede el plazo de validez de oferta, se usarï¿½n precios actualizados');
			
			$this->creada_desde($cod_cotizacion);
			session::un_set('FA_CREADA_DESDE');
			$this->dws['dw_factura']->set_item(0, 'ORIGEN_FACTURA', 'CREAR_DESDE');
			return;
		}
		if (session::is_set('FACTURA_DESDE_OC_COMERCIAL')) {
		
			$cod_oc = session::get('FACTURA_DESDE_OC_COMERCIAL');	
			$this->creada_desde_comercial($cod_oc);
			session::un_set('FACTURA_DESDE_OC_COMERCIAL');
			return;
		}
		if (session::is_set('FACTURA_DESDE_OC')) {
			
			$ws_origen = session::get('WS_ORIGEN');
			$array = session::get('FACTURA_DESDE_OC');	
			$this->creada_desde_oc($array, $ws_origen);
			$this->dws['dw_factura']->set_item(0, 'ORIGEN_FACTURA', 'CREAR_DESDE');
			session::un_set('FACTURA_DESDE_OC');
			session::un_set('WS_ORIGEN');
			return;
		}

		unset($this->dws['dw_factura']->controls['COD_FORMA_PAGO']);
		$sql="SELECT F.COD_FORMA_PAGO
				    ,NOM_FORMA_PAGO
			  FROM FORMA_PAGO F
			  WHERE ES_VIGENTE = 'S'
			  ORDER BY ORDEN";
		$this->dws['dw_factura']->add_control($control = new drop_down_dw('COD_FORMA_PAGO',$sql,150));
		$control->set_onChange("change_forma_pago();");
		
		$this->dws['dw_factura']->set_item(0, 'GENERA_ORDEN_DESPACHO', 'S');
		$this->dws['dw_factura']->set_item(0, 'ORIGEN_FACTURA', 'CREAR');
		$this->dws['dw_factura']->set_item(0, 'COD_USUARIO_VENDEDOR1', $this->cod_usuario);
		$this->dws['dw_factura']->set_item(0, 'COD_CENTRO_COSTO', '017');
		$this->dws['dw_factura']->set_item(0, 'PORC_VENDEDOR1', '0');
	}
	
	
	function creada_desde_comercial($cod_oc) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT  COD_ITEM_ORDEN_COMPRA
					   ,COD_ORDEN_COMPRA
					   ,ORDEN
					   ,ITEM
					   ,COD_PRODUCTO
					   ,NOM_PRODUCTO
					   ,CANTIDAD
					   ,PRECIO
				FROM ITEM_OC_COMERCIAL
				WHERE COD_ORDEN_COMPRA = $cod_oc";
				
		$result = $db->build_results($sql);
		
		for ($i=0; $i<count($result); $i++) {
			$this->dws['dw_item_factura']->insert_row();
			$this->dws['dw_item_factura']->set_item($i, 'ORDEN', $result[$i]['ORDEN']);
			$this->dws['dw_item_factura']->set_item($i, 'ITEM', $result[$i]['ITEM']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO', $result[$i]['COD_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO_OLD', $result[$i]['COD_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'NOM_PRODUCTO', $result[$i]['NOM_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD', $result[$i]['CANTIDAD']);
			$this->dws['dw_item_factura']->set_item($i, 'PRECIO', $result[$i]['PRECIO']);
		}
		
			$sql = "select E.COD_EMPRESA
    					,E.ALIAS
    					,E.RUT
    					,E.DIG_VERIF
    					,E.NOM_EMPRESA
    					,E.GIRO
    					,S.COD_SUCURSAL
    					,dbo.f_get_direccion('SUCURSAL', S.COD_SUCURSAL, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_FACTURA
		    			,dbo.f_emp_get_mail_cargo_persona(P.COD_PERSONA,  '[EMAIL] - [NOM_CARGO]') MAIL_CARGO_PERSONA
		    			,'none' DISPLAY_DESCARGA
				from EMPRESA E, SUCURSAL S, PERSONA P
				where E.COD_EMPRESA = 1 
				  and S.COD_EMPRESA = E.COD_EMPRESA
				  and P.COD_PERSONA = S.COD_SUCURSAL";
			  
        $result_emp = $db->build_results($sql);

		$this->dws['dw_factura']->set_item(0, 'COD_EMPRESA', $result_emp[0]['COD_EMPRESA']);
		$cod_empresa = $result_emp[0]['COD_EMPRESA'];	
		$this->dws['dw_factura']->set_item(0, 'ALIAS', $result_emp[0]['ALIAS']);	
		$this->dws['dw_factura']->set_item(0, 'RUT', $result_emp[0]['RUT']);	
		$this->dws['dw_factura']->set_item(0, 'DIG_VERIF', $result_emp[0]['DIG_VERIF']);	
		$this->dws['dw_factura']->set_item(0, 'NOM_EMPRESA', $result_emp[0]['NOM_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'GIRO', $result_emp[0]['GIRO']);	
		$this->dws['dw_factura']->set_item(0, 'DIRECCION_FACTURA', $result_emp[0]['DIRECCION_FACTURA']);
		$cod_sucursal = $result_emp[0]['COD_SUCURSAL'];
		$this->dws['dw_factura']->set_item(0, 'COD_SUCURSAL_FACTURA',$cod_sucursal);
		
		$sql_oc= "SELECT  SUBTOTAL
						,COD_PERSONA
						,PORC_DSCTO1
				 		,MONTO_DSCTO1
						,TOTAL_NETO
				    	,MONTO_IVA
				    	,TOTAL_CON_IVA
				    	,REFERENCIA
				    	,COD_NOTA_VENTA
				    	,COD_ORDEN_COMPRA
				    	,convert(varchar(10),FECHA_ORDEN_COMPRA,103)FECHA_ORDEN_COMPRA 
				FROM OC_COMERCIAL
				WHERE COD_ORDEN_COMPRA = $cod_oc";
		$result_oc = $db->build_results($sql_oc);
		$monto_iva=$result_oc[0]['MONTO_IVA'];
		$this->dws['dw_factura']->set_item(0, 'SUM_TOTAL', $result_oc[0]['SUBTOTAL']);
		$this->dws['dw_factura']->set_item(0, 'COD_PERSONA', $result_oc[0]['COD_PERSONA']);
		$this->dws['dw_factura']->set_item(0, 'PORC_DSCTO1', $result_oc[0]['PORC_DSCTO1']);
		$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO1', $result_oc[0]['MONTO_DSCTO1']);
		$this->dws['dw_factura']->set_item(0, 'TOTAL_NETO', $result_oc[0]['TOTAL_NETO']);
		$this->dws['dw_factura']->set_item(0, 'TOTAL_CON_IVA', $result_oc[0]['TOTAL_CON_IVA']);
		$this->dws['dw_factura']->set_item(0, 'REFERENCIA', $result_oc[0]['REFERENCIA']);
		$this->dws['dw_factura']->set_item(0, 'PORC_IVA', $this->get_parametro(1));
		$this->dws['dw_factura']->set_item(0, 'MONTO_IVA', $monto_iva);
		$this->dws['dw_factura']->set_item(0, 'COD_DOC', $result_oc[0]['COD_NOTA_VENTA']);
		$this->dws['dw_factura']->set_item(0, 'NRO_ORDEN_COMPRA', $result_oc[0]['COD_ORDEN_COMPRA']);
		$this->dws['dw_factura']->set_item(0, 'FECHA_ORDEN_COMPRA_CLIENTE', $result_oc[0]['FECHA_ORDEN_COMPRA']);
		
		
		$sql ="SELECT COD_TIPO_FACTURA
				FROM TIPO_FACTURA 
				WHERE COD_TIPO_FACTURA = 3";
		$result= $db->build_results($sql);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA', $result[0]['COD_TIPO_FACTURA']);
		
		
		$this->dws['dw_factura']->calc_computed();
		$this->dws['dw_item_factura']->calc_computed();
				
		$this->dws['dw_factura']->controls['COD_SUCURSAL_FACTURA']->retrieve($cod_empresa);
		$this->dws['dw_factura']->controls['COD_PERSONA']->retrieve($cod_empresa);
		
		$NRO_FACTURA = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		IF($NRO_FACTURA == ''){
			$this->dws['dw_docs']->set_entrable_dw(false);
		}
		
	}
	
	function cant_para_stock($result, $cod_item_oc, $i) {
		//obtener el cod_producto de $cod_item_oc
		$cod_producto = $result['ITEM_ORDEN_COMPRA'][$i]['COD_PRODUCTO'];
		$cant_para_stock = 0;
		
		for($j=0 ; $j < count($result['ITEM_ORDEN_COMPRA']) ; $j++){
			if ($result['ITEM_ORDEN_COMPRA'][$j]['COD_PRODUCTO'] == $cod_producto){
				$cantidad = $result['ITEM_ORDEN_COMPRA'][$j]['CANTIDAD'];
				$cant_para_stock += $cantidad;
			}
		}
		
		return $cant_para_stock;
	}
	
	function creada_desde_oc($result, $ws_origen){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$str_alert_aut = '';
		$str_alert_no_aut = '';
		$str_alert_aut_comp = '';
		$str_alert_no_aut_comp = '';
		$cod_orden_compra = $result['ORDEN_COMPRA'][0]['COD_ORDEN_COMPRA'];
		$rut = $result['ORDEN_COMPRA'][0]['RUT'];
		
		if(session::is_set('RESPETAR_PRECIO')) {
			$respeta_precio = 'S';
			session::un_set('RESPETAR_PRECIO');
		}else{
			$respeta_precio = 'N';
		}

		$sql="SELECT E.COD_EMPRESA
					,ALIAS
					,DIG_VERIF
					,NOM_EMPRESA
					,GIRO
					,P.COD_PERSONA
					,S.COD_SUCURSAL
					,dbo.f_get_direccion('SUCURSAL', S.COD_SUCURSAL, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_FACTURA						
					,dbo.f_emp_get_mail_cargo_persona(P.COD_PERSONA, '[EMAIL]') MAIL_CARGO_PERSONA
					,'none' DISPLAY_DESCARGA
			  FROM EMPRESA E
				  ,SUCURSAL S
				  ,PERSONA P
			  WHERE RUT = $rut
			  AND E.COD_EMPRESA = S.COD_EMPRESA
			  AND P.COD_SUCURSAL = S.COD_SUCURSAL";
		$result_emp = $db->build_results($sql);
		$cod_empresa = $result_emp[0]['COD_EMPRESA'];
		
		$sql = "SELECT COUNT(*) COUNT
				FROM FACTURA
				WHERE NRO_ORDEN_COMPRA = '$cod_orden_compra'
				AND COD_EMPRESA = ".$cod_empresa;

		$result_valida = $db->build_results($sql);
		$this->verifica_registro_bd = $result_valida[0]['COUNT'];
		
		$this->dws['dw_factura']->set_item(0, 'COD_EMPRESA',				$result_emp[0]['COD_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'ALIAS',						$result_emp[0]['ALIAS']);	
		$this->dws['dw_factura']->set_item(0, 'RUT',						$rut);	
		$this->dws['dw_factura']->set_item(0, 'DIG_VERIF',					$result_emp[0]['DIG_VERIF']);	
		$this->dws['dw_factura']->set_item(0, 'NOM_EMPRESA',				$result_emp[0]['NOM_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'GIRO',						$result_emp[0]['GIRO']);	
		$this->dws['dw_factura']->set_item(0, 'DIRECCION_FACTURA',			$result_emp[0]['DIRECCION_FACTURA']);
		$this->dws['dw_factura']->set_item(0, 'MAIL_CARGO_PERSONA',			$result_emp[0]['MAIL_CARGO_PERSONA']);
		$this->dws['dw_factura']->set_item(0, 'COD_PERSONA',				$result_emp[0]['COD_PERSONA']);
		$this->dws['dw_factura']->set_item(0, 'COD_SUCURSAL_FACTURA',		$result_emp[0]['COD_SUCURSAL']);
		
		$this->dws['dw_factura']->set_item(0, 'NRO_ORDEN_COMPRA',			$cod_orden_compra);
		$this->dws['dw_factura']->set_item(0, 'WS_ORIGEN',					$ws_origen);
		
		unset($this->dws['dw_factura']->controls['COD_CENTRO_COSTO']);
		if($ws_origen == 'COMERCIAL'){
			$sql = "SELECT	COD_CENTRO_COSTO
							,NOM_CENTRO_COSTO
				    FROM	CENTRO_COSTO
				    WHERE COD_CENTRO_COSTO in ('010', '011', '012', '013', '014')
				    ORDER BY COD_CENTRO_COSTO";
		}else if($ws_origen == 'BODEGA'){
			$sql = "SELECT	COD_CENTRO_COSTO
							,NOM_CENTRO_COSTO
				   FROM	CENTRO_COSTO
				   WHERE COD_CENTRO_COSTO = '015'
				   ORDER BY COD_CENTRO_COSTO";		
		}else{	//RENTAL
			$sql = "SELECT	COD_CENTRO_COSTO
							,NOM_CENTRO_COSTO
				    FROM	CENTRO_COSTO
				    WHERE COD_CENTRO_COSTO in ('010', '011', '012', '013', '014')
				    ORDER BY COD_CENTRO_COSTO";
		}
		
		$this->dws['dw_factura']->add_control(new drop_down_dw('COD_CENTRO_COSTO',$sql,150));
		
		if($ws_origen == 'COMERCIAL'){
			$this->dws['dw_factura']->set_item(0, 'COD_CENTRO_COSTO', '014');
			$this->dws['dw_factura']->set_item(0, 'REFERENCIA', 'NV:'.$result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'].' / '.$result['ORDEN_COMPRA'][0]['NV_NOM_EMPRESA'].' / '.$result['ORDEN_COMPRA'][0]['OC_NOM_USUARIO']);
		}else if($ws_origen == 'BODEGA'){
			$this->dws['dw_factura']->set_item(0, 'COD_CENTRO_COSTO', '015');
			$this->dws['dw_factura']->set_item(0, 'REFERENCIA', $result['ITEM_ORDEN_COMPRA'][0]['COD_PRODUCTO'].' / '.'BODEGA BIGGI STOCK');		
		}else{	//RENTAL
			$this->dws['dw_factura']->set_item(0, 'COD_CENTRO_COSTO', '010');
			if($result['ORDEN_COMPRA'][0]['TIPO_ORDEN_COMPRA'] == 'ARRIENDO')
				$this->dws['dw_factura']->set_item(0, 'REFERENCIA', 'ARR: '.$result['ORDEN_COMPRA'][0]['COD_DOC'].' / RENTAL / '.$result['ORDEN_COMPRA'][0]['A_NOM_EMPRESA'].' / '.$result['ORDEN_COMPRA'][0]['OC_NOM_USUARIO']);
			else{
				//$this->dws['dw_factura']->set_item(0, 'REFERENCIA', 'NV:'.$result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'].' / '.$result['ORDEN_COMPRA'][0]['NV_NOM_EMPRESA'].' / '.$result['ORDEN_COMPRA'][0]['OC_NOM_USUARIO']);
				$this->dws['dw_factura']->set_item(0, 'REFERENCIA', 'NV:'.$result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'].' / RENTAL / '.$result['ORDEN_COMPRA'][0]['OC_NOM_USUARIO']);
			}
		}	
		
		$this->dws['dw_factura']->set_item(0, 'FECHA_ORDEN_COMPRA_CLIENTE', $result['ORDEN_COMPRA'][0]['FECHA_ORDEN_COMPRA']);
		$this->dws['dw_factura']->set_item(0, 'RUT',						$result['ORDEN_COMPRA'][0]['RUT']);
		$this->dws['dw_factura']->set_item(0, 'PORC_DSCTO1',				$result['ORDEN_COMPRA'][0]['PORC_DSCTO1']);
		$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO1',				$result['ORDEN_COMPRA'][0]['MONTO_DSCTO1']);	
		$this->dws['dw_factura']->set_item(0, 'PORC_IVA',					$this->get_parametro(1));
		$this->dws['dw_factura']->set_item(0, 'COD_FORMA_PAGO',				$result['ORDEN_COMPRA'][0]['COD_FORMA_PAGO_CLIENTE']);
		
		$sql	= "SELECT COD_TIPO_FACTURA
	  					 ,NOM_TIPO_FACTURA
				   FROM TIPO_FACTURA 
				   WHERE COD_TIPO_FACTURA = 4";
		$result_f = $db->build_results($sql);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA', $result_f[0]['COD_TIPO_FACTURA']);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA_H', $result_f[0]['COD_TIPO_FACTURA']);
		
		$sql="SELECT COD_PERFIL
	  		  FROM USUARIO
	  		  WHERE COD_USUARIO = $this->cod_usuario";
		$result_usu	= $db->build_results($sql);

		$sql="SELECT AUTORIZA_MENU
			   		 FROM AUTORIZA_MENU
			   		 WHERE COD_PERFIL = ".$result_usu[0]['COD_PERFIL']."
			   		 AND COD_ITEM_MENU = '992050'";
		$result_aut	= $db->build_results($sql);
		
		if($ws_origen == 'BODEGA')
			$tipo_doc = 'ITEM_ORDEN_COMPRA_BODEGA';
		else if($ws_origen == 'COMERCIAL')
			$tipo_doc = 'ITEM_ORDEN_COMPRA_COMERCIAL';
		else //RENTAL
			$tipo_doc = 'ITEM_ORDEN_COMPRA_RENTAL';		
		
		$sum_total = 0;
		for ($i=0; $i < count($result['ITEM_ORDEN_COMPRA']); $i++){
			$alert_stk = false;
			
			$cod_item_oc = $result['ITEM_ORDEN_COMPRA'][$i]['COD_ITEM_ORDEN_COMPRA'];
			$cod_producto = $result['ITEM_ORDEN_COMPRA'][$i]['COD_PRODUCTO'];
			$nom_producto = $result['ITEM_ORDEN_COMPRA'][$i]['NOM_PRODUCTO'];
			
			$sql = "SELECT SUM(CANTIDAD) CANTIDAD
					FROM ITEM_FACTURA
					WHERE COD_ITEM_DOC = $cod_item_oc";
					
			if($ws_origen == 'BODEGA')
				$sql.= " AND TIPO_DOC = 'ITEM_ORDEN_COMPRA_BODEGA'";
			else if($ws_origen == 'COMERCIAL')
				$sql.= " AND TIPO_DOC = 'ITEM_ORDEN_COMPRA_COMERCIAL'";
			else //RENTAL
				$sql.= " AND TIPO_DOC = 'ITEM_ORDEN_COMPRA_RENTAL'";		
					
			$result_cant = $db->build_results($sql);
			
			$cantidad = $result['ITEM_ORDEN_COMPRA'][$i]['CANTIDAD'] - $result_cant[0]['CANTIDAD'];
			$cant_para_stock = $this->cant_para_stock($result, $cod_item_oc, $i);
			
			////////Manejo de precio publico e interno////////	
			if($cod_producto != 'E' && $cod_producto != 'TE' && $cod_producto != 'I' && $cod_producto != 'F'){
				
				if($ws_origen == 'BODEGA' && $respeta_precio == 'N'){

					$precio = $this->maneja_precio($db, $cod_empresa, $cod_producto);

				}else if($ws_origen == 'COMERCIAL'){

					if($result['ORDEN_COMPRA'][0]['RP_CLIENTE'] == 'N')
						$precio = $this->maneja_precio($db, $cod_empresa, $cod_producto);
					else{
						if($result['ITEM_ORDEN_COMPRA'][$i]['RP_CLIENTE_IT'] == 'N')
							$precio = $this->maneja_precio($db, $cod_empresa, $cod_producto);
						else{
							$precio = $result['ITEM_ORDEN_COMPRA'][$i]['PRECIO'];
							$this->alert('OC '.$cod_orden_compra.' se encuentra autorizada para respetar precio de compra.\nPor lo tanto, se respetarán los precios indicados por la OC.');
						}
					}

				}else{

					$precio = $result['ITEM_ORDEN_COMPRA'][$i]['PRECIO'];
					
				}
				
				///////Manejo Stock/////
				$sql_stock="SELECT dbo.f_bodega_stock(COD_PRODUCTO, 1, GETDATE()) STOCK
						  		  ,MANEJA_INVENTARIO
						    FROM PRODUCTO
						    WHERE COD_PRODUCTO = '$cod_producto'";
				$result_stock = $db->build_results($sql_stock);

				if($result_aut[0]['AUTORIZA_MENU'] == 'E'){
					if($result_stock[0]['MANEJA_INVENTARIO'] <> 'N'){
						if($cant_para_stock > $result_stock[0]['STOCK']){
							$pos = strpos($str_alert_aut, $cod_producto.', '.$nom_producto);
							if ($pos===false)
								$str_alert_aut .= $cod_producto.', '.$nom_producto.', CANTIDAD SOLICITADA '.$cant_para_stock.'|';
						}															
					}else{
						//Validacion de producto compuesto
						$sql_comp="SELECT COD_PRODUCTO_HIJO
					  					,(CANTIDAD * ".$cant_para_stock.") CANTIDAD
					  					,dbo.f_bodega_stock(COD_PRODUCTO_HIJO, 1, GETDATE()) STOCK
					  					,(SELECT MANEJA_INVENTARIO 
					  				 	  FROM PRODUCTO P 
					  					  WHERE P.COD_PRODUCTO = PC.COD_PRODUCTO_HIJO) MANEJA_INVENTARIO
					  					,(SELECT NOM_PRODUCTO 
					  					  FROM PRODUCTO P 
					  					  WHERE P.COD_PRODUCTO = PC.COD_PRODUCTO_HIJO) NOM_PRODUCTO_HIJO
							  	   FROM PRODUCTO_COMPUESTO PC
								   WHERE COD_PRODUCTO = '$cod_producto'";
						$result_comp = $db->build_results($sql_comp);
						
						if(count($result_comp) > 0){
							for($j=0 ; $j < count($result_comp) ; $j++){
								$cod_producto_hijo = $result_comp[$j]['COD_PRODUCTO_HIJO'];
								$nom_producto_hijo = $result_comp[$j]['NOM_PRODUCTO_HIJO'];
								$cantidad_comp = $result_comp[$j]['CANTIDAD'];
								if($result_comp[$j]['MANEJA_INVENTARIO'] != 'N' && $result_comp[$j]['CANTIDAD'] > $result_comp[$j]['STOCK'])
									$str_alert_aut_comp .= $cod_producto.' = '.$cod_producto_hijo.' / '.$nom_producto_hijo.' / Cantidad necesaria: '.$cantidad_comp.' unds.|';
							}
						}
					}
				}else{
					if($result_stock[0]['MANEJA_INVENTARIO'] <> 'N'){
						if($cant_para_stock > $result_stock[0]['STOCK']){
							$pos = strpos($str_alert_no_aut, $cod_producto.', '.$nom_producto);
							if ($pos===false)
								$str_alert_no_aut .= $cod_producto.', '.$nom_producto.', CANTIDAD SOLICITADA '.$cant_para_stock.'|';
							
							$alert_stk = true;
						}		
					}else{		
						//Validacion de producto compuesto
						$sql_comp="SELECT COD_PRODUCTO_HIJO
					  					,(CANTIDAD * ".$cant_para_stock.") CANTIDAD
					  					,dbo.f_bodega_stock(COD_PRODUCTO_HIJO, 1, GETDATE()) STOCK
					  					,(SELECT MANEJA_INVENTARIO 
					  				 	  FROM PRODUCTO P 
					  					  WHERE P.COD_PRODUCTO = PC.COD_PRODUCTO_HIJO) MANEJA_INVENTARIO
					  					,(SELECT NOM_PRODUCTO 
					  					  FROM PRODUCTO P 
					  					  WHERE P.COD_PRODUCTO = PC.COD_PRODUCTO_HIJO) NOM_PRODUCTO_HIJO
							  	   FROM PRODUCTO_COMPUESTO PC
								   WHERE COD_PRODUCTO = '$cod_producto'";
						$result_comp = $db->build_results($sql_comp);
						
						if(count($result_comp) > 0){
							for($k=0 ; $k < count($result_comp) ; $k++){
								$cod_producto_hijo = $result_comp[$k]['COD_PRODUCTO_HIJO'];
								$nom_producto_hijo = $result_comp[$k]['NOM_PRODUCTO_HIJO'];
								$cantidad_comp = $result_comp[$k]['CANTIDAD'];
								if($result_comp[$k]['MANEJA_INVENTARIO'] != 'N' && $result_comp[$k]['CANTIDAD'] > $result_comp[$k]['STOCK']){
									$str_alert_no_aut_comp .= $cod_producto.' = '.$cod_producto_hijo.' / '.$nom_producto_hijo.' / Cantidad necesaria: '.$cantidad_comp.' unds.|';
									$alert_stk = true;
								}	
							}
						}	
					}		
				}
				//////////////////////
			}else
				$precio = $result['ITEM_ORDEN_COMPRA'][$i]['PRECIO'];
			//////////////////////////////
			
			if($alert_stk == true)
				$cantidad = 0;	
				
			$this->dws['dw_item_factura']->insert_row();
			$this->dws['dw_item_factura']->set_item($i, 'ORDEN',			$result['ITEM_ORDEN_COMPRA'][$i]['ORDEN']);
			$this->dws['dw_item_factura']->set_item($i, 'ITEM',				$result['ITEM_ORDEN_COMPRA'][$i]['ITEM']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO',		$cod_producto);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO_OLD', $cod_producto);
			$this->dws['dw_item_factura']->set_item($i, 'NOM_PRODUCTO',		$nom_producto);
			$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD',			$cantidad);
			$this->dws['dw_item_factura']->set_item($i, 'PRECIO',			$precio);
			$this->dws['dw_item_factura']->set_item($i, 'COD_ITEM_DOC',		$cod_item_oc);
			$this->dws['dw_item_factura']->set_item($i, 'TIPO_DOC',			$tipo_doc);
			
			$total = $precio * $cantidad;
			$sum_total += $total;
		}
		//Alerta para sin stock autorizado
		if($str_alert_aut != ''){
			$message = 'Las cantidades solicitadas en la OC '.$cod_orden_compra.' de BIGGI CHILE SOCIEDAD LIMITADA exceden la cantidad de stock disponible en Bodega Todoinox.\n\n';
			$message .= 'Los productos son:\n\n';
			$arr_productos = explode('|',trim($str_alert_aut,'|'));
			
			for($i=0 ; $i < count($arr_productos) ; $i++)
				$message .=	$arr_productos[$i].'\n\n';
				
			$message .=	'Sin embargo, usted esta autorizado para facturar productos sin stock.';
			$this->alert($message);
		}
		//Alerta para sin stock no autorizado
		if($str_alert_no_aut != ''){
			$message = 'Las cantidades solicitadas en la OC '.$cod_orden_compra.' de BIGGI CHILE SOCIEDAD LIMITADA exceden la cantidad de stock disponible en Bodega Todoinox.\n\n';
			$message .=	'Los productos son:\n\n';
			$arr_productos = explode('|',trim($str_alert_no_aut,'|'));
			
			for($i=0 ; $i < count($arr_productos) ; $i++)
				$message .=	$arr_productos[$i].'\n\n';
			
			$message .=	'Usted no esta autorizado para facturar productos sin stock.';
			$this->alert($message);
		}
		//Alerta para sin stock autorizado compuesto
		if($str_alert_aut_comp != ''){
			$message = 'Las cantidades solicitadas en la OC '.$cod_orden_compra.' de BIGGI CHILE SOCIEDAD LIMITADA exceden la cantidad de stock disponible en Bodega Todoinox.\n\n';
			$message .=	'Las partes de estos productos compuestos que no tiene stock suficiente son:\n\n';
			$arr_productos = explode('|',trim($str_alert_aut_comp,'|'));
			
			for($i=0 ; $i < count($arr_productos) ; $i++)
				$message .=	$arr_productos[$i].'\n\n';
			
			$message .=	'Sin embargo, usted esta autorizado para facturar productos sin stock.';
			$this->alert($message);
		}
		
		//Alerta para sin stock no autorizado compuesto
		if($str_alert_no_aut_comp != ''){
			$message = 'Las cantidades solicitadas en la OC '.$cod_orden_compra.' de BIGGI CHILE SOCIEDAD LIMITADA exceden la cantidad de stock disponible en Bodega Todoinox.\n\n';
			$message .=	'Las partes de estos productos compuestos que no tiene stock suficiente son:\n\n';
			$arr_productos = explode('|',trim($str_alert_no_aut_comp,'|'));
			
			for($i=0 ; $i < count($arr_productos) ; $i++)
				$message .=	$arr_productos[$i].'\n\n';
			
			$message .=	'Usted no esta autorizado para facturar productos sin stock.';
			$this->alert($message);
		}
		
		$this->dws['dw_item_factura']->calc_computed();
		
		$total_neto = $sum_total - $result['ORDEN_COMPRA'][0]['MONTO_DSCTO1'];
		$monto_iva = $total_neto * ($this->get_parametro(1)/100);
		$total_con_iva = $total_neto + $monto_iva;
		
		$this->dws['dw_factura']->set_item(0, 'TOTAL_NETO',		$total_neto);
		$this->dws['dw_factura']->set_item(0, 'MONTO_IVA',		$monto_iva);
		$this->dws['dw_factura']->set_item(0, 'TOTAL_CON_IVA',	$total_con_iva);
			
		$this->dws['dw_factura']->controls['COD_SUCURSAL_FACTURA']->retrieve($cod_empresa);
		$this->dws['dw_factura']->controls['COD_PERSONA']->retrieve($cod_empresa);
		
		$this->dws['dw_factura']->set_entrable('RUT', false);
		$this->dws['dw_factura']->set_entrable('ALIAS', false);
		$this->dws['dw_factura']->set_entrable('NOM_EMPRESA', false);
		$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA', false);
		$this->dws['dw_factura']->set_entrable('COD_PERSONA', false);
		$this->dws['dw_factura']->set_entrable('COD_FORMA_PAGO', false);
		$this->dws['dw_factura']->set_entrable('NO_TIENE_OC', false);
		
		$this->dws['dw_item_factura']->b_add_line_visible = false;
		$this->dws['dw_item_factura']->b_del_line_visible = false;
		
		unset($this->dws['dw_factura']->controls['NRO_ORDEN_COMPRA']);
		$this->dws['dw_factura']->add_control(new static_text('NRO_ORDEN_COMPRA'));
		
		unset($this->dws['dw_factura']->controls['COD_EMPRESA']);
		$this->dws['dw_factura']->add_control(new static_text('COD_EMPRESA'));
		
		$NRO_FACTURA = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		IF($NRO_FACTURA == ''){
			$this->dws['dw_docs']->set_entrable_dw(false);
		}
	}

	function maneja_precio($db, $cod_empresa, $cod_producto){
		$precio = 0;

		$sql_emp = "SELECT COD_EMPRESA
					FROM PRECIO_INT_EMP
					WHERE COD_EMPRESA = $cod_empresa";
			
		$result_emp = $db->build_results($sql_emp);	
		if(count($result_emp) != 0){
			$sql_precio_int = "SELECT  PRECIO_VENTA_INTERNO PRECIO_INT
								FROM 	PRODUCTO
								WHERE COD_PRODUCTO = '$cod_producto'";
				
			$result_precio = $db->build_results($sql_precio_int);
			$precio = $result_precio[0]['PRECIO_INT'];
			
			if($precio == 0.00){
				$sql_precio_pub = "SELECT  PRECIO_VENTA_PUBLICO PRECIO_PUB
									FROM 	PRODUCTO
									WHERE COD_PRODUCTO = '$cod_producto'";
					
				$result_prec_pub = $db->build_results($sql_precio_pub);
				$precio = $result_prec_pub[0]['PRECIO_PUB'];
			}
		}else{
			$sql_precio_pub = "SELECT  PRECIO_VENTA_PUBLICO PRECIO_PUB
								FROM 	PRODUCTO
								WHERE COD_PRODUCTO = '$cod_producto'";
					
			$result_prec_pub = $db->build_results($sql_precio_pub);
			$precio = $result_prec_pub[0]['PRECIO_PUB'];
		}

		return $precio;
	}

	function procesa_event() {		
		if((isset($_POST['b_back_x']) && session::is_set('FACTURA_DESDE_INF_X_FAC')) 
			|| (isset($_POST['b_no_save_x']) && session::is_set('FACTURA_DESDE_INF_X_FAC'))
				|| (isset($_POST['b_delete_x']) && session::is_set('FACTURA_DESDE_INF_X_FAC'))) {
			session::un_set("FACTURA_DESDE_INF_X_FAC");
			$url = $this->root_url."../../commonlib/trunk/php/mantenedor.php?modulo=inf_oc_por_facturar_tdnx&cod_item_menu=4095";
			header ('Location:'.$url);
		}else
			parent::procesa_event();
	}


	function creada_desde_servindus($cod_oc) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT  COD_ITEM_ORDEN_COMPRA
					   ,COD_ORDEN_COMPRA
					   ,ORDEN
					   ,ITEM
					   ,COD_PRODUCTO
					   ,NOM_PRODUCTO
					   ,CANTIDAD
					   ,PRECIO
				FROM ITEM_OC_SERVINDUS
				WHERE COD_ORDEN_COMPRA = $cod_oc";
				
		$result = $db->build_results($sql);
		
		for ($i=0; $i<count($result); $i++){
			$this->dws['dw_item_factura']->insert_row();
			$this->dws['dw_item_factura']->set_item($i, 'ORDEN', $result[$i]['ORDEN']);
			$this->dws['dw_item_factura']->set_item($i, 'ITEM', $result[$i]['ITEM']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO', $result[$i]['COD_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO_OLD', $result[$i]['COD_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'NOM_PRODUCTO', $result[$i]['NOM_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD', $result[$i]['CANTIDAD']);
			$this->dws['dw_item_factura']->set_item($i, 'PRECIO', $result[$i]['PRECIO']);
			
		}
		
			$sql = "select E.COD_EMPRESA
    					,E.ALIAS
    					,E.RUT
    					,E.DIG_VERIF
    					,E.NOM_EMPRESA
    					,E.GIRO
    					,S.COD_SUCURSAL
    					,dbo.f_get_direccion('SUCURSAL', S.COD_SUCURSAL, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_FACTURA
		    			,dbo.f_emp_get_mail_cargo_persona(P.COD_PERSONA,  '[EMAIL] - [NOM_CARGO]') MAIL_CARGO_PERSONA
		    			,'none' DISPLAY_DESCARGA
				from EMPRESA E, SUCURSAL S, PERSONA P
				where E.COD_EMPRESA = 1 
				  and S.COD_EMPRESA = E.COD_EMPRESA
				  and P.COD_PERSONA = S.COD_SUCURSAL";
			  
        $result_emp = $db->build_results($sql);

		$this->dws['dw_factura']->set_item(0, 'COD_EMPRESA', $result_emp[0]['COD_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'ALIAS', $result_emp[0]['ALIAS']);	
		$this->dws['dw_factura']->set_item(0, 'RUT', $result_emp[0]['RUT']);	
		$this->dws['dw_factura']->set_item(0, 'DIG_VERIF', $result_emp[0]['DIG_VERIF']);	
		$this->dws['dw_factura']->set_item(0, 'NOM_EMPRESA', $result_emp[0]['NOM_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'GIRO', $result_emp[0]['GIRO']);	
		$this->dws['dw_factura']->set_item(0, 'DIRECCION_FACTURA', $result_emp[0]['DIRECCION_FACTURA']);
		$cod_sucursal = $result_emp[0]['COD_SUCURSAL'];
		$this->dws['dw_factura']->set_item(0, 'COD_SUCURSAL_FACTURA',$cod_sucursal);
		
		$sql_oc= "SELECT  SUBTOTAL
						,COD_PERSONA
						,PORC_DSCTO1
				 		,MONTO_DSCTO1
						,TOTAL_NETO
				    	,MONTO_IVA
				    	,TOTAL_CON_IVA
				    	,REFERENCIA
				    	,COD_NOTA_VENTA
				    	,COD_ORDEN_COMPRA
				    	,convert(varchar(10),FECHA_ORDEN_COMPRA,103)FECHA_ORDEN_COMPRA 
				FROM OC_SERVINDUS
				WHERE COD_ORDEN_COMPRA = $cod_oc";
		$result_oc = $db->build_results($sql_oc);
		$monto_iva=$result_oc[0]['MONTO_IVA'];
		$this->dws['dw_factura']->set_item(0, 'SUM_TOTAL', $result_oc[0]['SUBTOTAL']);
		$this->dws['dw_factura']->set_item(0, 'COD_PERSONA', $result_oc[0]['COD_PERSONA']);
		$this->dws['dw_factura']->set_item(0, 'PORC_DSCTO1', $result_oc[0]['PORC_DSCTO1']);
		$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO1', $result_oc[0]['MONTO_DSCTO1']);
		$this->dws['dw_factura']->set_item(0, 'TOTAL_NETO', $result_oc[0]['TOTAL_NETO']);
		$this->dws['dw_factura']->set_item(0, 'TOTAL_CON_IVA', $result_oc[0]['TOTAL_CON_IVA']);
		$this->dws['dw_factura']->set_item(0, 'REFERENCIA', $result_oc[0]['REFERENCIA']);
		$this->dws['dw_factura']->set_item(0, 'PORC_IVA', $this->get_parametro(1));
		$this->dws['dw_factura']->set_item(0, 'MONTO_IVA', $monto_iva);
		$this->dws['dw_factura']->set_item(0, 'COD_DOC', $result_oc[0]['COD_NOTA_VENTA']);
		$this->dws['dw_factura']->set_item(0, 'NRO_ORDEN_COMPRA', $result_oc[0]['COD_ORDEN_COMPRA']);
		$this->dws['dw_factura']->set_item(0, 'FECHA_ORDEN_COMPRA_CLIENTE', $result_oc[0]['FECHA_ORDEN_COMPRA']);
		
		
		$sql ="SELECT COD_TIPO_FACTURA
				FROM TIPO_FACTURA 
				WHERE COD_TIPO_FACTURA = 3";
		$result= $db->build_results($sql);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA', $result[0]['COD_TIPO_FACTURA']);
		
		$this->dws['dw_factura']->calc_computed();
		$this->dws['dw_item_factura']->calc_computed();
				
		$this->dws['dw_factura']->controls['COD_SUCURSAL_FACTURA']->retrieve($cod_empresa);
		$this->dws['dw_factura']->controls['COD_PERSONA']->retrieve($cod_empresa);
		
		$NRO_FACTURA = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		IF($NRO_FACTURA == ''){
			$this->dws['dw_docs']->set_entrable_dw(false);
		}
	}
	
	function creada_desde($cod_cotizacion) {
		$this->cod_cotizacion = $cod_cotizacion;
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		
		////////////////////
		// items		
		$sql = "select I.ORDEN
    					,I.ITEM
		    			,I.COD_PRODUCTO
    					,I.NOM_PRODUCTO           
					    , I.CANTIDAD               
					    ,dbo.f_bodega_stock_cero(I.COD_PRODUCTO, 1, getdate()) CANTIDAD_STOCK
					    ,I.PRECIO                 
    					,I.COD_ITEM_COTIZACION
    					,C.SUBTOTAL
    					,C.COD_FORMA_PAGO
    					,C.COD_EMPRESA
    					,C.COD_SUCURSAL_FACTURA
    					,C.COD_PERSONA
    					,C.COD_USUARIO_VENDEDOR1
    					,C.COD_USUARIO_VENDEDOR2
    					,C.PORC_DSCTO2
    					,C.PORC_DSCTO1
    					,C.MONTO_DSCTO1
    					,C.MONTO_DSCTO2
    					,C.PORC_VENDEDOR1
    					,C.PORC_VENDEDOR2
    					,C.TOTAL_NETO
    					,C.MONTO_IVA
    					,C.TOTAL_CON_IVA
    					,C.REFERENCIA
    					,'none' DISPLAY_DESCARGA
				from ITEM_COTIZACION I, BIGGI.DBO.PRODUCTO P ,COTIZACION C
				where I.COD_COTIZACION = $cod_cotizacion
				  and C.COD_COTIZACION = I.COD_COTIZACION
    			  and P.COD_PRODUCTO = I.COD_PRODUCTO
				order by ORDEN";
			
        $result = $db->build_results($sql);
        $sum_total = 0;
        for ($i=0; $i<count($result); $i++) {
			$this->dws['dw_item_factura']->insert_row();
			$this->dws['dw_item_factura']->set_item($i, 'ORDEN', $result[$i]['ORDEN']);
        	
			$this->dws['dw_item_factura']->set_item($i, 'ITEM', $result[$i]['ITEM']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO', $result[$i]['COD_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'COD_PRODUCTO_OLD', $result[$i]['COD_PRODUCTO']);
			$this->dws['dw_item_factura']->set_item($i, 'NOM_PRODUCTO', $result[$i]['NOM_PRODUCTO']);
		
			$cod_forma_pago = $result[$i]['COD_FORMA_PAGO'] ;
			$cod_empresa = $result[$i]['COD_EMPRESA'];
			$cod_persona =  $result[$i]['COD_PERSONA'];// 
			$cod_usuario_ven1 =  $result[$i]['COD_USUARIO_VENDEDOR1'];//
			$cod_usuario_ven2 =  $result[$i]['COD_USUARIO_VENDEDOR2'];//
			$porc_dscto1 =  $result[$i]['PORC_DSCTO1'];//
			$monto_dscto1 =  $result[$i]['MONTO_DSCTO1'];//
			$porc_dscto2 =  $result[$i]['PORC_DSCTO2'];//
			$monto_dscto2 =  $result[$i]['MONTO_DSCTO2'];//
			$cod_sucursal = $result[$i]['COD_SUCURSAL_FACTURA'];
			$porc_vend1 = $result[$i]['PORC_VENDEDOR1'];
			$porc_vend2 = $result[$i]['PORC_VENDEDOR2'];
			$total_neto = $result[$i]['TOTAL_NETO'];
			$monto_iva = $result[$i]['MONTO_IVA'];
			$total_con_iva = $result[$i]['TOTAL_CON_IVA'];
			$cod_producto = $result[$i]['COD_PRODUCTO'];
			$ing_usuario_dscto1 = 'P';
			$ing_usuario_dscto2 = 'P';
			
			if(session::is_set('PRECIO_PRODUCTO_ORIGINAL')){
				if($cod_producto != 'E' && $cod_producto != 'TE' && $cod_producto != 'I' && $cod_producto != 'F'){
					$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
					$sql_emp = "SELECT COD_EMPRESA
					FROM PRECIO_INT_EMP
					WHERE COD_EMPRESA = $cod_empresa";
			
					$result_emp = $db->build_results($sql_emp);	
					if(count($result_emp) != 0){		
						$sql_precio_int = "SELECT  PRECIO_VENTA_INTERNO PRECIO_INT
										   FROM 	PRODUCTO
										   WHERE COD_PRODUCTO = '$cod_producto'";
							
						$result_precio = $db->build_results($sql_precio_int);
						$precio = $result_precio[0]['PRECIO_INT'];
						
						if($precio == 0.00){
							$sql_precio_pub = "SELECT  PRECIO_VENTA_PUBLICO PRECIO_PUB
											   FROM 	PRODUCTO
											   WHERE COD_PRODUCTO = '$cod_producto'";
								
							$result_prec_pub = $db->build_results($sql_precio_pub);
							$precio = $result_prec_pub[0]['PRECIO_PUB'];
						}
					}else{
						$sql_precio_pub = "SELECT  PRECIO_VENTA_PUBLICO PRECIO_PUB
										   FROM 	PRODUCTO
										   WHERE COD_PRODUCTO = '$cod_producto'";
								
						$result_prec_pub = $db->build_results($sql_precio_pub);
						$precio = $result_prec_pub[0]['PRECIO_PUB'];
					}
				}else
					$precio = $result[$i]['PRECIO'];		
			}else
				$precio = $result[$i]['PRECIO'];
			
			$cantidad = $result[$i]['CANTIDAD'];
			$referencia = $result[$i]['REFERENCIA'];
			  
		/*
			if ($result[$i]['CANTIDAD'] > $result[$i]['CANTIDAD_STOCK'])
				$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD',$cantidad);
			else
				$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD', $result[$i]['CANTIDAD_STOCK']);
				
			*/
			$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD',$cantidad);	
			$this->dws['dw_item_factura']->set_item($i, 'CANTIDAD_POR_FACTURAR', $cantidad);
			
			$this->dws['dw_item_factura']->set_item($i, 'PRECIO', $precio);
			$this->dws['dw_item_factura']->set_item($i, 'COD_ITEM_DOC', $result[$i]['COD_ITEM_ORDEN_COMPRA']);
			$this->dws['dw_item_factura']->set_item($i, 'TIPO_DOC', 'ITEM_ORDEN_COMPRA_COMERCIAL');
			$this->dws['dw_item_factura']->set_item($i, 'TD_DISPLAY_CANT_POR_FACT', 'none');
			$total = $cantidad * $precio;
			$this->dws['dw_item_factura']->set_item($i, 'TOTAL', $total);
			$sum_total += $total;
			
        }
        
		$this->dws['dw_item_factura']->controls['ORDEN']->size = 3;
		$this->dws['dw_item_factura']->controls['ITEM']->size = 3;
		$this->dws['dw_item_factura']->controls['COD_PRODUCTO']->size = 20;
		$this->dws['dw_item_factura']->controls['NOM_PRODUCTO']->size = 45;
		
		$this->dws['dw_item_factura']->calc_computed();
		
		//$this->dws['dw_factura']->set_item(0, 'COD_FORMA_PAGO', $cod_forma_pago);
		$this->dws['dw_factura']->set_item(0, 'SUM_TOTAL', $sum_total);
		//$this->dws['dw_item_factura']->set_item(0, 'SUM_TOTAL', $sum_total);
		$this->dws['dw_factura']->set_item(0, 'PORC_IVA', $this->get_parametro(1));	// IVA
		
		$this->dws['dw_factura']->calc_computed();
		
			$sql = "select E.COD_EMPRESA
    					,E.ALIAS
    					,E.RUT
    					,E.DIG_VERIF
    					,E.NOM_EMPRESA
    					,E.GIRO
    					,S.COD_SUCURSAL
    					,dbo.f_get_direccion('SUCURSAL', S.COD_SUCURSAL, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_FACTURA
		    			,dbo.f_emp_get_mail_cargo_persona(P.COD_PERSONA,  '[EMAIL] - [NOM_CARGO]') MAIL_CARGO_PERSONA
				from EMPRESA E, SUCURSAL S, PERSONA P
				where E.COD_EMPRESA = $cod_empresa
				  and S.COD_EMPRESA = E.COD_EMPRESA
				  and P.COD_PERSONA = S.COD_SUCURSAL";
			  
        $result_emp = $db->build_results($sql);

		$this->dws['dw_factura']->set_item(0, 'COD_EMPRESA', $result_emp[0]['COD_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'ALIAS', $result_emp[0]['ALIAS']);	
		$this->dws['dw_factura']->set_item(0, 'RUT', $result_emp[0]['RUT']);	
		$this->dws['dw_factura']->set_item(0, 'DIG_VERIF', $result_emp[0]['DIG_VERIF']);	
		$this->dws['dw_factura']->set_item(0, 'NOM_EMPRESA', $result_emp[0]['NOM_EMPRESA']);	
		$this->dws['dw_factura']->set_item(0, 'GIRO', $result_emp[0]['GIRO']);	
		$this->dws['dw_factura']->set_item(0, 'COD_SUCURSAL_FACTURA', $result[0]['COD_SUCURSAL_FACTURA']);	
		$this->dws['dw_factura']->set_item(0, 'DIRECCION_FACTURA', $result_emp[0]['DIRECCION_FACTURA']);	
		$this->dws['dw_factura']->set_item(0, 'COD_PERSONA',$cod_sucursal);			
		$this->dws['dw_factura']->set_item(0, 'MAIL_CARGO_PERSONA', $result_emp[0]['MAIL_CARGO_PERSONA']);
		$this->dws['dw_factura']->set_item(0, 'COD_USUARIO_VENDEDOR1', $cod_usuario_ven1);
		$this->dws['dw_factura']->set_item(0, 'COD_USUARIO_VENDEDOR2', $cod_usuario_ven2);
		$this->dws['dw_factura']->set_item(0, 'COD_USUARIO_VENDEDOR2', $cod_usuario_ven2);
		$this->dws['dw_factura']->set_item(0, 'PORC_VENDEDOR1', $porc_vend1 );
		$this->dws['dw_factura']->set_item(0, 'PORC_VENDEDOR2', $porc_vend2 );
		$this->dws['dw_factura']->set_item(0, 'PORC_DSCTO1', $porc_dscto1 );
		$this->dws['dw_factura']->set_item(0, 'PORC_DSCTO2', $porc_dscto2 );
		$this->dws['dw_factura']->set_item(0, 'INGRESO_USUARIO_DSCTO1', $ing_usuario_dscto1 );
		$this->dws['dw_factura']->set_item(0, 'INGRESO_USUARIO_DSCTO2', $ing_usuario_dscto2 );
		
		if(session::is_set('PRECIO_PRODUCTO_ORIGINAL')){
			$monto_dscto1 = $sum_total * ($porc_dscto1/100);
			$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO1', $monto_dscto1);
			$monto_dscto2 = ($sum_total - $monto_dscto1) * ($porc_dscto2/100);
			$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO2', $monto_dscto2);
			$total_neto = $sum_total - ($monto_dscto1+$monto_dscto2);
			$this->dws['dw_factura']->set_item(0, 'TOTAL_NETO', $total_neto);
			$monto_iva = $total_neto * ($this->get_parametro(1)/100);
			$this->dws['dw_factura']->set_item(0, 'MONTO_IVA', $monto_iva);
			$total_con_iva = $total_neto + $monto_iva;
			$this->dws['dw_factura']->set_item(0, 'TOTAL_CON_IVA', $total_con_iva);
		}else{
			$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO1', $monto_dscto1);
			$this->dws['dw_factura']->set_item(0, 'MONTO_DSCTO2', $monto_dscto2);
			$this->dws['dw_factura']->set_item(0, 'TOTAL_NETO', $total_neto);
			$this->dws['dw_factura']->set_item(0, 'MONTO_IVA', $monto_iva);
			$this->dws['dw_factura']->set_item(0, 'TOTAL_CON_IVA', $total_con_iva);
		}
		
		$this->dws['dw_factura']->set_item(0, 'REFERENCIA', $referencia );

		$this->dws['dw_factura']->controls['COD_SUCURSAL_FACTURA']->retrieve($cod_empresa);
		$this->dws['dw_factura']->controls['COD_PERSONA']->retrieve($cod_empresa);

		// fuerzxa e3l js para que asigne el CC
		$this->js_onload="centro_costo();";
		session::un_set('PRECIO_PRODUCTO_ORIGINAL');
		
		$NRO_FACTURA = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		IF($NRO_FACTURA == ''){
			$this->dws['dw_docs']->set_entrable_dw(false);
		}
	}
	function load_record() {
		parent::load_record();
		
		$sql= "SELECT COD_TIPO_FACTURA
					 ,NOM_TIPO_FACTURA
			   FROM TIPO_FACTURA
			   WHERE COD_TIPO_FACTURA <> 3
			   ORDER BY COD_TIPO_FACTURA";
		$this->dws['dw_factura']->add_control($control = new drop_down_dw('COD_TIPO_FACTURA',$sql,150));
		$control->set_onChange("cambio_tipo_hidden();");
		$this->dws['dw_factura']->set_entrable('COD_TIPO_FACTURA', true);
		
		$this->dws['dw_factura']->set_entrable('GENERA_SALIDA', false);
		$cod_factura = $this->dws['dw_factura']->get_item(0, 'COD_FACTURA');
		$this->dws['dw_factura']->set_item(0, 'D_COD_FACTURA_ENCRIPT',$cod_factura);
		$this->dws['dw_docs']->retrieve($cod_factura);
		
		for($j=0 ; $j < $this->dws['dw_docs']->row_count(); $j++){
			$es_oc = $this->dws['dw_docs']->get_item($j, 'D_ES_OC');
			if($es_oc == 'S')
				$this->dws['dw_docs']->set_item($j, 'D_VALUE_OPTION', 'S');
			else
				$this->dws['dw_docs']->set_item($j, 'D_VALUE_OPTION', 'N');
		}
		
		$NRO_FACTURA = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		IF($NRO_FACTURA == ''){
			$this->dws['dw_docs']->set_entrable_dw(false);
		}
		
		if($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_EMITIDA){
			$this->dws['dw_item_factura']->set_entrable('COD_PRODUCTO'   	, true);
			$this->dws['dw_item_factura']->set_entrable('NOM_PRODUCTO'  	, true);
		}
		else{
			$this->dws['dw_item_factura']->set_entrable('COD_PRODUCTO'   	, false);
			$this->dws['dw_item_factura']->set_entrable('NOM_PRODUCTO'  	, false);
		}
		
		
		$COD_ESTADO_DOC_SII = $this->dws['dw_factura']->get_item(0, 'COD_ESTADO_DOC_SII');
		if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_ENVIADA) {
		
			if($this->tiene_privilegio_opcion(self::K_PUEDE_MODIFICAR_CC)) {
				$this->dws['dw_factura']->set_entrable('COD_CENTRO_COSTO', true);
				
				$sql	= "select 	 COD_CENTRO_COSTO
									,NOM_CENTRO_COSTO
							from 	 CENTRO_COSTO
							order by COD_CENTRO_COSTO";
				$this->dws['dw_factura']->add_control(new drop_down_dw('COD_CENTRO_COSTO',$sql,150));
			}
			else
				$this->dws['dw_factura']->set_entrable('COD_CENTRO_COSTO', false);
				
			$this->dws['dw_item_factura']->add_control(new static_num('CANTIDAD',1));	
		}
		
		$priv = $this->get_privilegio_opcion_usuario(self::K_PUEDE_MODIFICAR_TF, $this->cod_usuario);
		if($priv == 'N')
			$this->dws['dw_factura']->controls['COD_TIPO_FACTURA']->enabled = false;
		
		$cod_tipo_factura = $this->dws['dw_factura']->get_item(0, 'COD_TIPO_FACTURA');	
			
		if($cod_tipo_factura == 4){ // OC BODEGA
			$this->dws['dw_factura']->set_entrable('RUT', false);
			$this->dws['dw_factura']->set_entrable('ALIAS', false);
			$this->dws['dw_factura']->set_entrable('COD_EMPRESA', false);
			$this->dws['dw_factura']->set_entrable('NOM_EMPRESA', false);
			$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA', false);
			$this->dws['dw_factura']->set_entrable('COD_PERSONA', false);
			$this->dws['dw_factura']->set_entrable('COD_FORMA_PAGO', false);
			$this->dws['dw_factura']->set_entrable('NO_TIENE_OC', false);
			unset($this->dws['dw_factura']->controls['NRO_ORDEN_COMPRA']);
			$this->dws['dw_factura']->add_control(new static_text('NRO_ORDEN_COMPRA'));
			$this->dws['dw_item_factura']->set_entrable_dw(false);
			
			$this->dws['dw_item_factura']->b_add_line_visible = false;
			$this->dws['dw_item_factura']->b_del_line_visible = false;
		}else{
			$this->dws['dw_factura']->set_entrable('RUT', true);
			$this->dws['dw_factura']->set_entrable('ALIAS', true);
			$this->dws['dw_factura']->set_entrable('COD_EMPRESA', true);
			$this->dws['dw_factura']->set_entrable('NOM_EMPRESA', true);
			$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA', true);
			$this->dws['dw_factura']->set_entrable('COD_PERSONA', true);
			$this->dws['dw_factura']->set_entrable('COD_FORMA_PAGO', true);
			$this->dws['dw_factura']->set_entrable('NO_TIENE_OC', true);
			unset($this->dws['dw_factura']->controls['NRO_ORDEN_COMPRA']);
			$this->dws['dw_factura']->add_control($control = new edit_text_upper('NRO_ORDEN_COMPRA', 18, 18));
			$control->set_onChange("valida_nro_oc();");
			$this->dws['dw_item_factura']->set_entrable_dw(true);
			
			$this->dws['dw_item_factura']->b_add_line_visible = true;
			$this->dws['dw_item_factura']->b_del_line_visible = true;
		}

		unset($this->dws['dw_factura']->controls['COD_FORMA_PAGO']);
		$sql="SELECT F.COD_FORMA_PAGO
				    ,NOM_FORMA_PAGO
			  FROM FORMA_PAGO F
			  WHERE ES_VIGENTE = 'S'
			  ORDER BY ORDEN";
		$this->dws['dw_factura']->add_control(new drop_down_dw('COD_FORMA_PAGO',$sql,150));
		
		
		$cod_forma_pago		= $this->dws['dw_factura']->get_item(0, 'COD_FORMA_PAGO');
		if ($cod_forma_pago==1 || $cod_forma_pago==19 || $cod_forma_pago==27)
			$this->dws['dw_factura']->controls['NOM_FORMA_PAGO_OTRO']->set_type('text');	
		else
			$this->dws['dw_factura']->controls['NOM_FORMA_PAGO_OTRO']->set_type('hidden');
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql="	SELECT COUNT(*) TIENE_DESCARGA
				  FROM FACTURA_DOCS  FD
				 WHERE FD.ES_OC = 'S'
				AND FD.COD_FACTURA = $cod_factura";
		           
		 $result = $db->build_results($sql);					
		 $tiene_descarga = $result[0]['TIENE_DESCARGA'];
		 
		 if($tiene_descarga == 0){
			 $this->dws['dw_factura']->set_item(0, 'DISPLAY_DESCARGA','none');	
		 }else{
			 $this->dws['dw_factura']->set_item(0, 'DISPLAY_DESCARGA','');	
		 }
		 
		if($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_EMITIDA && $this->modify == true)
			 $this->dws['dw_factura']->set_item(0, 'DISABLE_BTN_GD', '');	
		else
			 $this->dws['dw_factura']->set_item(0, 'DISABLE_BTN_GD', 'disabled');	
		
	}
	function save_record($db) {
		$COD_FACTURA				= $this->get_key();
		$COD_USUARIO_IMPRESION		= $this->dws['dw_factura']->get_item(0, 'COD_USUARIO_IMPRESION');
		$COD_USUARIO				= $this->dws['dw_factura']->get_item(0, 'COD_USUARIO');
		$NRO_FACTURA				= $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		
		$FECHA_FACTURA				= $this->dws['dw_factura']->get_item(0, 'FECHA_FACTURA');
		
		$COD_ESTADO_DOC_SII			= $this->dws['dw_factura']->get_item(0, 'COD_ESTADO_DOC_SII');
		$COD_EMPRESA				= $this->dws['dw_factura']->get_item(0, 'COD_EMPRESA');
		$COD_SUCURSAL_FACTURA		= $this->dws['dw_factura']->get_item(0, 'COD_SUCURSAL_FACTURA');
		$COD_PERSONA				= $this->dws['dw_factura']->get_item(0, 'COD_PERSONA');
		$REFERENCIA					= $this->dws['dw_factura']->get_item(0, 'REFERENCIA');
		$REFERENCIA 				= str_replace("'", "''", $REFERENCIA);
		
		$NRO_ORDEN_COMPRA			= $this->dws['dw_factura']->get_item(0, 'NRO_ORDEN_COMPRA');
		$FECHA_ORDEN_COMPRA_CLIENTE				= $this->dws['dw_factura']->get_item(0, 'FECHA_ORDEN_COMPRA_CLIENTE');
		
		$OBS						= $this->dws['dw_factura']->get_item(0, 'OBS');
		$OBS 						= str_replace("'", "''", $OBS);
		$RETIRADO_POR				= $this->dws['dw_factura']->get_item(0, 'RETIRADO_POR');
		$RUT_RETIRADO_POR			= $this->dws['dw_factura']->get_item(0, 'RUT_RETIRADO_POR');
		$DIG_VERIF_RETIRADO_POR		= $this->dws['dw_factura']->get_item(0, 'DIG_VERIF_RETIRADO_POR');
		$GUIA_TRANSPORTE			= $this->dws['dw_factura']->get_item(0, 'GUIA_TRANSPORTE');
		$PATENTE					= $this->dws['dw_factura']->get_item(0, 'PATENTE');
		$COD_BODEGA					= $this->dws['dw_factura']->get_item(0, 'COD_BODEGA');
		$COD_TIPO_FACTURA			= $this->dws['dw_factura']->get_item(0, 'COD_TIPO_FACTURA_H');
		$COD_DOC					= $this->dws['dw_factura']->get_item(0, 'COD_DOC');
		$MOTIVO_ANULA				= $this->dws['dw_factura']->get_item(0, 'MOTIVO_ANULA');
		$MOTIVO_ANULA 				= str_replace("'", "''", $MOTIVO_ANULA);
		$COD_USUARIO_ANULA			= $this->dws['dw_factura']->get_item(0, 'COD_USUARIO_ANULA');
		
		if (($MOTIVO_ANULA != '') && ($COD_USUARIO_ANULA == ''))  // se anula 
			$COD_USUARIO_ANULA			= $this->cod_usuario;
		else
			$COD_USUARIO_ANULA			= "null";
			
		$COD_USUARIO_VENDEDOR1		= $this->dws['dw_factura']->get_item(0, 'COD_USUARIO_VENDEDOR1');
		$PORC_VENDEDOR1				= $this->dws['dw_factura']->get_item(0, 'PORC_VENDEDOR1');
		$COD_USUARIO_VENDEDOR2		= $this->dws['dw_factura']->get_item(0, 'COD_USUARIO_VENDEDOR2');
		$PORC_VENDEDOR2				=  0; //TODOINOX NO OCUPA ESTE CAMPO $this->dws['dw_factura']->get_item(0, 'PORC_VENDEDOR2');
		$COD_FORMA_PAGO				= $this->dws['dw_factura']->get_item(0, 'COD_FORMA_PAGO');	
		$COD_ORIGEN_VENTA			= $this->dws['dw_factura']->get_item(0, 'COD_ORIGEN_VENTA');

		
		if ($COD_ORIGEN_VENTA == ''){
			$COD_ORIGEN_VENTA = 'null';
		}

		$SUBTOTAL					= $this->dws['dw_factura']->get_item(0, 'SUM_TOTAL');
		$PORC_DSCTO1				= $this->dws['dw_factura']->get_item(0, 'PORC_DSCTO1');
		$PORC_DSCTO2				= $this->dws['dw_factura']->get_item(0, 'PORC_DSCTO2');
		$INGRESO_USUARIO_DSCTO1		= $this->dws['dw_factura']->get_item(0, 'INGRESO_USUARIO_DSCTO1');
		$MONTO_DSCTO1				= $this->dws['dw_factura']->get_item(0, 'MONTO_DSCTO1');
		$INGRESO_USUARIO_DSCTO2		= $this->dws['dw_factura']->get_item(0, 'INGRESO_USUARIO_DSCTO2');
		$MONTO_DSCTO2				= $this->dws['dw_factura']->get_item(0, 'MONTO_DSCTO2');
		$TOTAL_NETO					= $this->dws['dw_factura']->get_item(0, 'TOTAL_NETO');
		
		$PORC_IVA					= $this->dws['dw_factura']->get_item(0, 'PORC_IVA');
		$MONTO_IVA					= $this->dws['dw_factura']->get_item(0, 'MONTO_IVA');
		$TOTAL_CON_IVA				= $this->dws['dw_factura']->get_item(0, 'TOTAL_CON_IVA');
		
		$PORC_FACTURA_PARCIAL		= $this->dws['dw_factura']->get_item(0, 'PORC_FACTURA_PARCIAL');
		$NOM_FORMA_PAGO_OTRO		= $this->dws['dw_factura']->get_item(0, 'NOM_FORMA_PAGO_OTRO');
		$GENERA_SALIDA				= $this->dws['dw_factura']->get_item(0, 'GENERA_SALIDA');
		$CANCELADA					= $this->dws['dw_factura']->get_item(0, 'CANCELADA');
		$COD_CENTRO_COSTO			= $this->dws['dw_factura']->get_item(0, 'COD_CENTRO_COSTO');
		$COD_CENTRO_COSTO			= ($COD_CENTRO_COSTO =='') ? "null" : "'$COD_CENTRO_COSTO'";
		$COD_VENDEDOR_SOFLAND		= $this->dws['dw_factura']->get_item(0, 'COD_VENDEDOR_SOFLAND');
		$NO_TIENE_OC				= $this->dws['dw_factura']->get_item(0, 'NO_TIENE_OC');
		$WS_ORIGEN					= $this->dws['dw_factura']->get_item(0, 'WS_ORIGEN');
		$GENERA_ORDEN_DESPACHO		= $this->dws['dw_factura']->get_item(0, 'GENERA_ORDEN_DESPACHO');
		$COD_USUARIO_GENERA_OD		= $this->cod_usuario;
		$CENTRO_COSTO_CLIENTE		= $this->dws['dw_factura']->get_item(0, 'CENTRO_COSTO_CLIENTE');
		$NO_TIENE_CC_CLIENTE		= $this->dws['dw_factura']->get_item(0, 'NO_TIENE_CC_CLIENTE');
		$ORIGEN_FACTURA				= $this->dws['dw_factura']->get_item(0, 'ORIGEN_FACTURA');
		
		//Validacion especial al momento de consultar las facturas que se crean antes
		if($this->is_new_record()){
			if($NRO_ORDEN_COMPRA <> '' && $WS_ORIGEN <> ''){
				$sql = "SELECT COUNT(*) COUNT
						FROM FACTURA
						WHERE NRO_ORDEN_COMPRA = '$NRO_ORDEN_COMPRA'
						AND COD_EMPRESA = ".$COD_EMPRESA;
		
				$result_valida = $db->build_results($sql);
				
				if($result_valida[0]['COUNT'] <> $this->verifica_registro_bd){
					$k_root_url = session::get('K_ROOT_URL');
					session::set('ALERTA_REGISTRO', $NRO_ORDEN_COMPRA);
					header ('Location:'.$k_root_url."appl/login/presentacion_esp.php");
					return true;
				}
			}
		}
		
		$ORIGEN_FACTURA			= ($ORIGEN_FACTURA =='') ? "null" : "'$ORIGEN_FACTURA'";
		$CENTRO_COSTO_CLIENTE		= ($CENTRO_COSTO_CLIENTE =='') ? "null" : "'$CENTRO_COSTO_CLIENTE'";
		$COD_VENDEDOR_SOFLAND		= ($COD_VENDEDOR_SOFLAND =='') ? "null" : $COD_VENDEDOR_SOFLAND;
		$WS_ORIGEN					= ($WS_ORIGEN =='') ? "null" : $WS_ORIGEN;
		$COD_FACTURA			= ($COD_FACTURA =='') ? "null" : $COD_FACTURA;
		$NRO_FACTURA			= ($NRO_FACTURA =='') ? "null" : $NRO_FACTURA;
		$NRO_ORDEN_COMPRA		= ($NRO_ORDEN_COMPRA =='') ? "null" : "'$NRO_ORDEN_COMPRA'";
		$FECHA_ORDEN_COMPRA_CLIENTE		= $this->str2date($FECHA_ORDEN_COMPRA_CLIENTE);
		
		$OBS					= ($OBS =='') ? "null" : "'$OBS'";
		$RETIRADO_POR			= ($RETIRADO_POR =='') ? "null" : "'$RETIRADO_POR'";
		$RUT_RETIRADO_POR		= ($RUT_RETIRADO_POR =='') ? "null" : $RUT_RETIRADO_POR;
		$DIG_VERIF_RETIRADO_POR	= ($DIG_VERIF_RETIRADO_POR =='') ? "null" : "'$DIG_VERIF_RETIRADO_POR'";
		$GUIA_TRANSPORTE		= ($GUIA_TRANSPORTE =='') ? "null" : "'$GUIA_TRANSPORTE'"; 
		$PATENTE				= ($PATENTE =='') ? "null" : "'$PATENTE'"; 
		$COD_BODEGA				= ($COD_BODEGA =='') ? "null" : $COD_BODEGA; 
		$COD_DOC				= ($COD_DOC =='') ? "null" : $COD_DOC; 
		$COD_USUARIO_VENDEDOR2  = ($COD_USUARIO_VENDEDOR2 =='') ? "null" : $COD_USUARIO_VENDEDOR2;
		$PORC_VENDEDOR2 		= ($PORC_VENDEDOR2 =='') ? "null" : $PORC_VENDEDOR2;
		$MOTIVO_ANULA			= ($MOTIVO_ANULA =='') ? "null" : "'$MOTIVO_ANULA'";
		$INGRESO_USUARIO_DSCTO1 = ($INGRESO_USUARIO_DSCTO1 =='') ? "null" : "'$INGRESO_USUARIO_DSCTO1'";
		$INGRESO_USUARIO_DSCTO2 = ($INGRESO_USUARIO_DSCTO2 =='') ? "null" : "'$INGRESO_USUARIO_DSCTO2'";
		$COD_USUARIO_IMPRESION	= ($COD_USUARIO_IMPRESION =='') ? "null" : $COD_USUARIO_IMPRESION;
		$PORC_FACTURA_PARCIAL	= ($PORC_FACTURA_PARCIAL =='') ? "null" : "$PORC_FACTURA_PARCIAL";
		
		$SUBTOTAL = ($SUBTOTAL == '' ? 0: "$SUBTOTAL");
		$PORC_DSCTO1 = ($PORC_DSCTO1 == '' ? 0: "$PORC_DSCTO1");
		$MONTO_DSCTO1 = ($MONTO_DSCTO1 == '' ? 0: "$MONTO_DSCTO1");
		$PORC_DSCTO2 = ($PORC_DSCTO2 == '' ? 0: "$PORC_DSCTO2");
		$MONTO_DSCTO2 = ($MONTO_DSCTO2 == '' ? 0: "$MONTO_DSCTO2");
		$PORC_IVA = ($PORC_IVA == '' ? 0: "$PORC_IVA");
		$MONTO_IVA = ($MONTO_IVA == '' ? 0: "$MONTO_IVA");
		$TOTAL_CON_IVA = ($TOTAL_CON_IVA == '' ? 0: "$TOTAL_CON_IVA");
		$TOTAL_NETO = ($TOTAL_NETO == '' ? 0: "$TOTAL_NETO");
		
		$COD_FORMA_PAGO = $this->dws['dw_factura']->get_item(0, 'COD_FORMA_PAGO');
		if ($COD_FORMA_PAGO==1){ // forma de pago = OTRO
			$NOM_FORMA_PAGO_OTRO= $this->dws['dw_factura']->get_item(0, 'NOM_FORMA_PAGO_OTRO');
			
		}else{
			$NOM_FORMA_PAGO_OTRO= "";
		}
		$NOM_FORMA_PAGO_OTRO= ($NOM_FORMA_PAGO_OTRO =='') ? "null" : "'$NOM_FORMA_PAGO_OTRO'";
		
		$cod_cotizacion = $this->cod_cotizacion =='' ? "null" : $this->cod_cotizacion; 
		$sp = 'spu_factura';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';					
		
		$param	= "'$operacion'
				,$COD_FACTURA
				,$COD_USUARIO_IMPRESION
				,$COD_USUARIO	
				,$NRO_FACTURA
				,'$FECHA_FACTURA'
				,$COD_ESTADO_DOC_SII					
				,$COD_EMPRESA		
				,$COD_SUCURSAL_FACTURA		
				,$COD_PERSONA				
				,'$REFERENCIA'
				,$NRO_ORDEN_COMPRA
				,$FECHA_ORDEN_COMPRA_CLIENTE			
				,$OBS						
				,$RETIRADO_POR				
				,$RUT_RETIRADO_POR			
				,$DIG_VERIF_RETIRADO_POR		
				,$GUIA_TRANSPORTE			
				,$PATENTE	
				,$COD_BODEGA
				,$COD_TIPO_FACTURA
				,$COD_DOC	
				,$MOTIVO_ANULA
				,$COD_USUARIO_ANULA				
				,$COD_USUARIO_VENDEDOR1
				,$PORC_VENDEDOR1
				,$COD_USUARIO_VENDEDOR2
				,$PORC_VENDEDOR2
				,$COD_FORMA_PAGO
				,$COD_ORIGEN_VENTA
				,$SUBTOTAL
				,$PORC_DSCTO1
				,$INGRESO_USUARIO_DSCTO1
				,$MONTO_DSCTO1
				,$PORC_DSCTO2
				,$INGRESO_USUARIO_DSCTO2
				,$MONTO_DSCTO2
				,$TOTAL_NETO
				,$PORC_IVA
				,$MONTO_IVA
				,$TOTAL_CON_IVA
				,$PORC_FACTURA_PARCIAL
				,$NOM_FORMA_PAGO_OTRO
				,'$GENERA_SALIDA'
				,NULL	/*TIPO_DOC*/
				,'$CANCELADA'
				,$COD_CENTRO_COSTO
				,$COD_VENDEDOR_SOFLAND
				,'$NO_TIENE_OC'
				,$cod_cotizacion
				,$WS_ORIGEN
				,'$GENERA_ORDEN_DESPACHO'
				,$COD_USUARIO_GENERA_OD
				,null
				,null
				,null
				,$CENTRO_COSTO_CLIENTE
				,'$NO_TIENE_CC_CLIENTE'
				,$ORIGEN_FACTURA";
		
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_FACTURA = $db->GET_IDENTITY();
				$this->dws['dw_factura']->set_item(0, 'COD_FACTURA', $COD_FACTURA);
			}
			if (($MOTIVO_ANULA != 'null') && ($COD_USUARIO_ANULA != 'null')){ // se anula 
				$this->f_envia_mail('ANULADA');
			}
			// items

			for ($i=0; $i<$this->dws['dw_item_factura']->row_count(); $i++) 
				$this->dws['dw_item_factura']->set_item($i, 'COD_FACTURA', $COD_FACTURA);
		
			if (!$this->dws['dw_item_factura']->update($db)) return false;

			// cobranza
			for ($i=0; $i<$this->dws['dw_bitacora_factura']->row_count(); $i++) 
				$this->dws['dw_bitacora_factura']->set_item($i, 'COD_FACTURA', $COD_FACTURA);
		
			if (!$this->dws['dw_bitacora_factura']->update($db)) return false;
			
			if (!$this->dws['dw_docs']->update($db, $COD_FACTURA)) return false;
			
			$parametros_sp = "'item_factura','factura',$COD_FACTURA";
			if (!$db->EXECUTE_SP('sp_orden_no_parametricas', $parametros_sp)) return false;		
			
			$parametros_sp = "'RECALCULA',$COD_FACTURA";   
            if (!$db->EXECUTE_SP('spu_factura', $parametros_sp))
                return false;
			
            /**********************************************************************************************/    
            $RUT	= $this->dws['dw_factura']->get_item(0, 'RUT');
            $sql = "SELECT COUNT(*) COUNT
					FROM EMPRESA_SODEXO
					WHERE RUT_SODEXO = $RUT";
			$result = $db->build_results($sql);
            
			if($result[0]['COUNT'] > 0 && $this->dws['dw_referencias']->row_count() == 0 && $NO_TIENE_CC_CLIENTE == 'N'){
				$CENTRO_COSTO_CLIENTE = $this->dws['dw_factura']->get_item(0, 'CENTRO_COSTO_CLIENTE');
				
				$this->dws['dw_referencias']->insert_row();
				$this->dws['dw_referencias']->set_item(0, 'DOC_REFERENCIA', $CENTRO_COSTO_CLIENTE);
				$this->dws['dw_referencias']->set_item(0, 'COD_TIPO_REFERENCIA', 3);
				$this->dws['dw_referencias']->set_item(0, 'FECHA_REFERENCIA', $this->current_date());
			}
			/**********************************************************************************************/    
                
            for ($i=0; $i<$this->dws['dw_referencias']->row_count(); $i++) 
				$this->dws['dw_referencias']->set_item($i, 'COD_FACTURA', $COD_FACTURA);
				
			if (!$this->dws['dw_referencias']->update($db)) return false;     
                
            $COD_GUIA_DESPACHO = $this->dws['dw_factura']->get_item(0, 'NRO_GUIA_DESPACHO_H');//ACA!!!
            $sql = "SELECT dbo.f_fa_cods_guia_despacho(COD_FACTURA) COD_GUIA_DESPACHO_FUNC
            			  ,COD_ESTADO_DOC_SII
					FROM FACTURA
					WHERE COD_FACTURA = $COD_FACTURA";
			$result = $db->build_results($sql);
			
			if($COD_GUIA_DESPACHO <> $result[0]['COD_GUIA_DESPACHO_FUNC'] && $result[0]['COD_ESTADO_DOC_SII'] == 1){
				
				$param = "'INGRESO_GD'
						  ,$COD_FACTURA
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,null
						  ,'$COD_GUIA_DESPACHO'";
				
				if (!$db->EXECUTE_SP('spu_factura', $param))
                	return false;
			}

			return true;
		}
		
		return false;							
	}

	function envia_FA_electronica(){
		
			if (!$this->lock_record())
				return false;
			
			$COD_ESTADO_DOC_SII = $this->dws['dw_factura']->get_item(0, 'COD_ESTADO_DOC_SII');
			$cod_factura = $this->dws['dw_factura']->get_item(0, 'COD_FACTURA');
			
			if($COD_ESTADO_DOC_SII == 1){//Emitida
				/////////// reclacula la FA porsiaca
				$parametros_sp = "'RECALCULA',$cod_factura";   
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$db->EXECUTE_SP('spu_factura', $parametros_sp);
	            /////////
			}
			
			//verifica que las cantidades no sean 0, al encontrar lanza alerta.
			for($i=0 ; $i < $this->dws['dw_item_factura']->row_count() ; $i++){
				$cantidad = $this->dws['dw_item_factura']->get_item($i, 'CANTIDAD');
				
				if($cantidad == 0){
					$this->_load_record();
					$this->alert('Este factura registra item con cantidad 0 \nNo se puede registrar la factura.');
					return false;
				}
			}
			//////////////////////////////////////////////////////////////////
			
			$cod_factura = $this->get_key();	
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$count1= 0;
			
			$sql_valida="SELECT CANTIDAD 
				  		 FROM ITEM_FACTURA
				  		 WHERE COD_FACTURA = $cod_factura";
				  
			$result_valida = $db->build_results($sql_valida);

			for($i = 0 ; $i < count($result_valida) ; $i++){
				if($result_valida[$i] <> 0)
					$count1 = $count1 + 1;
			}
			if($count1 > 18){
				$this->_load_record();
				$this->alert('Se estï¿½ ingresando mï¿½s item que la cantidad permitida, favor contacte a IntegraSystem.');
				return false;
			}	
				
			$this->sepa_decimales	= ',';	//Usar , como separador de decimales
			$this->vacio 			= ' ';	//Usar rellenos de blanco, CAMPO ALFANUMERICO
			$this->llena_cero		= 0;	//Usar rellenos con '0', CAMPO NUMERICO
			$this->separador		= ';';	//Usar ; como separador de campos
			$cod_usuario_impresion = $this->cod_usuario;
			$CMR = 9;
			$cod_impresora_dte = $_POST['wi_impresora_dte'];
			
			if($cod_impresora_dte == 100){
				$emisor_factura = 'SALA VENTA';
			}else{
				
			if ($cod_impresora_dte == '')
				$sql = "SELECT U.NOM_USUARIO EMISOR_FACTURA
						FROM USUARIO U, FACTURA F
						WHERE F.COD_FACTURA = $cod_factura
						  and U.COD_USUARIO = $cod_usuario_impresion";
			else
				$sql = "SELECT NOM_REGLA EMISOR_FACTURA
						FROM IMPRESORA_DTE
						WHERE COD_IMPRESORA_DTE = $cod_impresora_dte";
						
			$result = $db->build_results($sql);
			$emisor_factura = $result[0]['EMISOR_FACTURA'] ;
			}
			
			$db->BEGIN_TRANSACTION();
			$sp = 'spu_factura';
			$param = "'ENVIA_DTE', $cod_factura, $cod_usuario_impresion";
			
			if ($db->EXECUTE_SP($sp, $param)) {
				//$db->COMMIT_TRANSACTION();
				
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				//declrar constante para que el monto con iva del reporte lo transpforme a palabras
				$sql_total = "select TOTAL_CON_IVA from FACTURA where COD_FACTURA = $cod_factura";
				$resul_total = $db->build_results($sql_total);
				$total_con_iva = $resul_total[0]['TOTAL_CON_IVA'] ;
				$total_en_palabras =  Numbers_Words::toWords($total_con_iva,"es"); 
				$total_en_palabras = strtr($total_en_palabras, "ï¿½ï¿½ï¿½ï¿½ï¿½", "aeiou");
				$total_en_palabras = strtoupper($total_en_palabras);
				
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql_dte = "SELECT	F.COD_FACTURA,
									F.NRO_FACTURA,
									F.TIPO_DOC,
									dbo.f_format_date(FECHA_FACTURA,1)FECHA_FACTURA,
									F.COD_USUARIO_IMPRESION,
									'$emisor_factura' EMISOR_FACTURA,
									F.NRO_ORDEN_COMPRA,
									dbo.f_fa_nros_guia_despacho(".$cod_factura.") NRO_GUIAS_DESPACHO,	
									F.REFERENCIA,
									F.NOM_EMPRESA,
									F.GIRO,
									F.RUT,
									F.DIG_VERIF,
									F.DIRECCION,
									dbo.f_emp_get_mail_cargo_persona(F.COD_PERSONA, '[EMAIL]') MAIL_CARGO_PERSONA,
									F.TELEFONO,
									F.FAX,
									F.COD_DOC,
									F.SUBTOTAL,
									F.PORC_DSCTO1,
									F.MONTO_DSCTO1,
									F.PORC_DSCTO2,
									F.MONTO_DSCTO2,
									F.MONTO_DSCTO1 + F.MONTO_DSCTO2 TOTAL_DSCTO,
									F.TOTAL_NETO,
									F.PORC_IVA,
									F.MONTO_IVA,
									F.TOTAL_CON_IVA,
									F.RETIRADO_POR,
									F.RUT_RETIRADO_POR,
									F.DIG_VERIF_RETIRADO_POR,
									COM.NOM_COMUNA,
									CIU.NOM_CIUDAD,
									FP.NOM_FORMA_PAGO,
									FP.COD_PAGO_DTE,
									F.NOM_FORMA_PAGO_OTRO,
									ITF.COD_ITEM_FACTURA,
									ITF.ORDEN,								
									ITF.ITEM,
									ITF.CANTIDAD,
									ITF.COD_PRODUCTO,
									ITF.NOM_PRODUCTO,
									ITF.PRECIO,
									ITF.PRECIO * ITF.CANTIDAD  TOTAL_FA,
									'".$total_en_palabras."' TOTAL_EN_PALABRAS,
									convert(varchar(5), GETDATE(), 8) HORA,
									F.GENERA_SALIDA,
									F.CANCELADA,
									F.OBS,
									F.RETIRADO_POR,
									F.RUT_RETIRADO_POR,
									F.DIG_VERIF_RETIRADO_POR,
									F.GUIA_TRANSPORTE,
									F.PATENTE,
									F.COD_EMPRESA,
									F.WS_ORIGEN
							FROM 	FACTURA F left outer join COMUNA COM on F.COD_COMUNA = COM.COD_COMUNA,
									ITEM_FACTURA ITF, CIUDAD CIU, FORMA_PAGO FP 
							WHERE 	F.COD_FACTURA = ".$cod_factura." 
							AND	ITF.COD_FACTURA = F.COD_FACTURA
							AND	CIU.COD_CIUDAD = F.COD_CIUDAD
							AND	FP.COD_FORMA_PAGO = F.COD_FORMA_PAGO";
				$result_dte = $db->build_results($sql_dte);
				
				
				//CANTIDAD DE ITEM_FACTURA 
				$count = count($result_dte);
				
				// datos de factura
				$NRO_FACTURA		= $result_dte[0]['NRO_FACTURA'] ;		// 1 Numero Factura
				$FECHA_FACTURA		= $result_dte[0]['FECHA_FACTURA'] ;		// 2 Fecha Factura
				//Email - VE: =>En el caso de las Factura y otros documentos, no aplica por lo que se dejan 0;0 
				$TD					= $this->llena_cero;					// 3 Tipo Despacho
				$TT					= $this->llena_cero;					// 4 Tipo Traslado
				//Email - VE: => 
				$PAGO_DTE			= $result_dte[0]['COD_PAGO_DTE'];		// 5 Forma de Pago
				$FV					= $this->vacio;							// 6 Fecha Vencimiento
				$RUT				= $result_dte[0]['RUT'];				
				$DIG_VERIF			= $result_dte[0]['DIG_VERIF'];
				$RUT_EMPRESA		= $RUT.'-'.$DIG_VERIF;					// 7 Rut Empresa
				$NOM_EMPRESA		= $result_dte[0]['NOM_EMPRESA'] ;		// 8 Razol Social_Nombre Empresa
				$GIRO				= $result_dte[0]['GIRO'];				// 9 Giro Empresa
				$DIRECCION			= $result_dte[0]['DIRECCION'];			//10 Direccion empresa
				$MAIL_CARGO_PERSONA	= $result_dte[0]['MAIL_CARGO_PERSONA'];	//11 E-Mail Contacto
				$TELEFONO			= $result_dte[0]['TELEFONO'];			//12 Telefono Empresa
				$REFERENCIA			= $result_dte[0]['REFERENCIA'];			//12 Referencia de la Factura  //datos olvidado por VE.
				$NRO_GUIA_DESPACHO	= $result_dte[0]['NRO_GUIAS_DESPACHO'];	//Solicitado a VE por SP
				$GENERA_SALIDA		= $result_dte[0]['GENERA_SALIDA'];		//Solicitado a VE por SP "DESPACHADO"
				if ($GENERA_SALIDA == 'S'){
					$GENERA_SALIDA = 'DESPACHADO';
				}else{
					$GENERA_SALIDA = '';
				}
				$CANCELADA			= $result_dte[0]['CANCELADA'];			//Solicitado a VE por SP "CANCELADO"
				if ($CANCELADA == 'S'){
					$CANCELADA = 'CANCELADA';
				}else{
					$CANCELADA = '';
				}
				$SUBTOTAL			= number_format($result_dte[0]['SUBTOTAL'], 1, ',', '');	//Solicitado a VE por SP "SUBTOTAL"
				$PORC_DSCTO1		= number_format($result_dte[0]['PORC_DSCTO1'], 1, ',', '');	//Solicitado a VE por SP "PORC_DSCTO1"
				$PORC_DSCTO2		= number_format($result_dte[0]['PORC_DSCTO2'], 1, ',', '');	//Solicitado a VE por SP "PORC_DSCTO2"
				$EMISOR_FACTURA		= $result_dte[0]['EMISOR_FACTURA'];		//Solicitado a VE por SP "EMISOR_FACTURA"
				$NOM_COMUNA			= $result_dte[0]['NOM_COMUNA'];			//13 Comuna Recepcion
				$NOM_CIUDAD			= $result_dte[0]['NOM_CIUDAD'];			//14 Ciudad Recepcion
				$DP					= $result_dte[0]['DIRECCION'];			//15 Direcciï¿½n Postal
				$COP				= $result_dte[0]['NOM_COMUNA'];			//16 Comuna Postal
				$CIP				= $result_dte[0]['NOM_CIUDAD'];			//17 Ciudad Postal

				//OBSERVACION
				$RETIRA_RECINTO		= $result_dte[0]['RETIRADO_POR'];		// Persona que Retira de Recinto
				$RECINTO			= $this->vacio;							// Recinto
				$PATENTE			= $result_dte[0]['PATENTE'];			// Patente de Vehiculo que retira
				$RUT_RETIRADO_POR	= $result_dte[0]['RUT_RETIRADO_POR'];				
				$DIG_VERIF_RETIRADO_POR	= $result_dte[0]['DIG_VERIF_RETIRADO_POR'];
				if($RUT_RETIRADO_POR == ''){
					$RUT_RETIRA = '';
				}else{
					$RUT_RETIRA		= $RUT_RETIRADO_POR.'-'.$DIG_VERIF_RETIRADO_POR; // 27 Rut quien Retira
				}
				
				//DATOS DE TOTALES number_format($result_dte[$i]['TOTAL_FA'], 0, ',', '.');
				$TOTAL_NETO			= number_format($result_dte[0]['TOTAL_NETO'], 1, ',', '');		//18 Monto Neto
				$PORC_IVA			= number_format($result_dte[0]['PORC_IVA'], 1, ',', '');		//19 Tasa IVA
				$MONTO_IVA			= number_format($result_dte[0]['MONTO_IVA'], 1, ',', '');		//20 Monto IVA
				$TOTAL_CON_IVA		= number_format($result_dte[0]['TOTAL_CON_IVA'], 1, ',', '');	//21 Monto Total
				$D1					= 'D1';															//22 Tipo de Mov 1 (Desc/Rec)
				$P1					= '$';															//23 Tipo de valor de Desc/Rec 1
				$MONTO_DSCTO1		= number_format($result_dte[0]['MONTO_DSCTO1'], 1, ',', '');	//24 Valor del Desc/Rec 1
				$D2					= 'D2';															//25 Tipo de Mov 2 (Desc/Rec)
				$P2					= '$';															//26 Tipo de valor de Desc/Rec 2
				$MONTO_DSCTO2		= number_format($result_dte[0]['MONTO_DSCTO2'], 1, ',', '');	//27 Valor del Desc/Rec 2
				$D3					= 'D3';															//28 Tipo de Mov 3 (Desc/Rec)
				$P3					= '$';															//29 Tipo de valor de Desc/Rec 3
				$MONTO_DSCTO3		= '';															//30 Valor del Desc/Rec 3
				$NOM_FORMA_PAGO		= $result_dte[0]['NOM_FORMA_PAGO'];								//Dato Especial forma de pago adicional
				$NRO_ORDEN_COMPRA	= $result_dte[0]['NRO_ORDEN_COMPRA'];							//Numero de Orden Pago
				$NRO_NOTA_VENTA		= $result_dte[0]['COD_DOC'];									//Numero de Nota Venta
				$OBSERVACIONES		= $result_dte[0]['OBS'];										//si la factura tiene notas u observaciones
				$OBSERVACIONES		=  eregi_replace("[\n|\r|\n\r]", ' ', $OBSERVACIONES); //elimina los saltos de linea. entre otros caracteres
				$TOTAL_EN_PALABRAS	= $result_dte[0]['TOTAL_EN_PALABRAS'];							//Total en palabras: Posterior al campo Notas
	
				//GENERA EL NOMBRE DEL ARCHIVO
				if($PORC_IVA != 0){
					$TIPO_FACT = 33;	//FACTURA AFECTA
				}else{
					$TIPO_FACT = 34;	//FACTURA EXENTA
					$PORC_IVA = ''; 
				}
	
				//GENERA EL ALFANUMERICO ALETORIO Y LLENA LA VARIABLE $RES = ALETORIO
				$length = 36;
				$source = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$source .= '1234567890';
				
				if($length>0){
			        $RES = "";
			        $source = str_split($source,1);
			        for($i=1; $i<=$length; $i++){
			            mt_srand((double)microtime() * 1000000);
			            $num	= mt_rand(1,count($source));
			            $RES	.= $source[$num-1];
			        }
				 
			    }			
				
				//GENERA ESPACIOS EN BLANCO
				$space = ' ';
				$i = 0; 
				while($i<=100){
					$space .= ' ';
				$i++;
				}
				
				//GENERA ESPACIOS CON CEROS
				$llena_cero = 0;
				$i = 0; 
				while($i<=100){
					$llena_cero .= 0;
				$i++;
				}
				
				//Asignando espacios en blanco Factura
				//LINEA 3
				$NRO_FACTURA	= substr($NRO_FACTURA.$space, 0, 10);		// 1 Numero Factura
				$FECHA_FACTURA	= substr($FECHA_FACTURA.$space, 0, 10);		// 2 Fecha Factura
				$TD				= substr($TD.$space, 0, 1);					// 3 Tipo Despacho
				$TT				= substr($TT.$space, 0, 1);					// 4 Tipo Traslado
				$PAGO_DTE		= substr($PAGO_DTE.$space, 0, 1);			// 5 Forma de Pago
				$FV				= substr($FV.$space, 0, 10);				// 6 Fecha Vencimiento
				$RUT_EMPRESA	= substr($RUT_EMPRESA.$space, 0, 10);		// 7 Rut Empresa
				$NOM_EMPRESA	= substr($NOM_EMPRESA.$space, 0, 100);		// 8 Razol Social_Nombre Empresa
				$GIRO			= substr($GIRO.$space, 0, 40);				// 9 Giro Empresa
				$DIRECCION		= substr($DIRECCION.$space, 0, 60);			//10 Direccion empresa
				$MAIL_CARGO_PERSONA = substr($MAIL_CARGO_PERSONA.$space, 0, 60);//11 E-Mail Contacto
				$TELEFONO		= substr($TELEFONO.$space, 0, 15);			//12 Telefono Empresa
				$REFERENCIA		= substr($REFERENCIA.$space, 0, 80);
				$NRO_GUIA_DESPACHO	= substr($NRO_GUIA_DESPACHO.$space, 0, 20);//Solicitado a VE por SP
				$GENERA_SALIDA	= substr($GENERA_SALIDA.$space, 0, 30);		//DESPACHADO
				$CANCELADA		= substr($CANCELADA.$space, 0, 30);			//CANCELADO
				$SUBTOTAL		= substr($SUBTOTAL.$space, 0, 18);			//Solicitado a VE por SP "SUBTOTAL"
				$PORC_DSCTO1	= substr($PORC_DSCTO1.$space, 0, 5);		//Solicitado a VE por SP "PORC_DSCTO1"
				$PORC_DSCTO2	= substr($PORC_DSCTO2.$space, 0, 5);		//Solicitado a VE por SP "PORC_DSCTO2"
				$EMISOR_FACTURA	= substr($EMISOR_FACTURA.$space, 0, 50);	//Solicitado a VE por SP "EMISOR_FACTURA"
				//LINEA4
				$NOM_COMUNA		= substr($NOM_COMUNA.$space, 0, 20);		//13 Comuna Recepcion
				$NOM_CIUDAD		= substr($NOM_CIUDAD.$space, 0, 20);		//14 Ciudad Recepcion
				$DP				= substr($DP.$space, 0, 60);				//15 Direcciï¿½n Postal
				$COP			= substr($COP.$space, 0, 20);				//16 Comuna Postal
				$CIP			= substr($CIP.$space, 0, 20);				//17 Ciudad Postal
	
				//Asignando espacios en blanco Totales de Factura
				$TOTAL_NETO		= substr($TOTAL_NETO.$space, 0, 18);		//18 Monto Neto
				$PORC_IVA		= substr($PORC_IVA.$space, 0, 5);			//19 Tasa IVA
				$MONTO_IVA		= substr($MONTO_IVA.$space, 0, 18);			//20 Monto IVA
				$TOTAL_CON_IVA	= substr($TOTAL_CON_IVA.$space, 0, 18);		//21 Monto Total
				$D1				= substr($D1.$space, 0, 1);					//22 Tipo de Mov 1 (Desc/Rec)
				$P1				= substr($P1.$space, 0, 1);					//23 Tipo de valor de Desc/Rec 1
				$MONTO_DSCTO1	= substr($MONTO_DSCTO1.$space, 0, 18);		//24 Valor del Desc/Rec 1
				$D2				= substr($D2.$space, 0, 1);					//25 Tipo de Mov 2 (Desc/Rec)
				$P2				= substr($P2.$space, 0, 1);					//26 Tipo de valor de Desc/Rec 2
				$MONTO_DSCTO2	= substr($MONTO_DSCTO2.$space, 0, 18);		//27 Valor del Desc/Rec 2
				$D3				= substr($D3.$space, 0, 1);					//28 Tipo de Mov 3 (Desc/Rec)
				$P3				= substr($P3.$space, 0, 1);					//29 Tipo de valor de Desc/Rec 3
				$MONTO_DSCTO3	= substr($MONTO_DSCTO3.$space, 0, 18);		//30 Valor del Desc/Rec 3
				$NOM_FORMA_PAGO = substr($NOM_FORMA_PAGO.$space, 0, 80);	//Dato Especial forma de pago adicional
				$NRO_ORDEN_COMPRA= substr($NRO_ORDEN_COMPRA.$space, 0, 20);	//Numero de Orden Pago
				$NRO_NOTA_VENTA = substr($NRO_NOTA_VENTA.$space, 0, 20);	//Numero de Nota Venta
				$OBSERVACIONES = substr($OBSERVACIONES.$space.$space.$space, 0, 250); //si la factura tiene notas u observaciones
				$TOTAL_EN_PALABRAS = substr($TOTAL_EN_PALABRAS.' PESOS.'.$space.$space, 0, 200);	//Total en palabras: Posterior al campo Notas
				//OBSERVACION
				
				$RETIRA_RECINTO	= substr($RETIRA_RECINTO.$space, 0, 30);	// Persona que Retira de Recinto
				$RECINTO		= substr($RECINTO.$space, 0, 30);			// Recinto
				$PATENTE		= substr($PATENTE.$space, 0, 30);			// Patente Vehiculo que retira
				$RUT_RETIRA		= substr($RUT_RETIRA.$space, 0, 18);		// Rut quien retira
				$FECHA_HORA_RETIRO = substr($FECHA_HORA_RETIRO.$space, 0, 20); // Fecha y hora de retiro del Recinto
				
				$name_archivo = $TIPO_FACT."_NPG_".$RES.".SPF";
				$fname = tempnam("/tmp", $name_archivo);
				$handle = fopen($fname,"w");
				//DATOS DE FACTURA A EXPORTAR 
				//linea 1 y 2
				fwrite($handle, "\r\n"); //salto de linea
				fwrite($handle, "\r\n"); //salto de linea
				//linea 3		
				fwrite($handle, ' ');									// 0 space 2
				fwrite($handle, $NRO_FACTURA.$this->separador);			// 1 Numero Factura
				fwrite($handle, $FECHA_FACTURA.$this->separador);		// 2 Fecha Factura
				fwrite($handle, $TD.$this->separador);					// 3 Tipo Despacho
				fwrite($handle, $TT.$this->separador);					// 4 Tipo Traslado
				fwrite($handle, $PAGO_DTE.$this->separador);			// 5 Forma de Pago
				fwrite($handle, $FV.$this->separador);					// 6 Fecha Vencimiento
				fwrite($handle, $RUT_EMPRESA.$this->separador);			// 7 Rut Empresa
				fwrite($handle, $NOM_EMPRESA.$this->separador);			// 8 Razol Social_Nombre Empresa
				fwrite($handle, $GIRO.$this->separador);				// 9 Giro Empresa
				fwrite($handle, $DIRECCION.$this->separador);			//10 Direccion empresa
				//Personalizados Linea 3
				fwrite($handle, $MAIL_CARGO_PERSONA.$this->separador);	//11 E-Mail Contacto 
				fwrite($handle, $TELEFONO.$this->separador);			//12 Telefono Empresa
				fwrite($handle, $REFERENCIA.$this->separador);			//Referencia de la Factura
				fwrite($handle, $NRO_GUIA_DESPACHO.$this->separador);	//Solicitado a VE por SP
				fwrite($handle, $GENERA_SALIDA.$this->separador);		//DESPACHADO Solicitado a VE por SP
				fwrite($handle, $CANCELADA.$this->separador);			//CANCELADO Solicitado a VE por SP
				fwrite($handle, $SUBTOTAL.$this->separador);			//Solicitado a VE por SP "SUBTOTAL"
				fwrite($handle, $PORC_DSCTO1.$this->separador);			//Solicitado a VE por SP "PORC_DSCTO1"
				fwrite($handle, $PORC_DSCTO2.$this->separador);			//Solicitado a VE por SP "PORC_DSCTO2"
				fwrite($handle, $EMISOR_FACTURA.$this->separador);		//Solicitado a VE por SP "EMISOR_FACTURA"
				//fwrite($handle, "\r\n"); //salto de linea
				fwrite($handle, $RETIRA_RECINTO.$this->separador);		// Persona que Retira de Recinto
				fwrite($handle, $RECINTO.$this->separador);				// Recinto
				fwrite($handle, $PATENTE.$this->separador);				// Patente Vehiculo que retira
				fwrite($handle, $RUT_RETIRA.$this->separador);			// Rut quien retira
				fwrite($handle, $FECHA_HORA_RETIRO.$this->separador);	// Fecha y hora de retiro del Recinto
				fwrite($handle, "\r\n"); //salto de linea
				//linea 4
				fwrite($handle, ' ');									// 0 space 2
				fwrite($handle, $NOM_COMUNA.$this->separador);			//13 Comuna Recepcion
				fwrite($handle, $NOM_CIUDAD.$this->separador);			//14 Ciudad Recepcion
				fwrite($handle, $DP.$this->separador);					//15 Direcciï¿½n Postal
				fwrite($handle, $COP.$this->separador);					//16 Comuna Postal
				fwrite($handle, $CIP.$this->separador);					//17 Ciudad Postal
				fwrite($handle, $TOTAL_NETO.$this->separador);			//18 Monto Neto
				fwrite($handle, $PORC_IVA.$this->separador);			//19 Tasa IVA
				fwrite($handle, $MONTO_IVA.$this->separador);			//20 Monto IVA
				fwrite($handle, $TOTAL_CON_IVA.$this->separador);		//21 Monto Total
				fwrite($handle, $D1.$this->separador);					//22 Tipo de Mov 1 (Desc/Rec)
				fwrite($handle, $P1.$this->separador);					//23 Tipo de valor de Desc/Rec 1
				fwrite($handle, $MONTO_DSCTO1.$this->separador);		//24 Valor del Desc/Rec 1
				fwrite($handle, $D2.$this->separador);					//25 Tipo de Mov 2 (Desc/Rec)
				fwrite($handle, $P2.$this->separador);					//26 Tipo de valor de Desc/Rec 2
				fwrite($handle, $MONTO_DSCTO2.$this->separador);		//27 Valor del Desc/Rec 2
				fwrite($handle, $D3.$this->separador);					//28 Tipo de Mov 3 (Desc/Rec)
				fwrite($handle, $P3.$this->separador);					//29 Tipo de valor de Desc/Rec 3			
				fwrite($handle, $MONTO_DSCTO3.$this->separador);		//30 Valor del Desc/Rec 2
				fwrite($handle, $NOM_FORMA_PAGO.$this->separador);		//Dato Especial forma de pago adicional
				fwrite($handle, $NRO_ORDEN_COMPRA.$this->separador);	//Numero de Orden Pago
				fwrite($handle, $NRO_NOTA_VENTA.$this->separador);		//Numero de Nota Venta
				fwrite($handle, $OBSERVACIONES.$this->separador);		//si la factura tiene notas u observaciones
				fwrite($handle, $TOTAL_EN_PALABRAS.$this->separador);	//Total en palabras: Posterior al campo Notas
				fwrite($handle, "\r\n"); //salto de linea
				
				//datos de dw_item_factura linea 5 a 34
				for ($i = 0; $i < 30; $i++){
					if($i < $count){
						fwrite($handle, ' '); //0 space 2
						$ORDEN		= $result_dte[$i]['ORDEN'];	
						$MODELO		= $result_dte[$i]['COD_PRODUCTO'];
						$NOM_PRODUCTO = substr($result_dte[$i]['NOM_PRODUCTO'], 0, 60);
						$CANTIDAD	= number_format($result_dte[$i]['CANTIDAD'], 1, ',', '');
						$P_UNITARIO	= number_format($result_dte[$i]['PRECIO'], 1, ',', '');
						$TOTAL		= number_format($result_dte[$i]['TOTAL_FA'], 1, ',', '');
						$DESCRIPCION= $MODELO; // se repite el modelo
						$CANTIDAD_DETALLE = $CANTIDAD; // se repite el $CANTIDAD
						
						//Asignando espacios en blanco dw_item_factura
						$ORDEN = $ORDEN / 10; //ELIMINA EL CERO
						$ORDEN		= substr($ORDEN.$space, 0, 2);
						$MODELO		= substr($MODELO.$space, 0, 35);
						$NOM_PRODUCTO= substr($NOM_PRODUCTO.$space, 0, 80);
						$CANTIDAD	= substr($CANTIDAD.$space, 0, 18);
						$P_UNITARIO	= substr($P_UNITARIO.$space, 0, 18);
						$TOTAL		= substr($TOTAL.$space, 0, 18);
						$DESCRIPCION= substr($DESCRIPCION.$space, 0, 59);
						$CANTIDAD_DETALLE = substr($CANTIDAD_DETALLE.$space, 0, 18);
	
						//DATOS DE ITEM_FACTURA A EXPORTAR
						fwrite($handle, $ORDEN.$this->separador);		//31 Nï¿½mero de Lï¿½nea
						fwrite($handle, $MODELO.$this->separador);		//32 Cï¿½digo item
						fwrite($handle, $NOM_PRODUCTO.$this->separador);//33 Nombre del Item
						fwrite($handle, $CANTIDAD.$this->separador);	//34 Cantidad
						fwrite($handle, $P_UNITARIO.$this->separador);	//35 Precio Unitario
						fwrite($handle, $TOTAL.$this->separador);		//36 Valor por linea de detalle
						fwrite($handle, $DESCRIPCION.$this->separador);	//37 personalizados Zona Detalles(Modelo ï¿½tem)
						fwrite($handle, $CANTIDAD_DETALLE.$this->separador);	//personalizados Zona Detalles SE REPITE $CANTIDAD
					}
					fwrite($handle, "\r\n");
				}
				
				//LINEA 35 SOLICITU DE V ESPINOIZA FA MINERAS
				$sql_ref = "SELECT	 NRO_ORDEN_COMPRA
									,CONVERT(VARCHAR(10), FECHA_ORDEN_COMPRA_CLIENTE ,103) FECHA_OC
							FROM 	FACTURA 
							WHERE 	COD_FACTURA = $cod_factura";
				
				$result_ref = $db->build_results($sql_ref);
				$NRO_OC_FACTURA	= $result_ref[0]['NRO_ORDEN_COMPRA'];
				$FECHA_REF_OC	= $result_ref[0]['FECHA_OC'];
				
				//($a == $b) && ($c > $b)
				if(($NRO_OC_FACTURA == '') or ($FECHA_REF_OC == '')){
					//no existe OC en factura
					//Linea 36 a 44	Referencia
					$TDR	= $this->llena_cero;
					$FR		= $this->llena_cero;
					$FECHA_R= $this->vacio;
					$CR		= $this->llena_cero;
					$RER	= $this->vacio;
					
					//Asignando espacios en blanco Referencia
					$TDR	= substr($TDR.$space, 0, 3);
					$FR		= substr($FR.$space, 0, 18);
					$FECHA_R= substr($FECHA_R.$space, 0, 10);
					$CR		= substr($CR.$space, 0, 1);
					$RER	= substr($RER.$space, 0, 100);					
					
					fwrite($handle, ' '); //0 space 2
					fwrite($handle, $TDR.$this->separador);			//38 Tipo documento referencia
					fwrite($handle, $FR.$this->separador);			//39 Folio Referencia
					fwrite($handle, $FECHA_R.$this->separador);		//40 Fecha de Referencia
					fwrite($handle, $CR.$this->separador);			//41 Cï¿½digo de Referencia
					fwrite($handle, $RER.$this->separador);			//42 Razï¿½n explï¿½cita de la referencia
				}else{
					$TIPO_COD_REF		= '801';
					$NRO_OC_FACTURA		= $result_ref[0]['NRO_ORDEN_COMPRA'];	
					$FECHA_REF_OC		= $result_ref[0]['FECHA_OC'];
					$CR					= '1';
					$RAZON_REF_OC		= 'ORDEN DE COMPRA';
					
					$TIPO_COD_REF	= substr($TIPO_COD_REF.$space, 0, 3);
					$NRO_OC_FACTURA	= substr($NRO_OC_FACTURA.$space, 0, 18);
					$FECHA_REF_OC	= substr($FECHA_REF_OC.$space, 0, 10);
					$CR				= substr($CR.$space, 0, 1);
					$RAZON_REF_OC	= substr($RAZON_REF_OC.$space, 0, 100);
					
					fwrite($handle, ' '); //0 space 2
					fwrite($handle, $TIPO_COD_REF.$this->separador);			//TIPOCODREF. SOLI 
					fwrite($handle, $NRO_OC_FACTURA.$this->separador);			//FOLIOREF......Folio Referencia
					fwrite($handle, $FECHA_REF_OC.$this->separador);			//FECHA OC Cï¿½digo de Referencia
					fwrite($handle, $CR.$this->separador);						//41 Cï¿½digo de Referencia
					fwrite($handle, $RAZON_REF_OC.$this->separador);			//RAZON  KJNSK... Razï¿½n explï¿½cita de la referencia
				}
				fclose($handle);
				/*
				header("Content-Type: application/x-msexcel; name=\"$name_archivo\"");
				header("Content-Disposition: inline; filename=\"$name_archivo\"");
				$fh=fopen($fname, "rb");
				fpassthru($fh);*/

				$upload = $this->Envia_DTE($name_archivo, $fname);
				$NRO_FACTURA	= trim($NRO_FACTURA);
				if (!$upload) {
					$db->ROLLBACK_TRANSACTION();
					$this->_load_record();
					//$this->alert('No se pudo enviar Fatura Electronica Nï¿½ '.$NRO_FACTURA.', Por favor contacte a IntegraSystem.');
					$this->alert('No se pudo enviar Fatura Electronica , Por favor contacte a IntegraSystem.');
													
				}else{
					if ($PORC_IVA == 0){
						$this->_load_record();
						$this->alert('Gestiï¿½n Realizada con exï¿½to. Factura Exenta Electronica Nï¿½ '.$NRO_FACTURA.'.');
						$db->COMMIT_TRANSACTION();
					}else{
						$this->_load_record();
						$this->alert('Gestiï¿½n Realizada con exï¿½to. Factura Electronica Nï¿½ '.$NRO_FACTURA.'.');
						$db->COMMIT_TRANSACTION();
					}
					
					$GENERA_ORDEN_DESPACHO = $this->dws['dw_factura']->get_item(0, 'GENERA_ORDEN_DESPACHO');
					
					if($GENERA_ORDEN_DESPACHO == 'S'){
						$sp = 'spu_factura';
						$cod_usuario = session::get('COD_USUARIO');
						
						$param = "'GENERA_OD', $cod_factura, $cod_usuario";
						$db->EXECUTE_SP($sp, $param);
					}
					
					/*Una vez enviada correctamente al SII, aqui se verifica si la empresa corresponde
					  a Bodega Biggi, Comercial Biggi o para rental para que se puedan crear las
					  faprov automaticas hacia el sistema cliente
					*/
					if($result_dte[0]['WS_ORIGEN'] == 'BODEGA'){
						//Se valida las OC de las cuales se va a realizar esta nueva implementacion
						if($result_dte[0]['NRO_ORDEN_COMPRA'] > 57130)
							$sistema = 'BODEGA';
						
					}else if($result_dte[0]['WS_ORIGEN'] == 'COMERCIAL'){
						//Se valida las OC de las cuales se va a realizar esta nueva implementacion
						if($result_dte[0]['NRO_ORDEN_COMPRA'] > 184160){
							$sistema = 'COMERCIAL';
						}
					}if($result_dte[0]['WS_ORIGEN'] == 'RENTAL'){
						//Se valida las OC de las cuales se va a realizar esta nueva implementacion
						if($result_dte[0]['NRO_ORDEN_COMPRA'] > 66110)
							$sistema = 'RENTAL';
						
					}
					if($sistema <> ''){
						
						$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
						$sql = "SELECT SISTEMA, URL_WS, USER_WS,PASSWROD_WS  FROM PARAMETRO_WS
								WHERE SISTEMA = '".$result_dte[0]['WS_ORIGEN']."'";
						$result = $db->build_results($sql);
						
						$user_ws		= $result[0]['USER_WS'];
						$passwrod_ws	= $result[0]['PASSWROD_WS'];
						$url_ws			= $result[0]['URL_WS'];
						
						$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
						$result_ws = $biggi->cli_add_faprov($result_dte, $sistema);
						
						/*if($result_ws == 'MSJ_REGISTRO')
							$this->alert('Ya hay un Registro en faprov bodega biggi');
						else if($result_ws == 'NO_REGISTRO_OC')
							$this->alert('No hay OC asociado a esta factura o factura no es para todoinox');
						else if($result_ws == 'NO_IGUAL')
							$this->alert('Tiene diferentes item, cantidades o productos');
						else if($result_ws == 'HECHO')
							$this->alert('registro guardado');*/
					}
				}
				unlink($fname);
			}else{
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
			$this->unlock_record();
		}
}
class print_factura extends print_factura_base {	
	function print_factura($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::print_factura_base($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);
	}			
	///////////FACTURA CON IVA BODEGA BIGGI/////////////////////////////////////////
	function print_con_iva_fa_Bodega_Biggi(&$pdf, $x, $y) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		$count = count($result);
		
		$fecha = $result[0]['FECHA_FACTURA'];		
		// CABECERA		
		$cod_factura = $result[0]['COD_FACTURA'];		
		$nro_factura = $result[0]['NRO_FACTURA'];
		$nom_empresa = $result[0]['NOM_EMPRESA'];
		$rut = number_format($result[0]['RUT'], 0, ',', '.')."-".$result[0]['DIG_VERIF'];
		$oc = $result[0]['NRO_ORDEN_COMPRA'];
		$direccion = $result[0]['DIRECCION'];
		$comuna = $result[0]['NOM_COMUNA'];		
		$ciudad = $result[0]['NOM_CIUDAD'];		
		$giro = $result[0]['GIRO'];
		
		$fono = $result[0]['TELEFONO'];
		$total_en_palabras = $result[0]['TOTAL_EN_PALABRAS'];
		
		$subtotal = number_format($result[0]['SUBTOTAL'], 0, ',', '.');
		$porc_dscto1 = number_format($result[0]['PORC_DSCTO1'], 1, ',', '.');
		$monto_dscto1 = number_format($result[0]['MONTO_DSCTO1'], 0, ',', '.');
		$porc_dscto2 = number_format($result[0]['PORC_DSCTO2'], 1, ',', '.');
		$monto_dscto2 = number_format($result[0]['MONTO_DSCTO2'], 0, ',', '.');
		$total_dscto = number_format($result[0]['TOTAL_DSCTO'], 0, ',', '.');
		$neto = number_format($result[0]['TOTAL_NETO'], 0, ',', '.');
		$porc_iva = number_format($result[0]['PORC_IVA'], 1, ',', '.');
		$monto_iva = number_format($result[0]['MONTO_IVA'], 0, ',', '.');
		$total_con_iva = number_format($result[0]['TOTAL_CON_IVA'], 0, ',', '.');
		$guia_despacho = $result[0]['NRO_GUIAS_DESPACHO'];
		$cond_venta = $result[0]['NOM_FORMA_PAGO'];
		$cond_venta_otro = $result[0]['NOM_FORMA_PAGO_OTRO'];
		$retirado_por = $result[0]['RETIRADO_POR'];
		$GENERA_SALIDA	= $result[0]['GENERA_SALIDA'];
		if ($result[0]['REFERENCIA']=='')
			$REFERENCIA	= '';
		else
			$REFERENCIA	= 'REF: '.substr ($result[0]['REFERENCIA'], 0, 53);

		$sql = "select dbo.f_fa_NV_COMERCIAL(COD_FACTURA) COD_NOTA_VENTA
				from FACTURA
				where COD_FACTURA = $cod_factura";
		$result_NV = $db->build_results($sql);
		$COD_NV		= $result_NV[0]['COD_NOTA_VENTA'];	
		
		$OBS		= $result[0]['OBS'];
		$linea	= '______________________________';
		$CANCELADA	=	$result[0]['CANCELADA']; 

		$retirado_por_rut = number_format($result[0]['RUT_RETIRADO_POR'], 0, ',', '.');
		if ($retirado_por_rut == 0) {
			$retirado_por_rut = '';
		}else {
			$retirado_por_rut = number_format($result[0]['RUT_RETIRADO_POR'], 0, ',', '.')."-".$result[0]['DIG_VERIF_RETIRADO_POR'];
		}
				
		$retira_fecha = $result[0]['HORA'];
		if($cond_venta == 'OTRO')
			 $cond_venta = $cond_venta_otro;		
		
		if(strlen($cond_venta) > 30)
			$cond_venta = substr($cond_venta, 0, 30);

		// DIBUJANDO LA CABECERA	
		$pdf->SetFont('Arial','',11);		
		$pdf->Text($x-11, $y-4, $fecha);
		
		$pdf->SetFont('Arial','',8);		
		$pdf->Text($x+339, $y-40, $nro_factura);
		
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY($x-16, $y+8);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(250, 15,"$nom_empresa");
		
		$pdf->Text($x+350, $y+16, $rut);
		
		$pdf->SetFont('Arial','',11);
		$pdf->Text($x+350, $y+45, $oc);
		
		$pdf->SetXY($x-16, $y+65);
		$pdf->MultiCell(250,10,"$direccion");
		
		$pdf->SetFont('Arial','',10);
		$pdf->Text($x+350, $y+70, $comuna);
		
		$pdf->Text($x-29, $y+98, $ciudad);
		
		$pdf->SetXY($x+126, $y+81);
		$pdf->MultiCell(120, 8,"$giro", 0, 'L');
		
		$pdf->Text($x+350, $y+98, $fono);
		
		$pdf->Text($x+25, $y+115, $guia_despacho);
		
		$pdf->Text($x+375, $y+125, $cond_venta);	
					
		$pdf->SetFont('Arial','B',10);
		$pdf->Text($x, $y+170, "$REFERENCIA");
		
		$pdf->SetFont('Arial','',9);	
		//DIBUJANDO LOS ITEMS DE LA FACTURA	
		for($i=0; $i<$count; $i++){
			$item = $result[$i]['ITEM'];
			$cantidad = $result[$i]['CANTIDAD'];
			$modelo = $result[$i]['COD_PRODUCTO'];
			$detalle = substr ($result[$i]['NOM_PRODUCTO'], 0, 45);	
			$p_unitario = number_format($result[$i]['PRECIO'], 0, ',', '.');
			$total = number_format($result[$i]['TOTAL_FA'], 0, ',', '.');
			// por cada pasada le asigna una nueva posicion	
			$pdf->Text($x-61, $y+188+(15*$i), $item);			
			$pdf->Text($x-31, $y+188+(15*$i), $cantidad);
			$pdf->Text($x+3, $y+188+(15*$i), $modelo);			
			$pdf->SetXY($x+54, $y+185+(15*$i));
			$pdf->Cell(300, 0, "$detalle");
			$pdf->SetXY($x+310, $y+181+(15*$i));
			$pdf->MultiCell(80,7, $p_unitario,0, 'R');		
			$pdf->SetXY($x+390, $y+181+(15*$i));
			$pdf->MultiCell(80,7, $total,0, 'R');							
		}					
									
		// DIBUJANDO TOTALES
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($x+48,$y+455);
		$pdf->MultiCell(270,10,'Son: '.$total_en_palabras.' pesos.');
		
		if($total_dscto <> 0){//tiene dscto
			if(($monto_dscto1 <> 0 && $monto_dscto2 == 0) || ($monto_dscto2 <> 0 && $monto_dscto1 == 0)){//solo tiene un DSCTO 1
				$pdf->SetXY($x+346, $y+490);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+378, $y+490);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				if($monto_dscto1 <> 0){
					$pdf->SetXY($x+343, $y+505);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto1.' % DSCTO  $ ',0, 'R');

					$pdf->SetXY($x+378, $y+505);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');
				}
				else{
					$pdf->SetXY($x+333, $y+505);
					$pdf->SetFont('A4ial','',9);
					$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO: $ ',0, 'R');

					$pdf->SetXY($x+378, $y+505);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');				
				}				
			}else if($monto_dscto1 <> 0 && $monto_dscto2 <> 0){//tiene ambos DSCTO

				$pdf->SetXY($x+346, $y+475);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+378, $y+475);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				$pdf->SetXY($x+340, $y+490);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO1 $ ',0, 'R');

				$pdf->SetXY($x+378, $y+490);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');	
				
				$pdf->SetXY($x+346, $y+505);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO2 $ ',0, 'R');

				$pdf->SetXY($x+378, $y+505);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');
			}
		}

		
		
		$pdf->SetXY($x+346, $y+520);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(80,4, 'TOTAL NETO $ ',0, 'R');
		$pdf->SetXY($x+378, $y+520);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$neto,0, 'R');
		$pdf->SetXY($x+346, $y+535);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(80,4, $porc_iva.' % IVA  $ ',0, 'R');
		$pdf->SetXY($x+378, $y+535);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$monto_iva,0, 'R');
		$pdf->Rect($x+360, $y+544, 120, 2, 'f');
		$pdf->SetXY($x+346, $y+555);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(80,4,'TOTAL  $ ',0, 'R');
		$pdf->SetXY($x+378, $y+555);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$total_con_iva,0, 'R');	


		//DIBUJANDO PERSONA QUE RETIRA PRODUCTOS 
		$pdf->SetFont('Arial','B',11);
		if ($GENERA_SALIDA == 'S'){
			$pdf->Rect($x-53, $y+510, 90, 15, 'f');
			$pdf->Text($x-47, $y+522, 'DESPACHADO');
		}	
		
		if ($CANCELADA == 'S'){
			$pdf->Rect($x-53, $y+550, 90, 14, 'f');
			$pdf->Text($x-47, $y+562, 'CANCELADA');
		}
		
		$pdf->SetFont('Arial','',13);
		$pdf->Text($x-52, $y+543, $COD_NV);
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x-70, $y+481);
		$pdf->MultiCell(380, 8, "$OBS");
		
		$pdf->SetFont('Arial','',9);
		$pdf->Text($x+83, $y+488, $retirado_por);
		$pdf->Text($x+83, $y+508, $retirado_por_rut);
		$pdf->Text($x+249, $y+530, $retira_fecha);
	}
	
///////////FIN FACTURA CON IVA BODEGA BIGGI/////////////////////////////////////////
	function modifica_pdf(&$pdf){
		$pdf->AutoPageBreak=false;		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		$porc_iva = $result[0]['PORC_IVA'];
		
		//USUARIOS
		$USUARIO_IMPRESION = $result[0]['USUARIO_IMPRESION'];
		$ADM = 1;

		//BODEGA BIGGI NO IMPRIME FA SIN IVA
		if($porc_iva != 0){
			if($USUARIO_IMPRESION == $ADM){ //Admin en Bodega Biggi
				$this->print_con_iva_fa_Bodega_Biggi($pdf, 85, 145);
			}else{//otros usuarios
				$this->print_con_iva_fa($pdf, 100, 145);
			}
		} else {
			if($USUARIO_IMPRESION == $ADM){ //Admin en Bodega Biggi
				$this->print_sin_iva_fa_Bodega_Biggi($pdf, 100, 145);
			}else{//otros usuarios
				$this->print_sin_iva_fa($pdf, 79, 155);
			}
		}
	}
	
}
?>