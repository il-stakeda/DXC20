<?php
require_once __DIR__ . '/db_config.php';
$pdo = getDBConnection();

$policy_id     = isset($_POST['policy_id']) ? (int)$_POST['policy_id'] : 0;
$customer_name = trim($_POST['customer_name'] ?? '');
$email         = trim($_POST['email'] ?? '');
$term_years    = isset($_POST['term_years']) ? (int)$_POST['term_years'] : 0;

if ($policy_id <= 0 || $customer_name === '' || $email === '' || $term_years <= 0) {
  http_response_code(400);
  echo "入力内容に不備があります。";
  exit;
}

// 保険商品の詳細を取得
$stmt = $pdo->prepare("SELECT id, name, annual_premium FROM policies WHERE id = :id");
$stmt->execute([':id' => $policy_id]);
$policy = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$policy) {
  http_response_code(404);
  echo "対象の保険商品が見つかりません。";
  exit;
}

// データベースに保存
$ins = $pdo->prepare("INSERT INTO applications (policy_id, customer_name, email, term_years)
                      VALUES (:policy_id, :customer_name, :email, :term_years)");
$ins->execute([
  ':policy_id' => $policy_id,
  ':customer_name' => $customer_name,
  ':email' => $email,
  ':term_years' => $term_years
]);

// 合計金額の簡易計算（例：単価 × 年数）
$total_estimate = $policy['annual_premium'] * $term_years;
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>見積依頼完了</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:sans-serif; margin:24px; line-height:1.6;}
    .summary-box { border: 2px solid #4CAF50; padding: 20px; border-radius: 8px; background: #f9f9f9; margin: 20px 0; }
    .footer-links { margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
  </style>
</head>
<body>
  <h1>見積依頼を完了しました</h1>
  <p><?php echo htmlspecialchars($customer_name); ?> 様、ありがとうございます。以下の内容で承りました。</p>

  <div class="summary-box">
    <h3>今回のお見積り内容</h3>
    <table>
      <tr><th>プラン名</th><td><?php echo htmlspecialchars($policy['name']); ?></td></tr>
      <tr><th>契約年数</th><td><?php echo htmlspecialchars($term_years); ?>年</td></tr>
      <tr><th>年間保険料</th><td>¥<?php echo number_format($policy['annual_premium']); ?></td></tr>
      <tr><th>概算合計金額</th><td><strong>¥<?php echo number_format($total_estimate); ?></strong></td></tr>
    </table>
  </div>

  <div class="footer-links">
    <a href="index.php">商品一覧へ戻る</a> | 
    <a href="history.php?email=<?php echo urlencode($email); ?>">自分の見積履歴を確認する</a>
  </div>
</body>
</html>
