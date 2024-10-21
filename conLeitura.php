<?php
// Database connection details
$host = 'toucandes.mysql.uhserver.com'; // Update with your database host
$db   = 'toucandes'; // Update with your database name
$user = 'toucandes'; // Update with your database username
$pass = 'touc@n2024'; // Update with your database password

// Create connection
$con = new mysqli($host, $user, $pass, $db);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Set UTF-8 encoding
$con->set_charset("utf8");

// Initialize variables
$dtLeitura = "";
$data = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve input date from form
    if (isset($_POST['dtLeitura'])) {
        $dtLeitura = $_POST['dtLeitura'];
        
        // SQL query to filter by dtLeitura
        $sql = "SELECT idLeitura, idDispositivo, dtLeitura, vlEncoder, vlCorrente, vlTensao 
                FROM leitura 
                WHERE DATE(dtLeitura) = ?";
        
        // Prepare and bind parameters
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("s", $dtLeitura);
            $stmt->execute();
            $result = $stmt->get_result();

            // Fetch all data
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            
            $stmt->close();
        }
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Leitura</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        input[type="date"], input[type="submit"] {
            padding: 10px;
            margin: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: white;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        /* Zebra striping for rows */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Filter Leitura Table by Date</h1>

    <!-- Search form -->
    <form method="POST" action="">
        <label for="dtLeitura">Select Date (dtLeitura): </label>
        <input type="date" id="dtLeitura" name="dtLeitura" required>
        <input type="submit" value="Search">
    </form>

    <!-- Display filtered data -->
    <?php if (!empty($data)) { ?>
        <h2>Search Results:</h2>
        <table>
            <thead>
                <tr>
                    <th>idLeitura</th>
                    <th>idDispositivo</th>
                    <th>dtLeitura</th>
                    <th>vlEncoder</th>
                    <th>vlCorrente</th>
                    <th>vlTensao</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['idLeitura']); ?></td>
                        <td><?php echo htmlspecialchars($row['idDispositivo']); ?></td>
                        <td><?php echo htmlspecialchars($row['dtLeitura']); ?></td>
                        <td><?php echo htmlspecialchars($row['vlEncoder']); ?></td>
                        <td><?php echo htmlspecialchars($row['vlCorrente']); ?></td>
                        <td><?php echo htmlspecialchars($row['vlTensao']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else if ($_SERVER["REQUEST_METHOD"] == "POST") { ?>
        <p>No results found for the selected date.</p>
    <?php } ?>
</body>
</html>
