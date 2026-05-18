CREATE DATABASE IF NOT EXISTS midnightMedia_db;
USE midnightMedia_db;

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name, VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    userRole ENUM('admin','moderator'),
    profilePic VARCHAR(255) DEFAULT 'D:\WT\pp\htdocs\Project\assets\png-transparent-default-avatar.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE contents(
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255) NOT NULL,
    category_id INT,
    uploader_id INT,
    download_count INT DEFAULT 0,
    uploaded_at TIMESTAMP CURRENT_TIMESTAMP
);

CREATE TABLE content_requests(
    id INT AUTO_INCREMENT PRIMARY KEY,
    requester_ip VARCHAR(50) NOT NULL,
    content_title VARCHAR(255) NOT NULL,
    category_requested VARCHAR(255),
    request_message TEXT,
    request_status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--sample data--
INSERT INTO categories(id,name,parent_id)
VALUES(1,'Movies',NULL),
VALUES(2,'TV Series',NULL),
VALUES(3,'Softwares',NULL),
VALUES(4,'Games',NULL),

INSERT INTO categories(id,name,parent_id)
VALUES(5,'Studio-Ghibli Movies',1),
VALUES(6,'Disney Movies',1),
VALUES(7,'Anime Series',2),
VALUES(8,'Thriller Series',2),
VALUES(9,'Developer Tools',3),
VALUES(10,'Drawing Softwares',3),
VALUES(11,'RPG',4),
VALUES(12,'Sports',4),

INSERT INTO contents (title, description, file_path, category_id, uploader_id, download_count) VALUES 
--Studio-Ghibli Movies
(
 'Spirited Away (2001) [1080p] BluRay Dual-Audio', 
 'During her family move to the suburbs, a sullen 10-year-old girl wanders into a world ruled by gods, witches, and spirits.', 
 'ftp://10.10.80.22/movies/ghibli/Spirited_Away_1080p.mkv', -- Simulated Local ISP FTP Address
 5, 1, 1420
),
--Disney Movies
(
 'The Lion King (1994) [2160p] 4K HDR Remaster', 
 'Lion prince Simba and his father are targeted by his bitter uncle, who wants to ascend the throne himself.', 
 'ftp://10.10.80.22/movies/disney/The_Lion_King_1994_4K.mkv', 
 6, 1, 985
),
--Anime Series
(
 'Attack on Titan [Season 4 Complete Final Edition]', 
 'The final apocalyptic war reaches its ultimate conclusion. Japanese audio with English hardcoded subtitles.', 
 'ftp://10.10.80.22/tv/anime/AoT_S04_Complete_1080p.zip', 
 7, 1, 3410
),
--Thriler Series
(
 'Breaking Bad [Seasons 1-5 Complete Boxset UHD]', 
 'A high school chemistry teacher turned manufacturing kingpin looks to secure his family financial future.', 
 'ftp://10.10.80.22/tv/thriller/Breaking_Bad_UHD_Full.zip', 
 8, 1, 2890
),
--Developer Tools
(
 'Visual Studio Code v1.98 Pro Installer (x64)', 
 'Complete offline installer containing extension bundles for professional web tech applications.', 
 'ftp://10.10.80.23/software/dev/VSCodeSetup-x64-1.98.exe', 
 9, 1, 560
),
--Drawing Softwares
(
 'Adobe Photoshop 2026 pre-activated (v27.0)', 
 'Full continuous release for local graphics workflow. Pre-patched installer asset.', 
 'ftp://10.10.80.23/software/graphics/Adobe_Photoshop_2026_x64.iso', 
 10, 1, 1120
),
--RPG GAMES
(
 'The Witcher 3: Wild Hunt - Next-Gen Edition', 
 'Includes Hearts of Stone and Blood and Wine expansions. Optimized with high-resolution textures.', 
 'ftp://10.10.80.24/games/rpg/The_Witcher_3_NextGen.rar', 
 11, 1, 2450
),
--Sports Games
(
 'EA Sports FC 26 [Digital Deluxe Repack]', 
 'HyperMotionV technology accurately captures real-world player movements. Extract and deploy local shortcut.', 
 'ftp://10.10.80.24/games/sports/EA_Sports_FC_26_Repack.rar', 
 12, 1, 4120
);



