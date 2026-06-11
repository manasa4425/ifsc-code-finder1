SELECT DISTINCT `bank_name` FROM `branches`;
SELECT DISTINCT bank_name FROM branches;

-- DROP TABLE if exists
DROP TABLE IF EXISTS branches;

-- CREATE TABLE
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100),
    state VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    branch VARCHAR(100),
    ifsc_code VARCHAR(20)
);







-- INSERT DATA FOR 6 BANKS x 6 STATES x 6 DISTRICTS (36 branches per bank)

-- BANK: Canara
INSERT INTO branches (bank_name, state, district, city, branch, ifsc_code) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Branch','CNRB0001004'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001005'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001006'),

('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0001010'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','Gandhipuram','CNRB0001011'),
('Canara','Tamil Nadu','Madurai','Madurai','Madurai Main','CNRB0001012'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0001013'),
('Canara','Tamil Nadu','Tirunelveli','Tirunelveli','Tirunelveli Main','CNRB0001014'),
('Canara','Tamil Nadu','Vellore','Vellore','Vellore Branch','CNRB0001015'),

('Canara','Maharashtra','Mumbai','Mumbai','Bandra','CNRB0001020'),
('Canara','Maharashtra','Pune','Pune','Shivajinagar','CNRB0001021'),
('Canara','Maharashtra','Nagpur','Nagpur','Nagpur Main','CNRB0001022'),
('Canara','Maharashtra','Nashik','Nashik','Nashik Branch','CNRB0001023'),
('Canara','Maharashtra','Thane','Thane','Thane Branch','CNRB0001024'),
('Canara','Maharashtra','Aurangabad','Aurangabad','Aurangabad Main','CNRB0001025'),

('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Statue Branch','CNRB0001030'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0001031'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0001032'),
('Canara','Kerala','Thrissur','Thrissur','Thrissur Main','CNRB0001033'),
('Canara','Kerala','Alappuzha','Alappuzha','Alappuzha Branch','CNRB0001034'),
('Canara','Kerala','Kollam','Kollam','Kollam Branch','CNRB0001035'),

('Canara','Uttar Pradesh','Lucknow','Lucknow','Hazratganj','CNRB0001040'),
('Canara','Uttar Pradesh','Kanpur','Kanpur','Civil Lines','CNRB0001041'),
('Canara','Uttar Pradesh','Varanasi','Varanasi','Dashashwamedh','CNRB0001042'),
('Canara','Uttar Pradesh','Agra','Agra','Taj Mahal Road','CNRB0001043'),
('Canara','Uttar Pradesh','Meerut','Meerut','Meerut Main','CNRB0001044'),
('Canara','Uttar Pradesh','Ghaziabad','Ghaziabad','Ghaziabad Main','CNRB0001045'),

('Canara','West Bengal','Kolkata','Kolkata','Park Street','CNRB0001050'),
('Canara','West Bengal','Howrah','Howrah','Howrah Main','CNRB0001051'),
('Canara','West Bengal','Darjeeling','Darjeeling','Mall Road','CNRB0001052'),
('Canara','West Bengal','Siliguri','Siliguri','Siliguri Branch','CNRB0001053'),
('Canara','West Bengal','Asansol','Asansol','Asansol Main','CNRB0001054'),
('Canara','West Bengal','Durgapur','Durgapur','Durgapur Branch','CNRB0001055');










CREATE TABLE `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(100),
    `state` VARCHAR(100),
    `district` VARCHAR(100),
    `city` VARCHAR(100),
    `branch` VARCHAR(100),
    `ifsc_code` VARCHAR(20)
);




USE bankifsc;

-- Update admin user (if exists) or insert new one
-- Password: Admin@123 (you can change this)
INSERT INTO users (username, email, password, full_name, role, phone, two_factor_enabled, created_at) 
VALUES ('admin', 'admin@bankifsc.com', 'Admin@123', 'Administrator', 'admin', '9999999999', 1, NOW())
ON DUPLICATE KEY UPDATE 
    role = 'admin',
    password = 'Admin@123',
    two_factor_enabled = 1;

-- Make sure the user is admin
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@bankifsc.com';

-- Verify
SELECT id, username, email, role, two_factor_enabled FROM users WHERE role = 'admin';




USE bankifsc;

-- Drop existing tables if they exist (to recreate with correct structure)
DROP TABLE IF EXISTS `security_alerts`;
DROP TABLE IF EXISTS `known_devices`;
DROP TABLE IF EXISTS `login_history`;
DROP TABLE IF EXISTS `users`;

-- Recreate users table with correct columns
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123 - in production use password_hash())
INSERT INTO users (username, email, password, full_name, role, phone) VALUES
('john_doe', 'john@example.com', 'User@123', 'John Doe', 'user', '9876543210'),
('jane_smith', 'jane@example.com', 'User@123', 'Jane Smith', 'user', '9876543211'),
('admin', 'admin@bank.com', 'User@123', 'Admin User', 'admin', '9876543212');

-- Verify data inserted
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'login_history', COUNT(*) FROM login_history
UNION ALL
SELECT 'known_devices', COUNT(*) FROM known_devices
UNION ALL
SELECT 'security_alerts', COUNT(*) FROM security_alerts;




-- Create database
CREATE DATABASE IF NOT EXISTS `bankifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bankifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;



define('DB_HOST', '3308');
define('DB_NAME', 'bankifsc');
define('DB_USER', 'root');
define('DB_PASS', '');


define('DB_HOST', '3308');
define('DB_NAME', 'bankifsc');
define('DB_USER', 'root');
define('DB_PASS', '');


CREATE DATABASE bank_ifsc;
USE bank_ifsc;



USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');




-- DROP TABLE if exists
DROP TABLE IF EXISTS branches;

-- CREATE TABLE
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100),
    state VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    branch VARCHAR(100),
    ifsc_code VARCHAR(20)
);

-- INSERT DATA FOR 6 BANKS x 6 STATES x 6 DISTRICTS (36 branches per bank)

-- BANK: Canara
INSERT INTO branches (bank_name, state, district, city, branch, ifsc_code) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Branch','CNRB0001004'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001005'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001006'),

