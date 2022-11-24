alter PROCEDURE spu_cx_moneda	(@ve_operacion varchar(20)
								,@ve_cod_cx_moneda numeric(10,0)
								,@ve_nom_cx_moneda varchar(100)
								,@ve_numero_decimales numeric(10,0))
AS
BEGIN
	if (@ve_operacion='INSERT')	
	begin
		insert into cx_moneda (cod_cx_moneda
								,nom_cx_moneda
								,numero_decimales)
		values (@ve_cod_cx_moneda
				,@ve_nom_cx_moneda
				,@ve_numero_decimales)
	end 
	if (@ve_operacion='UPDATE') 
	begin
		update cx_moneda 
		set nom_cx_moneda = @ve_nom_cx_moneda
			,numero_decimales=@ve_numero_decimales
	    where cod_cx_moneda = @ve_cod_cx_moneda
	end
	else if (@ve_operacion='DELETE') 
	begin
		delete cx_moneda 
    	where cod_cx_moneda = @ve_cod_cx_moneda
	end
END
go