<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_cot_nv.php");
require_once(dirname(__FILE__)."/class_informe_cx_oc_extranjera.php");
require_once(dirname(__FILE__)."/class_informe_cx_oc_extranjera_es.php");
require_once(dirname(__FILE__)."/../cx_cot_extranjera/class_dw_help_empresa.php");

class dw_item_packing_oc extends datawindow {
    function dw_item_packing_oc() {
        $sql = "SELECT COD_CX_PACKING_OC_EXTRANJERA
					  ,COD_CX_OC_EXTRANJERA
					  ,NOM_CONTAINER
					  ,CANT
				FROM CX_PACKING_OC_EXTRANJERA
				WHERE COD_CX_OC_EXTRANJERA = {KEY1}";
        
        parent::datawindow($sql, 'ITEM_PACKING_OC', true, true);
        
        $sql = "SELECT '40 ST' NOM_CONTAINER
					  ,'40 ST' NOM_CONTAINER_LBL
				UNION
				SELECT '40 HQ' NOM_CONTAINER
					  ,'40 HQ' NOM_CONTAINER_LBL
				UNION
				SELECT '20 ST' NOM_CONTAINER
					  ,'20 ST' NOM_CONTAINER_LBL
				UNION
				SELECT 'LCL' NOM_CONTAINER
					  ,'LCL' NOM_CONTAINER_LBL";
        $this->add_control(new drop_down_dw('NOM_CONTAINER', $sql, 100));
        $this->add_control(new edit_num('CANT',10, 10));
    }
    
    function update($db){
        $sp = 'spu_cx_packing_oc_extranjera';
        for ($i = 0; $i < $this->row_count(); $i++){
            $statuts = $this->get_status_row($i);
            if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW)
                continue;
                
            $COD_CX_PACKING_OC_EXTRANJERA	= $this->get_item($i, 'COD_CX_PACKING_OC_EXTRANJERA');
            $COD_CX_OC_EXTRANJERA			= $this->get_item($i, 'COD_CX_OC_EXTRANJERA');
            $NOM_CONTAINER					= $this->get_item($i, 'NOM_CONTAINER');
            $CANT							= $this->get_item($i, 'CANT');
            
            $COD_CX_PACKING_OC_EXTRANJERA	= ($COD_CX_PACKING_OC_EXTRANJERA =='') ? "null" : $COD_CX_PACKING_OC_EXTRANJERA;
                
            if ($statuts == K_ROW_NEW_MODIFIED)
                $operacion = 'INSERT';
            elseif ($statuts == K_ROW_MODIFIED)
                $operacion = 'UPDATE';
                    
            $param = "'$operacion'
                    ,$COD_CX_PACKING_OC_EXTRANJERA
                    ,$COD_CX_OC_EXTRANJERA
                    ,'$NOM_CONTAINER'
                    ,$CANT";
                    
            if(!$db->EXECUTE_SP($sp, $param))
                return false;
            else{
                if ($statuts == K_ROW_NEW_MODIFIED){
                    $COD_CX_PACKING_OC_EXTRANJERA = $db->GET_IDENTITY();
                    $this->set_item($i, 'COD_CX_PACKING_OC_EXTRANJERA', $COD_CX_PACKING_OC_EXTRANJERA);
                }
            }
        }
        
        for ($i = 0; $i < $this->row_count('delete'); $i++) {
            $statuts = $this->get_status_row($i, 'delete');
            if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
                continue;
                
            $COD_CX_PACKING_OC_EXTRANJERA = $this->get_item($i, 'COD_CX_PACKING_OC_EXTRANJERA', 'delete');
            if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_CX_PACKING_OC_EXTRANJERA")){
                return false;
            }
        }
        return true;
    }
}

