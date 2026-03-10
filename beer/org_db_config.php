<?php
// db_config.php

// MySQL接続設定
$servername = "AAA";
$username = "admin";        // MySQLのユーザー名
$password = "PasswordMySQL";  // MySQLのパスワード
$dbname = "beer_shop";      // 使用するデータベース名

// DB接続のPDOインスタンスを返す関数
function getDBConnection() {
    global $servername, $username, $password, $dbname;
    try {
        // SSL検証を無効化するためのオプション設定
        $options = [
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false, // SSLの証明書検証を無効にする
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // エラー時に例外を投げる
        ];

        // 第4引数に $options を追加
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password, $options);
        
        return $pdo;
    } catch (PDOException $e) {
        echo "接続失敗:" . $e->getMessage();
        exit;
    }
}
?>
