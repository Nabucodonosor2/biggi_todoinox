<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_titulo_detalle
*/
class wi_cx_titulo_detalle extends w_input {
	function wi_cx_titulo_detalle($cod_item_menu) {
		parent::w_input('cx_titulo_detalle', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT		COD_CX_TITULO_DETALLE
							,NOM_CX_TITULO_DETALLE 
				FROM 		CX_TITULO_DETALLE
				WHERE 		COD_CX_TITULO_DETALLE={KEY1}
				ORDER BY 	COD_CX_TITULO_DETALLE";
		$this->dws['dw_titulo_detalle'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_titulo_detalle']->add_control(new edit_text_upper('NOM_CX_TITULO_DETALLE', 140, 140));			
		
		// asigna los mandatorys
		$this->dws['dw_titulo_detalle']->set_mandatory('COD_CX_TITULO_DETALLE', 'Código');	
		$this->dws['dw_titulo_detalle']->set_mandatory('NOM_CX_TITULO_DETALLE', 'Detalle');	
	}
	function new_record() {
		$this->dws['dw_titulo_detalle']->insert_row();
		$this->dws['dw_titulo_detalle']->add_control(new edit_num('COD_CX_TITULO_DETALLE', 12, 10));	
	}
	function load_record() {
		$cod_titulo_detalle = $this->get_item_wo($this->current_record, 'COD_CX_TITULO_DETALLE');
		$this->dws['dw_titulo_detalle']->retrieve($cod_titulo_detalle);
	}
	function get_key() {
		return $this->dws['dw_titulo_detalle']->get_item(0, 'COD_CX_TITULO_DETALLE');
	}
	
	function save_record($db) {
		$cod_tipo_detalle = $this->get_key();
		$nom_tipo_detalle = $this->dws['dw_titulo_detalle']->get_item(0, 'NOM_CX_TITULO_DETALLE');	
		
		$cod_tipo_detalle = ($cod_tipo_detalle=='') ? "null" : $cod_tipo_detalle;		
    
		$sp = 'spu_cx_titulo_detalle';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_tipo_detalle, '$nom_tipo_detalle'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_tipo_detalle = $this->get_key();
				$this->dws['dw_titulo_detalle']->set_item(0, '$cod_tipo_detalle', $cod_tipo_detalle);
			}
			return true;
		}
		return false;
	}
}
////////////////////////

?>