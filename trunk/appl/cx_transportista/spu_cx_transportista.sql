CREATE PROCEDURE spu_cx_transportista	(@ve_operacion varchar(20)
										,@ve_cod_cx_transportista numeric(10,0)
										,@ve_nom_cx_transportista varchar(100)
										,@ve_direccion varchar(100)
										,@ve_contacto varchar(100))
AS
BEGIN
	if (@ve_operacion = 'INSERT')	
	begin
		insert into cx_transportista (cod_cx_transportista
									,nom_cx_transportista
									,direccion
									,contacto)
		values (@ve_cod_cx_transportista
				,@ve_nom_cx_transportista
				,@ve_direccion
				,@ve_contacto)
	end 
	if (@ve_operacion = 'UPDATE') 
	begin
		update cx_transportista 
		set nom_cx_transportista = @ve_nom_cx_transportista
			,direccion=@ve_direccion
			,contacto=@ve_contacto
	    where cod_cx_transportista = @ve_cod_cx_transportista
	end
	else if (@ve_operacion = 'DELETE') 
	begin
		delete cx_transportista 
    	where cod_cx_transportista = @ve_cod_cx_transportista
	end
END
go