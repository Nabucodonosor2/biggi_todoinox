<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_puerto_salida
*/
class wi_cx_puerto_salida extends w_input {
	function wi_cx_puerto_salida($cod_item_menu) {
		parent::w_input('cx_puerto_salida', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT 	COD_CX_PUERTO_SALIDA
						,NOM_CX_PUERTO_SALIDA 
				FROM 	CX_PUERTO_SALIDA
				WHERE 	COD_CX_PUERTO_SALIDA={KEY1}
				ORDER 	BY COD_CX_PUERTO_SALIDA";
		$this->dws['dw_puerto_salida'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_puerto_salida']->add_control(new edit_text_upper('NOM_CX_PUERTO_SALIDA', 80, 100));			
		
		// asigna los mandatorys
		$this->dws['dw_puerto_salida']->set_mandatory('COD_CX_PUERTO_SALIDA', 'Código de Puerto Salida');	
		$this->dws['dw_puerto_salida']->set_mandatory('NOM_CX_PUERTO_SALIDA', 'Nombre de Puerto Salida');	
		
	}
	function new_record() {
		$this->dws['dw_puerto_salida']->insert_row();
		$this->dws['dw_puerto_salida']->add_control(new edit_num('COD_CX_PUERTO_SALIDA', 12, 10));	
	}
	function load_record() {
		$cod_puerto_salida = $this->get_item_wo($this->current_record, 'COD_CX_PUERTO_SALIDA');
		$this->dws['dw_puerto_salida']->retrieve($cod_puerto_salida);
	}
	function get_key() {
		return $this->dws['dw_puerto_salida']->get_item(0, 'COD_CX_PUERTO_SALIDA');
	}
	
	function save_record($db) {
		$cod_puerto_salida = $this->get_key();
		$nom_puerto_salida = $this->dws['dw_puerto_salida']->get_item(0, 'NOM_CX_PUERTO_SALIDA');	
		
		$cod_puerto_salida = ($cod_puerto_salida=='') ? "null" : $cod_puerto_salida;		
    
		$sp = 'spu_cx_puerto_salida';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_puerto_salida, '$nom_puerto_salida'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_puerto_salida = $this->get_key();
				$this->dws['dw_puerto_salida']->set_item(0, 'COD_CX_PUERTO_SALIDA', $cod_puerto_salida);
			}
			return true;
		}
		return false;
	}
}
////////////////////////

?>