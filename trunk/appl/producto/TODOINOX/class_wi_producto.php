<?php
require_once(dirname(__FILE__)."/../../../../../commonlib/trunk/php/auto_load.php");

class dw_log_cambio extends datawindow{
	function dw_log_cambio(){
		$sql = "SELECT LC.COD_LOG_CAMBIO
					  ,CONVERT(VARCHAR, FECHA_CAMBIO, 103) + ' ' + CONVERT(VARCHAR, FECHA_CAMBIO, 108) FECHA_CAMBIO
					  ,NOM_USUARIO
					  ,CASE NOM_CAMPO
					  	WHEN 'PRECIO_VENTA_INTERNO' THEN 'Precio Venta Interno'
					  	WHEN 'PRECIO_VENTA_PUBLICO' THEN 'Precio Venta Publico'
					  END NOM_CAMPO
					  ,VALOR_ANTIGUO
					  ,VALOR_NUEVO
					  ,CASE (ROW_NUMBER() OVER (PARTITION BY NOM_CAMPO ORDER BY LC.COD_LOG_CAMBIO DESC, COD_DETALLE_CAMBIO DESC))
						WHEN 1 THEN '#00ff00'
						ELSE ''
					  END REG_DESTACADO_TR
					  ,CASE (ROW_NUMBER() OVER (PARTITION BY NOM_CAMPO ORDER BY LC.COD_LOG_CAMBIO DESC, COD_DETALLE_CAMBIO DESC))
						WHEN 1 THEN 'bold'
						ELSE ''
					  END REG_DESTACADO_NEG
				FROM LOG_CAMBIO LC
					,DETALLE_CAMBIO DC
					,USUARIO U
				WHERE NOM_TABLA = 'PRODUCTO'
				AND KEY_TABLA = '{KEY1}'
				AND NOM_CAMPO IN ('PRECIO_VENTA_PUBLICO', 'PRECIO_VENTA_INTERNO')
				AND LC.COD_LOG_CAMBIO = DC.COD_LOG_CAMBIO
				AND LC.COD_USUARIO = U.COD_USUARIO
				ORDER BY COD_LOG_CAMBIO DESC, COD_DETALLE_CAMBIO DESC";

		parent::datawindow($sql, 'DW_LOG_CAMBIO');

		$this->add_control(new static_num('VALOR_ANTIGUO'));
		$this->add_control(new static_num('VALOR_NUEVO'));
	}
}

class dw_estado_stock extends datawindow{
	function dw_estado_stock(){
		$sql = "exec spdw_estado_stock '{KEY1}'";

		parent::datawindow($sql, 'ITEM_ESTADO_STOCK');

		$this->add_control(new static_text('NOMBRE_TITULO'));
		$this->add_control(new static_num('DIAS_30_S'));
		$this->add_control(new static_num('DIAS_60_S'));
		$this->add_control(new static_num('DIAS_90_S'));
	}

	function truncateFloat($number, $digitos){
	    $raiz = 10;
	    $multiplicador = pow ($raiz,$digitos);
	    $resultado = ((int)($number * $multiplicador)) / $multiplicador;
	    return number_format($resultado, $digitos);

	}

	function fill_template(&$temp) {
		parent::fill_template($temp);
		if($this->row_count() <> 0){ //Solo lo hara si hay producto
			$COD_PRODUCTO = $this->get_item(0, 'COD_PRODUCTO');

			$DIAS_30_F = $this->get_item(0, 'DIAS_30_S');
			$DIAS_60_F = $this->get_item(0, 'DIAS_60_S');
			$DIAS_90_F = $this->get_item(0, 'DIAS_90_S');

			$DIAS_30_NC = $this->get_item(1, 'DIAS_30_S');
			$DIAS_60_NC = $this->get_item(1, 'DIAS_60_S');
			$DIAS_90_NC = $this->get_item(1, 'DIAS_90_S');

			$SUM_DIAS_30 = $DIAS_30_F - $DIAS_30_NC;
			$SUM_DIAS_60 = $DIAS_60_F - $DIAS_60_NC;
			$SUM_DIAS_90 = $DIAS_90_F - $DIAS_90_NC;

			$PROMEDIO = $SUM_DIAS_90/ 3;

			$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$sql = "SELECT dbo.f_bodega_stock('$COD_PRODUCTO', 1, GETDATE()) STOCK";
			$result = $db->build_results($sql);

			if($PROMEDIO <> 0)
				$MESES_STOCK = $result[0]['STOCK']/$PROMEDIO;
			else
				$MESES_STOCK = $result[0]['STOCK'];

			$temp->setVar("SUM_DIAS_30_S", $SUM_DIAS_30);
			$temp->setVar("SUM_DIAS_60_S", $SUM_DIAS_60);
			$temp->setVar("SUM_DIAS_90_S", $SUM_DIAS_90);
			$temp->setVar("PROMEDIO_DIAS", $this->truncateFloat($PROMEDIO, 1));
			$temp->setVar("MESES_STOCK", $this->truncateFloat($MESES_STOCK, 1));
		}
	}
}

class dw_producto_ajuste extends datawindow{
	function dw_producto_ajuste(){
		$sql = "exec spdw_producto_ajuste '{KEY1}'";

		parent::datawindow($sql);

		$this->add_control(new static_text('TIPO_MOVIMIENTO'));
		$this->add_control(new static_text('COD_MOVIMIENTO'));
		$this->add_control(new static_text('FECHA_MOVIMIENTO'));
		$this->add_control(new static_num('CANTIDAD_MOVIMIENTO'));
	}
}

class dw_producto_compuesto extends dw_producto_compuesto_base{
	function dw_producto_compuesto(){
		parent::dw_producto_compuesto_base();

		$this->add_control(new edit_check_box('ARMA_COMPUESTO','S','N'));

		$this->controls['NOM_PRODUCTO']->size = 58;
		$this->controls['COD_PRODUCTO']->size = 11;

		$this->set_first_focus('COD_PRODUCTO');
	}

