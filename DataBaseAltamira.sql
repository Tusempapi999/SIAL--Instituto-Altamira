DROP DATABASE IF EXISTS Altamira; -- si existe una base de datos con el mismo nombre la eleminamos
CREATE DATABASE Altamira;
USE Altamira;

CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'alumno', 'profesor') NOT NULL DEFAULT 'alumno',
    fecha_nacimiento DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE
);
-- La tabla anterior es la tabla del usuario, en la primera linea encontramos el campo id
-- que se incrementa automáticamente con AUTO_INCREMENT, este campo también es la clave primaria
-- seguido esta el email que con UNIQUE decimos que es unico, no se puede repertir dos correos en la BD
-- pwd (password) es la contraseña la cual tampoco puede ser nula (NOT NULL)
-- en rol la palabra ENUM nos permite definir una lista de valores esclusivos para este campo
-- es decir que solo puede tomar los valores 'admin', 'alumno' o 'profesor' y asigna alumno por defecto
-- este campo podemos retornar su valor como string o como numero.

CREATE TABLE alumno (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL, 
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE profesor (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    especialidad TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
);

CREATE TABLE justificante (
    id INT PRIMARY KEY AUTO_INCREMENT,
    alumno_id INT NOT NULL,
    fecha DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    motivo TEXT NOT NULL,
    estado ENUM('pendiente', 'aceptado', 'rechazado') DEFAULT 'pendiente',
    FOREIGN KEY (alumno_id) REFERENCES alumno(id) ON DELETE CASCADE
);

CREATE TABLE aula (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE asignatura (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

-- 2. NUEVA TABLA: Grupo (Reemplaza a 'imparte' y absorbe grado/grupo)
-- Esta tabla representa la clase, ejemplo


-- id    aignatura_id   profesor_id   grado  letra_grupo

--  1          3           4            6        ´B´ 
--  2          6           5            6        ´B´ 
--  3          1           4            6        ´B´ 


CREATE TABLE grupo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    asignatura_id INT NOT NULL,
    profesor_id INT NOT NULL,
    grado INT NOT NULL,
    letra_grupo CHAR(1) NOT NULL, -- Ej: 'A', 'B'
    FOREIGN KEY (asignatura_id) REFERENCES asignatura(id) ON DELETE CASCADE,
    FOREIGN KEY (profesor_id) REFERENCES profesor(id) ON DELETE CASCADE
);

-- 3. Matriculado ahora une al alumno con el GRUPO (no con la asignatura suelta)
-- De matriculado va ha haber tantos registros como materias que haya en el grupo

-- id     alumno_id     grupo_id     calificacion_1  calificacion_2  calificacion_3

--  1          1           1               6.4             10.0            9.0
--  2          1           2               9.9             10.0            10.0
--  3          1           3               9.9             9.0             10.0 

--  4          2           1               6.4             10.0            9.0
--  5          2           2               9.9             10.0            10.0
--  6          2           3               9.9             9.0             10.0         

--  7          3           1                0               0                0
--  8          3           2                0               0                0
--  9          3           3                0               0                0         


-- Sentencia para ingresar calificacion de un alumno (Usuario profesor)
-- UPDATE matriculado INNER JOIN alumno ON matriculado.alumno_id = alumno.id INNER JOIN grupo ON matriculado.grupo_id = grupo.id SET matriculado.calificacion_1 = 9.5 WHERE alumno.id = '1' AND grupo.id = '1';

CREATE TABLE matriculado (
    id INT PRIMARY KEY AUTO_INCREMENT,
    alumno_id INT NOT NULL,
    grupo_id INT NOT NULL, 
    calificacion_1 DECIMAL (3,1) DEFAULT 0,
    calificacion_2 DECIMAL (3,1) DEFAULT 0,
    calificacion_3 DECIMAL (3,1) DEFAULT 0,
    promedio_final DECIMAL(3,1) GENERATED ALWAYS AS ((calificacion_1 + calificacion_2 + calificacion_3) / 3) STORED,
    FOREIGN KEY (alumno_id) REFERENCES alumno(id) ON DELETE CASCADE,
    FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE,
    UNIQUE (alumno_id, grupo_id) 
);


-- 4. Horario se asigna al GRUPO, no al alumno matriculado
CREATE TABLE horario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    grupo_id INT NOT NULL, -- El horario es para todo el grupo
    aula_id INT NOT NULL,
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes') NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    FOREIGN KEY (grupo_id) REFERENCES grupo(id) ON DELETE CASCADE,
    FOREIGN KEY (aula_id) REFERENCES aula(id) ON DELETE CASCADE
);

-- Tabla asistencia

-- id    matriculado_id   fecha      estado   

-- 1          1         2024-09-01   presente       
-- 2          1         2024-09-02   sin justificar
-- 3          1         2024-09-03   presente

-- 4          2         2024-09-01   presente
-- 5          2         2024-09-02   justificado
-- 6          2         2024-09-03   presente

CREATE TABLE asistencia (
    id INT PRIMARY KEY AUTO_INCREMENT,
    matriculado_id INT NOT NULL, -- Esto ya vincula al alumno con la materia/grupo exacto
    fecha DATE NOT NULL,
    estado ENUM('presente', 'sin justificar', 'justificado', 'retardo') NOT NULL DEFAULT 'presente',
    FOREIGN KEY (matriculado_id) REFERENCES matriculado(id) ON DELETE CASCADE,
    UNIQUE (matriculado_id, fecha) -- ¡Clave! Esto evita pasarle lista dos veces al mismo alumno el mismo día
);

-- 1. Catálogo de los clubes disponibles
CREATE TABLE club (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    profesor_id INT, -- Opcional, si un profe lo coordina
    aula_id INT,     -- Opcional, si tienen un lugar fijo
    hora_inicio TIME,
    hora_fin TIME,
    cupo_maximo INT,
    FOREIGN KEY (profesor_id) REFERENCES profesor(id) ON DELETE SET NULL,
    FOREIGN KEY (aula_id) REFERENCES aula(id) ON DELETE SET NULL
);

-- 2. Inscripción al club
CREATE TABLE inscripcion_club (
    alumno_id INT PRIMARY KEY, -- ¡MAGIA AQUÍ! Al ser Primary Key, el alumno NO puede aparecer dos veces. Solo un club.
    club_id INT NOT NULL,
    fecha_inscripcion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (alumno_id) REFERENCES alumno(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES club(id) ON DELETE CASCADE
);

CREATE TABLE asistencia_club (
    id INT PRIMARY KEY AUTO_INCREMENT,
    alumno_id INT NOT NULL, -- Nos vincula directamente con su única inscripción al club
    fecha DATE NOT NULL,
    estado ENUM('presente', 'sin justificar', 'justificado', 'retardo') NOT NULL DEFAULT 'presente',
    FOREIGN KEY (alumno_id) REFERENCES inscripcion_club(alumno_id) ON DELETE CASCADE,
    UNIQUE (alumno_id, fecha) -- Evita pasar lista dos veces al mismo alumno en la misma fecha
);

DELIMITER //

CREATE TRIGGER insertar_alumno AFTER INSERT ON usuario
FOR EACH ROW
BEGIN
    IF NEW.rol = 'alumno' THEN
        INSERT INTO alumno (usuario_id) VALUES (NEW.id);
    END IF;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER insertar_profesor AFTER INSERT ON usuario
FOR EACH ROW
BEGIN
    IF NEW.rol = 'profesor' THEN
        INSERT INTO profesor (usuario_id) VALUES (NEW.id);
    END IF;
END; //

DELIMITER ;

DELIMITER //

CREATE TRIGGER insertar_admin AFTER INSERT ON usuario
FOR EACH ROW
BEGIN
    IF NEW.rol = 'admin' THEN
        INSERT INTO admin (usuario_id) VALUES (NEW.id);
    END IF;
END; //

DELIMITER ;

-- Sentencia insert para crear un usuario admin por defecto
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Admin', 'admin@altamira.com', 'admin', 'admin', '2008-11-26');
-- Sentencia insert para crear un usuario alumno por defecto
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Alumno', 'alumno@altamira.com', 'alumno', 'alumno', '2007-08-17');

-- 1. Usuario profesor
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Carlos Mendoza', 'carlos@altamira.com', 'profesor', 'profesor', '1985-03-10');
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Maria Lopez', 'maria@altamira.com', 'alumno', 'alumno', '2007-05-20');

-- 3 alumnos mas en usuario
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Juan Perez', 'juan@altamira.com', 'alumno', 'alumno', '2007-03-15');
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Luis Torres', 'luis@altamira.com', 'alumno', 'alumno', '2007-06-22');
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Ana Ramirez', 'ana@altamira.com', 'alumno', 'alumno', '2007-09-10');

-- 3 profesores mas en usuario
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Pedro Gutierrez', 'pedro@altamira.com', 'profesor', 'profesor', '1980-04-12');
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Laura Sanchez', 'laura@altamira.com', 'profesor', 'profesor', '1982-07-18');
INSERT INTO usuario (nombre, email, pwd, rol, fecha_nacimiento) VALUES ('Roberto Diaz', 'roberto@altamira.com', 'profesor', 'profesor', '1979-11-05');

-- Insertar Aulas
INSERT INTO aula (nombre) VALUES ('Aula 101'), ('Laboratorio de Cómputo'), ('Cancha Techada'), ('Taller de Arte');

-- Insertar Asignaturas
INSERT INTO asignatura (nombre, descripcion) VALUES 
('Matemáticas VI', 'Cálculo diferencial e integral'),
('Historia Universal', 'Desde la Revolución Industrial hasta la actualidad'),
('Programación Web', 'Desarrollo frontend y backend con bases de datos'),
('Física II', 'Electricidad y Magnetismo');

-- Carlos Mendoza (ID de profesor 1)
UPDATE profesor SET especialidad = 'Matemáticas Avanzadas y Cálculo' WHERE id = 1;

-- Pedro Gutierrez (ID de profesor 2)
UPDATE profesor SET especialidad = 'Ingeniería de Software y Bases de Datos' WHERE id = 2;

-- Laura Sanchez (ID de profesor 3)
UPDATE profesor SET especialidad = 'Ciencias Sociales e Historia Contemporánea' WHERE id = 3;

-- Roberto Diaz (ID de profesor 4)
UPDATE profesor SET especialidad = 'Física Cuántica y Termodinámica' WHERE id = 4;

-- Creamos grupos para 6to Grado, Grupo 'A' y 'B'
INSERT INTO grupo (asignatura_id, profesor_id, grado, letra_grupo) VALUES 
(1, 1, 6, 'A'), -- Matemáticas con Carlos Mendoza
(3, 2, 6, 'A'), -- Programación con Pedro Gutierrez
(2, 3, 6, 'B'), -- Historia con Laura Sanchez
(4, 4, 6, 'B'); -- Física con Roberto Diaz

-- Matricular al "Alumno" (ID 1) en Matemáticas y Programación
INSERT INTO matriculado (alumno_id, grupo_id, calificacion_1, calificacion_2, calificacion_3) VALUES 
(1, 1, 8.5, 9.0, 10.0), 
(1, 2, 7.0, 8.0, 9.5);

-- Matricular a "Maria Lopez" (ID 2) en Matemáticas e Historia
INSERT INTO matriculado (alumno_id, grupo_id, calificacion_1, calificacion_2, calificacion_3) VALUES 
(2, 1, 9.0, 10.0, 10.0),
(2, 3, 8.0, 8.5, 9.0);

-- Matricular a "Juan Perez" (ID 3) en todas las materias del Grado 6-A
INSERT INTO matriculado (alumno_id, grupo_id, calificacion_1, calificacion_2, calificacion_3) VALUES 
(3, 1, 6.0, 7.5, 8.0),
(3, 2, 10.0, 10.0, 9.0);

-- Matricular a "Luis Torres" (ID 4) en las materias del Grado 6-B
INSERT INTO matriculado (alumno_id, grupo_id, calificacion_1, calificacion_2, calificacion_3) VALUES 
(4, 3, 7.5, 7.0, 8.5),
(4, 4, 9.0, 8.0, 7.0);

-- Tomar asistencia para el "Alumno" (ID 1) en Matemáticas (Grupo 1)
INSERT INTO asistencia (matriculado_id, fecha, estado) VALUES (1, '2026-05-15', 'sin justificar');

-- Mostrar todas las asistencias, faltas o justificadas
-- SELECT asistencia.fecha, asistencia.estado FROM asistencia INNER JOIN matriculado ON asistencia.matriculado_id = matriculado.id INNER JOIN grupo ON matriculado.grupo_id = grupo.id WHERE grupo.asignatura_id = 1 AND matriculado.alumno_id = 1;

-- Sentencias listar faltas
SELECT asistencia.fecha, asistencia.estado FROM asistencia 
INNER JOIN matriculado ON asistencia.matriculado_id = matriculado.id
INNER JOIN grupo ON matriculado.grupo_id = grupo.id
WHERE grupo.asignatura_id = 1 AND matriculado.alumno_id = 1 AND asistencia.estado = 'sin justificar';


-- Sentencias contar faltas
SELECT COUNT(*) as cantidad FROM asistencia 
INNER JOIN matriculado ON asistencia.matriculado_id = matriculado.id
INNER JOIN grupo ON matriculado.grupo_id = grupo.id
WHERE grupo.asignatura_id = 1 AND matriculado.alumno_id = 1 AND asistencia.estado = 'sin justificar' GROUP BY matriculado.alumno_id;