<?php
session_start();
include("funcs.php");
sschk();

//1. POSTデータ取得
$id = $_POST['id'];
$tag_name = $_POST['tag_name'];

//2. DB接続
$pdo=db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare("UPDATE tags SET tag_name=:tag_name WHERE id=:id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);
$status = $stmt->execute();

//４．データ登録処理後
if($status==false){
  sql_error($stmt);
}else{
  redirect("tags.php");
}
?>
