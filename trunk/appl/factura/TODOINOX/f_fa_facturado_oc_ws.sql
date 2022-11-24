--------------------- f_fa_facturado_oc_ws --------------
alter FUNCTION f_fa_facturado_oc_ws(@ve_cod_item_orden_compra	numeric
										,@ve_origen					varchar(100))
RETURNS numeric(10,2)
AS
BEGIN
	declare
		@vl_tipo_item		varchar(100)
		,@vl_cant_fa		numeric(10,2)
		,@vl_cant_nc		numeric(10,2)
		
	set @vl_tipo_item = 'ITEM_ORDEN_COMPRA_' + @ve_origen

	SELECT @vl_cant_fa = isnull(SUM(i.CANTIDAD) , 0)
			, @vl_cant_nc = isnull(SUM(inc.cantidad), 0) 
    FROM ITEM_FACTURA i left outer join ITEM_NOTA_CREDITO inc on inc.COD_ITEM_DOC = i.COD_ITEM_FACTURA and inc.TIPO_DOC = 'ITEM_FACTURA'
										and inc.COD_NOTA_CREDITO not in (SELECT COD_NOTA_CREDITO 
																	     FROM NOTA_CREDITO
																	     WHERE COD_NOTA_CREDITO = inc.COD_NOTA_CREDITO
																	     AND COD_MOTIVO_NOTA_CREDITO = 1)
						left outer join NOTA_CREDITO nc on nc.COD_NOTA_CREDITO = inc.COD_NOTA_CREDITO and nc.COD_ESTADO_DOC_SII in (1,2,3)
		, FACTURA f
    WHERE i.COD_ITEM_DOC = @ve_cod_item_orden_compra
      and i.TIPO_DOC = @vl_tipo_item
      and f.cod_factura = i.cod_factura
	  and f.COD_ESTADO_DOC_SII in (1,2,3)
	
	return @vl_cant_fa - @vl_cant_nc		
END
