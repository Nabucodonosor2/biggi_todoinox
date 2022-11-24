<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");
require_once(dirname(__FILE__)."/../empresa/class_dw_help_empresa.php");
require_once(dirname(__FILE__)."/../common_appl/class_w_cot_nv.php");

/*******************************************************************************************************************************/
 
class wi_cx_parametro extends w_input {
			
	function wi_cx_parametro() 	{
		$this->tiene_wo = false;
		parent::w_input('cx_parametro', $_REQUEST['cod_item_menu']);
		
		$this->dws['dw_cx_parametro'] = new datawindow('SELECT 1');				

		$this->save_SESSION();
		$this->need_redraw();
		header("Location: wi_cx_parametro.php"); // para borrra el REQUEST
	}
	function load_record() 	{
		$this->current_record = 0;
		$this->dws['dw_cx_parametro']->retrieve();
	}
	
	function get_key() 	{
		return 0;
	}

	function goto_list() 	{
		$this->unlock_record();
		header('Location:' . $this->root_url . '../../commonlib/trunk/php/presentacion.php');		
	}
	function procesa_event(){		
		if (session::is_set('REDRAW_' . $this->nom_tabla)) {
			session::un_set('REDRAW_' . $this->nom_tabla);
			$this->load_record();
			$this->redraw();
		} 
		else
			parent::procesa_event();
	}
}
?>