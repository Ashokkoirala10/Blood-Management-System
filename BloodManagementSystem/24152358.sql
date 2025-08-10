-- Create database
CREATE DATABASE IF NOT EXISTS sid24152358;
USE sid24152358;

-- USERS: base login table for all types (donor, seeker, admin)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('donor', 'seeker', 'admin','staff') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- DONORS table
CREATE TABLE IF NOT EXISTS donors (
    user_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15), 
    blood_group VARCHAR(5) NOT NULL,
    city VARCHAR(100) NOT NULL,
    profile_photo VARCHAR(255),
    national_id_number VARCHAR(50) NOT NULL,
    national_id_photo VARCHAR(255),
    last_donation_date DATE DEFAULT Null ,
    availability BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) 
);

-- SEEKERS table
CREATE TABLE IF NOT EXISTS seekers (
    user_id INT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15), 
    blood_group_needed VARCHAR(5) NOT NULL,
    city VARCHAR(100) NOT NULL,
    national_id_number VARCHAR(50) NOT NULL,
    national_id_photo VARCHAR(255),
    seeker_photo VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) 
);


CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT, 
    name VARCHAR(100) NOT NULL,
    photo VARCHAR(255),  -- Photo of the admin
    address TEXT,
    phone_number VARCHAR(15),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);



-- HOSPITALS (optional but useful for tracking offline stock)

CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city VARCHAR(100) NOT NULL,
    location VARCHAR(255), 
    photo VARCHAR(255)
);
--staff table
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    photo VARCHAR(255),
    address TEXT,
    phone_number VARCHAR(15),
    email VARCHAR(100),
    designation VARCHAR(100),
    created_by_admin_id INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    user_id INT UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_admin_id) REFERENCES users(id)
);




-- BLOOD STOCK per hospital
CREATE TABLE IF NOT EXISTS blood_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    blood_group VARCHAR(5) NOT NULL,
    quantity INT DEFAULT 0,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) 
);

-- BLOOD REQUESTS
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_id INT NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    units_required INT NOT NULL,
    city VARCHAR(100),
    status ENUM('pending', 'approved', 'fulfilled', 'rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (requester_id) REFERENCES users(id) 
);

-- DONATIONS LOG (actual donation events)
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT NOT NULL,
    seeker_id INT,
    donation_date DATE DEFAULT CURDATE(),
    blood_group VARCHAR(5),
    quantity INT DEFAULT 1,
    hospital_id INT,
    FOREIGN KEY (donor_id) REFERENCES donors(user_id),
    FOREIGN KEY (seeker_id) REFERENCES seekers(user_id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id)
);

-- FEEDBACK / ISSUES
CREATE TABLE IF NOT EXISTS issues_feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    subject VARCHAR(255),
    message TEXT,
    category ENUM('bug', 'suggestion', 'complaint', 'other') DEFAULT 'other',
    status ENUM('new', 'in_progress', 'resolved') DEFAULT 'new',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);



CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL, -- recipient (donor or seeker)
    role ENUM('donor', 'seeker') NOT NULL, -- so we know the type
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0, -- for tracking if notification has been seen
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password, role) VALUES
('admin1', 'admin1@gmail.com', '$2y$10$J.IMRD.CkrOawfWo.txxTuAW3Fp4yyvK3DCAu8/19UKYchlQeRt52', 'admin'),
('donor1', 'donor1@gmail.com', '$2y$10$.Bmf7HhOL.js0y4BnuJEFO2ellmJHYtnI7pd1UDDtEFdITeMIGWZq', 'donor'),
('seeker1', 'seeker1@gmail.com', '$2y$10$W7rRsKBvBy61rf2S35kUEO3Y7SVTsryZj71WnGaOZGhyyan5GcIgu', 'seeker'),
('staff1', 'staff1@gmail.com', '$2y$10$iJMmqlhB6lXSBVSONGKeGuCjiV6XHa9RHMm2hARXs8SK6u6MF.rPS', 'staff');


CREATE TABLE IF NOT EXISTS verify (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    otp VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