	function insert_row($row = -1){
		$row = parent::insert_row($row);
		$this->set_item($row, 'ORDEN_PC', $this->row_count() * 10);
		return $row;
	}
	function update($db){
		$sp = 'spu_producto_compuesto';
		for ($i = 0; $i < $this->row_count(); $i++){
			$statuts = $this->get_status_row($i);

			if ($statuts == K_ROW_NOT_MODIFIED || $statuts == K_ROW_NEW){
				continue;
			}

			$cod_producto_compuesto = $this->get_item($i, 'COD_PRODUCTO_COMPUESTO');
			$cod_producto_principal = $this->get_item($i, 'COD_PRODUCTO_PRINCIPAL');
			$cod_producto_hijo 		= $this->get_item($i, 'COD_PRODUCTO');
			$orden 					= $this->get_item($i, 'ORDEN_PC');
			$cantidad 				= $this->get_item($i, 'CANTIDAD');
			$genera_compra 			= $this->get_item($i, 'GENERA_COMPRA');
			$arma_compuesto			= $this->get_item($i, 'ARMA_COMPUESTO');

			$cod_producto_compuesto = ($cod_producto_compuesto == '') ? "null" : "$cod_producto_compuesto";

			if ($statuts == K_ROW_NEW_MODIFIED){
				$operacion = 'INSERT';
			}
			elseif ($statuts == K_ROW_MODIFIED){
				$operacion = 'UPDATE';
			}

			$param = "'$operacion', $cod_producto_compuesto,'$cod_producto_principal','$cod_producto_hijo',$orden,$cantidad, '$genera_compra', '$arma_compuesto'";

			if (!$db->EXECUTE_SP($sp, $param)){
				return false;
			}
		}
		for ($i = 0; $i < $this->row_count('delete'); $i++) {
			$cod_producto_compuesto = $this->get_item($i, 'COD_PRODUCTO_COMPUESTO', 'delete');
			if (!$db->EXECUTE_SP($sp, "'DELETE', $cod_producto_compuesto")){
			}
		}
		//Ordernar
		if ($this->row_count() > 0){
			$cod_producto = $this->get_item(0, 'COD_PRODUCTO_PRINCIPAL');
			$parametros_sp = "'PRODUCTO_COMPUESTO','PRODUCTO', null, '$cod_producto'";
			if (!$db->EXECUTE_SP('sp_orden_no_parametricas', $parametros_sp)){
				return false;
			}
		}

		return true;
	}
}

