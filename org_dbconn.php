<?php
$host = 'AAA';  // RDSのエンドポイント
$dbname = 'beer_shop';  // 使用するデータベース名
$username = 'admin';  // RDSのMySQLユーザー名
$password = 'PasswordMySQL';  // RDSのMySQLパスワード

try {
    // PDOを使ってMySQLに接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 接続成功の場合
    echo "***成功***";
} catch (PDOException $e) {
    // 接続失敗の場合
    echo "***失敗***: " . $e->getMessage();
}
?>