class dw_cx_orden_pago extends datawindow{
    function dw_cx_orden_pago(){
        $sql = "SELECT COD_CX_CARTA_OP
					  ,COD_CX_OC_EXTRANJERA
					  ,CONVERT(VARCHAR ,FECHA_CX_CARTA_OP, 103) FECHA_CX_CARTA_OP
					  ,FECHA_REGISTRO
					  ,PORC_PAGO
					  ,MONTO_PAGO
					  ,NOM_ATENCION
					  ,NOM_ATENCION_CC
					  ,REFERENCIA
					  ,C.COD_ESTADO_CX_CARTA_OP
					  ,E.NOM_ESTADO_CX_CARTA_OP
					  ,'N' ANULAR_CX_ORDEN_PAGO
				FROM CX_CARTA_OP C
					,ESTADO_CX_CARTA_OP E
				WHERE COD_CX_OC_EXTRANJERA = {KEY1}
				AND C.COD_ESTADO_CX_CARTA_OP = E.COD_ESTADO_CX_CARTA_OP";
        
        parent::datawindow($sql, 'CX_ORDEN_PAGO', true, true);
        
        //controles
        $this->add_control(new edit_check_box('ANULAR_CX_ORDEN_PAGO','S','N'));
        $this->add_control(new edit_text_hidden('COD_ESTADO_CX_CARTA_OP'));
        $this->add_control(new edit_text_hidden('COD_CX_CARTA_OP'));
        
        $this->set_protect('ANULAR_CX_ORDEN_PAGO', "[COD_ESTADO_CX_CARTA_OP]==2");
    }
    
    function fill_record(&$temp, $record) {
        parent::fill_record($temp, $record);
        $cod_estado				= $this->get_item($record, 'COD_ESTADO_CX_CARTA_OP');
        $cod_cx_carta_op		= $this->get_item($record, 'COD_CX_CARTA_OP');
        
        if($this->entrable && $cod_estado == 1){
            $detalle_lupa1 = "<input type=\"image\" id=\"b_detalle_$record\" onclick=\"dlg_agrega_cx_carta_op($cod_cx_carta_op, ''); return false;\" src=\"../../../../commonlib/trunk/images/lupa1.jpg\" name=\"b_detalle_$record\">";
            $detalle_lupa2 = "<input type=\"image\" id=\"b_detalle_$record\" onclick=\"dlg_agrega_cx_carta_op($cod_cx_carta_op, ''); return false;\" src=\"../../../../commonlib/trunk/images/lupa2.jpg\" name=\"b_detalle_$record\">";
        }else{
            $detalle_lupa1 = "";
            $detalle_lupa2 = "";
        }
        
        if($cod_estado <> 2){
            $impresion1 = "<input type=\"image\" id=\"b_impresion\" onclick=\"return document.getElementById('wi_hidden').value = $cod_cx_carta_op;\" src=\"../../images_appl/impresion1.jpg\" name=\"b_impresion\">";
            $impresion2 = "<input type=\"image\" id=\"b_impresion\" onclick=\"return document.getElementById('wi_hidden').value = $cod_cx_carta_op;\" src=\"../../images_appl/impresion2.jpg\" name=\"b_impresion\">";
        }else{
            $impresion1 = "";
            $impresion2 = "";
        }
        
        if($record % 2 == 0){
            $temp->setVar($this->label_record.'.DETALLE_CARTA_OP', $detalle_lupa1);
            $temp->setVar($this->label_record.'.IMPRIMIR_CARTA_OP', $impresion1);
        }else{
            $temp->setVar($this->label_record.'.DETALLE_CARTA_OP', $detalle_lupa2);
            $temp->setVar($this->label_record.'.IMPRIMIR_CARTA_OP', $impresion2);
        }
    }
    
