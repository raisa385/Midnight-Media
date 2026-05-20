<?php
    $db='midnightMedia_db';
    define('DB_HOST','localhost');
    define('DB_NAME','midnightMedia_db');
    define('DB_USER','root');
    define('DB_PASS','');

    $conn=new mysqli(DB_HOST,DB_USER,DB_PASS);
    if($conn->connect_error){die("connection failed: ".$conn->connect_error);}

    $sql="CREATE DATABASE IF NOT EXISTS $db";
    if($conn->query($sql)===TRUE){} 
    else{die("Error creating database: ".$conn->error);}

    if($conn->select_db($db)){}
    else{die("Error selecting db: ".$conn->error);}

    $sql="CREATE TABLE IF NOT EXISTS users(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            userRole ENUM('admin','moderator'),
            profilePic VARCHAR(255) DEFAULT 'defaultprofilepic.png',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ";
    if($conn->query($sql)){}
    else{echo "Error creating users table: ".$conn->error;} 

    $sql="CREATE TABLE IF NOT EXISTS categories(
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            parent_id INT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ";
    if($conn->query($sql)){}
    else{echo "Error creating categories table: ".$conn->error;} 

    $sql="CREATE TABLE IF NOT EXISTS contents(
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            file_path VARCHAR(255) NOT NULL,
            category_id INT,
            uploader_id INT,
            download_count INT DEFAULT 0,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ";
    if($conn->query($sql)){}
    else{echo "Error creating contents table: ".$conn->error;} 

    $sql="CREATE TABLE IF NOT EXISTS content_requests(
            id INT AUTO_INCREMENT PRIMARY KEY,
            requester_ip VARCHAR(50) NOT NULL,
            content_title VARCHAR(255) NOT NULL,
            category_requested VARCHAR(255),
            request_message TEXT,
            request_status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
            ";
    if($conn->query($sql)){}
    else{echo "Error creating content request table: ".$conn->error;}

    $check_categories=$conn->query("SELECT id FROM categories LIMIT 1");
    if($check_categories->num_rows==0){
        $addCat="INSERT INTO categories(id,name,parent_id) VALUES
                    (1,'Movies',NULL),
                    (2,'TV Series',NULL),
                    (3,'Softwares',NULL),
                    (4,'Games',NULL)
                    ";

        $addSubCat="INSERT INTO categories(id,name,parent_id) VALUES
                    (5,'Studio-Ghibli Movies',1),
                    (6,'Disney Movies',1),
                    (7,'Anime Series',2),
                    (8,'Thriller Series',2),
                    (9,'Developer Tools',3),
                    (10,'Drawing Softwares',3),
                    (11,'RPG',4),
                    (12,'Sports',4)
                    ";
        if($conn->query($addCat)){}
        else{echo "Error adding root categories: ".$conn->error;}
        if($conn->query($addSubCat)){}
        else{echo "Error adding sub categories: ".$conn->error;}
    }

    $check_contents=$conn->query("SELECT id FROM contents LIMIT 1");
    if($check_contents->num_rows==0){
        $addContents="INSERT INTO contents (title, description, file_path, category_id, uploader_id, download_count) VALUES
                        ('Spirited Away (2001) [1080p]', 'A sullen 10-year-old girl wanders into a world ruled by gods and spirits.', 'ftp://10.10.80.22/movies/ghibli/Spirited_Away_1080p.mkv', 5, 1, 1420),
                        ('The Lion King (1994) [4K]', 'Lion prince Simba and his father are targeted by his bitter uncle.', 'ftp://10.10.80.22/movies/disney/The_Lion_King_1994_4K.mkv', 6, 1, 985),
                        ('Attack on Titan [Season 4 Complete]', 'The final apocalyptic war reaches its ultimate conclusion.', 'ftp://10.10.80.22/tv/anime/AoT_S04_Complete_1080p.zip', 7, 1, 3410),
                        ('Breaking Bad [Complete Boxset]', 'A chemistry teacher turned kingpin looks to secure his family future.', 'ftp://10.10.80.22/tv/thriller/Breaking_Bad_UHD_Full.zip', 8, 1, 2890),
                        ('Visual Studio Code v1.98', 'Complete offline installer containing default extension bundles.', 'ftp://10.10.80.23/software/dev/VSCodeSetup-x64-1.98.exe', 9, 1, 560),
                        ('Adobe Photoshop 2026', 'Full continuous release for local graphics workflow.', 'ftp://10.10.80.23/software/graphics/Adobe_Photoshop_2026_x64.iso', 10, 1, 1120),
                        ('The Witcher 3: Next-Gen', 'Includes Hearts of Stone and Blood and Wine expansions.', 'ftp://10.10.80.24/games/rpg/The_Witcher_3_NextGen.rar', 11, 1, 2450),
                        ('EA Sports FC 26', 'HyperMotionV technology accurately captures real player movement.', 'ftp://10.10.80.24/games/sports/EA_Sports_FC_26_Repack.rar', 12, 1, 4120)
                        ";  
        if($conn->query($addContents)){}
        else{echo "Error adding contents: ".$conn->error;}
    }

    $check_users=$conn->query("SELECT id FROM users LIMIT 1");
    if($check_users->num_rows==0){
        $pw=password_hash('admin1',PASSWORD_DEFAULT);
        $conn->query("INSERT INTO users(id,name,email,password_hash,userRole) VALUES(1,'RAISA','rr.anwar385@gmail.com','$pw','admin')");
    }
?>
