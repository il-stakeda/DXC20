<?php
  require 'common.php';
  $rows = array();
  $sum = 0;
  $pdo = connect();
  if (!isset($_SESSION['cart'])) $_SESSION['cart'] = array();
  if (@$_POST['submit']) {
    @$_SESSION['cart'][$_POST['code']] += $_POST['num'];
  }
  foreach($_SESSION['cart'] as $code => $num) {
    $st = $pdo->prepare("SELECT * FROM goods WHERE code=?");
    $st->execute(array($code));
    $row = $st->fetch();
    $st->closeCursor();
    $row['num'] = strip_tags($num);
    $sum += $num * $row['price'];
    $rows[] = $row;
  }
  require 't_cart.php';
?>
