<?php
class wo_inf_oc_por_facturar_tdnx extends wo_inf_oc_por_facturar_tdnx_base {
	var $origen;
	function wo_inf_oc_por_facturar_tdnx(){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_usuario = session::get("COD_USUARIO");
		
		$inventario = session::get("inf_oc_por_facturar_tdnx.INVENTARIO");
		$origen = session::get("inf_oc_por_facturar_tdnx.ORIGEN");
		$tipo = session::get("inf_oc_por_facturar_tdnx.TIPO");
		
		$this->origen = $origen;
		session::un_set("inf_oc_por_facturar_tdnx.INVENTARIO");
		session::un_set("inf_oc_por_facturar_tdnx.ORIGEN");
		session::un_set("inf_oc_por_facturar_tdnx.TIPO");
		
		if($tipo == 'TIPO_B'){
			//////////////// SE LIMPIA LA TABLA ANTES DE LLENARLA ////////////////////////////////
			$db->EXECUTE_SP("spu_inf_oc_por_facturar_tdnx", "$cod_usuario");
	   				
			///////////////////////////// COMUNICACION CON WEB SERVICE ///////////////////////////
   			$sql = "select SISTEMA, URL_WS, USER_WS,PASSWROD_WS  from PARAMETRO_WS
					where SISTEMA = '".$origen."' ";
			$result = $db->build_results($sql);
			
			$user_ws		= $result[0]['USER_WS'];
			$passwrod_ws	= $result[0]['PASSWROD_WS'];
			$url_ws			= $result[0]['URL_WS'];
	   		 
	   		$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");
	   		$lista_item_oc 	= '';
	   		
	   		////////////////-----------------------------------------------------\\\\\\\\\\\\\\\\\
	
	   		$biggi->cli_oc_por_facturar($cod_usuario,$inventario,$origen);
	   		
	   		////////////////////////////////////////////////////////
			
			$sql = "SELECT COD_ORDEN_COMPRA
							,convert(varchar, FECHA_ORDEN_COMPRA, 103) FECHA_ORDEN_COMPRA
							,COD_NOTA_VENTA
							,COD_USUARIO_VENDEDOR
							,COD_PRODUCTO
							,NOM_PRODUCTO
							,CANTIDAD_OC
							,CANT_FA
							,CANT_POR_FACT
							,NOM_USUARIO
					FROM inf_oc_por_facturar_tdnx
					WHERE COD_USUARIO = $cod_usuario
					AND CANT_POR_FACT > 0
					order by COD_ORDEN_COMPRA";
			
			parent::w_informe_pantalla('inf_oc_por_facturar_tdnx', $sql, $_REQUEST['cod_item_menu']);
	
			if ($origen == "BODEGA")
					$this->nom_template = "TODOINOX/wo_inf_oc_por_facturar_tdnx_bod.htm";
		
			// headers
			$this->add_header(new header_num('COD_ORDEN_COMPRA', 'COD_ORDEN_COMPRA', 'NUM OC'));
			$this->add_header(new header_date('FECHA_ORDEN_COMPRA', 'FECHA_ORDEN_COMPRA', 'Fecha OC'));
	
			// COD_SOLICITUD DE COMPRA SE ALMACENARA EN COD_NOTA_VENTA PARA NO ALTERAR LA TABLA inf_oc_por_facturar_tdnx
			if ($origen == "BODEGA")
				$this->add_header(new header_num('COD_NOTA_VENTA', 'COD_NOTA_VENTA', 'Cod. Solicitud'));
			else
				$this->add_header(new header_num('COD_NOTA_VENTA', 'COD_NOTA_VENTA', 'Nº NV'));
	
			$sql = "SELECT DISTINCT COD_USUARIO_VENDEDOR,NOM_USUARIO FROM inf_oc_por_facturar_tdnx order by COD_USUARIO_VENDEDOR";
			$this->add_header($control = new header_drop_down_string('COD_USUARIO_VENDEDOR', "COD_USUARIO_VENDEDOR", 'V1',$sql));
			$control->field_bd_order = 'NOM_USUARIO';
			$this->add_header(new header_text('COD_PRODUCTO', 'COD_PRODUCTO', 'Num producto'));
			$this->add_header(new header_text('NOM_PRODUCTO', "NOM_PRODUCTO", 'Nombre Product'));
			$this->add_header(new header_num('CANTIDAD_OC', 'CANTIDAD_OC', 'Cant OC'));
			$this->add_header(new header_num('CANT_FA', 'CANT_FA', 'Cant Facturada'));
			$this->add_header(new header_num('CANT_POR_FACT', 'CANT_POR_FACT', 'Cant Por Facturar'));
		}else{
			//////////////// SE LIMPIA LA TABLA ANTES DE LLENARLA ////////////////////////////////
			$db->EXECUTE_SP("spu_inf_oc_x_fact_tdnx", "$cod_usuario");

			///////////////////////////// COMUNICACION CON WEB SERVICE ///////////////////////////
   			$sql = "select SISTEMA, URL_WS, USER_WS,PASSWROD_WS  from PARAMETRO_WS
					where SISTEMA = '".$origen."' ";
			$result = $db->build_results($sql);

			$user_ws		= $result[0]['USER_WS'];
			$passwrod_ws	= $result[0]['PASSWROD_WS'];
			$url_ws			= $result[0]['URL_WS'];

	   		$biggi = new client_biggi("$user_ws", "$passwrod_ws", "$url_ws");	
	   		////////////////-----------------------------------------------------\\\\\\\\\\\\\\\\\

	   		$biggi->cli_oc_x_facturar_tipo_a($cod_usuario,$inventario,$origen);

	   		$sql = "select COD_ORDEN_COMPRA
						  ,CASE STATUS_FACTURACION
						  	WHEN 'S' THEN 'SI'
						  	ELSE 'NO'
						  END STATUS_FACTURACION	
						  ,CONVERT(VARCHAR, FECHA_FACTURACION, 103) FECHA_FACTURACION
						  ,FECHA_FACTURACION DATE_FECHA_FACTURACION
						  ,COD_NOTA_VENTA
						  ,CONVERT(VARCHAR, FECHA_ENTREGA_NV, 103) FECHA_ENTREGA_NV
						  ,FECHA_ENTREGA_NV DATE_FECHA_ENTREGA_NV
						  ,NOM_CLIENTE_NV
						  ,COD_USUARIO_VENDEDOR
						  ,TOTAL_NETO_OC
						  ,(CONVERT(VARCHAR,TOTAL_NETO_FACTURADO) + '%') TOTAL_NETO_FACTURADO
						  ,(CONVERT(VARCHAR,TOTAL_NETO_X_FACTURAR) + '%') TOTAL_NETO_X_FACTURAR
						  ,NOM_USUARIO
					from INF_OC_X_FACT_TDNX_TIPO_A
					where COD_USUARIO = $cod_usuario
					and TOTAL_NETO_X_FACTURAR <> 0
					order by STATUS_FACTURACION DESC, FECHA_FACTURACION DESC, COD_ORDEN_COMPRA DESC";
			
			parent::w_informe_pantalla('inf_oc_por_facturar_tdnx', $sql, $_REQUEST['cod_item_menu']);
			$this->nom_template = "TODOINOX/wo_inf_oc_x_fact_tdnx_tipo_a.htm";
			$this->dw->add_control(new static_num('TOTAL_NETO_OC'));
			
			$this->add_header(new header_num('COD_ORDEN_COMPRA', 'COD_ORDEN_COMPRA', 'N° OC'));
			$this->add_header(new header_text('STATUS_FACTURACION', 'STATUS_FACTURACION', 'Autoriza Facturación'));
			$this->add_header($control = new header_date('FECHA_FACTURACION', 'CONVERT(VARCHAR, FECHA_FACTURACION, 103)', 'Fecha solicita Facturación'));
			$control->field_bd_order = 'DATE_FECHA_FACTURACION';
			$this->add_header(new header_num('COD_NOTA_VENTA', 'COD_NOTA_VENTA', 'N° NV'));
			$this->add_header($control = new header_date('FECHA_ENTREGA_NV', 'CONVERT(VARCHAR, FECHA_ENTREGA_NV, 103)', 'Fecha Entrega NV.'));
			$control->field_bd_order = 'DATE_FECHA_ENTREGA_NV';
			$this->add_header(new header_text('NOM_CLIENTE_NV', 'NOM_CLIENTE_NV', 'Cliente'));
			$sql = "SELECT DISTINCT COD_USUARIO_VENDEDOR,NOM_USUARIO FROM INF_OC_X_FACT_TDNX_TIPO_A order by COD_USUARIO_VENDEDOR";
			$this->add_header($control = new header_drop_down_string('COD_USUARIO_VENDEDOR', "COD_USUARIO_VENDEDOR", 'V1',$sql));
			$this->add_header(new header_num('TOTAL_NETO_OC', 'TOTAL_NETO_OC', 'Neto OC'));
			$this->add_header(new header_num('TOTAL_NETO_FACTURADO', 'TOTAL_NETO_FACTURADO', '% Fact.'));
			$this->add_header(new header_num('TOTAL_NETO_X_FACTURAR', 'TOTAL_NETO_X_FACTURAR', '% x Fact.'));
			
		}
	}
	
	function procesa_event(){
		if(isset($_POST['b_back_x']))
			header('Location:' . $this->root_url . 'appl/inf_oc_por_facturar_tdnx/TODOINOX/inf_oc_por_facturar_tdnx.php?cod_item_menu='.$this->cod_item_menu_parametro);
		else
			parent::procesa_event();	
	}
	
	function redraw_item(&$temp, $ind, $record){
		parent::redraw_item(&$temp, $ind, $record);
		$cod_orden_compra = $this->dw->get_item($record, 'COD_ORDEN_COMPRA');
		$temp->setVar("wo_registro.WO_DETALLE_OC", "<img id=\"b_detalle\" onclick=\"lec_orden_compra_tdnx('$cod_orden_compra', '$this->origen');\" src=\"../../../../commonlib/trunk/images/lupa2.jpg\" name=\"b_detalle\">");
	}
}
?>