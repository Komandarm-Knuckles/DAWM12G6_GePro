CREATE DATABASE IF NOT EXISTS gepro;
USE gepro;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(50) NOT NULL,
    dni VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    tipo TINYINT NOT NULL CHECK (tipo IN (0,1,2))
);

CREATE TABLE grupos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE usuarios_grupos (
    id_usuario INT NOT NULL,
    id_grupo INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_grupo) REFERENCES grupos(id) ON DELETE CASCADE,
    PRIMARY KEY (id_usuario, id_grupo)
);

CREATE TABLE proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE,
    estado ENUM('pendiente', 'en progreso', 'completado') NOT NULL
);

CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    id_proyecto INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_asignacion DATE NOT NULL,
    fecha_vencimiento DATE,
    estado ENUM('pendiente', 'en progreso', 'completada') NOT NULL,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE reuniones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    id_proyecto INT NOT NULL,
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id) ON DELETE CASCADE
);

INSERT INTO usuarios (usuario, pass, nombre, apellido, dni, email, telefono, tipo) VALUES
('admin', 'admin123', 'Admin', 'Principal', '12345678A', 'admin@gepro.com', '600000001', 0),
('jefe', 'jefe123', 'Jefe', 'Equipo', '87654321B', 'jefe@gepro.com', '600000002', 1),
('empleado', 'empleado123', 'Empleado', 'Basico', '11223344C', 'empleado@gepro.com', '600000003', 2);

INSERT INTO proyectos (nombre, descripcion, fecha_inicio, estado) VALUES
('Proyecto Alpha', 'Desarrollo de una nueva plataforma web', '2024-03-01', 'en progreso'),
('Proyecto Beta', 'Implementación de mejoras en la aplicación móvil', '2024-02-15', 'pendiente');

INSERT INTO tareas (nombre, descripcion, id_proyecto, id_usuario, fecha_asignacion, estado) VALUES
('Diseño UI', 'Crear el diseño de la interfaz de usuario', 1, 2, '2024-03-01', 'en progreso'),
('Desarrollo Backend', 'Programar la lógica del servidor', 1, 1, '2024-03-01', 'pendiente');

