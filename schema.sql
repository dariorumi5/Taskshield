CREATE DATABASE IF NOT EXISTS taskshield
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
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