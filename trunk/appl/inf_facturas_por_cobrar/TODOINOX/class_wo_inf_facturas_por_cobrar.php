<?php
class wo_inf_facturas_por_cobrar extends wo_inf_facturas_por_cobrar_base {
	var $checkbox_comercial;
	var $checkbox_bodega;
	var $checkbox_servindus;
	var $checkbox_arriendo;
	var $checkbox_otros;
	var $dw_check_box;
	
	function make_sql() {
   		/* El cod_usuario debe leerese desde la sesion porque no se puede usar $this->cod_usuario
   		   hasta despues de llamar al ancestro
   		 */  
   		$cod_usuario =  session::get("COD_USUARIO");
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
   		$db->EXECUTE_SP("spi_facturas_por_cobrar", "$cod_usuario"); 
   		$sql = "select	I.COD_FACTURA
						,I.NRO_FACTURA
						,I.FECHA_FACTURA
						,I.FECHA_FACTURA_STR
						,I.DATE_FACTURA
						,I.RUT
						,I.DIG_VERIF
						,I.NOM_EMPRESA
						,I.INI_USUARIO_VENDEDOR_A
						,I.INI_USUARIO_VENDEDOR_B
						,I.TOTAL_CON_IVA
						,I.SALDO
						,I.PAGO
						,I.CANTIDAD_FA 
						,I.COD_USUARIO_VENDEDOR1
				FROM INF_FACTURAS_POR_COBRAR I
				where I.COD_USUARIO = $cod_usuario ";

   		if ($this->checkbox_comercial == false)
   			$sql .= " and I.COD_EMPRESA <> 1";
   		if ($this->checkbox_bodega == false)
   			$sql .= " and I.COD_EMPRESA <> 37";
   		if ($this->checkbox_servindus == false)
   			$sql .= " and I.COD_EMPRESA <> 38";
   		
   		if ($this->checkbox_otros == false && $this->checkbox_arriendo == false)
   			$sql .= " and I.COD_EMPRESA in (1,37,38) and I.COD_TIPO_FACTURA <> 2";
   		else if ($this->checkbox_otros == false && $this->checkbox_arriendo == true)
   			$sql .= " and I.COD_TIPO_FACTURA = 2";
   		else if ($this->checkbox_otros == true && $this->checkbox_arriendo == false)
   			$sql .= " and I.COD_TIPO_FACTURA <> 2";	
   		$sql .= " ORDER BY I.FECHA_FACTURA";
   		
		return $sql;		
	}
   function wo_inf_facturas_por_cobrar() {
   		$this->checkbox_comercial = false;
		$this->checkbox_bodega = false;
		$this->checkbox_servindus = false;
		$this->checkbox_arriendo = false;
		$this->checkbox_otros = true;
   	
		$sql = $this->make_sql();
		
		parent::w_informe_pantalla('inf_facturas_por_cobrar', $sql, $_REQUEST['cod_item_menu']);
		
		$this->dw->add_control(new edit_text_hidden('COD_NOTA_VENTA_H'));
		
		// headers
		$this->add_header(new header_num('NRO_FACTURA', 'I.NRO_FACTURA', 'Número'));
		$this->add_header($control = new header_date('FECHA_FACTURA_STR', 'I.FECHA_FACTURA', 'Fecha'));//*****
		$control->field_bd_order = 'DATE_FACTURA';
		$this->add_header(new header_text('NOM_EMPRESA', "I.NOM_EMPRESA", 'Cliente'));
		$sql = "select	distinct I.COD_USUARIO_VENDEDOR1 COD_USUARIO ,U.NOM_USUARIO 
				FROM INF_FACTURAS_POR_COBRAR I left outer join USUARIO U on U.COD_USUARIO = I.COD_USUARIO_VENDEDOR1 
				order by U.NOM_USUARIO";

		$this->add_header(new header_drop_down('INI_USUARIO_VENDEDOR_A', 'I.COD_USUARIO_VENDEDOR1', 'V1', $sql));
		$this->add_header(new header_rut('RUT', 'I', 'Rut'));
		$this->add_header(new header_num('TOTAL_CON_IVA', 'I.TOTAL_CON_IVA', 'Total', 0, true, 'SUM'));
		$this->add_header($control = new header_num('SALDO', 'I.SALDO', 'Saldo', 0, true, 'SUM'));
		$this->add_header($control = new header_num('PAGO', 'I.PAGO', 'Pagos', 0, true, 'SUM'));
		$this->add_header(new header_num('CANTIDAD_FA', '1', '', 0, true, 'SUM'));
		
		// controls
		$this->dw->add_control(new static_num('RUT'));
		$this->dw->add_control(new static_num('TOTAL_CON_IVA'));
		$this->dw->add_control(new static_num('SALDO'));
		$this->dw->add_control(new static_num('PAGO'));
   		
		$sql = "select 'N' CHECK_COMERCIAL,
					   'N' CHECK_BODEGA,
					   'N' CHECK_SERVINDUS,
					   'N' CHECK_ARRIENDO, 	
					   'S' CHECK_OTROS, 	
					   'N' HIZO_CLICK";
		$this->dw_check_box = new datawindow($sql);
		$this->dw_check_box->add_control($control = new edit_check_box('CHECK_COMERCIAL','S','N'));
		$control->set_onClick("agrega_factura(); document.getElementById('loader').style.display='';");
		$this->dw_check_box->add_control($control = new edit_check_box('CHECK_BODEGA','S','N'));
		$control->set_onClick("agrega_factura(); document.getElementById('loader').style.display='';");
		$this->dw_check_box->add_control($control = new edit_check_box('CHECK_SERVINDUS','S','N'));
		$control->set_onClick("agrega_factura(); document.getElementById('loader').style.display='';");
		$this->dw_check_box->add_control($control = new edit_check_box('CHECK_ARRIENDO','S','N'));
		$control->set_onClick("agrega_factura(); document.getElementById('loader').style.display='';");
		$this->dw_check_box->add_control($control = new edit_check_box('CHECK_OTROS','S','N'));
		$control->set_onClick("agrega_factura(); document.getElementById('loader').style.display='';");
		$this->dw_check_box->add_control(new edit_text_hidden('HIZO_CLICK'));
		$this->dw_check_box->retrieve();
	}
	function redraw(&$temp){
		parent::redraw(&$temp);
		$this->dw_check_box->habilitar($temp, true);
	}	
	function procesa_event() {
		if ($_POST['HIZO_CLICK_0'] == 'S') {
			$this->checkbox_comercial = isset($_POST['CHECK_COMERCIAL_0']);
			$this->checkbox_bodega = isset($_POST['CHECK_BODEGA_0']);
			$this->checkbox_servindus = isset($_POST['CHECK_SERVINDUS_0']);
			$this->checkbox_arriendo = isset($_POST['CHECK_ARRIENDO_0']);
			$this->checkbox_otros = isset($_POST['CHECK_OTROS_0']);
			
			if ($this->checkbox_comercial)
				$this->dw_check_box->set_item(0, 'CHECK_COMERCIAL', 'S');
			else{
				$this->dw_check_box->set_item(0, 'CHECK_COMERCIAL', 'N');
			}
			
			if ($this->checkbox_bodega)
				$this->dw_check_box->set_item(0, 'CHECK_BODEGA', 'S');
			else
				$this->dw_check_box->set_item(0, 'CHECK_BODEGA', 'N');

			if ($this->checkbox_servindus)
				$this->dw_check_box->set_item(0, 'CHECK_SERVINDUS', 'S');
			else
				$this->dw_check_box->set_item(0, 'CHECK_SERVINDUS', 'N');
				
			if ($this->checkbox_arriendo)
				$this->dw_check_box->set_item(0, 'CHECK_ARRIENDO', 'S');
			else
				$this->dw_check_box->set_item(0, 'CHECK_ARRIENDO', 'N');	

			if ($this->checkbox_otros)
				$this->dw_check_box->set_item(0, 'CHECK_OTROS', 'S');
			else
				$this->dw_check_box->set_item(0, 'CHECK_OTROS', 'N');

			$sql = $this->make_sql();
			$this->dw->set_sql($sql);
			$this->sql_original = $sql;
			$this->save_SESSION();	
			$this->make_filtros();
			$this->retrieve();	
		}else{ 
			$this->checkbox_comercial = 0;
			$this->checkbox_bodega = 0;
			$this->checkbox_servindus = 0;
			$this->checkbox_arriendo = 0;
			$this->checkbox_otros = 1;
			parent::procesa_event();
			
		}
	}
}
?>