<?php
echo "Database connection with SSL : ";

$endpoint = "AAA";
$user = "admin";
$pass = "PasswordMySQL";
$db = "insurance_portal";

// 1. mysqliの初期化
$link = mysqli_init();

// 2. SSL証明書のパスを指定（先ほどダウンロードした場所）
// 第4引数にCA証明書のパスを渡します
mysqli_ssl_set($link, NULL, NULL, '/etc/pki/tls/certs/global-bundle.pem', NULL, NULL);

// 3. 接続実行（第7引数に MYSQLI_CLIENT_SSL を指定）
$connected = mysqli_real_connect(
    $link, 
    $endpoint, 
    $user, 
    $pass, 
    $db, 
    3306, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$connected) {
    die("**ERROR** " . mysqli_connect_error() . "\n");
}

echo "SUCCESS\n";

// 接続を閉じる
mysqli_close($link);
?>
