<?php
class wo_inf_oc_por_facturar_tdnx extends wo_inf_oc_por_facturar_tdnx_base {
	var $origen;
	var $inventario;
	var $tipo;
	var $permiso;
	const K_PERMITE_FACTURAR_INF_OC = '992095';

	function wo_inf_oc_por_facturar_tdnx(){
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$cod_usuario = session::get("COD_USUARIO");
		
		$inventario = session::get("inf_oc_por_facturar_tdnx.INVENTARIO");
		$origen = session::get("inf_oc_por_facturar_tdnx.ORIGEN");
		$tipo = session::get("inf_oc_por_facturar_tdnx.TIPO");
		
		$this->origen = $origen;
		$this->inventario = $inventario;
		$this->tipo = $tipo;
		$this->permiso = $this->get_privilegio_opcion_usuario(self::K_PERMITE_FACTURAR_INF_OC, $cod_usuario);

		session::un_set("inf_oc_por_facturar_tdnx.INVENTARIO");
		session::un_set("inf_oc_por_facturar_tdnx.ORIGEN");
		session::un_set("inf_oc_por_facturar_tdnx.TIPO");
		session::un_set("FACTURA_DESDE_INF_X_FAC");
		
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
			//////////////////////////////////////////////////////////////////////////////////////

			///////////////////////////// AQUI SE EJECUTA LO QUE HACIA CON WEBSERVICE ///////////////////////////			   
			$sql= "SELECT COD_ORDEN_COMPRA
						,NV.COD_NOTA_VENTA
						,CONVERT(VARCHAR, NV.FECHA_ENTREGA, 103) FECHA_ENTREGA
						,CONVERT(VARCHAR, O.FECHA_ORDEN_COMPRA, 103) FECHA_ORDEN_COMPRA
						,(SELECT NOM_EMPRESA FROM BIGGI.dbo.EMPRESA WHERE COD_EMPRESA = NV.COD_EMPRESA) NOM_CLIENTE_NV
						,(SELECT U.NOM_USUARIO FROM BIGGI.dbo.USUARIO U WHERE U.COD_USUARIO = nv.COD_USUARIO_VENDEDOR1) NOM_USUARIO
						,O.TOTAL_NETO
						,O.AUTORIZA_FACTURACION
						,CONVERT(VARCHAR, O.FECHA_SOLICITA_FACTURACION, 103) FECHA_SOLICITA_FACTURACION
						,(SELECT INI_USUARIO FROM BIGGI.dbo.USUARIO WHERE COD_USUARIO = nv.COD_USUARIO_VENDEDOR1) COD_USUARIO_VENDEDOR
						,(SELECT COUNT(*) FROM BIGGI.dbo.ITEM_ORDEN_COMPRA IIO WHERE IIO.COD_ORDEN_COMPRA = O.COD_ORDEN_COMPRA AND IIO.FACTURADO_SIN_WS <> 'S') CANTIDAD_ITEM
				FROM BIGGI.dbo.ORDEN_COMPRA O, BIGGI.dbo.NOTA_VENTA NV
				WHERE O.COD_ORDEN_COMPRA > 175780
				AND O.COD_EMPRESA = 1302    --todoinox
				AND O.COD_NOTA_VENTA = NV.COD_NOTA_VENTA
				--15-11-2018 VM+MH desde OC 221039 en adelante que considere estado 3
				and (O.COD_ESTADO_ORDEN_COMPRA = 1 or (O.COD_ESTADO_ORDEN_COMPRA = 3 and o.COD_ORDEN_COMPRA >= 221039))					
				AND BIGGI.dbo.f_oc_get_saldo_sin_faprov(O.COD_ORDEN_COMPRA) > 0
				AND (SELECT COUNT(*) FROM BIGGI.dbo.ITEM_ORDEN_COMPRA IIO WHERE IIO.COD_ORDEN_COMPRA = O.COD_ORDEN_COMPRA AND IIO.FACTURADO_SIN_WS <> 'S') <> 0";
		   
			$result_ws = $db->build_results($sql);

			$sp = "spi_inf_oc_x_fact_tdnx";
            
            for($i=0 ; $i < count($result_ws) ; $i++){
                $fecha_entrega				= $this->str2date($result_ws[$i]['FECHA_ENTREGA']);
                $fecha_oc					= $this->str2date($result_ws[$i]['FECHA_ORDEN_COMPRA']);
                
                $fecha_solicita_facturacion	= $result_ws[$i]['FECHA_SOLICITA_FACTURACION'];
                $fecha_solicita_facturacion = ($fecha_solicita_facturacion =='') ? "null" : $this->str2date($fecha_solicita_facturacion);
                
                $autoriza_facturacion 		= $result_ws[$i]['AUTORIZA_FACTURACION'];
                $autoriza_facturacion 		= ($autoriza_facturacion =='') ? "null" : "'$autoriza_facturacion'";
                
                $param =   "'$origen'
							,".$result_ws[$i]['COD_ORDEN_COMPRA']."
							,$autoriza_facturacion
							,$fecha_solicita_facturacion
							,".$result_ws[$i]['COD_NOTA_VENTA']."
							,$fecha_entrega
							,$cod_usuario
							,'".$result_ws[$i]['NOM_CLIENTE_NV']."'
							,'".$result_ws[$i]['COD_USUARIO_VENDEDOR']."'
							,".$result_ws[$i]['TOTAL_NETO']."
							,'".$result_ws[$i]['NOM_USUARIO']."'
                            ,$fecha_oc";
                
                $db->EXECUTE_SP($sp, $param);
            }
			/////////////////////////////////////////////////////////////////////////////////////////////////////

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
                          ,CONVERT(VARCHAR,FECHA_ORDEN_COMPRA,103)FECHA_ORDEN_COMPRA
					from INF_OC_X_FACT_TDNX_TIPO_A
					where COD_USUARIO = $cod_usuario
					and TOTAL_NETO_X_FACTURAR > 0
					order by cod_nota_venta asc";
					//22092020 AS solicita ordenar este listado por Nro NV
					//order by STATUS_FACTURACION DESC, FECHA_FACTURACION DESC, COD_ORDEN_COMPRA DESC";
					//24032022 and TOTAL_NETO_X_FACTURAR > 0 se deja asi para que no muestre la OC refacturadas ej: 256111
			
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
			$sql = "SELECT DISTINCT COD_USUARIO_VENDEDOR,NOM_USUARIO 
                    FROM INF_OC_X_FACT_TDNX_TIPO_A 
                    WHERE COD_USUARIO  = $cod_usuario
                    order by COD_USUARIO_VENDEDOR";
			$this->add_header($control = new header_drop_down_string('COD_USUARIO_VENDEDOR', "COD_USUARIO_VENDEDOR", 'V1',$sql));
			$this->add_header(new header_num('TOTAL_NETO_OC', 'TOTAL_NETO_OC', 'Neto OC'));
			$this->add_header(new header_num('TOTAL_NETO_FACTURADO', 'TOTAL_NETO_FACTURADO', '% Fact.'));
			$this->add_header(new header_num('TOTAL_NETO_X_FACTURAR', 'TOTAL_NETO_X_FACTURAR', '% x Fact.'));
			$this->add_header(new header_date('FECHA_ORDEN_COMPRA', 'FECHA_ORDEN_COMPRA', 'Fecha OC'));
			
		}
	}
	
	function procesa_event(){
		if(isset($_POST['b_back_x'])){
			header('Location:' . $this->root_url . 'appl/inf_oc_por_facturar_tdnx/TODOINOX/inf_oc_por_facturar_tdnx.php?cod_item_menu='.$this->cod_item_menu_parametro);
		}else if(isset($_POST['b_factura_oc_x'])){
			$nro_orden_compra = $_POST['NRO_OC_INF_FACTURA'];
			$this->dws['dw_wo_factura'] = new wo_factura();
			$this->dws['dw_wo_factura']->cod_item_menu = '1535';
			$this->dws['dw_wo_factura']->retrieve();
			$this->dws['dw_wo_factura']->crear_desde_oc($nro_orden_compra, 'COMERCIAL');

			session::set('FACTURA_DESDE_INF_X_FAC', 'true');
			session::set('inf_oc_por_facturar_tdnx.ORIGEN', $this->origen);
			session::set('inf_oc_por_facturar_tdnx.INVENTARIO', $this->inventario);
			session::set('inf_oc_por_facturar_tdnx.TIPO', $this->tipo);
		
		}else
			parent::procesa_event();	
	}

	function redraw_item(&$temp, $ind, $record){
		parent::redraw_item($temp, $ind, $record);
		$cod_orden_compra = $this->dw->get_item($record, 'COD_ORDEN_COMPRA');
		$temp->setVar("wo_registro.WO_DETALLE_OC", "<img id=\"b_detalle\" onclick=\"lec_orden_compra_tdnx('$cod_orden_compra', '$this->origen', '$this->inventario');\" src=\"../../../../commonlib/trunk/images/lupa2.jpg\" name=\"b_detalle\">");
		
		if($this->origen == 'COMERCIAL' && $this->tipo == 'TIPO_A'){
			if($this->permiso == 'E')
				$temp->setVar("wo_registro.WO_FACTURA_OC", "<input name=\"b_factura_oc\" id=\"b_factura_oc\" onclick=\"document.getElementById('NRO_OC_INF_FACTURA').value = $cod_orden_compra;\"  value=\"'$ind'\" src=\"../../images_appl/b_dte_xml.png\" type=\"image\">");
			else
				$temp->setVar("wo_registro.WO_FACTURA_OC", "<img name=\"b_factura_oc\" id=\"b_factura_oc\" src=\"../../images_appl/b_dte_xml_d.png\" type=\"image\">");
		}
			
	}
}
?>