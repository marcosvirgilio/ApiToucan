<?php
// Database connection
$host = 'toucandes.mysql.uhserver.com'; // Update with your database host
$db   = 'toucandes'; // Update with your database name
$user = 'toucandes'; // Update with your database username
$pass = 'touc@n2024'; // Update with your database password

$con = new mysqli($host, $user, $pass, $db);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Set charset to handle UTF-8 characters
$con->set_charset("utf8");

// Retrieve JSON input
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Check if data was received and is in the correct format
if (!isset($data['idDispositivo']) || !isset($data['vlEncoder']) 
	|| !isset($data['vlCorrente']) || !isset($data['vlTensao'])) {
    echo json_encode(["error" => "Invalid input data."]);
    exit();
}

// Prepare SQL insert statement (without idDispositivo)
$sql = $con->prepare("
    INSERT INTO leitura (dtLeitura,idDispositivo, vlEncoder, vlCorrente, vlTensao) 
    VALUES (now(),?,?,?,?)
");

// Bind parameters to the prepared statement
$sql->bind_param(
    "sddd", 
    $data['idDispositivo'], 
    $data['vlEncoder'], 
    $data['vlCorrente'], 
    $data['vlTensao']
);

// Execute the statement
if ($sql->execute()) {
    echo json_encode(["success" => "Data inserted successfully."]);
} else {
    echo json_encode(["error" => "Error inserting data: " . $sql->error]);
}
// Close the statement and connection
$sql->close();
$con->close();
?>
