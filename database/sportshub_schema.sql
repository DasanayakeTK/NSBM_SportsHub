-- NSBM SportsHub - Master Database Schema
-- Run this file in phpMyAdmin to generate the sportshub_db database.

CREATE DATABASE IF NOT EXISTS sportshub_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE sportshub_db;

-- 1. Users Table (Handles both Students and Admins)
CREATE TABLE IF NOT EXISTS users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(100) NOT NULL UNIQUE,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Sports Clubs Table
CREATE TABLE IF NOT EXISTS sports_clubs (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL UNIQUE,
    description     TEXT,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Club Memberships (Links Users to Clubs)
CREATE TABLE IF NOT EXISTS club_memberships (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    club_id         INT NOT NULL,
    status          ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    UNIQUE KEY unique_student_club (student_id, club_id),
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (club_id) REFERENCES sports_clubs(id) ON DELETE CASCADE
);

-- 4. Training Sessions
CREATE TABLE IF NOT EXISTS training_sessions (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    club_id         INT NOT NULL,
    session_date    DATE NOT NULL,
    start_time      TIME NOT NULL,
    venue           VARCHAR(100) NOT NULL,
    FOREIGN KEY (club_id) REFERENCES sports_clubs(id) ON DELETE CASCADE
);

-- 5. Tournaments
CREATE TABLE IF NOT EXISTS tournaments (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(150) NOT NULL,
    sport_type      VARCHAR(50) NOT NULL,
    start_date      DATE NOT NULL
);

-- 6. Match Fixtures (Links Tournaments and two Clubs)
CREATE TABLE IF NOT EXISTS match_fixtures (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    tournament_id   INT NOT NULL,
    club_a_id       INT NOT NULL,
    club_b_id       INT NOT NULL,
    match_date      DATE NOT NULL,
    status          ENUM('scheduled', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id) ON DELETE CASCADE,
    FOREIGN KEY (club_a_id) REFERENCES sports_clubs(id) ON DELETE CASCADE,
    FOREIGN KEY (club_b_id) REFERENCES sports_clubs(id) ON DELETE CASCADE
);

-- 7. Facility Bookings
CREATE TABLE IF NOT EXISTS facility_bookings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    student_id      INT NOT NULL,
    facility_name   VARCHAR(100) NOT NULL,
    booking_date    DATE NOT NULL,
    time_slot       VARCHAR(50) NOT NULL,
    status          ENUM('pending', 'approved') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 8. Equipment Inventory
CREATE TABLE IF NOT EXISTS equipment_inventory (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    item_name           VARCHAR(100) NOT NULL,
    total_quantity      INT NOT NULL DEFAULT 0,
    available_quantity  INT NOT NULL DEFAULT 0
);

-- ==========================================
-- SAMPLE DATA FOR TESTING
-- Passwords for all accounts are set to "password"
-- ==========================================

INSERT INTO users (full_name, email, password_hash, role) VALUES
('System Administrator', 'admin@nsbm.ac.lk',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Kasun Perera',         'kasun@nsbm.ac.lk',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'),
('Nethmi Silva',         'nethmi@nsbm.ac.lk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student');

INSERT INTO sports_clubs (name, description) VALUES
('NSBM Cricket Club',   'The university cricket team. We practise twice a week and play in inter-university leagues.'),
('NSBM Football Club',  'Open to all students who love football. Beginners are welcome!'),
('NSBM Badminton Club', 'Friendly and competitive badminton for all skill levels.');

INSERT INTO club_memberships (student_id, club_id, status) VALUES
(2, 1, 'approved'),
(2, 2, 'pending'),
(3, 3, 'approved'),
(3, 2, 'rejected');

INSERT INTO training_sessions (club_id, session_date, start_time, venue) VALUES
(1, '2026-11-02', '16:00:00', 'Main Cricket Ground'),
(2, '2026-11-03', '17:30:00', 'University Football Field'),
(3, '2026-11-04', '15:00:00', 'Indoor Sports Hall');

INSERT INTO tournaments (name, sport_type, start_date) VALUES
('NSBM Inter-Club Championship 2026', 'Mixed', '2026-12-01');

INSERT INTO match_fixtures (tournament_id, club_a_id, club_b_id, match_date, status) VALUES
(1, 1, 2, '2026-12-02', 'scheduled'),
(1, 2, 3, '2026-12-03', 'scheduled');

INSERT INTO facility_bookings (student_id, facility_name, booking_date, time_slot, status) VALUES
(2, 'Basketball Court', '2026-11-05', '04:00 PM - 05:00 PM', 'approved'),
(3, 'Gymnasium',        '2026-11-06', '06:00 AM - 07:00 AM', 'pending');

INSERT INTO equipment_inventory (item_name, total_quantity, available_quantity) VALUES
('Cricket Bat',      10, 8),
('Football',         15, 12),
('Badminton Racket', 20, 17),
('Basketball',       8,  8),
('Volleyball Net',   3,  2);