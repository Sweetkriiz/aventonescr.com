<?php
    session_start();
    define("SITIO", "Aventones CR");
    date_default_timezone_set("America/Costa_Rica");
    
    // Detecta la ruta base del proyecto (por ejemplo: /aventonescr.com/public/)
    $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__ . '/../public/'));
    define('BASE_URL', rtrim($basePath, '/') . '/');
?>