<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_cot_nv.php");
require_once(dirname(__FILE__)."/../empresa/class_dw_help_empresa.php");
require_once("class_dw_item_factura.php");
		
class dw_referencias extends datawindow {
	function dw_referencias() {
		$sql = "SELECT COD_REFERENCIA
				      ,CONVERT(VARCHAR, FECHA_REFERENCIA, 103) FECHA_REFERENCIA
				      ,DOC_REFERENCIA
				      ,COD_TIPO_REFERENCIA
				      ,COD_FACTURA
				FROM REFERENCIA
				WHERE COD_FACTURA = {KEY1}";
		
		parent::datawindow($sql, 'REFERENCIAS', true, true);
		
		// controls
		$this->add_control($control = new edit_date('FECHA_REFERENCIA'));
		$control->set_onChange("valida_fecha_dte(this);");
		$this->add_control( $control = new edit_text('DOC_REFERENCIA', 100, 80));
		$control->set_onChange("valida_referencias(this);");
		
		$sql = "select COD_TIPO_REFERENCIA
						,NOM_TIPO_REFERENCIA
				from TIPO_REFERENCIA
				order by NOM_TIPO_REFERENCIA";
		$this->add_control($control = new drop_down_dw('COD_TIPO_REFERENCIA', $sql, 103));
		$control->set_onChange("valida_referencias(this);");

		// mandatory
		$this->set_mandatory('DOC_REFERENCIA', 'Doc. Referencia');
		$this->set_mandatory('COD_TIPO_REFERENCIA', 'Tipo Referencia');
	}
	function fill_template(&$temp) {
		parent::fill_template($temp);
		
		if($this->b_add_line_visible){
			if ($this->entrable){
				$agregar = '<img src="../../../../commonlib/trunk/images/b_add_line.jpg" onClick="add_line_ref(\''.$this->label_record.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer">';
			}else 
				$agregar = '<img src="../../../../commonlib/trunk/images/b_add_line_d.jpg">';
				
			$temp->setVar("AGREGAR_".strtoupper($this->label_record), $agregar);	
		}
	}
	
	function update($db){
		$sp = 'spu_referencia';
		
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
	
			$COD_REFERENCIA			= $this->get_item($i, 'COD_REFERENCIA');
			$FECHA_REFERENCIA		= $this->str2date($this->get_item($i, 'FECHA_REFERENCIA'));
			$DOC_REFERENCIA			= $this->get_item($i, 'DOC_REFERENCIA');
			$COD_TIPO_REFERENCIA	= $this->get_item($i, 'COD_TIPO_REFERENCIA');
			$COD_FACTURA			= $this->get_item($i, 'COD_FACTURA');
			
			$COD_REFERENCIA			= ($COD_REFERENCIA =='') ? "null" : $COD_REFERENCIA;							
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			else if ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';		
						
			$param = "'$operacion'
					,$COD_REFERENCIA
					,$FECHA_REFERENCIA
					,'$DOC_REFERENCIA'
					,$COD_TIPO_REFERENCIA
					,$COD_FACTURA";
			
			if(!$db->EXECUTE_SP($sp, $param))
				return false;
			else{
				if($statuts == K_ROW_NEW_MODIFIED) {
					$COD_REFERENCIA = $db->GET_IDENTITY();
					$this->set_item($i, 'COD_REFERENCIA', $COD_REFERENCIA);		
				}
			}
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$COD_REFERENCIA = $this->get_item($i, 'COD_REFERENCIA', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_REFERENCIA"))
				return false;
		}	
		return true;
	}
}

class dw_ingreso_pago_fa extends datawindow{
	function dw_ingreso_pago_fa() {
		$sql = "SELECT	IP.COD_INGRESO_PAGO 
						, convert(varchar(20), IP.FECHA_INGRESO_PAGO, 103) FECHA_INGRESO_PAGO
						, TDP.NOM_TIPO_DOC_PAGO
						, DIP.NRO_DOC
						, DIP.MONTO_DOC
						, B.NOM_BANCO
						, IPF.MONTO_ASIGNADO
						,MDA.MONTO_DOC_ASIGNADO
						, EIP.NOM_ESTADO_INGRESO_PAGO
						, convert(varchar(20), DIP.FECHA_DOC, 103) FECHA_DOCTO
						, IP.COD_USUARIO
						, IP.COD_USUARIO_CONFIRMA
				FROM	INGRESO_PAGO_FACTURA IPF, INGRESO_PAGO IP, ESTADO_INGRESO_PAGO EIP , DOC_INGRESO_PAGO DIP LEFT OUTER JOIN BANCO B ON DIP.COD_BANCO = B.COD_BANCO, TIPO_DOC_PAGO TDP
						,MONTO_DOC_ASIGNADO MDA
				WHERE	IPF.COD_INGRESO_PAGO = IP.COD_INGRESO_PAGO
				AND		DIP.COD_INGRESO_PAGO = IP.COD_INGRESO_PAGO
				AND		DIP.COD_TIPO_DOC_PAGO = TDP.COD_TIPO_DOC_PAGO
				AND		IP.COD_ESTADO_INGRESO_PAGO = EIP.COD_ESTADO_INGRESO_PAGO
				AND		IPF.COD_DOC = {KEY1}
				AND 	MDA.COD_DOC_INGRESO_PAGO = DIP.COD_DOC_INGRESO_PAGO
				AND MDA.COD_INGRESO_PAGO_FACTURA = IPF.COD_INGRESO_PAGO_FACTURA";
		
		parent::datawindow($sql, 'INGRESO_PAGO_FACTURA', true, true);
		$sql	= "select 	 COD_USUARIO
							,NOM_USUARIO
					from 	 USUARIO";
		$this->add_control(new drop_down_dw('COD_USUARIO',$sql,150));
		$this->set_entrable('COD_USUARIO', false);
		
		$sql	= "select 	 COD_USUARIO
							,NOM_USUARIO
					from 	 USUARIO";
		$this->add_control(new drop_down_dw('COD_USUARIO_CONFIRMA',$sql,150));
		$this->set_entrable('COD_USUARIO_CONFIRMA', false);	
	
		//$this->add_control(new static_link('COD_INGRESO_PAGO', '../../../../commonlib/trunk/php/link_wi.php?modulo_origen=factura&modulo_destino=ingreso_pago&cod_modulo_destino=[COD_INGRESO_PAGO]&cod_item_menu=2505'));
		$this->add_control(new static_text('COD_INGRESO_PAGO'));
		$this->add_control(new static_text('NRO_DOC'));
		$this->add_control(new static_num('MONTO_DOC'));
		$this->add_control(new static_num('MONTO_DOC_ASIGNADO'));
		$this->add_control(new static_text('NOM_TIPO_DOC_PAGO'));
		$this->add_control(new static_text('NOM_ESTADO_INGRESO_PAGO'));
		$this->add_control(new static_text('NOM_BANCO'));
		
	}
}
class dw_factura extends dw_help_empresa {
	const K_ESTADO_SII_EMITIDA 			= 1;	
	const K_ESTADO_SII_ANULADA			= 4;
	const K_PARAM_PORC_DSCTO_MAX 		= 26;
		
	function dw_factura() {
		$sql = "SELECT	F.COD_FACTURA,
					F.FECHA_REGISTRO,
					F.COD_USUARIO,
					U.NOM_USUARIO,
					F.NRO_FACTURA,
					REFERENCIA_HEM,
					REFERENCIA_HES,
					convert(varchar(20), F.FECHA_FACTURA, 103) FECHA_FACTURA,
					convert(varchar(20), F.FECHA_FACTURA, 103) FECHA_FACTURA_I,
					convert(varchar(20), F.FECHA_FACTURA, 103) FECHA_FACTURA_P,
					convert(varchar(20), F.FECHA_FACTURA, 103) FECHA_FACTURA_C,
					F.COD_ESTADO_DOC_SII,
					EDS.NOM_ESTADO_DOC_SII,
					F.COD_EMPRESA,
					F.COD_SUCURSAL_FACTURA,
					dbo.f_get_direccion('FACTURA', F.COD_FACTURA, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_FACTURA,
					F.COD_PERSONA,
					dbo.f_emp_get_mail_cargo_persona(F.COD_PERSONA,  '[EMAIL] - [NOM_CARGO]') MAIL_CARGO_PERSONA,
					F.REFERENCIA,
					F.NRO_ORDEN_COMPRA,
					convert(varchar(20), F.FECHA_ORDEN_COMPRA_CLIENTE, 103) FECHA_ORDEN_COMPRA_CLIENTE,
					dbo.f_fa_nros_guia_despacho(F.COD_FACTURA) NRO_GUIA_DESPACHO,
					dbo.f_fa_cods_guia_despacho(F.COD_FACTURA) NRO_GUIA_DESPACHO_H,
					NULL DISABLE_BTN_GD,
					F.OBS,
					F.NOM_CIUDAD,
					F.NOM_COMUNA,
					F.NOM_FORMA_PAGO,
					DESDE_4D,
					F.COD_USUARIO_VENDEDOR1,
					F.PORC_VENDEDOR1,
					F.COD_USUARIO_VENDEDOR2,
					F.PORC_VENDEDOR2,
					F.COD_ORIGEN_VENTA,
					F.RETIRADO_POR,
					F.RUT_RETIRADO_POR,
					F.DIG_VERIF_RETIRADO_POR,
					F.GUIA_TRANSPORTE,
					F.PATENTE,
					F.COD_BODEGA,
					F.COD_TIPO_FACTURA,
					F.COD_TIPO_FACTURA COD_TIPO_FACTURA_H,
					F.COD_DOC,
					F.SUBTOTAL SUM_TOTAL,
					F.TOTAL_NETO,
					F.INGRESO_USUARIO_DSCTO1,
					F.MONTO_DSCTO1,
					F.PORC_DSCTO1,
					F.PORC_DSCTO2,
					dbo.f_get_parametro(".self::K_PARAM_PORC_DSCTO_MAX.") PORC_DSCTO_MAX,
					F.INGRESO_USUARIO_DSCTO2,
					F.MONTO_DSCTO2,	
					F.PORC_IVA,
					F.MONTO_IVA,
					F.TOTAL_CON_IVA,
					F.TOTAL_CON_IVA STATIC_TOTAL_CON_IVA,
					dbo.f_fa_total_ingreso_pago(F.COD_FACTURA)SUM_MONTO_H,
					dbo.f_fa_saldo(F.COD_FACTURA) STATIC_SALDO,
					convert(varchar(20), F.FECHA_ANULA, 103) +'  '+ convert(varchar(20), F.FECHA_ANULA, 8) FECHA_ANULA,
					F.MOTIVO_ANULA,
					F.COD_USUARIO_ANULA, 			
					F.RUT RUT_FA,
					F.DIG_VERIF DIG_VERIF_FA,
					F.NOM_EMPRESA NOM_EMPRESA_FA,
					F.GIRO GIRO_FA,
					F.NOM_SUCURSAL,
					E.ALIAS,
					E.RUT,
					E.DIG_VERIF,
					E.NOM_EMPRESA,
					E.GIRO,
					F.DIRECCION,
					F.TELEFONO,
					F.FAX,
					F.NOM_PERSONA,
					F.MAIL,
					F.COD_CARGO,	
					F.TELEFONO,
					F.FAX,
					F.COD_USUARIO_IMPRESION,
					F.COD_FORMA_PAGO,
					F.PORC_FACTURA_PARCIAL,
					F.NOM_FORMA_PAGO_OTRO,
					(select NOM_USUARIO from USUARIO USU where USU.COD_USUARIO = F.COD_USUARIO_VENDEDOR1) VENDEDOR1,
					(select NOM_USUARIO from USUARIO USU where USU.COD_USUARIO = F.COD_USUARIO_VENDEDOR2) VENDEDOR2,
					(select valor from parametro where cod_parametro=29 ) VALOR_FA_H,
					case F.COD_ESTADO_DOC_SII 
						when ".self::K_ESTADO_SII_ANULADA." then '' 
						else 'none'
					end TR_DISPLAY,
					case F.COD_ESTADO_DOC_SII 
						when ".self::K_ESTADO_SII_ANULADA." then 'ANULADA' 
						else ''
					end TITULO_DOC_ANULADA,	
					case
						when f.COD_DOC IS NULL then ''
						else 'none'
					end TD_DISPLAY_ELIMINAR,
					case
						when F.COD_DOC IS not NULL and F.COD_ESTADO_DOC_SII = ".self::K_ESTADO_SII_EMITIDA." then ''
						else 'none'
					end TD_DISPLAY_CANT_POR_FACT,
					GENERA_SALIDA,
					CANCELADA,
					F.TIPO_DOC,
					'' VISIBLE_DTE,
					F.COD_CENTRO_COSTO,
					F.COD_VENDEDOR_SOFLAND
					,F.NO_TIENE_OC
					,F.COD_COTIZACION
					,F.WS_ORIGEN
					,'none' DISPLAY_DESCARGA
					,null D_COD_FACTURA_ENCRIPT
					,F.GENERA_ORDEN_DESPACHO
					,F.COD_USUARIO_GENERA_OD
					,CONVERT(VARCHAR, F.FECHA_GENERA_OD, 103) FECHA_GENERA_OD
					,(SELECT ' / ESTADO OD:'+E.NOM_ESTADO_ORDEN_DESPACHO+' / '+ UO.NOM_USUARIO 
					  FROM ORDEN_DESPACHO O,ESTADO_ORDEN_DESPACHO E,USUARIO UO 					  
					  WHERE O.COD_DOC_ORIGEN = F.COD_FACTURA
					  AND E.COD_ESTADO_ORDEN_DESPACHO = O.COD_ESTADO_ORDEN_DESPACHO
					  AND UO.COD_USUARIO = O.COD_USUARIO)ORDEN_DESPACHO
					,(SELECT O.COD_ORDEN_DESPACHO
					  FROM ORDEN_DESPACHO O,ESTADO_ORDEN_DESPACHO E,USUARIO UO 					  
					  WHERE O.COD_DOC_ORIGEN = F.COD_FACTURA
					  AND E.COD_ESTADO_ORDEN_DESPACHO = O.COD_ESTADO_ORDEN_DESPACHO
					  AND UO.COD_USUARIO = O.COD_USUARIO) COD_ORDEN_DESPACHO
					 ,NO_TIENE_CC_CLIENTE
					 ,CENTRO_COSTO_CLIENTE
					 ,ORIGEN_FACTURA
				FROM  FACTURA F,USUARIO U, EMPRESA E, ESTADO_DOC_SII EDS 
				WHERE F.COD_FACTURA = {KEY1} AND
					  F.COD_USUARIO = U.COD_USUARIO AND
					  E.COD_EMPRESA = F.COD_EMPRESA AND
					  EDS.COD_ESTADO_DOC_SII = F.COD_ESTADO_DOC_SII";
		parent::dw_help_empresa($sql);
		
		$this->add_control(new static_link('COD_ORDEN_DESPACHO', '../../../../commonlib/trunk/php/link_wi.php?modulo_origen=factura&modulo_destino=orden_despacho&cod_modulo_destino=[COD_ORDEN_DESPACHO]&cod_item_menu=1550'));
		$this->add_control(new edit_check_box('GENERA_ORDEN_DESPACHO','S','N',''));
		$this->add_control(new static_text('FECHA_GENERA_OD'));
		$this->add_control(new edit_text_hidden('REFERENCIA_HEM'));
		$this->add_control(new edit_text_hidden('REFERENCIA_HES'));
		
		$this->add_control(new edit_text('COD_FACTURA',10,10, 'hidden', false, true));
		
		$this->add_control(new edit_text('CENTRO_COSTO_CLIENTE',10,10));
		$this->add_control(new edit_check_box('NO_TIENE_CC_CLIENTE', 'S', 'N', 'Sin CC Cliente'));
		
		$this->add_control(new edit_text_hidden('NRO_GUIA_DESPACHO_H'));
		$this->add_control(new static_text('NRO_GUIA_DESPACHO'));
		
		$this->add_control(new edit_nro_doc('NRO_FACTURA','FACTURA'));
		$this->add_control(new static_text('FECHA_FACTURA_I'));
		$this->add_control(new static_text('FECHA_FACTURA_P'));
		$this->add_control(new static_text('FECHA_FACTURA_C'));
		$this->add_control($control = new edit_date('FECHA_FACTURA'));
		$control->set_onChange("change_fecha();");
		
		$this->add_control(new edit_text_upper('NRO_ORDEN_COMPRA', 25, 19));
		$this->add_control($control = new edit_date('FECHA_ORDEN_COMPRA_CLIENTE'));
		$control->set_onChange("valida_fecha_dte(this);");
		
		$sql	= "select 	 COD_TIPO_FACTURA
							,NOM_TIPO_FACTURA
					from 	 TIPO_FACTURA
					order by COD_TIPO_FACTURA";
		$this->add_control(new drop_down_dw('COD_TIPO_FACTURA',$sql,150));
		$this->set_entrable('COD_TIPO_FACTURA', false);
		$this->add_control(new edit_text('COD_TIPO_FACTURA_H',10,10, 'hidden'));		
		$this->add_control(new edit_text('COD_ESTADO_DOC_SII',10,10, 'hidden'));
		$this->add_control(new edit_text('WS_ORIGEN',10,10, 'hidden'));
		$this->add_control(new static_text('NOM_ESTADO_DOC_SII'));
		
		$sql = "select 		COD_ORIGEN_VENTA,
							NOM_ORIGEN_VENTA,
							ORDEN
				from 		ORIGEN_VENTA
				order by 	ORDEN";
		$this->add_control(new drop_down_dw('COD_ORIGEN_VENTA', $sql, 120));
		$sql	= "select 	 COD_CENTRO_COSTO
							,NOM_CENTRO_COSTO
					from 	 CENTRO_COSTO
					order by COD_CENTRO_COSTO";
		$this->add_control(new drop_down_dw('COD_CENTRO_COSTO',$sql,150));
		$sql	= "select 	 COD_VENDEDOR_SOFLAND
							,NOM_VENDEDOR_SOFLAND
					from 	 VENDEDOR_SOFLAND
					order by NOM_VENDEDOR_SOFLAND";
		$this->add_control(new drop_down_dw('COD_VENDEDOR_SOFLAND',$sql,150));
		
		$this->add_control(new static_link('COD_DOC', '../../../../commonlib/trunk/php/link_wi.php?modulo_origen=factura&modulo_destino=nota_venta&cod_modulo_destino=[COD_DOC]&cod_item_menu=1510&current_tab_page=0'));

		$this->add_control(new edit_text_upper('REFERENCIA',120, 100));	
		$this->add_control(new edit_check_box('GENERA_SALIDA','S','N','GENERA SALIDA'));
		$this->add_control(new edit_check_box('CANCELADA','S','N','CANCELADA'));
		
		$sql_forma_pago	= "	select COD_FORMA_PAGO
								,NOM_FORMA_PAGO
								,CANTIDAD_DOC
							from FORMA_PAGO
						   	order by ORDEN";
		$this->add_control($control = new drop_down_dw('COD_FORMA_PAGO', $sql_forma_pago, 160));
		$control->set_onChange("change_forma_pago('', this);");
		$this->add_control(new edit_text('NOM_FORMA_PAGO_OTRO',115, 100));
		$this->add_control(new static_text('NOM_FORMA_PAGO'));
		
		$this->add_control($control = new edit_num_doc_forma_pago('CANTIDAD_DOC_FORMA_PAGO_OTRO'));
		$control->set_onChange("change_forma_pago('OTRO', this);");
		
		
		$this->add_control(new static_num('STATIC_TOTAL_CON_IVA'));
		$this->add_control(new static_num('SUM_MONTO_H'));
		$this->add_control(new static_num('STATIC_SALDO'));
		
		//PARAMETROS FACTURA max cant items
		$this->add_control(new edit_text('VALOR_FA_H',10, 10, 'hidden'));
		
		// usuario anulaci�n
		$sql = "select 	COD_USUARIO
						,NOM_USUARIO
				from USUARIO";
								
		$this->add_control(new drop_down_dw('COD_USUARIO_ANULA',$sql,150));	
		$this->set_entrable('COD_USUARIO_ANULA', false);			
		
		// campos duplicados
		$this->add_control(new static_num('RUT_FA'));
		$this->add_control(new static_text('DIG_VERIF_FA'));
		$this->add_control(new static_text('NOM_EMPRESA_FA'));
		$this->add_control(new static_text('GIRO_FA'));
		
		$this->add_control(new static_text('NOM_SUCURSAL'));
		$this->add_control(new static_text('NOM_PERSONA'));
	
		$this->add_control(new edit_text_upper('RETIRADO_POR',37, 100));
		$this->add_control(new edit_text_upper('GUIA_TRANSPORTE',37, 100));
		$this->add_control(new edit_text_upper('PATENTE',37, 100));
		$this->add_control(new edit_text_multiline('OBS',59,1));
		$this->add_control(new edit_text('PORC_DSCTO_MAX',10, 10, 'hidden'));
		$this->add_control(new edit_rut('RUT_RETIRADO_POR', 10, 10, 'DIG_VERIF_RETIRADO_POR'));
		$this->add_control(new edit_dig_verif('DIG_VERIF_RETIRADO_POR', 'RUT_RETIRADO_POR'));

		$js = $this->controls['COD_EMPRESA']->get_onChange();
		$this->controls['COD_EMPRESA']->set_onChange($js." ajax_load_ref_hidden();");
		
		// asigna los mandatorys
		$this->set_mandatory('COD_ESTADO_DOC_SII', 'Estado');
		$this->set_mandatory('FECHA_FACTURA', 'Fecha de Factura');
		$this->set_mandatory('COD_EMPRESA', 'Empresa');
		$this->set_mandatory('COD_SUCURSAL_FACTURA', 'Sucursal de factura');
		$this->set_mandatory('COD_PERSONA', 'Persona');
		$this->set_mandatory('REFERENCIA', 'Referencia');
		$this->set_mandatory('COD_TIPO_FACTURA', 'Tipo Factura');
		$this->set_mandatory('COD_FORMA_PAGO', 'forma de pago');
		
		$this->add_control(new edit_text('COD_CIUDAD',10, 100, 'hidden'));
		$this->add_control(new edit_text('COD_PAIS',10, 100, 'hidden'));
		$this->add_control(new edit_text('TIPO_DOC',10, 100, 'hidden'));

		$this->add_control($control = new edit_check_box('NO_TIENE_OC','S','N','Sin OC'));
			$control->set_onChange("f_valida_oc()");
		$this->set_first_focus('NRO_ORDEN_COMPRA');

	}

	function fill_record(&$temp, $record) {	
		parent::fill_record($temp, $record);
		
			$COD_DOC = $this->get_item(0, 'COD_DOC');
			$COD_ESTADO_DOC_SII = $this->get_item(0, 'COD_ESTADO_DOC_SII');
			
			if (($COD_DOC != '') or ($COD_ESTADO_DOC_SII != 1))  //la FA viene desde NV, o estado <> emitida
				$temp->setVar('DISABLE_BUTTON', 'style="display:none"');
			else{	
					if ($this->entrable)
						$temp->setVar('DISABLE_BUTTON', '');
					else
						$temp->setVar('DISABLE_BUTTON', 'disabled="disabled"');
			}				
	}
}
class edit_protected extends edit_control {
	var $edit_text;
	var $static_text;
	