('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0001010'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','Gandhipuram','CNRB0001011'),
('Canara','Tamil Nadu','Madurai','Madurai','Madurai Main','CNRB0001012'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0001013'),
('Canara','Tamil Nadu','Tirunelveli','Tirunelveli','Tirunelveli Main','CNRB0001014'),
('Canara','Tamil Nadu','Vellore','Vellore','Vellore Branch','CNRB0001015'),

('Canara','Maharashtra','Mumbai','Mumbai','Bandra','CNRB0001020'),
('Canara','Maharashtra','Pune','Pune','Shivajinagar','CNRB0001021'),
('Canara','Maharashtra','Nagpur','Nagpur','Nagpur Main','CNRB0001022'),
('Canara','Maharashtra','Nashik','Nashik','Nashik Branch','CNRB0001023'),
('Canara','Maharashtra','Thane','Thane','Thane Branch','CNRB0001024'),
('Canara','Maharashtra','Aurangabad','Aurangabad','Aurangabad Main','CNRB0001025'),

('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Statue Branch','CNRB0001030'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0001031'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0001032'),
('Canara','Kerala','Thrissur','Thrissur','Thrissur Main','CNRB0001033'),
('Canara','Kerala','Alappuzha','Alappuzha','Alappuzha Branch','CNRB0001034'),
('Canara','Kerala','Kollam','Kollam','Kollam Branch','CNRB0001035'),

('Canara','Uttar Pradesh','Lucknow','Lucknow','Hazratganj','CNRB0001040'),
('Canara','Uttar Pradesh','Kanpur','Kanpur','Civil Lines','CNRB0001041'),
('Canara','Uttar Pradesh','Varanasi','Varanasi','Dashashwamedh','CNRB0001042'),
('Canara','Uttar Pradesh','Agra','Agra','Taj Mahal Road','CNRB0001043'),
('Canara','Uttar Pradesh','Meerut','Meerut','Meerut Main','CNRB0001044'),
('Canara','Uttar Pradesh','Ghaziabad','Ghaziabad','Ghaziabad Main','CNRB0001045'),

('Canara','West Bengal','Kolkata','Kolkata','Park Street','CNRB0001050'),
('Canara','West Bengal','Howrah','Howrah','Howrah Main','CNRB0001051'),
('Canara','West Bengal','Darjeeling','Darjeeling','Mall Road','CNRB0001052'),
('Canara','West Bengal','Siliguri','Siliguri','Siliguri Branch','CNRB0001053'),
('Canara','West Bengal','Asansol','Asansol','Asansol Main','CNRB0001054'),
('Canara','West Bengal','Durgapur','Durgapur','Durgapur Branch','CNRB0001055');



CREATE DATABASE bank_ifsc;
USE bank_ifsc;


SELECT DISTINCT bank_name FROM branches;




INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('SBI','Karnataka','Bangalore','Bangalore','Indiranagar','SBIN0001001'),
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Union Bank','Karnataka','Bangalore','Bangalore','Brigade Road','UBIN0001001'),
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Bank of India','Karnataka','Bangalore','Bangalore','Commercial Street','BKID0001001');






CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50)
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    ip_address VARCHAR(50),
    success BOOLEAN,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(50) UNIQUE
);






USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');


CREATE TABLE `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(100),
    `state` VARCHAR(100),
    `district` VARCHAR(100),
    `city` VARCHAR(100),
    `branch` VARCHAR(100),
    `ifsc_code` VARCHAR(20)
);





-- DROP TABLE if exists
DROP TABLE IF EXISTS branches;

-- CREATE TABLE
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100),
    state VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    branch VARCHAR(100),
    ifsc_code VARCHAR(20)
);

-- INSERT DATA FOR 6 BANKS x 6 STATES x 6 DISTRICTS (36 branches per bank)

-- BANK: Canara
INSERT INTO branches (bank_name, state, district, city, branch, ifsc_code) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Branch','CNRB0001004'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001005'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001006'),

('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0001010'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','Gandhipuram','CNRB0001011'),
('Canara','Tamil Nadu','Madurai','Madurai','Madurai Main','CNRB0001012'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0001013'),
('Canara','Tamil Nadu','Tirunelveli','Tirunelveli','Tirunelveli Main','CNRB0001014'),
('Canara','Tamil Nadu','Vellore','Vellore','Vellore Branch','CNRB0001015'),

('Canara','Maharashtra','Mumbai','Mumbai','Bandra','CNRB0001020'),
('Canara','Maharashtra','Pune','Pune','Shivajinagar','CNRB0001021'),
('Canara','Maharashtra','Nagpur','Nagpur','Nagpur Main','CNRB0001022'),
('Canara','Maharashtra','Nashik','Nashik','Nashik Branch','CNRB0001023'),
('Canara','Maharashtra','Thane','Thane','Thane Branch','CNRB0001024'),
('Canara','Maharashtra','Aurangabad','Aurangabad','Aurangabad Main','CNRB0001025'),

('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Statue Branch','CNRB0001030'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0001031'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0001032'),
('Canara','Kerala','Thrissur','Thrissur','Thrissur Main','CNRB0001033'),
('Canara','Kerala','Alappuzha','Alappuzha','Alappuzha Branch','CNRB0001034'),
('Canara','Kerala','Kollam','Kollam','Kollam Branch','CNRB0001035'),

('Canara','Uttar Pradesh','Lucknow','Lucknow','Hazratganj','CNRB0001040'),
('Canara','Uttar Pradesh','Kanpur','Kanpur','Civil Lines','CNRB0001041'),
('Canara','Uttar Pradesh','Varanasi','Varanasi','Dashashwamedh','CNRB0001042'),
('Canara','Uttar Pradesh','Agra','Agra','Taj Mahal Road','CNRB0001043'),
('Canara','Uttar Pradesh','Meerut','Meerut','Meerut Main','CNRB0001044'),
('Canara','Uttar Pradesh','Ghaziabad','Ghaziabad','Ghaziabad Main','CNRB0001045'),

('Canara','West Bengal','Kolkata','Kolkata','Park Street','CNRB0001050'),
('Canara','West Bengal','Howrah','Howrah','Howrah Main','CNRB0001051'),
('Canara','West Bengal','Darjeeling','Darjeeling','Mall Road','CNRB0001052'),
('Canara','West Bengal','Siliguri','Siliguri','Siliguri Branch','CNRB0001053'),
('Canara','West Bengal','Asansol','Asansol','Asansol Main','CNRB0001054'),
('Canara','West Bengal','Durgapur','Durgapur','Durgapur Branch','CNRB0001055');







SELECT DISTINCT bank_name FROM branches;








INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('SBI','Karnataka','Bangalore','Bangalore','Indiranagar','SBIN0001001'),
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Union Bank','Karnataka','Bangalore','Bangalore','Brigade Road','UBIN0001001'),
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Bank of India','Karnataka','Bangalore','Bangalore','Commercial Street','BKID0001001');

















SELECT DISTINCT `bank_name` FROM `branches`;














SELECT DISTINCT `bank_name` FROM `branches`;










define('DB_HOST', 'localhost');
define('DB_NAME', 'bank_ifsc');
define('DB_USER', 'root');
define('DB_PASS', '');CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20)
);

-- ------------------------
-- Canara Bank - 6 states Ã— 6 districts = 36 rows
-- ------------------------
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006');

-- ------------------------
-- SBI Bank - same structure 6Ã—6 = 36 rows
-- ------------------------
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006');













CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20)
);

-- ==========================
-- 1. Canara Bank
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006');

-- ==========================
-- 2. SBI Bank
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006');

-- ==========================
-- 3. Bank of Baroda
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006');

-- ==========================
-- 4. Union Bank
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006');

-- ==========================
-- 5. Punjab National Bank (PNB)
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006');

-- ==========================
-- 6. Bank of India
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');







USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');













USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');



















CREATE DATABASE bank_ifsc;
USE bank_ifsc;










CREATE DATABASE bank_ifsc;
USE bank_ifsc;









USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');
















-- Create database
CREATE DATABASE IF NOT EXISTS `bankifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bankifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;




















