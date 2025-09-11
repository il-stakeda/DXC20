<?php
session_start();

// db_config.php をインクルードして、DB接続情報を使用
require_once 'db_config.php';

// 商品情報をデータベースから取得
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- IMDSv2でAZ取得（v1フォールバック、1秒タイムアウト、プロキシ回避） ---
function getAz(): string {
    // 1) トークン取得 (IMDSv2)
    $t = curl_init('http://169.254.169.254/latest/api/token');
    curl_setopt_array($t, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'PUT',
        CURLOPT_HTTPHEADER     => ['X-aws-ec2-metadata-token-ttl-seconds: 60'],
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_TIMEOUT        => 1,
        CURLOPT_NOPROXY        => '169.254.169.254',
    ]);
    $token = curl_exec($t);
    $codeT = curl_getinfo($t, CURLINFO_RESPONSE_CODE);
    curl_close($t);

    // 2) AZ取得（IMDSv2優先、失敗時はIMDSv1）
    $headers = ($codeT === 200 && $token) ? ['X-aws-ec2-metadata-token: '.$token] : [];
    $c = curl_init('http://169.254.169.254/latest/meta-data/placement/availability-zone');
    curl_setopt_array($c, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_TIMEOUT        => 1,
        CURLOPT_NOPROXY        => '169.254.169.254',
    ]);
    $az   = curl_exec($c);
    $code = curl_getinfo($c, CURLINFO_RESPONSE_CODE);
    curl_close($c);

    return ($code === 200 && $az) ? trim($az) : '取得できませんでした';
}
$az = getAz();
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
            margin: 0; padding: 0;
        }
        h1 { text-align: center; margin-top: 20px; }
        .product-container {
            display: flex; justify-content: space-around; gap: 30px;
            flex-wrap: wrap; margin-top: 30px;
        }
        .product {
            background-color: #fff; border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px; text-align: center; width: 250px;
            flex: 0 1 calc(33% - 30px); box-sizing: border-box;
        }
        .product img { max-width: 100%; height: auto; border-radius: 8px; }
        .product h2 { font-size: 1.5em; color: #333; margin: 10px 0; }
        .product label { display: block; margin-top: 10px; font-size: 1.1em; }
        .product input { margin-top: 5px; width: 50px; text-align: center; }
        .submit-btn {
            background-color: #28a745; color: #fff; border: none;
            padding: 10px; border-radius: 5px; cursor: pointer;
            font-size: 1.1em; margin-top: 20px; width: 100%;
        }
        .submit-btn:hover { background-color: #218838; }
        .az { text-align:center; margin:20px 0 30px; color:#555; font-size: .95em; }
    </style>
</head>
<body>
    <h1>ビールを購入する</h1>

    <form action="purchase.php" method="POST">
        <div class="product-container">
            <?php foreach ($products as $product): ?>
                <div class="product">
                    <img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <h2><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <label for="product_<?= (int)$product['id']; ?>">数量: </label>
                    <input type="number" id="product_<?= (int)$product['id']; ?>"
                           name="product[<?= (int)$product['id']; ?>]" min="0" value="0">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="submit-btn">購入確定</button>
    </form>

    <div class="az">
        実行アベイラビリティゾーン: <?= htmlspecialchars($az, ENT_QUOTES, 'UTF-8'); ?>
    </div>
</body>
</html>
