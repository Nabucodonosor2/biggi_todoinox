alter FUNCTION [dbo].[f_cod_tipo_doc](@ve_cod_producto	varchar(100), @ve_out_in varchar(100)) 
RETURNS VARCHAR(1000)
AS
BEGIN
DECLARE
	@vl_tipo_doc	VARCHAR(100),
	@vl_cod_doc		NUMERIC(10),
	@vl_result		VARCHAR(1000)

	if(@ve_out_in = 'ENTRADA')BEGIN
	
		SELECT top 1 @vl_tipo_doc = E.TIPO_DOC,
					 @vl_cod_doc = E.COD_DOC
		FROM ITEM_ENTRADA_BODEGA I
			 ,ENTRADA_BODEGA E			
		WHERE I.COD_PRODUCTO = @ve_cod_producto	
		AND E.COD_ENTRADA_BODEGA = I.COD_ENTRADA_BODEGA
		order by FECHA_ENTRADA_BODEGA desc
		
		if(@vl_tipo_doc = 'A' or @vl_tipo_doc = 'AJUSTE')
			set @vl_tipo_doc = 'Ajuste Bodega'
		else if(@vl_tipo_doc = 'F')
			set @vl_tipo_doc = 'Factura 4D'
		else if(@vl_tipo_doc = 'FACTURA')
			set @vl_tipo_doc = 'Factura Electrónica'
		else if(@vl_tipo_doc = 'GD')
			set @vl_tipo_doc = 'Guía Despacho 4D'
		else if(@vl_tipo_doc = 'GR')
			set @vl_tipo_doc = 'Guía recepción'
		else if(@vl_tipo_doc = 'MEMO')
			set @vl_tipo_doc = 'Memo'
		else if(@vl_tipo_doc = 'N/C' or @vl_tipo_doc = 'NC')
			set @vl_tipo_doc = 'Nota Crédito 4D'
		else if(@vl_tipo_doc = 'NOTA_CREDITO')
			set @vl_tipo_doc = 'Nota Crédito Electrónica'	
		else if(@vl_tipo_doc = 'OC')
			set @vl_tipo_doc = 'Orden Compra'	
		else if(@vl_tipo_doc = 'REGISTRO_INGRESO')
			set @vl_tipo_doc = 'Registro Ingreso'	
		else
			set @vl_tipo_doc = 'Documento'
			
		if(@vl_tipo_doc = 'Ajuste Bodega')
			set @vl_result = 'según ' + @vl_tipo_doc + '.'
		else
			set @vl_result = 'según ' + @vl_tipo_doc + ', N° ' + CONVERT(VARCHAR,dbo.f_get_nro_doc(@vl_tipo_doc, @vl_cod_doc))											
	END
	ELSE IF(@ve_out_in = 'SALIDA')BEGIN
	
		SELECT top 1 @vl_tipo_doc = E.TIPO_DOC,
			   @vl_cod_doc = E.COD_DOC
		FROM ITEM_SALIDA_BODEGA I
			 ,SALIDA_BODEGA E			
		WHERE I.COD_PRODUCTO = @ve_cod_producto	
		AND E.COD_SALIDA_BODEGA = I.COD_SALIDA_BODEGA
		order by FECHA_SALIDA_BODEGA desc
		
		if(@vl_tipo_doc = 'A' or @vl_tipo_doc = 'AJUSTE')
			set @vl_tipo_doc = 'Ajuste Bodega'
		else if(@vl_tipo_doc = 'F')
			set @vl_tipo_doc = 'Factura 4D'
		else if(@vl_tipo_doc = 'FACTURA')
			set @vl_tipo_doc = 'Factura Electrónica'
		else if(@vl_tipo_doc = 'GD')
			set @vl_tipo_doc = 'Guía Despacho 4D'
		else if(@vl_tipo_doc = 'GR')
			set @vl_tipo_doc = 'Guía recepción'
		else if(@vl_tipo_doc = 'MEMO')
			set @vl_tipo_doc = 'Memo'
		else if(@vl_tipo_doc = 'N/C' or @vl_tipo_doc = 'NC')
			set @vl_tipo_doc = 'Nota Crédito 4D'
		else if(@vl_tipo_doc = 'NOTA_CREDITO')
			set @vl_tipo_doc = 'Nota Crédito Electrónica'	
		else if(@vl_tipo_doc = 'OC')
			set @vl_tipo_doc = 'Orden Compra'	
		else if(@vl_tipo_doc = 'REGISTRO_INGRESO')
			set @vl_tipo_doc = 'Registro Ingreso'	
		else
			set @vl_tipo_doc = 'Documento'
			
		if(@vl_tipo_doc = 'Ajuste Bodega')
			set @vl_result = 'según ' + @vl_tipo_doc + '.'
		else
			set @vl_result = 'según ' + @vl_tipo_doc + ', N° ' + CONVERT(VARCHAR,dbo.f_get_nro_doc(@vl_tipo_doc, @vl_cod_doc))											
	END
	
	RETURN @vl_result
END
