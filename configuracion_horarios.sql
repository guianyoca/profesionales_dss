-- Tabla para configuración de horarios de profesionales
CREATE TABLE IF NOT EXISTS horarios_profesionales (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_profesional INT(11) NOT NULL,
    dia_semana TINYINT(1) NOT NULL COMMENT '0=Domingo, 1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes, 6=Sábado',
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    duracion_turno INT(11) NOT NULL COMMENT 'Duración del turno en minutos',
    pacientes_por_turno INT(11) NOT NULL DEFAULT 1 COMMENT 'Cantidad de pacientes que puede atender simultáneamente',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_horarios_profesional FOREIGN KEY (id_profesional) REFERENCES usuario(idusuario),
    UNIQUE KEY unique_horario (id_profesional, dia_semana, hora_inicio)
);

-- Tabla para configuración general del sistema
CREATE TABLE IF NOT EXISTS configuracion_sistema (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    duracion_turno_default INT(11) NOT NULL DEFAULT 30 COMMENT 'Duración predeterminada del turno en minutos',
    pacientes_por_turno_default INT(11) NOT NULL DEFAULT 1 COMMENT 'Cantidad predeterminada de pacientes por turno',
    hora_inicio_default TIME NOT NULL DEFAULT '08:00:00',
    hora_fin_default TIME NOT NULL DEFAULT '18:00:00',
    dias_laborables VARCHAR(20) NOT NULL DEFAULT '1,2,3,4,5' COMMENT 'Días laborables por defecto (formato: 0,1,2,3,4,5,6)',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar configuración por defecto si no existe
INSERT INTO configuracion_sistema (duracion_turno_default, pacientes_por_turno_default, hora_inicio_default, hora_fin_default, dias_laborables)
VALUES (30, 1, '08:00:00', '18:00:00', '1,2,3,4,5')
ON DUPLICATE KEY UPDATE id = id;
