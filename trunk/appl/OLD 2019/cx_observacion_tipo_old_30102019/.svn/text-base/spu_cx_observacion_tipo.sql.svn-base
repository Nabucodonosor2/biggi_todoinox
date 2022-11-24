CREATE PROCEDURE spu_cx_observacion_tipo(@ve_operacion varchar(20)
										,@ve_cod_cx_observacion_tipo varchar(10)
										,@ve_nom_cx_observacion_tipo varchar(100)
										,@ve_texto varchar(2000))
AS
BEGIN
	if (@ve_operacion = 'INSERT')	
	begin
		insert into cx_observacion_tipo (cod_cx_observacion_tipo
										,nom_cx_observacion_tipo
										,texto)
		values (@ve_cod_cx_observacion_tipo
				,@ve_nom_cx_observacion_tipo
				,@ve_texto)
	end 
	if (@ve_operacion = 'UPDATE') 
	begin
		update cx_observacion_tipo 
		set nom_cx_observacion_tipo = @ve_nom_cx_observacion_tipo
			,texto = @ve_texto
	    where cod_cx_observacion_tipo = @ve_cod_cx_observacion_tipo
	end
	else if (@ve_operacion = 'DELETE') 
	begin
		delete cx_observacion_tipo 
    	where cod_cx_observacion_tipo = @ve_cod_cx_observacion_tipo
	end
END
go
