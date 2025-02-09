-- Create database if it does not exist with UTF-8 BIN collation
CREATE DATABASE IF NOT EXISTS DemocrApp 
DEFAULT CHARACTER SET utf8 
DEFAULT COLLATE utf8_bin;

USE DemocrApp;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    email VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin UNIQUE NOT NULL,
    password_hash VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Create rounds table
CREATE TABLE IF NOT EXISTS rounds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    stage VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,  
    start_date DATETIME,
    end_date DATETIME,
    status ENUM('active', 'inactive', 'finished') CHARACTER SET utf8 COLLATE utf8_bin DEFAULT 'inactive',
    topics_per_round INT DEFAULT 10,
    votes_per_user INT DEFAULT 1
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Create topics table
CREATE TABLE IF NOT EXISTS topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_id INT DEFAULT NULL,
    title VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
    topic VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,  
    category VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT 'Uncategorized',
    similarity_score FLOAT DEFAULT 0, -- Stores similarity score (0-1)
    similar_to TEXT NULL, -- Now stores multiple topic IDs (comma-separated)
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (round_id) REFERENCES rounds(id) ON DELETE SET NULL
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Create votes table
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    topic_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (topic_id) REFERENCES topics(id)
) CHARACTER SET utf8 COLLATE utf8_bin;

-- Create a view to dynamically calculate the total votes per topic
CREATE OR REPLACE VIEW topic_votes AS
SELECT 
    topics.id AS topic_id,
    topics.title AS topic_title,
    topics.topic,
    topics.category,
    rounds.stage AS stage,
    topics.similarity_score,
    topics.similar_to,
    COUNT(votes.id) AS total_votes
FROM 
    topics
LEFT JOIN 
    votes ON topics.id = votes.topic_id
LEFT JOIN 
    rounds ON topics.round_id = rounds.id
GROUP BY 
    topics.id, topics.title, topics.topic, topics.category, rounds.stage, topics.similarity_score, topics.similar_to;

-- Create indexes for optimization
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_votes_user ON votes(user_id);
CREATE INDEX idx_votes_topic ON votes(topic_id);
CREATE INDEX idx_topics_similarity ON topics(similarity_score);
CREATE INDEX idx_topics_similar_to ON topics(similar_to);
CREATE INDEX idx_topics_category ON topics(category);

-- Insert an admin user if not exists
INSERT INTO users (username, email, password_hash, is_admin, registration_date)
SELECT 'Admin', 'admin@democrapp.com', MD5('admin123'), TRUE, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE email = 'admin@democrapp.com' AND is_admin = TRUE
);
