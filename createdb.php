<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mycvdatabase";  

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Грешка при връзка с MySQL сървъра: " . $conn->connect_error);
}

$conn->select_db($dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    var_dump($_POST);

    $first_name = $_POST["first_name"];
    $middle_name = $_POST["middle_name"];
    $last_name = $_POST["last_name"];
    $birth_date = $_POST["birth_date"];
    $university = $_POST["university"];
    $skills = implode(", ", $_POST["skills"]);

$sql = "INSERT INTO cv (first_name, middle_name, last_name, birth_date, university, skills) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $first_name, $middle_name, $last_name, $birth_date, $university, $skills);

    if ($stmt->execute()) {
        echo "Данните бяха успешно записани в базата данни.";
    } else {
        echo "Грешка при запис на данните: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
