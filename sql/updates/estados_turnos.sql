-- Actualización de la estructura de estados de turnos
ALTER TABLE `turnos` 
CHANGE COLUMN `estado` `estado` tinyint(1) NOT NULL DEFAULT 0 
COMMENT '0=Pendiente, 1=Presente, 2=Ausente, 3=Aprobado, 4=Rechazado, 5=Asignado, 6=Cancelado';

-- Añadir un índice para mejorar las búsquedas por estado si no existe
ALTER TABLE `turnos` ADD INDEX IF NOT EXISTS `idx_estado` (`estado`);

-- Comentario para referencia:
-- 0 = Pendiente: Turno agregado por secretario o bot, esperando aprobación del profesional
-- 1 = Presente: El paciente asistió al turno
-- 2 = Ausente: El paciente no asistió al turno
-- 3 = Aprobado: El profesional aprobó el turno pendiente
-- 4 = Rechazado: El profesional rechazó el turno pendiente
-- 5 = Asignado: Turno creado directamente por el profesional
-- 6 = Cancelado: El paciente canceló el turno previamente
