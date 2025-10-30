<?php
require_once __DIR__ . '/db_config.php';
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, name, annual_premium, description, image_path FROM policies ORDER BY id ASC");
$policies = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>保険見積ポータル</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:sans-serif; margin:24px;}
    .grid{display:grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap:16px;}
    .card{border:1px solid #ddd; border-radius:12px; padding:16px;}
    img{max-width:100%; height:120px; object-fit:contain;}
    .price{font-weight:bold;}
    form{margin-top:12px;}
    input,select,button{padding:8px; width:100%; margin-top:6px;}
    button{cursor:pointer;}
  </style>
</head>
<body>
  <h1>保険見積ポータル</h1>
  <p>取り扱い商品からプランを選び、必要事項を入力して「見積依頼」を送信してください。</p>
  <div class="grid">
    <?php foreach ($policies as $p): ?>
    <div class="card">
      <img src="<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
      <h3><?php echo htmlspecialchars($p['name']); ?></h3>
      <p class="price">年間保険料（目安）：¥<?php echo number_format($p['annual_premium']); ?></p>
      <?php if (!empty($p['description'])): ?>
        <p><?php echo nl2br(htmlspecialchars($p['description'])); ?></p>
      <?php endif; ?>
      <form action="apply.php" method="post">
        <input type="hidden" name="policy_id" value="<?php echo (int)$p['id']; ?>">
        <label>お名前<input type="text" name="customer_name" required></label>
        <label>メールアドレス<input type="email" name="email" required></label>
        <label>契約年数
          <select name="term_years" required>
            <option value="1">1年</option>
            <option value="2">2年</option>
            <option value="3">3年</option>
          </select>
        </label>
        <button type="submit">見積依頼を送信</button>
      </form>
    </div>
    <?php endforeach; ?>
  </div>
</body>
</html>