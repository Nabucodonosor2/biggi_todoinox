<?php
ini_set('memory_limit', '720M');
ini_set('max_execution_time', 900);

class wi_inf_bodega_tarjeta_existencia extends wi_inf_bodega_tarjeta_existencia_base {	
	function wi_inf_bodega_tarjeta_existencia($cod_item_menu) {
		parent::wi_inf_bodega_tarjeta_existencia_base($cod_item_menu);
		
		$this->xml = session::get('K_ROOT_DIR').'appl/inf_bodega_tarjeta_existencia/TODOINOX/inf_bodega_tarjeta_existencia.xml';
		
		$sql_bodega="SELECT COD_BODEGA
							,NOM_BODEGA
					FROM BODEGA
					where COD_BODEGA = 1	
					ORDER BY COD_BODEGA ASC";
		$this->dws['dw_param']->add_control(new drop_down_dw('COD_BODEGA', $sql_bodega, 0, '', false));
	}
	function genera_pdf($labels = array(), $con_logo = true,$orientation='P',$unit='pt',$format='letter') {
		$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
		$sql = "exec spi_bodega_tarjeta_existencia ".$this->param;
		$result = $db->build_results($sql);
		
		$cod_producto = $result[0]['COD_PRODUCTO'];
		
		$sql2="select SUM(i.CANTIDAD) SUM_CANT
				from ENTRADA_BODEGA e
					,ITEM_ENTRADA_BODEGA i
				where e.TIPO_DOC = 'REGISTRO_INGRESO'
				and i.COD_ENTRADA_BODEGA = e.COD_ENTRADA_BODEGA
				and i.COD_PRODUCTO = '$cod_producto'";
		$result2 = $db->build_results($sql2);
		
		$sql3="SELECT NOM_PRODUCTO 
				FROM PRODUCTO WHERE COD_PRODUCTO = '$cod_producto'";
		$result3 = $db->build_results($sql3);
		
		$labels = array();
		$labels['strSUM_CANT'] =$result2[0]['SUM_CANT'];
		$labels['strFECHA_INICIO'] = $result[0]['FECHA_INICIO'];
		$labels['strFECHA_TERMINO'] = $result[0]['FECHA_TERMINO'];
		$labels['strCOD_PRODUCTO'] = $result[0]['COD_PRODUCTO'];
		$labels['strNOM_PRODUCTO'] = $result3[0]['NOM_PRODUCTO'];
		$rpt = new reporte($sql, $this->xml, $labels, $this->nom_informe, 1, true);
		$this->_load_record();
		
		
	}
}
?>