
<?php
    require_once '../config/database.php';

    $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

    try {
        $pdo = new PDO($dsn, $user, $pass); 

        if ($pdo) {
            echo "Connected to the $db database successfully!";
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
?>
