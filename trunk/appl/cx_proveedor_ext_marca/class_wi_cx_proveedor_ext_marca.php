<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

class dw_revision_stock extends datawindow {
    function dw_revision_stock() {
        $sql = "EXEC dbo.spdw_revision_stock {KEY1}";
        
        parent::datawindow($sql, 'REVISION_STOCK', true, true);
    }
}

class wi_cx_proveedor_ext_marca extends w_input{
	function wi_cx_proveedor_ext_marca($cod_item_menu){
		parent::w_input('cx_proveedor_ext_marca', $cod_item_menu);

		$sql = "SELECT CPE.COD_CX_PROVEEDOR_EXT_MARCA 
                    ,PE.COD_PROVEEDOR_EXT
                    ,ALIAS_PROVEEDOR_EXT
                    ,NOM_PROVEEDOR_EXT
                    ,M.NOM_MARCA
                FROM PROVEEDOR_EXT PE
                    ,CX_PROVEEDOR_EXT_MARCA CPE
                    ,MARCA M
                WHERE CPE.COD_CX_PROVEEDOR_EXT_MARCA = {KEY1}
                AND PE.COD_PROVEEDOR_EXT = CPE.COD_PROVEEDOR_EXT
                AND CPE.COD_MARCA = M.COD_MARCA";

		$this->dws['dw_cx_proveedor_ext_marca'] = new datawindow($sql);
        $this->dws['dw_revision_stock'] = new dw_revision_stock();

		// asigna los formatos				
		//$this->dws['dw_cx_proveedor_ext_marca']->add_control(new edit_text_upper('NOM_CX_PUERTO_ARRIBO', 80, 100));
	}

	function load_record(){
		$cod_cx_proveedor_ext_marca = $this->get_item_wo($this->current_record, 'COD_CX_PROVEEDOR_EXT_MARCA');
		$this->dws['dw_cx_proveedor_ext_marca']->retrieve($cod_cx_proveedor_ext_marca);
        $cod_proveedor_ext = $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_PROVEEDOR_EXT');
        $this->dws['dw_revision_stock']->retrieve($cod_proveedor_ext);
	}
    
	function get_key(){
		return $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_CX_PROVEEDOR_EXT_MARCA');
	}

    function navegacion($temp){
        parent::navegacion($temp);
        $arr_date = explode('/', $this->current_date());
        $year = $arr_date[2];
        $cod_proveedor_ext = $this->dws['dw_cx_proveedor_ext_marca']->get_item(0, 'COD_PROVEEDOR_EXT');

        $db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
        $sql = "SELECT CORRELATIVO_OC
                FROM CX_OC_EXTRANJERA
                WHERE COD_PROVEEDOR_EXT = $cod_proveedor_ext
                AND ETA_DATE > GETDATE()";
        $result = $db->build_results($sql);

        $temp->setVar("H_NEXT_CORRELATIVO", $result[0]['CORRELATIVO_OC']);
        $temp->setVar("H_VENTA_TRES", 'VENTA '.($arr_date[2] - 3));
        $temp->setVar("H_VENTA_DOS", 'VENTA '.($arr_date[2] - 2));
        $temp->setVar("H_VENTA_UNO", 'VENTA '.($arr_date[2] - 1));
        $temp->setVar("H_VENTA_HOY", 'VENTA al '.$this->current_date());
    }
}
?>