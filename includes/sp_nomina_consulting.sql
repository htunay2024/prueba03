--Iniciar Sesión
CREATE OR ALTER PROCEDURE sp_login
    @correo NVARCHAR(255),
    @contrasena NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    DECLARE @contrasenaBD VARBINARY(64);
    DECLARE @hashedContrasena VARBINARY(64);

    -- Obtener la contraseña almacenada en la base de datos
    SELECT @contrasenaBD = contrasena
    FROM Usuario
    WHERE correo = @correo;

    -- Verificar si la contraseña en la base de datos es NULL (usuario no encontrado)
    IF @contrasenaBD IS NULL
    BEGIN
        RAISERROR('Usuario no encontrado.', 16, 1);
        RETURN;
    END

    -- Encriptar la contraseña proporcionada para compararla
    SET @hashedContrasena = HASHBYTES('SHA2_256', @contrasena);

    -- Comparar la contraseña proporcionada (encriptada) con la almacenada
    IF @hashedContrasena = @contrasenaBD
    BEGIN
        -- Si las contraseñas coinciden, devolver los detalles del usuario y su rol
        SELECT 
            u.id_usuario AS ID,
            u.correo AS email,
            e.nombres + ' ' + e.apellidos AS username,
            em.id_empresa,
            em.nombre AS empresa,
            r.nombre AS rol  -- Agregar rol del usuario
        FROM 
            Usuario u
            INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
            INNER JOIN Empresa em ON u.fk_id_empresa = em.id_empresa
            INNER JOIN Rol r ON e.fk_id_rol = r.id_rol  -- Unir con la tabla de roles
        WHERE 
            u.correo = @correo;
    END
    ELSE
    BEGIN
        -- Si las contraseñas no coinciden, devolver un error
        RAISERROR('Las credenciales no coinciden.', 16, 1);
    END
END
GO


--Usuarios

--Crear
CREATE OR ALTER PROCEDURE sp_registrar_usuario
    @correo NVARCHAR(255),
    @contrasena NVARCHAR(255),
    @fk_id_empleado INT,
    @fk_id_empresa INT
AS
BEGIN
    SET NOCOUNT ON;

    -- Encriptar la contraseña usando SHA2_256
    DECLARE @hashedContrasena VARBINARY(64);
    SET @hashedContrasena = HASHBYTES('SHA2_256', @contrasena);

    -- Insertar el nuevo usuario con la contraseña encriptada
    INSERT INTO Usuario (correo, contrasena, fk_id_empleado, fk_id_empresa)
    VALUES (@correo, @hashedContrasena, @fk_id_empleado, @fk_id_empresa);

    -- Comprobación básica para asegurarse de que la inserción fue exitosa
    IF @@ROWCOUNT = 0
    BEGIN
        RAISERROR('No se pudo registrar el usuario.', 16, 1);
    END
END
GO

--Listar
CREATE OR ALTER PROCEDURE sp_listar_usuarios
    @SearchTerm NVARCHAR(255) = NULL,
    @Offset INT = 0,
    @RowsPerPage INT = 20
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        u.id_usuario AS ID,
        u.correo AS email,
        e.nombres + ' ' + e.apellidos AS username,
        em.id_empresa,
        em.nombre AS empresa
    FROM 
        Usuario u
        INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado
        INNER JOIN Empresa em ON u.fk_id_empresa = em.id_empresa
    WHERE 
        (@SearchTerm IS NULL OR
        u.correo LIKE '%' + @SearchTerm + '%' OR
        e.nombres + ' ' + e.apellidos LIKE '%' + @SearchTerm + '%' OR
        u.id_usuario LIKE '%' + @SearchTerm + '%')
    ORDER BY 
        u.id_usuario
    OFFSET @Offset ROWS 
    FETCH NEXT @RowsPerPage ROWS ONLY;
END


--empleado

--Crear
CREATE OR ALTER PROCEDURE sp_InsertarEmpleado
    @Nombres NVARCHAR(100),
    @Apellidos NVARCHAR(100),
    @TipoContrato NVARCHAR(50),
    @Puesto NVARCHAR(100),
    @DpiPasaporte NVARCHAR(20),
    @Salario DECIMAL(8,2),
    @CarnetIgss NVARCHAR(20),
    @CarnetIrtra NVARCHAR(20),
    @FechaNacimiento DATE,
    @CorreoElectronico NVARCHAR(100),
    @Telefono NVARCHAR(20), 
    @Expediente NVARCHAR(255),
    @Fk_Id_Oficina INT,
    @Fk_Id_Profesion INT,
    @Fk_Id_Departamento INT,
    @Fk_Id_Rol INT,
    @Fk_Id_Estado INT,
    @Fk_Id_Empresa INT
AS
BEGIN
    -- Insertar el empleado en la tabla Empleado
    INSERT INTO Empleado (nombres, apellidos, fecha_contratacion, tipo_contrato, puesto, dpi_pasaporte, carnet_igss, carnet_irtra, fecha_nacimiento, correo_electronico, 
                          numero_telefono, fk_id_oficina, fk_id_profesion, fk_id_departamento, fk_id_rol, fk_id_estado, fk_id_empresa) 
    VALUES (@Nombres, @Apellidos, GETDATE(), @TipoContrato, @Puesto, @DpiPasaporte, @CarnetIgss, @CarnetIrtra, @FechaNacimiento, @CorreoElectronico, 
            @Telefono, @Fk_Id_Oficina, @Fk_Id_Profesion, @Fk_Id_Departamento, @Fk_Id_Rol, @Fk_Id_Estado, @Fk_Id_Empresa);

    -- Obtener el ID del empleado recién insertado
    DECLARE @NuevoIdEmpleado INT;
    SET @NuevoIdEmpleado = SCOPE_IDENTITY();

    -- Insertar el salario para el nuevo empleado
    INSERT INTO Salario (salario_base, salario_anterior, fk_id_empleado) 
    VALUES (@Salario, 0, @NuevoIdEmpleado);

    -- Insertar el expediente del nuevo empleado
    INSERT INTO Expediente (documento, fk_id_empleado)
    VALUES (@Expediente, @NuevoIdEmpleado);
