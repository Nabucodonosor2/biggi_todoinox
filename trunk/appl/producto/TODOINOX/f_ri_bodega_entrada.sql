ALTER FUNCTION [dbo].[f_ri_bodega_entrada](@ve_nro_registro_ingreso  NUMERIC(14,2)) 
RETURNS VARCHAR(100)
AS
BEGIN
DECLARE
	@vl_count					NUMERIC(10,0),
	@vl_resp					VARCHAR(100),
	@vl_fecha_entrada_bodega	VARCHAR(100),
	@vl_cod_entrada_bodega		VARCHAR(100)

	SELECT @vl_count = COUNT(*)
	from ENTRADA_BODEGA
	where TIPO_DOC = 'REGISTRO_INGRESO'
	AND COD_DOC = @ve_nro_registro_ingreso
	
	if(@vl_count = 0)begin
		set @vl_resp = NULL
	end	
	else begin
		select @vl_cod_entrada_bodega = CONVERT(VARCHAR,COD_ENTRADA_BODEGA)
			  ,@vl_fecha_entrada_bodega = CONVERT(VARCHAR,FECHA_ENTRADA_BODEGA,103) 
		from ENTRADA_BODEGA
		where TIPO_DOC = 'REGISTRO_INGRESO'
		AND COD_DOC = @ve_nro_registro_ingreso
		
		set @vl_resp = @vl_cod_entrada_bodega + ' (' + @vl_fecha_entrada_bodega + ')'
	end						
	
	return @vl_resp
	
END
