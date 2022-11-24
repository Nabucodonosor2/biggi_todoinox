CREATE PROCEDURE spu_cx_puerto_salida	(@ve_operacion varchar(20)
										,@ve_cod_cx_puerto_salida numeric(10,0)
										,@ve_nom_cx_puerto_salida varchar(100))
AS
BEGIN
	if (@ve_operacion = 'INSERT')	
	begin
		insert into cx_puerto_salida (cod_cx_puerto_salida
									,nom_cx_puerto_salida)
		values (@ve_cod_cx_puerto_salida
				,@ve_nom_cx_puerto_salida)
	end 
	if (@ve_operacion = 'UPDATE') 
	begin
		update cx_puerto_salida 
		set nom_cx_puerto_salida = @ve_nom_cx_puerto_salida
	    where cod_cx_puerto_salida = @ve_cod_cx_puerto_salida
	end
	else if (@ve_operacion = 'DELETE') 
	begin
		delete cx_puerto_salida 
    	where cod_cx_puerto_salida = @ve_cod_cx_puerto_salida
	end
END
go