	function edit_protected($field, $edit_text, $static_text) {
		parent::edit_control($field);
		$this->edit_text = $edit_text;
		$this->edit_text->forzar_js = true;
		$this->edit_text->set_onChange("change_protected(this);");
		$this->static_text = $static_text;
	}
	function draw_entrable($dato, $record) {
		// input text visible
		$this->edit_text->type = 'text';
		$html = $this->edit_text->draw_entrable($dato, $record);
		
		// static text hidden
		$this->static_text->type = 'hidden';
		$html .= $this->static_text->draw_entrable($dato, $record);
		return $html; 
	}
	function draw_no_entrable($dato, $record) {
		// input text visible
		$this->edit_text->type = 'hidden';
		$html = $this->edit_text->draw_entrable($dato, $record);		// Es correcto que diga draw_entrable, porque se dese aque cree el input text 
		
		// static text hidden
		$this->static_text->type = '';
		$html .= $this->static_text->draw_no_entrable($dato, $record);
		return $html; 
	}
	function get_values_from_POST($record) {
		return $this->edit_text->get_values_from_POST($record);
	}
}
class dw_bitacora_factura extends datawindow {
	function dw_bitacora_factura() {
		$sql = "select BF.COD_BITACORA_FACTURA
						,convert(varchar, BF.FECHA_BITACORA_FACTURA, 103) FECHA_BITACORA_FACTURA
						,substring(convert(varchar, BF.FECHA_BITACORA_FACTURA, 108),1 , 5) HORA_BITACORA_FACTURA
						,BF.COD_USUARIO
						,U1.INI_USUARIO
						,BF.COD_FACTURA
						,BF.COD_ACCION_COBRANZA
						,BF.CONTACTO
						,BF.TELEFONO
						,BF.MAIL
						,BF.GLOSA
						,BF.TIENE_COMPROMISO
						,convert(varchar, BF.FECHA_COMPROMISO, 103) FECHA_COMPROMISO
						,substring(convert(varchar, BF.FECHA_COMPROMISO, 108),1 , 5) HORA_COMPROMISO
						,BF.GLOSA_COMPROMISO
						,BF.COMPROMISO_REALIZADO
						,convert(varchar, BF.FECHA_REALIZADO, 103) FECHA_REALIZADO
						,substring(convert(varchar, BF.FECHA_REALIZADO, 108),1 , 5) HORA_REALIZADO
						,BF.COD_USUARIO_REALIZADO
						,U2.INI_USUARIO INI_USUARIO_REALIZADO
						,CASE
							WHEN DAY(BF.FECHA_BITACORA_FACTURA) = DAY(GETDATE()) THEN 'N'
							ELSE 'S'
						END DISABLE_CURRENT_REC	
				from BITACORA_FACTURA BF left outer join USUARIO U2 on U2.COD_USUARIO = BF.COD_USUARIO_REALIZADO, USUARIO U1 
				where BF.COD_FACTURA = {KEY1}
				  and U1.COD_USUARIO = BF.COD_USUARIO";
		parent::datawindow($sql, 'BITACORA_FACTURA', true, false);
		
		// controls
		$this->add_control(new static_text('FECHA_BITACORA_FACTURA'));
		$this->add_control(new static_text('HORA_BITACORA_FACTURA'));
		$this->add_control(new static_text('INI_USUARIO'));
		$sql = "select COD_ACCION_COBRANZA
						,NOM_ACCION_COBRANZA
				from ACCION_COBRANZA
				order by NOM_ACCION_COBRANZA";
		$this->add_control(new drop_down_dw('COD_ACCION_COBRANZA', $sql, 103));
		$this->add_control(new edit_text_upper('CONTACTO', 20, 100));
		$this->add_control(new edit_text_upper('TELEFONO', 20, 100));
		$this->add_control(new edit_mail('MAIL', 20, 100));
		$this->add_control(new edit_text_multiline('GLOSA', 30, 1));
		$this->add_control($control = new edit_check_box('TIENE_COMPROMISO', 'S', 'N'));
		$control->set_onClick("tiene_compromiso(this);");
		$this->add_control(new edit_protected('FECHA_COMPROMISO', new edit_date('FECHA_COMPROMISO_E'), new static_text('FECHA_COMPROMISO_S')));
		$this->add_control(new edit_protected('HORA_COMPROMISO', new edit_time('HORA_COMPROMISO_E'), new static_text('HORA_COMPROMISO_S')));
		$this->add_control(new edit_protected('GLOSA_COMPROMISO', new edit_text_upper('GLOSA_COMPROMISO_E', 51, 100), new static_text('GLOSA_COMPROMISO_S')));
		$this->add_control($control = new edit_check_box('COMPROMISO_REALIZADO', 'S', 'N'));
		$control->set_onClick("compromiso_realizado(this);");
		$this->add_control(new static_text('FECHA_REALIZADO'));
		$this->add_control(new static_text('HORA_REALIZADO'));
		$this->add_control(new static_text('INI_USUARIO_REALIZADO'));
		
		// mandatory
		$this->set_mandatory('COD_ACCION_COBRANZA', 'Acci�n de cobranza');
		$this->set_mandatory('CONTACTO', 'Contacto');
		
		// first focus
		$this->set_first_focus('COD_ACCION_COBRANZA');

		// protected
		$this->set_protect('COD_ACCION_COBRANZA', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('CONTACTO', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('TELEFONO', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('MAIL', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('GLOSA', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('FECHA_COMPROMISO', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('HORA_COMPROMISO', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('GLOSA_COMPROMISO', "[COMPROMISO_REALIZADO]=='S' || [DISABLE_CURRENT_REC]=='S'");
		$this->set_protect('COMPROMISO_REALIZADO', "[COMPROMISO_REALIZADO]=='S'");
	}
	function insert_row($row=-1) {
		$row = parent::insert_row($row);
		$this->set_item($row, 'TIENE_COMPROMISO', 'S');
		$this->set_item($row, 'FECHA_BITACORA_FACTURA', $this->current_date());
		$this->set_item($row, 'HORA_BITACORA_FACTURA', $this->current_time());

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select INI_USUARIO
				from USUARIO
				where COD_USUARIO = ".$this->cod_usuario;
		$result = $db->build_results($sql);
		$this->set_item($row, 'INI_USUARIO', $result[0]['INI_USUARIO']);
		return $row;
	}
	function update($db)	{
		
		$sp = 'spu_bitacora_factura';
		
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
	
			$cod_bitacora_factura = $this->get_item($i, 'COD_BITACORA_FACTURA');
			$cod_factura = $this->get_item($i, 'COD_FACTURA');
			$cod_accion_cobranza = $this->get_item($i, 'COD_ACCION_COBRANZA');
			$contacto = $this->get_item($i, 'CONTACTO');
			$telefono = $this->get_item($i, 'TELEFONO');
			$mail = $this->get_item($i, 'MAIL');
			$glosa = $this->get_item($i, 'GLOSA');
			$tiene_compromiso = $this->get_item($i, 'TIENE_COMPROMISO');
			$fecha_compromiso = $this->get_item($i, 'FECHA_COMPROMISO');
			$hora_compromiso = $this->get_item($i, 'HORA_COMPROMISO');
			$glosa_compromiso = $this->get_item($i, 'GLOSA_COMPROMISO');
			$compromiso_realizado = $this->get_item($i, 'COMPROMISO_REALIZADO');
			
			$cod_bitacora_factura = ($cod_bitacora_factura =='') ? "null" : "$cod_bitacora_factura";			
			$telefono = ($telefono =='') ? "null" : "'$telefono'";			
			$mail = ($mail =='') ? "null" : "'$mail'";			
			$glosa = ($glosa =='') ? "null" : "'$glosa'";			
			$fecha_compromiso = ($fecha_compromiso =='') ? "null" : $this->str2date($fecha_compromiso, $hora_compromiso.':00');			
			$glosa_compromiso = ($glosa_compromiso =='') ? "null" : "'$glosa_compromiso'";			
			$compromiso_realizado = ($compromiso_realizado =='') ? "'N'" : "'$compromiso_realizado'";			
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			else if ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';		
						
			$param = "'$operacion'
					,$cod_bitacora_factura
					,$this->cod_usuario
					,$cod_factura
					,$cod_accion_cobranza
					,'$contacto'
					,$telefono
					,$mail
					,$glosa
					,'$tiene_compromiso'
					,$fecha_compromiso
					,$glosa_compromiso
					,$compromiso_realizado";
			
			if (!$db->EXECUTE_SP($sp, $param)) 
				return false;
			else {
				if ($statuts == K_ROW_NEW_MODIFIED) {
					$cod_bitacora_factura = $db->GET_IDENTITY();
					$this->set_item($i, 'COD_BITACORA_FACTURA', $cod_bitacora_factura);		
				}
			}
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$cod_bitacora_factura = $this->get_item($i, 'COD_BITACORA_FACTURA', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_bitacora_factura"))
				return false;
		}	
		return true;
	}
}
class wi_factura_base extends w_cot_nv {
	const K_ESTADO_SII_EMITIDA 			= 1;
	const K_ESTADO_SII_IMPRESA			= 2;
	const K_ESTADO_SII_ENVIADA			= 3;
	const K_ESTADO_SII_ANULADA			= 4;
	const K_PARAM_PORC_DSCTO_MAX 		= 26;
	const K_PARAM_CANT_ITEM_FACTURA 	= 29;	
	const K_TIPO_FACTURA_VENTA = 1;
	const K_PUEDE_ANULAR_FACTURA = '992005';
	const K_AUTORIZA_MODIFICA_FECHA = '992015';
	const K_PUEDE_ENVIAR_FA_DTE = '992020';
	const K_PUEDE_MODIFICAR_VENDEDOR = '992030';
	const K_AUTORIZA_VISIBLE_BTN_DTE = '992035';
	const K_AUTORIZA_GENERA_SALIDA = '992040';
	const K_AUTORIZA_MOD_MONTO_DSCTO = '992065';
	
	const K_AUTORIZA_ENVIAR_DTE = '992070';
	const K_AUTORIZA_IMPRIMIR_DTE = '992075';
	const K_AUTORIZA_CONSULTAR_DTE = '992080';
	const K_AUTORIZA_XML_DTE = '992085';
	
	const K_PARAM_RUTEMISOR = 20;
	const K_PARAM_RZNSOC = 6;
	const K_PARAM_GIROEMIS = 21;
	const K_PARAM_DIRORIGEN = 10;
	const K_PARAM_CMNAORIGEN = 70;
	const K_TIPO_DOC = 33;//FA
	const K_ACTV_ECON = 519000;// FORJA, PRENSADO, ESTAMPADO Y LAMINADO DE METAL; INCLUYE PULVIMETALURGIA
	const K_PARAM_HASH = 200;
	
	//VARIABLES DE SESSION "FTP"
	const K_IP_FTP		= 42;		// Direccion del FTP
	const K_USER_FTP	= 43;		//usuario para el FTP
	const K_PASS_FTP	= 44;		// password para el FTP

	const K_AUTORIZA_SOLO_BITACORA = '992025';
	
	var $desde_wo_inf_facturas_por_cobrar = false;
	var $desde_wo_inf_facturas_por_mes = false;
	var $desde_wo_bitacora_factura = false;
	
	function wi_factura_base($cod_item_menu) {		
		// Marca especial cuando viene desde wo_inf_facturas_por_cobrar
		// debe setearse antes del llamado del parent
		if (session::is_set('DESDE_wo_inf_facturas_por_cobrar')) {
			session::un_set('DESDE_wo_inf_facturas_por_cobrar');
			$this->desde_wo_inf_facturas_por_cobrar = true;
		}
		else if (session::is_set('DESDE_wo_inf_facturas_por_mes')) {
			session::un_set('DESDE_wo_inf_facturas_por_mes');
			$this->desde_wo_inf_facturas_por_mes = true;
		}
		else if (session::is_set('DESDE_wo_bitacora_factura')) {
			session::un_set('DESDE_wo_bitacora_factura');
			$this->desde_wo_bitacora_factura = true;
		}
		
		
		parent::w_cot_nv('factura', $cod_item_menu);
		$this->add_FK_delete_cascada('ITEM_FACTURA');	
		$this->add_FK_delete_cascada('GUIA_DESPACHO_FACTURA');
		
		// tab factura
		// DATAWINDOWS FACTURA
		$this->dws['dw_factura'] = new dw_factura();
		$this->add_controls_cot_nv();

		//$this->dws['dw_lista_guia_despacho_fa'] = new dw_lista_guia_despacho_fa();
		
		
		// tab items
		// DATAWINDOWS ITEMS FACTURA
		$this->dws['dw_item_factura'] = new dw_item_factura();
		
		// tab pagos
		// DATAWINDOWS PAGOS
		$this->dws['dw_ingreso_pago_fa'] = new dw_ingreso_pago_fa();
		
		// tab Cobranza
		$this->dws['dw_bitacora_factura'] = new dw_bitacora_factura();
		
		$this->dws['dw_referencias'] = new dw_referencias();
		
		//auditoria Solicitado por IS.
		$this->add_auditoria('COD_ESTADO_DOC_SII');
		$this->add_auditoria('FECHA_FACTURA');
		$this->add_auditoria('COD_USUARIO_VENDEDOR1');
		$this->add_auditoria('PORC_VENDEDOR1');
		$this->add_auditoria('COD_USUARIO_VENDEDOR2');
		$this->add_auditoria('PORC_VENDEDOR2');
		$this->add_auditoria('CANCELADA');
		$this->add_auditoria('GENERA_SALIDA');
		$this->add_auditoria('COD_EMPRESA');
		$this->add_auditoria('COD_SUCURSAL_FACTURA');
		$this->add_auditoria('COD_PERSONA');
		
		$this->add_auditoria('PORC_DSCTO1');
		$this->add_auditoria('MONTO_DSCTO1');
		$this->add_auditoria('PORC_DSCTO2');
		$this->add_auditoria('MONTO_DSCTO2');
		$this->add_auditoria('PORC_IVA');
		$this->add_auditoria('NO_TIENE_OC');
		
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'FECHA_BITACORA_FACTURA');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'COD_USUARIO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'COD_ACCION_COBRANZA');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'CONTACTO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'TELEFONO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'MAIL');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'GLOSA');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'TIENE_COMPROMISO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'FECHA_COMPROMISO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'GLOSA_COMPROMISO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'COMPROMISO_REALIZADO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'FECHA_REALIZADO');
		$this->add_auditoria_relacionada('BITACORA_FACTURA', 'COD_USUARIO_REALIZADO');
	}

	////////////////////
	// funciones auxiliares para cuando se accede a la FA desde_wo_inf_facturas_por_cobrar
	function load_wo() {
		if ($this->desde_wo_inf_facturas_por_cobrar)
			$this->wo = session::get("wo_inf_facturas_por_cobrar");
		else if ($this->desde_wo_inf_facturas_por_mes)
			$this->wo = session::get("wo_inf_facturas_por_mes");
		else if ($this->desde_wo_bitacora_factura)
			$this->wo = session::get("wo_bitacora_factura");
		else
			parent::load_wo();
	}
	function get_url_wo() {
		if ($this->desde_wo_inf_facturas_por_cobrar) 
			return $this->root_url.'appl/inf_facturas_por_cobrar/wo_inf_facturas_por_cobrar.php';
		else if ($this->desde_wo_inf_facturas_por_mes) 
			return $this->root_url.'appl/inf_facturas_por_mes/wo_inf_facturas_por_mes.php';
		else if ($this->desde_wo_bitacora_factura) 
			return $this->root_url.'appl/bitacora_factura/wo_bitacora_factura.php';
		else
			return parent::get_url_wo();
	}
	////////////////////
	
	function new_record() {
			
		$this->b_delete_visible  = false; //cuando es un registro nuevo no muestra el boton eliminar
				
		$this->dws['dw_factura']->insert_row();
		$this->dws['dw_factura']->set_item(0, 'TR_DISPLAY', 'none');
		$this->dws['dw_factura']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_factura']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_factura']->set_item(0, 'COD_ESTADO_DOC_SII', self::K_ESTADO_SII_EMITIDA);
		$this->dws['dw_factura']->set_item(0, 'NOM_ESTADO_DOC_SII', 'EMITIDA');
		$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', 'none');
		
		$this->dws['dw_factura']->set_entrable('COD_TIPO_FACTURA',false);
		$this->dws['dw_factura']->set_entrable('FECHA_FACTURA',	false);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA', self::K_TIPO_FACTURA_VENTA);
		$this->dws['dw_factura']->set_item(0, 'COD_TIPO_FACTURA_H', self::K_TIPO_FACTURA_VENTA);
		
		//$this->dws['dw_factura']->set_item(0, 'COD_FORMA_PAGO', $this->get_orden_min('FORMA_PAGO'));
		$this->dws['dw_factura']->controls['NOM_FORMA_PAGO_OTRO']->set_type('hidden');
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql_fa="select dbo.f_get_parametro(".self::K_PARAM_CANT_ITEM_FACTURA.")	VALOR_FA
						,dbo.f_get_parametro(".self::K_PARAM_PORC_DSCTO_MAX.") PORC_DSCTO_MAX";
		$result = $db->build_results($sql_fa);
		$valor_fa = $result[0]['VALOR_FA'];
		$porc_dscto_max = $result[0]['PORC_DSCTO_MAX'];
		
		//seteo en el htm estas variables
		$this->dws['dw_factura']->set_item(0, 'VALOR_FA_H', $valor_fa);
		$this->dws['dw_factura']->set_item(0, 'TD_DISPLAY_CANT_POR_FACT', 'none');
		$this->dws['dw_factura']->set_item(0, 'PORC_DSCTO_MAX', $porc_dscto_max);
		
		$this->dws['dw_factura']->set_item(0, 'NO_TIENE_OC', 'N');
	}
	function load_record() {
		$cod_factura = $this->get_item_wo($this->current_record, 'COD_FACTURA');
		$this->dws['dw_factura']->retrieve($cod_factura);
		$cod_empresa = $this->dws['dw_factura']->get_item(0, 'COD_EMPRESA');
		$this->dws['dw_factura']->controls['COD_SUCURSAL_FACTURA']->retrieve($cod_empresa);
		$this->dws['dw_factura']->controls['COD_PERSONA']->retrieve($cod_empresa);	
		$this->dws['dw_item_factura']->retrieve($cod_factura);
		$this->dws['dw_referencias']->retrieve($cod_factura);
		
		$COD_ESTADO_DOC_SII = $this->dws['dw_factura']->get_item(0, 'COD_ESTADO_DOC_SII');
		
		$this->b_print_visible 	 = true;
		$this->b_no_save_visible = true;
		$this->b_save_visible 	 = true;
		$this->b_modify_visible	 = true;
		$this->b_delete_visible  = true;
		
		$this->dws['dw_factura']->set_entrable('NRO_ORDEN_COMPRA'      	 , true);
		$this->dws['dw_factura']->set_entrable('FECHA_ORDEN_COMPRA_CLIENTE'      	 , true);
		
		$this->dws['dw_factura']->set_entrable('FECHA_FACTURA'			 ,false);
		$this->dws['dw_factura']->set_entrable('REFERENCIA'				 , true);
		$this->dws['dw_factura']->set_entrable('RETIRADO_POR'			 , true);
		$this->dws['dw_factura']->set_entrable('GUIA_TRANSPORTE'		 , true);
		$this->dws['dw_factura']->set_entrable('PATENTE'				 , true);
		$this->dws['dw_factura']->set_entrable('OBS'					 , true);
		$this->dws['dw_factura']->set_entrable('RUT_RETIRADO_POR'		 , true);
		$this->dws['dw_factura']->set_entrable('DIG_VERIF_RETIRADO_POR'	 , true);
		
		$this->dws['dw_factura']->set_entrable('NOM_EMPRESA'			, true);
		$this->dws['dw_factura']->set_entrable('ALIAS'					, true);
		$this->dws['dw_factura']->set_entrable('COD_EMPRESA'			, true);
		$this->dws['dw_factura']->set_entrable('RUT'					, true);
		$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA'	, true);
		$this->dws['dw_factura']->set_entrable('COD_PERSONA'			, true);
		
		// aqui se dejan no modificables los datos del tab items
		$this->dws['dw_item_factura']->set_entrable_dw(true);
	
		if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_EMITIDA) {
			/////////// reclacula la FA porsiaca
			$parametros_sp = "'RECALCULA',$cod_factura";   
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$db->EXECUTE_SP('spu_factura', $parametros_sp);
            /////////
			
			unset($this->dws['dw_factura']->controls['COD_ESTADO_DOC_SII']);
			$this->dws['dw_factura']->add_control(new edit_text('COD_ESTADO_DOC_SII',10,10, 'hidden'));
			$this->dws['dw_factura']->controls['NOM_ESTADO_DOC_SII']->type = '';
			
			if($this->tiene_privilegio_opcion(self::K_PUEDE_ENVIAR_FA_DTE)){
				$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', '');
			}else{
				$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', 'none');
			}
			
			$COD_DOC = $this->dws['dw_factura']->get_item(0, 'COD_DOC');
			if ($COD_DOC  != '') {	
				$this->dws['dw_factura']->set_entrable('NRO_ORDEN_COMPRA'   	, false);
				$this->dws['dw_factura']->set_entrable('FECHA_ORDEN_COMPRA_CLIENTE'   	, false);
				$this->dws['dw_factura']->set_entrable('RUT'					, false);
				$this->dws['dw_factura']->set_entrable('ALIAS'					, false);
				$this->dws['dw_factura']->set_entrable('COD_EMPRESA'			, false);
				$this->dws['dw_factura']->set_entrable('NOM_EMPRESA'			, false);
				$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA'   , false);
				$this->dws['dw_factura']->set_entrable('COD_PERSONA'			, false);
				
				// aqui se dejan no modificables los datos del tab items
				$this->dws['dw_item_factura']->set_entrable('ORDEN'      		, false);
				$this->dws['dw_item_factura']->set_entrable('ITEM'      		, false);
				$this->dws['dw_item_factura']->set_entrable('COD_PRODUCTO'   	, false);
				$this->dws['dw_item_factura']->set_entrable('NOM_PRODUCTO'  	, false);
				
				// Es una FA desde NV por 
				if ($this->dws['dw_item_factura']->row_count()==1) {
					$cod_producto = $this->dws['dw_item_factura']->get_item(0, 'COD_PRODUCTO');
					$nom_producto = $this->dws['dw_item_factura']->get_item(0, 'NOM_PRODUCTO');
					if ($cod_producto=='TE' && $nom_producto=='__ANTICIPO__') {
						$this->dws['dw_item_factura']->set_item(0, 'COD_PRODUCTO', '');
						$this->dws['dw_item_factura']->set_item(0, 'NOM_PRODUCTO', '');
						$this->dws['dw_item_factura']->set_item(0, 'CANTIDAD_POR_FACTURAR', 1);
						$this->dws['dw_item_factura']->set_entrable('COD_PRODUCTO', true);
						$this->dws['dw_item_factura']->controls['COD_PRODUCTO']->set_onChange("change_item_factura_anticipo(this);");
						$this->dws['dw_item_factura']->set_entrable('NOM_PRODUCTO', true);
						$this->dws['dw_item_factura']->controls['NOM_PRODUCTO']->set_readonly(true);
					}
				}
			}
		}
		else if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_IMPRESA) {
			/* VMC, Solicitado por SP 09-06-2010
			 * Solo se puede anular hasta fin de mes.
			 * Y se agrega un perfil de autorizaci�n de quienes pueden anular FA
			 */
			$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', 'none');

			if($this->tiene_privilegio_opcion(self::K_PUEDE_ANULAR_FACTURA)){
				$fecha_actual = $this->current_date();
				
				$fecha_factura = $this->dws['dw_factura']->get_item(0, 'FECHA_FACTURA');
				$date1 = explode('/', $fecha_actual);			
				$date2 = explode('/', $fecha_factura);
				$mes_anterior = $date1[1] - 1;
				$ano_anterior = $date1[2];
				if ($mes_anterior==0) {
					$mes_anterior = 12;
					$ano_anterior = $ano_anterior - 1;
				} 
				if (($date2[1] == $date1[1] && $date2[2] == $date1[2]) ||	// mismo mes y mismo a�o
				    ($mes_anterior==$date2[1] && $ano_anterior==$date2[2] && $date1[0] <= 5)) {
					
					$sql = "select 	COD_ESTADO_DOC_SII
									,NOM_ESTADO_DOC_SII
							from ESTADO_DOC_SII
							where COD_ESTADO_DOC_SII = ".self::K_ESTADO_SII_IMPRESA." or
									COD_ESTADO_DOC_SII = ".self::K_ESTADO_SII_ANULADA."
							order by COD_ESTADO_DOC_SII";
							
					unset($this->dws['dw_factura']->controls['COD_ESTADO_DOC_SII']);
					$this->dws['dw_factura']->add_control($control = new drop_down_dw('COD_ESTADO_DOC_SII',$sql,150));	
					$control->set_onChange("mostrarOcultar_Anula(this);");
					$this->dws['dw_factura']->controls['NOM_ESTADO_DOC_SII']->type = 'hidden';
					$this->dws['dw_factura']->add_control(new edit_text_upper('MOTIVO_ANULA',110, 100));
				 }
			}else {
				unset($this->dws['dw_factura']->controls['COD_ESTADO_DOC_SII']);
				$this->dws['dw_factura']->add_control(new edit_text('COD_ESTADO_DOC_SII',10,10, 'hidden'));
				$this->dws['dw_factura']->controls['NOM_ESTADO_DOC_SII']->type = '';
			}

			$this->dws['dw_factura']->set_entrable('NRO_ORDEN_COMPRA'        , false);
			$this->dws['dw_factura']->set_entrable('FECHA_ORDEN_COMPRA_CLIENTE'        , false);
			$this->dws['dw_factura']->set_entrable('NRO_FACTURA'		     , true);
			
			$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODIFICA_FECHA, $this->cod_usuario);
			if ($priv=='E') {
				$this->dws['dw_factura']->set_entrable('FECHA_FACTURA'		 , true);
			}
			else {
				$this->dws['dw_factura']->set_entrable('FECHA_FACTURA'		 , false);
			}
			$this->dws['dw_factura']->set_entrable('REFERENCIA'				 , false);
			$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_GENERA_SALIDA, $this->cod_usuario);
			if ($priv=='E') {
				$this->dws['dw_factura']->set_entrable('GENERA_SALIDA'		, true);
			}
			else {
				$this->dws['dw_factura']->set_entrable('GENERA_SALIDA'		, false);
			}
			
			$this->dws['dw_factura']->set_entrable('CANCELADA'				 , false);
			if($this->tiene_privilegio_opcion(self::K_PUEDE_MODIFICAR_VENDEDOR))
				$this->dws['dw_factura']->set_entrable('COD_USUARIO_VENDEDOR1', true);
			else
				$this->dws['dw_factura']->set_entrable('COD_USUARIO_VENDEDOR1', false);
			
			//aqui se deja no entrable los datos de vendedor y origen de la venta
			$this->dws['dw_factura']->set_entrable('PORC_VENDEDOR1'			 , false);
			$this->dws['dw_factura']->set_entrable('COD_USUARIO_VENDEDOR2'	 , false);
			$this->dws['dw_factura']->set_entrable('PORC_VENDEDOR2'			 , false);
			$this->dws['dw_factura']->set_entrable('COD_ORIGEN_VENTA'		 , false);
			
			$this->dws['dw_factura']->set_entrable('RETIRADO_POR'			 , false);
			$this->dws['dw_factura']->set_entrable('GUIA_TRANSPORTE'		 , false);
			$this->dws['dw_factura']->set_entrable('PATENTE'				 , false);
			$this->dws['dw_factura']->set_entrable('OBS'					 , false);
			$this->dws['dw_factura']->set_entrable('RUT_RETIRADO_POR'		 , false);
			$this->dws['dw_factura']->set_entrable('DIG_VERIF_RETIRADO_POR'  , false);
			
			$this->dws['dw_factura']->set_entrable('NOM_EMPRESA'			, false);
			$this->dws['dw_factura']->set_entrable('ALIAS'				    , false);
			$this->dws['dw_factura']->set_entrable('COD_EMPRESA'			, false);
			$this->dws['dw_factura']->set_entrable('RUT'					, false);
			$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA'   , false);
			$this->dws['dw_factura']->set_entrable('COD_PERSONA'			, false);

			//aqui se dejan no ingresable los datos de forma de pago y los totales del tab item_factura 
			$this->dws['dw_factura']->set_entrable('COD_FORMA_PAGO'			 , false);
			$this->dws['dw_factura']->set_entrable('NOM_FORMA_PAGO_OTRO'	 , false);
			$this->dws['dw_factura']->set_entrable('PORC_DSCTO1'			 , false);
			$this->dws['dw_factura']->set_entrable('MONTO_DSCTO1'			 , false);
			$this->dws['dw_factura']->set_entrable('PORC_DSCTO2'			 , false);
			$this->dws['dw_factura']->set_entrable('MONTO_DSCTO2'			 , false);
			$this->dws['dw_factura']->set_entrable('PORC_IVA'				 , false);
		
			
			// aqui se dejan no modificables los datos del tab items
			$this->dws['dw_item_factura']->set_entrable_dw(false);
			
			$this->b_delete_visible  = false;
				
			}else if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_ENVIADA) {
			//SI USUARIO TIENE PRIVILEGIOS DE ENVIAR POR SEGUNDA VES LA FA-ELECTRONICA
			if($this->tiene_privilegio_opcion(self::K_AUTORIZA_VISIBLE_BTN_DTE)){
				$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', '');
			}else{
				$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', 'none');
			}
			
			$this->b_print_visible  = false;

			unset($this->dws['dw_factura']->controls['COD_ESTADO_DOC_SII']);
			$this->dws['dw_factura']->add_control(new edit_text('COD_ESTADO_DOC_SII',10,10, 'hidden'));
			$this->dws['dw_factura']->controls['NOM_ESTADO_DOC_SII']->type = '';

			$this->dws['dw_factura']->set_entrable('NRO_ORDEN_COMPRA'        , false);
			$this->dws['dw_factura']->set_entrable('FECHA_ORDEN_COMPRA_CLIENTE'        , false);
			$this->dws['dw_factura']->set_entrable('NRO_FACTURA'		     , true);
			
			$this->dws['dw_factura']->set_entrable('FECHA_FACTURA'		 , false);
			
			$this->dws['dw_factura']->set_entrable('REFERENCIA'				 , false);
			
			$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_GENERA_SALIDA, $this->cod_usuario);
			if ($priv=='E') {
				$this->dws['dw_factura']->set_entrable('GENERA_SALIDA'		, true);
			}
			else {
				$this->dws['dw_factura']->set_entrable('GENERA_SALIDA'		, false);
			}
			
			$this->dws['dw_factura']->set_entrable('CANCELADA'				 , false);
			if($this->tiene_privilegio_opcion(self::K_PUEDE_MODIFICAR_VENDEDOR))
				$this->dws['dw_factura']->set_entrable('COD_USUARIO_VENDEDOR1', true);
			else
				$this->dws['dw_factura']->set_entrable('COD_USUARIO_VENDEDOR1', false);
			
			//aqui se deja no entrable los datos de vendedor y origen de la venta
			$this->dws['dw_factura']->set_entrable('PORC_VENDEDOR1'			 , false);
			$this->dws['dw_factura']->set_entrable('COD_USUARIO_VENDEDOR2'	 , false);
			$this->dws['dw_factura']->set_entrable('PORC_VENDEDOR2'			 , false);
			$this->dws['dw_factura']->set_entrable('COD_ORIGEN_VENTA'		 , false);
			
			$this->dws['dw_factura']->set_entrable('RETIRADO_POR'			 , false);
			$this->dws['dw_factura']->set_entrable('GUIA_TRANSPORTE'		 , false);
			$this->dws['dw_factura']->set_entrable('PATENTE'				 , false);
			$this->dws['dw_factura']->set_entrable('OBS'					 , false);
			$this->dws['dw_factura']->set_entrable('RUT_RETIRADO_POR'		 , false);
			$this->dws['dw_factura']->set_entrable('DIG_VERIF_RETIRADO_POR'  , false);
			
			$this->dws['dw_factura']->set_entrable('NOM_EMPRESA'			, false);
			$this->dws['dw_factura']->set_entrable('ALIAS'				    , false);
			$this->dws['dw_factura']->set_entrable('COD_EMPRESA'			, false);
			$this->dws['dw_factura']->set_entrable('RUT'					, false);
			$this->dws['dw_factura']->set_entrable('COD_SUCURSAL_FACTURA'   , false);
			$this->dws['dw_factura']->set_entrable('COD_PERSONA'			, false);

			//aqui se dejan no ingresable los datos de forma de pago y los totales del tab item_factura 
			$this->dws['dw_factura']->set_entrable('COD_FORMA_PAGO'			 , false);
			$this->dws['dw_factura']->set_entrable('NOM_FORMA_PAGO_OTRO'	 , false);
			$this->dws['dw_factura']->set_entrable('PORC_DSCTO1'			 , false);
			$this->dws['dw_factura']->set_entrable('MONTO_DSCTO1'			 , false);
			$this->dws['dw_factura']->set_entrable('PORC_DSCTO2'			 , false);
			$this->dws['dw_factura']->set_entrable('MONTO_DSCTO2'			 , false);
			$this->dws['dw_factura']->set_entrable('PORC_IVA'				 , false);
		
			
			// aqui se dejan no modificables los datos del tab items
			$this->dws['dw_item_factura']->set_entrable_dw(false);
			
			$this->b_delete_visible  = false;
				
		}
		else if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_ANULADA) {
			$this->b_print_visible 	 = false;
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible  = false;
			$this->b_delete_visible  = false;
			$this->dws['dw_factura']->set_item(0, 'VISIBLE_DTE', 'none');
		}
		
