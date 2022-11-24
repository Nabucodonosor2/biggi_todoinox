<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

/*
Clase : wi_cx_puerto_arribo
*/
class wi_cx_puerto_arribo extends w_input {
	function wi_cx_puerto_arribo($cod_item_menu) {
		parent::w_input('cx_puerto_arribo', $cod_item_menu);
		$this->valida_llave = true;		// valida que la PK sea unica

		$sql = "SELECT	 COD_CX_PUERTO_ARRIBO
						,NOM_CX_PUERTO_ARRIBO 
				FROM 	CX_PUERTO_ARRIBO
				WHERE 	COD_CX_PUERTO_ARRIBO={KEY1}
				ORDER 	BY COD_CX_PUERTO_ARRIBO";
		$this->dws['dw_puerto_arribo'] = new datawindow($sql);

		// asigna los formatos				
		$this->dws['dw_puerto_arribo']->add_control(new edit_text_upper('NOM_CX_PUERTO_ARRIBO', 80, 100));			
		
		// asigna los mandatorys
		$this->dws['dw_puerto_arribo']->set_mandatory('COD_CX_PUERTO_ARRIBO', 'Código');	
		$this->dws['dw_puerto_arribo']->set_mandatory('NOM_CX_PUERTO_ARRIBO', 'Puerto Arribo');	
		
	}
	function new_record() {
		$this->dws['dw_puerto_arribo']->insert_row();
		$this->dws['dw_puerto_arribo']->add_control(new edit_num('COD_CX_PUERTO_ARRIBO', 12, 10));	
	}
	function load_record() {
		$cod_puerto_arribo = $this->get_item_wo($this->current_record, 'COD_CX_PUERTO_ARRIBO');
		$this->dws['dw_puerto_arribo']->retrieve($cod_puerto_arribo);
	}
	function get_key() {
		return $this->dws['dw_puerto_arribo']->get_item(0, 'COD_CX_PUERTO_ARRIBO');
	}
	
	function save_record($db) {
		$cod_puerto_arribo = $this->get_key();
		$nom_puerto_arribo = $this->dws['dw_puerto_arribo']->get_item(0, 'NOM_CX_PUERTO_ARRIBO');	
		
		$cod_puerto_arribo = ($cod_puerto_arribo=='') ? "null" : $cod_puerto_arribo;		
    
		$sp = 'spu_cx_puerto_arribo';
	    if ($this->is_new_record())
	    	$operacion = 'INSERT';
	    else
	    	$operacion = 'UPDATE';
	    
	    $param	= "'$operacion', $cod_puerto_arribo, '$nom_puerto_arribo'"; 	
			
		if ($db->EXECUTE_SP($sp, $param)){
			if ($this->is_new_record()) {
				$cod_puerto_arribo = $this->get_key();
				$this->dws['dw_puerto_arribo']->set_item(0, 'COD_CX_PUERTO_ARRIBO', $cod_puerto_arribo);
			}
			return true;
		}
		return false;
	}
}
////////////////////////

?>