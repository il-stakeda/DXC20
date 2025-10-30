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
$stmt = $pdo->prepare("SELECT id, name FROM policies WHERE id = :id");
$stmt->execute([':id' => $policy_id]);
$policy = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$policy) {
  http_response_code(404);
  echo "対象の保険商品が見つかりません。";
  exit;
}
$ins = $pdo->prepare("INSERT INTO applications (policy_id, customer_name, email, term_years)
                      VALUES (:policy_id, :customer_name, :email, :term_years)");
$ins->execute([
  ':policy_id' => $policy_id,
  ':customer_name' => $customer_name,
  ':email' => $email,
  ':term_years' => $term_years
]);
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>見積依頼を受け付けました</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>body{font-family:sans-serif; margin:24px;} a{display:inline-block; margin-top:16px;}</style>
</head>
<body>
  <h1>見積依頼を受け付けました</h1>
  <p><?php echo htmlspecialchars($customer_name); ?> 様、<br>
     「<?php echo htmlspecialchars($policy['name']); ?>」の見積依頼を受け付けました。</p>
  <a href="index.php">商品一覧へ戻る</a>
</body>
</html>