-- Create database
CREATE DATABASE IF NOT EXISTS `bankifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bankifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;





















-- Create database
CREATE DATABASE IF NOT EXISTS `bank_ifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bank_ifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;


















USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');




























USE bankifsc;

-- Update admin user (if exists) or insert new one
-- Password: Admin@123 (you can change this)
INSERT INTO users (username, email, password, full_name, role, phone, two_factor_enabled, created_at) 
VALUES ('admin', 'admin@bankifsc.com', 'Admin@123', 'Administrator', 'admin', '9999999999', 1, NOW())
ON DUPLICATE KEY UPDATE 
    role = 'admin',
    password = 'Admin@123',
    two_factor_enabled = 1;

-- Make sure the user is admin
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@bankifsc.com';

-- Verify
SELECT id, username, email, role, two_factor_enabled FROM users WHERE role = 'admin';


































USE bankifsc;

-- Update admin user (if exists) or insert new one
-- Password: Admin@123 (you can change this)
INSERT INTO users (username, email, password, full_name, role, phone, two_factor_enabled, created_at) 
VALUES ('admin', 'admin@bankifsc.com', 'Admin@123', 'Administrator', 'admin', '9999999999', 1, NOW())
ON DUPLICATE KEY UPDATE 
    role = 'admin',
    password = 'Admin@123',
    two_factor_enabled = 1;

-- Make sure the user is admin
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@bankifsc.com';

-- Verify
SELECT id, username, email, role, two_factor_enabled FROM users WHERE role = 'admin';


























USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');






























CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50)
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    ip_address VARCHAR(50),
    success BOOLEAN,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(50) UNIQUE
);





























































































































































































SELECT DISTINCT bank_name FROM branches;

-- DROP TABLE if exists
DROP TABLE IF EXISTS branches;

-- CREATE TABLE
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100),
    state VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    branch VARCHAR(100),
    ifsc_code VARCHAR(20)
);







-- INSERT DATA FOR 6 BANKS x 6 STATES x 6 DISTRICTS (36 branches per bank)

-- BANK: Canara
INSERT INTO branches (bank_name, state, district, city, branch, ifsc_code) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Branch','CNRB0001004'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001005'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001006'),

('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0001010'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','Gandhipuram','CNRB0001011'),
('Canara','Tamil Nadu','Madurai','Madurai','Madurai Main','CNRB0001012'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0001013'),
('Canara','Tamil Nadu','Tirunelveli','Tirunelveli','Tirunelveli Main','CNRB0001014'),
('Canara','Tamil Nadu','Vellore','Vellore','Vellore Branch','CNRB0001015'),

('Canara','Maharashtra','Mumbai','Mumbai','Bandra','CNRB0001020'),
('Canara','Maharashtra','Pune','Pune','Shivajinagar','CNRB0001021'),
('Canara','Maharashtra','Nagpur','Nagpur','Nagpur Main','CNRB0001022'),
('Canara','Maharashtra','Nashik','Nashik','Nashik Branch','CNRB0001023'),
('Canara','Maharashtra','Thane','Thane','Thane Branch','CNRB0001024'),
('Canara','Maharashtra','Aurangabad','Aurangabad','Aurangabad Main','CNRB0001025'),

('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Statue Branch','CNRB0001030'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0001031'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0001032'),
('Canara','Kerala','Thrissur','Thrissur','Thrissur Main','CNRB0001033'),
('Canara','Kerala','Alappuzha','Alappuzha','Alappuzha Branch','CNRB0001034'),
('Canara','Kerala','Kollam','Kollam','Kollam Branch','CNRB0001035'),

('Canara','Uttar Pradesh','Lucknow','Lucknow','Hazratganj','CNRB0001040'),
('Canara','Uttar Pradesh','Kanpur','Kanpur','Civil Lines','CNRB0001041'),
('Canara','Uttar Pradesh','Varanasi','Varanasi','Dashashwamedh','CNRB0001042'),
('Canara','Uttar Pradesh','Agra','Agra','Taj Mahal Road','CNRB0001043'),
('Canara','Uttar Pradesh','Meerut','Meerut','Meerut Main','CNRB0001044'),
('Canara','Uttar Pradesh','Ghaziabad','Ghaziabad','Ghaziabad Main','CNRB0001045'),

('Canara','West Bengal','Kolkata','Kolkata','Park Street','CNRB0001050'),
('Canara','West Bengal','Howrah','Howrah','Howrah Main','CNRB0001051'),
('Canara','West Bengal','Darjeeling','Darjeeling','Mall Road','CNRB0001052'),
('Canara','West Bengal','Siliguri','Siliguri','Siliguri Branch','CNRB0001053'),
('Canara','West Bengal','Asansol','Asansol','Asansol Main','CNRB0001054'),
('Canara','West Bengal','Durgapur','Durgapur','Durgapur Branch','CNRB0001055');










CREATE TABLE `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(100),
    `state` VARCHAR(100),
    `district` VARCHAR(100),
    `city` VARCHAR(100),
    `branch` VARCHAR(100),
    `ifsc_code` VARCHAR(20)
);




USE bankifsc;

-- Update admin user (if exists) or insert new one
-- Password: Admin@123 (you can change this)
INSERT INTO users (username, email, password, full_name, role, phone, two_factor_enabled, created_at) 
VALUES ('admin', 'admin@bankifsc.com', 'Admin@123', 'Administrator', 'admin', '9999999999', 1, NOW())
ON DUPLICATE KEY UPDATE 
    role = 'admin',
    password = 'Admin@123',
    two_factor_enabled = 1;

-- Make sure the user is admin
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@bankifsc.com';

-- Verify
SELECT id, username, email, role, two_factor_enabled FROM users WHERE role = 'admin';




USE bankifsc;

-- Drop existing tables if they exist (to recreate with correct structure)
DROP TABLE IF EXISTS `security_alerts`;
DROP TABLE IF EXISTS `known_devices`;
DROP TABLE IF EXISTS `login_history`;
DROP TABLE IF EXISTS `users`;

-- Recreate users table with correct columns
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123 - in production use password_hash())
INSERT INTO users (username, email, password, full_name, role, phone) VALUES
('john_doe', 'john@example.com', 'User@123', 'John Doe', 'user', '9876543210'),
('jane_smith', 'jane@example.com', 'User@123', 'Jane Smith', 'user', '9876543211'),
('admin', 'admin@bank.com', 'User@123', 'Admin User', 'admin', '9876543212');

-- Verify data inserted
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'login_history', COUNT(*) FROM login_history
UNION ALL
SELECT 'known_devices', COUNT(*) FROM known_devices
UNION ALL
SELECT 'security_alerts', COUNT(*) FROM security_alerts;




-- Create database
CREATE DATABASE IF NOT EXISTS `bankifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bankifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;



define('DB_HOST', '3308');
define('DB_NAME', 'bankifsc');
define('DB_USER', 'root');
define('DB_PASS', '');


define('DB_HOST', '3308');
define('DB_NAME', 'bankifsc');
define('DB_USER', 'root');
define('DB_PASS', '');


CREATE DATABASE bank_ifsc;
USE bank_ifsc;



USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');




-- DROP TABLE if exists
DROP TABLE IF EXISTS branches;

-- CREATE TABLE
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100),
    state VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    branch VARCHAR(100),
    ifsc_code VARCHAR(20)
);

