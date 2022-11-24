<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../../common_appl/class_w_cot_nv.php");
require_once(dirname(__FILE__)."/../../empresa/class_dw_help_empresa.php");

class wi_cotizacion extends wi_cotizacion_base {
	const K_AUTORIZA_MODI_DESC  = '990520';
	
	function wi_cotizacion($cod_item_menu) {
		parent::w_cot_nv('cotizacion', $cod_item_menu);
	
		$sql = "select	 C.COD_COTIZACION
						,convert(varchar(20), C.FECHA_COTIZACION, 103) FECHA_COTIZACION
						,C.COD_USUARIO
						,U.NOM_USUARIO
						,C.COD_USUARIO_VENDEDOR1
						,C.PORC_VENDEDOR1
						,C.COD_USUARIO_VENDEDOR2
						,C.PORC_VENDEDOR2
						,IDIOMA
						,C.REFERENCIA
						,COD_MONEDA
						,C.COD_ESTADO_COTIZACION
						,EC.NOM_ESTADO_COTIZACION
						,COD_ORIGEN_COTIZACION
						,COD_COTIZACION_DESDE
						,C.COD_EMPRESA
						,C.COD_EMPRESA	COD_EMPRESA_H
						,E.ALIAS
						,'' ETIQUETA_DESCT1
						,'' ETIQUETA_DESCT2
						,'none' VISIBLE
						,0 MONTO_DSCTO1_H
						,0 MONTO_DSCTO2_H
						,E.RUT
						,E.DIG_VERIF
						,E.NOM_EMPRESA
						,E.GIRO
						,case E.SUJETO_A_APROBACION
							when 'S' then 'SUJETO A APROBACION'
							else ''
						end SUJETO_A_APROBACION
						,C.COD_FORMA_PAGO
						,C.NOM_FORMA_PAGO_OTRO
						,COD_COTIZACION_DESDE
						,C.COD_SUCURSAL_FACTURA
						,SUMAR_ITEMS
						,C.SUBTOTAL SUM_TOTAL
						,C.PORC_DSCTO1
						,C.MONTO_DSCTO1
						,C.INGRESO_USUARIO_DSCTO1
						,C.PORC_DSCTO2
						,C.MONTO_DSCTO2
						,C.INGRESO_USUARIO_DSCTO2
						,C.TOTAL_NETO
						,C.PORC_IVA
						,C.MONTO_IVA
						,C.TOTAL_CON_IVA
						,dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_FACTURA						
						,COD_SUCURSAL_DESPACHO
						,dbo.f_get_direccion('SUCURSAL', COD_SUCURSAL_DESPACHO, '[DIRECCION]  [NOM_COMUNA] [NOM_CIUDAD] - TELEFONO: [TELEFONO] - FAX: [FAX]') DIRECCION_DESPACHO
						,C.COD_PERSONA
						,dbo.f_emp_get_mail_cargo_persona(C.COD_PERSONA,  '[EMAIL] - [NOM_CARGO]') MAIL_CARGO_PERSONA
						,VALIDEZ_OFERTA
						,ENTREGA
						,C.COD_EMBALAJE_COTIZACION
						,C.COD_FLETE_COTIZACION
						,C.COD_INSTALACION_COTIZACION
						,GARANTIA
						,C.OBS
						,POSIBILIDAD_CIERRE
						,FECHA_POSIBLE_CIERRE
						,dbo.f_get_parametro(".self::K_PARAM_PORC_DSCTO_MAX.") PORC_DSCTO_MAX
						,C.NOM_FORMA_PAGO_OTRO
						,FECHA_REGISTRO_COTIZACION
						,'' TIPO_DISPOSITIVO
						,'' DISPLAY_EMPRESA
						,F.NRO_FACTURA
						,F.COD_FACTURA
						,C.DSCTO_CLIENTE_ORIGINAL
				from 	COTIZACION C LEFT OUTER JOIN FACTURA F ON F.COD_COTIZACION = C.COD_COTIZACION
													,USUARIO U, EMPRESA E, ESTADO_COTIZACION EC
				where	C.COD_COTIZACION = {KEY1} and
						U.COD_USUARIO = C.COD_USUARIO AND
						E.COD_EMPRESA = C.COD_EMPRESA AND
						EC.COD_ESTADO_COTIZACION = C.COD_ESTADO_COTIZACION";


		////////////////////
		// tab Cotizacion
		// DATAWINDOWS COTIZACION
		$this->dws['dw_cotizacion'] = new dw_help_empresa($sql);
		$this->dws['dw_cotizacion']->add_control(new edit_text_hidden('COD_FACTURA'));	
		$this->dws['dw_cotizacion']->add_control(new edit_text_hidden('COD_EMPRESA_H'));
		$this->dws['dw_cotizacion']->add_control(new edit_text_hidden('DSCTO_CLIENTE_ORIGINAL'));	
		
		$this->dws['dw_cotizacion']->add_control(new edit_nro_doc('COD_COTIZACION','COTIZACION'));
		$this->dws['dw_cotizacion']->add_control(new edit_text('COD_USUARIO',10, 10, 'hidden'));
		
		$this->add_controls_cot_nv();
		$this->dws['dw_cotizacion']->add_control(new static_text('TIPO_DISPOSITIVO'));
		$this->dws['dw_cotizacion']->add_control($control = new drop_down_list('IDIOMA',array('E','I'),array('ESPA�OL','INGLES'),150));
		$this->dws['dw_cotizacion']->set_entrable('IDIOMA', false);
		
		$this->dws['dw_cotizacion']->add_control(new static_text('NOM_ESTADO_COTIZACION'));
		$sql_origen  			= "select 			COD_ORIGEN_COTIZACION
													,NOM_ORIGEN_COTIZACION,
													ORDEN
									from 			ORIGEN_COTIZACION
									order by 		ORDEN";
		$this->dws['dw_cotizacion']->add_control(new drop_down_dw('COD_ORIGEN_COTIZACION',$sql_origen,150));
		$this->dws['dw_cotizacion']->add_control(new static_text('COD_COTIZACION_DESDE'));
		$this->dws['dw_cotizacion']->add_control(new static_text('ETIQUETA_DESCT1'));
		$this->dws['dw_cotizacion']->add_control(new static_text('ETIQUETA_DESCT2'));
		
		// asigna los mandatorys
		$this->dws['dw_cotizacion']->set_mandatory('COD_ESTADO_COTIZACION', 'un Estado');
		$this->dws['dw_cotizacion']->set_mandatory('COD_ORIGEN_COTIZACION', 'un Origen');
		//$this->dws['dw_cotizacion']->add_control(new edit_porcentaje('PORC_VENDEDOR1',2,2,1));

		////////////////////
		// tab items
		// DATAWINDOWS ITEMS COTIZACION
		$this->dws['dw_item_cotizacion'] = new dw_item_cotizacion();

		// TOTALES
		$this->dws['dw_cotizacion']->add_control(new edit_check_box('SUMAR_ITEMS','S','N'));

		////////////////////
		// tab Condiciones generales
		// CONDICIONES GENERALES
		$sql_forma_pago			= "	select 			COD_FORMA_PAGO
													,NOM_FORMA_PAGO
													,ORDEN
						   			from			FORMA_PAGO
						   			order by  		ORDEN";
		
		$this->dws['dw_cotizacion']->add_control($control = new drop_down_dw('COD_FORMA_PAGO', $sql_forma_pago, 180));
		$control->set_onChange('mostrarOcultar(this);');
		$this->dws['dw_cotizacion']->add_control(new edit_text_upper('NOM_FORMA_PAGO_OTRO',132, 100));
		
		$this->dws['dw_cotizacion']->add_control(new edit_num('VALIDEZ_OFERTA',2,2));
		$this->dws['dw_cotizacion']->add_control(new edit_text_upper('ENTREGA',180,100));
		$sql_embalaje_cot 		= "	select 			COD_EMBALAJE_COTIZACION
													,NOM_EMBALAJE_COTIZACION
						   			from			EMBALAJE_COTIZACION
						   			order by  		NOM_EMBALAJE_COTIZACION asc";
		$this->dws['dw_cotizacion']->add_control(new drop_down_dw('COD_EMBALAJE_COTIZACION',$sql_embalaje_cot,740));
		$sql_flete_cot 			= "	select 			COD_FLETE_COTIZACION
													,NOM_FLETE_COTIZACION
													,ORDEN
						  			 from			FLETE_COTIZACION
						  			order by  		ORDEN";
		$this->dws['dw_cotizacion']->add_control(new drop_down_dw('COD_FLETE_COTIZACION',$sql_flete_cot,740));
		$sql_ins_cot 			= "	select 			COD_INSTALACION_COTIZACION
													,NOM_INSTALACION_COTIZACION
													,ORDEN
						  			from			INSTALACION_COTIZACION
						   			order by  		ORDEN";
		$this->dws['dw_cotizacion']->add_control(new drop_down_dw('COD_INSTALACION_COTIZACION',$sql_ins_cot,740));
		$this->dws['dw_cotizacion']->add_control(new edit_text_upper('GARANTIA',180,100));
		$this->dws['dw_cotizacion']->add_control(new edit_text_multiline('OBS',54,4));

		//auditoria Solicitado por IS.
		$this->add_auditoria('COD_USUARIO_VENDEDOR1');
		$this->add_auditoria('PORC_VENDEDOR1');
		$this->add_auditoria('COD_USUARIO_VENDEDOR2');
		$this->add_auditoria('PORC_VENDEDOR2');
		$this->add_auditoria('COD_ESTADO_COTIZACION');
		$this->add_auditoria('COD_EMPRESA');
		$this->add_auditoria('COD_SUCURSAL_FACTURA');
		$this->add_auditoria('COD_SUCURSAL_DESPACHO');
		$this->add_auditoria('COD_PERSONA');
		$this->add_auditoria('PORC_DSCTO1');
		$this->add_auditoria('PORC_DSCTO2');
		$this->add_auditoria('MONTO_DSCTO1');
		$this->add_auditoria('MONTO_DSCTO2');
		
		
		// asigna los mandatorys
		$this->dws['dw_cotizacion']->set_mandatory('COD_FORMA_PAGO', 'Forma de Pago');
		$this->dws['dw_cotizacion']->set_mandatory('VALIDEZ_OFERTA', 'Validez Oferta');
		$this->dws['dw_cotizacion']->set_mandatory('ENTREGA', 'Entrega');
		$this->dws['dw_cotizacion']->set_mandatory('COD_EMBALAJE_COTIZACION', 'Embalaje');
		$this->dws['dw_cotizacion']->set_mandatory('COD_FLETE_COTIZACION', 'Flete');
		$this->dws['dw_cotizacion']->set_mandatory('COD_INSTALACION_COTIZACION', 'Instalaci�n');
		$this->dws['dw_cotizacion']->set_mandatory('GARANTIA', 'Garant�a');
	
		$sql_estado  			= "	select 			COD_ESTADO_COTIZACION
													,NOM_ESTADO_COTIZACION
													,ORDEN
									from 			ESTADO_COTIZACION
									order by 		ORDEN";
		$this->dws['dw_cotizacion']->add_control(new drop_down_dw('COD_ESTADO_COTIZACION',$sql_estado, 150));

		$this->dws['dw_cotizacion']->set_mandatory('COD_ESTADO_COTIZACION', 'un Estado');

		$this->set_first_focus('REFERENCIA');
		
		$this->dws['dw_cotizacion']->add_control(new edit_text('PORC_DSCTO_MAX',10, 10, 'hidden'));
		
		
	}
	function load_record(){
		parent::load_record();
		$cod_factura = $this->dws['dw_cotizacion']->get_item(0, 'COD_FACTURA');
		$this->dws['dw_cotizacion']->add_control(new static_link('NRO_FACTURA', '../../../../commonlib/trunk/php/link_wi.php?modulo_origen=cotizacion&modulo_destino=factura&cod_modulo_destino='.$cod_factura.'&cod_item_menu=1535&current_tab_page=0'));
		
		$os = base::get_tipo_dispositivo();
			if($os == 'IPAD' ){
	            $this->dws['dw_cotizacion']->set_item(0,'TIPO_DISPOSITIVO', 'IPAD');
			}
		$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODI_DESC, $this->cod_usuario);

