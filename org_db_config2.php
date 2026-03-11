<?php
// RDSの接続情報
$servername = "AAA"; // AAAを実際の値に変更
$username   = "admin";
$password   = "PasswordMySQL";
$dbname     = "insurance_portal";

// SSL証明書のパス（先ほどダウンロードした場所）
$ssl_ca_path = "/etc/pki/tls/certs/global-bundle.pem";

function getDBConnection() {
    global $servername, $username, $password, $dbname, $ssl_ca_path;

    try {
        // PDOのオプション設定
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // SSL接続を強制し、CA証明書を指定する設定
            PDO::MYSQL_ATTR_SSL_CA => $ssl_ca_path,
            // サーバー証明書の検証を有効にする（セキュリティ上推奨）
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
        ];

        // DSN（Data Source Name）の作成
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";

        // PDOインスタンスの作成
        $pdo = new PDO($dsn, $username, $password, $options);
        
        return $pdo;

    } catch (PDOException $e) {
        // エラーメッセージの出力
        echo "接続失敗: " . htmlspecialchars($e->getMessage());
        exit;
    }
}
?>
