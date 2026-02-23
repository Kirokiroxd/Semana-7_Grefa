<?php
// config/db.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "tienda_random";

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function money($cents) {
  return number_format($cents / 100, 2, '.', '');
}

function require_auth() {
  if (empty($_SESSION["user"])) {
    header("Location: /tienda_marcos/auth/login.php");
    exit;
  }
}