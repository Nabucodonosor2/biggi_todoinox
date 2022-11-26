<?php
require_once(dirname(__FILE__)."/../../ws_client_biggi/class_client_biggi.php");
require_once(dirname(__FILE__)."/../../common_appl/class_header_vendedor.php");
////////////////////////////////////////
/////////// TODOINOX ///////////////
////////////////////////////////////////
class drop_down_pago extends header_drop_down {
	function drop_down_pago(){
		$sql="SELECT F.COD_FORMA_PAGO
				    ,NOM_FORMA_PAGO
			  FROM FORMA_PAGO F
			  WHERE ES_VIGENTE = 'S'
			  ORDER BY ORDEN";
		parent::header_drop_down('NOM_FORMA_PAGO', 'F.COD_FORMA_PAGO', 'Forma Pago', $sql);
	}
}


class wo_factura extends wo_factura_base {
	const K_EMPRESA_BODEGA_BIGGI = 9;
	const K_EMPRESA_COMERCIAL_BIGGI = 37;
	
	function wo_factura() {
		parent::wo_factura_base();
		// se elimina F.COD_TIPO_FACTURA = ".self::K_TIPO_VENTA."
		// parab que traiga todas las FA
		$sql = "select F.COD_FACTURA
						,convert(varchar(20), F.FECHA_FACTURA, 103) FECHA_FACTURA
						,F.NRO_FACTURA
						,F.RUT
						,F.DIG_VERIF
						,F.NOM_EMPRESA
						,dbo.f_fa_NV_COMERCIAL(F.COD_FACTURA) COD_DOC
						,EDS.NOM_ESTADO_DOC_SII
						,F.TOTAL_CON_IVA
						,U.INI_USUARIO
						,F.NRO_ORDEN_COMPRA
						,F.COD_USUARIO_VENDEDOR1
						,EDS.COD_ESTADO_DOC_SII
						,F.COD_FORMA_PAGO
						,FP.NOM_FORMA_PAGO
						,ISNULL(CONVERT(VARCHAR, F.COD_COTIZACION), '--') COD_COTIZACION
				 from	FACTURA F, ESTADO_DOC_SII EDS, USUARIO U, FORMA_PAGO FP
				where	F.COD_ESTADO_DOC_SII = EDS.COD_ESTADO_DOC_SII AND
						F.COD_USUARIO_VENDEDOR1 = U.COD_USUARIO AND
						F.COD_FORMA_PAGO = FP.COD_FORMA_PAGO
				order by	isnull(NRO_FACTURA, 9999999999) desc, COD_FACTURA desc";
		
		$this->dw->set_sql($sql);		
		$this->sql_original = $sql;
		//$this->add_header(new header_text('COD_DOC', 'dbo.f_fa_NV_COMERCIAL(F.COD_FACTURA)', 'N° NV'));
		$this->add_header(new header_text('NRO_ORDEN_COMPRA', 'F.NRO_ORDEN_COMPRA', 'N° OC'));
		$this->add_header(new drop_down_pago());
		$this->add_header(new header_vendedor('INI_USUARIO', 'F.COD_USUARIO_VENDEDOR1', 'V1'));
		$this->add_header(new header_num('COD_COTIZACION', 'COD_COTIZACION', 'N° Cot.'));
		$this->row_per_page = 15;
	}
	
