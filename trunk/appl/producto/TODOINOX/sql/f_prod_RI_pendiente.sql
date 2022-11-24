CREATE FUNCTION [dbo].[f_prod_RI_pendiente](@ve_cod_producto	varchar(100))
RETURNS VARCHAR(100)
AS
BEGIN
declare
	@vl_numero_registro_ingreso		numeric(10,0),
	@vl_cod_item_registro_4d		numeric(10,0),
	@vl_resp						varchar(100),
	@vc_nro_registro_ingreso		numeric(10,0)

	set @vl_numero_registro_ingreso = dbo.f_prod_RI(@ve_cod_producto, 'NUMERO_REGISTRO_INGRESO')
	
		declare c_nro_ri cursor	for
		select  isnull(i.numero_registro_ingreso, 0)
		from item_registro_4d i, registro_ingreso_4d r
		where i.modelo = @ve_cod_producto
		  and i.numero_registro_ingreso > isnull(@vl_numero_registro_ingreso, 0)
	      and r.numero_registro_ingreso = i.numero_registro_ingreso
		order by fecha_registro_ingreso desc
		
		open c_nro_ri 
		fetch c_nro_ri into @vc_nro_registro_ingreso
		while @@fetch_status = 0 
		begin
			set @vl_resp = @vl_resp + CONVERT(VARCHAR, @vc_nro_registro_ingreso) + ' - '
		
			fetch c_nro_ri into @vc_nro_registro_ingreso
		end
		close c_nro_ri
		deallocate c_nro_ri
		
		return left(@vl_resp, len(@vl_resp) - 2)

END
