-- Create the MySQL user 'seaweedadmin' with the specified password
CREATE USER 'seaweedadmin'@'%' IDENTIFIED BY 'SeaweedFarmingIot2024';

-- Grant all privileges to the user for a specific database (e.g., 'seaweed_farming')
CREATE DATABASE seaweed_farming;
GRANT ALL PRIVILEGES ON seaweed_farming.* TO 'seaweedadmin'@'%';

-- Apply privilege changes
FLUSH PRIVILEGES;

-- Switch to the seaweed_farming database
USE seaweed_farming;

-- Create users table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Create farms table
CREATE TABLE farms (
    farm_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    farm_token VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    plant_type VARCHAR(255) NOT NULL, -- Added plant_type column
    initial_water_level DECIMAL(6, 2) NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);


-- Create farm_data table
CREATE TABLE farm_data (
    data_id INT AUTO_INCREMENT PRIMARY KEY,
    farm_token VARCHAR(255),
    ph_value DECIMAL(5, 2) NULL,
    temperature DECIMAL(5, 2) NULL,
    salinity DECIMAL(8, 2) NULL,
    light_intensity DECIMAL(8, 2) NULL,
    water_level DECIMAL(6, 2) NULL,
    humidity DECIMAL(5, 2) NULL,
    time DATETIME,
    FOREIGN KEY (farm_token) REFERENCES farms(farm_token) ON DELETE CASCADE
);



-- Create farm_images table
CREATE TABLE farm_images (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    farm_token VARCHAR(255),
    time DATETIME,
    image_path VARCHAR(255) NOT NULL,
    image_comment TEXT NULL,
    FOREIGN KEY (farm_token) REFERENCES farms(farm_token) ON DELETE CASCADE
);


CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL
);

CREATE TABLE user_fcm_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY, -- Unique identifier for each token
    user_id INT , -- Foreign key to associate with users table
    fcm_token VARCHAR(255) NOT NULL UNIQUE, -- FCM token for push notifications
    device_type VARCHAR(50), -- Device type (optional, e.g., Android, iOS)
    created_at DATETIME , -- Timestamp of token creation
    last_notification_sent DATETIME, -- Tracks the last notification sent
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE -- Ensures user deletion removes associated tokens
);

CREATE TABLE mobile_login_token (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mobile_token VARCHAR(255) NOT NULL UNIQUE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);
