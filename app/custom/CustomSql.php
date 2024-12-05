
<?php
function getPdo() {
    $host = '172.81.63.236';
    $db = 'ir_clients';
    $user = 'admin';
    $pass = 'Sapp123';
    $charset = 'utf8';
    $port = '5432';

    // Set up DSN (Data Source Name) for PostgreSQL
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";

    // Set up PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
        PDO::ATTR_PERSISTENT         => false,                 // Disable persistent connections
    ];

    try {
        // Create a PDO instance (connect to the database)
        $pdo = new PDO($dsn, $user, $pass, $options);
        return $pdo;
    } catch (\PDOException $e) {
        // Handle connection errors
        echo "Connection failed: " . $e->getMessage();
        return null;
    }
}

?>