-- INSERT DATA FOR 6 BANKS x 6 STATES x 6 DISTRICTS (36 branches per bank)

-- BANK: Canara
INSERT INTO branches (bank_name, state, district, city, branch, ifsc_code) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Branch','CNRB0001004'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001005'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001006'),

('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0001010'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','Gandhipuram','CNRB0001011'),
('Canara','Tamil Nadu','Madurai','Madurai','Madurai Main','CNRB0001012'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0001013'),
('Canara','Tamil Nadu','Tirunelveli','Tirunelveli','Tirunelveli Main','CNRB0001014'),
('Canara','Tamil Nadu','Vellore','Vellore','Vellore Branch','CNRB0001015'),

('Canara','Maharashtra','Mumbai','Mumbai','Bandra','CNRB0001020'),
('Canara','Maharashtra','Pune','Pune','Shivajinagar','CNRB0001021'),
('Canara','Maharashtra','Nagpur','Nagpur','Nagpur Main','CNRB0001022'),
('Canara','Maharashtra','Nashik','Nashik','Nashik Branch','CNRB0001023'),
('Canara','Maharashtra','Thane','Thane','Thane Branch','CNRB0001024'),
('Canara','Maharashtra','Aurangabad','Aurangabad','Aurangabad Main','CNRB0001025'),

('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Statue Branch','CNRB0001030'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0001031'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0001032'),
('Canara','Kerala','Thrissur','Thrissur','Thrissur Main','CNRB0001033'),
('Canara','Kerala','Alappuzha','Alappuzha','Alappuzha Branch','CNRB0001034'),
('Canara','Kerala','Kollam','Kollam','Kollam Branch','CNRB0001035'),

('Canara','Uttar Pradesh','Lucknow','Lucknow','Hazratganj','CNRB0001040'),
('Canara','Uttar Pradesh','Kanpur','Kanpur','Civil Lines','CNRB0001041'),
('Canara','Uttar Pradesh','Varanasi','Varanasi','Dashashwamedh','CNRB0001042'),
('Canara','Uttar Pradesh','Agra','Agra','Taj Mahal Road','CNRB0001043'),
('Canara','Uttar Pradesh','Meerut','Meerut','Meerut Main','CNRB0001044'),
('Canara','Uttar Pradesh','Ghaziabad','Ghaziabad','Ghaziabad Main','CNRB0001045'),

('Canara','West Bengal','Kolkata','Kolkata','Park Street','CNRB0001050'),
('Canara','West Bengal','Howrah','Howrah','Howrah Main','CNRB0001051'),
('Canara','West Bengal','Darjeeling','Darjeeling','Mall Road','CNRB0001052'),
('Canara','West Bengal','Siliguri','Siliguri','Siliguri Branch','CNRB0001053'),
('Canara','West Bengal','Asansol','Asansol','Asansol Main','CNRB0001054'),
('Canara','West Bengal','Durgapur','Durgapur','Durgapur Branch','CNRB0001055');



CREATE DATABASE bank_ifsc;
USE bank_ifsc;


SELECT DISTINCT bank_name FROM branches;




INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('SBI','Karnataka','Bangalore','Bangalore','Indiranagar','SBIN0001001'),
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Union Bank','Karnataka','Bangalore','Bangalore','Brigade Road','UBIN0001001'),
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Bank of India','Karnataka','Bangalore','Bangalore','Commercial Street','BKID0001001');






CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50)
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    ip_address VARCHAR(50),
    success BOOLEAN,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(50) UNIQUE
);






USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');


