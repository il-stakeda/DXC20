<?php
  session_start();
  function connect() {

$user = "admin";
$password = "PasswordMySQL";
$dns="database-takedasaori.#######.ap-northeast-1.rds.amazonaws.com";

    return new PDO("mysql:host=$dns; dbname=shop; charset=utf8", "$user", "$password");
  }

  function img_tag($code) {
    if (file_exists("images/$code.jpg")) $name = $code;
    else $name = 'noimage';
    return '<img src="images/' . $name . '.jpg" alt="">';
  }
?>
