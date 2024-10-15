<?php
session_start();
include("funcs.php");
sschk();

//1. POSTデータ取得
$id = $_POST['id'];
$user_name = $_POST['user_name'];
$user_email = $_POST['user_email'];
$user_pw = $_POST['user_pw'];
$is_admin = $_POST['is_admin'];
$life_flg = isset($_POST['life_flg']) ? $_POST['life_flg'] : 0;
$user_pw = password_hash($user_pw, PASSWORD_DEFAULT);

//2. DB接続
$pdo=db_conn();

//３．データ登録SQL作成
$stmt = $pdo->prepare("UPDATE users SET user_name=:user_name, user_email=:user_email, user_pw=:user_pw, is_admin=:is_admin, life_flg=:life_flg WHERE id=:id");
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$stmt->bindValue(':user_name', $user_name, PDO::PARAM_STR);
$stmt->bindValue(':user_email', $user_email, PDO::PARAM_STR);
$stmt->bindValue(':user_pw', $user_pw, PDO::PARAM_STR);
$stmt->bindValue(':is_admin', $is_admin, PDO::PARAM_INT);
$stmt->bindValue(':life_flg', $life_flg, PDO::PARAM_INT);
$status = $stmt->execute();

// データ挿入後のIDを取得
$inserted_id = $pdo->lastInsertId();

//４．データ登録処理後
if ($status == false) {
    sql_error($stmt);
} else {
    // life_flgが1の場合、ログアウト処理を実行
    if ($life_flg == 1) {
        // セッションの初期化〜破棄
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 42000, '/');
        }
        session_destroy();
        // 処理後リダイレクト
        redirect("login.php");
    } else {
        // 通常はusersへ戻る
        redirect("users.php");
    }
}
?>