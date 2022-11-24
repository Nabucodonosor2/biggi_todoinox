function validate() {
	var aTR = get_TR('ITEM_CX_COT_EXTRANJERA');
	if(aTR.length == 0){
		alert('Debe ingresar al menos un item');
		return false;
	}
	
	return true;
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

function ingreso_TE(cod_producto) {	

	var record = get_num_rec_field(cod_producto.id);
	// datos del TE		
	var nom_te_value = URLEncode(get_value('NOM_PRODUCTO_' + record));
	var previo_value = URLEncode(document.getElementById('PRECIO_H_' + record).value);
	var cod_tipo_te_value = URLEncode(document.getElementById('COD_TIPO_TE_' + record).value);
	var motivo_te_value = URLEncode(document.getElementById('MOTIVO_TE_' + record).value);

	// solo para NV, existe nom_usuario_autoriza_te 
	var nom_usuario_autoriza_te = document.getElementById('NOM_USUARIO_AUTORIZA_TE_' + record);
	if (nom_usuario_autoriza_te) {
	
		var nom_usuario_autoriza_te_value = URLEncode(document.getElementById('NOM_USUARIO_AUTORIZA_TE_' + record).value);
		var fecha_autoriza_te_value = URLEncode(document.getElementById('FECHA_AUTORIZA_TE_' + record).value);
		var motivo_autoriza_te_value = URLEncode(document.getElementById('MOTIVO_AUTORIZA_TE_' + record).value);
		
		var url = "../common_appl/ingreso_TE.php?nom_te="+nom_te_value+"&precio="+previo_value+"&cod_tipo_te="+cod_tipo_te_value+"&motivo_te="+motivo_te_value+"&nom_usuario_autoriza_te="+nom_usuario_autoriza_te_value+"&fecha_autoriza_te="+fecha_autoriza_te_value+"&motivo_autoriza_te="+motivo_autoriza_te_value;		
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 430,
			 width: 550,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			 	
				if (returnVal == null){
					if (cod_tipo_te_value!='')	// mantiene los valores anteriores.
		 			return;
		 				
		 		cod_producto.value = '';
				
				var precio 			= document.getElementById('PRECIO_'+ record);
				var precio_h 		= document.getElementById('PRECIO_H_' + record);
				var motivo  		= document.getElementById('MOTIVO_TE_' + record);
				var tipo_TE  		= document.getElementById('COD_TIPO_TE_' + record);		
				var cantidad  		= document.getElementById('CANTIDAD_' + record);
				
				
				set_value('NOM_PRODUCTO_' + record, '', '');
				precio.innerHTML 	= '';
				precio_h.value 		= '';
				motivo.value		= '';
				tipo_TE.value		= '';
				cantidad.value		= '';		
				
				recalc_computed_relacionados(record, 'PRECIO');	
				}		
				else {
					var vl_boton_precio = document.getElementById('BOTON_PRECIO_' + record)
					if (vl_boton_precio)
						vl_boton_precio.value = 'TE';
					var res = returnVal.split('|');	
					var precio 			= document.getElementById('PRECIO_'+ record);
					var precio_h 		= document.getElementById('PRECIO_H_' + record);
					var motivo  		= document.getElementById('MOTIVO_TE_' + record);
					var tipo_TE  		= document.getElementById('COD_TIPO_TE_' + record);		
					var cantidad  		= document.getElementById('CANTIDAD_' + record);
					var motivo_autoriza_te	= document.getElementById('MOTIVO_AUTORIZA_TE_' + record);	
					
					set_value('NOM_PRODUCTO_' + record, res[0], res[0]);
					set_value('PRECIO_' + record, res[1], number_format(res[1], 0, ',', '.'));
					precio_h.value 		= res[1];
					motivo.value		= res[2];
					tipo_TE.value		= res[3];
					if (nom_usuario_autoriza_te)
						motivo_autoriza_te.value  = res[4];
			
					cantidad.focus();		
					
					recalc_computed_relacionados(record, 'PRECIO');
				}
				
				return true;	
			}
		});
	}
	else {
		var url = "../common_appl/ingreso_TE.php?nom_te="+nom_te_value+"&precio="+previo_value+"&cod_tipo_te="+cod_tipo_te_value+"&motivo_te="+motivo_te_value;		
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 430,
			 width: 550,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			 	
				if (returnVal == null){
					if (cod_tipo_te_value!='')	// mantiene los valores anteriores.
		 				return;
		 				
			 		cod_producto.value = '';
					
					var precio 			= document.getElementById('PRECIO_'+ record);
					var precio_h 		= document.getElementById('PRECIO_H_' + record);
					var motivo  		= document.getElementById('MOTIVO_TE_' + record);
					var tipo_TE  		= document.getElementById('COD_TIPO_TE_' + record);		
					var cantidad  		= document.getElementById('CANTIDAD_' + record);
					
					
					set_value('NOM_PRODUCTO_' + record, '', '');
					precio.innerHTML 	= '';
					precio_h.value 		= '';
					motivo.value		= '';
					tipo_TE.value		= '';
					cantidad.value		= '';		
					
					recalc_computed_relacionados(record, 'PRECIO');	
				}		
				else {
					var vl_boton_precio = document.getElementById('BOTON_PRECIO_' + record)
					if (vl_boton_precio)
						vl_boton_precio.value = 'TE';
					var res = returnVal.split('|');	
					var precio 			= document.getElementById('PRECIO_'+ record);
					var precio_h 		= document.getElementById('PRECIO_H_' + record);
					var motivo  		= document.getElementById('MOTIVO_TE_' + record);
					var tipo_TE  		= document.getElementById('COD_TIPO_TE_' + record);		
					var cantidad  		= document.getElementById('CANTIDAD_' + record);
					var motivo_autoriza_te	= document.getElementById('MOTIVO_AUTORIZA_TE_' + record);	
					
					set_value('NOM_PRODUCTO_' + record, res[0], res[0]);
					set_value('DESC_EQUIPO_OC_EX_' + record, res[0], res[0]);
					set_value('DESC_EQUIPO_OC_EX_H_' + record, res[0], res[0]);
					set_value('COD_EQUIPO_OC_EX_' + record, 'TE', 'TE');
					set_value('COD_EQUIPO_OC_EX_H_' + record, 'TE', 'TE');
					set_value('PRECIO_' + record, res[1], number_format(res[1], 0, ',', '.'));
					precio_h.value 		= res[1];
					motivo.value		= res[2];
					tipo_TE.value		= res[3];
					if (nom_usuario_autoriza_te)
						motivo_autoriza_te.value  = res[4];
			
					cantidad.focus();		
					
					recalc_computed_relacionados(record, 'PRECIO');
				}
				
				return true;	
			}
		});
	}
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
		var url = "../../../../commonlib/trunk/php/help_lista_producto.php?sql="+URLEncode(lista[1]);
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 240,
			 width: 670,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			 	if (returnVal == null){		
					alert('El producto no existe, favor ingrese nuevamente');
					cod_producto.value = nom_producto.value = precio.innerHTML = ''; 
					campo.focus();
					recalc_computed_relacionados(record, 'PRECIO');
				}			
				else {
					returnVal = URLDecode(returnVal);
			   		var valores = returnVal.split('|');
		  			select_1_producto(valores, record);
		  			recalc_computed_relacionados(record, 'PRECIO');
				}
			}
		});		
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
	document.getElementById('DESC_EQUIPO_OC_EX_' + record).innerHTML = URLDecode(vl_result[0]['DESC_EQUIPO_OC_EX']);
	document.getElementById('COD_EQUIPO_OC_EX_H_' + record).value = vl_result[0]['COD_EQUIPO_OC_EX'];
	document.getElementById('DESC_EQUIPO_OC_EX_H_' + record).value = URLDecode(vl_result[0]['DESC_EQUIPO_OC_EX']);

}
function readonly_lcl(ve_control){
	var vl_record = get_num_rec_field(ve_control.id);
	var vl_cod_container = get_value('NOM_CONTAINER_'+vl_record);
	
	if(vl_cod_container == 'LCL'){
		document.getElementById('CANT_' + vl_record).value = 1;
		document.getElementById('CANT_' + vl_record).readOnly = true;
	}else{
		document.getElementById('CANT_' + vl_record).value = '';
		document.getElementById('CANT_' + vl_record).readOnly = false;
	}
}