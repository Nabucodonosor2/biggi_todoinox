<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/class_informe_cx_cot_extranjera.php");
class dw_item_cx_cot_extranjera extends datawindow {
	function dw_item_cx_cot_extranjera() {
		$sql = "SELECT COD_CX_ITEM_COT_EXTRANJERA
						,COD_CX_COT_EXTRANJERA
						,ORDEN 
						,ITEM
						,COD_PRODUCTO
						,NOM_PRODUCTO
						,COD_EQUIPO_OC_EX
						,DESC_EQUIPO_OC_EX
						,COD_EQUIPO_OC_EX	COD_EQUIPO_OC_EX_H
						,DESC_EQUIPO_OC_EX	DESC_EQUIPO_OC_EX_H
						,CANTIDAD
						,PRECIO
				FROM CX_ITEM_COT_EXTRANJERA  
				WHERE COD_CX_COT_EXTRANJERA = {KEY1}";
		
		parent::datawindow($sql, 'ITEM_CX_COT_EXTRANJERA', true, true,'COD_PRODUCTO');
		
		$this->add_control(new edit_text('COD_CX_ITEM_COT_EXTRANJERA',10, 10, 'hidden'));
		$this->add_control(new edit_num('ORDEN',4, 10));
		$this->add_control(new edit_text('ITEM',4 , 5));
		$this->add_control(new static_text('COD_EQUIPO_OC_EX',10, 10));
		$this->add_control(new static_text('DESC_EQUIPO_OC_EX',10, 10));
		$this->add_control(new edit_text('COD_EQUIPO_OC_EX_H',10, 10, 'hidden'));
		$this->add_control(new edit_text('DESC_EQUIPO_OC_EX_H',10, 10, 'hidden'));
		$this->add_control($control = new edit_num('CANTIDAD',7,10));
		$control->set_onChange("monto_total();");
		$this->add_control($control = new edit_num('PRECIO',10,10,2));
		$control->set_onChange("monto_total();");
		$this->set_computed('TOTAL', '[CANTIDAD] * [PRECIO]', 2);
		$this->accumulate('TOTAL');
		$this->add_controls_producto_help();
		
		$js = $this->controls['COD_PRODUCTO']->get_onChange();
		$js =$js."equipo_oc_ex(this);";
		$this->controls['COD_PRODUCTO']->set_onChange($js);
				
		$this->controls['COD_PRODUCTO']->size = 16;
		$this->controls['NOM_PRODUCTO']->type = 'hidden';
		$this->set_first_focus('COD_PRODUCTO');
		
		// asigna los mandatorys
		$this->set_mandatory('ORDEN', 'Orden');
		$this->set_mandatory('CANTIDAD', 'Cantidad');
	}
	function insert_row($row=-1) {
		$row = parent::insert_row($row);
		$this->set_item($row, 'ORDEN', $this->row_count() * 10);
		$this->set_item($row, 'ITEM', $this->row_count() * 1);
		return $row;
	}
	function fill_template(&$temp) {
		parent::fill_template($temp);
		if ($this->entrable) {
			$agregar = '<img src="../../../../commonlib/trunk/images/b_add_line.jpg" onClick="add_line_item(\''.$this->label_record.'\', \''.$this->nom_tabla.'\');" style="cursor:pointer">';
			$temp->setVar("AGREGAR_".strtoupper($this->label_record), $agregar);
		}
	}
	function update($db, $cod_cx_cot_extranjera){
		$sp = 'spu_cx_item_cot_extranjera';
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
			
			$COD_CX_ITEM_COT_EXTRANJERA	= $this->get_item($i, 'COD_CX_ITEM_COT_EXTRANJERA');
			$ORDEN						= $this->get_item($i, 'ORDEN');
			$ITEM						= $this->get_item($i, 'ITEM');
			$COD_PRODUCTO				= $this->get_item($i, 'COD_PRODUCTO');
			$NOM_PRODUCTO				= $this->get_item($i, 'NOM_PRODUCTO');
			$COD_EQUIPO_OC_EX			= $this->get_item($i, 'COD_EQUIPO_OC_EX_H');
			$DESC_EQUIPO_OC_EX			= $this->get_item($i, 'DESC_EQUIPO_OC_EX_H');
			$CANTIDAD					= $this->get_item($i, 'CANTIDAD');
			$PRECIO						= $this->get_item($i, 'PRECIO');
			
			$COD_CX_ITEM_COT_EXTRANJERA	= ($COD_CX_ITEM_COT_EXTRANJERA =='') ? "null" : $COD_CX_ITEM_COT_EXTRANJERA;
			$COD_EQUIPO_OC_EX			= ($COD_EQUIPO_OC_EX =='') ? "null" : "'$COD_EQUIPO_OC_EX'";
			$DESC_EQUIPO_OC_EX			= ($DESC_EQUIPO_OC_EX =='') ? "null" : "'$DESC_EQUIPO_OC_EX'";
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
		
			$param = "'$operacion'
					  ,$COD_CX_ITEM_COT_EXTRANJERA
					  ,$cod_cx_cot_extranjera
					  ,$ORDEN
					  ,'$ITEM'
					  ,'$COD_PRODUCTO'
					  ,'$NOM_PRODUCTO'
					  ,$COD_EQUIPO_OC_EX
					  ,$DESC_EQUIPO_OC_EX
					  ,$CANTIDAD
					  ,$PRECIO";
			
			if (!$db->EXECUTE_SP($sp, $param))
				return false;
			else {
				if ($statuts == K_ROW_NEW_MODIFIED){
					$COD_CX_ITEM_COT_EXTRANJERA = $db->GET_IDENTITY();
					$this->set_item($i, 'COD_CX_ITEM_COT_EXTRANJERA', $COD_CX_ITEM_COT_EXTRANJERA);		
				}
			}
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$COD_CX_ITEM_COT_EXTRANJERA = $this->get_item($i, 'COD_CX_ITEM_COT_EXTRANJERA', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_CX_ITEM_COT_EXTRANJERA")){
				return false;				
			}			
		}
		return true;
	}
}
class wi_cx_cot_extranjera extends w_input {
	function wi_cx_cot_extranjera($cod_item_menu) {
		parent::w_input('cx_cot_extranjera', $cod_item_menu);

		$sql = "SELECT C.COD_CX_COT_EXTRANJERA			
						,CONVERT(VARCHAR, C.FECHA_CX_COT_EXTRANJERA, 103) FECHA_CX_COT_EXTRANJERA        
						,C.COD_USUARIO                    
						,U.NOM_USUARIO
						,C.CORRELATIVO_COT_EXTRANJERA     
						,C.COD_CX_ESTADO_COT_EXTRANJERA   
						,DBO.F_LAST_MOD('NOM_USUARIO', 'CX_COT_EXTRANJERA', 'COD_CX_ESTADO_COT_EXTRANJERA', C.COD_CX_COT_EXTRANJERA) NOM_USUARIO_CAMBIO
					    ,DBO.F_LAST_MOD('FECHA_CAMBIO', 'CX_COT_EXTRANJERA', 'COD_CX_ESTADO_COT_EXTRANJERA', C.COD_CX_COT_EXTRANJERA) FECHA_CAMBIO
						,P.ALIAS_PROVEEDOR_EXT
						,C.COD_PROVEEDOR_EXT
						,P.NOM_PROVEEDOR_EXT
						,P.ALIAS_PROVEEDOR_EXT ALIAS_PROVEEDOR_EXT_TEXT
						,C.COD_PROVEEDOR_EXT COD_PROVEEDOR_EXT_TEXT
						,P.NOM_PROVEEDOR_EXT NOM_PROVEEDOR_EXT_TEXT
						,P.DIRECCION
						,P.NOM_PAIS_4D NOM_PAIS
						,P.NOM_CIUDAD_4D NOM_CIUDAD
						,P.POST_OFFICE_BOX
						,C.COD_CX_CONTACTO_PROVEEDOR_EXT  
						,CC.TELEFONO
						,CC.MAIL
						,C.REFERENCIA                     
						,CONVERT(VARCHAR, C.DELIVERY_DATE, 103) DELIVERY_DATE                  
						,C.COD_CX_PUERTO_SALIDA  
						,PS.NOM_CX_PUERTO_SALIDA         
						,C.COD_CX_CLAUSULA_COMPRA   
						,CCO.NOM_CX_CLAUSULA_COMPRA      
						,C.COD_CX_PUERTO_ARRIBO           
						,C.COD_CX_MONEDA
						,M.NOM_CX_MONEDA                  
						,C.PACKING                        
						,C.COD_CX_TERMINO_PAGO            
						,C.OBSERVACIONES                  
						,C.MONTO_TOTAL
						,C.MONTO_TOTAL MONTO_TOTAL_H
						,CONVERT(VARCHAR, C.FECHA_REGISTRO, 103) FECHA_REGISTRO             
				FROM CX_COT_EXTRANJERA C
						,USUARIO U
						,PROVEEDOR_EXT P
						,CX_CONTACTO_PROVEEDOR_EXT CC
						,CX_MONEDA M
						,CX_CLAUSULA_COMPRA CCO
						,CX_PUERTO_SALIDA  PS
				WHERE C.COD_CX_COT_EXTRANJERA = {KEY1}
				  AND U.COD_USUARIO = C.COD_USUARIO
				  AND P.COD_PROVEEDOR_EXT = C.COD_PROVEEDOR_EXT
				  AND CC.COD_CX_CONTACTO_PROVEEDOR_EXT= C.COD_CX_CONTACTO_PROVEEDOR_EXT
				  AND C.COD_CX_MONEDA =  M.COD_CX_MONEDA
				  AND C.COD_CX_CLAUSULA_COMPRA = CCO.COD_CX_CLAUSULA_COMPRA
				  AND C.COD_CX_PUERTO_SALIDA = PS.COD_CX_PUERTO_SALIDA";
			
		$this->dws['wi_cx_cot_extranjera'] = new datawindow($sql);
		// asigna los formatos				
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text_upper('COD_CX_COT_EXTRANJERA', 20, 100));			
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('FECHA_CX_COT_EXTRANJERA'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('NOM_USUARIO'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text('CORRELATIVO_COT_EXTRANJERA',10,10));
		$sql = "SELECT 		COD_CX_ESTADO_COT_EXTRANJERA
							,NOM_CX_ESTADO_COT_EXTRANJERA
				FROM 		CX_ESTADO_COT_EXTRANJERA
				ORDER BY 	COD_CX_ESTADO_COT_EXTRANJERA";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_ESTADO_COT_EXTRANJERA', $sql, 165));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('NOM_USUARIO_CAMBIO'));
		$this->dws['wi_cx_cot_extranjera']->add_control($control = new edit_text('ALIAS_PROVEEDOR_EXT', 27, 27));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text('COD_PROVEEDOR_EXT',10,10));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('NOM_PROVEEDOR_EXT'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('DIRECCION'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('NOM_PAIS'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('NOM_CIUDAD'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('POST_OFFICE_BOX'));
		$control->set_onChange('help_proveedor(this);');
		
		$sql = "SELECT COD_CX_CONTACTO_PROVEEDOR_EXT
						,NOM_CONTACTO_PROVEEDOR_EXT
				 FROM CX_CONTACTO_PROVEEDOR_EXT
				 ORDER BY COD_CX_CONTACTO_PROVEEDOR_EXT";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_CONTACTO_PROVEEDOR_EXT', $sql, 165));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('TELEFONO'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_text('MAIL'));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text('REFERENCIA',100, 10));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_date('DELIVERY_DATE',10, 10));
		$sql = "SELECT COD_CX_PUERTO_SALIDA
						,NOM_CX_PUERTO_SALIDA
				FROM CX_PUERTO_SALIDA
				ORDER BY COD_CX_PUERTO_SALIDA";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_PUERTO_SALIDA', $sql, 165));
		$sql = "SELECT COD_CX_CLAUSULA_COMPRA
						,NOM_CX_CLAUSULA_COMPRA
				FROM CX_CLAUSULA_COMPRA
				ORDER BY COD_CX_CLAUSULA_COMPRA";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_CLAUSULA_COMPRA', $sql, 165));
		$sql = "SELECT COD_CX_PUERTO_ARRIBO
						,NOM_CX_PUERTO_ARRIBO
				FROM CX_PUERTO_ARRIBO
				ORDER BY COD_CX_PUERTO_ARRIBO";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_PUERTO_ARRIBO', $sql, 165));
		$sql = "SELECT COD_CX_MONEDA
						,NOM_CX_MONEDA
				FROM CX_MONEDA
				ORDER BY COD_CX_MONEDA";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_MONEDA', $sql, 165));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text('PACKING',10, 10));
		$sql = "SELECT COD_CX_TERMINO_PAGO 
						,NOM_CX_TERMINO_PAGO
				FROM CX_TERMINO_PAGO
				ORDER BY COD_CX_TERMINO_PAGO";
		$this->dws['wi_cx_cot_extranjera']->add_control(new drop_down_dw('COD_CX_TERMINO_PAGO', $sql, 165));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text_multiline('OBSERVACIONES', 100, 2));
		// asigna los mandatorys
		$this->dws['wi_cx_cot_extranjera']->set_mandatory('COD_CX_COT_EXTRANJERA', 'C�digo de Clausula');
		//auditoria
		$this->add_auditoria('COD_CX_COT_EXTRANJERA');
		
		////////////////////
		// tab items
		// DATAWINDOWS ITEMS COT_EXTRANJERA
		$this->dws['dw_item_cx_cot_extranjera'] = new dw_item_cx_cot_extranjera();
		
		///////Montos///////////////////
			
		$this->dws['wi_cx_cot_extranjera']->add_control(new static_num('MONTO_TOTAL',2));
		$this->dws['wi_cx_cot_extranjera']->add_control(new edit_text('MONTO_TOTAL_H',10, 10, 'hidden'));
		
	}
	
	function new_record() {
		parent::new_record();
		$this->dws['wi_cx_cot_extranjera']->insert_row();
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'FECHA_REGISTRO', $this->current_date());
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'FECHA_CX_COT_EXTRANJERA', $this->current_date());
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'COD_CX_ESTADO_COT_EXTRANJERA', 1);
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'MONTO_TOTAL', '0,00');
		$this->dws['wi_cx_cot_extranjera']->set_item(0, 'MONTO_TOTAL_H', '0,00');
		$this->dws['wi_cx_cot_extranjera']->set_entrable('COD_CX_COT_EXTRANJERA',false);
	}
	function load_record() {
		$cod_cx_cot_extranjera = $this->get_item_wo($this->current_record, 'COD_CX_COT_EXTRANJERA');
		$this->dws['wi_cx_cot_extranjera']->retrieve($cod_cx_cot_extranjera);
		$this->dws['dw_item_cx_cot_extranjera']->retrieve($cod_cx_cot_extranjera);
		$this->dws['wi_cx_cot_extranjera']->set_entrable('COD_CX_COT_EXTRANJERA',false);
	}
	function get_key(){
		return $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_COT_EXTRANJERA');
	}
	function save_record($db) {
		$COD_CX_COT_EXTRANJERA			= $this->get_key();
		$COD_CX_CLAUSULA_COMPRA			= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_CLAUSULA_COMPRA');
	 	$COD_PROVEEDOR_EXT				= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_PROVEEDOR_EXT');
	 	$COD_CX_CONTACTO_PROVEEDOR_EXT	= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_CONTACTO_PROVEEDOR_EXT');
	 	$COD_CX_MONEDA					= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_MONEDA');
	 	$COD_CX_PUERTO_ARRIBO			= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_PUERTO_ARRIBO');
	 	$COD_CX_PUERTO_SALIDA			= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_CLAUSULA_COMPRA');
		$COD_CX_TERMINO_PAGO			= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_PUERTO_SALIDA');
		$COD_USUARIO					= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_USUARIO');
		$COD_CX_ESTADO_COT_EXTRANJERA	= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'COD_CX_ESTADO_COT_EXTRANJERA');
		$CORRELATIVO_COT_EXTRANJERA		= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'CORRELATIVO_COT_EXTRANJERA');
		$DELIVERY_DATE					= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'DELIVERY_DATE');
		$OBSERVACIONES					= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'OBSERVACIONES');
		$PACKING						= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'PACKING');
		$REFERENCIA						= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'REFERENCIA');
		$FECHA_REGISTRO					= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'FECHA_REGISTRO');
		$FECHA_CX_COT_EXTRANJERA		= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'FECHA_CX_COT_EXTRANJERA');
		$MONTO_TOTAL					= $this->dws['wi_cx_cot_extranjera']->get_item(0, 'MONTO_TOTAL_H');
	 
	 
	 	$COD_CX_COT_EXTRANJERA			= ($COD_CX_COT_EXTRANJERA =='') ? "null" : "$COD_CX_COT_EXTRANJERA";
		$COD_CX_CLAUSULA_COMPRA			= ($COD_CX_CLAUSULA_COMPRA =='') ? "null" : "$COD_CX_CLAUSULA_COMPRA";
		$COD_PROVEEDOR_EXT				= ($COD_PROVEEDOR_EXT =='') ? "null" : "$COD_PROVEEDOR_EXT";
		$COD_CX_CONTACTO_PROVEEDOR_EXT	= ($COD_CX_CONTACTO_PROVEEDOR_EXT =='') ? "null" : "$COD_CX_CONTACTO_PROVEEDOR_EXT";
		$COD_CX_MONEDA					= ($COD_CX_MONEDA =='') ? "null" : "$COD_CX_MONEDA";
		$COD_CX_PUERTO_ARRIBO			= ($COD_CX_PUERTO_ARRIBO =='') ? "null" : "$COD_CX_PUERTO_ARRIBO";
		$COD_CX_PUERTO_SALIDA			= ($COD_CX_PUERTO_SALIDA =='') ? "null" : "$COD_CX_PUERTO_SALIDA";
		$COD_CX_TERMINO_PAGO			= ($COD_CX_TERMINO_PAGO =='') ? "null" : "$COD_CX_TERMINO_PAGO";
		$COD_USUARIO					= ($COD_USUARIO =='') ? "null" : "$COD_USUARIO";
		$COD_CX_ESTADO_COT_EXTRANJERA	= ($COD_CX_ESTADO_COT_EXTRANJERA =='') ? "null" : "$COD_CX_ESTADO_COT_EXTRANJERA";
		$CORRELATIVO_COT_EXTRANJERA		= ($CORRELATIVO_COT_EXTRANJERA =='') ? "null" : "$CORRELATIVO_COT_EXTRANJERA";
		$OBSERVACIONES					= ($OBSERVACIONES =='') ? "null" : "'$OBSERVACIONES'";
		$PACKING						= ($PACKING =='') ? "null" : "'$PACKING'";
		$REFERENCIA						= ($REFERENCIA =='') ? "null" : "'$REFERENCIA'";
		$MONTO_TOTAL					= number_format($MONTO_TOTAL,2,'.',',');
		
		$sp = 'spu_cx_cot_extranjera';
		if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
		
	    $param = "'$operacion'
	    		,$COD_CX_COT_EXTRANJERA
				,$COD_CX_CLAUSULA_COMPRA
				,$COD_PROVEEDOR_EXT
				,$COD_CX_CONTACTO_PROVEEDOR_EXT
				,$COD_CX_MONEDA
				,$COD_CX_PUERTO_ARRIBO
				,$COD_CX_PUERTO_SALIDA
				,$COD_CX_TERMINO_PAGO
				,$COD_USUARIO
				,$COD_CX_ESTADO_COT_EXTRANJERA
				,'$CORRELATIVO_COT_EXTRANJERA'
				,'$DELIVERY_DATE'
				,$OBSERVACIONES
				,$PACKING
				,$REFERENCIA
				,'$FECHA_REGISTRO'
				,'$FECHA_CX_COT_EXTRANJERA'
				,$MONTO_TOTAL";
				
		if ($db->EXECUTE_SP($sp, $param)){
			
			
			if ($this->is_new_record()) {
				$COD_CX_COT_EXTRANJERA = $db->GET_IDENTITY();
				$this->dws['wi_cx_cot_extranjera']->set_item(0, 'COD_CX_COT_EXTRANJERA', $COD_CX_COT_EXTRANJERA);
			}
			if (!$this->dws['dw_item_cx_cot_extranjera']->update($db, $COD_CX_COT_EXTRANJERA))
				return false;
								
			return true;
		}
		return false;
	}
	function print_record(){
		//////////////Temporal//////////////
		$cod_cx_cot_extranjera = $this->get_key();
		$sql = "SELECT C.COD_CX_COT_EXTRANJERA			
						,CONVERT (VARCHAR (20),C.FECHA_CX_COT_EXTRANJERA,103)  FECHA_CX_COT_EXTRANJERA      
						,C.COD_USUARIO                    
						,U.NOM_USUARIO
						,C.CORRELATIVO_COT_EXTRANJERA     
						,C.COD_CX_ESTADO_COT_EXTRANJERA   
						,DBO.F_LAST_MOD('NOM_USUARIO', 'CX_COT_EXTRANJERA', 'COD_CX_ESTADO_COT_EXTRANJERA', C.COD_CX_COT_EXTRANJERA) NOM_USUARIO_CAMBIO
					    ,DBO.F_LAST_MOD('FECHA_CAMBIO', 'CX_COT_EXTRANJERA', 'COD_CX_ESTADO_COT_EXTRANJERA', C.COD_CX_COT_EXTRANJERA) FECHA_CAMBIO
						,P.ALIAS_PROVEEDOR_EXT
						,C.COD_PROVEEDOR_EXT
						,P.NOM_PROVEEDOR_EXT
						,P.DIRECCION
						,P.NOM_PAIS_4D
						,P.NOM_CIUDAD_4D
						,P.POST_OFFICE_BOX
						,C.COD_CX_CONTACTO_PROVEEDOR_EXT  
						,CC.TELEFONO
						,CC.MAIL
						,C.REFERENCIA                     
						,C.DELIVERY_DATE                  
						,C.COD_CX_PUERTO_SALIDA           
						,CCOM.NOM_CX_CLAUSULA_COMPRA       
						,C.COD_CX_PUERTO_ARRIBO
						,CPS.NOM_CX_PUERTO_SALIDA           
						,CM.NOM_CX_MONEDA               
						,C.PACKING                        
						,C.COD_CX_TERMINO_PAGO            
						,C.OBSERVACIONES                  
						,C.MONTO_TOTAL
						,C.OBSERVACIONES                    
				FROM CX_COT_EXTRANJERA C
					,USUARIO U, PROVEEDOR_EXT P
					,CX_CONTACTO_PROVEEDOR_EXT CC
					,CX_MONEDA CM
					,CX_CLAUSULA_COMPRA CCOM
					,CX_PUERTO_SALIDA CPS
				WHERE C.COD_CX_COT_EXTRANJERA = $COD_CX_COT_EXTRANJERA
				  AND U.COD_USUARIO = C.COD_USUARIO
				  AND P.COD_PROVEEDOR_EXT = C.COD_PROVEEDOR_EXT
				  AND CC.COD_CX_CONTACTO_PROVEEDOR_EXT= C.COD_CX_CONTACTO_PROVEEDOR_EXT
				  AND CM.COD_CX_MONEDA=C.COD_CX_MONEDA
				  AND CCOM.COD_CX_CLAUSULA_COMPRA=C.COD_CX_CLAUSULA_COMPRA
				  AND CPS.COD_CX_PUERTO_SALIDA=C.COD_CX_PUERTO_SALIDA";
		////////////////////////////////////
		$file_name = $this->find_file('cx_cot_extranjera', 'cx_cot_extranjera.xml');
		$rpt = new informe_cot_extranjera($sql, $file_name, $labels, "Cotizaci�n Extranjera", 1);												
		$this->_load_record();
	}
}
?>