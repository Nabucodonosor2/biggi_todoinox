function dlg_print(){
	var url = "dlg_print_oc_extranjera.php";
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 250,
			 width: 490,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			 	if (returnVal == null){		
					return false;
				}			
				else {
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "b_print_x");
					input.setAttribute("id", "b_print_x");
					document.getElementById("input").appendChild(input);
					
					document.getElementById('wi_hidden').value = returnVal;
					document.input.submit();
			   		return true;
				}
			}
		});	
}

function subtotal(){
 var aTR = get_TR('CX_ITEM_OC_EXTRANJERA');
	vl_total_total = 0;
	for (i=0; i < aTR.length; i++) {
		var precio = document.getElementById('PRECIO_' + get_num_rec_field(aTR[i].id)).value;
		precio = precio.replace(",",".");
		if(precio == '0,00'){
			return false;
		}
		var cantidad = document.getElementById('CANTIDAD_' + get_num_rec_field(aTR[i].id)).value;
		vl_cant_precio = parseFloat(precio) * parseFloat(cantidad);
		vl_total_total = parseFloat(vl_cant_precio) + parseFloat(vl_total_total);
	}
	document.getElementById('SUBTOTAL_H_0').value = vl_total_total;
	vl_total_total = number_format(vl_total_total, 2, ',', '.');
	
	document.getElementById('SUBTOTAL_0').innerHTML = vl_total_total;
	monto_total();
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
	subtotal();
}

function monto_total(){
	var vl_subtotal			= document.getElementById('SUBTOTAL_H_0').value;
	var vl_flete_interno	= document.getElementById('MONTO_FLETE_INTERNO_0').value;
	var vl_embalaje			= document.getElementById('MONTO_EMBALAJE_0').value;
	var vl_descuento		= document.getElementById('MONTO_DESCUENTO_0').value;
	var vl_monto_total		= 0;
	
	vl_subtotal			= vl_subtotal.replace(",",".");
	vl_flete_interno	= vl_flete_interno.replace(",",".");
	vl_embalaje			= vl_embalaje.replace(",",".");
	vl_descuento		= vl_descuento.replace(",",".");
	
	vl_monto_total = (parseFloat(vl_subtotal) + parseFloat(vl_flete_interno) + parseFloat(vl_embalaje)) - vl_descuento;
	document.getElementById('MONTO_TOTAL_H_0').value = vl_monto_total;
	vl_monto_total = number_format(vl_monto_total, 2, ',', '.');
	document.getElementById('MONTO_TOTAL_0').innerHTML = vl_monto_total;
}

function add_line_item(tabla_id, nom_tabla) {
	var row = add_line(tabla_id, nom_tabla);
	var aTR = get_TR('CX_ITEM_OC_EXTRANJERA');
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
	vl_ajax.open("GET", "ajax_display_equipo_oc_ex.php?cod_producto="+cod_producto, false);
	vl_ajax.send(null);		
	var vl_resp = URLDecode(vl_ajax.responseText);
	var vl_result = eval("(" + vl_resp + ")");
	
	document.getElementById('COD_EQUIPO_OC_EX_' + record).innerHTML = vl_result[0]['COD_EQUIPO_OC_EX'];
	document.getElementById('DESC_EQUIPO_OC_EX_' + record).innerHTML = vl_result[0]['DESC_EQUIPO_OC_EX'];
	
	document.getElementById('COD_EQUIPO_OC_EX_H_' + record).value = vl_result[0]['COD_EQUIPO_OC_EX'];
	document.getElementById('DESC_EQUIPO_OC_EX_H_' + record).value = vl_result[0]['DESC_EQUIPO_OC_EX'];
}


function dlg_crea_desde_oc(){
	var url = "dlg_crea_desde_oc.php";
		$.showModalDialog({
			 url: url,
			 dialogArguments: '',
			 height: 210,
			 width: 560,
			 scrollable: false,
			 onClose: function(){ 
			 	var returnVal = this.returnValue;
			 	if (returnVal == null){		
					return false;
				}			
				else {
					var input = document.createElement("input");
					input.setAttribute("type", "hidden");
					input.setAttribute("name", "b_create_x");
					input.setAttribute("id", "b_create_x");
					document.getElementById("output").appendChild(input);
					
					document.getElementById('wo_hidden').value = returnVal;
					document.output.submit();
			   		return true;
				}
			}
		});		
}

