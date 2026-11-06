-- =============================================
-- Oase DB v7 — Con traducciones en galeria_grupos
-- =============================================

-- DROP DATABASE IF EXISTS oase_db;
-- CREATE DATABASE oase_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE oase_db;

-- =============================================
-- ADMIN USERS
-- =============================================
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100),
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SERVICIOS
-- =============================================
CREATE TABLE servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_de VARCHAR(150) NOT NULL,
    nombre_es VARCHAR(150) NOT NULL,
    nombre_fr VARCHAR(150) NOT NULL,
    nombre_en VARCHAR(150) NOT NULL DEFAULT '',
    precio DECIMAL(10,2) NOT NULL,
    precio_texto VARCHAR(50) DEFAULT NULL,
    duracion_min INT DEFAULT 60,
    categoria VARCHAR(50) DEFAULT 'general',
    imagen VARCHAR(255),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- HORARIOS
-- =============================================
CREATE TABLE horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- RESERVAS
-- =============================================
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    email_confirmacion VARCHAR(150) NOT NULL DEFAULT '',
    telefono VARCHAR(20),
    servicio_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('confirmada','cancelada','pendiente_confirmacion') DEFAULT 'confirmada',
    motivo_cancelacion TEXT,
    lang VARCHAR(5) DEFAULT 'de',
    recordatorio_enviado TINYINT(1) DEFAULT 0,
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- DÍAS BLOQUEADOS
-- =============================================
CREATE TABLE dias_bloqueados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL UNIQUE,
    motivo VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- GALERIA (original — sin cambios)
-- =============================================
CREATE TABLE galeria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('imagen','video') DEFAULT 'imagen',
    archivo VARCHAR(255) NOT NULL,
    titulo VARCHAR(150),
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- GALERIA_GRUPOS (con traducciones 4 idiomas)
-- =============================================
CREATE TABLE galeria_grupos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('foto','antes_despues','carrusel') NOT NULL DEFAULT 'foto',
    titulo_de VARCHAR(200),
    titulo_es VARCHAR(200),
    titulo_fr VARCHAR(200),
    titulo_en VARCHAR(200),
    descripcion_de TEXT,
    descripcion_es TEXT,
    descripcion_fr TEXT,
    descripcion_en TEXT,
    categoria VARCHAR(50) DEFAULT 'general',
    tags VARCHAR(500),
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- GALERIA_FOTOS
-- =============================================
CREATE TABLE galeria_fotos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    grupo_id INT NOT NULL,
    archivo VARCHAR(255) NOT NULL,
    rol ENUM('normal','antes','despues') DEFAULT 'normal',
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES galeria_grupos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =============================================
-- VIDEOS (con traducciones 4 idiomas)
-- =============================================
CREATE TABLE videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    archivo VARCHAR(255) NOT NULL,
    titulo_de VARCHAR(200),
    titulo_es VARCHAR(200),
    titulo_fr VARCHAR(200),
    titulo_en VARCHAR(200),
    descripcion_de TEXT,
    descripcion_es TEXT,
    descripcion_fr TEXT,
    descripcion_en TEXT,
    categoria VARCHAR(50) DEFAULT 'general',
    tags VARCHAR(500),
    orden INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- HORARIOS POR DEFECTO
-- =============================================
INSERT INTO horarios (dia_semana, hora_inicio, hora_fin, activo) VALUES
(1, '09:00:00', '19:00:00', 1),
(2, '09:00:00', '19:00:00', 1),
(3, '09:00:00', '19:00:00', 1),
(4, '09:00:00', '19:00:00', 1),
(5, '09:00:00', '19:00:00', 1),
(6, '09:00:00', '17:00:00', 1);

-- =============================================
-- SERVICIOS REALES DE EDWIN
-- =============================================
INSERT INTO servicios (nombre_de, nombre_es, nombre_fr, nombre_en, precio, precio_texto, duracion_min, categoria) VALUES
('Waschen, Schneiden & Föhnen (kurzes Haar)',       'Lavado, corte y peinado (cabello corto)',    'Lavage, coupe et coiffage (cheveux courts)', 'Wash, cut & blowdry (short hair)',   85.00, 'desde / ab', 60,  'corte'),
('Waschen, Schneiden & Föhnen (mittellanges Haar)', 'Lavado, corte y peinado (media melena)',     'Lavage, coupe et coiffage (mi-long)',        'Wash, cut & blowdry (medium hair)',  95.00, 'desde / ab', 75,  'corte'),
('Waschen, Schneiden & Föhnen (langes Haar)',       'Lavado, corte y peinado (cabello largo)',    'Lavage, coupe et coiffage (cheveux longs)', 'Wash, cut & blowdry (long hair)',   105.00, 'desde / ab', 90,  'corte'),
('Waschen & Föhnen',                                'Lavado y peinado',                           'Lavage et coiffage',                        'Wash & blowdry',                     65.00, 'desde / ab', 45,  'corte'),
('Spitzenschnitt',                                  'Corte de puntas',                            'Coupe des pointes',                         'Trim',                               45.00, 'desde / ab', 30,  'corte'),
('Herrenhaarschnitt',                               'Corte de caballero',                         'Coupe homme',                               'Men''s haircut',                     40.00, '',           30,  'corte'),
('Kinderhaarschnitt',                               'Corte de niño',                              'Coupe enfant',                              'Children''s haircut',                25.00, 'desde / ab', 20,  'corte'),
('Ansatzfarbe',                                     'Color raíz',                                 'Couleur racines',                           'Root color',                        100.00, 'desde / ab', 90,  'color'),
('Komplettfarbe',                                   'Color completo',                             'Couleur complète',                          'Full color',                        120.00, 'desde / ab', 120, 'color'),
('Teilsträhnen',                                    'Mechas parciales',                           'Mèches partielles',                         'Partial highlights',                120.00, 'desde / ab', 90,  'color'),
('Komplettsträhnen',                                'Mechas completas',                           'Mèches complètes',                          'Full highlights',                   200.00, 'desde / ab', 150, 'color'),
('Balayage',                                        'Balayage',                                   'Balayage',                                  'Balayage',                          220.00, 'desde / ab', 150, 'color'),
('Gloss / Toner',                                   'Gloss / Toner',                              'Gloss / Toner',                             'Gloss / Toner',                      50.00, 'desde / ab', 30,  'color'),
('Feuchtigkeitspflege',                             'Tratamiento hidratante',                     'Soin hydratant',                            'Moisturising treatment',             45.00, 'desde / ab', 45,  'tratamiento'),
('Intensivpflege',                                  'Tratamiento nutritivo profundo',              'Soin nutritif intense',                     'Deep nourishing treatment',          55.00, 'desde / ab', 60,  'tratamiento'),
('Olaplex oder K18',                                'Olaplex o K18',                              'Olaplex ou K18',                            'Olaplex or K18',                     50.00, 'desde / ab', 45,  'tratamiento'),
('Keratinbehandlung',                               'Keratina',                                   'Kératine',                                  'Keratin treatment',                 220.00, 'desde / ab', 180, 'tratamiento');