CREATE TABLE `branches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(100),
    `state` VARCHAR(100),
    `district` VARCHAR(100),
    `city` VARCHAR(100),
    `branch` VARCHAR(100),
    `ifsc_code` VARCHAR(20)
);





-- DROP TABLE if exists
DROP TABLE IF EXISTS branches;

-- CREATE TABLE
CREATE TABLE branches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bank_name VARCHAR(100),
    state VARCHAR(100),
    district VARCHAR(100),
    city VARCHAR(100),
    branch VARCHAR(100),
    ifsc_code VARCHAR(20)
);

-- INSERT DATA FOR 6 BANKS x 6 STATES x 6 DISTRICTS (36 branches per bank)

-- BANK: Canara
INSERT INTO branches (bank_name, state, district, city, branch, ifsc_code) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Branch','CNRB0001004'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001005'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001006'),

('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0001010'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','Gandhipuram','CNRB0001011'),
('Canara','Tamil Nadu','Madurai','Madurai','Madurai Main','CNRB0001012'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0001013'),
('Canara','Tamil Nadu','Tirunelveli','Tirunelveli','Tirunelveli Main','CNRB0001014'),
('Canara','Tamil Nadu','Vellore','Vellore','Vellore Branch','CNRB0001015'),

('Canara','Maharashtra','Mumbai','Mumbai','Bandra','CNRB0001020'),
('Canara','Maharashtra','Pune','Pune','Shivajinagar','CNRB0001021'),
('Canara','Maharashtra','Nagpur','Nagpur','Nagpur Main','CNRB0001022'),
('Canara','Maharashtra','Nashik','Nashik','Nashik Branch','CNRB0001023'),
('Canara','Maharashtra','Thane','Thane','Thane Branch','CNRB0001024'),
('Canara','Maharashtra','Aurangabad','Aurangabad','Aurangabad Main','CNRB0001025'),

('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Statue Branch','CNRB0001030'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0001031'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0001032'),
('Canara','Kerala','Thrissur','Thrissur','Thrissur Main','CNRB0001033'),
('Canara','Kerala','Alappuzha','Alappuzha','Alappuzha Branch','CNRB0001034'),
('Canara','Kerala','Kollam','Kollam','Kollam Branch','CNRB0001035'),

('Canara','Uttar Pradesh','Lucknow','Lucknow','Hazratganj','CNRB0001040'),
('Canara','Uttar Pradesh','Kanpur','Kanpur','Civil Lines','CNRB0001041'),
('Canara','Uttar Pradesh','Varanasi','Varanasi','Dashashwamedh','CNRB0001042'),
('Canara','Uttar Pradesh','Agra','Agra','Taj Mahal Road','CNRB0001043'),
('Canara','Uttar Pradesh','Meerut','Meerut','Meerut Main','CNRB0001044'),
('Canara','Uttar Pradesh','Ghaziabad','Ghaziabad','Ghaziabad Main','CNRB0001045'),

('Canara','West Bengal','Kolkata','Kolkata','Park Street','CNRB0001050'),
('Canara','West Bengal','Howrah','Howrah','Howrah Main','CNRB0001051'),
('Canara','West Bengal','Darjeeling','Darjeeling','Mall Road','CNRB0001052'),
('Canara','West Bengal','Siliguri','Siliguri','Siliguri Branch','CNRB0001053'),
('Canara','West Bengal','Asansol','Asansol','Asansol Main','CNRB0001054'),
('Canara','West Bengal','Durgapur','Durgapur','Durgapur Branch','CNRB0001055');







SELECT DISTINCT bank_name FROM branches;








INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('SBI','Karnataka','Bangalore','Bangalore','Indiranagar','SBIN0001001'),
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Union Bank','Karnataka','Bangalore','Bangalore','Brigade Road','UBIN0001001'),
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Bank of India','Karnataka','Bangalore','Bangalore','Commercial Street','BKID0001001');

















SELECT DISTINCT `bank_name` FROM `branches`;














SELECT DISTINCT `bank_name` FROM `branches`;










define('DB_HOST', 'localhost');
define('DB_NAME', 'bank_ifsc');
define('DB_USER', 'root');
define('DB_PASS', '');CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20)
);

-- ------------------------
-- Canara Bank - 6 states Ã— 6 districts = 36 rows
-- ------------------------
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006');

-- ------------------------
-- SBI Bank - same structure 6Ã—6 = 36 rows
-- ------------------------
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006');













CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20)
);

-- ==========================
-- 1. Canara Bank
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Canara','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006');

-- ==========================
-- 2. SBI Bank
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006');

-- ==========================
-- 3. Bank of Baroda
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006');

-- ==========================
-- 4. Union Bank
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006');

-- ==========================
-- 5. Punjab National Bank (PNB)
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006');

-- ==========================
-- 6. Bank of India
-- ==========================
INSERT INTO `branches` (`bank_name`,`state`,`district`,`city`,`branch`,`ifsc_code`) VALUES
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');







USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');













USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');



















CREATE DATABASE bank_ifsc;
USE bank_ifsc;










CREATE DATABASE bank_ifsc;
USE bank_ifsc;









USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');
















-- Create database
CREATE DATABASE IF NOT EXISTS `bankifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bankifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;




















