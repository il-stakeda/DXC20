<?php
  require 'common.php';
  $error = $name = $address = $tel = '';
  if (@$_POST['submit']) {
    $name = htmlspecialchars($_POST['name']);
    $address = htmlspecialchars($_POST['address']);
    $tel = htmlspecialchars($_POST['tel']);
    if (!$name) $error .= 'お名前を入力してください。<br>';
    if (!$address) $error .= 'ご住所を入力してください。<br>';
    if (!$tel) $error .= '電話番号を入力してください。<br>';
    if (preg_match('/[^\d-]/', $tel)) $error .= '電話番号が正しくありません。<br>';
    if (!$error) {
      $pdo = connect();
        // 1. 購入者情報を `orders` テーブルに挿入
        $st = $pdo->prepare("INSERT INTO orders (name, address, tel) VALUES (?, ?, ?)");
        $st->execute([$name, $address, $tel]);
        $order_id = $pdo->lastInsertId();  // 新規注文のIDを取得

        // 2. 購入した商品情報を `order_items` テーブルに挿入
        foreach ($_SESSION['cart'] as $code => $num) {
            $st = $pdo->prepare("SELECT * FROM goods WHERE code = ?");
            $st->execute([$code]);
            $row = $st->fetch();

            $product_name = $row['name'];
            $price = $row['price'];
            $total_price = $price * $num;

            // 商品情報を `order_items` テーブルに挿入
            $st = $pdo->prepare("INSERT INTO order_items (order_id, product_code, product_name, quantity, price, total_price) VALUES (?, ?, ?, ?, ?, ?)");
            $st->execute([$order_id, $code, $product_name, $num, $price, $total_price]);
        }
      $_SESSION['cart'] = null;
      require 't_buy_complete.php';
      exit();
    }
  }
  require 't_buy.php';
?>
