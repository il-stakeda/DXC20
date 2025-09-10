<?php
session_start();

require_once 'db_config.php';

// フォームからの入力（未送信時も想定）
$posted = isset($_POST['product']) && is_array($_POST['product']) ? $_POST['product'] : [];

// DB接続
$pdo = getDBConnection();

// まとめて商品名を引くための準備（ID -> name のマップを作る）
$productNames = [];
if (!empty($posted)) {
    $ids = array_keys($posted);
    $ids = array_map('intval', $ids);
    $ids = array_values(array_filter($ids, fn($v) => $v > 0));

    if (!empty($ids)) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, name FROM products WHERE id IN ($in)");
        $stmt->execute($ids);
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $productNames[(int)$row['id']] = $row['name'];
        }
    }
}

// 注文データ作成（表示＆保存用）。数量は整数に正規化、0以下は除外
$orders = [];
foreach ($posted as $productId => $quantity) {
    $pid = (int)$productId;
    $qty = filter_var($quantity, FILTER_VALIDATE_INT, ['options' => ['default' => 0]]);
    if ($qty > 0 && isset($productNames[$pid])) {
        $orders[] = ['name' => $productNames[$pid], 'qty' => $qty];
    }
}

// DBへ保存
if (!empty($orders)) {
    $ins = $pdo->prepare("INSERT INTO orders (product_name, quantity) VALUES (?, ?)");
    foreach ($orders as $o) {
        try {
            $ins->execute([$o['name'], $o['qty']]);
        } catch (PDOException $e) {
            echo "エラー: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            exit;
        }
    }
}

// --- AZ取得（IMDSv2 優先, v1フォールバック） ---
function getAz(): string {
    // トークン取得
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
    <title>購入完了</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; text-align: center; padding: 50px; }
        h1 { color: #333; }
        .summary { max-width: 520px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border-bottom: 1px solid #eee; padding: 10px; }
        th { background: #fafafa; text-align: left; }
        .az { margin-top: 20px; font-size: 0.9em; color: #555; }
        .back-btn { display: inline-block; margin-top: 30px; padding: 10px 20px; background-color: #007bff; color: #fff; text-decoration: none; border-radius: 5px; }
        .back-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <h1>購入が完了しました！</h1>

    <div class="summary">
        <?php if (!empty($orders)): ?>
            <p>以下の内容で注文を受け付けました。</p>
            <table>
                <thead>
                    <tr><th>商品名</th><th style="text-align:right;">本数</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><?= htmlspecialchars($o['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td style="text-align:right;"><?= (int)$o['qty'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>選択された商品はありませんでした。</p>
        <?php endif; ?>
        <p>ありがとうございます。</p>
        <div class="az">実行アベイラビリティゾーン: <?= htmlspecialchars($az, ENT_QUOTES, 'UTF-8') ?></div>
    </div>

    <!-- 元の購入ページに戻る -->
    <a href="index.php" class="back-btn">商品ページに戻る</a>
</body>
</html>
