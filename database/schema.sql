-- Tabellen van de PC-bouwplanner. Bestaande tabellen worden eerst verwijderd,
-- zodat het seed-script altijd met een schone database begint.

DROP VIEW IF EXISTS part_details;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS build_parts, builds, cpus, motherboards, ram_kits, gpus, coolers, cases,
    storage_drives, psus, parts, sockets, ram_types, form_factors;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE sockets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE ram_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(10) NOT NULL UNIQUE
);

CREATE TABLE form_factors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL UNIQUE,
    size_rank TINYINT UNSIGNED NOT NULL UNIQUE
);

CREATE TABLE parts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category ENUM('cpu', 'motherboard', 'ram', 'gpu', 'cooler', 'case', 'storage', 'psu') NOT NULL,
    brand VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    price_eur DECIMAL(7, 2) NOT NULL CHECK (price_eur > 0),
    price_date DATE NOT NULL,
    power_w SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE (brand, name),
    INDEX (category)
);

CREATE TABLE cpus (
    part_id INT UNSIGNED PRIMARY KEY,
    socket_id INT UNSIGNED NOT NULL,
    cores TINYINT UNSIGNED NOT NULL,
    threads TINYINT UNSIGNED NOT NULL,
    boost_clock_ghz DECIMAL(3, 1) NOT NULL,
    has_integrated_graphics BOOLEAN NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE,
    FOREIGN KEY (socket_id) REFERENCES sockets (id)
);

CREATE TABLE motherboards (
    part_id INT UNSIGNED PRIMARY KEY,
    socket_id INT UNSIGNED NOT NULL,
    ram_type_id INT UNSIGNED NOT NULL,
    form_factor_id INT UNSIGNED NOT NULL,
    chipset VARCHAR(20) NOT NULL,
    ram_slots TINYINT UNSIGNED NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE,
    FOREIGN KEY (socket_id) REFERENCES sockets (id),
    FOREIGN KEY (ram_type_id) REFERENCES ram_types (id),
    FOREIGN KEY (form_factor_id) REFERENCES form_factors (id)
);

CREATE TABLE ram_kits (
    part_id INT UNSIGNED PRIMARY KEY,
    ram_type_id INT UNSIGNED NOT NULL,
    capacity_gb SMALLINT UNSIGNED NOT NULL,
    module_count TINYINT UNSIGNED NOT NULL,
    speed_mts SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE,
    FOREIGN KEY (ram_type_id) REFERENCES ram_types (id)
);

CREATE TABLE gpus (
    part_id INT UNSIGNED PRIMARY KEY,
    chip VARCHAR(50) NOT NULL,
    vram_gb TINYINT UNSIGNED NOT NULL,
    length_mm SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE
);

CREATE TABLE coolers (
    part_id INT UNSIGNED PRIMARY KEY,
    height_mm SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE
);

CREATE TABLE cases (
    part_id INT UNSIGNED PRIMARY KEY,
    max_form_factor_id INT UNSIGNED NOT NULL,
    max_gpu_length_mm SMALLINT UNSIGNED NOT NULL,
    max_cooler_height_mm SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE,
    FOREIGN KEY (max_form_factor_id) REFERENCES form_factors (id)
);

CREATE TABLE storage_drives (
    part_id INT UNSIGNED PRIMARY KEY,
    type ENUM('NVMe', 'SATA') NOT NULL,
    capacity_gb SMALLINT UNSIGNED NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE
);

CREATE TABLE psus (
    part_id INT UNSIGNED PRIMARY KEY,
    wattage_w SMALLINT UNSIGNED NOT NULL,
    efficiency_rating VARCHAR(30) NOT NULL,
    FOREIGN KEY (part_id) REFERENCES parts (id) ON DELETE CASCADE
);

CREATE TABLE builds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    share_code CHAR(8) NOT NULL UNIQUE,
    name VARCHAR(60) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE build_parts (
    build_id INT UNSIGNED NOT NULL,
    part_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (build_id, part_id),
    FOREIGN KEY (build_id) REFERENCES builds (id) ON DELETE CASCADE,
    FOREIGN KEY (part_id) REFERENCES parts (id)
);

-- Eén rij per onderdeel met alle specificaties, zodat de PHP-code niet steeds
-- dezelfde lange reeks joins nodig heeft. Velden van een andere categorie zijn NULL.
CREATE VIEW part_details AS
SELECT
    p.id,
    p.category,
    p.brand,
    p.name,
    p.price_eur,
    p.price_date,
    p.power_w,
    COALESCE(cpu_socket.name, board_socket.name) AS socket,
    COALESCE(board_ram.name, kit_ram.name) AS ram_type,
    board_size.name AS form_factor,
    board_size.size_rank AS form_factor_rank,
    case_size.name AS max_form_factor,
    case_size.size_rank AS max_form_factor_rank,
    c.cores,
    c.threads,
    c.boost_clock_ghz,
    c.has_integrated_graphics,
    m.chipset,
    m.ram_slots,
    COALESCE(r.capacity_gb, s.capacity_gb) AS capacity_gb,
    r.module_count,
    r.speed_mts,
    g.chip,
    g.vram_gb,
    g.length_mm,
    co.height_mm,
    ca.max_gpu_length_mm,
    ca.max_cooler_height_mm,
    s.type AS storage_type,
    ps.wattage_w,
    ps.efficiency_rating
FROM parts p
LEFT JOIN cpus c ON c.part_id = p.id
LEFT JOIN sockets cpu_socket ON cpu_socket.id = c.socket_id
LEFT JOIN motherboards m ON m.part_id = p.id
LEFT JOIN sockets board_socket ON board_socket.id = m.socket_id
LEFT JOIN ram_types board_ram ON board_ram.id = m.ram_type_id
LEFT JOIN form_factors board_size ON board_size.id = m.form_factor_id
LEFT JOIN ram_kits r ON r.part_id = p.id
LEFT JOIN ram_types kit_ram ON kit_ram.id = r.ram_type_id
LEFT JOIN gpus g ON g.part_id = p.id
LEFT JOIN coolers co ON co.part_id = p.id
LEFT JOIN cases ca ON ca.part_id = p.id
LEFT JOIN form_factors case_size ON case_size.id = ca.max_form_factor_id
LEFT JOIN storage_drives s ON s.part_id = p.id
LEFT JOIN psus ps ON ps.part_id = p.id;
