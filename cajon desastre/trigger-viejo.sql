  /* TRIGGER */
  /*   DELIMITER $$

    CREATE TRIGGER after_round_update
    AFTER UPDATE ON rounds
    FOR EACH ROW
    BEGIN
        -- Solo ejecutar si la ronda cambió a "finished"
        IF OLD.status != 'finished' AND NEW.status = 'finished' THEN
            IF NEW.stage = 'propuestas' THEN
                    RETURN;

            ELSE IF NEW.stage = 'clasificatoria' OR NEW.stage = 'desempate' THEN
                INSERT INTO finalists (topic_id, round_id, votes, classified_at)
                SELECT topic_id, NEW.id, SUM(value) AS total_votes, NOW()
                FROM votes
                WHERE round_id = NEW.id
                GROUP BY topic_id
                HAVING total_votes > 0  
                ORDER BY total_votes DESC
                LIMIT 2;

            -- Si la ronda es "final", selecciona el tema con más votos y lo marca como ganador
            ELSE IF NEW.stage = 'final' THEN
                INSERT INTO finalists (topic_id, round_id, votes, classified_at, winner)
                SELECT topic_id, NEW.id, SUM(value) AS total_votes, NOW(), TRUE
                FROM votes
                WHERE round_id = NEW.id
                GROUP BY topic_id
                HAVING total_votes > 0  
                ORDER BY total_votes DESC
                LIMIT 1;
            END IF;

        END IF;
    END;

    DELIMITER $$
 */