<?php
session_start();

// db_config.php をインクルードして、DB接続情報を使用
require_once 'db_config.php';

// 商品情報をデータベースから取得
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ビール購入ページ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        .product-container {
            display: flex;               /* 横並びに変更 */
            justify-content: space-around; /* 商品間に均等にスペースを配置 */
            gap: 30px;                   /* 商品間の間隔 */
            flex-wrap: wrap;             /* 画面が狭くなった場合、折り返して表示 */
            margin-top: 30px;
        }
        .product {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            width: 250px;               /* 商品カードの幅 */
            flex: 0 1 calc(33% - 30px); /* 商品カードの最大幅を指定、横並び */
            box-sizing: border-box;     /* paddingを含めた幅を指定 */
        }
        .product img {
            max-width: 100%;    /* 画像の最大幅を商品カードの幅に合わせる */
            height: auto;       /* 高さは自動的に調整 */
            border-radius: 8px; /* 画像の角を丸める */
        }
        .product h2 {
            font-size: 1.5em;
            color: #333;
            margin: 10px 0;
        }
        .product label {
            display: block;
            margin-top: 10px;
            font-size: 1.1em;
        }
        .product input {
            margin-top: 5px;
            width: 50px;
            text-align: center;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin-top: 20px;
            width: 100%;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <h1>ビールを購入する</h1>
    <form action="purchase.php" method="POST">
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?= htmlspecialchars($product['image_path']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                    <h2><?= htmlspecialchars($product['name']); ?></h2>
                    <label for="product_<?= $product['id']; ?>">数量: </label>
                    <input type="number" id="product_<?= $product['id']; ?>" name="product[<?= $product['id']; ?>]" min="0" value="0">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="submit-btn">購入確定</button>
    </form>
</body>
</html>