function dlg_agrega_cx_carta_op(ve_cod_cx_carta_op, ve_estado){
	const vl_cx_oc_extranjera	= get_value('COD_CX_OC_EXTRANJERA_0');
	const url = "dlg_agrega_cx_carta_op.php?cx_co_extranjera="+vl_cx_oc_extranjera+"&cod_cx_carta_op="+ve_cod_cx_carta_op+"&estado="+ve_estado;
	$.showModalDialog({
		url: url,
		dialogArguments: '',
		height: 280,
		width: 470,
		scrollable: false,
		onClose: function(){ 
			const returnVal = this.returnValue;
			if(returnVal == null){		
				return false;
			}else{
				document.getElementById('b_no_save').click();
				return true;
			}
		}
	});			
}

function selecciona(){
	vl_tabla= document.getElementById("CORRELATIVE").rows.length;
	cod_mod_arriendo = "";
		for (var i=0; i < vl_tabla; i++) {
			
			if(document.getElementById("SELECCION_"+i).checked)
			{
				code =document.getElementById("COD_CX_COT_EXTRANJERA"+i).innerHTML;
			}
		}
		return code;
		
	} 
function Correlative_Quote(){
	
	var correlative = document.getElementById('ORDEN_COMPRA_0').value;
	var ajax = nuevoAjax();
	var php = "../cx_oc_extranjera/ajax_correlative_quote.php?correlative="+correlative;
	ajax.open("GET", php, false);
	ajax.send(null);
    var vl_resp = URLDecode(ajax.responseText);

    if(vl_resp==''){
    	alert("La Cotización N° "+correlative+" no existe");
    }else{
	    var vl_result = eval("(" + vl_resp + ")");
	        
		var vl_table = document.getElementById("CORRELATIVE");
		var vl_tr = document.getElementById("DW_TR_ID");
		var vl_tabla_x = vl_table.rows.length;
		 for(var i= vl_tabla_x -1; i>=0; i --){
		   vl_table.deleteRow(vl_tr);
	  	}
	   
	   var vl_tabla = document.getElementById('CORRELATIVE');
		for (var i=0; i < vl_result.length; i++) {
			var vl_tr = document.createElement("tr");
			vl_tr.className="claro";
			vl_tr.setAttribute("id","TR_"+i);
			
			var check_box = document.createElement('INPUT');
			check_box.setAttribute("type","radio");
			check_box.setAttribute("value","valor_checkbox");
			check_box.setAttribute("id","SELECCION_"+i);
			check_box.setAttribute("name","SELECCIONA");
			
			
			var vl_td_check = document.createElement("td");
			vl_td_check.width = "10%";
			vl_td_check.align = "center";
			vl_td_check.innerHTML = ''; 
			vl_tr.appendChild(vl_td_check); 
			vl_td_check.appendChild(check_box);
	
			var vl_td = document.createElement("td");
			vl_td.width = "5%";
			vl_td.align = "center";
			vl_td.setAttribute("id","COD_CX_COT_EXTRANJERA"+i);
			vl_td.innerHTML = vl_result[i]['COD_CX_COT_EXTRANJERA'];
			vl_tr.appendChild(vl_td); 
			
			var vl_td = document.createElement("td");
			vl_td.width = "20%";
			vl_td.align = "center";
			vl_td.innerHTML = vl_result[i]['ALIAS_PROVEEDOR_EXT'];
			vl_tr.appendChild(vl_td); 
			vl_tabla.appendChild(vl_tr); 
			
			var vl_td = document.createElement("td");
			vl_td.width = "40%";
			vl_td.align = "center";
			vl_td.innerHTML = vl_result[i]['NOM_PROVEEDOR_EXT'];
			vl_tr.appendChild(vl_td); 
			vl_tabla.appendChild(vl_tr); 
			
			var vl_td = document.createElement("td");
			vl_td.width = "10%";
			vl_td.align = "center";
			vl_td.innerHTML = vl_result[i]['NOM_CX_CLAUSULA_COMPRA'];
			vl_tr.appendChild(vl_td); 
			vl_tabla.appendChild(vl_tr); 
			
			var vl_td = document.createElement("td");
			vl_td.width = "15%";
			vl_td.align = "center";
			vl_td.innerHTML = vl_result[i]['MONTO_TOTAL'];
			vl_tr.appendChild(vl_td); 
			vl_tabla.appendChild(vl_tr); 
			
		}
		document.getElementById('CORRELATIVE_DISPLAY').style.display = '';
		document.getElementById('DESDE_QUOTE_DISPLAY').style.display = 'none';
    }
	
	
}
function del_line(ve_tr_id, ve_nom_mantenedor) {
	var vl_record = get_num_rec_field(ve_tr_id);
	if(document.getElementById('COD_ESTADO_CX_CARTA_OP_'+vl_record)){
		var vl_cod_est_cx_carta	= document.getElementById('COD_ESTADO_CX_CARTA_OP_'+vl_record).value;
		
		if(vl_cod_est_cx_carta == 1) //Emitida
			del_line_standard(ve_tr_id, ve_nom_mantenedor);
		else if(vl_cod_est_cx_carta == 2)//Anulada	
			alert('No se puede eliminar, esta anulada');
		else
			alert('No se puede eliminar, tiene que anularlo');
	}else{
		del_line_standard(ve_tr_id, ve_nom_mantenedor);
	}
}

