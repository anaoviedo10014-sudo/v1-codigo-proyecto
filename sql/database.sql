DROP DATABASE IF EXISTS control_computadores_sena;
CREATE DATABASE control_computadores_sena;
USE control_computadores_sena;

CREATE TABLE rol (
    id int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE jornada (
    id int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE usuario (
    id int(11) NOT NULL AUTO_INCREMENT,
    carnet varchar(20) NOT NULL,
    nombre_completo varchar(100) NOT NULL,
    contrasena varchar(255) NOT NULL,
    rol varchar(50) NOT NULL,
    id_jornada int(11) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY carnet (carnet),
    KEY id_jornada (id_jornada),
    CONSTRAINT usuario_ibfk_1 FOREIGN KEY (id_jornada) REFERENCES jornada (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE marca (
    id int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE modelo (
    id int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(100) NOT NULL,
    id_marca int(11) NOT NULL,
    PRIMARY KEY (id),
    KEY id_marca (id_marca),
    CONSTRAINT modelo_ibfk_1 FOREIGN KEY (id_marca) REFERENCES marca (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tipo (
    id int(11) NOT NULL AUTO_INCREMENT,
    nombre varchar(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE portatil (
    id int(11) NOT NULL AUTO_INCREMENT,
    serial varchar(50) NOT NULL,
    id_marca int(11) NOT NULL,
    id_modelo int(11) NOT NULL,
    asignado_a int(11) DEFAULT NULL,
    estado enum('disponible','asignado','en_reparacion') DEFAULT 'disponible',
    tipo_equipo varchar(50) DEFAULT 'portatil',
    otro_tipo varchar(100) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY serial (serial),
    KEY id_marca (id_marca),
    KEY id_modelo (id_modelo),
    KEY asignado_a (asignado_a),
    CONSTRAINT portatil_ibfk_1 FOREIGN KEY (id_marca) REFERENCES marca (id) ON DELETE CASCADE,
    CONSTRAINT portatil_ibfk_2 FOREIGN KEY (id_modelo) REFERENCES modelo (id) ON DELETE CASCADE,
    CONSTRAINT portatil_ibfk_3 FOREIGN KEY (asignado_a) REFERENCES usuario (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE registro_entrada_salida (
    id int(11) NOT NULL AUTO_INCREMENT,
    id_usuario int(11) NOT NULL,
    id_portatil int(11) NOT NULL,
    tipo enum('entrada','salida') NOT NULL,
    fecha_hora datetime NOT NULL,
    observacion text,
    PRIMARY KEY (id),
    KEY id_usuario (id_usuario),
    KEY id_portatil (id_portatil),
    CONSTRAINT registro_entrada_salida_ibfk_1 FOREIGN KEY (id_usuario) REFERENCES usuario (id) ON DELETE CASCADE,
    CONSTRAINT registro_entrada_salida_ibfk_2 FOREIGN KEY (id_portatil) REFERENCES portatil (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO rol (nombre) VALUES ('administrador'), ('guarda'), ('aprendiz'), ('instructor'), ('funcionario');

INSERT INTO jornada (nombre) VALUES 
('Mañana (6:00 - 12:00)'), 
('Tarde (12:00 - 18:00)'), 
('Noche (18:00 - 22:00)'), 
('Diurna (6:00 - 18:00)'), 
('Nocturna (18:00 - 6:00)'), 
('Otro');

INSERT INTO usuario (carnet, nombre_completo, contrasena, rol, id_jornada) VALUES 
('ADMIN001', 'Administrador SENA', MD5('123456'), 'administrador', 1),
('GUARDA001', 'Guarda de Seguridad', MD5('guarda123'), 'guarda', 1),
('APR2024001', 'Juan Pérez López', MD5('aprendiz123'), 'aprendiz', 1),
('INS2024001', 'Carlos Gómez Ramírez', MD5('instructor123'), 'instructor', 2);

INSERT INTO marca (nombre) VALUES 
('HP'), ('Dell'), ('Lenovo'), ('Acer'), ('Apple'), 
('Samsung'), ('ASUS'), ('Toshiba'), ('Sony'), ('LG'), 
('MSI'), ('Razer'), ('Huawei'), ('Xiaomi'), ('Alienware');

INSERT INTO modelo (nombre, id_marca) VALUES 
('Pavilion 15', 1), ('Inspiron 14', 2), ('ThinkCentre', 3), ('Aspire 5', 4), ('MacBook Air', 5),
('Galaxy Book', 6), ('ZenBook', 7), ('Tecra', 8), ('Vaio', 9), ('Gram', 10),
('Stealth', 11), ('Blade', 12), ('MateBook', 13), ('Mi Notebook', 14), ('Area-51m', 15);

INSERT INTO tipo (nombre) VALUES ('Portátil'), ('Desktop'), ('All-in-One'), ('Tablet'), ('Otro');

INSERT INTO portatil (serial, id_marca, id_modelo, estado) VALUES 
('PC-001', 1, 1, 'disponible'),
('PC-002', 2, 2, 'disponible'),
('PC-003', 3, 3, 'disponible'),
('PC-004', 4, 4, 'disponible'),
('PC-005', 5, 5, 'disponible');