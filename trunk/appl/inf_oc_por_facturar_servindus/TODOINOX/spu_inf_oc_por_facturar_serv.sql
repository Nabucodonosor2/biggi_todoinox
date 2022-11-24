CREATE PROCEDURE spu_inf_oc_por_facturar_serv(@ve_item numeric(10)
											,@ve_cant_fa numeric(14,4))
AS
BEGIN
	UPDATE INF_OC_POR_FACTURAR_TDNX
	SET CANT_FA			= @ve_cant_fa
		,CANT_POR_FACT	=(CANTIDAD_OC - @ve_cant_fa)
	WHERE COD_ITEM_ORDEN_COMPRA = @ve_item
END