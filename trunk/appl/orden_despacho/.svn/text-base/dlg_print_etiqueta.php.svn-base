<?php
require_once(dirname(__FILE__)."/../../../../commonlib/trunk/php/auto_load.php");

$temp = new Template_appl('dlg_print_etiqueta.htm');
$cod_orden_despacho = $_REQUEST['cod_orden_despacho'];

$db = new database(K_TIPO_BD, K_SERVER, K_BD, K_USER, K_PASS);
$sql = "exec spdw_ventana_item_etiqueta $cod_orden_despacho";

class edit_radio_button_linea extends edit_radio_button{
	function edit_radio_button_linea($field, $value_true, $value_false, $label='', $group='') {
		parent::edit_radio_button($field, $value_true, $value_false, $label, $group);
	}
	function draw_entrable($dato, $record) {
		$field = $this->field.'_'.$record;
		if ($this->group == '')
			$group = $field;
		else
			$group = $this->group;
		
		$group = $group.'_'.$record;
		if ($dato == $this->value_true)
			 $ctrl = '<label><input name="'.$group.'" type="radio" id="'.$field.'" value="'.$this->value_true.'" checked="checked" '.$this->make_java_script().'/>'.$this->label.'</label>';
		else
			 $ctrl = '<label><input name="'.$group.'" type="radio" id="'.$field.'" value="'.$this->value_true.'" '.$this->make_java_script().'/>'.$this->label.'</label>';

		return $ctrl;
	}
}

$dw = new datawindow($sql, 'ITEM_ORDEN_DESPACHO');

$dw->add_control(new edit_text_hidden('COD_PRODUCTO'));
$dw->add_control(new static_text('COD_PRODUCTO_S'));
$dw->add_control(new static_num('CANTIDAD'));
$dw->add_control(new edit_text_hidden('NORMAL_CHECK'));
$dw->add_control($control = new edit_radio_button_linea('IMPRESION_NORMAL', 'S', 'N','','OD'));
$control->set_onChange('change_value(this);');
$dw->add_control($control = new edit_radio_button_linea('OMITIR_IMPRESION', 'S', 'N','','OD'));
$control->set_onChange('change_value(this);');
$dw->add_control($control = new edit_num('BULTO_UNO', 5, 5));
$control->set_onChange('if(valida_cantidad(this)) change_value(this);');
$dw->add_control($control = new edit_num('BULTO_DOS', 5, 5));
$control->set_onChange('if(valida_cantidad(this))  change_value(this);');
$dw->add_control($control = new edit_num('BULTO_TRES', 5, 5));
$control->set_onChange('if(valida_cantidad(this))  change_value(this);');
$dw->add_control($control = new edit_num('BULTO_CUATRO', 5, 5));
$control->set_onChange('if(valida_cantidad(this))  change_value(this);');
$dw->add_control($control = new edit_num('BULTO_CINCO', 5, 5));
$control->set_onChange('if(valida_cantidad(this))  change_value(this);');
$dw->add_control($control = new edit_num('BULTO_SEIS', 5, 5));
$control->set_onChange('if(valida_cantidad(this))  change_value(this);');

$dw->retrieve();
$dw->habilitar($temp, true);
print $temp->toString();	
?>