CREATE PROCEDURE spu_cx_titulo_detalle	(@ve_operacion varchar(20)
										,@ve_cod_cx_titulo_detalle numeric(10,0)
										,@ve_nom_cx_titulo_detalle varchar(100))
AS
BEGIN
	if (@ve_operacion='INSERT')	
	begin
		insert into cx_titulo_detalle (cod_cx_titulo_detalle
										,nom_cx_titulo_detalle)
		values (@ve_cod_cx_titulo_detalle
				,@ve_nom_cx_titulo_detalle)
	end 
	if (@ve_operacion='UPDATE') 
	begin
		update cx_titulo_detalle 
		set nom_cx_titulo_detalle = @ve_nom_cx_titulo_detalle
	    where cod_cx_titulo_detalle = @ve_cod_cx_titulo_detalle
	end
	else if (@ve_operacion='DELETE') 
	begin
		delete cx_titulo_detalle 
    	where cod_cx_titulo_detalle = @ve_cod_cx_titulo_detalle
	end
END
go
