-- Asegurarse de que la tabla notas existe
CREATE TABLE IF NOT EXISTS notas (
  id_nota INT AUTO_INCREMENT PRIMARY KEY,
  id_aprendiz VARCHAR(20) NOT NULL,
  competencia VARCHAR(100) NOT NULL,
  nota DECIMAL(3,1) NOT NULL,
  fecha DATE NOT NULL,
  FOREIGN KEY (id_aprendiz) REFERENCES aprendices(id_aprendiz) ON DELETE CASCADE
);

-- Agregar índices para mejorar el rendimiento de las consultas
CREATE INDEX IF NOT EXISTS idx_notas_id_aprendiz ON notas(id_aprendiz);
CREATE INDEX IF NOT EXISTS idx_notas_competencia ON notas(competencia);

-- Crear tabla para almacenar múltiples notas por competencia
CREATE TABLE IF NOT EXISTS notas_multiples (
  id_nota INT AUTO_INCREMENT PRIMARY KEY,
  id_aprendiz VARCHAR(20) NOT NULL,
  competencia VARCHAR(100) NOT NULL,
  nota1 DECIMAL(3,1) NOT NULL,
  nota2 DECIMAL(3,1) NOT NULL,
  nota3 DECIMAL(3,1) NOT NULL,
  promedio DECIMAL(3,1) NOT NULL,
  fecha DATE NOT NULL,
  FOREIGN KEY (id_aprendiz) REFERENCES aprendices(id_aprendiz) ON DELETE CASCADE
);

-- Agregar índices para la nueva tabla
CREATE INDEX IF NOT EXISTS idx_notas_multiples_id_aprendiz ON notas_multiples(id_aprendiz);
CREATE INDEX IF NOT EXISTS idx_notas_multiples_competencia ON notas_multiples(competencia);