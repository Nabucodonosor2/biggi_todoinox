<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_cot_nv.php");
require_once(dirname(__FILE__)."/class_informe_cx_oc_extranjera.php");
require_once(dirname(__FILE__)."/class_informe_oc_cx_carga.php");
require_once(dirname(__FILE__)."/class_informe_oc_cx_ins_cobertura.php");
require_once(dirname(__FILE__)."/class_informe_oc_cx_pago.php");

class dw_cx_item_oc_extranjera extends dw_item{
	function dw_cx_item_oc_extranjera() {
		$sql = "SELECT COD_CX_ITEM_OC_EXTRANJERA
					  ,ITEM
					  ,ORDEN
					  ,COD_PRODUCTO
					  ,NOM_PRODUCTO
					  ,COD_EQUIPO_OC_EX
					  ,DESC_EQUIPO_OC_EX
					  ,COD_EQUIPO_OC_EX		COD_EQUIPO_OC_EX_H
					  ,DESC_EQUIPO_OC_EX	DESC_EQUIPO_OC_EX_H
					  ,CANTIDAD
					  ,PRECIO 
				FROM CX_ITEM_OC_EXTRANJERA
				WHERE COD_CX_OC_EXTRANJERA = {KEY1}";
		
		parent::dw_item($sql, 'CX_ITEM_OC_EXTRANJERA', true, true, 'COD_PRODUCTO');
		
		//controles
		$this->add_control(new edit_text('COD_CX_ITEM_OC_EXTRANJERA',10, 10, 'hidden'));
		$this->add_control(new edit_num('ORDEN',4 ,10));
		$this->add_control(new edit_text('ITEM',4 ,5));
		$this->add_control(new static_text('COD_EQUIPO_OC_EX',10, 10));
		$this->add_control(new static_text('DESC_EQUIPO_OC_EX',10, 10));
		$this->add_control(new edit_text('COD_EQUIPO_OC_EX_H',10, 10, 'hidden'));
		$this->add_control(new edit_text('DESC_EQUIPO_OC_EX_H',10, 10, 'hidden'));
		$this->add_control($control = new edit_num('CANTIDAD',7,10));
		$control->set_onChange("subtotal();");
		$this->add_control($control = new edit_num('PRECIO',10,10,2));
		$control->set_onChange("subtotal();");
		$this->set_computed('TOTAL', '[CANTIDAD] * [PRECIO]', 2);
		$this->accumulate('TOTAL');
		$this->add_controls_producto_help();
		
		$js = $this->controls['COD_PRODUCTO']->get_onChange();
		$js =$js."equipo_oc_ex(this);";
		$this->controls['COD_PRODUCTO']->set_onChange($js);
				
		$this->controls['COD_PRODUCTO']->size = 20;
		$this->controls['NOM_PRODUCTO']->type = 'hidden';
		$this->set_first_focus('COD_PRODUCTO');
		
		//mandatory
		$this->set_mandatory('ORDEN','Orden');
		$this->set_mandatory('CANTIDAD','Cantidad');
		$this->set_mandatory('PRECIO','Precio');
	}
	function insert_row($row=-1){
		$row = parent::insert_row($row);
		$this->set_item($row, 'ORDEN', $this->row_count() * 10);
		$this->set_item($row, 'ITEM', $this->row_count() * 1);
		return $row;
	}
	function update($db, $cod_cx_oc_extranjera){
		$sp = 'spu_cx_item_oc_extranjera';
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);
			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
				continue;
			
			$COD_CX_ITEM_OC_EXTRANJERA	= $this->get_item($i, 'COD_CX_ITEM_OC_EXTRANJERA');
			$ORDEN						= $this->get_item($i, 'ORDEN');
			$ITEM						= $this->get_item($i, 'ITEM');
			$COD_PRODUCTO				= $this->get_item($i, 'COD_PRODUCTO');
			$NOM_PRODUCTO				= $this->get_item($i, 'NOM_PRODUCTO');
			$COD_EQUIPO_OC_EX			= $this->get_item($i, 'COD_EQUIPO_OC_EX_H');
			$DESC_EQUIPO_OC_EX			= $this->get_item($i, 'DESC_EQUIPO_OC_EX_H');
			$CANTIDAD					= $this->get_item($i, 'CANTIDAD');
			$PRECIO						= $this->get_item($i, 'PRECIO');
			
