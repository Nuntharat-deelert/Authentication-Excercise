-- Database Lab: Personal Resume Website
-- Starter database only. Students must design the remaining tables themselves.


DROP TABLE IF EXISTS education;
DROP TABLE IF EXISTS profile;

CREATE TABLE profile (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    headline VARCHAR(200),
    email VARCHAR(150),
    phone VARCHAR(30),
    address VARCHAR(255),
    about_me TEXT,
    photo VARCHAR(255)
);

CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_id INT NOT NULL,
    degree VARCHAR(200) NOT NULL,
    institution VARCHAR(200) NOT NULL,
    start_year YEAR,
    end_year YEAR,
    FOREIGN KEY (profile_id) REFERENCES profile(id) ON DELETE CASCADE
);

INSERT INTO profile (full_name, headline, email, phone, address, about_me, photo)
VALUES (
    'Student Name',
    'Computer Science Student',
    'student@example.com',
    '08X-XXX-XXXX',
    'Chonburi, Thailand',
    'I am a student who is interested in database, web development, and artificial intelligence.',
    'student.jpg'
);

INSERT INTO education (profile_id, degree, institution, start_year, end_year)
VALUES
(1, 'Bachelor of Science in Example Program', 'Burapha University', 2024, 2027),
(1, 'High School', 'Example School', 2021, 2023);

-- ====================================================
-- STUDENT TASK
-- Design and create at least these two tables yourself:
-- 1) skill
-- 2) training
-- Add suitable PK, FK, data types, and sample data.
-- ====================================================