		//campos duplicados
		if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_EMITIDA){ // estado = emitida
			
			$giro = $this->dws['dw_factura']->get_item(0, 'GIRO');
			
			$this->dws['dw_factura']->controls['RUT']->type = 'text';
			$this->dws['dw_factura']->controls['RUT_FA']->type = 'hidden';
			
			$this->dws['dw_factura']->controls['DIG_VERIF']->type = 'text';
			$this->dws['dw_factura']->controls['DIG_VERIF_FA']->type = 'hidden';
			
			$this->dws['dw_factura']->controls['NOM_EMPRESA']->type = 'text';
			$this->dws['dw_factura']->controls['NOM_EMPRESA_FA']->type = 'hidden';
			
			$this->dws['dw_factura']->controls['GIRO']->type = '';
			$this->dws['dw_factura']->controls['GIRO_FA']->type = 'hidden';
			
			$this->dws['dw_factura']->set_visible('COD_SUCURSAL_FACTURA', true);
			$this->dws['dw_factura']->controls['NOM_SUCURSAL']->type = 'hidden';
			
			$this->dws['dw_factura']->set_visible('COD_PERSONA', true);
			$this->dws['dw_factura']->controls['NOM_PERSONA']->type = 'hidden';
			
		}else{
			$this->dws['dw_factura']->controls['RUT']->type = 'hidden';
			$this->dws['dw_factura']->controls['RUT_FA']->type = '';

			$this->dws['dw_factura']->controls['DIG_VERIF']->type = 'hidden';
			$this->dws['dw_factura']->controls['DIG_VERIF_FA']->type = '';	

			$this->dws['dw_factura']->controls['NOM_EMPRESA']->type = 'hidden';
			$this->dws['dw_factura']->controls['NOM_EMPRESA_FA']->type = '';	
			
			$this->dws['dw_factura']->controls['GIRO']->type = 'hidden';
			$this->dws['dw_factura']->controls['GIRO_FA']->type = '';	
			
			$this->dws['dw_factura']->set_visible('COD_SUCURSAL_FACTURA', false);
			$this->dws['dw_factura']->controls['NOM_SUCURSAL']->type = '';	
			
			$this->dws['dw_factura']->set_visible('COD_PERSONA', false);
			$this->dws['dw_factura']->controls['NOM_PERSONA']->type = '';	
		
		}
		
		$cod_forma_pago		= $this->dws['dw_factura']->get_item(0, 'COD_FORMA_PAGO');
		if ($cod_forma_pago==1){
			$this->dws['dw_factura']->controls['NOM_FORMA_PAGO_OTRO']->set_type('text');
		}	
		else{
			$this->dws['dw_factura']->controls['NOM_FORMA_PAGO_OTRO']->set_type('hidden');
		}
		$this->dws['dw_ingreso_pago_fa']->retrieve($cod_factura);		
		$this->dws['dw_bitacora_factura']->retrieve($cod_factura);		

		//////////////////////////////////////////
		// Si tiene acceso solo bitacora se deshabilita lo demas
	   	$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_SOLO_BITACORA, $this->cod_usuario);	// acceso bitacora

	   	if ($priv=='E')	{	// tiene acceso solo a bitacora
			$this->dws['dw_factura']->set_entrable_dw(false);
			//$this->dws['dw_lista_guia_despacho_fa']->set_entrable_dw(false);
			$this->dws['dw_item_factura']->set_entrable_dw(false);
			$this->dws['dw_ingreso_pago_fa']->set_entrable_dw(false);
			$this->b_delete_visible = false;
	   	}
	   	
	   	if(!$this->is_new_record()){
	   		$this->dws['dw_factura']->set_visible('COD_FORMA_PAGO', true);
	   		$this->dws['dw_factura']->set_visible('NOM_FORMA_PAGO', false);	
	   	}
	   	
	   	$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MOD_MONTO_DSCTO, $this->cod_usuario);
	   	if ($priv=='E'){
	   		$this->dws['dw_factura']->controls['MONTO_DSCTO1']->readonly = false;
	   		$this->dws['dw_factura']->controls['MONTO_DSCTO2']->readonly = false;
	   	}else{
	   		$this->dws['dw_factura']->controls['MONTO_DSCTO1']->readonly = true;
	   		$this->dws['dw_factura']->controls['MONTO_DSCTO2']->readonly = true;
	   	}
	}
	
	 
	function goto_record($record) {
		if (!session::is_set("cant_fa_a_hacer")) 
			parent::goto_record($record);
		else {
			$cant_fa_a_hacer = session::get("cant_fa_a_hacer");
			session::un_set("cant_fa_a_hacer");
			$this->current_record = $record;
			$this->load_record();
			$this->modify_record();
			if ($cant_fa_a_hacer > 1)
				$this->alert('Se har�n '.$cant_fa_a_hacer.' Facturas de esta nota de venta.');
		}
	}
 	
	
	
	
	function get_key() {
		return $this->dws['dw_factura']->get_item(0, 'COD_FACTURA');
	}
	function get_key_para_ruta_menu() {
		return $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
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
		$PORC_VENDEDOR2				= $this->dws['dw_factura']->get_item(0, 'PORC_VENDEDOR2');
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
		$COD_VENDEDOR_SOFLAND		= $this->dws['dw_factura']->get_item(0, 'COD_VENDEDOR_SOFLAND');
		$WS_ORIGEN					= $this->dws['dw_factura']->get_item(0, 'WS_ORIGEN');
		
		$WS_ORIGEN			= ($WS_ORIGEN =='') ? "null" : "'$WS_ORIGEN'";
		$COD_CENTRO_COSTO			= ($COD_CENTRO_COSTO =='') ? "null" : "'$COD_CENTRO_COSTO'";
		$COD_VENDEDOR_SOFLAND		= ($COD_VENDEDOR_SOFLAND =='') ? "null" : $COD_VENDEDOR_SOFLAND;
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
				,$WS_ORIGEN";

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
			
			$parametros_sp = "'item_factura','factura',$COD_FACTURA";
			if (!$db->EXECUTE_SP('sp_orden_no_parametricas', $parametros_sp)) return false;		

			$parametros_sp = "'RECALCULA',$COD_FACTURA";   
            if (!$db->EXECUTE_SP('spu_factura', $parametros_sp))
                return false;
   
			return true;
		}
		
		return false;							
	}
	function print_record() {
		if (!$this->lock_record())
			return false;
		$cod_factura = $this->get_key();
		$cod_tipo_doc_sii = 1;
		$cod_doc_excenta_sii = 5;
		$cod_usuario_impresion = $this->cod_usuario;
		$nro_factura = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		$iva = $this->dws['dw_factura']->get_item(0, 'PORC_IVA');
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		if($nro_factura == ''){
			if($iva != 0){
				//IMPRESION DE DOCUMENTO FACTURA NORMAL
				$sql = "select dbo.f_get_nro_doc_sii ($cod_tipo_doc_sii , $cod_usuario_impresion) NRO_FACTURA";
				$result = $db->build_results($sql);
				$nro_factura = $result[0]['NRO_FACTURA'];
			}else if($iva == 0){
				//IMPRESION DE DOCUMENTO FACTURA EXCENTA
				$sql = "select dbo.f_get_nro_doc_sii ($cod_doc_excenta_sii , $cod_usuario_impresion) NRO_FACTURA";
				$result = $db->build_results($sql);
				$nro_factura = $result[0]['NRO_FACTURA'];
			}
		}

		//declrar constante para que el monto con iva del reporte lo transpforme a palabras
		$sql = "select TOTAL_CON_IVA from FACTURA where COD_FACTURA = $cod_factura";
		$resultado = $db->build_results($sql);
		$total_con_iva = $resultado [0] ['TOTAL_CON_IVA'] ;
		$total_en_palabras =  Numbers_Words::toWords($total_con_iva,"es"); 
		$total_en_palabras = strtr($total_en_palabras, "�����", "aeiou");
	
		if ($nro_factura == -1){
			$this->redraw();		
			$this->dws['dw_factura']->message("Sr(a). Usuario: Ud. no tiene documentos asignados, para imprimir la Factura.");	
			return false;

		}	
		else{
			//se buscan ingresos de pago de la NV (si existe) que esten en estado emitida
			$tipo_doc = $this->dws['dw_factura']->get_item(0, 'TIPO_DOC');
			if($tipo_doc == 'NOTA_VENTA' || $tipo_doc == 'GUIA_DESPACHO'){
				$cod_doc = $this->dws['dw_factura']->get_item(0, 'COD_DOC');
				$sql = "select count(*) COUNT
						from ingreso_pago_factura ipf, ingreso_pago ip
						where tipo_doc = 'NOTA_VENTA' 
							and cod_doc = $cod_doc
							and ip.cod_ingreso_pago = ipf.cod_ingreso_pago 
							and ip.cod_estado_ingreso_pago = ".self::K_ESTADO_SII_EMITIDA;
							
				$resultado = $db->build_results($sql);
				$count = $resultado [0] ['COUNT'] ;	
				if($count > 0){
					$this->redraw();		
					$this->dws['dw_factura']->message("Sr(a). Usuario: Antes de imprimir la factura debe autorizar los ingresos de pago que estan en estado emitida.");	
					return false;
				}				
			}
			
			$db->BEGIN_TRANSACTION();
			$sp = 'spu_factura';
			$param = "'PRINT', $cod_factura, $cod_usuario_impresion";

			if ($db->EXECUTE_SP($sp, $param)) {
				$estado_sii_impresa = self::K_ESTADO_SII_IMPRESA; 
				$cod_estado_doc = $this->dws['dw_factura']->get_item(0, 'COD_ESTADO_DOC_SII');
				if ($cod_estado_doc != $estado_sii_impresa){//es la 1era vez que se imprime la Factura					
						
					$sql =	"SELECT NRO_FACTURA
									,PORC_IVA
							FROM 	FACTURA
							WHERE 	COD_FACTURA = ".$cod_factura;
					$result = $db->build_results($sql);
					$porc_iva = $result[0]['PORC_IVA'];
					$nro_factura = $result[0]['NRO_FACTURA'];
					$this->redraw();
						if($porc_iva ==0)
							$this->dws['dw_factura']->message("Sr(a). Usuario: Se imprimir� la Factura Exenta N�".$nro_factura);
						else
							$this->dws['dw_factura']->message("Sr(a). Usuario: Se imprimir� la Factura N�".$nro_factura);
					$this->f_envia_mail('IMPRESO');
				}
							
				$db->COMMIT_TRANSACTION();
				$sql = "exec spdw_factura_print $cod_factura, 'PRINT', $cod_usuario_impresion, '$total_en_palabras'";
				// reporte
				$labels = array();
				$labels['strCOD_FACTURA'] = $cod_factura;					
				$file_name = $this->find_file('factura', 'factura.xml');					
				$rpt = new print_factura($sql, $file_name, $labels, "Factura ".$cod_factura.".pdf", 0);
				$this->_load_record();
				$this->b_delete_visible  = false;
				return true;
			}
			else {
				$db->ROLLBACK_TRANSACTION();
				return false;
			}			
		}
		$this->unlock_record();
	}

		function Envia_DTE($name_archivo, $fname){
			//SOLO para el CHAITEN
		/*if (K_SERVER <> "192.168.2.26")
				return false;*/
		//if (K_SERVER <> "192.168.2.40")
		//		return false;
			
			
			
			$cod_factura = $this->get_key();
			$cod_tipo_doc_sii = 1;
			$cod_doc_excenta_sii = 5;
			$cod_usuario_impresion = $this->cod_usuario;
			//$nro_factura = $this->dws['dw_factura']->get_item(0, 'NRO_FACTURA');
		
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql_ftp =	"select dbo.f_get_parametro(".self::K_IP_FTP.") DIRECCION_FTP
								,dbo.f_get_parametro(".self::K_USER_FTP.")	USER_FTP
								,dbo.f_get_parametro(".self::K_PASS_FTP.")	PASS_FTP";

			$result_ftp = $db->build_results($sql_ftp);
			
			$file_name_ftp = (dirname(__FILE__)."/../../ftp_dte.php");
			if (file_exists($file_name_ftp)){ 
			require_once($file_name_ftp);
				$K_DIRECCION_FTP	= K_DIRECCION_FTP;	//Ip FTP
				$K_USUARIO_FTP		= K_USUARIO_FTP;		//Usuario FTP
				$K_PASSWORD_FTP		= K_PASSWORD_FTP;		//Password FTP
				$K_PORT 			= 21; 		// PUERTO DEL FTP
			}else{
				$K_DIRECCION_FTP	= $result_ftp[0]['DIRECCION_FTP'] ;	//Ip FTP
				$K_USUARIO_FTP		= $result_ftp[0]['USER_FTP'] ;		//Usuario FTP
				$K_PASSWORD_FTP		= $result_ftp[0]['PASS_FTP'] ;		//Password FTP
				$K_PORT 			= 21; 		// PUERTO DEL FTP
			}

			// establecer una conexi�n b�sica
			$conn_id = ftp_connect($K_DIRECCION_FTP);
			if ($conn_id===false)
				return false; 
			
			// iniciar una sesi�n con nombre de usuario y contrase�a
			$login_result = ftp_login($conn_id, $K_USUARIO_FTP, $K_PASSWORD_FTP);
			if($login_result === false)
				return false;

			ftp_pasv ($conn_id, true) ;
			// subir un archivo
			//$upload = ftp_put($conn_id, $name_archivo, $fname, FTP_BINARY);  
			if(!(ftp_put($conn_id, $name_archivo, $fname, FTP_BINARY)))
				return false;

			// cerrar la conexi�n ftp 
			ftp_close($conn_id);
			
			return true;
		}

		function envia_FA_electronica(){
			if (!$this->lock_record())
				return false;

			$COD_ESTADO_DOC_SII = $this->dws['dw_factura']->get_item(0, 'COD_ESTADO_DOC_SII');
			
			if($COD_ESTADO_DOC_SII == 1){//Emitida
				/////////// reclacula la FA porsiaca
				$parametros_sp = "'RECALCULA',$cod_factura";   
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$db->EXECUTE_SP('spu_factura', $parametros_sp);
	            /////////
			}	
				
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
				$this->alert('Se est� ingresando m�s item que la cantidad permitida, favor contacte a IntegraSystem.');
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
				$db->COMMIT_TRANSACTION();
				
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				//declrar constante para que el monto con iva del reporte lo transpforme a palabras
				$sql_total = "select TOTAL_CON_IVA from FACTURA where COD_FACTURA = $cod_factura";
				$resul_total = $db->build_results($sql_total);
				$total_con_iva = $resul_total[0]['TOTAL_CON_IVA'] ;
				$total_en_palabras =  Numbers_Words::toWords($total_con_iva,"es"); 
				$total_en_palabras = strtr($total_en_palabras, "�����", "aeiou");
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
									F.OBS,
									F.CANCELADA
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
				$DP					= $result_dte[0]['DIRECCION'];			//15 Direcci�n Postal
				$COP				= $result_dte[0]['NOM_COMUNA'];			//16 Comuna Postal
				$CIP				= $result_dte[0]['NOM_CIUDAD'];			//17 Ciudad Postal
				
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
				$DP				= substr($DP.$space, 0, 60);				//15 Direcci�n Postal
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
				fwrite($handle, "\r\n"); //salto de linea
				
				//linea 4
				fwrite($handle, ' ');									// 0 space 2
				fwrite($handle, $NOM_COMUNA.$this->separador);			//13 Comuna Recepcion
				fwrite($handle, $NOM_CIUDAD.$this->separador);			//14 Ciudad Recepcion
				fwrite($handle, $DP.$this->separador);					//15 Direcci�n Postal
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
						fwrite($handle, $ORDEN.$this->separador);		//31 N�mero de L�nea
						fwrite($handle, $MODELO.$this->separador);		//32 C�digo item
						fwrite($handle, $NOM_PRODUCTO.$this->separador);//33 Nombre del Item
						fwrite($handle, $CANTIDAD.$this->separador);	//34 Cantidad
						fwrite($handle, $P_UNITARIO.$this->separador);	//35 Precio Unitario
						fwrite($handle, $TOTAL.$this->separador);		//36 Valor por linea de detalle
						fwrite($handle, $DESCRIPCION.$this->separador);	//37 personalizados Zona Detalles(Modelo �tem)
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
					fwrite($handle, $CR.$this->separador);			//41 C�digo de Referencia
					fwrite($handle, $RER.$this->separador);			//42 Raz�n expl�cita de la referencia
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
					fwrite($handle, $FECHA_REF_OC.$this->separador);			//FECHA OC C�digo de Referencia
					fwrite($handle, $CR.$this->separador);						//41 C�digo de Referencia
					fwrite($handle, $RAZON_REF_OC.$this->separador);			//RAZON  KJNSK... Raz�n expl�cita de la referencia
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
					$this->_load_record();
					$this->alert('No se pudo enviar Fatura Electronica N� '.$NRO_FACTURA.', Por favor contacte a IntegraSystem.');								
				}else{
					if ($PORC_IVA == 0){
						$this->_load_record();
						$this->alert('Gesti�n Realizada con ex�to. Factura Exenta Electronica N� '.$NRO_FACTURA.'.');
					}else{
						$this->_load_record();
						$this->alert('Gesti�n Realizada con ex�to. Factura Electronica N� '.$NRO_FACTURA.'.');
					}								
				}
				unlink($fname);
			}else{
				$db->ROLLBACK_TRANSACTION();
				return false;
			}
			$this->unlock_record();
		}
			
		function f_envia_mail($estado_factura){
	 		$cod_factura = $this->get_key();
	 		$remitente = $this->nom_usuario;
	        $cod_remitente = $this->cod_usuario;
	
        	$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        	$sql = "SELECT NRO_FACTURA from FACTURA where COD_FACTURA = $cod_factura";
        	$result = $db->build_results($sql);
        	$nro_factura = $result[0]['NRO_FACTURA'];		
			
	        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
	        // obtiene el mail de quien creo la tarea y manda el mail
	        $sql_remitente = "SELECT MAIL from USUARIO where COD_USUARIO =".$cod_remitente;
	        $result_remitente = $db->build_results($sql_remitente);
	        $mail_remitente = $result_remitente[0]['MAIL'];
			
	        // Mail destinatarios
	        $para_admin1 = 'mulloa@integrasystem.cl';
	        $para_admin2 = 'mulloa@integrasystem.cl';
	        /*
	        $para_admin1 = 'mherrera@integrasystem.cl';
	        $para_admin2 = 'imeza@integrasystem.cl';
			*/
	        
	        if($estado_factura == 'IMPRESO')
			{
				$asunto = 'Impresion de Factura N� '.$nro_factura;
		        $mensaje = 'Se ha <b>IMPRESO</b> la <b>FACTURA N� '.$nro_factura.'</b> por el usuario <b><i>'.$remitente.'<i><b>';  
			 }
		  	
		 	if($estado_factura == 'ANULADA')
			{
		        $asunto = 'Anulacion de la Factura N� '.$nro_factura;
		        $mensaje = 'Se ha <b>ANULADO</b> la <b>FACTURA N� '.$nro_factura.'</b> por el usuario <b><i>'.$remitente.'<i><b>';
			}
			
		  	$cabeceras  = 'MIME-Version: 1.0' . "\n";
	        $cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\n";
	        $cabeceras .= 'From: '.$mail_remitente. "\n";
	        // se comenta el envio de mail por q ya no es necesario => Vmelo. 
	        // mail($para_admin1, $asunto, $mensaje, $cabeceras);
	        // mail($para_admin2, $asunto, $mensaje, $cabeceras);
	 		return 0;
   		}
		function habilitar(&$temp, $habilita){
			parent::habilitar($temp, $habilita);
			if (!$this->is_new_record()) {
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$COD_FACTURA				= $this->get_key();
				$sql = "select COUNT(*)count from ORDEN_DESPACHO where COD_DOC_ORIGEN = $COD_FACTURA";
				$result = $db->build_results($sql);
				
				if($result[0]['count'] > 0)
					$this->habilita_boton($temp, 'print_od', true);
			}	
				
		}
		function habilita_boton(&$temp, $boton, $habilita){
			parent::habilita_boton($temp, $boton, $habilita);
			
			if ($boton=='print_od') {
				if ($habilita)
					$temp->setVar("WI_PRINT_OD", '<input name="print_od" id="print_od" src="../../images_appl/b_print_od.jpg" type="image" '.
												'onMouseDown="MM_swapImage(\'print_od\',\'\',\'../../images_appl/b_print_od_click.jpg\',1)" '.
												'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
												'onMouseOver="MM_swapImage(\'print_od\',\'\',\'../../images_appl/b_print_od_over.jpg\',1)" '.
												'onClick=""'.
												'/>');
			}
			if($boton == 'enviar_dte'){
				if($habilita){
					$control = '<input name="b_enviar_dte" id="b_enviar_dte" src="../../images_appl/b_enviar_dte.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_enviar_dte\',\'\',\'../../images_appl/b_enviar_dte_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_enviar_dte\',\'\',\'../../images_appl/b_enviar_dte_over.jpg\',1)" 
								 onClick="var vl_tab = document.getElementById(\'wi_current_tab_page\'); if (TabbedPanels1 && vl_tab) vl_tab.value =TabbedPanels1.getCurrentTabIndex();
										 if (document.getElementById(\'b_save\')) {
											 if (validate_save()) {
											 		document.getElementById(\'wi_hidden\').value = \'save_enviar_dte\';
											 		document.getElementById(\'b_save\').click();
											 		return true;
											 	}
											 	else
											 		return false;
										 }
									 	 else
									 	 		return true;"/>';
				}else{
					$control = '<img src="../../images_appl/b_enviar_dte_d.jpg">';
				}
				
				$temp->setVar("WSWAP_ENVIA_DTE", $control);
			}
			if($boton == 'consultar_dte'){
				if($habilita){
					$control = '<input name="b_consultar_dte" id="b_consultar_dte" src="../../images_appl/b_consultar_dte.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_consultar_dte\',\'\',\'../../images_appl/b_consultar_dte_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_consultar_dte\',\'\',\'../../images_appl/b_consultar_dte_over.jpg\',1)"
								 onClick="return true;"/>';
				}else{
					$control = '<img src="../../images_appl/b_consultar_dte_d.jpg">';
				}
				
				$temp->setVar("WSWAP_CONSULTAR_DTE", $control);
			}
			if($boton == 'imprimir_dte'){
				if($habilita){
					$ruta_over = "'../../images_appl/b_reimprime_dte_over.jpg'";
					$ruta_out = "'../../images_appl/b_reimprime_dte.jpg'";
					$ruta_click = "'../../images_appl/b_reimprime_dte_click.jpg'";
					$control =  '<input name="b_imprimir_dte" id="b_imprimir_dte" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
					   			'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../images_appl/b_reimprime_dte.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
					   			'onClick="return dlg_print_dte();" />';
				
				}else{
					$control = '<img src="../../images_appl/b_reimprime_dte_d.jpg">';
				}
				
				$temp->setVar("WSWAP_IMPRIMIR_DTE", $control);
			}
			if($boton == 'reenviar_dte'){
				if($habilita){
					$control = '<input name="b_reenviar_dte" id="b_reenviar_dte" src="../../images_appl/b_reenviar.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_reenviar_dte\',\'\',\'../../images_appl/b_reenviar_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_reenviar_dte\',\'\',\'../../images_appl/b_reenviar_over.jpg\',1)"
								 onClick="return true;"/>';
				}else{
					$control = '<img src="../../images_appl/b_reenviar_d.jpg">';
				}
				
				$temp->setVar("WSWAP_REENVIAR_DTE", $control);
			}
			if($boton == 'xml_dte'){
				if($habilita){
					$control = '<input name="b_xml_dte" id="b_xml_dte" src="../../images_appl/b_xml_dte.jpg" type="image" '.
								 'onMouseDown="MM_swapImage(\'b_xml_dte\',\'\',\'../../images_appl/b_xml_dte_click.jpg\',1)" '.
								 'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
								 'onMouseOver="MM_swapImage(\'b_xml_dte\',\'\',\'../../images_appl/b_xml_dte_over.jpg\',1)"
								 onClick="return true;"/>';
				}else{
					$control = '<img src="../../images_appl/b_xml_dte_d.jpg">';
				}
				
				$temp->setVar("WSWAP_XML_DTE", $control);
			}
		}
		
	function navegacion(&$temp){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		parent::navegacion($temp);
		
		$cod_factura = $this->get_key();
		if($cod_factura <> ""){
			$Sql= "SELECT F.COD_ESTADO_DOC_SII
							,F.TRACK_ID_DTE
							,F.RESP_EMITIR_DTE
				    FROM FACTURA F
					WHERE F.COD_FACTURA = $cod_factura";
			$result = $db->build_results($Sql);
			$COD_ESTADO_DOC_SII = $result[0]['COD_ESTADO_DOC_SII'];
			$TRACK_ID_DTE		= $result[0]['TRACK_ID_DTE'];
			$RESP_EMITIR_DTE	= $result[0]['RESP_EMITIR_DTE'];
		}
		 
		if($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_EMITIDA){
			if($RESP_EMITIR_DTE == '' && $TRACK_ID_DTE == ''){ //ingresa por primera vez
				if($this->tiene_privilegio_opcion(self::K_AUTORIZA_ENVIAR_DTE)== 'S')
					$this->habilita_boton($temp, 'enviar_dte', true);
				else
					$this->habilita_boton($temp, 'enviar_dte', false);
				
				$this->habilita_boton($temp, 'imprimir_dte', false);
				$this->habilita_boton($temp, 'consultar_dte', false);
				$this->habilita_boton($temp, 'xml_dte', false);
			}else if($RESP_EMITIR_DTE <> '' && $TRACK_ID_DTE == ''){ //Reimprime
				$this->habilita_boton($temp, 'enviar_dte', false);
				$this->habilita_boton($temp, 'imprimir_dte', false);
				$this->habilita_boton($temp, 'consultar_dte', false);
				$this->habilita_boton($temp, 'xml_dte', false);
			}
		}else if ($COD_ESTADO_DOC_SII == self::K_ESTADO_SII_ENVIADA){
			if($TRACK_ID_DTE <> ''){
				if($this->tiene_privilegio_opcion(self::K_AUTORIZA_CONSULTAR_DTE)== 'S')
					$this->habilita_boton($temp, 'consultar_dte', true);
				else
					$this->habilita_boton($temp, 'consultar_dte', false);
					
				$this->habilita_boton($temp, 'enviar_dte', false);
				
				if($this->tiene_privilegio_opcion(self::K_AUTORIZA_IMPRIMIR_DTE)== 'S')
					$this->habilita_boton($temp, 'imprimir_dte', true);
				else
					$this->habilita_boton($temp, 'imprimir_dte', false);
				
				if($this->tiene_privilegio_opcion(self::K_AUTORIZA_XML_DTE)== 'S')
					$this->habilita_boton($temp, 'xml_dte', true);
				else
					$this->habilita_boton($temp, 'xml_dte', false);
			}
		}else{
			$this->habilita_boton($temp, 'enviar_dte', false);
			$this->habilita_boton($temp, 'imprimir_dte', false);
			$this->habilita_boton($temp, 'consultar_dte', false);
			$this->habilita_boton($temp, 'xml_dte', false);
		}
	}
		
	function procesa_event() {		
		if(isset($_POST['b_save_x'])) {
			if (isset($_POST['b_save'])) $this->current_tab_page = $_POST['b_save'];
			if ($this->_save_record()) {
				if ($_POST['wi_hidden']=='save_desde_print')		// Si el save es gatillado desde el boton print, se fuerza que se ejecute nuevamente el print
					print '<script type="text/javascript"> document.getElementById(\'b_print\').click(); </script>';
				elseif ($_POST['wi_hidden']=='save_desde_dte')		// Es es el codigo NUEVO
					print '<script type="text/javascript"> document.getElementById(\'b_print_dte\').click(); </script>';
				elseif ($_POST['wi_hidden']=='save_enviar_dte')		// Es es el save enviar_dte
					print '<script type="text/javascript"> document.getElementById(\'b_enviar_dte\').click(); </script>';	
			}
		}
		/*else if(isset($_POST['b_print_dte_x']))
			$this->envia_FA_electronica();*/
		else if(isset($_POST['print_od_x']))
			$this->print_od();
		else if(isset($_POST['b_enviar_dte_x'])){
			$this->enviar_dte();
		}else if(isset($_POST['b_consultar_dte_x'])){
			$this->actualizar_estado_dte();
		}else if(isset($_POST['b_imprimir_dte_x'])){
			$this->imprimir_dte($_POST['wi_hidden']);
		}else if(isset($_POST['b_reenviar_dte_x'])){
			$this->reenviar_dte();
		}else if(isset($_POST['b_xml_dte_x'])){
			$this->xml_dte();
		}else
			parent::procesa_event();
	}
		
	function enviar_dte(){
		if (!$this->lock_record())
			return false;

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_factura = $this->get_key();
		
		$REFERENCIA_HEM	= $this->dws['dw_factura']->get_item(0, 'REFERENCIA_HEM');
		$REFERENCIA_HES	= $this->dws['dw_factura']->get_item(0, 'REFERENCIA_HES');
		$count_hem = 0;
		$count_hes = 0;
		$tipo = "";
		
			for($i=0 ; $i < $this->dws['dw_referencias']->row_count() ; $i++){
				$COD_TIPO_REFERENCIA = $this->dws['dw_referencias']->get_item($i, 'COD_TIPO_REFERENCIA');
				
				if($COD_TIPO_REFERENCIA == 1){//HEM
					$count_hem++;
					
				}	
			}	
		
			for($i=0 ; $i < $this->dws['dw_referencias']->row_count() ; $i++){
				$COD_TIPO_REFERENCIA = $this->dws['dw_referencias']->get_item($i, 'COD_TIPO_REFERENCIA');
				
				if($COD_TIPO_REFERENCIA == 2){//HES
					$count_hes++;
					
				}
			}
		
		/*
		 * DW de Referencia y de solo contacto
		 */
		$sqlcto ="SELECT DOC_REFERENCIA
						FROM REFERENCIA
						WHERE COD_FACTURA = $cod_factura
						AND COD_TIPO_REFERENCIA in(3,4)";
		$cto = $db->build_results($sqlcto);

		$DOC_REFERENCIA	= $cto[0]['DOC_REFERENCIA'];
		$MAIL_CONTACTO	= $cto[1]['DOC_REFERENCIA'];

		$sql = "SELECT (CAST(F.RUT AS NVARCHAR(8)))+'-'+(CAST (F.DIG_VERIF AS NVARCHAR(1))) as RUT_COMPLETO
						,F.NOM_EMPRESA
              			,F.GIRO
              			,F.DIRECCION
              			,F.NOM_COMUNA
              			,F.PORC_DSCTO1
              			,F.MONTO_DSCTO1
              			,F.MONTO_DSCTO2
              			,F.REFERENCIA TermPagoGlosa
              			,801 TpoDocRef
              			,NRO_ORDEN_COMPRA FolioRef
              			,replace (CONVERT(varchar,FECHA_ORDEN_COMPRA_CLIENTE,102),'.','-')FchRef
              			,1 CodRef
              			,'ORDEN DE COMPRA' RazonRef
				FROM FACTURA F
				WHERE F.COD_FACTURA =$cod_factura";
		$contenido = $db->build_results($sql);
		
		$SqlDetalles ="SELECT ROW_NUMBER()OVER(ORDER BY PRECIO DESC) AS NroLinDet
							,('INT1')AS TpoCodigo
							,ITF.COD_PRODUCTO AS VlrCodigo
							,ITF.NOM_PRODUCTO AS NmbItem 
							,ITF.CANTIDAD
							,ITF.PRECIO
							,(ITF.CANTIDAD * ITF.PRECIO) AS MONTO_TOTAL
						FROM ITEM_FACTURA ITF WHERE ITF.COD_FACTURA = $cod_factura";
		$Detalles = $db->build_results($SqlDetalles);

		for($i = 0; $i < count($Detalles); $i++) {
			$NmbItem	= $Detalles[$i]['NmbItem'];
			$VlrCodigo	= $Detalles[$i]['VlrCodigo'];
			$CANTIDAD	= $Detalles[$i]['CANTIDAD'];
			$PRECIO		= $Detalles[$i]['PRECIO'];
			$MONTO_TOTAL= $Detalles[$i]['MONTO_TOTAL'];

			$ad['Detalle'][$i]["NmbItem"]= utf8_encode(trim($NmbItem));
			$ad['Detalle'][$i]["CdgItem"]= $VlrCodigo;
			$ad['Detalle'][$i]["QtyItem"]= $CANTIDAD;
			$ad['Detalle'][$i]["PrcItem"]= $PRECIO;
		}
		
		$RutRecep		= $contenido[0]['RUT_COMPLETO']; 
		$RznSocRecep	= $contenido[0]['NOM_EMPRESA'];
		$GiroRecep		= $contenido[0]['GIRO'];
		$DirRecep		= $contenido[0]['DIRECCION'];
		$ComRecep		= $contenido[0]['NOM_COMUNA'];
		$DireccionC		= str_replace("#","N",$DirRecep);
		$GiroRecep40	= substr($GiroRecep, 0, 40);
		$DescuentoPct	= $contenido[0]['PORC_DSCTO1'];
		$DescuentoMonto1= $contenido[0]['MONTO_DSCTO1'];
		$DescuentoMonto2= $contenido[0]['F.MONTO_DSCTO2'];
		$DescuentoMonto = $DescuentoMonto1 + $DescuentoMonto2;
		$TermPagoGlosa	= $contenido[0]['TermPagoGlosa'];
		$TpoDocRef		= $contenido[0]['TpoDocRef'];
		$FolioRef		= $contenido[0]['FolioRef'];
		$FchRef			= $contenido[0]['FchRef'];
		$CodRef			= $contenido[0]['CodRef'];
		$RazonRef		= $contenido[0]['RazonRef'];
		
		if($ComRecep == ''){
			$this->_load_record();
			print " <script>alert('Error al Emitir Dte, la empresa de la factura no tiene asignada Comuna.');</script>";
			return;
		}
		
		  
		$SqlEmisor ="SELECT	REPLACE(dbo.f_get_parametro(".self::K_PARAM_RUTEMISOR."),'.','') RUTEMISOR
							,dbo.f_get_parametro(".self::K_PARAM_RZNSOC.") RZNSOC
							,dbo.f_get_parametro(".self::K_PARAM_GIROEMIS.") GIROEMIS
							,dbo.f_get_parametro(".self::K_PARAM_DIRORIGEN.") DIRORIGEN
							,dbo.f_get_parametro(".self::K_PARAM_CMNAORIGEN.") CMNAORIGEN";  
		$Datos_Emisor = $db->build_results($SqlEmisor);
		
		$rutemisor	= $Datos_Emisor[0]['RUTEMISOR']; 
		$rznsoc		= $Datos_Emisor[0]['RZNSOC']; 
		$giroemis	= $Datos_Emisor[0]['GIROEMIS']; 
		$dirorigen	= $Datos_Emisor[0]['DIRORIGEN']; 
		$cmnaorigen	= $Datos_Emisor[0]['CMNAORIGEN']; 
		
		$a['Encabezado']['IdDoc']['TipoDTE']		= self::K_TIPO_DOC; //Factura
		$a['Encabezado']['IdDoc']['Folio']			= 0; //el folio lo da el sistema de facturacion.
		$a['Encabezado']['Emisor']['RUTEmisor']		= $rutemisor;//'76163420-8';
		$a['Encabezado']['Emisor']['RznSoc']		= utf8_encode($rznsoc);//'COMERCIAL E INDUSTRIAL INGTEC LIMITADA';
		$a['Encabezado']['Emisor']['GiroEmis']		= utf8_encode($giroemis);//'FORJA, PRENSADO, ESTAMPADO Y LAMINADO DE METAL';
		$a['Encabezado']['Emisor']['Acteco']		= self::K_ACTV_ECON;//codigo de actividad economica del emisor registrada en el sii.
		$a['Encabezado']['Emisor']['DirOrigen']		= utf8_encode($dirorigen);//'Santa Ester 624';
		$a['Encabezado']['Emisor']['CmnaOrigen']	= utf8_encode($cmnaorigen);//'San Miguel';
		$a['Encabezado']['Receptor']['RUTRecep']	= $RutRecep;
		$a['Encabezado']['Receptor']['RznSocRecep']	= utf8_encode($RznSocRecep);
		$a['Encabezado']['Receptor']['GiroRecep']	= utf8_encode($GiroRecep40);
		if($DOC_REFERENCIA <> ''){
			$a['Encabezado']['Receptor']['Contacto']= utf8_encode($DOC_REFERENCIA); //contacto solo si esta en referencias
		}
		if($MAIL_CONTACTO <> ''){
			$a['Encabezado']['Receptor']['CorreoRecep']= utf8_encode($MAIL_CONTACTO); //contacto solo si esta en el mail contacto
		}
		$a['Encabezado']['Receptor']['DirRecep']	= utf8_encode($DireccionC);
		$a['Encabezado']['Receptor']['CmnaRecep']	= utf8_encode($ComRecep);
		$a['Encabezado']['Receptor']['CmnaRecep']	= utf8_encode($ComRecep);

		$tiene_Folio = 'N';
		$tiene_descuento = 'N';
		$i = 0;
		//////////////////REFERENCIAS///////////////////
		if ($FolioRef <> ''){
			$c['Referencia'][$i]['NroLinRef']	= $i+1;
			$c['Referencia'][$i]['TpoDocRef']	= $TpoDocRef;
			$c['Referencia'][$i]['FolioRef']	= $FolioRef;
			$c['Referencia'][$i]['FchRef']		= $FchRef;
			$c['Referencia'][$i]['CodRef']		= $CodRef;
			$c['Referencia'][$i]['RazonRef']	= $RazonRef;
			$tiene_Folio = 'S';
			$i++;
		}
		
		$sql_guia_despacho = "SELECT REPLACE(dbo.f_fa_nros_guia_despacho(COD_FACTURA), ' ', '') NRO_GUIAS_DESPACHO
							  FROM FACTURA
							  WHERE COD_FACTURA = $cod_factura";
		$result_gd = $db->build_results($sql_guia_despacho);
		
		if(trim($result_gd[0]['NRO_GUIAS_DESPACHO']) <> "")
			$arr_cod_gd = explode('-',$result_gd[0]['NRO_GUIAS_DESPACHO']);
		
		for($k=0 ; $k < count($arr_cod_gd) ; $k++){
			$FolioRef = $arr_cod_gd[$k];
			
			$sql = "SELECT replace (CONVERT(varchar,FECHA_GUIA_DESPACHO,102),'.','-') FECHA_GUIA_DESPACHO
					FROM GUIA_DESPACHO
					WHERE NRO_GUIA_DESPACHO = $FolioRef";
			$result = $db->build_results($sql);
			$FchRef = $result[0]['FECHA_GUIA_DESPACHO'];

			$c['Referencia'][$i]['NroLinRef']	= $i+1;
			$c['Referencia'][$i]['TpoDocRef']	= "52";
			$c['Referencia'][$i]['FolioRef']	= $FolioRef;
			$c['Referencia'][$i]['FchRef']		= $FchRef;
			$c['Referencia'][$i]['CodRef']		= "1";
			$c['Referencia'][$i]['RazonRef']	= "GUIA DE DESPACHO ELECTRONICA";
			$i++;
			
			$tiene_Folio = 'S';
		}
		
		if($count_hem > 0){
			$sql = "SELECT REPLACE(CONVERT(varchar,FECHA_REFERENCIA,102),'.','-') FECHA_REFERENCIA
						  ,DOC_REFERENCIA
					FROM REFERENCIA
					WHERE COD_FACTURA = $cod_factura
					AND COD_TIPO_REFERENCIA = 1";
			$result = $db->build_results($sql);
			
			$FolioRef	= $result[0]['DOC_REFERENCIA'];
			$FchRef		= $result[0]['FECHA_REFERENCIA'];
			
			$c['Referencia'][$i]['NroLinRef']	= $i+1;
			$c['Referencia'][$i]['TpoDocRef']	= "HEM";
			$c['Referencia'][$i]['FolioRef']	= $FolioRef;
			$c['Referencia'][$i]['FchRef']		= $FchRef;
			$c['Referencia'][$i]['CodRef']		= "1";
			$c['Referencia'][$i]['RazonRef']	= "HEM";
			$i++;
			
			$tiene_Folio = 'S';
		}
		
		if($count_hes  > 0){
			$sql = "SELECT REPLACE(CONVERT(varchar,FECHA_REFERENCIA,102),'.','-') FECHA_REFERENCIA
						  ,DOC_REFERENCIA
					FROM REFERENCIA
					WHERE COD_FACTURA = $cod_factura
					AND COD_TIPO_REFERENCIA = 2";
			$result = $db->build_results($sql);
			
			$FolioRef	= $result[0]['DOC_REFERENCIA'];
			$FchRef		= $result[0]['FECHA_REFERENCIA'];
			
			$c['Referencia'][$i]['NroLinRef']	= $i+1;
			$c['Referencia'][$i]['TpoDocRef']	= "HES";
			$c['Referencia'][$i]['FolioRef']	= $FolioRef;
			$c['Referencia'][$i]['FchRef']		= $FchRef;
			$c['Referencia'][$i]['CodRef']		= "1";
			$c['Referencia'][$i]['RazonRef']	= "HES";
			$i++;
			
			$tiene_Folio = 'S';
		}
		///////////////////////////////////////////////////

		if($DescuentoMonto <> 0){
			$b['DscRcgGlobal']['TpoMov']	= 'D'; //D(descuento) o R(recargo)
			$b['DscRcgGlobal']['TpoValor']	= '$';//Indica si es Porcentaje o Monto �%� o �$�
			$b['DscRcgGlobal']['ValorDR']	= $DescuentoMonto;//Valor del descuento o recargo en 16 enteros y 2 decimales
			
			$tiene_descuento = 'S';
			//junta los arreglos en uno.
		}
		
		if($tiene_Folio == 'S' && $tiene_descuento == 'N'){
			 $resultado = array_merge($a,$ad,$c);
		}else if ($tiene_Folio == 'N' && $tiene_descuento == 'S'){
			//junta los arreglos en uno.
			$resultado = array_merge($a,$ad,$b);
		}else if ($tiene_Folio == 'S' && $tiene_descuento == 'S'){
			$resultado = array_merge($a,$ad,$b,$c);
		}else{
			$resultado = array_merge($a,$ad);
		}
		
		
		//se agrega el json_para codificacion requerida por libre_dte.
		$objEnJson = json_encode($resultado);
		
		//LLamo a la nueva clase dte.
		$dte = new dte();
		
		//Se le pasa como variable hash de la clase obtenida en parametros en la BD
		$SqlHash = "SELECT dbo.f_get_parametro(".self::K_PARAM_HASH.") K_HASH";  
		$Datos_Hash = $db->build_results($SqlHash);
		$dte->hash = $Datos_Hash[0]['K_HASH'];
		
		//envio json al la funcion de la clase dte.
		$response = $dte->post_emitir_dte($objEnJson);
		
		//Guarda el response de la funci�n emitir_dte.
		$db->BEGIN_TRANSACTION();
		$sp = 'spu_factura';
		$param = "'SAVE_EMITIR_DTE' 
								,$cod_factura 	--@ve_cod_factura
                                ,NULL 			--@ve_cod_usuario_impresion
                                ,NULL 			--@ve_cod_usuario
                                ,NULL 			--@ve_nro_factura
                                ,NULL 			--@ve_fecha_factura
                                ,NULL 			--@ve_cod_estado_doc_sii
                                ,NULL 			--@ve_cod_empresa
                                ,NULL 			--@ve_cod_sucursal_factura
                                ,NULL 			--@ve_cod_persona
                                ,NULL 			--@ve_referencia
                                ,NULL 			--@ve_nro_orden_compra
                                ,NULL 			--@ve_fecha_orden_compra_cliente
                                ,NULL 			--@ve_obs
                                ,NULL 			--@ve_retirado_por
                                ,NULL 			--@ve_rut_retirado_por
                                ,NULL 			--@ve_dig_verif_retirado_por
                                ,NULL 			--@ve_guia_transporte
                                ,NULL 			--@ve_patente
                                ,NULL 			--@ve_cod_bodega
                                ,NULL 			--@ve_cod_tipo_factura
                                ,NULL 			--@ve_cod_doc
                                ,NULL 			--@ve_motivo_anula
                                ,NULL 			--@ve_cod_usuario_anula
                                ,NULL 			--@ve_cod_usuario_vendedor1
                                ,NULL 			--@ve_porc_vendedor1
                                ,NULL 			--@ve_cod_usuario_vendedor2
                                ,NULL 			--@ve_porc_vendedor2
                                ,NULL 			--@ve_cod_forma_pago
                                ,NULL 			--@ve_cod_origen_venta
                                ,NULL 			--@ve_subtotal
                                ,NULL 			--@ve_porc_dscto1
                                ,NULL 			--@ve_ingreso_usuario_dscto1
                                ,NULL 			--@ve_monto_dscto1
                                ,NULL 			--@ve_porc_dscto2
                                ,NULL 			--@ve_ingreso_usuario_dscto2
                                ,NULL 			--@ve_monto_dscto2
                                ,NULL 			--@ve_total_neto
                                ,NULL 			--@ve_porc_iva
                                ,NULL 			--@ve_monto_iva
                                ,NULL 			--@ve_total_con_iva
                                ,NULL 			--@ve_porc_factura_parcial
                                ,NULL 			--@ve_nom_forma_pago_otro
                                ,NULL 			--@ve_genera_salida
                                ,NULL 			--@ve_tipo_doc
                                ,NULL 			--@ve_cancelada
                                ,NULL 			--@ve_cod_centro_costo
                                ,NULL 			--@ve_cod_vendedor_sofland
                                ,NULL 			--@ve_ws_origen
                                ,NULL 			--@ve_xml_dte
                                ,NULL 			--@ve_track_id_dte
                                ,'$response' 	--@ve_resp_emitir_dte respuesta del envio";
                       
		if ($db->EXECUTE_SP($sp, $param)) {
			$db->COMMIT_TRANSACTION();
		}else{
			$db->ROLLBACK_TRANSACTION();
		}
		
		//Verificamos que realice bien el documento emitido.
		$rep_response = explode("200 OK", $response);
		
		if($rep_response[1] <> ''){
			
			//resuelve la cadena entrega
			$objEnJson_genera = $dte->respuesta_emitir_dte($response);
			
			//se envia al genera.
			$response_genera = $dte->post_genera_dte($objEnJson_genera);
			
			//resuelve cadena enviada desde el genera
			$respuesta_genera_dte = $dte->respuesta_genera_dte($response_genera);
			
			$nro_fa_dte		= $respuesta_genera_dte [6];
			$EnvioDTExml	= $respuesta_genera_dte [28];
			$track_id		= $respuesta_genera_dte [30];
				
			if (($nro_fa_dte <> '') && ($EnvioDTExml <> '')&& ($track_id <> '')){
				$cod_factura = $this->get_key();
				
				$db->BEGIN_TRANSACTION();
				$sp = 'spu_factura';
				$param = "'SAVE_DTE'
								,$cod_factura
                                ,$this->cod_usuario
                                ,NULL 			--@ve_cod_usuario
                                ,$nro_fa_dte
                                ,NULL 			--@ve_fecha_factura
                                ,".self::K_ESTADO_SII_ENVIADA."
                                ,NULL 			--@ve_cod_empresa
                                ,NULL 			--@ve_cod_sucursal_factura
                                ,NULL 			--@ve_cod_persona
                                ,NULL 			--@ve_referencia
                                ,NULL 			--@ve_nro_orden_compra
                                ,NULL 			--@ve_fecha_orden_compra_cliente
                                ,NULL 			--@ve_obs
                                ,NULL 			--@ve_retirado_por
                                ,NULL 			--@ve_rut_retirado_por
                                ,NULL 			--@ve_dig_verif_retirado_por
                                ,NULL 			--@ve_guia_transporte
                                ,NULL 			--@ve_patente
                                ,NULL 			--@ve_cod_bodega
                                ,NULL 			--@ve_cod_tipo_factura
                                ,NULL 			--@ve_cod_doc
                                ,NULL 			--@ve_motivo_anula
                                ,NULL 			--@ve_cod_usuario_anula
                                ,NULL 			--@ve_cod_usuario_vendedor1
                                ,NULL 			--@ve_porc_vendedor1
                                ,NULL 			--@ve_cod_usuario_vendedor2
                                ,NULL 			--@ve_porc_vendedor2
                                ,NULL 			--@ve_cod_forma_pago
                                ,NULL 			--@ve_cod_origen_venta
                                ,NULL 			--@ve_subtotal
                                ,NULL 			--@ve_porc_dscto1
                                ,NULL 			--@ve_ingreso_usuario_dscto1
                                ,NULL 			--@ve_monto_dscto1
                                ,NULL 			--@ve_porc_dscto2
                                ,NULL 			--@ve_ingreso_usuario_dscto2
                                ,NULL 			--@ve_monto_dscto2
                                ,NULL 			--@ve_total_neto
                                ,NULL 			--@ve_porc_iva
                                ,NULL 			--@ve_monto_iva
                                ,NULL 			--@ve_total_con_iva
                                ,NULL 			--@ve_porc_factura_parcial
                                ,NULL 			--@ve_nom_forma_pago_otro
                                ,NULL 			--@ve_genera_salida
                                ,NULL 			--@ve_tipo_doc
                                ,NULL 			--@ve_cancelada
                                ,NULL 			--@ve_cod_centro_costo
                                ,NULL 			--@ve_cod_vendedor_sofland
                                ,NULL 			--@ve_no_tiene_oc
                                ,NULL 			--@ve_cod_cotizacion
                                ,NULL 			--@ve_ws_origen
                                ,NULL 			--@ve_genera_orden_despacho
                                ,NULL 			--@ve_cod_usuario_genera_od
                                ,'$EnvioDTExml'	--@ve_xml_dte
                                ,$track_id 		--@ve_track_id_dte";
	            if ($db->EXECUTE_SP($sp, $param)) {
					$db->COMMIT_TRANSACTION();
					$cod_factura = $this->get_key();
					
					$GENERA_ORDEN_DESPACHO	= $this->dws['dw_factura']->get_item(0, 'GENERA_ORDEN_DESPACHO');
					$WS_ORIGEN				= $this->dws['dw_factura']->get_item(0, 'WS_ORIGEN');
					$NRO_ORDEN_COMPRA		= $this->dws['dw_factura']->get_item(0, 'NRO_ORDEN_COMPRA');
					
					$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
					$sql_dte = "SELECT	F.COD_FACTURA,
									F.NRO_FACTURA,
									F.TIPO_DOC,
									dbo.f_format_date(FECHA_FACTURA,1)FECHA_FACTURA,
									F.COD_USUARIO_IMPRESION,
									F.NRO_ORDEN_COMPRA,
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
									convert(varchar(5), GETDATE(), 8) HORA,
									F.GENERA_SALIDA,
									F.OBS,
									F.CANCELADA
							FROM 	FACTURA F left outer join COMUNA COM on F.COD_COMUNA = COM.COD_COMUNA,
									ITEM_FACTURA ITF, CIUDAD CIU, FORMA_PAGO FP 
							WHERE 	F.COD_FACTURA = ".$cod_factura." 
							AND	ITF.COD_FACTURA = F.COD_FACTURA
							AND	CIU.COD_CIUDAD = F.COD_CIUDAD
							AND	FP.COD_FORMA_PAGO = F.COD_FORMA_PAGO";
					$result_dte = $db->build_results($sql_dte);
					
					
	            	if($GENERA_ORDEN_DESPACHO == 'S'){
						$sp = 'spu_factura';
						$cod_usuario = session::get('COD_USUARIO');
						
						$param = "'GENERA_OD', $cod_factura, $cod_usuario";
						$db->EXECUTE_SP($sp, $param);
					}
	            	
					if($WS_ORIGEN == 'BODEGA'){
						//Se valida las OC de las cuales se va a realizar esta nueva implementacion
						if($NRO_ORDEN_COMPRA > 57130)
							$sistema = 'BODEGA';
						
					}else if($WS_ORIGEN == 'COMERCIAL'){
						//Se valida las OC de las cuales se va a realizar esta nueva implementacion
						if($NRO_ORDEN_COMPRA > 184160){
							$sistema = 'COMERCIAL';
						}
					}else if($WS_ORIGEN == 'RENTAL'){
						//Se valida las OC de las cuales se va a realizar esta nueva implementacion
						if($NRO_ORDEN_COMPRA > 66110)
							$sistema = 'RENTAL';
						
					}
					
	           		if($sistema <> ''){
						
						$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
						$sql = "SELECT SISTEMA, URL_WS, USER_WS,PASSWROD_WS  FROM PARAMETRO_WS
								WHERE SISTEMA = '".$WS_ORIGEN."'";
						$result = $db->build_results($sql);
						
						$user_ws		= $result[0]['USER_WS'];
						$passwrod_ws	= $result[0]['PASSWROD_WS'];
						$url_ws			= $result[0]['URL_WS'];
						
						$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
						$result_ws = $biggi->cli_add_faprov($result_dte, $sistema);
						
						if($result_ws == 'MSJ_REGISTRO')
							$this->alert('Ya hay un Registro en faprov bodega biggi');
						else if($result_ws == 'NO_REGISTRO_OC')
							$this->alert('No hay OC asociado a esta factura o factura no es para todoinox');
						else if($result_ws == 'NO_IGUAL')
							$this->alert('Tiene diferentes item, cantidades o productos');
						else if($result_ws == 'HECHO')
							$this->alert('registro guardado');
					}
					
					print " <script>window.open('../common_appl/print_dte.php?cod_documento=$cod_factura&DTE_ORIGEN=33&ES_CEDIBLE=N')</script>";
					$this->_load_record();
				}else{
					$db->ROLLBACK_TRANSACTION();
				}
			}else{
				$this->_load_record();
				print " <script>alert('Error al Generar Dte contactarse con Integrasystem. $respuesta_genera_dte[0]');</script>";
			}	
		}else{
			//responde al dte consultado.
			$this->_load_record();
			print " <script>alert('Error al Emitir Dte contactarse con Integrasystem.');</script>";
		}
		$this->unlock_record();
	}
	
	function actualizar_estado_dte(){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_factura = $this->get_key();
		
		$sql = "SELECT '".self::K_TIPO_DOC."' DTE
              			,F.NRO_FACTURA
              			,REPLACE(REPLACE(dbo.f_get_parametro(".self::K_PARAM_RUTEMISOR."),'.',''),'-7','') as RUTEMISOR
				FROM FACTURA F
				WHERE F.COD_FACTURA =$cod_factura";
		$consultar = $db->build_results($sql);
		
		$tipodte		= $consultar[0]['DTE']; 
		$nro_factura	= $consultar[0]['NRO_FACTURA']; 
		$rutemisor		= $consultar[0]['RUTEMISOR'];
		
		//Llamamos a dte.
		$dte = new dte();
		
		//Se le pasa como variable hash de la clase obtenida en parametros en la BD
		$SqlHash = "SELECT dbo.f_get_parametro(".self::K_PARAM_HASH.") K_HASH";  
		$Datos_Hash = $db->build_results($SqlHash);
		$dte->hash = $Datos_Hash[0]['K_HASH'];
		
		//Llamamos al envio consultar estado de socumento.
		$response = $dte->actualizar_estado($tipodte,$nro_factura,$rutemisor);
		
		$actualizar_estado = $dte->respuesta_actualizar_estado($response);
		
		$revision_estado	= $actualizar_estado [9]; //respuesta de aceptado.
		if ($revision_estado == ''){
			$revision_estado	= $actualizar_estado [6]; //respuesta de rechazado.
		}
		//responde al dte consultado.
		$this->_load_record();
		print "<script>alert('Su documento electronico se encuentra en estado: $revision_estado');</script>";
	}
	
	function imprimir_dte($es_cedible){
		$cod_factura = $this->get_key();
		print " <script>window.open('../common_appl/print_dte.php?cod_documento=$cod_factura&DTE_ORIGEN=33&ES_CEDIBLE=$es_cedible')</script>";
		$this->_load_record();
	}
	
	function reenviar_dte(){
		if (!$this->lock_record())
			return false;

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_factura = $this->get_key();
		$Sql= "SELECT F.COD_ESTADO_DOC_SII
						,F.TRACK_ID_DTE
						,F.RESP_EMITIR_DTE
			    FROM FACTURA F
				WHERE F.COD_FACTURA = $cod_factura";
		$result = $db->build_results($Sql);
		$COD_ESTADO_DOC_SII = $result[0]['COD_ESTADO_DOC_SII'];
		$TRACK_ID_DTE		= $result[0]['TRACK_ID_DTE'];
		$RESP_EMITIR_DTE	= $result[0]['RESP_EMITIR_DTE'];
		
		$rep_response = explode("200 OK", $RESP_EMITIR_DTE);
		if($rep_response[1] == '' && $TRACK_ID_DTE == '' && $COD_ESTADO_DOC_SII == self::K_ESTADO_SII_EMITIDA){
			$this->_load_record();
			print " <script>alert('Error al Emitir Dte contactarse con Integrasystem.');</script>";
		}else{
			//Llamamos a dte.
			$dte = new dte();
			//Se le pasa como variable hash de la clase obtenida en parametros en la BD
			$SqlHash = "SELECT dbo.f_get_parametro(".self::K_PARAM_HASH.") K_HASH";  
			$Datos_Hash = $db->build_results($SqlHash);
			$dte->hash = $Datos_Hash[0]['K_HASH'];
			
			//resuelve la cadena entrega
			$objEnJson_genera = $dte->respuesta_emitir_dte($RESP_EMITIR_DTE);
			//se envia al genera.
			$response_genera = $dte->post_genera_dte($objEnJson_genera);
			//resuelve cadena enviada desde el genera
			$respuesta_genera_dte = $dte->respuesta_genera_dte($response_genera);
			$nro_fa_dte		= $respuesta_genera_dte [6];
			$EnvioDTExml	= $respuesta_genera_dte [28];
			$track_id		= $respuesta_genera_dte [30];
				
			if (($nro_fa_dte <> '') && ($EnvioDTExml <> '')&& ($track_id <> '')){
				$cod_factura = $this->get_key();
				
				$db->BEGIN_TRANSACTION();
				$sp = 'spu_factura';
				$param = "'SAVE_DTE'
								,$cod_factura
                                ,$this->cod_usuario
                                ,NULL 			--@ve_cod_usuario
                                ,$nro_fa_dte
                                ,NULL 			--@ve_fecha_factura
                                ,".self::K_ESTADO_SII_ENVIADA."
                                ,NULL 			--@ve_cod_empresa
                                ,NULL 			--@ve_cod_sucursal_factura
                                ,NULL 			--@ve_cod_persona
                                ,NULL 			--@ve_referencia
                                ,NULL 			--@ve_nro_orden_compra
                                ,NULL 			--@ve_fecha_orden_compra_cliente
                                ,NULL 			--@ve_obs
                                ,NULL 			--@ve_retirado_por
                                ,NULL 			--@ve_rut_retirado_por
                                ,NULL 			--@ve_dig_verif_retirado_por
                                ,NULL 			--@ve_guia_transporte
                                ,NULL 			--@ve_patente
                                ,NULL 			--@ve_cod_bodega
                                ,NULL 			--@ve_cod_tipo_factura
                                ,NULL 			--@ve_cod_doc
                                ,NULL 			--@ve_motivo_anula
                                ,NULL 			--@ve_cod_usuario_anula
                                ,NULL 			--@ve_cod_usuario_vendedor1
                                ,NULL 			--@ve_porc_vendedor1
                                ,NULL 			--@ve_cod_usuario_vendedor2
                                ,NULL 			--@ve_porc_vendedor2
                                ,NULL 			--@ve_cod_forma_pago
                                ,NULL 			--@ve_cod_origen_venta
                                ,NULL 			--@ve_subtotal
                                ,NULL 			--@ve_porc_dscto1
                                ,NULL 			--@ve_ingreso_usuario_dscto1
                                ,NULL 			--@ve_monto_dscto1
                                ,NULL 			--@ve_porc_dscto2
                                ,NULL 			--@ve_ingreso_usuario_dscto2
                                ,NULL 			--@ve_monto_dscto2
                                ,NULL 			--@ve_total_neto
                                ,NULL 			--@ve_porc_iva
                                ,NULL 			--@ve_monto_iva
                                ,NULL 			--@ve_total_con_iva
                                ,NULL 			--@ve_porc_factura_parcial
                                ,NULL 			--@ve_nom_forma_pago_otro
                                ,NULL 			--@ve_genera_salida
                                ,NULL 			--@ve_tipo_doc
                                ,NULL 			--@ve_cancelada
                                ,NULL 			--@ve_cod_centro_costo
                                ,NULL 			--@ve_cod_vendedor_sofland
                                ,NULL 			--@ve_no_tiene_oc
                                ,NULL 			--@ve_cod_cotizacion
                                ,NULL 			--@ve_ws_origen
                                ,NULL 			--@ve_genera_orden_despacho
                                ,NULL 			--@ve_cod_usuario_genera_od
                                ,'$EnvioDTExml'		--@ve_xml_dte
                                ,$track_id 			--@ve_track_id_dte";
				if ($db->EXECUTE_SP($sp, $param)) {
					$db->COMMIT_TRANSACTION();
					$cod_factura = $this->get_key();
					print " <script>window.open('print_dte.php?cod_factura=$cod_factura')</script>";
					$this->_load_record();
				}else{
					$db->ROLLBACK_TRANSACTION();
				}
			}else{
				$this->_load_record();
				print " <script>alert('Error al Generar Dte contactarse con Integrasystem. $respuesta_genera_dte[0]');</script>";
			}
		}		
		$this->unlock_record();
	}

	function xml_dte(){
		$cod_factura = $this->get_key();
		$name_archivo = "XML_DTE_FACTURA_".$this->get_key_para_ruta_menu().".xml";
		
		$fname = tempnam("/tmp", $name_archivo);
		$handle = fopen($fname,"w");
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);

		$sql= "SELECT XML_DTE
			   FROM FACTURA
			   WHERE COD_FACTURA = $cod_factura";
		$result = $db->build_results($sql);
		
		$XML_DTE = base64_decode($result[0]['XML_DTE']);
		$XML_DTE = urldecode($XML_DTE);
		
		fwrite($handle, $XML_DTE);				
		fwrite($handle, "\r\n");
		
		fclose($handle);
		
		header("Content-Type: application/force-download; name=\"$name_archivo\"");
		header("Content-Disposition: inline; filename=\"$name_archivo\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
	}
	
		function print_od(){
			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$COD_FACTURA = $this->get_key();
			
			$sql = "select COUNT(*) COUNT
					from ORDEN_DESPACHO od,ESTADO_ORDEN_DESPACHO eo
					where od.COD_DOC_ORIGEN = $COD_FACTURA
					and eo.COD_ESTADO_ORDEN_DESPACHO = od.COD_ESTADO_ORDEN_DESPACHO";
			$result = $db->build_results($sql);
			
			if($result[0]['COUNT'] > 1){
				$this->_load_record();
				print " <script>alert('Error al Imprimir OD, contactarse con Integrasystem.');</script>";
			}
			
			$sql = "select od.COD_ORDEN_DESPACHO,eo.NOM_ESTADO_ORDEN_DESPACHO
					from ORDEN_DESPACHO od,ESTADO_ORDEN_DESPACHO eo
					where od.COD_DOC_ORIGEN = $COD_FACTURA
					and eo.COD_ESTADO_ORDEN_DESPACHO = od.COD_ESTADO_ORDEN_DESPACHO";
			$result = $db->build_results($sql);
			$cod_orden_despacho = $result[0]['COD_ORDEN_DESPACHO'];
		    $nom_estado_orden_despacho = $result[0]['NOM_ESTADO_ORDEN_DESPACHO'];
		    
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
						  ,F.NRO_FACTURA
						  ,(SELECT UD.NOM_USUARIO FROM USUARIO UD WHERE UD.COD_USUARIO = OD.COD_USUARIO_VENDEDOR1) VENDEDOR_1
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
}
   		
class print_factura_base extends reporte {	
	function print_factura_base($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
		parent::reporte($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);			
	}
	function print_con_iva_fa(&$pdf, $x, $y) {
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
		$COD_NV		= $result[0]['COD_DOC'];	
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
		$pdf->Text($x+339, $y-33, $nro_factura);
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY($x-16, $y+8);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(250, 15,"$nom_empresa");
		$pdf->Text($x+304, $y+16, $rut);
		$pdf->SetFont('Arial','',11);
		$pdf->Text($x+330, $y+40, $oc);
		$pdf->SetXY($x-16, $y+55);
		$pdf->MultiCell(250,10,"$direccion");
		$pdf->SetFont('Arial','',10);
		$pdf->Text($x+324, $y+65, $comuna);
		$pdf->Text($x-29, $y+88, $ciudad);
		$pdf->SetXY($x+126, $y+81);
		$pdf->MultiCell(120, 8,"$giro", 0, 'L');
		$pdf->Text($x+314, $y+88, $fono);
		$pdf->Text($x+25, $y+115, $guia_despacho);
		$pdf->Text($x+364, $y+115, $cond_venta);				
		$pdf->SetFont('Arial','B',10);
		$pdf->Text($x, $y+160, "$REFERENCIA");
		
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
			$pdf->Text($x-51, $y+188+(15*$i), $item);			
			$pdf->Text($x-21, $y+188+(15*$i), $cantidad);
			$pdf->Text($x+9, $y+188+(15*$i), $modelo);			
			$pdf->SetXY($x+54, $y+185+(15*$i));
			$pdf->Cell(300, 0, "$detalle");
			$pdf->SetXY($x+304, $y+181+(15*$i));
			$pdf->MultiCell(80,7, $p_unitario,0, 'R');		
			$pdf->SetXY($x+371, $y+181+(15*$i));
			$pdf->MultiCell(80,7, $total,0, 'R');						
		}					
									
		// DIBUJANDO TOTALES
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($x+48,$y+455);
		$pdf->MultiCell(270,10,'Son: '.$total_en_palabras.' pesos.');
		
		if($total_dscto <> 0){//tiene dscto
			if(($monto_dscto1 <> 0 && $monto_dscto2 == 0) || ($monto_dscto2 <> 0 && $monto_dscto1 == 0)){//solo tiene un DSCTO 1
				$pdf->SetXY($x+316, $y+490);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+490);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				if($monto_dscto1 <> 0){
					$pdf->SetXY($x+313, $y+505);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto1.' % DSCTO  $ ',0, 'R');

					$pdf->SetXY($x+348, $y+505);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');
				}
				else{
					$pdf->SetXY($x+313, $y+505);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO: $ ',0, 'R');

					$pdf->SetXY($x+348, $y+505);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');				
				}				
			}else if($monto_dscto1 <> 0 && $monto_dscto2 <> 0){//tiene ambos DSCTO

				$pdf->SetXY($x+316, $y+475);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+475);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				$pdf->SetXY($x+310, $y+490);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO1 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+490);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');	
				
				$pdf->SetXY($x+316, $y+505);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO2 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+505);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');
			}
		}

		$pdf->SetXY($x+316, $y+520);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(80,4, 'TOTAL NETO $ ',0, 'R');
		$pdf->SetXY($x+348, $y+520);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$neto,0, 'R');
		$pdf->SetXY($x+316, $y+535);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(80,4, $porc_iva.' % IVA  $ ',0, 'R');
		$pdf->SetXY($x+348, $y+535);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$monto_iva,0, 'R');
		$pdf->Rect($x+330, $y+544, 120, 2, 'f');
		$pdf->SetXY($x+317, $y+555);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(80,4,'TOTAL  $ ',0, 'R');
		$pdf->SetXY($x+348, $y+553);
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
	
	//Factura Exenta
	function print_sin_iva_fa(&$pdf, $x, $y) {
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
		$COD_NV		= $result[0]['COD_DOC'];	
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
		$pdf->Text($x-15, $y-4,$fecha);
		$pdf->SetFont('Arial','',8);		
		$pdf->Text($x+355, $y-35, $nro_factura);
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY($x-20, $y+6);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(250, 17,"$nom_empresa");
		$pdf->Text($x+304, $y+16, $rut);
		$pdf->SetFont('Arial','',11);
		$pdf->Text($x+330, $y+40, $oc);
		$pdf->SetXY($x-16, $y+55);
		$pdf->MultiCell(250,10,"$direccion");
		$pdf->SetFont('Arial','',10);
		$pdf->Text($x+324, $y+63, $comuna);
		$pdf->Text($x-30, $y+88, $ciudad);
		$pdf->SetXY($x+126, $y+79);
		$pdf->MultiCell(120, 10,"$giro", 0, 'L');
		$pdf->Text($x+314, $y+86, $fono);
		$pdf->Text($x+31, $y+115, $guia_despacho);
		$pdf->Text($x+354, $y+112, $cond_venta);
		$pdf->SetFont('Arial','B',10);
		$pdf->Text($x, $y+161, "$REFERENCIA");
		
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
			$pdf->Text($x-60, $y+181+(15*$i), $item);			
			$pdf->Text($x-30, $y+181+(15*$i), $cantidad);
			$pdf->Text($x-2, $y+181+(15*$i), $modelo);			
			$pdf->SetXY($x+51, $y+179+(15*$i));
			$pdf->Cell(300, 0, "$detalle");
			$pdf->SetXY($x+290, $y+174+(15*$i));
			$pdf->MultiCell(80,7, $p_unitario,0, 'R');		
			$pdf->SetXY($x+361, $y+174+(15*$i));
			$pdf->MultiCell(80,7, $total,0, 'R');						
		}

		/*// DIBUJANDO TOTALES
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($x-40, $y+445);
		$pdf->MultiCell(360, 9,'Son: '.$total_en_palabras.' pesos.');
		
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x-70, $y+475);
		$pdf->MultiCell(380, 8, "$OBS");*/
		
		//DIBUJANDO TOTALES
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x-50, $y+445);
		$pdf->MultiCell(380, 8, "$OBS");
		
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($x-50, $y+475);
		$pdf->MultiCell(360, 9,'Son: '.$total_en_palabras.' pesos.');
		
		if($total_dscto <> 0){//tiene dscto
			if(($monto_dscto1 <> 0 && $monto_dscto2 == 0) || ($monto_dscto2 <> 0 && $monto_dscto1 == 0)){//solo tiene un DSCTO 1
				$pdf->SetXY($x+316, $y+515);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+515);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				if($monto_dscto1 <> 0){
					$pdf->SetXY($x+310, $y+530);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO  $ ',0, 'R');

					$pdf->SetXY($x+348, $y+530);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');
				}
				else{
					$pdf->SetXY($x+316, $y+530);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO: $ ',0, 'R');

					$pdf->SetXY($x+348, $y+530);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');				
				}				
			}else if($monto_dscto1 <> 0 && $monto_dscto2 <> 0){//tiene ambos DSCTO

				$pdf->SetXY($x+316, $y+500);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+500);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				$pdf->SetXY($x+310, $y+515);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO1 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+515);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');	
				
				$pdf->SetXY($x+316, $y+530);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO2 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+530);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');
			}
		}

		$pdf->Rect($x+300, $y+544, 150, 2, 'f');
		$pdf->SetXY($x+300, $y+555);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(95,4,'TOTAL EXENTO  $ ',0, 'R');
		$pdf->SetXY($x+348, $y+555);
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
		$pdf->Text($x+83, $y+503, $retirado_por);
		$pdf->Text($x+83, $y+524, $retirado_por_rut);
		$pdf->Text($x+245, $y+542, $retira_fecha);
	}
		