-- Create database
CREATE DATABASE IF NOT EXISTS `bankifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bankifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;





















-- Create database
CREATE DATABASE IF NOT EXISTS `bank_ifsc` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `bank_ifsc`;

-- Create branches table
CREATE TABLE IF NOT EXISTS `branches` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `bank_name` VARCHAR(100),
  `state` VARCHAR(100),
  `district` VARCHAR(100),
  `city` VARCHAR(100),
  `branch` VARCHAR(100),
  `ifsc_code` VARCHAR(20),
  INDEX `idx_ifsc` (`ifsc_code`),
  INDEX `idx_state` (`state`),
  INDEX `idx_city` (`city`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert your bank data (I'll include all 6 banks)
INSERT INTO `branches` (`bank_name`, `state`, `district`, `city`, `branch`, `ifsc_code`) VALUES
-- Canara Bank
('Canara Bank','Karnataka','Bangalore','Bangalore','Indiranagar','CNRB0001001'),
('Canara Bank','Karnataka','Mysuru','Mysuru','Kuvempunagar','CNRB0001002'),
('Canara Bank','Karnataka','Mandya','Mandya','Mandya Main','CNRB0001003'),
('Canara Bank','Karnataka','Tumkur','Tumkur','Tumkur Main','CNRB0001004'),
('Canara Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','CNRB0001005'),
('Canara Bank','Karnataka','Udupi','Udupi','Udupi Branch','CNRB0001006'),
('Canara Bank','Tamil Nadu','Chennai','Chennai','T Nagar','CNRB0002001'),
('Canara Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','CNRB0002002'),
('Canara Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','CNRB0002003'),
('Canara Bank','Tamil Nadu','Salem','Salem','Salem Branch','CNRB0002004'),
('Canara Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','CNRB0002005'),
('Canara Bank','Tamil Nadu','Erode','Erode','Erode Branch','CNRB0002006'),
('Canara Bank','Maharashtra','Mumbai','Mumbai','Fort','CNRB0003001'),
('Canara Bank','Maharashtra','Pune','Pune','Shivaji Nagar','CNRB0003002'),
('Canara Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','CNRB0003003'),
('Canara Bank','Maharashtra','Nashik','Nashik','Main Branch','CNRB0003004'),
('Canara Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','CNRB0003005'),
('Canara Bank','Maharashtra','Thane','Thane','Wagle Estate','CNRB0003006'),
('Canara Bank','Kerala','Kochi','Kochi','Marine Drive','CNRB0004001'),
('Canara Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','CNRB0004002'),
('Canara Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','CNRB0004003'),
('Canara Bank','Kerala','Thrissur','Thrissur','Main Branch','CNRB0004004'),
('Canara Bank','Kerala','Alappuzha','Alappuzha','Punnapra','CNRB0004005'),
('Canara Bank','Kerala','Kollam','Kollam','Chinnakada','CNRB0004006'),
('Canara Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','CNRB0005001'),
('Canara Bank','Gujarat','Surat','Surat','Varachha','CNRB0005002'),
('Canara Bank','Gujarat','Vadodara','Vadodara','Alkapuri','CNRB0005003'),
('Canara Bank','Gujarat','Rajkot','Rajkot','Main Branch','CNRB0005004'),
('Canara Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','CNRB0005005'),
('Canara Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','CNRB0005006'),
('Canara Bank','Rajasthan','Jaipur','Jaipur','MI Road','CNRB0006001'),
('Canara Bank','Rajasthan','Udaipur','Udaipur','Main Branch','CNRB0006002'),
('Canara Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','CNRB0006003'),
('Canara Bank','Rajasthan','Ajmer','Ajmer','Main Branch','CNRB0006004'),
('Canara Bank','Rajasthan','Bikaner','Bikaner','Main Branch','CNRB0006005'),
('Canara Bank','Rajasthan','Alwar','Alwar','Main Branch','CNRB0006006'),

-- SBI
('SBI','Karnataka','Bangalore','Bangalore','MG Road','SBIN0001001'),
('SBI','Karnataka','Mysuru','Mysuru','Ashoka Circle','SBIN0001002'),
('SBI','Karnataka','Mandya','Mandya','Mandya Main','SBIN0001003'),
('SBI','Karnataka','Tumkur','Tumkur','Tumkur Branch','SBIN0001004'),
('SBI','Karnataka','Mangalore','Mangalore','Mangalore Main','SBIN0001005'),
('SBI','Karnataka','Udupi','Udupi','Udupi Branch','SBIN0001006'),
('SBI','Tamil Nadu','Chennai','Chennai','T Nagar','SBIN0002001'),
('SBI','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','SBIN0002002'),
('SBI','Tamil Nadu','Madurai','Madurai','Thirumalai','SBIN0002003'),
('SBI','Tamil Nadu','Salem','Salem','Salem Branch','SBIN0002004'),
('SBI','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','SBIN0002005'),
('SBI','Tamil Nadu','Erode','Erode','Erode Branch','SBIN0002006'),
('SBI','Maharashtra','Mumbai','Mumbai','Fort','SBIN0003001'),
('SBI','Maharashtra','Pune','Pune','Shivaji Nagar','SBIN0003002'),
('SBI','Maharashtra','Nagpur','Nagpur','Sitabuldi','SBIN0003003'),
('SBI','Maharashtra','Nashik','Nashik','Main Branch','SBIN0003004'),
('SBI','Maharashtra','Aurangabad','Aurangabad','CIDCO','SBIN0003005'),
('SBI','Maharashtra','Thane','Thane','Wagle Estate','SBIN0003006'),
('SBI','Kerala','Kochi','Kochi','Marine Drive','SBIN0004001'),
('SBI','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','SBIN0004002'),
('SBI','Kerala','Kozhikode','Kozhikode','Mavoor Road','SBIN0004003'),
('SBI','Kerala','Thrissur','Thrissur','Main Branch','SBIN0004004'),
('SBI','Kerala','Alappuzha','Alappuzha','Punnapra','SBIN0004005'),
('SBI','Kerala','Kollam','Kollam','Chinnakada','SBIN0004006'),
('SBI','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','SBIN0005001'),
('SBI','Gujarat','Surat','Surat','Varachha','SBIN0005002'),
('SBI','Gujarat','Vadodara','Vadodara','Alkapuri','SBIN0005003'),
('SBI','Gujarat','Rajkot','Rajkot','Main Branch','SBIN0005004'),
('SBI','Gujarat','Bhavnagar','Bhavnagar','Main Branch','SBIN0005005'),
('SBI','Gujarat','Jamnagar','Jamnagar','Main Branch','SBIN0005006'),
('SBI','Rajasthan','Jaipur','Jaipur','MI Road','SBIN0006001'),
('SBI','Rajasthan','Udaipur','Udaipur','Main Branch','SBIN0006002'),
('SBI','Rajasthan','Jodhpur','Jodhpur','Pal Road','SBIN0006003'),
('SBI','Rajasthan','Ajmer','Ajmer','Main Branch','SBIN0006004'),
('SBI','Rajasthan','Bikaner','Bikaner','Main Branch','SBIN0006005'),
('SBI','Rajasthan','Alwar','Alwar','Main Branch','SBIN0006006'),

-- Bank of Baroda
('Bank of Baroda','Karnataka','Bangalore','Bangalore','MG Road','BARB0001001'),
('Bank of Baroda','Karnataka','Mysuru','Mysuru','Ashoka Circle','BARB0001002'),
('Bank of Baroda','Karnataka','Mandya','Mandya','Mandya Main','BARB0001003'),
('Bank of Baroda','Karnataka','Tumkur','Tumkur','Tumkur Branch','BARB0001004'),
('Bank of Baroda','Karnataka','Mangalore','Mangalore','Mangalore Main','BARB0001005'),
('Bank of Baroda','Karnataka','Udupi','Udupi','Udupi Branch','BARB0001006'),
('Bank of Baroda','Tamil Nadu','Chennai','Chennai','T Nagar','BARB0002001'),
('Bank of Baroda','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BARB0002002'),
('Bank of Baroda','Tamil Nadu','Madurai','Madurai','Thirumalai','BARB0002003'),
('Bank of Baroda','Tamil Nadu','Salem','Salem','Salem Branch','BARB0002004'),
('Bank of Baroda','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BARB0002005'),
('Bank of Baroda','Tamil Nadu','Erode','Erode','Erode Branch','BARB0002006'),
('Bank of Baroda','Maharashtra','Mumbai','Mumbai','Fort','BARB0003001'),
('Bank of Baroda','Maharashtra','Pune','Pune','Shivaji Nagar','BARB0003002'),
('Bank of Baroda','Maharashtra','Nagpur','Nagpur','Sitabuldi','BARB0003003'),
('Bank of Baroda','Maharashtra','Nashik','Nashik','Main Branch','BARB0003004'),
('Bank of Baroda','Maharashtra','Aurangabad','Aurangabad','CIDCO','BARB0003005'),
('Bank of Baroda','Maharashtra','Thane','Thane','Wagle Estate','BARB0003006'),
('Bank of Baroda','Kerala','Kochi','Kochi','Marine Drive','BARB0004001'),
('Bank of Baroda','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BARB0004002'),
('Bank of Baroda','Kerala','Kozhikode','Kozhikode','Mavoor Road','BARB0004003'),
('Bank of Baroda','Kerala','Thrissur','Thrissur','Main Branch','BARB0004004'),
('Bank of Baroda','Kerala','Alappuzha','Alappuzha','Punnapra','BARB0004005'),
('Bank of Baroda','Kerala','Kollam','Kollam','Chinnakada','BARB0004006'),
('Bank of Baroda','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BARB0005001'),
('Bank of Baroda','Gujarat','Surat','Surat','Varachha','BARB0005002'),
('Bank of Baroda','Gujarat','Vadodara','Vadodara','Alkapuri','BARB0005003'),
('Bank of Baroda','Gujarat','Rajkot','Rajkot','Main Branch','BARB0005004'),
('Bank of Baroda','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BARB0005005'),
('Bank of Baroda','Gujarat','Jamnagar','Jamnagar','Main Branch','BARB0005006'),
('Bank of Baroda','Rajasthan','Jaipur','Jaipur','MI Road','BARB0006001'),
('Bank of Baroda','Rajasthan','Udaipur','Udaipur','Main Branch','BARB0006002'),
('Bank of Baroda','Rajasthan','Jodhpur','Jodhpur','Pal Road','BARB0006003'),
('Bank of Baroda','Rajasthan','Ajmer','Ajmer','Main Branch','BARB0006004'),
('Bank of Baroda','Rajasthan','Bikaner','Bikaner','Main Branch','BARB0006005'),
('Bank of Baroda','Rajasthan','Alwar','Alwar','Main Branch','BARB0006006'),

-- Union Bank
('Union Bank','Karnataka','Bangalore','Bangalore','MG Road','UBIN0001001'),
('Union Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','UBIN0001002'),
('Union Bank','Karnataka','Mandya','Mandya','Mandya Main','UBIN0001003'),
('Union Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','UBIN0001004'),
('Union Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','UBIN0001005'),
('Union Bank','Karnataka','Udupi','Udupi','Udupi Branch','UBIN0001006'),
('Union Bank','Tamil Nadu','Chennai','Chennai','T Nagar','UBIN0002001'),
('Union Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','UBIN0002002'),
('Union Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','UBIN0002003'),
('Union Bank','Tamil Nadu','Salem','Salem','Salem Branch','UBIN0002004'),
('Union Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','UBIN0002005'),
('Union Bank','Tamil Nadu','Erode','Erode','Erode Branch','UBIN0002006'),
('Union Bank','Maharashtra','Mumbai','Mumbai','Fort','UBIN0003001'),
('Union Bank','Maharashtra','Pune','Pune','Shivaji Nagar','UBIN0003002'),
('Union Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','UBIN0003003'),
('Union Bank','Maharashtra','Nashik','Nashik','Main Branch','UBIN0003004'),
('Union Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','UBIN0003005'),
('Union Bank','Maharashtra','Thane','Thane','Wagle Estate','UBIN0003006'),
('Union Bank','Kerala','Kochi','Kochi','Marine Drive','UBIN0004001'),
('Union Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','UBIN0004002'),
('Union Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','UBIN0004003'),
('Union Bank','Kerala','Thrissur','Thrissur','Main Branch','UBIN0004004'),
('Union Bank','Kerala','Alappuzha','Alappuzha','Punnapra','UBIN0004005'),
('Union Bank','Kerala','Kollam','Kollam','Chinnakada','UBIN0004006'),
('Union Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','UBIN0005001'),
('Union Bank','Gujarat','Surat','Surat','Varachha','UBIN0005002'),
('Union Bank','Gujarat','Vadodara','Vadodara','Alkapuri','UBIN0005003'),
('Union Bank','Gujarat','Rajkot','Rajkot','Main Branch','UBIN0005004'),
('Union Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','UBIN0005005'),
('Union Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','UBIN0005006'),
('Union Bank','Rajasthan','Jaipur','Jaipur','MI Road','UBIN0006001'),
('Union Bank','Rajasthan','Udaipur','Udaipur','Main Branch','UBIN0006002'),
('Union Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','UBIN0006003'),
('Union Bank','Rajasthan','Ajmer','Ajmer','Main Branch','UBIN0006004'),
('Union Bank','Rajasthan','Bikaner','Bikaner','Main Branch','UBIN0006005'),
('Union Bank','Rajasthan','Alwar','Alwar','Main Branch','UBIN0006006'),

-- Punjab National Bank
('Punjab National Bank','Karnataka','Bangalore','Bangalore','MG Road','PUNB0001001'),
('Punjab National Bank','Karnataka','Mysuru','Mysuru','Ashoka Circle','PUNB0001002'),
('Punjab National Bank','Karnataka','Mandya','Mandya','Mandya Main','PUNB0001003'),
('Punjab National Bank','Karnataka','Tumkur','Tumkur','Tumkur Branch','PUNB0001004'),
('Punjab National Bank','Karnataka','Mangalore','Mangalore','Mangalore Main','PUNB0001005'),
('Punjab National Bank','Karnataka','Udupi','Udupi','Udupi Branch','PUNB0001006'),
('Punjab National Bank','Tamil Nadu','Chennai','Chennai','T Nagar','PUNB0002001'),
('Punjab National Bank','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','PUNB0002002'),
('Punjab National Bank','Tamil Nadu','Madurai','Madurai','Thirumalai','PUNB0002003'),
('Punjab National Bank','Tamil Nadu','Salem','Salem','Salem Branch','PUNB0002004'),
('Punjab National Bank','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','PUNB0002005'),
('Punjab National Bank','Tamil Nadu','Erode','Erode','Erode Branch','PUNB0002006'),
('Punjab National Bank','Maharashtra','Mumbai','Mumbai','Fort','PUNB0003001'),
('Punjab National Bank','Maharashtra','Pune','Pune','Shivaji Nagar','PUNB0003002'),
('Punjab National Bank','Maharashtra','Nagpur','Nagpur','Sitabuldi','PUNB0003003'),
('Punjab National Bank','Maharashtra','Nashik','Nashik','Main Branch','PUNB0003004'),
('Punjab National Bank','Maharashtra','Aurangabad','Aurangabad','CIDCO','PUNB0003005'),
('Punjab National Bank','Maharashtra','Thane','Thane','Wagle Estate','PUNB0003006'),
('Punjab National Bank','Kerala','Kochi','Kochi','Marine Drive','PUNB0004001'),
('Punjab National Bank','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','PUNB0004002'),
('Punjab National Bank','Kerala','Kozhikode','Kozhikode','Mavoor Road','PUNB0004003'),
('Punjab National Bank','Kerala','Thrissur','Thrissur','Main Branch','PUNB0004004'),
('Punjab National Bank','Kerala','Alappuzha','Alappuzha','Punnapra','PUNB0004005'),
('Punjab National Bank','Kerala','Kollam','Kollam','Chinnakada','PUNB0004006'),
('Punjab National Bank','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','PUNB0005001'),
('Punjab National Bank','Gujarat','Surat','Surat','Varachha','PUNB0005002'),
('Punjab National Bank','Gujarat','Vadodara','Vadodara','Alkapuri','PUNB0005003'),
('Punjab National Bank','Gujarat','Rajkot','Rajkot','Main Branch','PUNB0005004'),
('Punjab National Bank','Gujarat','Bhavnagar','Bhavnagar','Main Branch','PUNB0005005'),
('Punjab National Bank','Gujarat','Jamnagar','Jamnagar','Main Branch','PUNB0005006'),
('Punjab National Bank','Rajasthan','Jaipur','Jaipur','MI Road','PUNB0006001'),
('Punjab National Bank','Rajasthan','Udaipur','Udaipur','Main Branch','PUNB0006002'),
('Punjab National Bank','Rajasthan','Jodhpur','Jodhpur','Pal Road','PUNB0006003'),
('Punjab National Bank','Rajasthan','Ajmer','Ajmer','Main Branch','PUNB0006004'),
('Punjab National Bank','Rajasthan','Bikaner','Bikaner','Main Branch','PUNB0006005'),
('Punjab National Bank','Rajasthan','Alwar','Alwar','Main Branch','PUNB0006006'),

-- Bank of India
('Bank of India','Karnataka','Bangalore','Bangalore','MG Road','BKID0001001'),
('Bank of India','Karnataka','Mysuru','Mysuru','Ashoka Circle','BKID0001002'),
('Bank of India','Karnataka','Mandya','Mandya','Mandya Main','BKID0001003'),
('Bank of India','Karnataka','Tumkur','Tumkur','Tumkur Branch','BKID0001004'),
('Bank of India','Karnataka','Mangalore','Mangalore','Mangalore Main','BKID0001005'),
('Bank of India','Karnataka','Udupi','Udupi','Udupi Branch','BKID0001006'),
('Bank of India','Tamil Nadu','Chennai','Chennai','T Nagar','BKID0002001'),
('Bank of India','Tamil Nadu','Coimbatore','Coimbatore','RS Puram','BKID0002002'),
('Bank of India','Tamil Nadu','Madurai','Madurai','Thirumalai','BKID0002003'),
('Bank of India','Tamil Nadu','Salem','Salem','Salem Branch','BKID0002004'),
('Bank of India','Tamil Nadu','Tiruchirappalli','Trichy','Trichy Main','BKID0002005'),
('Bank of India','Tamil Nadu','Erode','Erode','Erode Branch','BKID0002006'),
('Bank of India','Maharashtra','Mumbai','Mumbai','Fort','BKID0003001'),
('Bank of India','Maharashtra','Pune','Pune','Shivaji Nagar','BKID0003002'),
('Bank of India','Maharashtra','Nagpur','Nagpur','Sitabuldi','BKID0003003'),
('Bank of India','Maharashtra','Nashik','Nashik','Main Branch','BKID0003004'),
('Bank of India','Maharashtra','Aurangabad','Aurangabad','CIDCO','BKID0003005'),
('Bank of India','Maharashtra','Thane','Thane','Wagle Estate','BKID0003006'),
('Bank of India','Kerala','Kochi','Kochi','Marine Drive','BKID0004001'),
('Bank of India','Kerala','Thiruvananthapuram','Thiruvananthapuram','Palayam','BKID0004002'),
('Bank of India','Kerala','Kozhikode','Kozhikode','Mavoor Road','BKID0004003'),
('Bank of India','Kerala','Thrissur','Thrissur','Main Branch','BKID0004004'),
('Bank of India','Kerala','Alappuzha','Alappuzha','Punnapra','BKID0004005'),
('Bank of India','Kerala','Kollam','Kollam','Chinnakada','BKID0004006'),
('Bank of India','Gujarat','Ahmedabad','Ahmedabad','Navrangpura','BKID0005001'),
('Bank of India','Gujarat','Surat','Surat','Varachha','BKID0005002'),
('Bank of India','Gujarat','Vadodara','Vadodara','Alkapuri','BKID0005003'),
('Bank of India','Gujarat','Rajkot','Rajkot','Main Branch','BKID0005004'),
('Bank of India','Gujarat','Bhavnagar','Bhavnagar','Main Branch','BKID0005005'),
('Bank of India','Gujarat','Jamnagar','Jamnagar','Main Branch','BKID0005006'),
('Bank of India','Rajasthan','Jaipur','Jaipur','MI Road','BKID0006001'),
('Bank of India','Rajasthan','Udaipur','Udaipur','Main Branch','BKID0006002'),
('Bank of India','Rajasthan','Jodhpur','Jodhpur','Pal Road','BKID0006003'),
('Bank of India','Rajasthan','Ajmer','Ajmer','Main Branch','BKID0006004'),
('Bank of India','Rajasthan','Bikaner','Bikaner','Main Branch','BKID0006005'),
('Bank of India','Rajasthan','Alwar','Alwar','Main Branch','BKID0006006');

-- Verify data inserted
SELECT COUNT(*) as total_branches FROM branches;
SELECT DISTINCT bank_name FROM branches;
SELECT DISTINCT state FROM branches;


















USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');




























USE bankifsc;

-- Update admin user (if exists) or insert new one
-- Password: Admin@123 (you can change this)
INSERT INTO users (username, email, password, full_name, role, phone, two_factor_enabled, created_at) 
VALUES ('admin', 'admin@bankifsc.com', 'Admin@123', 'Administrator', 'admin', '9999999999', 1, NOW())
ON DUPLICATE KEY UPDATE 
    role = 'admin',
    password = 'Admin@123',
    two_factor_enabled = 1;

-- Make sure the user is admin
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@bankifsc.com';

-- Verify
SELECT id, username, email, role, two_factor_enabled FROM users WHERE role = 'admin';


































USE bankifsc;

-- Update admin user (if exists) or insert new one
-- Password: Admin@123 (you can change this)
INSERT INTO users (username, email, password, full_name, role, phone, two_factor_enabled, created_at) 
VALUES ('admin', 'admin@bankifsc.com', 'Admin@123', 'Administrator', 'admin', '9999999999', 1, NOW())
ON DUPLICATE KEY UPDATE 
    role = 'admin',
    password = 'Admin@123',
    two_factor_enabled = 1;

-- Make sure the user is admin
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@bankifsc.com';

-- Verify
SELECT id, username, email, role, two_factor_enabled FROM users WHERE role = 'admin';


























USE bankifsc;

-- Users table
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `full_name` VARCHAR(100),
    `role` ENUM('user', 'admin') DEFAULT 'user',
    `two_factor_enabled` BOOLEAN DEFAULT FALSE,
    `biometric_enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL
);

-- Login history table
CREATE TABLE IF NOT EXISTS `login_history` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `browser` VARCHAR(100),
    `os` VARCHAR(100),
    `device_type` VARCHAR(50),
    `location` VARCHAR(255),
    `login_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `status` ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Known devices table
CREATE TABLE IF NOT EXISTS `known_devices` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `device_fingerprint` VARCHAR(255),
    `device_name` VARCHAR(255),
    `last_seen` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `is_trusted` BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Security alerts table
CREATE TABLE IF NOT EXISTS `security_alerts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `alert_type` VARCHAR(50),
    `message` TEXT,
    `ip_address` VARCHAR(45),
    `device_info` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample users (password: User@123)
INSERT INTO users (username, email, password, full_name, role) VALUES
('john_doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', 'John Doe', 'user'),
('jane_smith', 'jane@example.com', '$2y$10$YourHashedPasswordHere', 'Jane Smith', 'user'),
('admin', 'admin@bank.com', '$2y$10$YourHashedPasswordHere', 'Admin User', 'admin');






























CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50)
);

CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    ip_address VARCHAR(50),
    success BOOLEAN,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(50) UNIQUE
);

















































































































































































































