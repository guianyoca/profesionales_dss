-- Verificar si la columna observaciones ya existe
SET @exist := (SELECT COUNT(*) 
               FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = DATABASE() 
               AND TABLE_NAME = 'turnos' 
               AND COLUMN_NAME = 'observaciones');

-- Agregar columna si no existe
SET @query := IF(@exist = 0, 
                'ALTER TABLE turnos ADD COLUMN observaciones TEXT NULL AFTER estado', 
                'SELECT "La columna observaciones ya existe" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Actualizar la definición del campo estado para soportar nuevos estados
-- 0 = Asignado (anterior), ahora será "Pendiente de aprobación"
-- 1 = Presente (anterior)
-- 2 = Ausente (anterior)
-- 3 = Aprobado por profesional (nuevo)
-- 4 = Rechazado por profesional (nuevo)

-- Verificar valores máximos actuales en estado para no afectar datos existentes
SET @max_estado := (SELECT MAX(estado) FROM turnos);

-- Solo actualizar si es seguro (no hay valores mayores a 2)
SET @query := IF(@max_estado <= 2, 
                'ALTER TABLE turnos MODIFY COLUMN estado TINYINT(1) NOT NULL DEFAULT 0 COMMENT "0=Pendiente, 1=Presente, 2=Ausente, 3=Aprobado, 4=Rechazado"', 
                'SELECT "No se puede modificar el campo estado porque ya existen valores mayores a 2" AS mensaje');
PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
