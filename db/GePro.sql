CREATE DATABASE IF NOT EXISTS gepro;
USE gepro;

-- TABLA DE USUARIOS
CREATE TABLE usuarios (
    usuario VARCHAR(50) PRIMARY KEY NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL CHECK (LENGTH(pass) >= 60), -- Evita contraseñas vacías
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    tipo TINYINT NOT NULL CHECK (tipo IN (0,1,2))
);

-- TABLA DE GRUPOS
CREATE TABLE grupos (
    id_grupo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- RELACIÓN USUARIOS - GRUPOS
CREATE TABLE usuarios_grupos (
    usuario VARCHAR(50) NOT NULL,
    id_grupo INT NOT NULL,
    FOREIGN KEY (usuario) REFERENCES usuarios(usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo) ON DELETE CASCADE,
    PRIMARY KEY (usuario, id_grupo)
);

-- TABLA DE PROYECTOS
CREATE TABLE proyectos (
    id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE CHECK (fecha_fin IS NULL OR fecha_fin > fecha_inicio), -- Evita fechas inválidas
    estado ENUM('pendiente', 'en progreso', 'completado') NOT NULL
);

-- TABLA DE TAREAS
CREATE TABLE tareas (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_proyecto INT NOT NULL,
    usuario VARCHAR(50) NOT NULL,
    fecha_asignacion DATE NOT NULL,
    fecha_vencimiento DATE CHECK (fecha_vencimiento IS NULL OR fecha_vencimiento > fecha_asignacion), -- Evita fechas inválidas
    estado ENUM('pendiente', 'en progreso', 'completada') NOT NULL,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (usuario) REFERENCES usuarios(usuario) ON DELETE CASCADE
);

-- TABLA DE REUNIONES
CREATE TABLE reuniones (
    id_reunion INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    id_proyecto INT NOT NULL,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE
);

-- INSERTAR USUARIOS
INSERT INTO usuarios (usuario, pass, nombre, apellido, dni, email, telefono, tipo) VALUES
('admin', '$2y$10$HOSftQSI0gMU/1IKkpV6buCjdux1n1u68EfKAy2aYDN9ayG97NVQe', 'Admin', 'Principal', '12345678A', 'admin@gepro.com', '600000001', 0),
('jefe', '$2y$10$2LiPuaPNAZRfwBpHSHO/MOQGrvRXola06XyY016ty0SS3nPPiN3hm', 'Jefe', 'Equipo', '87654321B', 'jefe@gepro.com', '600000002', 1),
('empleado', '$2y$10$Wgce0JvarR3fQBNjKNcdJ.OEvjkPfnlJeSrhjpGccnB9f0o5icBvi', 'Empleado', 'Basico', '11223344C', 'empleado@gepro.com', '600000003', 2);

-- INSERTAR PROYECTOS
INSERT INTO proyectos (nombre, descripcion, fecha_inicio, estado) VALUES
('Proyecto Alpha', 'Desarrollo de una nueva plataforma web', '2024-03-01', 'en progreso'),
('Proyecto Beta', 'Implementación de mejoras en la aplicación móvil', '2024-02-15', 'pendiente');

-- INSERTAR TAREAS ( en el campo usuarios se usan nombres de usuario en vez de IDs )
INSERT INTO tareas (nombre, descripcion, id_proyecto, usuario, fecha_asignacion, estado) VALUES
('Diseño UI', 'Crear el diseño de la interfaz de usuario', 1, 'empleado', '2024-03-01', 'en progreso'),
('Desarrollo Backend', 'Programar la lógica del servidor', 1, 'jefe', '2024-03-01', 'pendiente');