END
GO

--Actuliazar
CREATE PROCEDURE sp_actualizar_empleado
    @id_empleado INT,
    @nombres NVARCHAR(100),
    @apellidos NVARCHAR(100),
    @tipo_contrato NVARCHAR(100),
	@puesto NVARCHAR(100),
	@dpi_pasaporte NVARCHAR(20),
	@carnet_igss NVARCHAR(20),
	@carnet_irtra NVARCHAR(20),
	@fecha_nacimiento DATE,
	@fecha_contratacion DATE,
	@correo_electronico NVARCHAR(100),
	@numero_telefono NVARCHAR(20),
	@fk_id_oficina INT,
	@fk_id_profesion INT,
    @fk_id_rol INT,
    @fk_id_estado INT
AS
BEGIN
    UPDATE Empleado
                SET nombres = @nombres, apellidos = @apellidos, 
				tipo_contrato = @tipo_contrato, 
				puesto = @puesto, dpi_pasaporte = @dpi_pasaporte, 
				carnet_igss = @carnet_igss, carnet_irtra = @carnet_irtra, 
				fecha_nacimiento =  @fecha_nacimiento,
				fecha_contratacion = @fecha_contratacion,  
				correo_electronico = @correo_electronico, numero_telefono = @numero_telefono, 
				fk_id_oficina = @fk_id_oficina, 
				fk_id_profesion = @fk_id_profesion, 
				fk_id_rol = @fk_id_rol, 
				fk_id_estado = @fk_id_estado
                WHERE id_empleado = @id_empleado
END;



-- Listar
CREATE PROCEDURE sp_listar_empleados_con_filtro
    @criterio NVARCHAR(255),
    @fk_id_rol INT 
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        E.id_empleado, 
        E.nombres, 
        E.apellidos, 
        E.fecha_contratacion, 
        E.puesto, 
        E.dpi_pasaporte, 
        E.numero_telefono, 
        E.correo_electronico, 
        P.nombre AS profesion, 
        D.nombre AS departamento
    FROM 
        Empleado E
    INNER JOIN 
        Profesion P ON E.fk_id_profesion = P.id_profesion
    INNER JOIN 
        Departamento D ON E.fk_id_departamento = D.id_departamento
    WHERE
        E.fk_id_rol = @fk_id_rol AND
        E.id_empleado LIKE '%' + @criterio + '%' 
        OR E.nombres LIKE '%' + @criterio + '%' 
        OR E.apellidos LIKE '%' + @criterio + '%' 
        OR E.puesto LIKE '%' + @criterio + '%' 
        OR E.numero_telefono LIKE '%' + @criterio + '%' 
        OR E.correo_electronico LIKE '%' + @criterio + '%' 
        OR P.nombre LIKE '%' + @criterio + '%' 
        OR D.nombre LIKE '%' + @criterio + '%';
END
GO


--Actualizar Contraeña
CREATE OR ALTER PROCEDURE sp_cambiar_contra
    @id_usuario INT,
    @nueva_contrasena NVARCHAR(255)
AS
BEGIN
    SET NOCOUNT ON;

    -- Encriptar la nueva contraseña usando SHA2_256
    DECLARE @hashedContrasena VARBINARY(64);
    SET @hashedContrasena = HASHBYTES('SHA2_256', @nueva_contrasena);

    -- Actualizar la contraseña del usuario en la tabla con el tipo de dato VARBINARY
    UPDATE Usuario
    SET contrasena = @hashedContrasena
    WHERE id_usuario = @id_usuario;
    
    -- Comprobación básica para asegurarse de que la actualización fue exitosa
    IF @@ROWCOUNT = 0
    BEGIN
        RAISERROR('No se pudo cambiar la contraseña. Usuario no encontrado.', 16, 1);
    END
END
GO

--Expediente empleado

--Listar
  CREATE PROCEDURE sp_listar_expedientes_empleados
    @criterio NVARCHAR(255),
	@fk_id_empresa INT
AS
BEGIN
    SET NOCOUNT ON;

    SELECT 
        E.id_empleado, 
        E.nombres + ' ' + E.apellidos AS [Nombre completo], 
        E.numero_telefono, 
        E.correo_electronico, 
        EX.documento

    FROM 
        Empleado E
    INNER JOIN 
        Expediente EX ON E.id_empleado = EX.fk_id_empleado
    WHERE 
	    E.fk_id_empresa = @fk_id_empresa AND 
        E.id_empleado LIKE '%' + @criterio + '%' 
        OR E.nombres LIKE '%' + @criterio + '%' 
        OR E.apellidos LIKE '%' + @criterio + '%' 
        OR E.numero_telefono LIKE '%' + @criterio + '%' 
        OR E.correo_electronico LIKE '%' + @criterio + '%';
END
GO


--Actualizar

CREATE PROCEDURE sp_update_expediente
   @expediente NVARCHAR(255),
   @id_empleado INT
AS
BEGIN
    UPDATE Expediente
	SET documento = @expediente
	WHERE fk_id_empleado = @id_empleado
END;
   
