
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