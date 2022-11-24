<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_titulo_detalle
*/
class wi_cx_transportista extends w_input {
	function wi_cx_transportista($cod_item_menu) {
		parent::w_input('cx_transportista', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT	COD_CX_TRANSPORTISTA
						,NOM_CX_TRANSPORTISTA
						,DIRECCION
						,CONTACTO 
				FROM	CX_TRANSPORTISTA
				WHERE 	COD_CX_TRANSPORTISTA={KEY1}
				ORDER BY COD_CX_TRANSPORTISTA";
		$this->dws['dw_transportista'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_transportista']->add_control(new edit_text_upper('NOM_CX_TRANSPORTISTA', 80, 100));			
		$this->dws['dw_transportista']->add_control(new edit_text_upper('DIRECCION', 80, 100));
		$this->dws['dw_transportista']->add_control(new edit_text_upper('CONTACTO', 80, 100));
		
		// asigna los mandatorys
		$this->dws['dw_transportista']->set_mandatory('COD_CX_TRANSPORTISTA', 'Código');	
		$this->dws['dw_transportista']->set_mandatory('NOM_CX_TRANSPORTISTA', 'Transportista');
		$this->dws['dw_transportista']->set_mandatory('DIRECCION', 'Dirección');	
		$this->dws['dw_transportista']->set_mandatory('CONTACTO', 'Contacto');		
	}
	function new_record() {
		$this->dws['dw_transportista']->insert_row();
		$this->dws['dw_transportista']->add_control(new edit_num('COD_CX_TRANSPORTISTA', 12, 10));	
	}
	function load_record() {
		$cod_titulo_detalle = $this->get_item_wo($this->current_record, 'COD_CX_TRANSPORTISTA');
		$this->dws['dw_transportista']->retrieve($cod_titulo_detalle);
	}
	function get_key() {
		return $this->dws['dw_transportista']->get_item(0, 'COD_CX_TRANSPORTISTA');
	}
	
	function save_record($db) {
		$cod_transportista= $this->get_key();
		$nom_transportista = $this->dws['dw_transportista']->get_item(0, 'NOM_CX_TRANSPORTISTA');
		$direccion = $this->dws['dw_transportista']->get_item(0, 'DIRECCION');	
		$contacto = $this->dws['dw_transportista']->get_item(0, 'CONTACTO');	
		
		$cod_transportista = ($cod_transportista=='') ? "null" : $cod_transportista;		
    
		$sp = 'spu_cx_transportista';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_transportista, '$nom_transportista','$direccion','$contacto'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_transportista = $this->get_key();
				$this->dws['dw_transportista']->set_item(0, 'COD_CX_TRANSPORTISTA', $cod_transportista);
			}
			return true;
		}
		return false;
	}
}
////////////////////////

?>