		if ($priv <> 'E') {
			$this->dws['dw_cotizacion']->controls['MONTO_DSCTO1']->readonly = true;
			$this->dws['dw_cotizacion']->controls['MONTO_DSCTO2']->readonly = true;
		}	
		$this->dws['dw_cotizacion']->set_entrable('RUT', false);
		$this->dws['dw_cotizacion']->set_entrable('ALIAS', false);
		$this->dws['dw_cotizacion']->set_entrable('COD_EMPRESA', false);
		$this->dws['dw_cotizacion']->set_entrable('NOM_EMPRESA', false);
		$this->dws['dw_cotizacion']->set_item(0, 'DISPLAY_EMPRESA', 'none');	
	}
	function new_record(){
		parent::new_record();
		
		$priv = $this->get_privilegio_opcion_usuario(self::K_AUTORIZA_MODI_DESC, $this->cod_usuario);

		if ($priv <> 'E') {
			$this->dws['dw_cotizacion']->controls['MONTO_DSCTO1']->readonly = true;
			$this->dws['dw_cotizacion']->controls['MONTO_DSCTO2']->readonly = true;
		}
	}
	function load_cotizacion($cod_cotizacion) {
		parent::load_cotizacion($cod_cotizacion);
	}
	function save_record($db) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$usuario = session::get("COD_USUARIO");
		
		$item_menu = '990520';
		$sql_perfil = "SELECT COD_PERFIL
					     FROM USUARIO
						WHERE COD_USUARIO =$usuario";
		$result_perfil = $db->build_results($sql_perfil);
		$perfil = 	$result_perfil[0]['COD_PERFIL'];
		
		$sql_autoriza = "SELECT AUTORIZA_MENU
					FROM AUTORIZA_MENU
					WHERE COD_PERFIL = $perfil
					AND COD_ITEM_MENU =$item_menu";
		$result_autoriza = $db->build_results($sql_autoriza);					
		$autoriza_menu  = 	$result_autoriza[0]['AUTORIZA_MENU'];
		
		
		$cod_cotizacion 		= $this->get_key();
		$fecha_cotizacion		= $this->dws['dw_cotizacion']->get_item(0, 'FECHA_COTIZACION');
		$cod_usuario 			= $this->dws['dw_cotizacion']->get_item(0, 'COD_USUARIO');
		$cod_usuario_vend1 		= $this->dws['dw_cotizacion']->get_item(0, 'COD_USUARIO_VENDEDOR1');
		$dscto_cliente_original = $this->dws['dw_cotizacion']->get_item(0, 'DSCTO_CLIENTE_ORIGINAL');
		
		
		$porc_vendedor1		= $this->dws['dw_cotizacion']->get_item(0, 'PORC_VENDEDOR1');
		$cod_usuario_vend2 	= $this->dws['dw_cotizacion']->get_item(0, 'COD_USUARIO_VENDEDOR2');
		if ($cod_usuario_vend2 =='') {
			$cod_usuario_vend2	= "null";
			$porc_vendedor2		= "null";
		}
		else
			$porc_vendedor2		= $this->dws['dw_cotizacion']->get_item(0, 'PORC_VENDEDOR2');	
		
		$cod_moneda			= $this->dws['dw_cotizacion']->get_item(0, 'COD_MONEDA');
		$idioma			 	= $this->dws['dw_cotizacion']->get_item(0, 'IDIOMA');
		$referencia			= $this->dws['dw_cotizacion']->get_item(0, 'REFERENCIA');
		$referencia 		= str_replace("'", "''", $referencia);
		$cod_est_cot		= $this->dws['dw_cotizacion']->get_item(0, 'COD_ESTADO_COTIZACION');
		$cod_ori_cot		= $this->dws['dw_cotizacion']->get_item(0, 'COD_ORIGEN_COTIZACION');
		$cod_cot_desde		= $this->dws['dw_cotizacion']->get_item(0, 'COD_COTIZACION_DESDE');
		$cod_cot_desde		= ($cod_cot_desde =='') ? "null" : "$cod_cot_desde";
		$cod_empresa		= $this->dws['dw_cotizacion']->get_item(0, 'COD_EMPRESA');
		$cod_suc_despacho	= $this->dws['dw_cotizacion']->get_item(0, 'COD_SUCURSAL_DESPACHO');
		$cod_suc_factura	= $this->dws['dw_cotizacion']->get_item(0, 'COD_SUCURSAL_FACTURA');
		$cod_persona		= $this->dws['dw_cotizacion']->get_item(0, 'COD_PERSONA');
		$cod_persona		= ($cod_persona =='') ? "null" : "$cod_persona";
		$sumar_items		= $this->dws['dw_cotizacion']->get_item(0, 'SUMAR_ITEMS');

		$sub_total			= $this->dws['dw_cotizacion']->get_item(0, 'SUM_TOTAL');
		$sub_total      	= ($sub_total =='') ? 0 : "$sub_total";

		$porc_descto1		= $this->dws['dw_cotizacion']->get_item(0, 'PORC_DSCTO1');
		$porc_descto1		= ($porc_descto1 =='') ? "null" : "$porc_descto1";
		if($autoriza_menu <> 'E'){
		$monto_dscto1		= $this->dws['dw_cotizacion']->get_item(0, 'MONTO_DSCTO1_H');
		$monto_dscto1		= ($monto_dscto1 =='') ? 0 : "$monto_dscto1";
		
		$monto_dscto2		= $this->dws['dw_cotizacion']->get_item(0, 'MONTO_DSCTO2_H');
		$monto_dscto2		= ($monto_dscto2 =='') ? 0 : "$monto_dscto2";
			
		}else{
		$monto_dscto1		= $this->dws['dw_cotizacion']->get_item(0, 'MONTO_DSCTO1');
		$monto_dscto1		= ($monto_dscto1 =='') ? 0 : "$monto_dscto1";
		
		$monto_dscto2		= $this->dws['dw_cotizacion']->get_item(0, 'MONTO_DSCTO2');
		$monto_dscto2		= ($monto_dscto2 =='') ? 0 : "$monto_dscto2";	
		}
		

		$porc_descto2		= $this->dws['dw_cotizacion']->get_item(0, 'PORC_DSCTO2');
		$porc_descto2		= ($porc_descto2 =='') ? "null" : "$porc_descto2";

		$total_neto			= $this->dws['dw_cotizacion']->get_item(0, 'TOTAL_NETO');
		$total_neto			= ($total_neto =='') ? 0 : "$total_neto";

		$porc_iva			= $this->dws['dw_cotizacion']->get_item(0, 'PORC_IVA');

		$monto_iva			= $this->dws['dw_cotizacion']->get_item(0, 'MONTO_IVA');
		$monto_iva			= ($monto_iva =='') ? 0 : "$monto_iva";

		$total_con_iva		= $this->dws['dw_cotizacion']->get_item(0, 'TOTAL_CON_IVA');
		$total_con_iva		= ($total_con_iva =='') ? 0 : "$total_con_iva";

		$cod_forma_pago		= $this->dws['dw_cotizacion']->get_item(0, 'COD_FORMA_PAGO');
		if ($cod_forma_pago==1){ // forma de pago = OTRO
			$nom_forma_pago_otro= $this->dws['dw_cotizacion']->get_item(0, 'NOM_FORMA_PAGO_OTRO');
		}else{
			$nom_forma_pago_otro= "";
		}
		$nom_forma_pago_otro= ($nom_forma_pago_otro =='') ? "null" : "'$nom_forma_pago_otro'";
		$validez_oferta		= $this->dws['dw_cotizacion']->get_item(0, 'VALIDEZ_OFERTA');
		$entrega			= $this->dws['dw_cotizacion']->get_item(0, 'ENTREGA');
		$entrega 			= str_replace("'", "''", $entrega);
		$cod_embalaje_cot	= $this->dws['dw_cotizacion']->get_item(0, 'COD_EMBALAJE_COTIZACION');
		$cod_flete_cot		= $this->dws['dw_cotizacion']->get_item(0, 'COD_FLETE_COTIZACION');
		$cod_inst_cot		= $this->dws['dw_cotizacion']->get_item(0, 'COD_INSTALACION_COTIZACION');
		$garantia			= $this->dws['dw_cotizacion']->get_item(0, 'GARANTIA');
		$garantia 			= str_replace("'", "''", $garantia);
		$obs				= $this->dws['dw_cotizacion']->get_item(0, 'OBS');
		$obs	 			= str_replace("'", "''", $obs);
		$obs				= ($obs =='') ? "null" : "'$obs'";
		$posib_cierre		= 1;//$this->dws['dw_cotizacion']->get_item(0, 'POSIBILIDAD_CIERRE');
		$fec_posib_cierre	= '01/12/2009';	// NOTA: para el manejo de fecha se debe pasar un string dd/mm/yyyy y en el sp llamar a to_date ber eje en spi_orden_trabajo
		$ing_usuario_dscto1	= $this->dws['dw_cotizacion']->get_item(0, 'INGRESO_USUARIO_DSCTO1');
		$ing_usuario_dscto1	= ($ing_usuario_dscto1 =='') ? "null" : "'$ing_usuario_dscto1'";
		$ing_usuario_dscto2	= $this->dws['dw_cotizacion']->get_item(0, 'INGRESO_USUARIO_DSCTO2');
		$ing_usuario_dscto2	= ($ing_usuario_dscto2 =='') ? "null" : "'$ing_usuario_dscto2'";
		$dscto_cliente_original = ($dscto_cliente_original =='') ? "null" : "$dscto_cliente_original";
		
		$cod_cotizacion = ($cod_cotizacion=='') ? "null" : $cod_cotizacion;		
    
		$sp = 'spu_cotizacion';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
		$param	= "		'$operacion'
						,$cod_cotizacion
						,'$fecha_cotizacion'
						,$cod_usuario
						,$cod_usuario_vend1
						,$porc_vendedor1
						,$cod_usuario_vend2
						,$porc_vendedor2
						,$cod_moneda
						,'$idioma'
						,'$referencia'
						,$cod_est_cot
						,$cod_ori_cot
						,$cod_cot_desde
						,$cod_empresa
						,$cod_suc_despacho
						,$cod_suc_factura
						,$cod_persona
						,'$sumar_items'
						,$sub_total
						,$porc_descto1
						,$monto_dscto1
						,$porc_descto2
						,$monto_dscto2
						,$total_neto
						,$porc_iva
						,$monto_iva
						,$total_con_iva
						,$cod_forma_pago
						,$validez_oferta
						,'$entrega'
						,$cod_embalaje_cot
						,$cod_flete_cot
						,$cod_inst_cot
						,'$garantia'
						,$obs
						,$posib_cierre
						,'$fec_posib_cierre'
						,$ing_usuario_dscto1
						,$ing_usuario_dscto2
						,$nom_forma_pago_otro
						,$dscto_cliente_original
					";
						
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_cotizacion = $db->GET_IDENTITY();
				$this->dws['dw_cotizacion']->set_item(0, 'COD_COTIZACION', $cod_cotizacion);
				/*
				VMC, 7-01-2011
				se elimina el envio de mail cuando se cotiza a un cliente no asignado 

				$this->envia_mail_acuse();
				*/				
			}
			for ($i=0; $i<$this->dws['dw_item_cotizacion']->row_count(); $i++)
				$this->dws['dw_item_cotizacion']->set_item($i, 'COD_COTIZACION', $cod_cotizacion);

			if (!$this->dws['dw_item_cotizacion']->update($db))
				return false;
				
			$parametros_sp = "'item_cotizacion','cotizacion',$cod_cotizacion";
			if (!$db->EXECUTE_SP('sp_orden_no_parametricas', $parametros_sp))
				return false;
				
			$parametros_sp = "'RECALCULA',$cod_cotizacion";	
			if (!$db->EXECUTE_SP('spu_cotizacion', $parametros_sp))
				return false;
			return true;			
		}
		return false;
	}
	function print_record() {
		$sel_print_cot = $_POST['wi_hidden'];
		$print_cot = explode("|", $sel_print_cot);
		switch ($print_cot[0]) {
    	case "resumen":
				if($print_cot[2] == 'pdf')
					$this->printcot_resumen_pdf($print_cot[3] == 'logo');
				else
					$this->printcot_resumen_excel($print_cot[3] == 'logo');
       	break;
    	case "ampliada":
				if($print_cot[2] == 'pdf')
					$this->printcot_ampliada_pdf($print_cot[3] == 'logo');
				else
					$this->printcot_ampliada_excel($print_cot[3] == 'logo');
       break;
    	case "pesomedida":
				if($print_cot[2] == 'pdf')
					$this->printcot_pesomedida_pdf($print_cot[3] == 'logo', $print_cot[4]);
				else
					$this->printcot_pesomedida_excel($print_cot[3] == 'logo');
      	break;
    	case "tecnica":
    		$lista_tecnica = explode("�", $print_cot[1]);
    		$tope = count($lista_tecnica);
    		for ($i = 0; $i < $tope; $i++){
    			switch ($lista_tecnica[$i]) {
    				case "electrico":
    					if($print_cot[2] == 'pdf')
								$this->printcot_tecnica_electrico_pdf($print_cot[3] == 'logo');
							else
								$this->printcot_tecnica_electrico_excel($print_cot[3] == 'logo');
      				break;
    				case "gas":
    					if($print_cot[2] == 'pdf')
								$this->printcot_tecnica_gas_pdf($print_cot[3] == 'logo');
							else
								$this->printcot_tecnica_gas_excel($print_cot[3] == 'logo');
    					break;
    				case "vapor":
    				 	if($print_cot[2] == 'pdf')
								$this->printcot_tecnica_vapor_pdf($print_cot[3] == 'logo');
							else
								$this->printcot_tecnica_vapor_excel($print_cot[3] == 'logo');
    					break;
    				case "agua":
    					if($print_cot[2] == 'pdf')
								$this->printcot_tecnica_agua_pdf($print_cot[3] == 'logo');
							else
								$this->printcot_tecnica_agua_excel($print_cot[3] == 'logo');
    					break;
    				case "ventilacion":
    					if($print_cot[2] == 'pdf')
								$this->printcot_tecnica_ventilacion_pdf($print_cot[3] == 'logo');
							else
								$this->printcot_tecnica_ventilacion_excel($print_cot[3] == 'logo');
    					break;
    				case "desague":
    					if($print_cot[2] == 'pdf')
								$this->printcot_tecnica_desague_pdf($print_cot[3] == 'logo');
							else
								$this->printcot_tecnica_desague_excel($print_cot[3] == 'logo');
    					break;
    			}
    		}
        break;
		}
		$this->redraw();
	}
	/*
	FUNCIONES PARA IMPRIMIR COTIZACIONES RESUMEN AMPLIADA PESO Y MEDIDA
	*/
	
	function printcot_resumen_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
						
		$sql = "SELECT	C.COD_COTIZACION,
				E.NOM_EMPRESA,
				E.RUT,
				E.DIG_VERIF,
				dbo.f_format_date(C.FECHA_COTIZACION, 3) FECHA_COTIZACION,				
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[DIRECCION]') DIRECCION,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_COMUNA]') NOM_COMUNA,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_CIUDAD]') NOM_CIUDAD,
				SF.TELEFONO TELEFONO_F,
				SF.FAX FAX_F,
				C.REFERENCIA,
				P.NOM_PERSONA,
				P.EMAIL,
				p.TELEFONO,
				IC.NOM_PRODUCTO,
					case IC.COD_PRODUCTO
						when 'T' then ''
					else IC.ITEM
					end ITEM,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.COD_PRODUCTO
					end COD_PRODUCTO,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD
					end CANTIDAD,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.PRECIO
					end PRECIO,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD * IC.PRECIO
					end TOTAL,
				C.SUBTOTAL,
				C.PORC_DSCTO1,
				C.MONTO_DSCTO1,
				C.PORC_DSCTO2,
				C.MONTO_DSCTO2,
				C.MONTO_DSCTO1 + C.MONTO_DSCTO2 FINAL,
				C.TOTAL_NETO,
				C.PORC_IVA,
				C.MONTO_IVA,
				C.TOTAL_CON_IVA,
				C.NOM_FORMA_PAGO_OTRO,
				FP.NOM_FORMA_PAGO,
				C.VALIDEZ_OFERTA,
				C.ENTREGA,
				C.OBS,
				EC.NOM_EMBALAJE_COTIZACION,
				FL.NOM_FLETE_COTIZACION,
				I.NOM_INSTALACION_COTIZACION,
				C.GARANTIA,
				M.SIMBOLO,
				U.NOM_USUARIO,
				U.MAIL MAIL_U,
				U.TELEFONO FONO_U,
				U.CELULAR CEL_U,
				U.INI_USUARIO,
				dbo.f_get_parametro(".self::K_PARAM_NOM_EMPRESA.") NOM_EMPRESA_EMISOR,
				dbo.f_get_parametro(".self::K_PARAM_RUT_EMPRESA.") RUT_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_DIR_EMPRESA.") DIR_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_GIRO_EMPRESA.") GIRO_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_TEL_EMPRESA.") TEL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_BANCO.") BANCO,
				dbo.f_get_parametro(".self::K_PARAM_CTA_CTE.") CTA_CTE,	
				dbo.f_get_parametro(".self::K_PARAM_FAX_EMPRESA.") FAX_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_MAIL_EMPRESA.") MAIL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_CIUDAD_EMPRESA.") CIUDAD_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_PAIS_EMPRESA.") PAIS_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_SITIO_WEB_EMPRESA.") SITIO_WEB_EMPRESA
	FROM COTIZACION C, EMPRESA E, PERSONA P, ITEM_COTIZACION IC,FORMA_PAGO FP,
				 INSTALACION_COTIZACION I, FLETE_COTIZACION FL, EMBALAJE_COTIZACION EC,
				 MONEDA M, USUARIO U, SUCURSAL SF
	WHERE C.COD_COTIZACION = $cod_cotizacion AND 
				E.COD_EMPRESA = C.COD_EMPRESA AND
				P.COD_PERSONA = C.COD_PERSONA AND
				IC.COD_COTIZACION = C.COD_COTIZACION AND
				FP.COD_FORMA_PAGO = C.COD_FORMA_PAGO AND
				I.COD_INSTALACION_COTIZACION =C.COD_INSTALACION_COTIZACION AND
				FL.COD_FLETE_COTIZACION = C.COD_FLETE_COTIZACION AND
				EC.COD_EMBALAJE_COTIZACION = C.COD_EMBALAJE_COTIZACION AND	
				M.COD_MONEDA = C.COD_MONEDA AND
				U.COD_USUARIO = C.COD_USUARIO_VENDEDOR1 AND
				SF.COD_SUCURSAL = C.COD_SUCURSAL_FACTURA
				order by IC.ORDEN asc";
				

		// reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/cot_resumen.xml', $labels, "Cotizaci�n Resumen ".$cod_cotizacion.".pdf", $con_logo);
	}
	function printcot_ampliada_pdf($con_logo) {
	$cod_cotizacion = $this->get_key();
	
	$sql = "SELECT			C.COD_COTIZACION,
				E.NOM_EMPRESA,
				E.RUT,
				E.DIG_VERIF,
				dbo.f_format_date(C.FECHA_COTIZACION, 3) FECHA_COTIZACION,				
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[DIRECCION]') DIRECCION,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_COMUNA]') NOM_COMUNA,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_CIUDAD]') NOM_CIUDAD,
				SF.TELEFONO TELEFONO_F,
				SF.FAX FAX_F,
				C.REFERENCIA,
				P.NOM_PERSONA,
				P.EMAIL,
				p.TELEFONO,
				IC.NOM_PRODUCTO,
				case IC.COD_PRODUCTO
					when 'T' then ''
					else IC.ITEM
				end ITEM,
				IC.COD_PRODUCTO COD_PRODUCTO_ORIGINAL,
				case IC.COD_PRODUCTO
					when 'T' then null
					else IC.COD_PRODUCTO
				end COD_PRODUCTO,
				case IC.COD_PRODUCTO
					when 'T' then null
					else IC.CANTIDAD
				end CANTIDAD,
				case IC.COD_PRODUCTO
					when 'T' then null
					else IC.PRECIO
				end PRECIO,
				case IC.COD_PRODUCTO
					when 'T' then null
					else IC.CANTIDAD * IC.PRECIO
				end TOTAL,
				C.SUBTOTAL,
				C.PORC_DSCTO1,
				C.MONTO_DSCTO1,
				C.PORC_DSCTO2,
				C.MONTO_DSCTO2,
				C.MONTO_DSCTO1 + C.MONTO_DSCTO2 FINAL,
				C.TOTAL_NETO,
				C.PORC_IVA,
				C.MONTO_IVA,
				C.TOTAL_CON_IVA,
				FP.NOM_FORMA_PAGO,
				C.VALIDEZ_OFERTA,
				C.ENTREGA,
				C.OBS,
				EC.NOM_EMBALAJE_COTIZACION,
				FL.NOM_FLETE_COTIZACION,
				I.NOM_INSTALACION_COTIZACION,
				C.GARANTIA,
				M.SIMBOLO,
				U.NOM_USUARIO,
				U.MAIL MAIL_U,
				U.TELEFONO FONO_U,
				U.CELULAR CEL_U,
				case convert(varchar(3000), dbo.f_prod_get_atributo(IC.COD_PRODUCTO)) 
					when '' then IC.MOTIVO_TE 
					else convert(varchar(3000), dbo.f_prod_get_atributo(IC.COD_PRODUCTO)) 
				end ATRIBUTO_PRODUCTO,
				dbo.f_get_parametro(".self::K_PARAM_NOM_EMPRESA.") NOM_EMPRESA_EMISOR,
				dbo.f_get_parametro(".self::K_PARAM_RUT_EMPRESA.") RUT_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_DIR_EMPRESA.") DIR_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_GIRO_EMPRESA.") GIRO_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_TEL_EMPRESA.") TEL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_BANCO.") BANCO,
				dbo.f_get_parametro(".self::K_PARAM_CTA_CTE.") CTA_CTE,	
				dbo.f_get_parametro(".self::K_PARAM_FAX_EMPRESA.") FAX_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_MAIL_EMPRESA.") MAIL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_CIUDAD_EMPRESA.") CIUDAD_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_PAIS_EMPRESA.") PAIS_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_SITIO_WEB_EMPRESA.") SITIO_WEB_EMPRESA
		FROM COTIZACION C, EMPRESA E, PERSONA P, ITEM_COTIZACION IC,FORMA_PAGO FP,
					 INSTALACION_COTIZACION I, FLETE_COTIZACION FL, EMBALAJE_COTIZACION EC,
					 MONEDA M, USUARIO U, SUCURSAL SF
		WHERE C.COD_COTIZACION = $cod_cotizacion AND 
						E.COD_EMPRESA = C.COD_EMPRESA AND
						P.COD_PERSONA = C.COD_PERSONA AND
						IC.COD_COTIZACION = C.COD_COTIZACION AND
						FP.COD_FORMA_PAGO = C.COD_FORMA_PAGO AND
						I.COD_INSTALACION_COTIZACION =C.COD_INSTALACION_COTIZACION AND
						FL.COD_FLETE_COTIZACION = C.COD_FLETE_COTIZACION AND
						EC.COD_EMBALAJE_COTIZACION = C.COD_EMBALAJE_COTIZACION AND	
						M.COD_MONEDA = C.COD_MONEDA AND
						U.COD_USUARIO = C.COD_USUARIO_VENDEDOR1 AND
						SF.COD_SUCURSAL = C.COD_SUCURSAL_FACTURA
						order by IC.ORDEN asc";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/cot_ampliado.xml', $labels, "Cotizaci�n Ampliada ".$cod_cotizacion.".pdf", $con_logo);
	}
	function printcot_pesomedida_pdf($con_logo, $embalada) {
	$cod_cotizacion = $this->get_key();	
		
	$sql= "SELECT C.COD_COTIZACION,
				E.NOM_EMPRESA,
				E.RUT,
				E.DIG_VERIF,
				dbo.f_format_date(C.FECHA_COTIZACION, 3) FECHA_COTIZACION,				
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[DIRECCION]') DIRECCION,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_COMUNA]') NOM_COMUNA,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_CIUDAD]') NOM_CIUDAD,
				SF.TELEFONO TELEFONO_F,
				SF.FAX FAX_F,
				C.REFERENCIA,
				P.NOM_PERSONA,
				P.EMAIL,
				p.TELEFONO,
				IC.NOM_PRODUCTO,
				case IC.COD_PRODUCTO
					when 'T' then ''
					else IC.ITEM
				end ITEM,
				case IC.COD_PRODUCTO
					when 'T' then null
					else IC.COD_PRODUCTO
				end COD_PRODUCTO,
				case IC.COD_PRODUCTO
					when 'T' then null
					else IC.CANTIDAD
				end CANTIDAD, ";
				
		if($embalada == 'noembalada'){
			$sql.= "case PR.COD_PRODUCTO
						when 'T' then null
						else PR.LARGO
					end LARGO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else PR.ANCHO
					end ANCHO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else PR.ALTO
					end ALTO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else PR.PESO
					end PESO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else ((PR.LARGO)*(PR.ANCHO)*(PR.ALTO))/1000000 
					end VOLUMEN,
					case PR.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD * (((PR.LARGO)*(PR.ANCHO)*(PR.ALTO))/1000000)
					end VOLT,
					case PR.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD * PR.PESO
					end PESOT,
					'Especificaciones Equipo sin Embalaje' TITLE_ITEM, ";
		}else{
			$sql.= "case PR.COD_PRODUCTO
						when 'T' then null
						else PR.LARGO_EMBALADO
					end LARGO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else PR.ANCHO_EMBALADO
					end ANCHO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else PR.ALTO_EMBALADO
					end ALTO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else PR.PESO_EMBALADO
					end PESO,
					case PR.COD_PRODUCTO
						when 'T' then null
						else ((PR.LARGO_EMBALADO)*(PR.ANCHO_EMBALADO)*(PR.ALTO_EMBALADO))/1000000 
					end VOLUMEN,
					case PR.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD * (((PR.LARGO_EMBALADO)*(PR.ANCHO_EMBALADO)*(PR.ALTO_EMBALADO))/1000000)
					end VOLT,
					case PR.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD * PR.PESO_EMBALADO
					end PESOT,
					'Especificaciones Equipo con Embalaje' TITLE_ITEM, ";
		}
	
		$sql.= "U. NOM_USUARIO,
				U.MAIL MAIL_U,
				U.TELEFONO FONO_U,
				U.CELULAR CEL_U,
				dbo.f_get_parametro(".self::K_PARAM_NOM_EMPRESA.") NOM_EMPRESA_EMISOR,
				dbo.f_get_parametro(".self::K_PARAM_RUT_EMPRESA.") RUT_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_DIR_EMPRESA.") DIR_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_GIRO_EMPRESA.") GIRO_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_TEL_EMPRESA.") TEL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_BANCO.") BANCO,
				dbo.f_get_parametro(".self::K_PARAM_CTA_CTE.") CTA_CTE,	
				dbo.f_get_parametro(".self::K_PARAM_FAX_EMPRESA.") FAX_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_MAIL_EMPRESA.") MAIL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_CIUDAD_EMPRESA.") CIUDAD_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_PAIS_EMPRESA.") PAIS_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_SITIO_WEB_EMPRESA.") SITIO_WEB_EMPRESA
		FROM COTIZACION C, EMPRESA E, PERSONA P,
				ITEM_COTIZACION IC, USUARIO U, PRODUCTO PR,
				SUCURSAL SF, SUCURSAL SD
		WHERE C.COD_COTIZACION = $cod_cotizacion AND 
				E.COD_EMPRESA = C.COD_EMPRESA AND
				P.COD_PERSONA = C.COD_PERSONA AND
				IC.COD_COTIZACION = C.COD_COTIZACION AND
				U.COD_USUARIO = C.COD_USUARIO_VENDEDOR1 and
				SF.COD_SUCURSAL = C.COD_SUCURSAL_FACTURA AND						
				SD.COD_SUCURSAL = C.COD_SUCURSAL_DESPACHO AND
		    	PR.COD_PRODUCTO = IC.COD_PRODUCTO 
				order by IC.ORDEN asc";
				

		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/pesos_medidas.xml', $labels, "Cotizaci�n Resumen ".$cod_cotizacion.".pdf", $con_logo);				
	}
	/*
	FUNCIONES PARA IMPRIMIR COTIZACIONES LISTA TECNICA
	*/
	function printcot_tecnica_electrico_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
		$sql = "exec spr_cot_tecnica $cod_cotizacion, 'ELECTRICIDAD'";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt = new reporte($sql, $this->root_dir.'appl/cotizacion/list_elect.xml', $labels, "Cotizaci�n Lista El�ctrica ".$cod_cotizacion.".pdf", $con_logo);
	}
	function printcot_tecnica_gas_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
		
		$sql = "exec spr_cot_tecnica $cod_cotizacion, 'GAS'";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/list_gas.xml', $labels, "Cotizaci�n Lista Gas ".$cod_cotizacion.".pdf", $con_logo);						
		
	}
	function printcot_tecnica_vapor_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
		
		$sql = "exec spr_cot_tecnica $cod_cotizacion, 'VAPOR'";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/list_vapor.xml', $labels, "Cotizaci�n Lista Vapor ".$cod_cotizacion.".pdf", $con_logo);						
		
	}
	function printcot_tecnica_agua_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
		
		$sql = "exec spr_cot_tecnica $cod_cotizacion, 'AGUA'";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/list_agua.xml', $labels, "Cotizaci�n Lista Agua ".$cod_cotizacion.".pdf", $con_logo);						
	}
	function printcot_tecnica_ventilacion_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
		
		$sql = "exec spr_cot_tecnica $cod_cotizacion, 'VENTILACION'";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/list_ventilacion.xml', $labels, "Cotizaci�n Lista Ventilaci�n ".$cod_cotizacion.".pdf", $con_logo);
	}
	function printcot_tecnica_desague_pdf($con_logo) {
		$cod_cotizacion = $this->get_key();
		
		$sql = "exec spr_cot_tecnica $cod_cotizacion, 'DESAGUE'";
		//reporte
		$labels = array();
		$labels['strCOD_COTIZACION'] = $cod_cotizacion;
		$rpt= new reporte($sql, $this->root_dir.'appl/cotizacion/list_desague.xml', $labels, "Cotizaci�n Lista Desague ".$cod_cotizacion.".pdf", $con_logo);
	}	
	// EXCEL
	function printcot_resumen_excel($con_logo) {
		
		error_reporting(E_ALL & ~E_NOTICE);
		
		require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php");
		require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php");
		
		$fname = tempnam("/tmp", "resumen.xls");
		$workbook = &new writeexcel_workbook($fname);
		$cod_cotizacion = $this->get_key();
		$worksheet = &$workbook->addworksheet('COTIZACION_'.$cod_cotizacion);
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT	C.COD_COTIZACION,
				E.NOM_EMPRESA,
				E.RUT,
				E.DIG_VERIF,
				dbo.f_format_date(getdate(), 3) FECHA_IMPRESO,				
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[DIRECCION]') DIRECCION,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_COMUNA]') NOM_COMUNA,
				dbo.f_get_direccion('SUCURSAL', C.COD_SUCURSAL_FACTURA, '[NOM_CIUDAD]') NOM_CIUDAD,
				SF.TELEFONO TELEFONO_F,
				SF.FAX FAX_F,
				C.REFERENCIA,
				P.NOM_PERSONA,
				P.EMAIL,
				p.TELEFONO,
				IC.NOM_PRODUCTO,
					case IC.COD_PRODUCTO
						when 'T' then ''
					else IC.ITEM
					end ITEM,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.COD_PRODUCTO
					end COD_PRODUCTO,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD
					end CANTIDAD,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.PRECIO
					end PRECIO,
					case IC.COD_PRODUCTO
						when 'T' then null
						else IC.CANTIDAD * IC.PRECIO
					end TOTAL,
				C.SUBTOTAL,
				C.PORC_DSCTO1,
				C.MONTO_DSCTO1,
				C.PORC_DSCTO2,
				C.MONTO_DSCTO2,
				C.MONTO_DSCTO1 + C.MONTO_DSCTO2 FINAL,
				C.TOTAL_NETO,
				C.PORC_IVA,
				C.MONTO_IVA,
				C.TOTAL_CON_IVA,
				FP.NOM_FORMA_PAGO,
				C.VALIDEZ_OFERTA,
				C.ENTREGA,
				C.OBS,
				EC.NOM_EMBALAJE_COTIZACION,
				FL.NOM_FLETE_COTIZACION,
				I.NOM_INSTALACION_COTIZACION,
				C.GARANTIA,
				M.SIMBOLO,
				U.NOM_USUARIO,
				U.MAIL MAIL_U,
				U.TELEFONO FONO_U,
				U.CELULAR CEL_U,
				dbo.f_get_parametro(".self::K_PARAM_NOM_EMPRESA.") NOM_EMPRESA_EMISOR,
				dbo.f_get_parametro(".self::K_PARAM_RUT_EMPRESA.") RUT_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_DIR_EMPRESA.") DIR_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_TEL_EMPRESA.") TEL_EMPRESA,	
				dbo.f_get_parametro(".self::K_PARAM_FAX_EMPRESA.") FAX_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_MAIL_EMPRESA.") MAIL_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_CIUDAD_EMPRESA.") CIUDAD_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_PAIS_EMPRESA.") PAIS_EMPRESA,
				dbo.f_get_parametro(".self::K_PARAM_SITIO_WEB_EMPRESA.") SITIO_WEB_EMPRESA
			FROM COTIZACION C, EMPRESA E, PERSONA P, ITEM_COTIZACION IC,FORMA_PAGO FP,
				 INSTALACION_COTIZACION I, FLETE_COTIZACION FL, EMBALAJE_COTIZACION EC,
				 MONEDA M, USUARIO U, SUCURSAL SF
			WHERE C.COD_COTIZACION = $cod_cotizacion AND 
				E.COD_EMPRESA = C.COD_EMPRESA AND
				P.COD_PERSONA = C.COD_PERSONA AND
				IC.COD_COTIZACION = C.COD_COTIZACION AND
				FP.COD_FORMA_PAGO = C.COD_FORMA_PAGO AND
				I.COD_INSTALACION_COTIZACION =C.COD_INSTALACION_COTIZACION AND
				FL.COD_FLETE_COTIZACION = C.COD_FLETE_COTIZACION AND
				EC.COD_EMBALAJE_COTIZACION = C.COD_EMBALAJE_COTIZACION AND	
				M.COD_MONEDA = C.COD_MONEDA AND
				U.COD_USUARIO = C.COD_USUARIO_VENDEDOR1 AND
				SF.COD_SUCURSAL = C.COD_SUCURSAL_FACTURA
				order by IC.ORDEN asc";
				
		$result = $db->build_results($sql);
		
		$worksheet->set_row(0, 60);
		$worksheet->set_column(0, 0, 4);
		$worksheet->set_column(1, 2, 7);
		$worksheet->set_column(3, 9, 14);
		$worksheet->insert_bitmap('B1',$this->root_dir."images_appl/TODOINOX/logo_reporte_excel_2.bmp");
		
	
		$text =& $workbook->addformat();
		$text->set_font("Verdana");
		$text->set_valign('vcenter');
    
		$text_bold =& $workbook->addformat();
		$text_bold->copy($text);
		$text_bold->set_bold(1);
	
		$text_blue_bold_left =& $workbook->addformat();
		$text_blue_bold_left->copy($text_bold);
		$text_blue_bold_left->set_align('left');
		$text_blue_bold_left->set_color('blue_0x20');

		$text_blue_bold_center =& $workbook->addformat();
		$text_blue_bold_center->copy($text_bold);
		$text_blue_bold_center->set_align('center');
		$text_blue_bold_center->set_color('blue_0x20');
		
		$text_blue_bold_right =& $workbook->addformat();
		$text_blue_bold_right->copy($text_bold);
		$text_blue_bold_right->set_align('right');
		$text_blue_bold_right->set_color('blue_0x20');

		$text_nro_docto =& $workbook->addformat();
		$text_nro_docto->copy($text_blue_bold_right);
		$text_nro_docto->set_size(13);
		
		$text_pie_de_pagina =& $workbook->addformat();
		$text_pie_de_pagina->copy($text_blue_bold_left);
		$text_pie_de_pagina->set_size(8);
		
		$text_normal_left =& $workbook->addformat();
		$text_normal_left->copy($text);
		$text_normal_left->set_align('left');
		
		$text_normal_center =& $workbook->addformat();
		$text_normal_center->copy($text);
		$text_normal_center->set_align('center');
		
		$text_normal_right =& $workbook->addformat();
		$text_normal_right->copy($text);
		$text_normal_right->set_align('right');
				
		$text_normal_bold_left =& $workbook->addformat();
		$text_normal_bold_left->copy($text_bold);
		$text_normal_bold_left->set_align('left');
		
		
		$text_normal_bold_center =& $workbook->addformat();
		$text_normal_bold_center->copy($text_bold);
		$text_normal_bold_center->set_align('center');
	
		$text_normal_bold_right =& $workbook->addformat();
		$text_normal_bold_right->copy($text_bold);
		$text_normal_bold_right->set_align('right');
	
		
		$titulo_item_border_all =& $workbook->addformat();
		$titulo_item_border_all->copy($text_blue_bold_center);
		$titulo_item_border_all->set_border_color('black');
		$titulo_item_border_all->set_top(2);
		$titulo_item_border_all->set_bottom(2);
		$titulo_item_border_all->set_right(2);
		$titulo_item_border_all->set_left(2);
		
		$titulo_item_border_all_merge =& $workbook->addformat();
		$titulo_item_border_all_merge->copy($titulo_item_border_all);
		$titulo_item_border_all_merge->set_merge();
				
	
		$border_item_left = & $workbook->addformat();
		$border_item_left->copy($text_normal_left);
		$border_item_left->set_border_color('black');
		$border_item_left->set_left(2);
		
		$border_item_left_bold = & $workbook->addformat();
		$border_item_left_bold->copy($text_bold);
		$border_item_left_bold->set_border_color('black');
		$border_item_left_bold->set_left(2);
		
		$border_item_center = & $workbook->addformat();
		$border_item_center->copy($text_normal_center);
		$border_item_center->set_border_color('black');
		$border_item_center->set_left(2);
		$border_item_center->set_right(2);
		
		$border_item_right = & $workbook->addformat();
		$border_item_right->copy($text_normal_right);
		$border_item_right->set_border_color('black');
		$border_item_right->set_right(2);		
		
		$cant_normal =& $workbook->addformat();
		$cant_normal->copy($border_item_right);
		$cant_normal->set_num_format('0.0');
					
		$monto_normal =& $workbook->addformat();
		$monto_normal->copy($border_item_right);
		$monto_normal->set_num_format('#,##0');
		
		$border_item_top = & $workbook->addformat();
		$border_item_top->copy($text);
		$border_item_top->set_border_color('black');
		$border_item_top->set_top(2);
		
		$border_item_bottom = & $workbook->addformat();
		$border_item_bottom->copy($text);
		$border_item_bottom->set_border_color('black');
		$border_item_bottom->set_bottom(2);
		
		$border_item_especial_left = & $workbook->addformat();
		$border_item_especial_left->copy($text_normal_left);
		$border_item_especial_left->set_border_color('black');
		$border_item_especial_left->set_left(2);
		$border_item_especial_left->set_right(2);
	
		
		
		$COD_COTIZACION = $result[0]['COD_COTIZACION'];
		$FECHA_IMPRESO = $result[0]['FECHA_IMPRESO'];
		$NOM_EMPRESA = $result[0]['NOM_EMPRESA'];
		$RUT = $result[0]['RUT'];
		$DIG_VERIF = $result[0]['DIG_VERIF'];
		$DIRECCION = $result[0]['DIRECCION'];
		$NOM_COMUNA = $result[0]['NOM_COMUNA'];
		$NOM_CIUDAD = $result[0]['NOM_CIUDAD'];
		$TELEFONO_F = $result[0]['TELEFONO_F'];
		$FAX_F = $result[0]['FAX_F'];	
		$NOM_PERSONA = $result[0]['NOM_PERSONA'];
		$EMAIL = $result[0]['EMAIL'];
		$REFERENCIA = $result[0]['REFERENCIA'];
		$SIMBOLO = $result[0]['SIMBOLO'];
		
		$worksheet->write(1, 9, "COTIZACION N�".$COD_COTIZACION, $text_nro_docto);
		$worksheet->write(1, 1, "Santiago,".$FECHA_IMPRESO, $text_blue_bold_left);
		$worksheet->write(3, 1, "Raz�n Social", $text_blue_bold_left);
		
		$worksheet->write(3, 3, $NOM_EMPRESA, $text_normal_bold_left);
		$worksheet->write(3, 8, "Rut", $text_blue_bold_left);
		
		$rut=number_format($RUT, 0, ',', '.');
		$rut=$rut.'-'.$DIG_VERIF;
		
		$worksheet->write(3, 9, $rut, $text_normal_bold_left);
		
		$worksheet->write(4, 1, "Direcci�n", $text_blue_bold_left);
		$worksheet->write(4, 3, $DIRECCION, $text_normal_left);
		$worksheet->write(5, 1, "Comuna", $text_blue_bold_left);
		$worksheet->write(5, 3, $NOM_COMUNA, $text_normal_left);
		$worksheet->write(5, 4, "Ciudad", $text_blue_bold_left);
		$worksheet->write(5, 5, $NOM_CIUDAD, $text_normal_left);
		$worksheet->write(5, 6, "Fono", $text_blue_bold_left);
		$worksheet->write(5, 7, $TELEFONO_F, $text_normal_left);
		$worksheet->write(5, 8, "Fax",$text_blue_bold_left);
		$worksheet->write(5, 9, $FAX_F,$text_normal_left);
		$worksheet->write(6, 1, "Atenci�n", $text_blue_bold_left);
		$worksheet->write(6, 3, $NOM_PERSONA." ".$EMAIL, $text_normal_left);
		$worksheet->write(7, 1, "Referencia",$text_blue_bold_left);
		$worksheet->write(7, 3, $REFERENCIA,$text_normal_left);
		
		$worksheet->write(9, 1, "�tem", $titulo_item_border_all);
		$worksheet->write(9, 2, "", $titulo_item_border_all);
		$worksheet->write(9, 3, "                                Producto                                ", $titulo_item_border_all_merge);
		$worksheet->write(9, 4, "", $titulo_item_border_all);
		$worksheet->write(9, 5, "", $titulo_item_border_all);
		$worksheet->write(9, 6, "Modelo", $titulo_item_border_all);
		$worksheet->write(9, 7, "Cantidad", $titulo_item_border_all);
		$worksheet->write(9, 8, "Precio ".$SIMBOLO, $titulo_item_border_all);
		$worksheet->write(9, 9, "Total ".$SIMBOLO, $titulo_item_border_all);
		
		for ($i=0 ; $i <count($result); $i++) {
			$ITEM = $result[$i]['ITEM'];
			$NOM_PRODUCTO = $result[$i]['NOM_PRODUCTO'];
			$COD_PRODUCTO = $result[$i]['COD_PRODUCTO'];
			$CANTIDAD = $result[$i]['CANTIDAD'];
			$PRECIO = $result[$i]['PRECIO'];
			$TOTAL = $result[$i]['TOTAL'];
			
			$worksheet->write(10+$i, 1, $ITEM, $border_item_left);
			
			if($COD_PRODUCTO == '')
				$worksheet->write(10+$i, 2, $NOM_PRODUCTO, $border_item_left_bold);
			else
				$worksheet->write(10+$i, 2, $NOM_PRODUCTO, $border_item_left);
			
			$worksheet->write(10+$i, 6, $COD_PRODUCTO, $border_item_especial_left);
			$worksheet->write(10+$i, 7, $CANTIDAD, $cant_normal);
			$worksheet->write(10+$i, 8, $PRECIO, $monto_normal);
			$worksheet->write(10+$i, 9, $TOTAL, $monto_normal);
		}

		$worksheet->write(10+$i, 1, " ", $border_item_top);
		$worksheet->write(10+$i, 2, " ", $border_item_top);
		$worksheet->write(10+$i, 3, " ", $border_item_top);
		$worksheet->write(10+$i, 4, " ", $border_item_top);
		$worksheet->write(10+$i, 5, " ", $border_item_top);
		$worksheet->write(10+$i, 6, " ", $border_item_top);
		$worksheet->write(10+$i, 7, " ", $border_item_top);
		$worksheet->write(10+$i, 8, " ", $border_item_top);
		$worksheet->write(10+$i, 9, " ", $border_item_top);	
		
		$row_position = $i+12;
		
		$SUBTOTAL = $result[0]['SUBTOTAL'];
		$PORC_DSCTO1 = $result[0]['PORC_DSCTO1'];
		$MONTO_DSCTO1 = $result[0]['MONTO_DSCTO1'];
		$PORC_DSCTO2 = $result[0]['PORC_DSCTO2'];
		$MONTO_DSCTO2 = $result[0]['MONTO_DSCTO2'];
		$TOTAL_NETO = $result[0]['TOTAL_NETO'];
		$PORC_IVA = $result[0]['PORC_IVA'];
		$MONTO_IVA = $result[0]['MONTO_IVA'];
		$TOTAL_CON_IVA = $result[0]['TOTAL_CON_IVA'];
	
		$NOM_FORMA_PAGO = $result[0]['NOM_FORMA_PAGO'];
		$VALIDEZ_OFERTA = $result[0]['VALIDEZ_OFERTA'];
		$ENTREGA = $result[0]['ENTREGA'];
		$NOM_EMBALAJE_COTIZACION = $result[0]['NOM_EMBALAJE_COTIZACION'];
		$NOM_FLETE_COTIZACION = $result[0]['NOM_FLETE_COTIZACION'];
		$NOM_INSTALACION_COTIZACION = $result[0]['NOM_INSTALACION_COTIZACION'];
		$GARANTIA = $result[0]['GARANTIA'];
		$OBS = $result[0]['OBS'];		
		$NOM_USUARIO = $result[0]['NOM_USUARIO'];
		$MAIL_U = $result[0]['MAIL_U'];
		$FONO_U = $result[0]['FONO_U'];
		$CEL_U = $result[0]['CEL_U'];
		
	
		$NOM_EMPRESA_EMISOR = $result[0]['NOM_EMPRESA_EMISOR'];
		$RUT_EMPRESA = $result[0]['RUT_EMPRESA'];
		$DIR_EMPRESA = $result[0]['DIR_EMPRESA'];
		$CIUDAD_EMPRESA = $result[0]['CIUDAD_EMPRESA'];
		$PAIS_EMPRESA = $result[0]['PAIS_EMPRESA'];
		$TEL_EMPRESA = $result[0]['TEL_EMPRESA'];
		$FAX_EMPRESA = $result[0]['FAX_EMPRESA'];
		$MAIL_EMPRESA = $result[0]['MAIL_EMPRESA'];
		$SITIO_WEB_EMPRESA = $result[0]['SITIO_WEB_EMPRESA'];
		
		$FINAL = $result[0]['FINAL'];

		$worksheet->write($row_position-1, 6, " ", $border_item_bottom);
		$worksheet->write($row_position-1, 7, " ", $border_item_bottom);
		$worksheet->write($row_position-1, 8, " ", $border_item_bottom);
		$worksheet->write($row_position-1, 9, " ", $border_item_bottom);
		
		if($MONTO_DSCTO1 > 0 && $MONTO_DSCTO2 > 0){
			$worksheet->write($row_position, 6, "Subtotal ", $border_item_left);
			$worksheet->write($row_position, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position, 9, $SUBTOTAL, $monto_normal);
			$worksheet->write($row_position+1, 6, "Descuento ".$PORC_DSCTO1."% ", $border_item_left);
			$worksheet->write($row_position+1, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+1, 9, $MONTO_DSCTO1, $monto_normal);
			$worksheet->write($row_position+2, 6, "Descuento Adicional ".$PORC_DSCTO2."% ", $border_item_left);
			$worksheet->write($row_position+2, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+2, 9, $MONTO_DSCTO2, $monto_normal);
			
			$worksheet->write($row_position+3, 6, "Total Neto ", $border_item_left);
			$worksheet->write($row_position+3, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+3, 9, $TOTAL_NETO, $monto_normal);
			$worksheet->write($row_position+4, 6, "IVA ".$PORC_IVA."% ", $border_item_left);
			$worksheet->write($row_position+4, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+4, 9, $MONTO_IVA, $monto_normal);
			$worksheet->write($row_position+5, 6, "Total con IVA ", $border_item_left);
			$worksheet->write($row_position+5, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+5, 9, $TOTAL_CON_IVA, $monto_normal);
			$worksheet->write($row_position+6, 6, " ", $border_item_top);
			$worksheet->write($row_position+6, 7, " ", $border_item_top);
			$worksheet->write($row_position+6, 8, " ", $border_item_top);
			$worksheet->write($row_position+6, 9, " ", $border_item_top);
		
		}
		elseif($MONTO_DSCTO1 > 0 && $MONTO_DSCTO2 == 0){
			$worksheet->write($row_position, 6, "Subtotal ", $border_item_left);
			$worksheet->write($row_position, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position, 9, $SUBTOTAL, $monto_normal);
			$worksheet->write($row_position+1, 6, "Descuento ".$PORC_DSCTO1."% ", $border_item_left);
			$worksheet->write($row_position+1, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+1, 9, $MONTO_DSCTO1, $monto_normal);

			$worksheet->write($row_position+2, 6, "Total Neto ", $border_item_left);
			$worksheet->write($row_position+2, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+2, 9, $TOTAL_NETO, $monto_normal);
			$worksheet->write($row_position+3, 6, "IVA ".$PORC_IVA."% ", $border_item_left);
			$worksheet->write($row_position+3, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+3, 9, $MONTO_IVA, $monto_normal);
			$worksheet->write($row_position+4, 6, "Total con IVA ", $border_item_left);
			$worksheet->write($row_position+4, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+4, 9, $TOTAL_CON_IVA, $monto_normal);
			$worksheet->write($row_position+5, 6, " ", $border_item_top);
			$worksheet->write($row_position+5, 7, " ", $border_item_top);
			$worksheet->write($row_position+5, 8, " ", $border_item_top);
			$worksheet->write($row_position+5, 9, " ", $border_item_top);
		}
		elseif($MONTO_DSCTO2 > 0 && $MONTO_DSCTO1 == 0){
			$worksheet->write($row_position, 6, "Subtotal ", $border_item_left);
			$worksheet->write($row_position, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position, 9, $SUBTOTAL, $monto_normal);			
			$worksheet->write($row_position+1, 6, "Descuento Adicional ".$PORC_DSCTO2."% ", $border_item_left);
			$worksheet->write($row_position+1, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+1, 9, $MONTO_DSCTO2, $monto_normal);
			
			$worksheet->write($row_position+2, 6, "Total Neto ", $border_item_left);
			$worksheet->write($row_position+2, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+2, 9, $TOTAL_NETO, $monto_normal);
			$worksheet->write($row_position+3, 6, "IVA ".$PORC_IVA."% ", $border_item_left);
			$worksheet->write($row_position+3, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+3, 9, $MONTO_IVA, $monto_normal);
			$worksheet->write($row_position+4, 6, "Total con IVA ", $border_item_left);
			$worksheet->write($row_position+4, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+4, 9, $TOTAL_CON_IVA, $monto_normal);
			$worksheet->write($row_position+5, 6, " ", $border_item_top);
			$worksheet->write($row_position+5, 7, " ", $border_item_top);
			$worksheet->write($row_position+5, 8, " ", $border_item_top);
			$worksheet->write($row_position+5, 9, " ", $border_item_top);
		}
		else
		{	
			$worksheet->write($row_position, 6, "Total Neto ", $border_item_left);
			$worksheet->write($row_position, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position, 9, $TOTAL_NETO, $monto_normal);
			$worksheet->write($row_position+1, 6, "IVA ".$PORC_IVA."% ", $border_item_left);
			$worksheet->write($row_position+1, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+1, 9, $MONTO_IVA, $monto_normal);
			$worksheet->write($row_position+2, 6, "Total con IVA ", $border_item_left);
			$worksheet->write($row_position+2, 8, $SIMBOLO, $text_normal_right);
			$worksheet->write($row_position+2, 9, $TOTAL_CON_IVA, $monto_normal);
			$worksheet->write($row_position+3, 6, " ", $border_item_top);
			$worksheet->write($row_position+3, 7, " ", $border_item_top);
			$worksheet->write($row_position+3, 8, " ", $border_item_top);
			$worksheet->write($row_position+3, 9, " ", $border_item_top);	
		}

		$worksheet->write($row_position+7, 1, "Condiciones Generales:", $text_blue_bold_left);
		$worksheet->write($row_position+8, 1, "Foma de Pago", $text_blue_bold_left);
		$worksheet->write($row_position+8, 3, $NOM_FORMA_PAGO, $text_normal_left);
		$worksheet->write($row_position+9, 1, "Validez Oferta", $text_blue_bold_left);
		$worksheet->write($row_position+9, 3, $VALIDEZ_OFERTA." D�AS", $text_normal_left);
		$worksheet->write($row_position+10, 1, "Entrega", $text_blue_bold_left);
		$worksheet->write($row_position+10, 3, $ENTREGA, $text_normal_left);
		$worksheet->write($row_position+11, 1, "Embalaje", $text_blue_bold_left);
		$worksheet->write($row_position+11, 3, $NOM_EMBALAJE_COTIZACION, $text_normal_left);
		$worksheet->write($row_position+12, 1, "Flete", $text_blue_bold_left);
		$worksheet->write($row_position+12, 3, $NOM_FLETE_COTIZACION, $text_normal_left);
		$worksheet->write($row_position+13, 1, "Instalaci�n", $text_blue_bold_left);
		$worksheet->write($row_position+13, 3, $NOM_INSTALACION_COTIZACION, $text_normal_left);
		$worksheet->write($row_position+14, 1, "Garant�a", $text_blue_bold_left);
		$worksheet->write($row_position+14, 3, $GARANTIA, $text_normal_left);
		$worksheet->write($row_position+15, 1, "Notas", $text_blue_bold_left);
		$worksheet->write($row_position+16, 1, $OBS, $text_normal_left);
		
		$worksheet->write($row_position+19, 8, $NOM_EMPRESA_EMISOR, $text_blue_bold_center);
		$worksheet->write($row_position+20, 8, $NOM_USUARIO, $text_blue_bold_center);
		$worksheet->write($row_position+21, 8, $MAIL_U, $text_blue_bold_center);
		$worksheet->write($row_position+22, 8, $FONO_U."-".$CEL_U, $text_blue_bold_center);

		$worksheet->write($row_position+25, 1, $NOM_EMPRESA_EMISOR." - RUT: ".$RUT_EMPRESA." - ".$DIR_EMPRESA." - ".$CIUDAD_EMPRESA." - ".$PAIS_EMPRESA." - ".$TEL_EMPRESA." - ".$FAX_EMPRESA, $text_pie_de_pagina);
		$worksheet->write($row_position+26, 5, $MAIL_EMPRESA." - ".$SITIO_WEB_EMPRESA, $text_pie_de_pagina);
		
		
		$workbook->close();
		
		header("Content-Type: application/x-msexcel; name=\"Cotizacion Resumen $COD_COTIZACION.xls\"");
		header("Content-Disposition: inline; filename=\"Cotizacion Resumen $COD_COTIZACION.xls\"");
		$fh=fopen($fname, "rb");
		fpassthru($fh);
		unlink($fname);
		
		error_reporting(E_ALL);

	}
	
	function printcot_ampliada_excel($con_logo) {}
	function printcot_pesomedida_excel($con_logo) {}
	function printcot_tecnica_electrico_excel($con_logo) {}
	function printcot_tecnica_gas_excel($con_logo) {}
	function printcot_tecnica_vapor_excel($con_logo) {}
	function printcot_tecnica_agua_excel($con_logo) {}
	function printcot_tecnica_ventilacion_excel($con_logo) {}
	function printcot_tecnica_desague_excel($con_logo) {}

	function creada_desde($cod_cotizacion) {
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$usuario = session::get("COD_USUARIO");
		
		$item_menu = '990520';
		$sql_perfil = "SELECT COD_PERFIL
					     FROM USUARIO
						WHERE COD_USUARIO =$usuario";
		$result_perfil = $db->build_results($sql_perfil);
		$perfil = 	$result_perfil[0]['COD_PERFIL'];
		
		$sql_autoriza = "SELECT AUTORIZA_MENU
					FROM AUTORIZA_MENU
					WHERE COD_PERFIL = $perfil
					AND COD_ITEM_MENU =$item_menu";
		$result_autoriza = $db->build_results($sql_autoriza);					
		$autoriza_menu  = 	$result_autoriza[0]['AUTORIZA_MENU'];
		
		
		
		$this->load_cotizacion($cod_cotizacion);
		$this->dws['dw_cotizacion']->set_item(0, 'COD_COTIZACION','');
		$this->dws['dw_cotizacion']->set_item(0, 'FECHA_COTIZACION', $this->current_date());
		$this->dws['dw_cotizacion']->set_item(0, 'COD_USUARIO', $this->cod_usuario);
		$this->dws['dw_cotizacion']->set_item(0, 'NOM_USUARIO', $this->nom_usuario);
		$this->dws['dw_cotizacion']->set_item(0, 'COD_COTIZACION_DESDE', $cod_cotizacion);
		
		if($autoriza_menu <> 'E'){
		$none ='none';
		$this->dws['dw_cotizacion']->set_item(0,'VISIBLE', '');
		}
		
		
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "select COD_USUARIO, PORC_PARTICIPACION from USUARIO where COD_USUARIO = $this->cod_usuario and es_vendedor = 'S'";
		$result = $db->build_results($sql);
		if (count($result)>0) {
			$this->dws['dw_cotizacion']->set_item(0, 'COD_USUARIO_VENDEDOR1',$this->cod_usuario);
			$this->dws['dw_cotizacion']->set_item(0, 'PORC_VENDEDOR1', $result[0]['PORC_PARTICIPACION']);
		}
		else
		{
			$this->dws['dw_cotizacion']->set_item(0, 'COD_USUARIO_VENDEDOR1','');
			$this->dws['dw_cotizacion']->set_item(0, 'PORC_VENDEDOR1','');
		}
				
		$num_dif = 0;			

		for($i=0; $i<$this->dws['dw_item_cotizacion']->row_count(); $i++){						
			$cod_producto 	= $this->dws['dw_item_cotizacion']->get_item($i, 'COD_PRODUCTO');
			$precio_cot		= $this->dws['dw_item_cotizacion']->get_item($i, 'PRECIO');													
			$result			= $db->build_results("select PRECIO_VENTA_PUBLICO, PRECIO_LIBRE from PRODUCTO where COD_PRODUCTO = '$cod_producto'");
			// para los TE, E, I, etc Se los salta
			if ($result[0]['PRECIO_LIBRE']=='S') 
				continue;
			
			$precio_bd		= $result[0]['PRECIO_VENTA_PUBLICO'];
			if($precio_bd != $precio_cot ){
				$num_dif++;
				break;
			}	
		}


		// Cambia el status de las los items
		for($i=0; $i<$this->dws['dw_item_cotizacion']->row_count(); $i++){
			$this->dws['dw_item_cotizacion']->set_item($i, 'COD_ITEM_COTIZACION', '');
			$this->dws['dw_item_cotizacion']->set_status_row($i, K_ROW_NEW_MODIFIED);
		}
		
		$sql="SELECT PORC_MODIFICA_PRECIO 
			  FROM USUARIO
			  WHERE COD_USUARIO = $usuario";
		$result = $db->build_results($sql);

		if($result[0]['PORC_MODIFICA_PRECIO'] == 0){
			if($num_dif > 0){
				$this->usa_precio_prod();
				$this->dws['dw_cotizacion']->calc_computed();
				$this->dws['dw_item_cotizacion']->calc_computed();
			}	
		}else{
			if($num_dif > 0)
				$this->que_precio_usa($cod_cotizacion);	
		}
		
	}
}
?>