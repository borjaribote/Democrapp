vale entonces necesito:

1º el trigger de la ronda de clasificados. 
Lo dicho se clasifican todos los que empaten con el 1º o con el 2º en cantidad de puntos, no en cantidad de votos. 

2º Tigger de la ronda final: 
 - Gana el que mas puntos saque.
 - Si hay empate gana el que mas puntos haya obtenido en rondas previas. 
 - Si aun así hay dos temas con los mismos puntos totales gana el que mas votos haya tenido. 
 - Y si los dos tienen los mismos votos, ganan los dos temas. 

3º dame de nuevo la nueva tabla votes_history y su trigger. 

4º No me haría falta la tabla desempate y estoy pensando eliminar tambien clasificados y simplemente poner un campo que sea finalista y sea true o false y otro campo que sea winner. 


Para que no me haga mucho lío, mandamelo todo hecho please. 
Actualizame la base de datos: 
    -- Crear la base de datos con codificación UTF-8 BIN
    DROP DATABASE IF EXISTS DemocrApp;

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
        stage ENUM('propuestas', 'clasificatoria', 'final', 'desempate') NOT NULL,
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
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
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

    -- Crear la tabla de votos con la columna `value` en vez de `points`
    CREATE TABLE IF NOT EXISTS votes (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        user_id INT DEFAULT NULL,
        topic_id INT NOT NULL,
        round_id INT NOT NULL,
        value INT NOT NULL, -- Almacena el valor del voto (1, 2 o 3)
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

    CREATE TABLE IF NOT EXISTS ties (
        id INT AUTO_INCREMENT PRIMARY KEY,
        topic_id INT NOT NULL,
        round_id INT NOT NULL,
        votes INT DEFAULT 0,
        tie_detected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
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
        SUM(votes.value) AS total_votes -- Usa SUM en vez de COUNT para reflejar correctamente los valores de voto
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

/* 🏆 TRIGGER 1: Manejo de Clasificatoria y Desempate */
CREATE TRIGGER after_round_update_classificatoria
AFTER UPDATE ON rounds
FOR EACH ROW
BEGIN
    DECLARE first_place_score INT DEFAULT 0;
    DECLARE second_place_score INT DEFAULT 0;
    DECLARE first_count INT DEFAULT 0;
    DECLARE second_count INT DEFAULT 0;

    IF OLD.status != 'finished' AND NEW.status = 'finished' 
       AND (NEW.stage = 'clasificatoria' OR NEW.stage = 'desempate') THEN

        -- Obtener la puntuación más alta
        SELECT SUM(value) INTO first_place_score
        FROM votes
        WHERE round_id = NEW.id
        GROUP BY topic_id
        ORDER BY SUM(value) DESC
        LIMIT 1;

        -- Obtener la segunda puntuación más alta
        SELECT SUM(value) INTO second_place_score
        FROM votes
        WHERE round_id = NEW.id
        GROUP BY topic_id
        ORDER BY SUM(value) DESC
        LIMIT 1 OFFSET 1;

        -- Contar cuántos temas tienen la puntuación más alta
        SELECT COUNT(*) INTO first_count
        FROM (
            SELECT topic_id FROM votes
            WHERE round_id = NEW.id
            GROUP BY topic_id
            HAVING SUM(value) = first_place_score
        ) AS subquery;

        -- Contar cuántos temas tienen la segunda puntuación más alta
        SELECT COUNT(*) INTO second_count
        FROM (
            SELECT topic_id FROM votes
            WHERE round_id = NEW.id
            GROUP BY topic_id
            HAVING SUM(value) = second_place_score
        ) AS subquery;

        -- 🔥 Caso 1: Empate en primer lugar → Todos los empatados pasan a `finalists`
        IF first_count > 1 THEN
            INSERT INTO finalists (topic_id, round_id, votes, classified_at)
            SELECT topic_id, NEW.id, SUM(value), NOW()
            FROM votes
            WHERE round_id = NEW.id
            GROUP BY topic_id
            HAVING SUM(value) = first_place_score;

        -- 🔥 Caso 2: No hay empate en primer lugar → Clasificar 1º y 2º, empates en 2º van a `ties`
        ELSE
            -- Insertar los dos primeros clasificados
            INSERT INTO finalists (topic_id, round_id, votes, classified_at)
            SELECT topic_id, NEW.id, SUM(value), NOW()
            FROM votes
            WHERE round_id = NEW.id
            GROUP BY topic_id
            ORDER BY SUM(value) DESC
            LIMIT 2;

            -- Si hay empate en segundo lugar, insertar en `ties`
            IF second_count > 1 THEN
                INSERT INTO ties (topic_id, round_id, votes, tie_detected_at)
                SELECT topic_id, NEW.id, SUM(value), NOW()
                FROM votes
                WHERE round_id = NEW.id
                GROUP BY topic_id
                HAVING SUM(value) = second_place_score;
            END IF;
        END IF;

    END IF;
END$$

/* 🏆 TRIGGER 2: Manejo de la Final */
CREATE TRIGGER after_round_update_final
AFTER UPDATE ON rounds
FOR EACH ROW
BEGIN
    IF OLD.status != 'finished' AND NEW.status = 'finished' AND NEW.stage = 'final' THEN
        -- Insertar solo el tema con más votos como ganador
        INSERT INTO finalists (topic_id, round_id, votes, classified_at, winner)
        SELECT topic_id, NEW.id, SUM(value), NOW(), TRUE
        FROM votes
        WHERE round_id = NEW.id
        GROUP BY topic_id
        ORDER BY SUM(value) DESC
        LIMIT 1;
    END IF;
END$$

DELIMITER ;


    /* EVENTOS */
    DELIMITER $$

    /* Evento para ACTIVAR rondas cuando llegue la fecha de inicio */
    CREATE EVENT IF NOT EXISTS activate_rounds_event
    ON SCHEDULE EVERY 5 MINUTE 
    DO
    BEGIN
        UPDATE rounds
        SET status = 'active'
        WHERE status = 'inactive' AND start_date <= NOW();
    END$$

    /* Evento para FINALIZAR rondas cuando llegue la fecha de fin */
    CREATE EVENT IF NOT EXISTS finalize_rounds_event
    ON SCHEDULE EVERY 5 MINUTE 
    DO
    BEGIN
        -- Finalizar rondas que han alcanzado su fecha de cierre
        UPDATE rounds
        SET status = 'finished'
        WHERE status = 'active' AND end_date <= NOW();
    END$$

    DELIMITER ;

    SET GLOBAL event_scheduler = ON;


    -- Insertar un administrador si no existe
    INSERT INTO users (username, email, password_hash, is_admin, registration_date)
    SELECT 'Admin', 'admin@democrapp.com', 
        '$2y$10$vWZ5VLcQvp5IlprUsKt6Gud7Vo0msdU8ClR14Fux24xz336zlZ54e',
        TRUE, NOW()
    WHERE NOT EXISTS (
        SELECT 1 FROM users WHERE email = 'admin@democrapp.com' AND is_admin = TRUE
    );


Y aparte dame las querys que necesito. 

Si consideras que hay algo que hago mal o que va a ser mas facil de otra manera consultamelo o sugiereme algún cambio antes de pasarme ningún código. 