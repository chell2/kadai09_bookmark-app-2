<?php
//PHP:コード記述/修正の流れ
//1. insert.phpの処理をマルっとコピー。
//   POSTデータ受信 → DB接続 → SQL実行 → 前ページへ戻る
//2. $id = POST["id"]を追加
//3. SQL修正
//   "UPDATE テーブル名 SET 変更したいカラムを並べる WHERE 条件"
//   bindValueにも「id」の項目を追加
//4. header関数"Location"を「select.php」に変更

//1. POSTデータ取得
$id = $_POST['id'];
$user_id = $_POST['user_id'];
$company_name = $_POST['company_name'];
$contact_name = $_POST['contact_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$prime_contractor = $_POST['prime_contractor'];
$inquiry_content = $_POST['inquiry_content'];
$inquiry_datetime = $_POST['inquiry_datetime'];
$contact_method = $_POST['contact_method'];

//2. DB接続します
include("funcs.php");
$pdo = db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare("UPDATE inquiries SET user_id=:user_id, company_name=:company_name, contact_name=:contact_name, phone=:phone, email=:email, prime_contractor=:prime_contractor, inquiry_content=:inquiry_content, inquiry_datetime=:inquiry_datetime, contact_method=:contact_method WHERE id=:id");

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':company_name', $company_name, PDO::PARAM_STR);
$stmt->bindValue(':contact_name', $contact_name, PDO::PARAM_STR);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':prime_contractor', $prime_contractor, PDO::PARAM_STR);
$stmt->bindValue(':inquiry_content', $inquiry_content, PDO::PARAM_STR);
$stmt->bindValue(':inquiry_datetime', $inquiry_datetime, PDO::PARAM_STR);
$stmt->bindValue(':contact_method', $contact_method, PDO::PARAM_STR);

$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute(); //実行

//４．データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("dashboard.php");
}
?>
