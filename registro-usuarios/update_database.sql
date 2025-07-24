-- Script para agregar la columna email_verificado a la tabla wp_aess_users
-- Ejecuta este script en tu base de datos MySQL

ALTER TABLE wp_aess_users 
ADD COLUMN email_verificado TINYINT(1) DEFAULT 0 
COMMENT '0 = no verificado, 1 = verificado';

-- Opcional: Agregar un índice para mejorar el rendimiento de las consultas
ALTER TABLE wp_aess_users 
ADD INDEX idx_email_verificado (email_verificado);

-- Opcional: Agregar un índice compuesto para email y código de activación
ALTER TABLE wp_aess_users 
ADD INDEX idx_email_codigo (email, codigo_activacion); 