	function crear_desde_oc($cod_orden_compra, $sistema){
		$bdName = '';
		if($sistema == 'BODEGA'){
			if($cod_orden_compra <= 55676){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$cod_orden_compra.', debe ser facturada por el metodo tradicional.');								
				return;
			}
			session::set('WS_ORIGEN', 'BODEGA');
			$bdName = 'BODEGA_BIGGI';

		}else if($sistema == 'COMERCIAL'){
			if($cod_orden_compra <= 175780){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$cod_orden_compra.', debe ser facturada por el metodo tradicional.');								
				return;
			}
			session::set('WS_ORIGEN', 'COMERCIAL');
			$bdName = 'BIGGI';

		}else{ // RENTAL
			if($cod_orden_compra <= 65555){
				$this->_redraw();
				$this->alert('La Orden Compra N° '.$cod_orden_compra.', debe ser facturada por el metodo tradicional.');								
				return;
			}
			session::set('WS_ORIGEN', 'RENTAL');
			$bdName = 'RENTAL';
		}

		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		///////////////////////////// AQUI SE EJECUTA LO QUE HACIA CON WEBSERVICE ///////////////////////////
		$sql_ws = "SELECT COD_ORDEN_COMPRA
						,OC.REFERENCIA
						,CONVERT(VARCHAR,FECHA_ORDEN_COMPRA,103) FECHA_ORDEN_COMPRA
						,OC.SUBTOTAL
						,OC.PORC_DSCTO1
						,OC.MONTO_DSCTO1
						,OC.PORC_DSCTO2
						,OC.MONTO_DSCTO2
						,OC.TOTAL_NETO
						,OC.TOTAL_CON_IVA
						,OC.MONTO_IVA
						,OC.COD_NOTA_VENTA
						,RUT
						,(SELECT NOM_EMPRESA FROM $bdName.dbo.EMPRESA WHERE COD_EMPRESA = NV.COD_EMPRESA) NV_NOM_EMPRESA
						,(SELECT NOM_EMPRESA FROM $bdName.dbo.EMPRESA WHERE COD_EMPRESA = A.COD_EMPRESA) A_NOM_EMPRESA
						,(SELECT NOM_USUARIO FROM $bdName.dbo.USUARIO WHERE COD_USUARIO = OC.COD_USUARIO_SOLICITA) OC_NOM_USUARIO
						,(SELECT NOM_MONEDA FROM $bdName.dbo.MONEDA WHERE COD_MONEDA = OC.COD_MONEDA) OC_NOM_MONEDA
						,(SELECT NOM_ESTADO_ORDEN_COMPRA 
						FROM $bdName.dbo.ESTADO_ORDEN_COMPRA 
						WHERE COD_ESTADO_ORDEN_COMPRA = OC.COD_ESTADO_ORDEN_COMPRA) ESTADO_OC
						,OC.TIPO_ORDEN_COMPRA
						,OC.COD_DOC
						,E.COD_FORMA_PAGO_CLIENTE
						,OC.OBS
						,COD_ESTADO_ORDEN_COMPRA ";

		if($sistema == 'BODEGA'){	
			$sql_ws .= ",OC.RESPETA_PRECIO ";
		}

		$sql_ws .=	"FROM $bdName.dbo.ORDEN_COMPRA OC LEFT OUTER JOIN $bdName.dbo.NOTA_VENTA NV ON OC.COD_NOTA_VENTA = NV.COD_NOTA_VENTA
										LEFT OUTER JOIN $bdName.dbo.ARRIENDO A ON OC.COD_DOC = A.COD_ARRIENDO
						,$bdName.dbo.EMPRESA E
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra
					AND OC.COD_EMPRESA = E.COD_EMPRESA";
		
		$result_ws = $db->build_results($sql_ws);
		$result['ORDEN_COMPRA'] = $result_ws;		

		// items OC
		$sql_ws = "SELECT COD_ITEM_ORDEN_COMPRA
						,ORDEN
						,ITEM
						,COD_PRODUCTO
						,NOM_PRODUCTO
						,CANTIDAD
						,PRECIO
				FROM $bdName.dbo.ITEM_ORDEN_COMPRA 
				WHERE COD_ORDEN_COMPRA = $cod_orden_compra
				AND FACTURADO_SIN_WS = 'N'";

		$result_ws = $db->build_results($sql_ws);
		$result['ITEM_ORDEN_COMPRA'] = $result_ws;
		///////////////////////////// AQUI SE EJECUTA LO QUE HACIA CON WEBSERVICE ///////////////////////////

		$index = '';
		//Valida que exista un registro en la OC ingresada
		if(count($result['ORDEN_COMPRA']) != 0){
			//Valida que la OC no este en estado anulada
			if($result['ORDEN_COMPRA'][0]['COD_ESTADO_ORDEN_COMPRA'] == 2){
				$this->_redraw();
				$this->alert('La Orden de Compra N° '.$cod_orden_compra.', del Sistema '.$sistema.' está anulada.');								
				return;
			}
			//Valida que la OC sea para TODOINOX
			if($result['ORDEN_COMPRA'][0]['RUT'] == 89257000){ //COMERCIAL TODOINOX LTDA.
				if($sistema == 'RENTAL' && $result['ORDEN_COMPRA'][0]['COD_ESTADO_ORDEN_COMPRA'] != 4){ //Confirmada
					$this->_redraw();
					$this->alert('La Orden de Compra N° '.$cod_orden_compra.', del Sistema Web Rental, NO ESTA en estado autorizada.\nEl responsable de la OC en Sistema Web Rental debe solicitar autorizacion de la OC a Administracion BIGGI.\n\nNo se puede facturar la OC N° '.$cod_orden_compra.'.');								
					return;
				}
				/*
				Cuando se crean facturas desde OC de Rental, inexplicablemente habían OC
				en la bd oficial de rental con tipo_orden_compra = NOTA_VENTA pero el COD_DOC era NULL.
				No se sabe aun por que ocurre esto, pero por mientras se valida con un mensaje
				y se deriva al usuario que llama a integrasystem.
 				*/
				/*if($sistema == 'RENTAL'){
					if($result['ORDEN_COMPRA'][0]['TIPO_ORDEN_COMPRA'] != 'ARRIENDO'){
						if($result['ORDEN_COMPRA'][0]['COD_NOTA_VENTA'] == ''){
							$this->_redraw();
							$this->alert('Error al crear la factura, la OC '.$cod_orden_compra.' no proviene desde una Nota de Venta o Arriendo.\nContactese con Integrasystem indicando este mensaje.');								
							return;
						}
					}
				}*/	//IC 22/10/2014:Se comenta esta validación, ya que no pueden facturar una orden de compra con ese caso especial
				//////////////////////////////////		
				for ($i=0; $i < count($result['ITEM_ORDEN_COMPRA']); $i++) {
					
					$cod_item_oc = $result['ITEM_ORDEN_COMPRA'][$i]['COD_ITEM_ORDEN_COMPRA'];
					$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);			
					
					$sql = "SELECT SUM(CANTIDAD) CANTIDAD
							FROM ITEM_FACTURA
							WHERE COD_ITEM_DOC = $cod_item_oc";
					
					if($sistema == 'BODEGA')
						$sql .= " AND TIPO_DOC = 'ITEM_ORDEN_COMPRA_BODEGA'";
					else if($sistema == 'COMERCIAL')
						$sql .= " AND TIPO_DOC = 'ITEM_ORDEN_COMPRA_COMERCIAL'";
					else //RENTAL
						$sql .= " AND TIPO_DOC = 'ITEM_ORDEN_COMPRA_RENTAL'";				
					
					$result_cant = $db->build_results($sql);
					$cantidad = $result['ITEM_ORDEN_COMPRA'][$i]['CANTIDAD'] - $result_cant[0]['CANTIDAD'];
					
					//Se concatena los indices del arreglo con cantidades = 0
					if($cantidad == 0)
						$index = $index.$i.'|';
							
				}
				
				$index = explode('|', trim($index,'|'));
				for($j= 0 ; $j < count($index) ; $j++)
					unset($result['ITEM_ORDEN_COMPRA'][$index[$j]]);
				
				$result['ITEM_ORDEN_COMPRA'] = array_values($result['ITEM_ORDEN_COMPRA']);
				
				$count_item = count($result['ITEM_ORDEN_COMPRA']);
				//Valida que los item de la OC esten totalmente facturadas
				if($count_item == 0){
					$this->_redraw();
					$message = 'La Orden de compra N '.$cod_orden_compra.' del Sistema '.$sistema.' está 100% facturada \n\n Facturas asociadas: ';
					
					$sql = "SELECT DISTINCT F.COD_FACTURA
										   ,F.NRO_FACTURA 
							FROM ITEM_FACTURA ITF
								,FACTURA F
							WHERE COD_ITEM_DOC = $cod_item_oc 
							AND F.COD_FACTURA = ITF.COD_FACTURA";
					
					if($sistema == 'BODEGA')
						$sql .= " AND ITF.TIPO_DOC = 'ITEM_ORDEN_COMPRA_BODEGA'";
					else if($sistema == 'COMERCIAL')
						$sql .= " AND ITF.TIPO_DOC = 'ITEM_ORDEN_COMPRA_COMERCIAL'";
					else //RENTAL
						$sql .= " AND ITF.TIPO_DOC = 'ITEM_ORDEN_COMPRA_RENTAL'";
					
					$result_m = $db->build_results($sql);
					
					for($i=0 ; $i < count($result_m) ; $i++)
						$message .= 'FA N° '.$result_m[$i]['NRO_FACTURA'].', ';

					$message = 	trim($message,', ');
						
					$this->alert($message);								
					return;
				}
				
				if($sistema == 'BODEGA')
					$result['ORDEN_COMPRA'][0]['RUT'] = 80112900; // RUT DE BODEGA BIGGI
				else
					$result['ORDEN_COMPRA'][0]['RUT'] = 91462001; // RUT DE COMERCIAL	
				/*
				$cant_max_fact = $this->get_parametro(29);//Máxima cantidad items en Factura 
				if($count_item > $cant_max_fact){
					$this->_redraw();
					$this->alert('Se está ingresando más item que la cantidad permitida, favor contacte a IntegraSystem.');								
					return;
				}*/
				if($result['ORDEN_COMPRA'][0]['RESPETA_PRECIO'] == 'S' && $sistema == 'BODEGA'){
					session::set('RESPETAR_PRECIO', 'S');
				}	
					
				session::set('FACTURA_DESDE_OC', $result);
				$this->add();
			}else{
				$this->_redraw();
				$this->alert('La Orden de compra N° '.$cod_orden_compra.' del Sistema '.$sistema.' no es para COMERCIAL TODOINOX LTDA.');								
				return;
			}	
		}else{
			$this->_redraw();
			$this->alert('La Orden de compra N° '.$cod_orden_compra.' no existe en el Sistema '.$sistema);								
			return;
		}	
	}
	
	function crear_fa_from_cot($cod_cotizacion) {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT dbo.f_get_tiene_acceso (".$this->cod_usuario.", 'COTIZACION',C.COD_USUARIO_VENDEDOR1, C.COD_USUARIO_VENDEDOR2) TIENE_ACCESO
				FROM COTIZACION C 
				WHERE C.COD_COTIZACION = $cod_cotizacion";
		$result = $db->build_results($sql);
		if (count($result) == 0){
			$this->_redraw();
			$this->alert('La Cotización Nº '.$cod_cotizacion.' no existe.');								
			return;
		}
		else if ($result[0]['TIENE_ACCESO']==0){
			$this->_redraw();
			$this->alert('Ud. no tiene acceso a a Cotización Nº '.$cod_cotizacion);								
			return;
		}

		session::set('FA_CREADA_DESDE', $cod_cotizacion);
		$this->add();
	}
	function redraw(&$temp){
		parent::redraw($temp);
  		if ($this->b_add_visible){
			$this->habilita_boton($temp, 'create', $this->get_privilegio_opcion_usuario($this->cod_item_menu, $this->cod_usuario)=='E');
  		}	
  		$this->habilita_boton($temp, 'crear_desde', $this->get_privilegio_opcion_usuario('992055', $this->cod_usuario)=='E');
	}
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='create') {
			if ($habilita){
				$ruta_over = "'../../../../commonlib/trunk/images/b_create_over.jpg'";
				$ruta_out = "'../../../../commonlib/trunk/images/b_create.jpg'";
				$ruta_click = "'../../../../commonlib/trunk/images/b_create_click.jpg'";
				$temp->setVar("WO_CREATE", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
								'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../../../commonlib/trunk/images/b_'.$boton.'.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
								'onClick="request_factura(\'Ingrese Nº de la Nota de Venta\',\'\');" />');
			}else
				$temp->setVar("WO_".strtoupper($boton), '<img src="../../../../commonlib/trunk/images/b_create_d.jpg"/>');
		}else if($boton=='crear_desde'){
			if ($habilita){
				$ruta_over = "'../../images_appl/b_crear_desde_over.jpg'";
				$ruta_out = "'../../images_appl/b_crear_desde.jpg'";
				$ruta_click = "'../../images_appl/b_crear_desde_click.jpg'";
				$temp->setVar("WO_CREATE_FROM", '<input name="b_'.$boton.'" id="b_'.$boton.'" type="button" onmouseover="entrada(this, '.$ruta_over.')" onmouseout="salida(this, '.$ruta_out.')" onmousedown="down(this, '.$ruta_click.')"'.
								'style="cursor:pointer;height:68px;width:66px;border: 0;background-image:url(../../images_appl/b_crear_desde.jpg);background-repeat:no-repeat;background-position:center;border-radius: 15px;"'.
								'onClick="dlg_crear_desde(\'Ingrese Nº de la OC\',\'\');" />');
			}else
				$temp->setVar("WO_CREATE_FROM", '<img src="../../images_appl/b_crear_desde_d.jpg"/>');	
		}else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	function detalle_record_desde($modificar, $cant_fa_a_hacer) 
	{
		// No se llama al ancestro porque se reimplementa toda la rutina
		session::set("cant_fa_a_hacer", $cant_fa_a_hacer);

		// retrieve
		$this->set_count_output();
		$this->last_page = Ceil($this->row_count_output / $this->row_per_page);
		$this->set_current_page(0);
		$this->save_SESSION();

		$pag_a_mostrar=$cant_fa_a_hacer -1;

		$this->detalle_record($pag_a_mostrar);	// Se va al primer registro
	}
  	function crear_fa_from($valor_devuelto){
  		
  		$pos = strpos($valor_devuelto, '-');
	  	if ($pos!==false)
	  	{
	  		list($cod_nota_venta,$codigosGD )=split('[-]', $valor_devuelto);
			$opcion=substr($codigosGD, 0,9);
	  	}
		else
	  		list($opcion, $cod_nota_venta)=split('[|]', $valor_devuelto);

	  	$cantidad_max = $this->get_parametro(self::K_PARAM_MAX_IT_FA);
		if ($opcion=='desde_nv') {
				//crear la FA para todos los itemsNV que tengan pendiente por facturar
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				///valida que la NV exista
				$sql = "select * from NOTA_VENTA where COD_NOTA_VENTA = $cod_nota_venta";
				$result = $db->build_results($sql);
				if (count($result) == 0) {
					$this->_redraw();
					$this->alert('La Nota de Venta Nº '.$cod_nota_venta.' no existe.');								
					return;
				}

				//valida que la NV este confirmada
				$sql = "select * from NOTA_VENTA 
						where COD_NOTA_VENTA = ".$cod_nota_venta." 
						and	COD_ESTADO_NOTA_VENTA IN (".self::K_ESTADO_CONFIRMADA.", ".self::K_ESTADO_CERRADA.")";
				$result = $db->build_results($sql);
				if (count($result) == 0) {
					$this->_redraw();
					$this->alert('La Nota de Venta Nº '.$cod_nota_venta.' no esta confirmada.');								
					return;
				}

				/* valida que la NV no tenga FAs anteriores en estado = emitida
				ya que es suceptible a errores tener varias GD en estado emitida, ya que la cantidad por despachar 
				siempre será la misma cantidad de la NV.
				*/
				$sql = "select * from FACTURA
							where COD_DOC = $cod_nota_venta and
							COD_ESTADO_DOC_SII = ".self::K_ESTADO_SII_EMITIDA;
				$result = $db->build_results($sql);
				if (count($result) != 0) {
					$this->_redraw();
					$this->alert('La Nota de Venta Nº '.$cod_nota_venta.' tiene Factura(s) pendientes(s) en estado emitido. Para poder generar más Facturas deberá imprimir los documentos emitidos.');						
					return;
				}

				//****************
				// valida que este pendiente de facturar
				$sql = "select dbo.f_nv_porc_facturado($cod_nota_venta ) PORC_FACTURA";
				$result = $db->build_results($sql);
				$porc_factura = $result[0]['PORC_FACTURA'];
				if ($porc_factura >= 100) { 
					$this->_redraw();
					$this->alert('La Nota de Venta Nº '.$cod_nota_venta.' está totalmente Facturada.');								
					return;
				}
					
				if ($opcion=='desde_nv') {
					//cuenta cuantos items hay
					$sql_cuenta="select count(*) cantidad
									from ITEM_NOTA_VENTA IT, NOTA_VENTA NV
									where NV.COD_NOTA_VENTA = $cod_nota_venta and 
									NV.COD_NOTA_VENTA = IT.COD_NOTA_VENTA";
					$result_cuenta = $db->build_results($sql_cuenta);
					$cantidad = $result_cuenta[0]['cantidad'];
					$cant_fa_a_hacer=ceil($cantidad/$cantidad_max);
						
					$cod_usuario = $this->cod_usuario;	
								
					$sp = 'sp_fa_crear_desde_nv';
					$param = "$cod_nota_venta, $cod_usuario";
				}
				else if ($opcion=='desde_nv_anticipo') {
					$cant_fa_a_hacer = 1;
					$cod_usuario = $this->cod_usuario;	
								
					$sp = 'sp_fa_crear_desde_nv_anticipo';
					$param = "$cod_nota_venta, $cod_usuario";
				}
					
				
					
				$db->BEGIN_TRANSACTION();
				if ($db->EXECUTE_SP($sp, $param)) { 
					$db->COMMIT_TRANSACTION();
					$this->detalle_record_desde(true,$cant_fa_a_hacer);
				}
				else { 
					$db->ROLLBACK_TRANSACTION();
					$this->_redraw();
					$this->alert("No se pudo crear la factura. Error en 'sp_fa_crear_desde_nv', favor contacte a IntegraSystem.");
				}	
		}
		elseif($opcion=='desde_cot')  {	
			
				$cod_cotizacion = $cod_nota_venta;
				$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
				$sql = "SELECT dbo.f_get_tiene_acceso (".$this->cod_usuario.", 'COTIZACION',C.COD_USUARIO_VENDEDOR1, C.COD_USUARIO_VENDEDOR2) TIENE_ACCESO
						FROM COTIZACION C 
						WHERE C.COD_COTIZACION = $cod_cotizacion";
				$result = $db->build_results($sql);
				if (count($result) == 0){
					$this->_redraw();
					$this->alert('La Cotización Nº '.$cod_cotizacion.' no existe.');								
					return;
				}
				else if ($result[0]['TIENE_ACCESO']==0){
					$this->_redraw();
					$this->alert('Ud. no tiene acceso a a Cotización Nº '.$cod_cotizacion);								
					return;
				}
				///////Dias Validez Oferta/////////
				$sql_parametro="SELECT VALOR 
								FROM PARAMETRO
								WHERE COD_PARAMETRO = 7";
				
				$result_parametro = $db->build_results($sql_parametro);
				
				$sql="SELECT ".$result_parametro[0]['VALOR']." + DATEDIFF(DAY, GETDATE(), FECHA_COTIZACION) VALIDEZ
					  FROM COTIZACION
					  WHERE COD_COTIZACION = $cod_cotizacion";
				
				$result = $db->build_results($sql);
				
				if($result[0]['VALIDEZ'] >= 0){
					session::set('FA_CREADA_DESDE', $cod_cotizacion);
					$this->add();
				}else{
					session::set('FA_CREADA_DESDE', $cod_cotizacion);
					session::set('PRECIO_PRODUCTO_ORIGINAL', 'S');
					$this->add();
				}
					
		}elseif($opcion == 'desde_gd'){
			
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
			$codigos=substr($codigosGD, 10); //solo los codigos, sin valor de la opcion (venía primero en la variable)
			$codigos_gd=str_replace('|', ' ', $codigos);
			$codigos_gd2=str_replace('|', ',', $codigos);
			
			$largo=strlen($codigos_gd);
			
			$codigos_gd=substr($codigos_gd, 0,$largo -1);
			$codigos_gd2=substr($codigos_gd2, 0,$largo -1);
			
			// valida que hayan item pendiente por facturar.
				$sql_por_facturar = "select sum(dbo.f_nv_cant_por_facturar(IT.COD_ITEM_DOC, 'TODO_ESTADO')) POR_FACTURAR
				from ITEM_GUIA_DESPACHO IT, GUIA_DESPACHO GD
				where GD.COD_GUIA_DESPACHO = IT.COD_GUIA_DESPACHO
						AND GD.COD_DOC = $cod_nota_venta 
						AND GD.COD_GUIA_DESPACHO in ($codigos_gd2)
						AND IT.CANTIDAD > 0 
						AND IT.PRECIO > 0";
						
				$result = $db->build_results($sql_por_facturar);
				$por_facturarGd = $result[0]['POR_FACTURAR'];
				if ($por_facturarGd <= 0)
				{
						$this->_redraw();
						$this->alert('La Guía de despacho está totalmente Facturada.');								
						return;
				}
	
			//ver cuantos items tendré en todos esos GD
			$sql_items="select count(distinct(cod_item_doc)) cantidad from item_guia_despacho where COD_GUIA_DESPACHO in (".$codigos_gd2.")";
			$result_items = $db->build_results($sql_items);
			$cantidad= $result_items[0]['cantidad'];

			$cant_fa_a_hacer=ceil($cantidad/$cantidad_max);
			$db->BEGIN_TRANSACTION();
			$cod_usuario = $this->cod_usuario;	
			$codigos_gd="'".$codigos_gd."'";		
			$sp = 'sp_fa_crear_desde_gds';
			$param = "$cod_nota_venta, $codigos_gd, $cod_usuario";
			 	
			if ($db->EXECUTE_SP($sp, $param)) { 
				$db->COMMIT_TRANSACTION();
				$this->detalle_record_desde(true,$cant_fa_a_hacer);
			}
			else { 
				$db->ROLLBACK_TRANSACTION();
				$this->_redraw();
				$this->alert("No se pudo crear la factura. Error en 'sp_fa_crear_desde_gds', favor contacte a IntegraSystem.");
			}
						
		}
  	elseif($opcion == 'desde_comercial'){
		 $cod_orden_compra_comercial =$cod_nota_venta;
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COD_ORDEN_COMPRA FROM OC_COMERCIAL WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial";
		$result = $db->build_results($sql);
		if (count($result) == 0){
			$this->_redraw();
			$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no existe en Comercial.');								
			return;
		}else{
			//si existe la Oc determina el tipo
			$sql = "SELECT COD_ORDEN_COMPRA
							,COD_EMPRESA 
					FROM OC_COMERCIAL 
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial
						and TIPO_ORDEN_COMPRA IN ('NOTA_VENTA', 'BACKCHARGE')";

			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no es de tipo VENTA ó BACKCHARGE');								
				return;
			}
			else if ($result[0]['COD_EMPRESA'] != self::K_EMPRESA_COMERCIAL_BIGGI){
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no es para Comercial Biggi');								
				return;
			}

			//Verica si tiene items pendientes de facturar
			$sql = "SELECT isnull(sum(dbo.f_fa_OC_Comercial_por_facturar(COD_ITEM_ORDEN_COMPRA)), 0) CANT 
					FROM ITEM_OC_COMERCIAL
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial";
			$result = $db->build_results($sql);
			if ($result[0]['CANT']==0) {
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' ya esta facturada');								
				return;
			}
			else {			
				session::set('FACTURA_DESDE_OC_COMERCIAL', $cod_orden_compra_comercial);
				$this->add();
	   		}
  		
		}
  	
  	 }
  	elseif($opcion == 'desde_bodega'){
		 $cod_orden_compra_comercial =$cod_nota_venta;
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COD_ORDEN_COMPRA FROM OC_BODEGA WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial";
		$result = $db->build_results($sql);
		if (count($result) == 0){
			$this->_redraw();
			$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no existe en Comercial.');								
			return;
		}else{
			//si existe la Oc determina el tipo
			$sql = "SELECT COD_ORDEN_COMPRA
							,COD_EMPRESA 
					FROM OC_BODEGA 
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial
						and TIPO_ORDEN_COMPRA IN ('NOTA_VENTA', 'BACKCHARGE')";

			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no es de tipo VENTA ó BACKCHARGE');								
				return;
			}
			else if ($result[0]['COD_EMPRESA'] != self::K_EMPRESA_BODEGA_BIGGI){
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no es para bodega Biggi');								
				return;
			}

			//Verica si tiene items pendientes de facturar
			$sql = "SELECT isnull(sum(dbo.f_fa_OC_Comercial_por_facturar(COD_ITEM_ORDEN_COMPRA)), 0) CANT 
					FROM ITEM_OC_BODEGA
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial";
			$result = $db->build_results($sql);
			if ($result[0]['CANT']==0) {
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' ya esta facturada');								
				return;
			}
			else {			
				session::set('FACTURA_DESDE_OC_BODEGA', $cod_orden_compra_comercial);
				$this->add();
	   		}
  		
		}
  	
  	 }
  	elseif($opcion == 'desde_servindus'){
		 $cod_orden_compra_comercial =$cod_nota_venta;
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "SELECT COD_ORDEN_COMPRA FROM OC_BODEGA WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial";
		$result = $db->build_results($sql);
		if (count($result) == 0){
			$this->_redraw();
			$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no existe en Comercial.');								
			return;
		}else{
			//si existe la Oc determina el tipo
			$sql = "SELECT COD_ORDEN_COMPRA
							,COD_EMPRESA 
					FROM OC_SERVINDUS 
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial
						and TIPO_ORDEN_COMPRA IN ('NOTA_VENTA', 'BACKCHARGE')";

			$result = $db->build_results($sql);
			if (count($result) == 0){
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no es de tipo VENTA ó BACKCHARGE');								
				return;
			}
			else if ($result[0]['COD_EMPRESA'] != self::K_EMPRESA_BODEGA_BIGGI){
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' no es para bodega Biggi');								
				return;
			}

			//Verica si tiene items pendientes de facturar
			$sql = "SELECT isnull(sum(dbo.f_fa_OC_Comercial_por_facturar(COD_ITEM_ORDEN_COMPRA)), 0) CANT 
					FROM ITEM_OC_SERVINDUS
					WHERE COD_ORDEN_COMPRA = $cod_orden_compra_comercial";
			$result = $db->build_results($sql);
			if ($result[0]['CANT']==0) {
				$this->_redraw();
				$this->alert('La OC Nº'.$cod_orden_compra_comercial.' ya esta facturada');								
				return;
			}
			else {			
				session::set('FACTURA_DESDE_OC_BODEGA', $cod_orden_compra_comercial);
				$this->add();
	   		}
  		
		}
  	
  	 }
  	}	
	function procesa_event() {		
		if(isset($_POST['b_create_x'])){
			$this->crear_fa_from($_POST['wo_hidden']);
		}else if(isset($_POST['b_crear_desde_x'])){
			$values = explode("|", $_POST['wo_hidden']);
			if($values[2] == 'etiqueta'){
				if($values[1] == '80112900X') //bodega
					$this->crear_desde_oc($values[0], 'BODEGA');
				else if($values[1] == '91462001X') //comercial
					$this->crear_desde_oc($values[0], 'COMERCIAL');
				else if($values[1] == '91462001R') //rental
					$this->crear_desde_oc($values[0], 'RENTAL');				
			}else{
				if($values[1] == 'bodega')
					$this->crear_desde_oc($values[0], 'BODEGA');
				if($values[1] == 'comercial')
					$this->crear_desde_oc($values[0], 'COMERCIAL');
				if($values[1] == 'rental')
					$this->crear_desde_oc($values[0], 'RENTAL');	
			}
			
		}else{
			parent::procesa_event();
		}
	}
 }
?>