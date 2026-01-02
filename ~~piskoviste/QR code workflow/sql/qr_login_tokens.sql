/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Other/SQLTemplate.sql to edit this template
 */
/**
 * Author:  pes2704
 * Created: 15. 12. 2025
 */

-- ✅ 1. Databázové tabulky
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