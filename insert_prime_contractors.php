<?php
session_start();
include("funcs.php");
sschk();

//1. POSTデータ取得
$name = $_POST['name'];

// 確認
var_dump($name);

//2. DB接続
$pdo=db_conn();

//３．データ登録SQL作成
$sql = "INSERT INTO prime_contractors (name)
VALUES (:name);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$status = $stmt->execute();

// データ挿入後のIDを取得
$inserted_id = $pdo->lastInsertId();

//４．データ登録処理後
if($status==false){
    sql_error($stmt);
}else{
    //５．index.phpへリダイレクト
    redirect("prime_contractors.php");
}

?>
