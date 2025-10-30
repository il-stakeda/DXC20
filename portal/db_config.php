<?php
$servername = "database-<yourname>-ins-1.xxxxxx.ap-northeast-1.rds.amazonaws.com";
$username   = "admin";
$password   = "PasswordMySQL";
$dbname     = "insurance_portal";
function getDBConnection() {
  global $servername, $username, $password, $dbname;
  try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
                   $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    echo "接続失敗: " . htmlspecialchars($e->getMessage());
    exit;
  }
}
?>