class wi_producto extends wi_producto_base {
	const K_BODEGA_EQ_TERMINADO = 1;
	const K_DOLAR_COMERCIAL = 5;
	const K_MENU_PRODUCTO = '995005';
	const K_MODIFICACION_OC_EXTRANJERA = '995015';
	function wi_producto($cod_item_menu) {
		parent::wi_producto_base($cod_item_menu);

		$cod_usuario = session::get("COD_USUARIO");
		//$sql_original = $this->dws['dw_producto']->get_sql();
		$sql = "select  top 1 P.COD_PRODUCTO COD_PRODUCTO_PRINCIPAL
						,P.COD_PRODUCTO COD_PRODUCTO_TEXT
						,P.COD_PRODUCTO COD_PRODUCTO_H
			            ,P.NOM_PRODUCTO NOM_PRODUCTO_PRINCIPAL
			            ,TP.COD_TIPO_PRODUCTO
			            ,NOM_TIPO_PRODUCTO
			            ,P.COD_MARCA
			            ,'' MARCA_H
			            ,NOM_MARCA
			            ,NOM_MARCA NOM_MARCA_NO_ING
			            ,NOM_PRODUCTO_INGLES
			            ,NOM_PRODUCTO_INGLES NOM_PRODUCTO_INGLES_NO_ING
			            ,COD_FAMILIA_PRODUCTO 
			            ,LARGO
			            ,ANCHO
			            ,ALTO
			            ,PESO
			            ,(LARGO/100 * ANCHO/100 * ALTO/100) VOLUMEN
			            ,LARGO_EMBALADO
			            ,ANCHO_EMBALADO
			            ,ALTO_EMBALADO
			            ,PESO_EMBALADO
			            ,(LARGO_EMBALADO/100 * ANCHO_EMBALADO/100 * ALTO_EMBALADO/100) VOLUMEN_EMBALADO
			            ,FACTOR_VENTA_INTERNO
                  ,FACTOR_VENTA_INTERNO FACTOR_VENTA_INTERNO_NO_ING
                  ,FACTOR_VENTA_INTERNO FACTOR_VENTA_INTERNO_AUX
			            ,PRECIO_VENTA_INTERNO
									,PRECIO_VENTA_INTERNO PRECIO_VENTA_INTERNO_UNO
                  ,PRECIO_VENTA_INTERNO PRECIO_VENTA_INTERNO_EC
			            ,PRECIO_VENTA_INTERNO PRECIO_VENTA_INTERNO_NO_ING
			            ,PRECIO_VENTA_INTERNO PRECIO_VENTA_INTERNO_NO_ING2
			            ,FACTOR_VENTA_PUBLICO
                  ,FACTOR_VENTA_PUBLICO FACTOR_VENTA_PUBLICO_NO_ING
			            ,PRECIO_VENTA_PUBLICO
               ,PRECIO_VENTA_PUBLICO PRECIO_VENTA_PUBLICO_UNO
			            ,PRECIO_VENTA_PUBLICO PRECIO_VENTA_PUBLICO_H
			            ,PRECIO_VENTA_PUBLICO PRECIO_VENTA_PUBLICO_NO_ING
			            ,dbo.f_redondeo_tdnx(PRECIO_VENTA_INTERNO * FACTOR_VENTA_PUBLICO) PRECIO_VENTA_PUB_SUG
                  ,dbo.f_redondeo_tdnx(PRECIO_VENTA_INTERNO * FACTOR_VENTA_PUBLICO) PRECIO_VENTA_PUB_SUG_EC
			            ,'none' PRECIO_INTERNO_ALTO
			            ,'none' PRECIO_INTERNO_BAJO
			            ,'none' PRECIO_PUBLICO_ALTO
			            ,'none' PRECIO_PUBLICO_BAJO
			            ,USA_ELECTRICIDAD
			            ,NRO_FASES MONOFASICO
			            ,NRO_FASES TRIFASICO
			            ,CONSUMO_ELECTRICIDAD
			            ,RANGO_TEMPERATURA
			            ,VOLTAJE
			            ,FRECUENCIA
			            ,NRO_CERTIFICADO_ELECTRICO
			            ,USA_GAS
			            ,POTENCIA
			            ,CONSUMO_GAS
			            ,USA_VAPOR
			            ,NRO_CERTIFICADO_GAS
			            ,CONSUMO_VAPOR
			            ,PRESION_VAPOR
			            ,USA_AGUA_FRIA
			            ,USA_AGUA_CALIENTE
			            ,CAUDAL
			            ,PRESION_AGUA
			            ,DIAMETRO_CANERIA
			            ,USA_VENTILACION
			            ,CAIDA_PRESION
			            ,DIAMETRO_DUCTO
			            ,NRO_FILTROS
			            ,USA_DESAGUE
			            ,VOLUMEN VOLUMEN_ESP
			            ,P.POTENCIA_KW
			            ,DIAMETRO_DESAGUE
			            ,MANEJA_INVENTARIO
			            ,STOCK_CRITICO
			            ,TIEMPO_REPOSICION
		                ,FOTO_GRANDE
		                ,FOTO_CHICA
		                ,'' FOTO_CON_CAMBIO
		                ,ISNULL(PL.ES_COMPUESTO, 'N') ES_COMPUESTO
		                ,P.COD_CLASIF_INVENTARIO
		                ,PRECIO_LIBRE
		                ,ES_DESPACHABLE
		                ,'' TABLE_PRODUCTO_COMPUESTO
		                ,'' ULTIMO_REG_INGRESO
		                ,case
	                    when (dbo.f_get_autoriza_menu($cod_usuario, ".self::K_MENU_PRODUCTO.") = 'E') then dbo.number_format(dbo.f_bodega_stock(P.COD_PRODUCTO, ".self::K_BODEGA_EQ_TERMINADO.", GETDATE()),0,',','.')
                    	when (dbo.f_get_autoriza_menu($cod_usuario, ".self::K_MENU_PRODUCTO.") = 'N') and (dbo.f_bodega_stock(P.COD_PRODUCTO, ".self::K_BODEGA_EQ_TERMINADO.", GETDATE()) > 0)  then 'HAY'
                    	else 'NO HAY'
                		end STOCK
		                ,COD_EQUIPO_OC_EX
		                ,DESC_EQUIPO_OC_EX
						,dbo.f_prod_RI(P.COD_PRODUCTO, 'NUMERO_REGISTRO_INGRESO') NUMERO_REGISTRO_INGRESO
						,case dbo.f_prod_RI_pendiente(P.COD_PRODUCTO)
							when NULL then 'No hay RI Pendientes de entrada'
							else 'RI Pendiente de entrada: ' + dbo.f_prod_RI_pendiente(P.COD_PRODUCTO)
						end RI_PENDIENTES
						,convert(varchar(10),dbo.f_get_fecha_registro_ingreso(P.COD_PRODUCTO),103) FECHA_REGISTRO_INGRESO
						,dbo.f_prod_RI(P.COD_PRODUCTO, 'PRECIO') COSTO_EX_FCA
						,dbo.f_prod_RI(P.COD_PRODUCTO, 'FACTOR_IMP') FACTOR_IMP
						,round(dbo.f_prod_RI(P.COD_PRODUCTO, 'PRECIO') * dbo.f_prod_RI(P.COD_PRODUCTO, 'FACTOR_IMP'),2) COSTO_BASE_DOLAR
						,round(dbo.f_prod_RI(P.COD_PRODUCTO, 'PRECIO') * dbo.f_prod_RI(P.COD_PRODUCTO, 'FACTOR_IMP'),2) COSTO_BASE_DOLAR_H
						,dbo.f_prod_get_costo_base(P.COD_PRODUCTO) COSTO_BASE_PI
						,dbo.f_prod_get_costo_base(P.COD_PRODUCTO) COSTO_BASE_PESOS
						,dbo.f_prod_get_costo_base(P.COD_PRODUCTO) COSTO_BASE_PESOS_H
		                ,dbo.f_get_parametro(".self::K_DOLAR_COMERCIAL.") DOLAR
				        ,case MANEJA_INVENTARIO
							when 'N' then ''
							else (SELECT CONVERT(VARCHAR(10),max(E.FECHA_ENTRADA_BODEGA),103)
								  FROM ITEM_ENTRADA_BODEGA I
									  ,ENTRADA_BODEGA E
								  WHERE I.COD_PRODUCTO = P.COD_PRODUCTO
								  AND E.COD_ENTRADA_BODEGA = I.COD_ENTRADA_BODEGA)
						end FECHA_ENTRADA_BODEGA
				        ,case MANEJA_INVENTARIO
							when 'N' then ''
							else(SELECT CONVERT(VARCHAR,max(E.COD_ENTRADA_BODEGA))
							FROM ITEM_ENTRADA_BODEGA I
								 ,ENTRADA_BODEGA E
							WHERE I.COD_PRODUCTO = P.COD_PRODUCTO
							AND E.COD_ENTRADA_BODEGA = I.COD_ENTRADA_BODEGA)
						end NRO_ENTRADA_BODEGA
				        ,case MANEJA_INVENTARIO
							when 'N' then ''
							else(SELECT CONVERT(VARCHAR(10),max(E.FECHA_SALIDA_BODEGA),103)
							FROM ITEM_SALIDA_BODEGA I
								 ,SALIDA_BODEGA E
							WHERE I.COD_PRODUCTO = P.COD_PRODUCTO
							AND E.COD_SALIDA_BODEGA = I.COD_SALIDA_BODEGA)
						end FECHA_SALIDA_BODEGA
				        ,case MANEJA_INVENTARIO
							when 'N' then ''
				            else(SELECT CONVERT(VARCHAR,max(E.COD_SALIDA_BODEGA))
							FROM ITEM_SALIDA_BODEGA I
								 ,SALIDA_BODEGA E
							WHERE I.COD_PRODUCTO = P.COD_PRODUCTO
							AND E.COD_SALIDA_BODEGA = I.COD_SALIDA_BODEGA)
						end NRO_SALIDA_BODEGA
			            ,dbo.f_redondeo_tdnx(dbo.f_prod_get_costo_base(P.COD_PRODUCTO) * FACTOR_VENTA_INTERNO) PRECIO_VENTA_INT_SUG
			            ,dbo.f_redondeo_tdnx(dbo.f_prod_get_costo_base(P.COD_PRODUCTO) * FACTOR_VENTA_INTERNO) PRECIO_VENTA_INT_SUG_H
	        			,dbo.f_bodega_pmp_us(P.COD_PRODUCTO,".self::K_BODEGA_EQ_TERMINADO.",getdate()) PMP_US
	        			,dbo.f_bodega_pmp(P.COD_PRODUCTO,".self::K_BODEGA_EQ_TERMINADO.",getdate()) PMP_PESOS
	        			,case MANEJA_INVENTARIO
	        				when 'N' then ''
	        				else dbo.f_cod_tipo_doc(P.COD_PRODUCTO, 'ENTRADA')
	        			END ENTRADA_BODEGA
	        			,case MANEJA_INVENTARIO
	        				when 'N' then ''
	        				else dbo.f_cod_tipo_doc(P.COD_PRODUCTO, 'SALIDA')
	        			END SALIDA_BODEGA
	        			,dbo.f_ri_bodega_entrada(dbo.f_prod_RI(P.COD_PRODUCTO, 'NUMERO_REGISTRO_INGRESO')) COD_BODEGA_ENTRADA
	        			,P.SISTEMA_VALIDO
		                ,SUBSTRING(P.SISTEMA_VALIDO, 1, 1) PRODUCTO_COMERCIAL
		                ,SUBSTRING(P.SISTEMA_VALIDO, 2, 1) PRODUCTO_BODEGA
		                ,SUBSTRING(P.SISTEMA_VALIDO, 3, 1) PRODUCTO_RENTAL
		                ,SUBSTRING(P.SISTEMA_VALIDO, 4, 1) PRODUCTO_TODOINOX
		                ,P.COD_TIPO_OBSERVACION_COMEX
		                ,TOC.NOM_TIPO_OBSERVACION_COMEX
		                ,CASE
		                	WHEN MANEJA_STOCK_CRITICO = 'S' AND (dbo.f_get_autoriza_menu($cod_usuario, '996015') = 'E') THEN ''
		                	ELSE 'none'
		                END DISPLAY_TAB_STOCK
		                ,DATEPART(YEAR, DATEADD(YEAR, -1, GETDATE())) ANO_ANTERIOR
		                ,dbo.f_get_tot_factura_anterior(P.COD_PRODUCTO) TOTAL_FACTURADO_ANTERIOR
						,dbo.f_redondeo_tdnx(dbo.f_get_costbase_aux(P.COD_PRODUCTO)* FACTOR_VENTA_INTERNO) SUM_TOTAL_COSTO_BASE_AUX
                    	,dbo.f_get_costbase_aux(P.COD_PRODUCTO) TOTAL_COSTO_BASE_EC
						,dbo.f_redondeo_tdnx(dbo.f_get_costbase_aux(P.COD_PRODUCTO) * FACTOR_VENTA_INTERNO) + ISNULL(PRECIO_ADICIONAL, 0) PRCO_VENTA_INT_SUG_EC
						,'' DISPLAY_FOOTER_PI_UNO
						,'' DISPLAY_FOOTER_PI_DOS
						,EQ_NO_COMPUESTO
						,CASE
							WHEN PRECIO_ADICIONAL IS NULL THEN 0
							ELSE PRECIO_ADICIONAL
						END PRECIO_ADICIONAL	
						,(dbo.f_redondeo_tdnx(dbo.f_get_costbase_aux(P.COD_PRODUCTO)* FACTOR_VENTA_INTERNO) + ISNULL(PRECIO_ADICIONAL, 0)) PRECIO_TOTAL_SUG
        from   			PRODUCTO P LEFT OUTER JOIN PRODUCTO_LOCAL PL ON PL.COD_PRODUCTO = P.COD_PRODUCTO
        				,MARCA M
        				,TIPO_PRODUCTO TP
        				,TIPO_OBSERVACION_COMEX TOC
        where			P.COD_PRODUCTO = '{KEY1}'
        				AND P.COD_TIPO_OBSERVACION_COMEX = TOC.COD_TIPO_OBSERVACION_COMEX
        				AND P.COD_MARCA = M.COD_MARCA
        				AND P.COD_TIPO_PRODUCTO = TP.COD_TIPO_PRODUCTO";
		$this->dws['dw_producto']->set_sql($sql);
		$this->dws['dw_producto']->retrieve();

		// asigna los formatos
		$this->dws['dw_producto']->add_control($control = new edit_num('PRECIO_ADICIONAL', 10, 16));
		$control->set_onChange("tot_costo_base_dos();redondeo_biggi(); calc_precio_int_pub();");
		$this->dws['dw_producto']->add_control(new static_num('PRECIO_TOTAL_SUG'));

		$this->dws['dw_producto']->add_control(new static_num('SUM_TOTAL_COSTO_BASE_AUX'));
		$sql = "select COD_CLASIF_INVENTARIO
						,NOM_CLASIF_INVENTARIO
				from CLASIF_INVENTARIO";
		$this->dws['dw_producto']->add_control(new drop_down_dw('COD_CLASIF_INVENTARIO', $sql, 100));

		$sql = "select COD_TIPO_OBSERVACION_COMEX
						,NOM_TIPO_OBSERVACION_COMEX
				from TIPO_OBSERVACION_COMEX";
		$this->dws['dw_producto']->add_control(new drop_down_dw('COD_TIPO_OBSERVACION_COMEX', $sql, 135));

		$this->dws['dw_producto']->add_control(new edit_text('COD_EQUIPO_OC_EX',20,100));
		$this->dws['dw_producto']->add_control(new edit_text('DESC_EQUIPO_OC_EX',20,100));

		$this->dws['dw_producto']->add_control($control = new edit_num_producto('PRECIO_VENTA_PUBLICO'));
		$control->set_onChange("valida_precio_venta_pub();");
		$this->dws['dw_producto']->add_control(new static_text('NUMERO_REGISTRO_INGRESO'));
		$this->dws['dw_producto']->add_control(new static_text('FECHA_REGISTRO_INGRESO'));
		$this->dws['dw_producto']->add_control(new static_num('COSTO_EX_FCA', 2));
		$this->dws['dw_producto']->add_control(new static_num('FACTOR_IMP', 2));
		$this->dws['dw_producto']->add_control(new static_num('DOLAR'));
		$this->dws['dw_producto']->add_control(new static_num('COSTO_BASE_DOLAR', 2));
		$this->dws['dw_producto']->add_control(new static_num('PRECIO_VENTA_INTERNO_NO_ING2'));
		$this->dws['dw_producto']->add_control(new static_num('PRECIO_VENTA_PUBLICO_NO_ING'));
		$this->dws['dw_producto']->add_control(new static_text('FECHA_ENTRADA_BODEGA'));
		$this->dws['dw_producto']->add_control(new static_text('NRO_ENTRADA_BODEGA'));
		$this->dws['dw_producto']->add_control(new static_text('FECHA_SALIDA_BODEGA'));
		$this->dws['dw_producto']->add_control(new static_text('NRO_SALIDA_BODEGA'));
		$this->dws['dw_producto']->add_control(new edit_text('COSTO_BASE_DOLAR_H', 20, 20, 'hidden'));
		$this->dws['dw_producto']->add_control(new edit_text('COSTO_BASE_PESOS_H', 20, 20, 'hidden'));
		$this->dws['dw_producto']->add_control(new edit_text('PRECIO_VENTA_INT_SUG_H', 20, 20, 'hidden'));
		$this->dws['dw_producto']->add_control(new static_num('PMP_US'));
		$this->dws['dw_producto']->add_control(new static_num('PMP_PESOS'));

		$this->dws['dw_producto']->add_control($control = new edit_check_box('PRODUCTO_COMERCIAL','S','N'));
		$control->set_onChange("vl_modify_check = true;");
		$this->dws['dw_producto']->add_control($control = new edit_check_box('PRODUCTO_TODOINOX','S','N'));
		$control->set_onChange("vl_modify_check = true;");
		$this->dws['dw_producto']->add_control($control = new edit_check_box('PRODUCTO_BODEGA','S','N'));
		$control->set_onChange("vl_modify_check = true;");
		$this->dws['dw_producto']->add_control($control = new edit_check_box('PRODUCTO_RENTAL','S','N'));
		$control->set_onChange("vl_modify_check = true;");


		$this->add_auditoria('COD_CLASIF_INVENTARIO');
		$this->add_auditoria('SISTEMA_VALIDO');

		$this->dws['dw_producto_ajuste'] = new dw_producto_ajuste();
		$this->dws['dw_estado_stock'] = new dw_estado_stock();
		$this->dws['dw_log_cambio'] = new dw_log_cambio();

		$this->dws['dw_producto']->set_mandatory('COD_TIPO_OBSERVACION_COMEX', 'Observación Comex');
	}

