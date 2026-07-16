-- =========================================================
-- SAPD - Base de datos oficial
-- San Andreas Police Department
-- =========================================================

CREATE DATABASE IF NOT EXISTS sapd CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sapd;

-- ---------------------------------------------------------
-- Usuarios del panel (staff web: admin / editor)
-- ---------------------------------------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Rangos (jerarquía). nivel más bajo = más alto en la cadena de mando
-- ---------------------------------------------------------
CREATE TABLE rangos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    nivel INT NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Divisiones / unidades
-- ---------------------------------------------------------
CREATE TABLE divisiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL,
    descripcion VARCHAR(255) NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Oficiales
-- ---------------------------------------------------------
CREATE TABLE oficiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    apellido VARCHAR(60) NOT NULL,
    placa VARCHAR(20) NOT NULL UNIQUE,
    rango_id INT NOT NULL,
    division_id INT NULL,
    foto VARCHAR(255) NULL,
    biografia TEXT NULL,
    fecha_ingreso DATE NOT NULL,
    estado ENUM('activo','baja','suspendido') NOT NULL DEFAULT 'activo',
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rango_id) REFERENCES rangos(id),
    FOREIGN KEY (division_id) REFERENCES divisiones(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Noticias / comunicados
-- ---------------------------------------------------------
CREATE TABLE noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    resumen VARCHAR(300) NOT NULL,
    contenido TEXT NOT NULL,
    imagen VARCHAR(255) NULL,
    categoria VARCHAR(60) NOT NULL DEFAULT 'General',
    autor_id INT NOT NULL,
    estado ENUM('borrador','publicado') NOT NULL DEFAULT 'borrador',
    vistas INT NOT NULL DEFAULT 0,
    publicado_en DATETIME NULL,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Destacados: oficial de la semana / del mes (con histórico)
-- ---------------------------------------------------------
CREATE TABLE destacados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('semana','mes') NOT NULL,
    oficial_id INT NOT NULL,
    motivo TEXT NOT NULL,
    periodo_inicio DATE NOT NULL,
    periodo_fin DATE NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (oficial_id) REFERENCES oficiales(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =========================================================
-- Datos iniciales
-- =========================================================

-- Usuario administrador por defecto -> usuario: admin / contraseña: sapd2026
-- (cambia la contraseña en cuanto entres al panel)
INSERT INTO usuarios (nombre, usuario, password, rol) VALUES
('Administrador', 'admin', '$2y$10$.pEo6a.8soHpXKU5fM1VseN7KUfvmsL92lJ5chxW5fxhJoQT6oNvi', 'admin');
-- El hash de arriba corresponde a "sapd2026". Puedes generar uno nuevo con password_hash() en PHP.

INSERT INTO rangos (nombre, nivel) VALUES
('Jefe de Policía', 1),
('Subjefe', 2),
('Comandante', 3),
('Capitán', 4),
('Teniente', 5),
('Sargento', 6),
('Oficial Senior', 7),
('Oficial', 8),
('Cadete', 9);

INSERT INTO divisiones (nombre, descripcion) VALUES
('Patrulla', 'Respuesta y vigilancia en las calles de San Andreas'),
('Detectives', 'Investigación de delitos mayores'),
('SWAT', 'Unidad táctica de operaciones especiales'),
('Tránsito', 'Control y seguridad vial'),
('Academia', 'Formación y evaluación de nuevos reclutas'),
('Asuntos Internos', 'Supervisión de conducta y disciplina interna');

INSERT INTO oficiales (nombre, apellido, placa, rango_id, division_id, biografia, fecha_ingreso, estado) VALUES
('Marcus', 'Bennett', '1001', 1, 1, 'Al frente del departamento desde hace tres años, con un historial dedicado a reducir la criminalidad en los distritos centrales de Los Santos.', '2019-03-14', 'activo'),
('Elena', 'Vasquez', '1042', 4, 3, 'Capitana de la unidad SWAT, especializada en resolución de incidentes de alto riesgo.', '2020-06-01', 'activo'),
('Ryan', 'Ortega', '1187', 6, 1, 'Sargento de patrulla, referente en la zona de Vinewood por su cercanía con la comunidad.', '2021-01-20', 'activo'),
('Sophia', 'Nakamura', '1219', 5, 2, 'Teniente de detectives, ha cerrado algunos de los casos más complejos del último año.', '2021-09-10', 'activo'),
('David', 'Kwan', '1305', 8, 4, 'Oficial de tránsito responsable de la seguridad vial en la autopista de Los Santos.', '2023-02-05', 'activo'),
('Priya', 'Malhotra', '1341', 7, 1, 'Oficial senior de patrulla con un enfoque en mediación comunitaria.', '2022-11-18', 'activo');

INSERT INTO noticias (titulo, slug, resumen, contenido, categoria, autor_id, estado, publicado_en) VALUES
('Nuevo protocolo de respuesta rápida entra en vigor', 'nuevo-protocolo-respuesta-rapida',
 'El departamento actualiza los tiempos y procedimientos de respuesta ante llamadas de emergencia.',
 'A partir de este mes, el SAPD implementa un protocolo revisado para la coordinación entre centralita y unidades de patrulla, reduciendo los tiempos de respuesta en zonas de alta incidencia. La medida se apoya en un nuevo sistema de asignación por proximidad y busca reforzar la presencia policial en horarios de mayor actividad.',
 'Comunicado', 1, 'publicado', NOW()),
('Se gradúa la nueva promoción de la Academia', 'graduacion-nueva-promocion-academia',
 'Doce cadetes completan su formación y se incorporan oficialmente a las filas del departamento.',
 'Tras dieciséis semanas de entrenamiento físico, táctico y legal, doce nuevos oficiales juraron su cargo esta semana. La ceremonia contó con la presencia de mandos superiores y familiares, y marca el inicio de una nueva etapa para los graduados, quienes se integrarán a distintas divisiones según su desempeño durante la formación.',
 'Academia', 1, 'publicado', NOW()),
('Balance mensual de seguridad ciudadana', 'balance-mensual-seguridad-ciudadana',
 'El SAPD comparte las cifras y logros más relevantes del último mes de operaciones.',
 'Durante el último mes, las unidades de patrulla atendieron un número significativo de incidentes, con una reducción notable en delitos contra la propiedad respecto al periodo anterior. El departamento reconoce el trabajo conjunto entre las divisiones de Patrulla, Detectives y Tránsito como factor clave en estos resultados.',
 'Estadísticas', 1, 'publicado', NOW());

INSERT INTO destacados (tipo, oficial_id, motivo, periodo_inicio, periodo_fin, activo) VALUES
('semana', 3, 'Por su intervención decisiva durante un robo en curso en el distrito de Vinewood, logrando la detención de los sospechosos sin incidentes.', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 6 DAY), 1),
('mes', 2, 'Por liderar con éxito la resolución de una toma de rehenes, priorizando en todo momento la seguridad de los civiles involucrados.', CURDATE(), LAST_DAY(CURDATE()), 1);
