<?php
    include_once __DIR__."/database.php";

    function getDB(): PDO {
        static $pdo=null;
        if($pdo===null){
            try{
                $pdo=new PDO(
                    "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
                    DB_USER,
                    DB_PASS,
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                    ]
                );
            }catch(PDOException $e){
                die(json_encode(['error'=>'Database connection failed.']));
            }
        }
        return $pdo;
    }

    $conn=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if(!$conn){
        die("Database connection failed: ".mysqli_connect_error());
    }
?>