	function load_record(){
		parent::load_record();
		$cod_producto = $this->get_item_wo($this->current_record, 'COD_PRODUCTO');
		$this->dws['dw_producto_ajuste']->retrieve($cod_producto);
		$this->dws['dw_estado_stock']->retrieve($cod_producto);
		$this->dws['dw_log_cambio']->retrieve($cod_producto);

		$costo_base_pesos = $this->dws['dw_producto']->get_item(0, 'COSTO_BASE_PESOS_H');
		$costo_base_pesos	=  number_format($costo_base_pesos, 0, ',', '.');
		$this->dws['dw_producto']->set_item(0, 'COSTO_BASE_PI', $costo_base_pesos);
		$this->dws['dw_producto']->set_item(0, 'MARCA_H', '');

		if($this->tiene_privilegio_opcion(self::K_MODIFICACION_OC_EXTRANJERA)== 'S'){
			$this->dws['dw_producto']->set_entrable('COD_EQUIPO_OC_EX',	true);
			$this->dws['dw_producto']->set_entrable('DESC_EQUIPO_OC_EX',	true);
		}
		else{
			$this->dws['dw_producto']->set_entrable('COD_EQUIPO_OC_EX',	false);
			$this->dws['dw_producto']->set_entrable('DESC_EQUIPO_OC_EX',	false);
		}

		$eq_no_compuesto = $this->dws['dw_producto']->get_item(0, 'EQ_NO_COMPUESTO');
		if($this->dws['dw_producto_compuesto']->row_count() > 0 && $eq_no_compuesto == 'N'){
			$this->dws['dw_producto']->set_item(0, 'DISPLAY_FOOTER_PI_UNO', 'none');
			$this->dws['dw_producto']->set_item(0, 'DISPLAY_FOOTER_PI_DOS', '');
		}else{
			$this->dws['dw_producto']->set_item(0, 'DISPLAY_FOOTER_PI_UNO', '');
			$this->dws['dw_producto']->set_item(0, 'DISPLAY_FOOTER_PI_DOS', 'none');
		}
	}


