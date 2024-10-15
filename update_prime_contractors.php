<?php
session_start();
include("funcs.php");
sschk();

// 1. POSTデータ取得とバリデーション
$id = isset($_POST['id']) ? intval($_POST['id']) : null;
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$life_flg = isset($_POST['life_flg']) ? $_POST['life_flg'] : 0;

if (empty($id) || empty($name)) {
    exit('IDまたは名称が不正です');
}

// 2. DB接続
$pdo = db_conn();

// 3. データ登録SQL作成
$stmt = $pdo->prepare("UPDATE prime_contractors SET name=:name, life_flg=:life_flg WHERE id=:id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':life_flg', $life_flg, PDO::PARAM_INT);
$status = $stmt->execute();

// 4. データ登録処理後
if ($status == false) {
    sql_error($stmt);
} else {
    redirect("prime_contractors.php");
}
?>
