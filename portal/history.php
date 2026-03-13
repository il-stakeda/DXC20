<?php
require_once __DIR__ . '/db_config.php';
$pdo = getDBConnection();

$email = trim($_GET['email'] ?? '');
$applications = [];

if ($email !== '') {
    // applicationsとpoliciesをJOINして詳細な情報を取得
    $sql = "SELECT a.*, p.name as policy_name, p.annual_premium 
            FROM applications a 
            JOIN policies p ON a.policy_id = p.id 
            WHERE a.email = :email 
            ORDER BY a.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>見積履歴検索</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:sans-serif; margin:24px;}
    .search-box { margin-bottom: 30px; background: #eee; padding: 15px; border-radius: 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #f4f4f4; }
    .no-data { color: #888; }
  </style>
</head>
<body>
  <h1>見積依頼履歴</h1>

  <div class="search-box">
    <form action="history.php" method="get">
      <label>登録したメールアドレスで検索：
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
      </label>
      <button type="submit">履歴を表示</button>
    </form>
  </div>

  <?php if ($email !== ''): ?>
    <h2>「<?php echo htmlspecialchars($email); ?>」様の履歴</h2>
    <?php if (count($applications) > 0): ?>
      <table>
        <thead>
          <tr>
            <th>日時</th>
            <th>プラン名</th>
            <th>契約年数</th>
            <th>概算合計</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($applications as $app): ?>
          <tr>
            <td><?php echo htmlspecialchars($app['created_at']); ?></td>
            <td><?php echo htmlspecialchars($app['policy_name']); ?></td>
            <td><?php echo htmlspecialchars($app['term_years']); ?>年</td>
            <td>¥<?php echo number_format($app['annual_premium'] * $app['term_years']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p class="no-data">該当する見積履歴が見つかりませんでした。</p>
    <?php endif; ?>
  <?php endif; ?>

  <p><a href="index.php">商品一覧へ戻る</a></p>
</body>
</html>
