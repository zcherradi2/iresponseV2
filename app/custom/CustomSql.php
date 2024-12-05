
<?php
function getPdo($host,$port,$db,$user,$pass) {
    // $host = '172.81.63.236';
    // $db = 'ir_clients';
    // $user = 'admin';
    // $pass = 'Sapp123';
    // $port = '5432';

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

function clearTable($pdo, $schema ,$TableName) {
    // Sanitize table name to prevent SQL injection (use with care)
    $TableName = preg_replace('/[^a-zA-Z0-9_]/', '', $TableName);

    // Construct the SQL query
    $sql = "DELETE FROM $schema." . $TableName;

    try {
        // Prepare and execute the statement
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return true;
    } catch (PDOException $e) {
        // Handle exceptions
        false;
    }
}

function createSupTableIfNotExists($pdo,$schema, $TableName) {
    // Sanitize the table name to prevent SQL injection
    $TableName = preg_replace('/[^a-zA-Z0-9_]/', '', $TableName);

    // Construct the SQL query to create the table if it doesn't exist
    $sql = "
        CREATE TABLE IF NOT EXISTS $schema.$TableName (
            id SERIAL PRIMARY KEY,
            email_md5 TEXT  NOT NULL
        );
    ";

    try {
        // Execute the query
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

function getDbInfo($key){
    $filePath = "/usr/gm/datasources/$key.json";
    if (!file_exists($filePath)) {
        throw new Exception("File not found: $filePath");
    }

    // Read the file content
    $jsonContent = file_get_contents($filePath);

    // Decode the JSON content
    $config = json_decode($jsonContent, true);

    // Check if decoding was successful
    if ($config === null) {
        return false;
    }

    return $config;
}
?>
