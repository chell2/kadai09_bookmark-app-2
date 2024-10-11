<?php
//$_SESSION使うよ！
// session_start();

//※htdocsと同じ階層に「includes」を作成してfuncs.phpを入れましょう！
//include "../../includes/funcs.php";
include "funcs.php";
// sschk();

//1. POSTデータ取得
$user_name = filter_input( INPUT_POST, "user_name" );
$user_email = filter_input( INPUT_POST, "user_email" );
$user_pw = filter_input( INPUT_POST, "user_pw" );
$is_admin = filter_input( INPUT_POST, "is_admin" );
$user_pw = password_hash($user_pw, PASSWORD_DEFAULT);   //パスワードハッシュ化

//2. DB接続します
$pdo = db_conn();

//３．データ登録SQL作成
$sql = "INSERT INTO users(user_name, user_email, user_pw, is_admin, life_flg) VALUES (:user_name, :user_email, :user_pw, :is_admin, 0)";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
$stmt->bindValue(':user_email', $user_email, PDO::PARAM_STR);
$stmt->bindValue(':user_pw', $user_pw, PDO::PARAM_STR);
$stmt->bindValue(':is_admin', $is_admin, PDO::PARAM_INT);
$status = $stmt->execute();

//４．データ登録処理後
if ($status == false) {
    sql_error($stmt);
} else {
    redirect("users.php");
}
