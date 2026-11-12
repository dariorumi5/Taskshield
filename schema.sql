CREATE DATABASE IF NOT EXISTS taskshield
USE taskshield;

DROP TABLE IF EXISTS incidencias;
DROP TABLE IF EXISTS tecnicos;

CREATE TABLE tecnicos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    tareas_activas INT UNSIGNED NOT NULL DEFAULT 0
)

CREATE TABLE incidencias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('Baja','Media','Alta') NOT NULL DEFAULT 'Media',
    email_cliente VARCHAR(100) NOT NULL,
    estado ENUM('Pendiente','En Proceso','Resuelta') NOT NULL DEFAULT 'Pendiente',
    tecnico_id INT UNSIGNED NULL,
    FOREIGN KEY (tecnico_id) REFERENCES tecnicos(id)
)

INSERT INTO tecnicos (nombre) VALUES ('Laura Gómez'), ('Miguel Torres')

INSERT INTO incidencias (titulo, descripcion, prioridad, email_cliente) VALUES
('Impresora sin tinta', 'La impresora de la planta 2 no imprime.', 'Baja', 'ana@empresa.local'),
('Correo no sincroniza', 'Outlook no actualiza desde esta mañana.', 'Alta', 'pedro@empresa.local'),
('Alta de nuevo empleado', 'Crear usuario y equipo para RR. HH.', 'Media', 'rrhh@empresa.local'),
('VPN caída', 'Varios usuarios no pueden conectarse en remoto.', 'Alta', 'carmen@empresa.local');