///////////CLAUDIA MORALES/////////////////////////////////////////
	
	//Factura Normal CMR
	function CMR_print_con_iva(&$pdf, $x, $y) {
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
		$COD_NV		= $result[0]['COD_DOC'];	
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
		$pdf->Text($x-11, $y-4,$fecha);
		$pdf->SetFont('Arial','',8);		
		$pdf->Text($x+339, $y-33, $nro_factura);
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY($x-16, $y+8);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(250, 15,"$nom_empresa");
		$pdf->Text($x+304, $y+16, $rut);
		$pdf->SetFont('Arial','',11);
		$pdf->Text($x+330, $y+40, $oc);
		$pdf->SetXY($x-16, $y+55);
		$pdf->MultiCell(250,10,"$direccion");
		$pdf->SetFont('Arial','',10);
		$pdf->Text($x+324, $y+65, $comuna);
		$pdf->Text($x-30, $y+88, $ciudad);
		$pdf->SetXY($x+126, $y+81);
		$pdf->MultiCell(120, 10,"$giro", 0, 'L');
		$pdf->Text($x+314, $y+88, $fono);
		$pdf->Text($x+31, $y+115, $guia_despacho);
		$pdf->Text($x+354, $y+115, $cond_venta);
		$pdf->SetFont('Arial','B',10);
		$pdf->Text($x+50, $y+164, "$REFERENCIA");
		
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
			$pdf->Text($x-60, $y+184+(15*$i), $item);			
			$pdf->Text($x-30, $y+184+(15*$i), $cantidad);
			$pdf->Text($x, $y+184+(15*$i), $modelo);			
			$pdf->SetXY($x+54, $y+181+(15*$i));
			$pdf->Cell(300, 0, "$detalle");
			$pdf->SetXY($x+304, $y+177+(15*$i));
			$pdf->MultiCell(80,7, $p_unitario,0, 'R');		
			$pdf->SetXY($x+371, $y+177+(15*$i));
			$pdf->MultiCell(80,7, $total,0, 'R');						
		}					
									
		// DIBUJANDO TOTALES
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($x+48, $y+455);
		$pdf->MultiCell(270,10,'Son: '.$total_en_palabras.' pesos.');
		
		if($total_dscto <> 0){//tiene dscto
			if(($monto_dscto1 <> 0 && $monto_dscto2 == 0) || ($monto_dscto2 <> 0 && $monto_dscto1 == 0)){//solo tiene un DSCTO 1
				$pdf->SetXY($x+316, $y+490);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+490);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				if($monto_dscto1 <> 0){
					$pdf->SetXY($x+313, $y+505);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto1.' % DSCTO  $ ',0, 'R');

					$pdf->SetXY($x+348, $y+505);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');
				}
				else{
					$pdf->SetXY($x+316, $y+505);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO: $ ',0, 'R');

					$pdf->SetXY($x+348, $y+505);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');				
				}				
			}else if($monto_dscto1 <> 0 && $monto_dscto2 <> 0){//tiene ambos DSCTO

				$pdf->SetXY($x+316, $y+475);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+475);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				$pdf->SetXY($x+310, $y+490);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO1 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+490);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');	
				
				$pdf->SetXY($x+316, $y+505);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO2 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+505);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');
			}
		}

		$pdf->SetXY($x+316, $y+520);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(80,4, 'TOTAL NETO $ ',0, 'R');
		$pdf->SetXY($x+348, $y+520);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$neto,0, 'R');
		$pdf->SetXY($x+316, $y+535);
		$pdf->SetFont('Arial','',9);
		$pdf->MultiCell(80,4, $porc_iva.' % IVA  $ ',0, 'R');
		$pdf->SetXY($x+348, $y+535);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(105,4,$monto_iva,0, 'R');
		$pdf->Rect($x+330, $y+544, 120, 2, 'f');
		$pdf->SetXY($x+317, $y+555);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(80,4,'TOTAL  $ ',0, 'R');
		$pdf->SetXY($x+348, $y+553);
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
		$pdf->Text($x+83, $y+499, $retirado_por);
		$pdf->Text($x+83, $y+520, $retirado_por_rut);
		$pdf->Text($x+243, $y+538, $retira_fecha);
	}
	

	//Factura Exenta CMR
	
	function CMR_print_sin_iva(&$pdf, $x, $y) {
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
		$COD_NV		= $result[0]['COD_DOC'];	
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
		$pdf->Text($x-11, $y-4,$fecha);
		$pdf->SetFont('Arial','',8);		
		$pdf->Text($x+359, $y-31, $nro_factura);
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY($x-16, $y+6);
		$pdf->SetFont('Arial','B',11);
		$pdf->MultiCell(250, 17,"$nom_empresa");
		$pdf->Text($x+304, $y+16, $rut);
		$pdf->SetFont('Arial','',11);
		$pdf->Text($x+330, $y+40, $oc);
		$pdf->SetXY($x-16, $y+55);
		$pdf->MultiCell(250,10,"$direccion");
		$pdf->SetFont('Arial','',10);
		$pdf->Text($x+324, $y+63, $comuna);
		$pdf->Text($x-30, $y+88, $ciudad);
		$pdf->SetXY($x+126, $y+79);
		$pdf->MultiCell(120, 10,"$giro", 0, 'L');
		$pdf->Text($x+314, $y+86, $fono);
		$pdf->Text($x+31, $y+115, $guia_despacho);
		$pdf->Text($x+354, $y+112, $cond_venta);
		$pdf->SetFont('Arial','B',10);
		$pdf->Text($x+50, $y+164, "$REFERENCIA");
		
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
			$pdf->Text($x-60, $y+184+(15*$i), $item);			
			$pdf->Text($x-30, $y+184+(15*$i), $cantidad);
			$pdf->Text($x-2, $y+184+(15*$i), $modelo);			
			$pdf->SetXY($x+51, $y+181+(15*$i));
			$pdf->Cell(300, 0, "$detalle");
			$pdf->SetXY($x+290, $y+177+(15*$i));
			$pdf->MultiCell(80,7, $p_unitario,0, 'R');		
			$pdf->SetXY($x+361, $y+177+(15*$i));
			$pdf->MultiCell(80,7, $total,0, 'R');						
		}

		// DIBUJANDO TOTALES
		$pdf->SetFont('Arial','',9);
		$pdf->SetXY($x-50, $y+445);
		$pdf->MultiCell(380, 8, "$OBS");
				
		$pdf->SetFont('Arial','',12);
		$pdf->SetXY($x-50, $y+475);
		$pdf->MultiCell(270,10,'Son: '.$total_en_palabras.' pesos.');

		if($total_dscto <> 0){//tiene dscto
			if(($monto_dscto1 <> 0 && $monto_dscto2 == 0) || ($monto_dscto2 <> 0 && $monto_dscto1 == 0)){//solo tiene un DSCTO 1
				$pdf->SetXY($x+316, $y+515);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+515);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				if($monto_dscto1 <> 0){
					$pdf->SetXY($x+310, $y+530);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO  $ ',0, 'R');

					$pdf->SetXY($x+348, $y+530);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');
				}
				else{
					$pdf->SetXY($x+316, $y+530);
					$pdf->SetFont('Arial','',9);
					$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO: $ ',0, 'R');

					$pdf->SetXY($x+348, $y+530);
					$pdf->SetFont('Arial','B',11);
					$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');				
				}				
			}else if($monto_dscto1 <> 0 && $monto_dscto2 <> 0){//tiene ambos DSCTO

				$pdf->SetXY($x+316, $y+500);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4, 'SUBTOTAL  $ ',0, 'R');
			
				$pdf->SetXY($x+348, $y+500);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$subtotal,0, 'R');

				$pdf->SetXY($x+310, $y+515);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(85,4,$porc_dscto1.' % DSCTO1 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+515);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto1, 0, 'R');
				
				$pdf->SetXY($x+316, $y+530);
				$pdf->SetFont('Arial','',9);
				$pdf->MultiCell(80,4,$porc_dscto2.' % DSCTO2 $ ',0, 'R');

				$pdf->SetXY($x+348, $y+530);
				$pdf->SetFont('Arial','B',11);
				$pdf->MultiCell(105,4,$monto_dscto2, 0, 'R');
			}
		}

		$pdf->Rect($x+300, $y+544, 150, 2, 'f');
		$pdf->SetXY($x+300, $y+555);
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(95,4,'TOTAL EXENTO  $ ',0, 'R');
		$pdf->SetXY($x+348, $y+555);
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
		$pdf->Text($x+83, $y+503, $retirado_por);
		$pdf->Text($x+83, $y+524, $retirado_por_rut);
		$pdf->Text($x+245, $y+542, $retira_fecha);
	}
