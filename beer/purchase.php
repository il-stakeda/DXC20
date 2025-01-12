<?php
session_start();

// db_config.php をインクルードして、DB接続情報を使用
require_once 'db_config.php';

// フォームからの入力を取得
$products = isset($_POST['product']) ? $_POST['product'] : [];

// DB接続
$pdo = getDBConnection();

// 注文が1つでもある場合にDBに書き込み
foreach ($products as $productId => $quantity) {
    if ($quantity > 0) {
        // 商品情報をデータベースから取得
        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            try {
                $stmt = $pdo->prepare("INSERT INTO orders (product_name, quantity) VALUES (?, ?)");
                $stmt->execute([$product['name'], $quantity]);
            } catch (PDOException $e) {
                echo "エラー: " . $e->getMessage();
                exit;
            }
        }
    }
}

// 購入完了ページ
echo "<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <title>購入完了</title>
    <style>
        /* スタイルはそのまま */
    </style>
</head>
<body>
    <h1>購入が完了しました！</h1>";

foreach ($products as $productId => $quantity) {
    if ($quantity > 0) {
        $stmt = $pdo->prepare("SELECT name FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>{$product['name']}: $quantity 本</p>";
    }
}

echo "<p>ありがとうございます。</p>
</body>
</html>";
?>