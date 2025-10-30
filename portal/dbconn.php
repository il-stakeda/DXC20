<?php
require_once __DIR__ . '/db_config.php';
try {
  $pdo = getDBConnection();
  $ok  = $pdo->query("SHOW TABLES FROM insurance_portal")->fetchAll();
  echo "***成功*** テーブル数: " . count($ok);
} catch (Throwable $e) {
  echo "***失敗***: " . htmlspecialchars($e->getMessage());
}
?>