////////END  CLAUDIA MORALES///////////////////////////////
	
	function modifica_pdf(&$pdf){
		$pdf->AutoPageBreak=false;		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$result = $db->build_results($this->sql);
		$porc_iva = $result[0]['PORC_IVA'];
		
		//USUARIOS
		$USUARIO_IMPRESION = $result[0]['USUARIO_IMPRESION'];
		$SP = 4;
		$KV = 30;
		$CMR = 9;
		$IS = 31;

		if($porc_iva != 0){
			if($USUARIO_IMPRESION == $CMR){ //Claudia Morales
				$this->CMR_print_con_iva($pdf, 100, 128);
			}else if($USUARIO_IMPRESION == $KV){ //karina Verdugo
				$this->print_con_iva_fa($pdf, 100, 150);
			}else if($USUARIO_IMPRESION == $SP){ //Sergio Pechoante
				$this->print_con_iva_fa($pdf, 100, 145);
			}else{//otros usuarios
				$this->print_con_iva_fa($pdf, 100, 145);
			}
		}else{
			if($USUARIO_IMPRESION == $CMR){ //Claudia Morales
				$this->CMR_print_sin_iva($pdf, 78, 161);
			}else if($USUARIO_IMPRESION == $KV){ //karina Verdugo
				$this->print_sin_iva_fa($pdf, 81, 158);
			}else if($USUARIO_IMPRESION == $SP){ //Sergio Pechoante
				$this->print_sin_iva_fa($pdf, 79, 155);
			}else{//otros usuarios
				$this->print_sin_iva_fa($pdf, 79, 155);
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

		$pdf->SetFont('Arial','',8.5);
		$pdf->SetXY(30,$y_ini-15);
		$pdf->Cell(555, 15, 'OBSERVACION:', '', '','L');

		$pdf->SetXY(30,$y_ini+5);
		
		$pdf->MultiCell(554, 15, $result[0]['OBS'], '1', 'T');
		
		$y_ini = 700;
		
		$pdf->SetFont('Arial','',11);
		$pdf->SetXY(50,$y_ini-10);
		$pdf->Cell(200, 15, '----------------------------------------------------', '', '','L');
		$pdf->SetXY(50,$y_ini);
		$pdf->Cell(200, 15, 'Entrega:', '', '','L');
		
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
		$pdf->Cell(200, 15, $result[0]['NOM_PERSONA'], '', '','R');//NOM_USUARIO_RECIBE
		
		$pdf->SetXY(369,$y_ini+15);
		$pdf->SetTextColor(0, 0, 0);
		$pdf->Cell(200, 15,$result[0]['NOM_EMPRESA'], '', '','L');
		
		$pdf->SetFont('Arial','',8.5);		
	}
}

/////////////////////////////////////////////////////////////
// Se separa por K_CLIENTE
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_wi_factura.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class wi_factura extends wi_factura_base {
		function wi_factura($cod_item_menu) {
			parent::wi_factura_base($cod_item_menu); 
		}
	}
	class print_factura extends print_factura_base {	
		function print_factura($sql, $xml, $labels=array(), $titulo, $con_logo, $vuelve_a_presentacion=false) {
			parent::print_factura_base($sql, $xml, $labels, $titulo, $con_logo, $vuelve_a_presentacion);
		}			
	}
}

//item_factura
$file_name = dirname(__FILE__)."/".K_CLIENTE."/class_dw_item_factura.php";
if (file_exists($file_name)) 
	require_once($file_name);
else {
	class dw_item_factura extends dw_item_factura_base {
		function dw_item_factura() {
			parent::dw_item_factura_base(); 
		}
	}
}
?>