			$COD_CX_ITEM_OC_EXTRANJERA	= ($COD_CX_ITEM_OC_EXTRANJERA =='') ? "null" : $COD_CX_ITEM_OC_EXTRANJERA;
			$COD_EQUIPO_OC_EX			= ($COD_EQUIPO_OC_EX =='') ? "null" : "'$COD_EQUIPO_OC_EX'";
			$DESC_EQUIPO_OC_EX			= ($DESC_EQUIPO_OC_EX =='') ? "null" : "'$DESC_EQUIPO_OC_EX'";
			
			if ($statuts == K_ROW_NEW_MODIFIED)
				$operacion = 'INSERT';
			elseif ($statuts == K_ROW_MODIFIED)
				$operacion = 'UPDATE';
		
			$param = "'$operacion'
					  ,$COD_CX_ITEM_OC_EXTRANJERA
					  ,$cod_cx_oc_extranjera
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
					$COD_CX_ITEM_OC_EXTRANJERA = $db->GET_IDENTITY();
					$this->set_item($i, 'COD_CX_ITEM_OC_EXTRANJERA', $COD_CX_ITEM_OC_EXTRANJERA);		
				}
			}
		}
		
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$statuts = $this->get_status_row($i, 'delete');
			if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
				continue;
				
			$COD_CX_ITEM_OC_EXTRANJERA = $this->get_item($i, 'COD_CX_ITEM_OC_EXTRANJERA', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_CX_ITEM_OC_EXTRANJERA")){
				return false;				
			}			
		}
		return true;
	}
}

class wi_cx_oc_extranjera extends w_input{
	const K_ESTADO_CX_EMITIDA = 1;
	const K_ESTADO_CX_ANULADA = 2;	
	
