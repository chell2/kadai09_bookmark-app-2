<?php
//PHP:コード記述/修正の流れ
//1. insert.phpの処理をマルっとコピー。
//   POSTデータ受信 → DB接続 → SQL実行 → 前ページへ戻る
//2. $id = POST["id"]を追加
//3. SQL修正
//   "UPDATE テーブル名 SET 変更したいカラムを並べる WHERE 条件"
//   bindValueにも「id」の項目を追加
//4. header関数"Location"を「select.php」に変更

session_start();
include("funcs.php");
sschk();

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
$pdo = db_conn();

// タグづけ処理
function assignTags($inquiry_content, $pdo) {
    // DBからタグを取得
    $sql = "SELECT tag_name FROM tags";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $db_tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $tags = [];
    $inquiry_content = trim($inquiry_content);
    
    // 問い合わせ内容をタグづけ
    foreach ($db_tags as $tag) {
        if (mb_strpos($inquiry_content, $tag) !== false) {
            $tags[] = $tag;
        }
    }
    
    return $tags;
}

// 該当タグをカンマ区切りに
$tags = assignTags($inquiry_content, $pdo);
$tags_string = implode(",", $tags);

//３．データ登録SQL作成
$stmt = $pdo->prepare("UPDATE inquiries SET user_id=:user_id, company_name=:company_name, contact_name=:contact_name, phone=:phone, email=:email, prime_contractor=:prime_contractor, inquiry_content=:inquiry_content, inquiry_datetime=:inquiry_datetime, contact_method=:contact_method, tags=:tags WHERE id=:id");

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':company_name', $company_name, PDO::PARAM_STR);
$stmt->bindValue(':contact_name', $contact_name, PDO::PARAM_STR);
$stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':prime_contractor', $prime_contractor, PDO::PARAM_STR);
$stmt->bindValue(':inquiry_content', $inquiry_content, PDO::PARAM_STR);
$stmt->bindValue(':inquiry_datetime', $inquiry_datetime, PDO::PARAM_STR);
$stmt->bindValue(':contact_method', $contact_method, PDO::PARAM_STR);
$stmt->bindValue(':tags', $tags_string, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
$status = $stmt->execute(); //実行

// 画像の処理

// 1. データベースから現在のファイル名を取得
$current_file_sql = "SELECT file_name FROM inquiries WHERE id = :id";
$current_file_stmt = $pdo->prepare($current_file_sql);
$current_file_stmt->bindValue(':id', $id, PDO::PARAM_INT);
$current_file_stmt->execute();
$current_file_row = $current_file_stmt->fetch(PDO::FETCH_ASSOC);

// 2. 古い画像のファイル名を取得
$current_file_name = $current_file_row['file_name'];
$upload_dir = 'upload/'; // アップロードディレクトリ

// 3. 古い画像を削除
if (!empty($current_file_name)) {
    $current_file_path = $upload_dir . $current_file_name;
    if (file_exists($current_file_path)) {
        unlink($current_file_path); // 古い画像を削除
    }
}

// 4. ファイル名の初期化
$file_names = [];

// 5. ファイルの処理
if (isset($_FILES['image']) && $_FILES['image']['error'][0] == 0) {
    $upload_dir = 'upload/';

    // アップロードされた全てのファイルを処理
    foreach ($_FILES['image']['name'] as $key => $filename) {
        if ($_FILES['image']['error'][$key] == 0) {
            // ファイル名の拡張子を取得
            $ext = pathinfo($filename, PATHINFO_EXTENSION);

            // ID付きファイル名の生成
            $new_filename = $inserted_id . "_" . basename($filename);

            // アップロード先のファイルパスを生成
            $target_file = $upload_dir . $new_filename;

            // ファイルを移動
            if (move_uploaded_file($_FILES['image']['tmp_name'][$key], $target_file)) {
                // アップロードディレクトリを除いたファイル名を配列に追加
                $file_names[] = $new_filename;
            }
        }
    }
}

// 複数ファイルの場合、ファイル名をカンマで連結
$file_name = implode(",", $file_names);

var_dump($file_name);

// アップロードしたファイル名をUPDATEでデータベースに保存
$update_sql = "UPDATE inquiries SET file_name = :file_name WHERE id = :id";
$update_stmt = $pdo->prepare($update_sql);
$update_stmt->bindValue(':file_name', $file_name, PDO::PARAM_STR);
$update_stmt->bindValue(':id', $id, PDO::PARAM_INT); //更新のため既存レコードのIDに修正
$update_status = $update_stmt->execute();

if ($update_status == false) {
    $error = $update_stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

//４．データ登録処理後
if($status==false){
    sql_error($stmt);
}else{
    redirect("dashboard.php");
}

?>
