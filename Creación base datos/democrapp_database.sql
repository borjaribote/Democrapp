-- Crear la base de datos con codificación UTF-8 BIN
CREATE DATABASE IF NOT EXISTS DemocrApp 
DEFAULT CHARACTER SET utf8 
DEFAULT COLLATE utf8_bin;

USE DemocrApp;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    email VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin UNIQUE NOT NULL,
    password_hash VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de rondas 
CREATE TABLE IF NOT EXISTS rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    stage ENUM('proposals', 'qualifying', 'final', 'tiebreaker') NOT NULL,
    start_date DATETIME,
    end_date DATETIME,
    status ENUM('active', 'inactive', 'finished') DEFAULT 'inactive',
    topics_per_round INT DEFAULT 10,
    votes_per_user INT DEFAULT 3,
    final_round BOOLEAN DEFAULT FALSE -- Indica si la ronda es la última
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de temas (topics)
CREATE TABLE IF NOT EXISTS topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    topic VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    description TEXT NULL,
    similarity_score FLOAT DEFAULT 0,
    similar_to TEXT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    CONSTRAINT unique_topic UNIQUE (topic)
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla intermedia para relacionar temas con múltiples rondas
CREATE TABLE IF NOT EXISTS topic_rounds (
    topic_id INT NOT NULL,
    round_id INT NOT NULL,
    PRIMARY KEY (topic_id, round_id),
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de votos
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT DEFAULT NULL,
    topic_id INT NOT NULL,
    round_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (user_id, topic_id, round_id), -- Evita votos duplicados
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear la tabla de finalistas con el campo winner
CREATE TABLE IF NOT EXISTS finalists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    round_id INT NOT NULL,
    votes INT DEFAULT 0,
    classified_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    winner BOOLEAN DEFAULT FALSE, -- Indica si es el ganador de la ronda final
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Crear una vista para calcular dinámicamente los votos de cada tema (solo temas aprobados)
CREATE OR REPLACE VIEW topic_votes AS
SELECT 
    topics.id AS topic_id,
    topics.user_id, -- Muestra qué usuario propuso el tema (puede ser NULL si fue eliminado)
    topics.title AS topic_title,
    topics.topic,
    rounds.stage AS stage,
    topics.similarity_score,
    topics.similar_to,
    COUNT(votes.user_id) AS total_votes
FROM 
    topics
LEFT JOIN 
    topic_rounds ON topics.id = topic_rounds.topic_id 
LEFT JOIN 
    rounds ON topic_rounds.round_id = rounds.id
LEFT JOIN 
    votes ON topics.id = votes.topic_id AND votes.round_id = rounds.id
WHERE 
    topics.is_approved = TRUE -- Solo incluir temas aprobados
GROUP BY 
    topics.id, topics.user_id, topics.title, topics.topic, topics.similarity_score, 
    topics.similar_to, rounds.stage;

DELIMITER $$

CREATE EVENT IF NOT EXISTS rounds_events
ON SCHEDULE EVERY 1 DAY STARTS TIMESTAMP(CURRENT_DATE + INTERVAL 1 DAY, '00:00:00')
DO
BEGIN
    -- Finalizar rondas que han alcanzado su fecha de cierre
    UPDATE rounds
    SET status = 'finished'
    WHERE status = 'active' AND end_date <= NOW();

    -- Insertar finalistas para rondas que NO sean la final (2 temas más votados)
    INSERT INTO finalists (topic_id, round_id, votes, classified_at)
    SELECT topic_id, round_id, COUNT(user_id) AS total_votes, NOW()
    FROM votes
    WHERE round_id IN (SELECT id FROM rounds WHERE status = 'finished' AND final_round = FALSE)
    GROUP BY topic_id, round_id
    HAVING total_votes > 0  -- Asegurar que solo se seleccionen temas con votos
    ORDER BY round_id ASC, total_votes DESC
    LIMIT 2;

    -- Seleccionar un único ganador si es la ronda final
    INSERT INTO finalists (topic_id, round_id, votes, classified_at, winner)
    SELECT topic_id, round_id, COUNT(user_id) AS total_votes, NOW(), TRUE
    FROM votes
    WHERE round_id IN (SELECT id FROM rounds WHERE status = 'finished' AND final_round = TRUE)
    GROUP BY topic_id, round_id
    HAVING total_votes > 0  -- Asegurar que solo se seleccionen temas con votos
    ORDER BY total_votes DESC
    LIMIT 1;
    
END$$

DELIMITER ;

-- Activar eventos si no están activos
SET GLOBAL event_scheduler = ON;

-- Insertar un administrador si no existe
INSERT INTO users (username, email, password_hash, is_admin, registration_date)
SELECT 'Admin', 'admin@democrapp.com', 
    '$2y$10$vWZ5VLcQvp5IlprUsKt6Gud7Vo0msdU8ClR14Fux24xz336zlZ54e',
    TRUE, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@democrapp.com' AND is_admin = TRUE
);
