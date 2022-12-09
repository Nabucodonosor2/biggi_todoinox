function lec_orden_compra_tdnx(ve_cod_oc_tdx, ve_sistema, ve_inventario){
	/*var args = "location:no;dialogLeft:50px;;dialogWidth:1034px;dialogHeight:570px;dialogLocation:0;Toolbar:no;";
	var returnVal = window.showModalDialog("TODOINOX/dlg_orden_compra_tdnx.php?cod_orden_compra="+ve_cod_oc_tdx+"&sistema="+ve_sistema+"&inventario="+ve_inventario, "_blank", args);*/
	var url = "TODOINOX/dlg_orden_compra_tdnx.php?cod_orden_compra="+ve_cod_oc_tdx+"&sistema="+ve_sistema+"&inventario="+ve_inventario;
	$.showModalDialog({
		 url: url,
		 dialogArguments: '',
		 height: 580,
		 width: 1038,
		 scrollable: false,
		 onClose: function(){ 
		 	var returnVal = this.returnValue;
			if(returnVal == true)
				window.open('../TODOINOX/print_oc_comercial.php?cod_orden_compra='+ve_cod_oc_tdx+"&sistema="+ve_sistema);
		}
	});
}