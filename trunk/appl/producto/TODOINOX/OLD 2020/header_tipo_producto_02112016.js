function dlg_tipo_producto(ve_nom_header, ve_valor_filtro) {
	var args = "location:no;dialogLeft:400px;dialogTop:320px;dialogWidth:650px;dialogHeight:300px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("TODOINOX/dlg_tipo_producto.php?nom_header="+URLEncode(ve_nom_header)+"&valor_filtro="+URLEncode(ve_valor_filtro), "_blank", args);
 	
 	if (navigator.appName=='Microsoft Internet Explorer'){
 		if (returnVal == null)
			document.getElementById('wo_header').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_header').value = returnVal;
		document.forms["output"].submit();
	   	return true;
 	}else{
 		if (returnVal == null)
			document.getElementById('wo_hidden').value = '__BORRAR_FILTRO__';
		else
			document.getElementById('wo_hidden').value = returnVal;
		document.output.submit();
	   	return true;
 	}
}