function anula_carta_op(){
	var aTR = get_TR('CX_ORDEN_PAGO');
	var vl_str_cod_carta_op = "";
	for (i=0; i < aTR.length; i++){
		var vl_record = get_num_rec_field(aTR[i].id);
		var vl_cx_carta_op = document.getElementById('COD_CX_CARTA_OP_' + vl_record).value;
		var vl_anula = document.getElementById('ANULAR_CX_ORDEN_PAGO_' + vl_record).checked;
		
		if(vl_anula)
			vl_str_cod_carta_op = vl_str_cod_carta_op + vl_cx_carta_op + ",";
	}
	
	if(vl_str_cod_carta_op == ''){
		alert('Debe marcar al menos 1 item como anulada.');
		return;
	}

	vl_str_cod_carta_op = vl_str_cod_carta_op.substring(0, vl_str_cod_carta_op.length-1);
	
	if(confirm("Esta seguro de anular estas cartas op?")){
		var vl_ajax = nuevoAjax();
		vl_ajax.open("GET", "anula_carta_op.php?str_cod_carta_op="+vl_str_cod_carta_op, false);
		vl_ajax.send(null);		
		var vl_resp = vl_ajax.responseText;
		
		document.getElementById('b_no_save').click();
	}
}

function calculaCorrelativo(){
	var cod_empresa = $("#COD_PROVEEDOR_EXT_0").val();
	if(cod_empresa != ''){
		parametros = {                   
				"cod_empresa" : cod_empresa
			};
		
		url = "ajaxObtieneCorrelativo.php";
		$.ajax({           
			url: url,
			data: parametros, 
	        
			success: function(data)             
			{
				$("#CORRELATIVO_OC_0").val(data);
			}
		});
	}
}

function set_values_empresa(valores, record) {
	set_value('COD_PROVEEDOR_EXT_' + record, valores[1], valores[1]);
	set_value('ALIAS_PROVEEDOR_EXT_' + record, valores[2], valores[2]);
	set_value('NOM_PROVEEDOR_EXT_' + record, valores[3], valores[3]);
	set_value('NOM_CIUDAD_' + record, valores[4], valores[4]);
	set_value('NOM_PAIS_' + record, valores[5], valores[5]);
	set_value('TELEFONO_' + record, valores[6], valores[6]);
	set_value('DIRECCION_' + record, valores[9], valores[9]);
	set_value('POST_OFFICE_BOX_' + record, valores[10], valores[10]);
	set_drop_down('COD_CX_CONTACTO_PROVEEDOR_EXT_' + record, valores[11]);
	calculaCorrelativo();
}
