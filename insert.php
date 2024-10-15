<?php
session_start();
include("funcs.php");
sschk();

//1. POSTデータ取得
$user_id = $_POST['user_id'];
$company_name = $_POST['company_name'];
$contact_name = $_POST['contact_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$prime_contractor = $_POST['prime_contractor'];
$inquiry_content = $_POST['inquiry_content'];
$inquiry_datetime = $_POST['inquiry_datetime']; 
$contact_method = $_POST['contact_method'];

// 確認
// var_dump($user_id.$company_name.$contact_name.$phone.$email.$prime_contractor.$inquiry_content.$inquiry_datetime.$contact_method);

//2. DB接続
$pdo=db_conn();

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
$sql = "INSERT INTO inquiries (user_id, company_name, contact_name, phone, email, prime_contractor, inquiry_content, inquiry_datetime, contact_method, tags)
VALUES (:user_id, :company_name, :contact_name, :phone, :email, :prime_contractor, :inquiry_content, :inquiry_datetime, :contact_method, :tags);";
$stmt = $pdo->prepare($sql);
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
$status = $stmt->execute();

// データ挿入後のIDを取得
$inserted_id = $pdo->lastInsertId();

// 画像の処理
$file_names = []; // 初期化

if (isset($_FILES['image']) && $_FILES['image']['error'][0] == 0) {
    $upload_dir = 'upload/';

    // アップロードディレクトリが存在しない場合に作成
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            die("ディレクトリの作成に失敗しました。");
        }
    }

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
$update_stmt->bindValue(':id', $inserted_id, PDO::PARAM_INT);
$update_status = $update_stmt->execute();

if ($update_status == false) {
    $error = $update_stmt->errorInfo();
    exit("SQL_ERROR: " . $error[2]);
}

//４．データ登録処理後
if($status==false){
    //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）:funcsで関数化
    sql_error($stmt);
}else{
    //５．index.phpへリダイレクト:funcsで関数化
    redirect("index.php");
}

?>