	function wi_cx_oc_extranjera($cod_item_menu){
		parent::w_input('cx_oc_extranjera', $cod_item_menu);

		$sql = "SELECT  C.COD_CX_OC_EXTRANJERA
						,CONVERT(VARCHAR, C.FECHA_REGISTRO, 103) FECHA_REGISTRO
						,CONVERT(VARCHAR,(C.FECHA_CX_OC_EXTRANJERA), 103) FECHA_CX_OC_EXTRANJERA
						,CONVERT(VARCHAR,(C.FECHA_CX_OC_EXTRANJERA), 103) FECHA_CX_OC_EXTRANJERA_L
						,C.COD_USUARIO                    
						,U.NOM_USUARIO
						,C.CORRELATIVO_OC
						,C.CORRELATIVO_OC CORRELATIVO_OC_L
						,C.COD_CX_ESTADO_OC_EXTRANJERA
						,CE.NOM_CX_ESTADO_OC_EXTRANJERA
						,P.ALIAS_PROVEEDOR_EXT
						,C.COD_PROVEEDOR_EXT
						,C.COD_CX_COT_EXTRANJERA
						,P.NOM_PROVEEDOR_EXT
						,P.DIRECCION
						,P.NOM_PAIS_4D	NOM_PAIS
						,P.NOM_CIUDAD_4D NOM_CIUDAD
						,P.POST_OFFICE_BOX
						,C.COD_CX_CONTACTO_PROVEEDOR_EXT  
						,CC.TELEFONO
						,CC.MAIL
						,CC.FAX
						,C.REFERENCIA                     
						,CONVERT(VARCHAR, C.DELIVERY_DATE, 103) DELIVERY_DATE                  
						,C.COD_CX_PUERTO_SALIDA           
						,C.COD_CX_CLAUSULA_COMPRA         
						,C.COD_CX_PUERTO_ARRIBO           
						,C.COD_CX_MONEDA                  
						,C.PACKING                        
						,C.COD_CX_TERMINO_PAGO            
						,C.OBSERVACIONES
						,C.SUBTOTAL
						,C.SUBTOTAL SUBTOTAL_H
						,C.MONTO_EMBALAJE
						,C.MONTO_FLETE_INTERNO
						,C.PORC_DESCUENTO
						,C.MONTO_DESCUENTO                  
						,C.MONTO_TOTAL
						,C.MONTO_TOTAL MONTO_TOTAL_H 
				FROM  CX_OC_EXTRANJERA C, USUARIO U, PROVEEDOR_EXT P, CX_CONTACTO_PROVEEDOR_EXT CC,
					  CX_ESTADO_OC_EXTRANJERA CE
				WHERE C.COD_CX_OC_EXTRANJERA		= {KEY1}
				AND C.COD_USUARIO					= U.COD_USUARIO
				AND C.COD_PROVEEDOR_EXT				= P.COD_PROVEEDOR_EXT
				AND C.COD_CX_CONTACTO_PROVEEDOR_EXT = CC.COD_CX_CONTACTO_PROVEEDOR_EXT
				AND CE.COD_CX_ESTADO_OC_EXTRANJERA = C.COD_CX_ESTADO_OC_EXTRANJERA";
		
		$this->dws['wi_cx_oc_extranjera'] = new datawindow($sql);
		$this->dws['dw_cx_item_oc_extranjera'] = new dw_cx_item_oc_extranjera();
		
		//controles
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_text('COD_USUARIO', 80, 80, 'hidden'));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_upper('REFERENCIA', 96, 100));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_date('DELIVERY_DATE'));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_upper('PACKING', 27, 27));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_num('COD_PROVEEDOR_EXT', 10, 10));
		
		///////Temporal///////
		$this->dws['wi_cx_oc_extranjera']->add_control($control = new edit_text('ALIAS_PROVEEDOR_EXT', 27, 27));
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_text('DIRECCION', 27, 27));
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_text('NOM_PROVEEDOR_EXT', 27, 27));
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_text('NOM_PAIS', 27, 27));
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_text('NOM_CIUDAD', 27, 27));
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_text('POST_OFFICE_BOX', 27, 27));
		$control->set_onChange('help_proveedor(this);');
		//////////////////////
		
		$sql="SELECT COD_CX_ESTADO_OC_EXTRANJERA
					,NOM_CX_ESTADO_OC_EXTRANJERA 
				FROM CX_ESTADO_OC_EXTRANJERA";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_ESTADO_OC_EXTRANJERA', $sql, 150));
		
		$sql="SELECT COD_CX_CONTACTO_PROVEEDOR_EXT
					,NOM_CONTACTO_PROVEEDOR_EXT 
			  FROM CX_CONTACTO_PROVEEDOR_EXT";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_CONTACTO_PROVEEDOR_EXT', $sql, 150));
		
		$sql="SELECT COD_CX_PUERTO_SALIDA
					,NOM_CX_PUERTO_SALIDA
			  FROM CX_PUERTO_SALIDA";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_PUERTO_SALIDA', $sql, 150));
		
		$sql="SELECT COD_CX_CLAUSULA_COMPRA
					 ,NOM_CX_CLAUSULA_COMPRA
			  FROM CX_CLAUSULA_COMPRA";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_CLAUSULA_COMPRA', $sql, 150));
		
		$sql="SELECT COD_CX_PUERTO_ARRIBO
					,NOM_CX_PUERTO_ARRIBO 
			  FROM CX_PUERTO_ARRIBO";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_PUERTO_ARRIBO', $sql, 150));
		
		$sql="SELECT COD_CX_MONEDA
					,NOM_CX_MONEDA 
			  FROM CX_MONEDA";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_MONEDA', $sql, 150));
		
		$sql="SELECT COD_CX_TERMINO_PAGO
					,NOM_CX_TERMINO_PAGO 
			  FROM CX_TERMINO_PAGO";
		
		$this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_TERMINO_PAGO', $sql, 150));
		
		$this->dws['wi_cx_oc_extranjera']->add_control($control = new edit_num('MONTO_EMBALAJE', 10, 10, 2));
		$control->set_onChange("monto_total();");
		$this->dws['wi_cx_oc_extranjera']->add_control($control = new edit_num('MONTO_FLETE_INTERNO', 10, 10, 2));
		$control->set_onChange("monto_total();");
		$this->dws['wi_cx_oc_extranjera']->add_control($control = new edit_num('MONTO_DESCUENTO', 10, 10, 2));
		$control->set_onChange("monto_total();");
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_num('SUBTOTAL',2));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_text('SUBTOTAL_H',10, 10, 'hidden'));
		$this->dws['wi_cx_oc_extranjera']->add_control(new static_num('MONTO_TOTAL',2));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_text('MONTO_TOTAL_H',10, 10, 'hidden'));
		$this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_multiline('OBSERVACIONES', 100, 2));
		
		//mandatorys
		$this->dws['wi_cx_oc_extranjera']->set_mandatory('COD_CX_ESTADO_OC_EXTRANJERA', 'Estado');
		$this->dws['wi_cx_oc_extranjera']->set_mandatory('COD_PROVEEDOR_EXT', 'C�digo del Proveedor');
		$this->dws['wi_cx_oc_extranjera']->set_mandatory('COD_CX_CONTACTO_PROVEEDOR_EXT', 'C�digo Contacto Proveedor');
		
	}
	
	function new_record(){
		parent::new_record();
		$this->dws['wi_cx_oc_extranjera']->insert_row();
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'FECHA_REGISTRO', $this->current_date());
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'FECHA_CX_OC_EXTRANJERA', $this->current_date());
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_ESTADO_OC_EXTRANJERA', self::K_ESTADO_CX_EMITIDA);
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'SUBTOTAL', '0,00');
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'SUBTOTAL_H', '0.00');
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_EMBALAJE', '0,00');
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_FLETE_INTERNO', '0,00');
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_TOTAL', '0,00');
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_TOTAL_H', '0.00');
		$this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_DESCUENTO', '0,00');
		$this->dws['wi_cx_oc_extranjera']->set_entrable('COD_CX_ESTADO_OC_EXTRANJERA',false);
		
		session::is_set('COD_CX_OC_EXTRANJERA_CD');
		$cod_cx_oc_extranjera = session::get('COD_CX_OC_EXTRANJERA_CD');
		if($cod_cx_oc_extranjera != ''){
			$this->dws['wi_cx_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
			$this->dws['dw_cx_item_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
			$this->dws['wi_cx_oc_extranjera']->set_item(0, 'CORRELATIVO_OC', '');
			$this->dws['wi_cx_oc_extranjera']->set_item(0, 'CORRELATIVO_OC_L', '');
		}	
		session::un_set('COD_CX_OC_EXTRANJERA_CD');
		
		return;
	}
	function load_record() {
		$cod_cx_oc_extranjera = $this->get_item_wo($this->current_record, 'COD_CX_OC_EXTRANJERA');
		$this->dws['wi_cx_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
		$this->dws['dw_cx_item_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
		$this->dws['wi_cx_oc_extranjera']->set_entrable('COD_CX_ESTADO_OC_EXTRANJERA',true);
		$COD_CX_ESTADO_OC_EXTRANJERA = $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_ESTADO_OC_EXTRANJERA');
		
		$this->b_print_visible 	 = true;
		$this->b_no_save_visible = true;
		$this->b_save_visible 	 = true;
		$this->b_modify_visible	 = true;
		
		if($COD_CX_ESTADO_OC_EXTRANJERA == self::K_ESTADO_CX_ANULADA){
			$this->b_print_visible 	 = false;
			$this->b_no_save_visible = false;
			$this->b_save_visible 	 = false;
			$this->b_modify_visible	 = false;
		}
		
	}
	function get_key(){
		return $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_OC_EXTRANJERA');
	}
	function save_record($db){
		$COD_CX_OC_EXTRANJERA			= $this->get_key();
		$FECHA_REGISTRO					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'FECHA_REGISTRO');
		$FECHA_CX_OC_EXTRANJERA			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'FECHA_CX_OC_EXTRANJERA');
		$COD_CX_ESTADO_OC_EXTRANJERA	= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_ESTADO_OC_EXTRANJERA');
		$COD_PROVEEDOR_EXT				= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_PROVEEDOR_EXT');
		$COD_CX_CONTACTO_PROVEEDOR_EXT	= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_CONTACTO_PROVEEDOR_EXT');
		$COD_CX_CLAUSULA_COMPRA			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_CLAUSULA_COMPRA');
		$COD_CX_COT_EXTRANJERA			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_COT_EXTRANJERA');
		$COD_CX_MONEDA					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_MONEDA');
		$COD_CX_PUERTO_ARRIBO			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_PUERTO_ARRIBO');
		$COD_CX_PUERTO_SALIDA			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_PUERTO_SALIDA');
		$COD_CX_TERMINO_PAGO			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_TERMINO_PAGO');
		$COD_USUARIO					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_USUARIO');
		$CORRELATIVO_OC					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'CORRELATIVO_OC');
		$DELIVERY_DATE					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'DELIVERY_DATE');
		$REFERENCIA						= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'REFERENCIA');
		$OBSERVACIONES					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'OBSERVACIONES');
		$PACKING						= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'PACKING');
		$SUBTOTAL						= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'SUBTOTAL_H');
		$MONTO_EMBALAJE					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'MONTO_EMBALAJE');
		$MONTO_FLETE_INTERNO			= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'MONTO_FLETE_INTERNO');
		$PORC_DESCUENTO					= '0.00';	//no usan porcentaje
		$MONTO_DESCUENTO				= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'MONTO_DESCUENTO');
		$MONTO_TOTAL					= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'MONTO_TOTAL_H');
		$ALIAS							= $this->dws['wi_cx_oc_extranjera']->get_item(0, 'ALIAS_PROVEEDOR_EXT');
		
		$COD_CX_OC_EXTRANJERA			= ($COD_CX_OC_EXTRANJERA =='') ? "null" : "$COD_CX_OC_EXTRANJERA";
		$FECHA_CX_OC_EXTRANJERA			= ($FECHA_CX_OC_EXTRANJERA =='') ? "null" : "'$FECHA_CX_OC_EXTRANJERA'";
		$COD_CX_CLAUSULA_COMPRA			= ($COD_CX_CLAUSULA_COMPRA =='') ? "null" : "$COD_CX_CLAUSULA_COMPRA";
		$COD_CX_COT_EXTRANJERA			= ($COD_CX_COT_EXTRANJERA =='') ? "null" : "$COD_CX_COT_EXTRANJERA";
		$COD_CX_MONEDA					= ($COD_CX_MONEDA =='') ? "null" : "$COD_CX_MONEDA";
		$COD_CX_PUERTO_ARRIBO			= ($COD_CX_PUERTO_ARRIBO =='') ? "null" : "$COD_CX_PUERTO_ARRIBO";
		$COD_CX_PUERTO_SALIDA			= ($COD_CX_PUERTO_SALIDA =='') ? "null" : "$COD_CX_PUERTO_SALIDA";
		$COD_CX_TERMINO_PAGO			= ($COD_CX_TERMINO_PAGO =='') ? "null" : "$COD_CX_TERMINO_PAGO";
		$CORRELATIVO_OC					= ($CORRELATIVO_OC =='') ? "null" : "'$CORRELATIVO_OC'";
		$DELIVERY_DATE					= ($DELIVERY_DATE =='') ? "null" : "'$DELIVERY_DATE'";
		$REFERENCIA						= ($REFERENCIA =='') ? "null" : "'$REFERENCIA'";
		$OBSERVACIONES					= ($OBSERVACIONES =='') ? "null" : "'$OBSERVACIONES'";
		$PACKING						= ($PACKING =='') ? "null" : "'$PACKING'";

		$sp = 'spu_cx_oc_extranjera';
		if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
		
	    $param = "'$operacion'
	    		,$COD_CX_OC_EXTRANJERA
				,'$FECHA_REGISTRO'
				,$FECHA_CX_OC_EXTRANJERA
				,$COD_CX_ESTADO_OC_EXTRANJERA
				,$COD_PROVEEDOR_EXT
				,$COD_CX_CONTACTO_PROVEEDOR_EXT
				,$COD_CX_CLAUSULA_COMPRA
				,$COD_CX_COT_EXTRANJERA
				,$COD_CX_MONEDA
				,$COD_CX_PUERTO_ARRIBO
				,$COD_CX_PUERTO_SALIDA
				,$COD_CX_TERMINO_PAGO
				,$COD_USUARIO
				,$CORRELATIVO_OC
				,$DELIVERY_DATE
				,$REFERENCIA
				,$OBSERVACIONES
				,$PACKING
				,$SUBTOTAL
				,$MONTO_EMBALAJE
				,$MONTO_FLETE_INTERNO
				,$PORC_DESCUENTO
				,$MONTO_DESCUENTO
				,$MONTO_TOTAL
				,'$ALIAS'";
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$COD_CX_OC_EXTRANJERA = $db->GET_IDENTITY();
				$this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_OC_EXTRANJERA', $COD_CX_OC_EXTRANJERA);
			}
			if (!$this->dws['dw_cx_item_oc_extranjera']->update($db, $COD_CX_OC_EXTRANJERA))
				return false;
								
			return true;
		}
		return false;
	}
	
	function print_record(){
		$operacion = $_POST['wi_hidden'];
		
		if($operacion == 'ORDEN_COMPRA')
			$this->print_orden_compra();
		else if($operacion == 'ORDEN_PAGO')
			$this->print_orden_pago();
		else if($operacion == 'INS_COBERTURA')
			$this->print_ins_cobertura();
		else if($operacion == 'ORDEN_CARGA')
			$this->print_orden_carga();		
	}
	
	function print_orden_compra(){
		//////////////Temporal//////////////
		$cod_cx_oc_extranjera = $this->get_key();
		
		$sql = "select c.COD_CX_OC_EXTRANJERA			
						,CONVERT (varchar (20),c.FECHA_CX_OC_EXTRANJERA,103)  FECHA_CX_OC_EXTRANJERA      
						,c.COD_USUARIO                    
						,u.NOM_USUARIO
						,c.CORRELATIVO_OC    
						,c.COD_CX_ESTADO_OC_EXTRANJERA   
						,dbo.f_last_mod('NOM_USUARIO', 'CX_OC_EXTRANJERA', 'COD_CX_ESTADO_OC_EXTRANJERA', c.COD_CX_OC_EXTRANJERA) NOM_USUARIO_CAMBIO
					    ,dbo.f_last_mod('FECHA_CAMBIO', 'CX_OC_EXTRANJERA', 'COD_CX_ESTADO_OC_EXTRANJERA', c.COD_CX_OC_EXTRANJERA) FECHA_CAMBIO
						,p.ALIAS_PROVEEDOR_EXT
						,c.COD_PROVEEDOR_EXT
						,p.NOM_PROVEEDOR_EXT
						,p.DIRECCION
						,p.NOM_PAIS_4D
						,p.NOM_CIUDAD_4D
						,p.POST_OFFICE_BOX
						,P.TELEFONO TELEFONO_PROVEEDOR
						,P.FAX FAX_PROVEEDOR
						,c.COD_CX_CONTACTO_PROVEEDOR_EXT  
						,cc.TELEFONO
						,cc.MAIL
						,cc.FAX
						,c.REFERENCIA
						,CONVERT (varchar (20),c.DELIVERY_DATE,103)  DELIVERY_DATE                              
						,c.COD_CX_PUERTO_SALIDA           
						,ccom.NOM_CX_CLAUSULA_COMPRA       
						,c.COD_CX_PUERTO_ARRIBO
						,cps.NOM_CX_PUERTO_SALIDA           
						,cm.NOM_CX_MONEDA               
						,c.PACKING                        
						,c.COD_CX_TERMINO_PAGO            
						,c.OBSERVACIONES                  
						,c.MONTO_TOTAL
						,c.OBSERVACIONES
						,tp.NOM_CX_TERMINO_PAGO
						,pa.NOM_CX_PUERTO_ARRIBO
						,c.MONTO_FLETE_INTERNO
						,c.MONTO_EMBALAJE
						,c.MONTO_DESCUENTO                    
				from CX_OC_EXTRANJERA c
					,USUARIO u, PROVEEDOR_EXT p
					,CX_CONTACTO_PROVEEDOR_EXT cc
					,CX_MONEDA cm
					,CX_CLAUSULA_COMPRA ccom
					,CX_PUERTO_SALIDA cps
					,CX_TERMINO_PAGO tp
					,CX_PUERTO_ARRIBO pa
				where c.COD_CX_OC_EXTRANJERA = $cod_cx_oc_extranjera
				  and u.COD_USUARIO = c.COD_USUARIO
				  and p.COD_PROVEEDOR_EXT = c.COD_PROVEEDOR_EXT
				  and cc.COD_CX_CONTACTO_PROVEEDOR_EXT= c.COD_CX_CONTACTO_PROVEEDOR_EXT
				  and cm.COD_CX_MONEDA=c.COD_CX_MONEDA
				  and ccom.COD_CX_CLAUSULA_COMPRA=c.COD_CX_CLAUSULA_COMPRA
				  and cps.COD_CX_PUERTO_SALIDA=c.COD_CX_PUERTO_SALIDA
				  and c.COD_CX_TERMINO_PAGO=tp.COD_CX_TERMINO_PAGO
				  and c.COD_CX_PUERTO_ARRIBO=pa.COD_CX_PUERTO_ARRIBO";
		////////////////////////////////////
		
		$file_name = $this->find_file('cx_oc_extranjera', 'cx_oc_extranjera.xml');
		$rpt = new informe_oc_extranjera($sql, $file_name, $labels, "Purchase Order", 1);												
		$this->_load_record();
	}
	
	function print_orden_pago(){
		//////////Temporal///////////
		$sql="SELECT COD_CX_OC_EXTRANJERA FROM CX_OC_EXTRANJERA";
		
		$file_name = $this->find_file('cx_oc_extranjera', 'cx_oc_cx_pago.xml');
		$rpt = new informe_oc_cx_pago($sql, $file_name, $labels, "Orden Pago", 1);												
		$this->_load_record();
		/////////////////////////////
	}
	
	function print_ins_cobertura(){
		//////////Temporal///////////
		$sql="SELECT COD_CX_OC_EXTRANJERA FROM CX_OC_EXTRANJERA";
		
		$file_name = $this->find_file('cx_oc_extranjera', 'cx_oc_ins_cobertura.xml');
		$rpt = new informe_oc_cx_ins_cobertura($sql, $file_name, $labels, "Ins. Cobertura", 1);												
		$this->_load_record();
		/////////////////////////////
	}
	
	function print_orden_carga(){
		//////////Temporal///////////
		$sql="SELECT COD_CX_OC_EXTRANJERA FROM CX_OC_EXTRANJERA";
		
		$file_name = $this->find_file('cx_oc_extranjera', 'cx_oc_cx_carga.xml');
		$rpt = new informe_oc_cx_carga($sql, $file_name, $labels, "Orden Carga", 1);												
		$this->_load_record();
		/////////////////////////////
	}
}
?>