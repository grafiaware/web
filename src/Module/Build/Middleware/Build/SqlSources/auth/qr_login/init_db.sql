/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 15. 12. 2025
 */


-- users: local user accounts
CREATE TABLE users (
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(255),
email VARCHAR(255) UNIQUE,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- oauth_accounts: map provider accounts to local users
CREATE TABLE oauth_accounts (
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
user_id BIGINT UNSIGNED NOT NULL,
provider VARCHAR(50) NOT NULL,
provider_user_id VARCHAR(255) NOT NULL,
access_token TEXT,
refresh_token TEXT,
expires_at DATETIME NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
UNIQUE KEY provider_user (provider, provider_user_id),
FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


-- qr_logins: temporary tokens used for QR login
CREATE TABLE qr_logins (
token CHAR(32) PRIMARY KEY,
status ENUM('pending','authenticated','expired') NOT NULL DEFAULT 'pending',
user_id BIGINT UNSIGNED NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
expires_at DATETIME NOT NULL
);




-- qr_login_tokens
-- 
-- Slouží k QR přihlášení (token → user).

CREATE TABLE qr_login_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token CHAR(64) NOT NULL,
    user_id INT NULL,
    created_at DATETIME NOT NULL,
    expires_at DATETIME NOT NULL,
    consumed TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE(token),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE user_providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider VARCHAR(50) NOT NULL,
    provider_user_id VARCHAR(255) NOT NULL,
    access_token TEXT NULL,
    refresh_token TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(provider, provider_user_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);