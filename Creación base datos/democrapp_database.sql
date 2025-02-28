-- Borrar la base de datos si ya existe
DROP DATABASE IF EXISTS DemocrApp;

-- Crear la base de datos con codificación UTF-8
CREATE DATABASE IF NOT EXISTS DemocrApp 
DEFAULT CHARACTER SET utf8 
DEFAULT COLLATE utf8_bin;

USE DemocrApp;

-- Tabla de Usuarios
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    email VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin UNIQUE NOT NULL,
    password_hash VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Tabla de Rondas 
CREATE TABLE IF NOT EXISTS rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stage ENUM('propuestas', 'clasificatoria', 'final') NOT NULL,
    start_date DATETIME,
    end_date DATETIME,
    status ENUM('active', 'inactive', 'finished') DEFAULT 'inactive',
  --  topics_per_round INT DEFAULT 10,
    votes_per_user INT DEFAULT 3,
    final_round BOOLEAN DEFAULT FALSE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Tabla de Temas (Topics)
CREATE TABLE IF NOT EXISTS topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    topic VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    description TEXT NULL,
    similarity_score FLOAT DEFAULT 0,
    similar_to TEXT NULL,
    is_approved BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    finalist BOOLEAN DEFAULT FALSE,
    winner BOOLEAN DEFAULT FALSE,
    disqualified BOOLEAN DEFAULT FALSE,
    CONSTRAINT unique_topic UNIQUE (topic)
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Tabla intermedia para relacionar temas con rondas
CREATE TABLE IF NOT EXISTS topic_rounds (
    topic_id INT NOT NULL,
    round_id INT NOT NULL,
    PRIMARY KEY (topic_id, round_id),
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Tabla de Votos
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT DEFAULT NULL,
    topic_id INT NOT NULL,
    round_id INT NOT NULL,
    value INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (user_id, topic_id, round_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Tabla de Historial de Votos (para almacenar resultados pasados)
CREATE TABLE IF NOT EXISTS votes_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    round_id INT NOT NULL,
    total_points INT DEFAULT 0,
    total_votes INT DEFAULT 0,
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE CASCADE
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Vista para ver resultados en tiempo real
CREATE OR REPLACE VIEW topic_votes AS
SELECT 
    topics.id AS topic_id,
    topics.user_id, 
    topics.title AS topic_title,
    topics.topic,
    rounds.stage AS stage,
    topics.similarity_score,
    topics.similar_to,
    SUM(votes.value) AS total_votes
FROM 
    topics
LEFT JOIN topic_rounds ON topics.id = topic_rounds.topic_id 
LEFT JOIN rounds ON topic_rounds.round_id = rounds.id
LEFT JOIN votes ON topics.id = votes.topic_id AND votes.round_id = rounds.id
WHERE 
    topics.is_approved = TRUE AND topics.disqualified = FALSE
GROUP BY 
    topics.id, topics.user_id, topics.title, topics.topic, topics.similarity_score, 
    topics.similar_to, rounds.stage;

DELIMITER $$

-- TRIGGER 1: Registrar Historial de Votos al Finalizar una Ronda
CREATE TRIGGER after_round_finished
AFTER UPDATE ON rounds
FOR EACH ROW
BEGIN
    IF OLD.status != 'finished' AND NEW.status = 'finished' THEN
        INSERT INTO votes_history (topic_id, round_id, total_votes, total_points)
        SELECT topic_id, NEW.id, COUNT(*), SUM(value)
        FROM votes
        WHERE round_id = NEW.id
        GROUP BY topic_id;
    END IF;
END$$

-- TRIGGER 2: Selección de Finalistas en ClasificatoriaDELIMITER $$

CREATE TRIGGER select_finalists
AFTER UPDATE ON rounds
FOR EACH ROW
BEGIN
    DECLARE second_place_score INT DEFAULT 0;

    -- Solo ejecuta si la ronda cambia a "finished"
    IF OLD.status != 'finished' AND NEW.status = 'finished' THEN
        
        -- Obtener la puntuación del segundo clasificado
        SELECT DISTINCT SUM(value) INTO second_place_score
        FROM votes
        WHERE round_id = NEW.id
        GROUP BY topic_id
        ORDER BY SUM(value) DESC
        LIMIT 1 OFFSET 1;

        -- Actualizar el primer y segundo clasificado + empatados con el segundo
        UPDATE topics
        SET finalist = TRUE
        WHERE id IN (
            SELECT topic_id 
            FROM votes
            WHERE round_id = NEW.id
            GROUP BY topic_id
            HAVING SUM(value) >= second_place_score
        );

        -- Actualizar añadir el resto de temas como descalificados
        UPDATE topics
        SET disqualified = TRUE
        WHERE id IN (
            SELECT topic_id 
            FROM votes
            WHERE round_id = NEW.id
            GROUP BY topic_id
            HAVING SUM(value) < second_place_score
        );

    END IF;
END$$

-- TRIGGER 3: Selección del Ganador en la Final
CREATE TRIGGER after_round_final
AFTER UPDATE ON rounds
FOR EACH ROW
BEGIN
    IF OLD.status != 'finished' AND NEW.status = 'finished' AND NEW.stage = 'final' THEN
        UPDATE topics SET winner = TRUE
        WHERE id = (
            -- Selecciona el ganador con desempates
            SELECT topic_id FROM votes 
            WHERE round_id = NEW.id
            GROUP BY topic_id
            ORDER BY 
                SUM(value) DESC, -- Primero, mayor puntaje en la final
                (SELECT SUM(total_points) FROM votes_history WHERE topic_id = votes.topic_id) DESC, -- Segundo, puntaje acumulado
                (SELECT SUM(total_votes) FROM votes_history WHERE topic_id = votes.topic_id) DESC -- Tercero, cantidad de votos
            LIMIT 1
        );
    END IF;
END$$

-- TRIGGER 4: Cambiar end_date de la última ronda
CREATE TRIGGER actualizar_end_date
BEFORE UPDATE ON rounds
FOR EACH ROW
BEGIN
    -- Si la ronda se está marcando como 'finished' y no lo estaba antes
    IF NEW.status = 'finished' AND OLD.status != 'finished' THEN
        SET NEW.end_date = NOW();
    END IF;
END $$

DELIMITER ;

-- EVENTOS
DELIMITER $$

CREATE EVENT IF NOT EXISTS activate_rounds_event
ON SCHEDULE EVERY 5 MINUTE 
DO
BEGIN
    UPDATE rounds
    SET status = 'active'
    WHERE status = 'inactive' AND start_date <= NOW();
END$$

CREATE EVENT IF NOT EXISTS finalize_rounds_event
ON SCHEDULE EVERY 5 MINUTE 
DO
BEGIN
    UPDATE rounds
    SET status = 'finished'
    WHERE status = 'active' AND end_date <= NOW();
END$$

DELIMITER ;

SET GLOBAL event_scheduler = ON;

-- Insertar un administrador por defecto
INSERT INTO users (username, email, password_hash, is_admin, registration_date)
SELECT 'Admin', 'admin@democrapp.com', 
    '$2y$10$vWZ5VLcQvp5IlprUsKt6Gud7Vo0msdU8ClR14Fux24xz336zlZ54e',
    TRUE, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@democrapp.com' AND is_admin = TRUE
);
