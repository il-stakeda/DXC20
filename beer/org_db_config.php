<?php
// db_config.php

// MySQL接続設定
$servername = "AAA";  // RDSのエンドポイント
$username = "admin";        // MySQLのユーザー名
$password = "PasswordMySQL";        // MySQLのパスワード
$dbname = "beer_shop";              // 使用するデータベース名

// DB接続のPDOインスタンスを返す関数
function getDBConnection() {
    global $servername, $username, $password, $dbname;
    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        echo "接続失敗: " . $e->getMessage();
        exit;
    }
}
?>