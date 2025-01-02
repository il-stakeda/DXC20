<?php
  require 'common.php';
  $pdo = connect();
  $st = $pdo->query("DELETE FROM goods WHERE code={$_GET['code']}");
  header('Location: index.php');
?>
