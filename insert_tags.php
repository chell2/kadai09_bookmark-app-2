<?php
//1. POSTデータ取得
$tag_name = $_POST['tag_name'];

// 確認
var_dump($tag_name);

//2. DB接続
include("funcs.php");
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
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
    $error = $stmt->errorInfo();
    exit("SQL_ERROR:".$error[2]);
}else{
    //５．index.phpへリダイレクト
    header("Location: tags.php");
    // redirect("tags.php");
    exit();
}

?>
