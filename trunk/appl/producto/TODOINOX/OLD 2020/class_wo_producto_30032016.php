<?php

class wo_producto extends wo_producto_base{
	const K_BODEGA_TERMINADO = 1;
	const K_MENU_PRODUCTO = '995005';
	
	function wo_producto(){
		$cod_usuario = session::get("COD_USUARIO");
		// Es igual al BASE, solo cambia elk sql donde se agrega stock
		$sql = "select	P.COD_PRODUCTO
						,NOM_PRODUCTO
						,PRECIO_VENTA_PUBLICO
						,NOM_TIPO_PRODUCTO
						,case 
		                    when (dbo.f_get_autoriza_menu($cod_usuario,".self::K_MENU_PRODUCTO.") = 'E') then dbo.number_format(dbo.f_bodega_stock(P.COD_PRODUCTO, ".self::K_BODEGA_TERMINADO.", GETDATE()),0,',','.')
		                    when (dbo.f_get_autoriza_menu($cod_usuario, ".self::K_MENU_PRODUCTO.") = 'N') and (dbo.f_bodega_stock(P.COD_PRODUCTO, ".self::K_BODEGA_TERMINADO.", GETDATE()) > 0)  then 'HAY'
		                    else 'NO HAY'
		                end STOCK
						,M.NOM_MARCA
						,P.COD_MARCA
						,TP.COD_TIPO_PRODUCTO
						,P.COD_TIPO_OBSERVACION_COMEX
						,TOC.NOM_TIPO_OBSERVACION_COMEX
			from 		PRODUCTO P
						,TIPO_PRODUCTO TP
						,MARCA M
						,TIPO_OBSERVACION_COMEX TOC
			where		P.COD_TIPO_PRODUCTO = TP.COD_TIPO_PRODUCTO
						AND dbo.f_prod_valido (COD_PRODUCTO) = 'S'
						AND P.COD_MARCA = M.COD_MARCA
						AND P.COD_TIPO_OBSERVACION_COMEX = TOC.COD_TIPO_OBSERVACION_COMEX
			order by 	COD_PRODUCTO";
			
		parent::w_output_biggi('producto', $sql, $_REQUEST['cod_item_menu']);

		// headers
		$this->add_header(new header_modelo('COD_PRODUCTO', 'P.COD_PRODUCTO', 'Modelo'));
		$this->add_header(new header_text('NOM_PRODUCTO', 'NOM_PRODUCTO', 'Descripción'));
		$this->dw->add_control(new edit_precio('PRECIO_VENTA_PUBLICO'));
		$this->add_header(new header_num('PRECIO_VENTA_PUBLICO', 'PRECIO_VENTA_PUBLICO', 'Precio'));
		
		$sql = "select COD_MARCA ,NOM_MARCA from MARCA order by	NOM_MARCA";		
		$this->add_header(new header_drop_down('NOM_MARCA', 'P.COD_MARCA', 'Marca', $sql));
		
		$sql = "select COD_TIPO_OBSERVACION_COMEX ,NOM_TIPO_OBSERVACION_COMEX from TIPO_OBSERVACION_COMEX order by ORDEN";		
		$this->add_header(new header_drop_down('NOM_TIPO_OBSERVACION_COMEX', 'P.COD_TIPO_OBSERVACION_COMEX', 'Obs. Comex', $sql));
		
		$sql_tipo_producto = "select COD_TIPO_PRODUCTO ,NOM_TIPO_PRODUCTO from TIPO_PRODUCTO order by	ORDEN";
		$this->add_header($header = new header_drop_down('NOM_TIPO_PRODUCTO', 'TP.COD_TIPO_PRODUCTO', 'Tipo Producto', $sql_tipo_producto));
		$this->add_header($control = new header_num('STOCK', 'dbo.f_bodega_stock(P.COD_PRODUCTO, 1, GETDATE())', 'Stock'));
		$control->field_bd_order = 'STOCK';
		// formatos de columnas
		$this->dw->add_control(new static_num('PRECIO_VENTA_PUBLICO'));
	}
	
	function habilita_boton(&$temp, $boton, $habilita) {
		if ($boton=='print' && $habilita)
			$temp->setVar("WO_PRINT", '<input name="b_'.$boton.'" id="b_'.$boton.'" src="../../../../commonlib/trunk/images/b_'.$boton.'.jpg" type="image" '.
											'onMouseDown="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib/trunk/images/b_'.$boton.'_click.jpg\',1)" '.
											'onMouseUp="MM_swapImgRestore()" onMouseOut="MM_swapImgRestore()" '.
											'onMouseOver="MM_swapImage(\'b_'.$boton.'\',\'\',\'../../../../commonlib/trunk/images/b_'.$boton.'_over.jpg\',1)" '.
											'onClick="return request_crear_desde();" />');
		else
			parent::habilita_boton($temp, $boton, $habilita);
	}
	function redraw(&$temp) {
		parent::redraw($temp);
		if($this->priv_impresion == 'S')
			$this->habilita_boton($temp, 'print', true);		
		else
			$this->habilita_boton($temp, 'print', false);	
	}
	function print_producto(){
		$this->make_filtros();
		$sql = $this->dw->get_sql();
		$fecha = $this->current_date();
		$time = $this->current_time();
		$labels = array();
		$labels['strFECHA'] = $fecha;
		$labels['strTIME'] = $time;
		$file_name = $this->root_dir.'appl/producto/TODOINOX/producto.xml';
		$rpt = new reporte($sql, $file_name, $labels, "Productos".".pdf", 0, false, 'L');
		$this->_redraw();
	}
	
	function procesa_event() {		
		if(isset($_POST['b_print_x']))
			$this->print_producto();
		else
			parent::procesa_event();
	}
}
?>