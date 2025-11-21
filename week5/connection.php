<?php
$conn = new mysqli("localhost", "root", "", "ins3064");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

