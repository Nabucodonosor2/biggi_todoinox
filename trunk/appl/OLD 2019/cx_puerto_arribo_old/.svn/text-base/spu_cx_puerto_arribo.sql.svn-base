CREATE PROCEDURE spu_cx_puerto_arribo	(@ve_operacion varchar(20)
										,@ve_cod_cx_puerto_arribo numeric(10,0)
										,@ve_nom_cx_puerto_arribo varchar(100)
AS
BEGIN
	if (@ve_operacion = 'INSERT')	
	begin
		insert into cx_puerto_arribo (cod_cx_puerto_arribo
										,nom_cx_puerto_arribo)
		values (@ve_cod_cx_puerto_arribo
				,@ve_nom_cx_puerto_arribo)
	end 
	if (@ve_operacion = 'UPDATE') 
	begin
		update cx_puerto_arribo
		set nom_cx_puerto_arribo = @ve_nom_cx_puerto_arribo
	    where cod_cx_puerto_arribo = @ve_cod_puerto_arribo
	end
	else if (@ve_operacion = 'DELETE') 
	begin
		delete cx_puerto_arribo 
    	where cod_cx_puerto_arribo = @ve_cod_cx_puerto_arribo
	end
END
go