	function new_record(){
		parent::new_record();
		$this->dws['dw_producto']->set_item(0, 'COD_TIPO_OBSERVACION_COMEX', 1); //Sin Observaciones
		$this->dws['dw_producto']->set_item(0, 'DISPLAY_FOOTER_PI_UNO', '');
		$this->dws['dw_producto']->set_item(0, 'DISPLAY_FOOTER_PI_DOS', 'none');
	}

	function save_record($db){
		//parent::save_record();
		$cod_producto 				= $this->dws['dw_producto']->get_item(0, 'COD_PRODUCTO_PRINCIPAL');
		$nom_producto 				= $this->dws['dw_producto']->get_item(0, 'NOM_PRODUCTO_PRINCIPAL');
		$cod_tipo_producto 			= $this->dws['dw_producto']->get_item(0, 'COD_TIPO_PRODUCTO');
		$cod_marca 					= $this->dws['dw_producto']->get_item(0, 'COD_MARCA');
		$nom_producto_ingles 		= $this->dws['dw_producto']->get_item(0, 'NOM_PRODUCTO_INGLES');
		$cod_familia_producto 		= $this->dws['dw_producto']->get_item(0, 'COD_FAMILIA_PRODUCTO');
		$largo 						= $this->dws['dw_producto']->get_item(0, 'LARGO');
		$ancho 						= $this->dws['dw_producto']->get_item(0, 'ANCHO');
		$alto 						= $this->dws['dw_producto']->get_item(0, 'ALTO');
		$peso 						= $this->dws['dw_producto']->get_item(0, 'PESO');
		$largo_embalado 			= $this->dws['dw_producto']->get_item(0, 'LARGO_EMBALADO');
		$ancho_embalado 			= $this->dws['dw_producto']->get_item(0, 'ANCHO_EMBALADO');
		$alto_embalado 				= $this->dws['dw_producto']->get_item(0, 'ALTO_EMBALADO');
		$peso_embalado 				= $this->dws['dw_producto']->get_item(0, 'PESO_EMBALADO');
		$factor_venta_interno 		= $this->dws['dw_producto']->get_item(0, 'FACTOR_VENTA_INTERNO');
		$precio_venta_interno 		= $this->dws['dw_producto']->get_item(0, 'PRECIO_VENTA_INTERNO');
		$factor_venta_publico 		= $this->dws['dw_producto']->get_item(0, 'FACTOR_VENTA_PUBLICO');
		$precio_venta_publico 		= $this->dws['dw_producto']->get_item(0, 'PRECIO_VENTA_PUBLICO');
		$usa_electricidad 			= $this->dws['dw_producto']->get_item(0, 'USA_ELECTRICIDAD');
		$nro_fases 					= $this->dws['dw_producto']->get_item(0, 'TRIFASICO');
		$consumo_electricidad 		= $this->dws['dw_producto']->get_item(0, 'CONSUMO_ELECTRICIDAD');
		$rango_temperatura 			= $this->dws['dw_producto']->get_item(0, 'RANGO_TEMPERATURA');
		$voltaje 					= $this->dws['dw_producto']->get_item(0, 'VOLTAJE');
		$nro_certificado_electrico 	= $this->dws['dw_producto']->get_item(0, 'NRO_CERTIFICADO_ELECTRICO');
		$frecuencia 				= $this->dws['dw_producto']->get_item(0, 'FRECUENCIA');
		$usa_gas 					= $this->dws['dw_producto']->get_item(0, 'USA_GAS');
		$potencia 					= $this->dws['dw_producto']->get_item(0, 'POTENCIA');
		$consumo_gas 				= $this->dws['dw_producto']->get_item(0, 'CONSUMO_GAS');
		$nro_certificado_gas 		= $this->dws['dw_producto']->get_item(0, 'NRO_CERTIFICADO_GAS');
		$usa_vapor 					= $this->dws['dw_producto']->get_item(0, 'USA_VAPOR');
		$consumo_vapor 				= $this->dws['dw_producto']->get_item(0, 'CONSUMO_VAPOR');
		$presion_vapor 				= $this->dws['dw_producto']->get_item(0, 'PRESION_VAPOR');
		$usa_agua_fria 				= $this->dws['dw_producto']->get_item(0, 'USA_AGUA_FRIA');
		$usa_agua_caliente 			= $this->dws['dw_producto']->get_item(0, 'USA_AGUA_CALIENTE');
		$caudal 					= $this->dws['dw_producto']->get_item(0, 'CAUDAL');
		$presion_agua 				= $this->dws['dw_producto']->get_item(0, 'PRESION_AGUA');
		$diametro_caneria 			= $this->dws['dw_producto']->get_item(0, 'DIAMETRO_CANERIA');
		$usa_ventilacion 			= $this->dws['dw_producto']->get_item(0, 'USA_VENTILACION');
		$volumen					= $this->dws['dw_producto']->get_item(0, 'VOLUMEN_ESP');
		$caida_presion 				= $this->dws['dw_producto']->get_item(0, 'CAIDA_PRESION');
		$diametro_ducto 			= $this->dws['dw_producto']->get_item(0, 'DIAMETRO_DUCTO');
		$nro_filtros 				= $this->dws['dw_producto']->get_item(0, 'NRO_FILTROS');
		$usa_desague 				= $this->dws['dw_producto']->get_item(0, 'USA_DESAGUE');
		$diametro_desague			= $this->dws['dw_producto']->get_item(0, 'DIAMETRO_DESAGUE');
		$maneja_inventario 			= 'N';
		$stock_critico 				= 0;
		$tiempo_reposicion			= 0;
		$foto_grande 				= $this->dws['dw_producto']->get_item(0, 'FOTO_GRANDE');
		$foto_chica 				= $this->dws['dw_producto']->get_item(0, 'FOTO_CHICA');
		$es_compuesto 				= $this->dws['dw_producto']->get_item(0, 'ES_COMPUESTO');

		$precio_libre 				= $this->dws['dw_producto']->get_item(0, 'PRECIO_LIBRE');
		$es_despachable 			= $this->dws['dw_producto']->get_item(0, 'ES_DESPACHABLE');
		$potencia_kw				= $this->dws['dw_producto']->get_item(0, 'POTENCIA_KW');
		$cod_clasif_inventario		= $this->dws['dw_producto']->get_item(0, 'COD_CLASIF_INVENTARIO');
		$cod_tipo_observacion_comex	= $this->dws['dw_producto']->get_item(0, 'COD_TIPO_OBSERVACION_COMEX');

		$cod_equipo_oc_ex 			= $this->dws['dw_producto']->get_item(0, 'COD_EQUIPO_OC_EX');
		$desc_equipo_oc_ex			= $this->dws['dw_producto']->get_item(0, 'DESC_EQUIPO_OC_EX');
		$precio_venta_int_sug		= $this->dws['dw_producto']->get_item(0, 'SUM_TOTAL_COSTO_BASE_AUX');
		$precio_adicional			= $this->dws['dw_producto']->get_item(0, 'PRECIO_ADICIONAL');

		$nom_producto_ingles 		= ($nom_producto_ingles == '') ? "null" : "'$nom_producto_ingles'";
		$cod_familia_producto 		= ($cod_familia_producto == '') ? "null" : $cod_familia_producto;
		$nro_fases 					= ($nro_fases == '') ? "null" : "'$nro_fases'";
		$consumo_electricidad		= ($consumo_electricidad == '') ? "null" : $consumo_electricidad;
		$rango_temperatura 			= ($rango_temperatura == '') ? "null" : "'$rango_temperatura'";
		$voltaje 					= ($voltaje == '') ? "null" : $voltaje;
		$frecuencia 				= ($frecuencia == '') ? "null" : $frecuencia;
		$nro_certificado_electrico	= ($nro_certificado_electrico == '') ? "null" : "'$nro_certificado_electrico'";
		$potencia 					= ($potencia == '') ? "null" : $potencia;
		$consumo_gas 				= ($consumo_gas == '') ? "null" : $consumo_gas;
		$nro_certificado_gas 		= ($nro_certificado_gas == '') ? "null" : "'$nro_certificado_gas'";
		$consumo_vapor 				= ($consumo_vapor == '') ? "null" : $consumo_vapor;
		$presion_vapor 				= ($presion_vapor == '') ? "null" : $presion_vapor;
		$caudal 					= ($caudal == '') ? "null" : $caudal;
		$presion_agua 				= ($presion_agua == '') ? "null" : $presion_agua;
		$diametro_caneria 			= ($diametro_caneria == '') ? "null" : "'$diametro_caneria'";
		$volumen 					= ($volumen == '') ? "null" : $volumen;
		$caida_presion 				= ($caida_presion == '') ? "null" : $caida_presion;
		$potencia_kw				= ($potencia_kw == '') ? "null" : $potencia_kw;
		$diametro_ducto 			= ($diametro_ducto == '') ? "null" : $diametro_ducto;
		$nro_filtros 				= ($nro_filtros == '') ? "null" : $nro_filtros;
		$diametro_desague 			= ($diametro_desague == '') ? "null" : "'$diametro_desague'";
		$stock_critico 				= ($stock_critico == '') ? "null" : $stock_critico;
		$foto_grande 				= ($foto_grande == '') ? "null" : $foto_grande;
		$foto_chica 				= ($foto_chica == '') ? "null" : $foto_chica;
		$cod_producto				= ($cod_producto == '') ? "null" : $cod_producto;
		$cod_producto_local			= ($cod_producto_local == '') ? "null" : $cod_producto_local;
		$cod_clasif_inventario		= ($cod_clasif_inventario == '') ? "null" : $cod_clasif_inventario;
		$cod_tipo_observacion_comex = ($cod_tipo_observacion_comex == '') ? "null" :$cod_tipo_observacion_comex;

		$cod_equipo_oc_ex			= ($cod_equipo_oc_ex == '') ? "null" : "'$cod_equipo_oc_ex'";
		$desc_equipo_oc_ex			= ($desc_equipo_oc_ex == '') ? "null" : "'$desc_equipo_oc_ex'";
		$precio_venta_int_sug		= ($precio_venta_int_sug == '') ? "null" : str_replace('.', '', "$precio_venta_int_sug");
		$precio_adicional			= ($precio_adicional == '') ? "null" : $precio_adicional;

		//se actualiza el precio del producto si tiene cambios
		$db->query("exec RENTAL.dbo.sp_update_costo_producto 'TODOINOX','$cod_producto',$precio_venta_interno");
		$db->query("exec BIGGI.dbo.sp_update_costo_producto 'TODOINOX','$cod_producto',$precio_venta_interno");

		$tot_precio_sugerido = $precio_venta_int_sug+$precio_adicional;

		if((int)$precio_venta_interno > (int)$tot_precio_sugerido)
			$db->query("exec BODEGA_BIGGI.dbo.sp_update_costo_producto 'TODOINOX','$cod_producto',$precio_venta_interno");
		else
			$db->query("exec BODEGA_BIGGI.dbo.sp_update_costo_producto 'TODOINOX','$cod_producto',$tot_precio_sugerido");

		$sp = 'spu_producto';

		if ($this->is_new_record()){
			$operacion = 'INSERT';
		}
		else{
			$operacion = 'UPDATE';
		}

		/*marca en campo SISTEMA_VALIDO para que sistema es válido el equipo
		 * solo en el insert del equipo se asignará valor, por lo tanto no tiene update
		 */
		$sistema = $this->get_parametro(self::K_PARAM_SISTEMA);
		$prod_comercial = $this->dws['dw_producto']->get_item(0, 'PRODUCTO_COMERCIAL');
		$prod_bodega = $this->dws['dw_producto']->get_item(0, 'PRODUCTO_BODEGA');
		$prod_rental = $this->dws['dw_producto']->get_item(0, 'PRODUCTO_RENTAL');
		$prod_todoinox = $this->dws['dw_producto']->get_item(0, 'PRODUCTO_TODOINOX');

		if ($sistema == 'DEMO')
			$sistema_valido = 'SNNN';
		else
			$sistema_valido = $prod_comercial.$prod_bodega.$prod_rental.$prod_todoinox;


		$param = "'$operacion','$cod_producto','$nom_producto',$cod_tipo_producto,$cod_marca,$nom_producto_ingles,
		$cod_familia_producto,$largo,$ancho,$alto,$peso,$largo_embalado,$ancho_embalado,
		$alto_embalado,$peso_embalado,$factor_venta_interno,$precio_venta_interno,
		$factor_venta_publico,$precio_venta_publico,'$usa_electricidad',$nro_fases,
		$consumo_electricidad,$rango_temperatura,$voltaje,$frecuencia,$nro_certificado_electrico,
		'$usa_gas',$potencia,$consumo_gas,$nro_certificado_gas,'$usa_vapor',$consumo_vapor,
		$presion_vapor,'$usa_agua_fria','$usa_agua_caliente',$caudal,$presion_agua,$diametro_caneria,
		'$usa_ventilacion',$volumen,$caida_presion,$diametro_ducto,$nro_filtros,'$usa_desague',
		$diametro_desague,'$maneja_inventario',$stock_critico,$tiempo_reposicion,'$precio_libre', '$es_despachable', '$sistema_valido',$potencia_kw,$cod_clasif_inventario
		,$cod_tipo_observacion_comex,$cod_equipo_oc_ex,$desc_equipo_oc_ex,$precio_adicional";


		if ($db->EXECUTE_SP($sp, $param)) {
			for ($i = 0; $i < $this->dws['dw_producto_proveedor']->row_count(); $i++){
				$this->dws['dw_producto_proveedor']->set_item($i, 'COD_PRODUCTO', $cod_producto);
			}
			for ($i = 0; $i < $this->dws['dw_producto_compuesto']->row_count(); $i++){
				$this->dws['dw_producto_compuesto']->set_item($i, 'COD_PRODUCTO_PRINCIPAL', $cod_producto);
			}
			for ($i = 0; $i < $this->dws['dw_atributo_producto']->row_count(); $i++){
				$this->dws['dw_atributo_producto']->set_item($i, 'COD_PRODUCTO', $cod_producto);
			}

			// TAB PROVEEDORES //
			/*
			if ($es_compuesto == 'S'){
				// SI ES COMPUESTO SE ELIMINAN LOS PROVEEDORES Y SE ESCONDE EL TAB PROVEEDORES
				for ($i = 0; $i < $this->dws['dw_producto_proveedor']->row_count(); $i++) {
					$cod_producto_proveedor = $this->dws['dw_producto_proveedor']->get_item($i, 'COD_PRODUCTO_PROVEEDOR');
					$sp = 'spu_producto_proveedor';
					$db->EXECUTE_SP($sp, "'DELETE', $cod_producto_proveedor");
				}
				//$this->dws['dw_producto']->set_item(0, "TAB_".self::K_IT_MENU_TAB_PROVEE_VISIBLE,'none');

				if (!$this->dws['dw_producto_compuesto']->update($db))
					return false;
			}
			else{
				// si no es compuesto se eliminan los productos compuestos de este producto
				for ($i = 0; $i < $this->dws['dw_producto_compuesto']->row_count(); $i++) {
					$cod_producto_compuesto = $this->dws['dw_producto_compuesto']->get_item($i, 'COD_PRODUCTO_COMPUESTO');
					$sp = 'spu_producto_compuesto';
					$db->EXECUTE_SP($sp, "'DELETE', $cod_producto_compuesto");
				}
				if (!$this->dws['dw_producto_proveedor']->update($db)){
					return false;
				}
			}
			*/
			if (!$this->dws['dw_producto_proveedor']->update($db))
					return false;

			if (!$this->dws['dw_producto_compuesto']->update($db))
				return false;

			if (!$this->dws['dw_atributo_producto']->update($db))
				return false;

			$sql ="SELECT COD_PRODUCTO_LOCAL
					FROM PRODUCTO_LOCAL
					WHERE COD_PRODUCTO = '$cod_producto'";
			$result = $db->build_results($sql);
			$cod_producto_local = $result[0]['COD_PRODUCTO_LOCAL'];
			$cod_producto_local			= ($cod_producto_local == '') ? "null" : $cod_producto_local;
			$param = "'$operacion',$cod_producto_local,'$cod_producto','$es_compuesto'";


				if(count($result) == 0 && $operacion == 'INSERT'){

					if (!$db->EXECUTE_SP('spu_producto_local', $param)){
							return false;
					}
					if (!$db->EXECUTE_SP('BIGGI_dbo_spu_producto_local', $param)){
							return false;
					}
					if (!$db->EXECUTE_SP('RENTAL_dbo_spu_producto_local', $param)){
							return false;
					}

				}elseif($operacion == 'UPDATE'){
				if (!$db->EXECUTE_SP('spu_producto_local', $param)){
							return false;
					}
				}

			if (!$this->subir_imagen($db, $cod_producto))
				return false;

			$param = "'PRODUCTO_BUSQUEDA','$cod_producto'";

			if (!$db->EXECUTE_SP('spu_producto', $param))
				return false;

			return true;
		}
		return false;
	}
}

?>
