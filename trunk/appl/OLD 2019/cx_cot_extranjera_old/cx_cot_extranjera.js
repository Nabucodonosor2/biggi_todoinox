function validate() {

}
function help_proveedor(ve_control){
	if(ve_control.value == 'COMPONENT'){
		ve_control.value='COMPONENT';
		document.getElementById('COD_PROVEEDOR_EXT_0').value = '122';
		document.getElementById('NOM_PROVEEDOR_EXT_0').innerHTML = 'COMPONENT HARDWARE GROUP INC';
		document.getElementById('DIRECCION_0').innerHTML = '1890 SWARTHMORE AVE., P.O. BOX 1582';
		document.getElementById('NOM_PAIS_0').innerHTML = 'U S A';
		document.getElementById('NOM_CIUDAD_0').innerHTML = 'N. J. 08701';
		document.getElementById('POST_OFFICE_BOX_0').innerHTML = '';
	}else{
		alert('No existe el proveedor ingresado');
		ve_control.value='';
		document.getElementById('COD_PROVEEDOR_EXT_0').value = '';
		document.getElementById('NOM_PROVEEDOR_EXT_0').innerHTML = '';
		document.getElementById('DIRECCION_0').innerHTML = '';
		document.getElementById('NOM_PAIS_0').innerHTML = '';
		document.getElementById('NOM_CIUDAD_0').innerHTML = '';
		document.getElementById('POST_OFFICE_BOX_0').innerHTML = ''; 
	}
}
function monto_total(){
 	var aTR = get_TR('ITEM_CX_COT_EXTRANJERA');
	vl_monto_total = 0;
	for (i=0; i < aTR.length; i++) {
		var precio = document.getElementById('PRECIO_' + get_num_rec_field(aTR[i].id)).value;
		precio = precio.replace(",",".");
		if(precio == '0,00'){
			return false;
		}
		var cantidad = document.getElementById('CANTIDAD_' + get_num_rec_field(aTR[i].id)).value;
		vl_cant_precio = parseFloat(precio) * parseFloat(cantidad);
		vl_monto_total = parseFloat(vl_cant_precio) + parseFloat(vl_monto_total);
	}
	document.getElementById('MONTO_TOTAL_H_0').value = vl_monto_total;
	vl_monto_total = number_format(vl_monto_total, 2, ',', '.');
	document.getElementById('MONTO_TOTAL_0').innerHTML = vl_monto_total;
}

function del_line_standard(tr_id, nom_tabla) {
	var tr = document.getElementById(tr_id); 
	var label_record = get_nom_field(tr_id);
	var record = get_num_rec_field(tr_id);

	var ajax = nuevoAjax();
	ajax.open("GET", "../../../../commonlib/trunk/php/del_line.php?nom_tabla="+nom_tabla+"&label_record="+label_record+"&record="+record, false);
	ajax.send(null);	
	var resp = ajax.responseText;
	recalc_sum(tr);
	tr.parentNode.removeChild(tr);
	monto_total();
}
function add_line_item(tabla_id, nom_tabla) {
	
	var row = add_line(tabla_id, nom_tabla);
	var aTR = get_TR('ITEM_CX_COT_EXTRANJERA');
	var item = 1;
	var letra = '';
	for (var i=aTR.length - 2; i >=0; i--, item++) {
		var cod_producto_value = document.getElementById('COD_PRODUCTO_' + get_num_rec_field(aTR[i].id)).value
		if (cod_producto_value=='T') {
			letra = document.getElementById('ITEM_' + get_num_rec_field(aTR[i].id)).value; 
			break;
		}
	}	
	document.getElementById('ITEM_' + row).value = letra + item;
}
function help_producto(campo, num_dec) {
	var campo_id = campo.id;
	var field = get_nom_field(campo_id);
	var record = get_num_rec_field(campo_id);

	var cod_producto = document.getElementById('COD_PRODUCTO_' + record); 
	var nom_producto = document.getElementById('NOM_PRODUCTO_' + record); 
	var precio = document.getElementById('PRECIO_' + record);

	cod_producto.value = cod_producto.value.toUpperCase();
	var cod_producto_value = nom_producto_value = '';
	switch (field) {
	case 'COD_PRODUCTO': if (cod_producto.value=='TE') {
   							ingreso_TE(cod_producto);
   							return;
   						}
   						var boton_precio = document.getElementById('BOTON_PRECIO_' + record);
   						if (boton_precio)
   							boton_precio.value =  'Precio';
   						cod_producto_value = campo.value;	
   						break;
	case 'NOM_PRODUCTO': if (cod_producto.value=='T' || cod_producto.value=='TE') return;   											
   						nom_producto_value = campo.value;	
   						break;
	}
	var ajax = nuevoAjax();
	cod_producto_value = URLEncode(cod_producto_value);
	nom_producto_value = URLEncode(nom_producto_value);
	ajax.open("GET", "../registro_ingreso/help_producto.php?cod_producto="+cod_producto_value+"&nom_producto="+nom_producto_value, false);
	ajax.send(null);		

	var resp = URLDecode(ajax.responseText);
	var lista = resp.split('|');
	switch (lista[0]) {
  	case '0':	
				alert('El producto no existe, favor ingrese nuevamente');
			cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
			campo.focus();
	   	break;
  	case '1': 				
  		select_1_producto(lista, record);
	   	break;
  	default:
		var args = "dialogLeft:100px;dialogTop:300px;dialogWidth:650px;dialogHeight:200px;dialogLocation:0;Toolbar:'yes';";
 			var returnVal = window.showModalDialog("../../../../commonlib/trunk/php/help_lista_producto.php?sql="+URLEncode(lista[1]), "_blank", args);
	   	if (returnVal == null) {
 				alert('El producto no existe, favor ingrese nuevamente');
				cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
				campo.focus();
			}
			else {
				returnVal = URLDecode(returnVal);
			   	var valores = returnVal.split('|');
		  		select_1_producto(valores, record);
			}
			break;
	}
	
	recalc_computed_relacionados(record, 'PRECIO');
}

function equipo_oc_ex(ve_control){

	var cod_producto = ve_control.value;
	var campo_id = ve_control.id;
	var record = get_num_rec_field(campo_id);
	
	var vl_ajax = nuevoAjax();
	vl_ajax.open("GET", "ajax_display_equipo_cot_ex.php?cod_producto="+cod_producto, false);
	vl_ajax.send(null);	
		
	var vl_resp = URLDecode(vl_ajax.responseText);
	
	var vl_result = eval("(" + vl_resp + ")");

	document.getElementById('COD_EQUIPO_OC_EX_' + record).innerHTML = vl_result[0]['COD_EQUIPO_OC_EX'];
	document.getElementById('DESC_EQUIPO_OC_EX_' + record).innerHTML = vl_result[0]['DESC_EQUIPO_OC_EX'];
	document.getElementById('COD_EQUIPO_OC_EX_H_' + record).value = vl_result[0]['COD_EQUIPO_OC_EX'];
	document.getElementById('DESC_EQUIPO_OC_EX_H_' + record).value = vl_result[0]['DESC_EQUIPO_OC_EX'];

}