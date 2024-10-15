<?php
session_start();
include("funcs.php");
sschk();

//1. POSTデータ取得
$tag_name = $_POST['tag_name'];

// 確認
// var_dump($tag_name);

//2. DB接続
$pdo=db_conn();

//３．データ登録SQL作成
$sql = "INSERT INTO tags (tag_name)
VALUES (:tag_name);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);
$status = $stmt->execute();

// データ挿入後のIDを取得
$inserted_id = $pdo->lastInsertId();

//４．データ登録処理後
if($status==false){
    sql_error($stmt);
}else{
    //５．index.phpへリダイレクト
    redirect("tags.php");
}

?>
