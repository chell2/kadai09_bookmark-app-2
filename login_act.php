<?php
//最初にSESSIONを開始！！ココ大事！！
session_start();
include("funcs.php");

//POST値
$user_email = $_POST["user_email"]; //lid
$user_pw = $_POST["user_pw"]; //lpw

//1.  DB接続します
$pdo = db_conn();

//2. データ登録SQL作成
//* PasswordがHash化→条件はlidのみ！！
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_email=:user_email AND life_flg=0"); 
$stmt->bindValue(':user_email', $user_email, PDO::PARAM_STR);
$status = $stmt->execute();

//3. SQL実行時にエラーがある場合STOP
if($status==false){
    sql_error($stmt);
}

//4. 抽出データ数を取得
$val = $stmt->fetch();         //1レコードだけ取得する方法
//$count = $stmt->fetchColumn(); //SELECT COUNT(*)で使用可能()


//5.該当１レコードがあればSESSIONに値を代入
//入力したPasswordと暗号化されたPasswordを比較！[戻り値：true,false]
$pw = password_verify($user_pw, $val["user_pw"]); //$lpw = password_hash($lpw, PASSWORD_DEFAULT);   //パスワードハッシュ化
if($pw){ 
  //Login成功時
  $_SESSION["chk_ssid"] = session_id();
  $_SESSION["is_admin"] = $val['is_admin'];
  $_SESSION["user_name"] = $val['user_name'];
  //Login成功時（select.phpへ）
  redirect("index.php");

}else{
  //Login失敗時(login.phpへ)
  redirect("login.php");

}

exit();


