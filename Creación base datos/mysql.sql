-- Crear la base de datos con codificación UTF-8 BIN
CREATE DATABASE IF NOT EXISTS DemocrApp 
DEFAULT CHARACTER SET utf8 
DEFAULT COLLATE utf8_bin;

USE DemocrApp;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, -- Nombre de usuario
    email VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin UNIQUE NOT NULL, -- Correo electrónico único
    password_hash VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, -- Contraseña encriptada
    is_admin BOOLEAN DEFAULT FALSE, -- Indica si el usuario es administrador
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP -- Fecha de registro del usuario
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de rondas (cada ronda agrupa un conjunto de temas)
CREATE TABLE IF NOT EXISTS rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, -- Nombre de la ronda
    stage VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,  -- Nombre de la fase en la que está la ronda
    start_date DATETIME, -- Fecha de inicio de la ronda
    end_date DATETIME, -- Fecha de finalización de la ronda
    status ENUM('active', 'inactive', 'finished') CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'inactive', -- Estado de la ronda
    topics_per_round INT DEFAULT 10, -- Número máximo de temas por ronda
    votes_per_user INT DEFAULT 1 -- Número de votos que puede emitir cada usuario en la ronda
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de temas (topics)
CREATE TABLE IF NOT EXISTS topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, -- Título del tema
    topic VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, -- Categoría principal del tema
    category VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'Uncategorized', -- Categoría general del tema
    similarity_score FLOAT DEFAULT 0, -- Puntaje de similitud con otros temas (0-1)
    similar_to TEXT NULL, -- IDs de los temas similares separados por comas
    is_approved BOOLEAN DEFAULT FALSE, -- Indica si el tema ha sido aprobado por un administrador
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP -- Fecha de creación del tema
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla intermedia para relacionar temas con múltiples rondas (relación muchos a muchos)
CREATE TABLE IF NOT EXISTS topic_rounds (
    topic_id INT NOT NULL, -- ID del tema
    round_id INT NOT NULL, -- ID de la ronda
    PRIMARY KEY (topic_id, round_id) -- Clave primaria compuesta para evitar duplicados
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de votos (evita votos duplicados del mismo usuario en un mismo tema y ronda)
CREATE TABLE IF NOT EXISTS votes (
    user_id INT NOT NULL, -- ID del usuario que vota
    topic_id INT NOT NULL, -- ID del tema votado
    round_id INT NOT NULL, -- ID de la ronda donde se emitió el voto
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, -- Fecha del voto
    PRIMARY KEY (user_id, topic_id, round_id) -- Clave primaria compuesta para evitar votos duplicados
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Agregar claves foráneas con ALTER TABLE (mejor legibilidad)
ALTER TABLE topic_rounds 
ADD CONSTRAINT FK_topic_rounds_topic FOREIGN KEY (topic_id)
REFERENCES topics(id) ON DELETE CASCADE;

ALTER TABLE topic_rounds 
ADD CONSTRAINT FK_topic_rounds_round FOREIGN KEY (round_id)
REFERENCES rounds(id) ON DELETE CASCADE;

ALTER TABLE votes 
ADD CONSTRAINT FK_votes_user FOREIGN KEY (user_id)
REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE votes 
ADD CONSTRAINT FK_votes_topic FOREIGN KEY (topic_id)
REFERENCES topics(id) ON DELETE CASCADE;

ALTER TABLE votes 
ADD CONSTRAINT FK_votes_round FOREIGN KEY (round_id)
REFERENCES rounds(id) ON DELETE CASCADE;

-- Crear una vista para calcular dinámicamente los votos de cada tema (solo temas aprobados)
CREATE OR REPLACE VIEW topic_votes AS
SELECT 
    topics.id AS topic_id,
    topics.title AS topic_title,
    topics.topic,
    topics.category,
    rounds.stage AS stage,
    topics.similarity_score,
    topics.similar_to,
    COUNT(votes.user_id) AS total_votes
FROM 
    topics
LEFT JOIN 
    topic_rounds ON topics.id = topic_rounds.topic_id -- Relación entre temas y rondas
LEFT JOIN 
    rounds ON topic_rounds.round_id = rounds.id
LEFT JOIN 
    votes ON topics.id = votes.topic_id AND votes.round_id = rounds.id
WHERE 
    topics.is_approved = TRUE -- Solo incluir temas aprobados
GROUP BY 
    topics.id, topics.title, topics.topic, topics.category, rounds.stage, 
    topics.similarity_score, topics.similar_to;

-- Crear índices para optimización de consultas
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_votes_user ON votes(user_id);
CREATE INDEX idx_votes_topic ON votes(topic_id);
CREATE INDEX idx_topics_similar_to ON topics(similar_to);
CREATE INDEX idx_topics_approved ON topics(is_approved);
CREATE INDEX idx_topic_rounds ON topic_rounds(topic_id, round_id);

-- Insertar un administrador si no existe
INSERT INTO users (username, email, password_hash, is_admin, registration_date)
SELECT 'Admin', 'admin@democrapp.com', MD5('admin123'), TRUE, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@democrapp.com' AND is_admin = TRUE
);