    function update($db){
        $sp = 'spu_cx_orden_pago';
        
        for($i = 0; $i < $this->row_count('delete'); $i++){
            $statuts = $this->get_status_row($i, 'delete');
            if ($statuts == K_ROW_NEW || $statuts == K_ROW_NEW_MODIFIED)
                continue;
                
                $COD_CX_CARTA_OP = $this->get_item($i, 'COD_CX_CARTA_OP', 'delete');
                if (!$db->EXECUTE_SP($sp, "'DELETE', $COD_CX_CARTA_OP")){
                    return false;
                }
        }
        return true;
    }
}

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
                      ,PRECIO PRECIO_H
                      ,COD_TIPO_TE
    				  ,MOTIVO_TE
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
        
        $this->add_control(new edit_text('PRECIO_H',10, 10, 'hidden'));
        $this->add_control(new edit_text('COD_TIPO_TE',10, 100, 'hidden'));
        $this->add_control(new edit_text('MOTIVO_TE',10, 100, 'hidden'));
        
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
        
        $this->controls['COD_PRODUCTO']->size = 16;
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
                
                $COD_TIPO_TE			= $this->get_item($i, 'COD_TIPO_TE');
                $COD_TIPO_TE			= ($COD_TIPO_TE =='') ? "null" : "$COD_TIPO_TE";
                $MOTIVO_TE		 		= $this->get_item($i, 'MOTIVO_TE');
                $MOTIVO_TE		 		= ($MOTIVO_TE =='') ? "null" : "'".$MOTIVO_TE."'";
                
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
					  ,$PRECIO
                      ,$COD_TIPO_TE
                      ,$MOTIVO_TE";
                    
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
                        ,C.COD_CX_OC_EXTRANJERA COD_CX_OC_EXTRANJERA_L
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
						,CONVERT(TEXT,C.OBSERVACIONES) OBSERVACIONES
						,C.SUBTOTAL
						,C.SUBTOTAL SUBTOTAL_H
						,C.MONTO_EMBALAJE
						,C.MONTO_FLETE_INTERNO
						,C.PORC_DESCUENTO
						,C.MONTO_DESCUENTO
						,C.MONTO_TOTAL
						,C.MONTO_TOTAL MONTO_TOTAL_H
                        ,'none' MSG_ERR_CX_CARTA_OP
                        ,CONVERT(VARCHAR, C.DELIVERY_DATE, 103) DELIVERY_DATE_L
                        ,CPA.NOM_CX_PUERTO_ARRIBO NOM_CX_PUERTO_ARRIBO_L
                        ,CPS.NOM_CX_PUERTO_SALIDA NOM_CX_PUERTO_SALIDA_L
                        ,CONVERT(VARCHAR, C.ETA_DATE, 103) ETA_DATE
                        ,CONVERT(VARCHAR, C.FECHA_ZARPE, 103) FECHA_ZARPE
                        ,INCLUIR_CUADRO_EMBARQUE
                        ,INCLUIR_CUADRO_EMBARQUE INCLUIR_CUADRO_EMBARQUE_L
				FROM  CX_OC_EXTRANJERA C
                     ,USUARIO U
                     ,PROVEEDOR_EXT P
                     ,CX_CONTACTO_PROVEEDOR_EXT CC
                     ,CX_ESTADO_OC_EXTRANJERA CE
                     ,CX_PUERTO_SALIDA CPS
                     ,CX_PUERTO_ARRIBO CPA
				WHERE C.COD_CX_OC_EXTRANJERA		= {KEY1}
				AND CPA.COD_CX_PUERTO_ARRIBO		= C.COD_CX_PUERTO_ARRIBO
                AND CPS.COD_CX_PUERTO_SALIDA		= C.COD_CX_PUERTO_SALIDA
                AND C.COD_USUARIO					= U.COD_USUARIO
				AND C.COD_PROVEEDOR_EXT				= P.COD_PROVEEDOR_EXT
				AND C.COD_CX_CONTACTO_PROVEEDOR_EXT = CC.COD_CX_CONTACTO_PROVEEDOR_EXT
				AND CE.COD_CX_ESTADO_OC_EXTRANJERA = C.COD_CX_ESTADO_OC_EXTRANJERA";
        
        $this->dws['wi_cx_oc_extranjera'] = new dw_help_empresa($sql);
        $this->dws['dw_cx_item_oc_extranjera'] = new dw_cx_item_oc_extranjera();
        $this->dws['dw_cx_orden_pago'] = new dw_cx_orden_pago();
        $this->dws['dw_item_packing_oc'] = new dw_item_packing_oc();
        
        //controles
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_hidden('COD_CX_OC_EXTRANJERA'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_text('COD_USUARIO', 80, 80, 'hidden'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_upper('REFERENCIA', 96, 500));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_date('DELIVERY_DATE'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_date('ETA_DATE'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_date('FECHA_ZARPE'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_upper('PACKING', 27, 27));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_text_hidden('COD_PROVEEDOR_EXT'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_text('CORRELATIVO_OC', 10, 100));
        $this->dws['wi_cx_oc_extranjera']->controls['NOM_PROVEEDOR_EXT']->size = 59;
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_check_box('INCLUIR_CUADRO_EMBARQUE','S','N'));
        $this->dws['wi_cx_oc_extranjera']->add_control(new edit_check_box('INCLUIR_CUADRO_EMBARQUE_L','S','N'));
        $this->dws['wi_cx_oc_extranjera']->set_entrable('INCLUIR_CUADRO_EMBARQUE_L', false);
        
        $sql="SELECT COD_CX_ESTADO_OC_EXTRANJERA
					,NOM_CX_ESTADO_OC_EXTRANJERA
				FROM CX_ESTADO_OC_EXTRANJERA";
        
        $this->dws['wi_cx_oc_extranjera']->add_control(new drop_down_dw('COD_CX_ESTADO_OC_EXTRANJERA', $sql, 150));
        
        $sql = "SELECT COD_CX_CONTACTO_PROVEEDOR_EXT
						,NOM_CONTACTO_PROVEEDOR_EXT
				FROM CX_CONTACTO_PROVEEDOR_EXT
				WHERE COD_PROVEEDOR_EXT = {KEY1}
				ORDER BY COD_CX_CONTACTO_PROVEEDOR_EXT";
        $this->dws['wi_cx_oc_extranjera']->add_control($control = new drop_down_dw('COD_CX_CONTACTO_PROVEEDOR_EXT', $sql, 165));
        $control->set_onChange('registro_help_empresa();');
        $this->dws['wi_cx_oc_extranjera']->add_control(new static_text('MAIL'));
        
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
			  FROM CX_TERMINO_PAGO ORDER BY ORDEN ASC";
        
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
        $this->dws['wi_cx_oc_extranjera']->set_mandatory('COD_PROVEEDOR_EXT', 'Código del Proveedor');
        $this->dws['wi_cx_oc_extranjera']->set_mandatory('COD_CX_CONTACTO_PROVEEDOR_EXT', 'Código Contacto Proveedor');
        
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
        
        $cod_cx_oc_extranjera = session::get('COD_CX_OC_EXTRANJERA_CD');
        if($cod_cx_oc_extranjera != ''){
            $this->dws['wi_cx_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
            $this->dws['dw_cx_item_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
            $this->dws['wi_cx_oc_extranjera']->set_item(0, 'CORRELATIVO_OC', '');
            $this->dws['wi_cx_oc_extranjera']->set_item(0, 'CORRELATIVO_OC_L', '');
            session::un_set('COD_CX_OC_EXTRANJERA_CD');
        }
        
        $cod_cx_cot_extranjera = session::get('COD_CX_COT_EXTRANJERA_CD');
        if($cod_cx_cot_extranjera != ''){
            $this->create_from_cot($cod_cx_cot_extranjera);
            session::un_set('COD_CX_COT_EXTRANJERA_CD');
        }
        
    }
    
    function create_from_cot($cod_cotizacion_ext){
        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql = "SELECT CCE.COD_PROVEEDOR_EXT
					  ,ALIAS_PROVEEDOR_EXT
					  ,NOM_PROVEEDOR_EXT
					  ,NOM_CIUDAD_4D NOM_CIUDAD
					  ,NOM_PAIS_4D NOM_PAIS
					  ,TELEFONO
					  ,FAX
					  ,OBS
					  ,DIRECCION
					  ,POST_OFFICE_BOX
					  ,COD_CX_CONTACTO_PROVEEDOR_EXT
					  ,REFERENCIA
					  ,CONVERT(VARCHAR, DELIVERY_DATE, 103) DELIVERY_DATE
					  ,COD_CX_COT_EXTRANJERA
					  ,COD_CX_PUERTO_SALIDA
					  ,COD_CX_MONEDA
					  ,COD_CX_TERMINO_PAGO
					  ,COD_CX_PUERTO_ARRIBO
					  ,COD_CX_CLAUSULA_COMPRA
				FROM CX_COT_EXTRANJERA CCE
					,PROVEEDOR_EXT PE
				WHERE COD_CX_COT_EXTRANJERA = $cod_cotizacion_ext
				AND CCE.COD_PROVEEDOR_EXT = PE.COD_PROVEEDOR_EXT";
        $result = $db->build_results($sql);
        
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_PROVEEDOR_EXT',				$result[0]['COD_PROVEEDOR_EXT']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'ALIAS_PROVEEDOR_EXT',			$result[0]['ALIAS_PROVEEDOR_EXT']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'NOM_PROVEEDOR_EXT',				$result[0]['NOM_PROVEEDOR_EXT']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'NOM_CIUDAD',					$result[0]['NOM_CIUDAD']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'NOM_PAIS',						$result[0]['NOM_PAIS']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'TELEFONO',						$result[0]['TELEFONO']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'FAX',							$result[0]['FAX']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'DIRECCION',						$result[0]['DIRECCION']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'POST_OFFICE_BOX',				$result[0]['POST_OFFICE_BOX']);
        $this->dws['wi_cx_oc_extranjera']->controls['COD_CX_CONTACTO_PROVEEDOR_EXT']->retrieve($result[0]['COD_PROVEEDOR_EXT']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_CONTACTO_PROVEEDOR_EXT',	$result[0]['COD_CX_CONTACTO_PROVEEDOR_EXT']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'REFERENCIA',					$result[0]['REFERENCIA']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'DELIVERY_DATE',					$result[0]['DELIVERY_DATE']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'DELIVERY_DATE_L',				$result[0]['DELIVERY_DATE']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_COT_EXTRANJERA',			$result[0]['COD_CX_COT_EXTRANJERA']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_PUERTO_SALIDA',			$result[0]['COD_CX_PUERTO_SALIDA']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_MONEDA',					$result[0]['COD_CX_MONEDA']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_TERMINO_PAGO',			$result[0]['COD_CX_TERMINO_PAGO']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_PUERTO_ARRIBO',			$result[0]['COD_CX_PUERTO_ARRIBO']);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_CLAUSULA_COMPRA',		$result[0]['COD_CX_CLAUSULA_COMPRA']);
        /*INI CORRELATIVO DINAMICO*/
        $cod_provedor = $result[0]['COD_PROVEEDOR_EXT'];
        $sql = "SELECT ALIAS_PROVEEDOR_EXT,YEAR(GETDATE())ANO  FROM PROVEEDOR_EXT where COD_PROVEEDOR_EXT = $cod_provedor";
        $result = $db->build_results($sql);
        
        $ANO = $result[0]['ANO'];
        $ALIAS = $result[0]['ALIAS_PROVEEDOR_EXT'];
        
        $sql="select TOP 1 CX.CORRELATIVO_OC
        from CX_OC_EXTRANJERA CX
        where CX.COD_PROVEEDOR_EXT = $cod_provedor
        ORDER BY CX.COD_CX_OC_EXTRANJERA DESC";
        $result = $db->build_results($sql);
        
        $CORRELATIVO_STR = $result[0]['CORRELATIVO_OC'];
        
        if(!empty($CORRELATIVO_STR)){
            $arrOld = explode( '/', $CORRELATIVO_STR );
            $str = $arrOld[0];
            $anoOLd = $arrOld[1];
            
            $correlativo = substr($str, -2);
            $correlativo = trim($correlativo);
            $n_correlativo = (int)$correlativo + 1;
            if(trim($anoOLd) != $ANO){
                $n_correlativo = 1;
            }
        }else{
            $n_correlativo = 1;
        }
        
        $correlativo_new = $ALIAS.' '.$n_correlativo.'/'.$ANO;
        
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'CORRELATIVO_OC',	$correlativo_new);
        /*FIN CORRELATIVO DINAMICO*/
        
        $sql = "SELECT ORDEN
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
				WHERE COD_CX_COT_EXTRANJERA = $cod_cotizacion_ext";
        $result = $db->build_results($sql);
        
        $vl_subtotal = 0;
        for($i=0 ; $i < count($result) ; $i++){
            $this->dws['dw_cx_item_oc_extranjera']->insert_row();
            
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'ORDEN',				$result[$i]['ORDEN']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'ITEM',				$result[$i]['ITEM']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'COD_PRODUCTO',		$result[$i]['COD_PRODUCTO']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'NOM_PRODUCTO',		$result[$i]['NOM_PRODUCTO']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'COD_EQUIPO_OC_EX',	$result[$i]['COD_EQUIPO_OC_EX']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'DESC_EQUIPO_OC_EX',	$result[$i]['DESC_EQUIPO_OC_EX']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'COD_EQUIPO_OC_EX_H',	$result[$i]['COD_EQUIPO_OC_EX_H']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'DESC_EQUIPO_OC_EX_H',	$result[$i]['DESC_EQUIPO_OC_EX_H']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'CANTIDAD',			$result[$i]['CANTIDAD']);
            $this->dws['dw_cx_item_oc_extranjera']->set_item($i, 'PRECIO',				$result[$i]['PRECIO']);
            
            $vl_subtotal = $vl_subtotal + ($result[$i]['PRECIO'] * $result[$i]['CANTIDAD']);
        }
        
        $this->dws['dw_cx_item_oc_extranjera']->calc_computed();
        
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'SUBTOTAL_H',	$vl_subtotal);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'SUBTOTAL',		$vl_subtotal);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_TOTAL_H',	$vl_subtotal);
        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'MONTO_TOTAL',	$vl_subtotal);
        
        //dw_item_packing_oc
        $sql = "SELECT NOM_CONTAINER
                      ,CANT
                FROM CX_PACKING_COT_EXTRANJERA
                WHERE COD_CX_COT_EXTRANJERA = $cod_cotizacion_ext";
        $result = $db->build_results($sql);

        for($i=0 ; $i < count($result) ; $i++){
            $this->dws['dw_item_packing_oc']->insert_row();
            
            $this->dws['dw_item_packing_oc']->set_item($i, 'NOM_CONTAINER', $result[$i]['NOM_CONTAINER']);
            $this->dws['dw_item_packing_oc']->set_item($i, 'CANT',          $result[$i]['CANT']);
        }
    }
    
    function load_record() {
        $cod_cx_oc_extranjera = $this->get_item_wo($this->current_record, 'COD_CX_OC_EXTRANJERA');
        $this->dws['wi_cx_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
        $cod_proveedor_ext = $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_PROVEEDOR_EXT');
        $this->dws['wi_cx_oc_extranjera']->controls['COD_CX_CONTACTO_PROVEEDOR_EXT']->retrieve($cod_proveedor_ext);
        $this->dws['dw_cx_item_oc_extranjera']->retrieve($cod_cx_oc_extranjera);
        $this->dws['dw_cx_orden_pago']->retrieve($cod_cx_oc_extranjera);
        $this->dws['wi_cx_oc_extranjera']->set_entrable('COD_CX_ESTADO_OC_EXTRANJERA',true);
        $this->dws['dw_item_packing_oc']->retrieve($cod_cx_oc_extranjera);
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

        $it_confirmado = false;

        for ($i=0; $i < $this->dws['dw_cx_orden_pago']->row_count(); $i++){ 
            if($this->dws['dw_cx_orden_pago']->get_item($i, 'COD_ESTADO_CX_CARTA_OP') == 3){
                $it_confirmado = true;
                break;
            }
        }

        if($it_confirmado){
            $monto_total = $this->dws['wi_cx_oc_extranjera']->get_item(0, 'MONTO_TOTAL');
            $sum_monto_it = 0;

            for ($j=0; $j < $this->dws['dw_cx_orden_pago']->row_count(); $j++){
                if($this->dws['dw_cx_orden_pago']->get_item($j, 'COD_ESTADO_CX_CARTA_OP') == 3){
                    $sum_monto_it += $this->dws['dw_cx_orden_pago']->get_item($j, 'MONTO_PAGO');
                }
            }

            if($sum_monto_it > $monto_total)
                $this->dws['wi_cx_oc_extranjera']->set_item(0, 'MSG_ERR_CX_CARTA_OP', '');
        }
    }

    function habilita_boton_print(&$temp, $boton, $habilita) {
        if ($habilita){
            $ruta_over = "'../../../../commonlib/trunk/images/b_print_over.jpg'";
            $ruta_out = "'../../../../commonlib/trunk/images/b_print.jpg'";
            $ruta_click = "'../../../../commonlib/trunk/images/b_print_click.jpg'";
            $temp->setVar("WI_PRINT", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
                'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../../../commonlib/trunk/images/b_print.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
                'onClick="var vl_tab = document.getElementById(\'wi_current_tab_page\'); if (TabbedPanels1 && vl_tab) vl_tab.value =TabbedPanels1.getCurrentTabIndex();dlg_print();" />');
        }
        
    }
    function habilitar(&$temp, $habilita) {
        if($this->is_new_record())
            $this->habilita_boton_print($temp, 'print', false);
            else
                $this->habilita_boton_print($temp, 'print', true);
                
    }
    function get_key(){
        return $this->dws['wi_cx_oc_extranjera']->get_item(0, 'COD_CX_OC_EXTRANJERA');
    }
    function save_record($db){
        $COD_CX_OC_EXTRANJERA			= $this->get_key();
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
        $ETA_DATE                       = $this->dws['wi_cx_oc_extranjera']->get_item(0, 'ETA_DATE');
        $FECHA_ZARPE                    = $this->dws['wi_cx_oc_extranjera']->get_item(0, 'FECHA_ZARPE');
        $INCLUIR_CUADRO_EMBARQUE        = $this->dws['wi_cx_oc_extranjera']->get_item(0, 'INCLUIR_CUADRO_EMBARQUE');
        
        $COD_CX_OC_EXTRANJERA			= ($COD_CX_OC_EXTRANJERA =='') ? "null" : "$COD_CX_OC_EXTRANJERA";
        $FECHA_CX_OC_EXTRANJERA			= ($FECHA_CX_OC_EXTRANJERA =='') ? "null" : $this->str2date($FECHA_CX_OC_EXTRANJERA);
        $COD_CX_CLAUSULA_COMPRA			= ($COD_CX_CLAUSULA_COMPRA =='') ? "null" : "$COD_CX_CLAUSULA_COMPRA";
        $COD_CX_COT_EXTRANJERA			= ($COD_CX_COT_EXTRANJERA =='') ? "null" : "$COD_CX_COT_EXTRANJERA";
        $COD_CX_MONEDA					= ($COD_CX_MONEDA =='') ? "null" : "$COD_CX_MONEDA";
        $COD_CX_PUERTO_ARRIBO			= ($COD_CX_PUERTO_ARRIBO =='') ? "null" : "$COD_CX_PUERTO_ARRIBO";
        $COD_CX_PUERTO_SALIDA			= ($COD_CX_PUERTO_SALIDA =='') ? "null" : "$COD_CX_PUERTO_SALIDA";
        $COD_CX_TERMINO_PAGO			= ($COD_CX_TERMINO_PAGO =='') ? "null" : "$COD_CX_TERMINO_PAGO";
        $CORRELATIVO_OC					= ($CORRELATIVO_OC =='') ? "null" : "'$CORRELATIVO_OC'";
        $DELIVERY_DATE					= ($DELIVERY_DATE =='') ? "null" : $this->str2date($DELIVERY_DATE);
        $REFERENCIA						= ($REFERENCIA =='') ? "null" : "'$REFERENCIA'";
        $OBSERVACIONES					= ($OBSERVACIONES =='') ? "null" : "'$OBSERVACIONES'";
        $PACKING						= ($PACKING =='') ? "null" : "'$PACKING'";
        $ETA_DATE					    = ($ETA_DATE =='') ? "null" : $this->str2date($ETA_DATE);
        $FECHA_ZARPE					= ($FECHA_ZARPE =='') ? "null" : $this->str2date($FECHA_ZARPE);
        $INCLUIR_CUADRO_EMBARQUE		= ($INCLUIR_CUADRO_EMBARQUE =='') ? "null" : "'$INCLUIR_CUADRO_EMBARQUE'";
        
        $sp = 'spu_cx_oc_extranjera';
        if ($this->is_new_record())
            $operacion = 'INSERT';
            else
                $operacion = 'UPDATE';
                
                $param = "'$operacion'
	    		,$COD_CX_OC_EXTRANJERA
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
				,'$ALIAS'
                ,$ETA_DATE
                ,$FECHA_ZARPE
                ,$INCLUIR_CUADRO_EMBARQUE";
                
                if ($db->EXECUTE_SP($sp, $param)){
                    if ($this->is_new_record()) {
                        $COD_CX_OC_EXTRANJERA = $db->GET_IDENTITY();
                        $this->dws['wi_cx_oc_extranjera']->set_item(0, 'COD_CX_OC_EXTRANJERA', $COD_CX_OC_EXTRANJERA);
                    }
                    if (!$this->dws['dw_cx_item_oc_extranjera']->update($db, $COD_CX_OC_EXTRANJERA))
                        return false;
                        
                    for($i=0 ; $i < $this->dws['dw_cx_orden_pago']->row_count() ; $i++)
                        $this->dws['dw_cx_orden_pago']->set_item($i, 'COD_CX_OC_EXTRANJERA', $COD_CX_OC_EXTRANJERA);
                        
                    for ($j=0; $j < $this->dws['dw_item_packing_oc']->row_count(); $j++)
                        $this->dws['dw_item_packing_oc']->set_item($j, 'COD_CX_OC_EXTRANJERA', $COD_CX_OC_EXTRANJERA);
                                    
                    if (!$this->dws['dw_item_packing_oc']->update($db))
                        return false;
                            
                    if (!$this->dws['dw_cx_orden_pago']->update($db))
                        return false;
                                
                    return true;
                }
                return false;
    }
    
    function navegacion(&$temp){
        parent::navegacion($temp);
        
        if($this->modify && $this->is_new_record() == false)
            $temp->setVar("AGREGAR_CX_ITEM_ORDEN_PAGO", '<img style="cursor:pointer" onclick="dlg_agrega_cx_carta_op(\'\', \'none\');" src="../../../../commonlib/trunk/images/b_add_line.jpg">');
        else
            $temp->setVar("AGREGAR_CX_ITEM_ORDEN_PAGO", '<img src="../../../../commonlib/trunk/images/b_add_line_d.jpg">');
    }
    
    function print_record(){
        $tipo_print = $_POST['wi_hidden'];

        if($tipo_print == 'ORDEN_COMPRA_ES')
            $this->print_orden_compra_es();
        else
            $this->print_orden_compra();
    }
    
    function procesa_event() {
        if(isset($_POST['b_impresion_x']))
            $this->print_carta_op($_POST['wi_hidden']);
        else
            parent::procesa_event();
    }
    
    function print_carta_op($cod_carta_op){
        $sql="SELECT dbo.f_format_date(FECHA_CX_CARTA_OP, 3) FECHA_CX_CARTA_OP
					  ,NOM_ATENCION
					  ,NOM_ATENCION_CC
					  ,CCO.REFERENCIA
					  ,SUBSTRING(NOM_ATENCION, 0, CHARINDEX(' ', NOM_ATENCION))
					  ,dbo.number_format(MONTO_PAGO, 3, ',', '.') MONTO_PAGO
					  ,BENEFICIARY_NAMEEMP
					  ,BENEFICIARY_DIREMP
					  ,BENEFICIARY_NAMEBANK
					  ,BENEFICIARY_DIRBANK
					  ,BP_ACCOUNT_NUMBER
					  ,BP_SWIFT
					  ,MAIL
					  ,U.NOM_USUARIO
                      ,CCO.COD_ESTADO_CX_CARTA_OP
			  FROM CX_CARTA_OP CCO
				  ,CX_OC_EXTRANJERA COE
				  ,PROVEEDOR_EXT PE
				  ,USUARIO U
			  WHERE CCO.COD_CX_CARTA_OP = $cod_carta_op
			  AND CCO.COD_CX_OC_EXTRANJERA = COE.COD_CX_OC_EXTRANJERA
			  AND PE.COD_PROVEEDOR_EXT = COE.COD_PROVEEDOR_EXT
			  AND U.COD_USUARIO = COE.COD_USUARIO";
        
        $sql = base64_encode($sql);

        print " <script>window.open('informe_oc_cx_pago.php?sql=$sql')</script>";	    
        $this->_load_record();
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
						,c.COD_CX_COT_EXTRANJERA
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
						,CONVERT(TEXT,C.OBSERVACIONES) OBSERVACIONES
						,c.MONTO_TOTAL
						,CONVERT(TEXT,C.OBSERVACIONES) OBSERVACIONES
						,tp.NOM_CX_TERMINO_PAGO
						,pa.NOM_CX_PUERTO_ARRIBO
						,c.MONTO_FLETE_INTERNO
						,c.MONTO_EMBALAJE
						,c.MONTO_DESCUENTO
						,CAST(ROUND((c.MONTO_DESCUENTO * 100) / (c.MONTO_TOTAL + c.MONTO_DESCUENTO),2,1) as decimal(18,2)) PORCENTAJE_PO
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
        $rpt = new informe_oc_extranjera($sql, $file_name, $labels, "Purchase Order.pdf", 1);
        $this->_load_record();
    }

    function print_orden_compra_es(){
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
						,c.COD_CX_COT_EXTRANJERA
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
						,CONVERT(TEXT,C.OBSERVACIONES) OBSERVACIONES
						,c.MONTO_TOTAL
						,CONVERT(TEXT,C.OBSERVACIONES) OBSERVACIONES
						,tp.NOM_CX_TERMINO_PAGO
						,pa.NOM_CX_PUERTO_ARRIBO
						,c.MONTO_FLETE_INTERNO
						,c.MONTO_EMBALAJE
						,c.MONTO_DESCUENTO
						,CAST(ROUND((c.MONTO_DESCUENTO * 100) / (c.MONTO_TOTAL + c.MONTO_DESCUENTO),2,1) as decimal(18,2)) PORCENTAJE_PO
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

        $file_name = $this->find_file('cx_oc_extranjera', 'cx_oc_extranjera.xml');
        $rpt = new informe_oc_extranjera_es($sql, $file_name, $labels, "Purchase Order ES.pdf", 1);
        $this->_load_record();
    }
}
?>