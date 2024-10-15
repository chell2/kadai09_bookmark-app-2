<?php
session_start();
include("funcs.php");
sschk();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB接続
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

// フォームがPOSTメソッドで送信されたときだけ処理を実行
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // POSTデータ取得
    $id = $_POST["id"];
    $user_id = $_POST["user_id"];
    $company_name = $_POST["company_name"];
    $contact_name = $_POST["contact_name"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $prime_contractor = $_POST["prime_contractor"];
    $inquiry_content = $_POST["inquiry_content"];
    $inquiry_datetime = $_POST["inquiry_datetime"];
    $contact_method = $_POST["contact_method"];
    
    // タグの処理
    // 該当タグをカンマ区切りに
    $tags = assignTags($inquiry_content, $pdo);
    $tags_string = implode(",", $tags);
    
    // 画像の処理
    // 1. 既存のファイル名を取得
    $stmt = $pdo->prepare("SELECT file_name FROM inquiries WHERE id=:id");
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $existing_file = $stmt->fetchColumn();

    // 2. 新しいファイルを処理
    if (!empty($_FILES['image']['name'][0])) {
        $file_names = [];
        
        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['image']['name'][$key];
            $file_tmp = $_FILES['image']['tmp_name'][$key];

            // ファイルの拡張子を取得
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);

            // ファイル名を生成（タイムスタンプ + ランダム文字列）
            $unique_name = uniqid(date('YmdHis') . '_') . '.' . $ext;

            // 保存先ディレクトリ
            $upload_path = "upload/" . $unique_name;

            // ファイルを移動
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $file_names[] = $unique_name; // 保存したファイル名を配列に追加
            }
        }

        // 3. 既存のファイル名がある場合は結合
        if (!empty($existing_file)) {
            $existing_files = explode(',', $existing_file); // 既存ファイルを配列に変換
            $file_names = array_merge($existing_files, $file_names);
        }

        // 4. ファイル名をカンマ区切りで保存
        $file_names_string = implode(',', $file_names);
        } else {
            // 新しいファイルがアップロードされなかった場合は、既存のファイル名を使用
            $file_names_string = $existing_file;
        }

        // 5. データをデータベースに更新
        $stmt = $pdo->prepare("UPDATE inquiries SET 
            user_id=:user_id,
            company_name=:company_name,
            contact_name=:contact_name,
            phone=:phone,
            email=:email,
            prime_contractor=:prime_contractor,
            inquiry_content=:inquiry_content,
            contact_method=:contact_method,
            inquiry_datetime=:inquiry_datetime,
            file_name=:file_name,
            tags=:tags
            WHERE id=:id");

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);  //Integer（数値の場合 PDO::PARAM_INT)
        $stmt->bindValue(':company_name', $company_name, PDO::PARAM_STR);
        $stmt->bindValue(':contact_name', $contact_name, PDO::PARAM_STR);
        $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':prime_contractor', $prime_contractor, PDO::PARAM_STR);
        $stmt->bindValue(':inquiry_content', $inquiry_content, PDO::PARAM_STR);
        $stmt->bindValue(':inquiry_datetime', $inquiry_datetime, PDO::PARAM_STR);
        $stmt->bindValue(':contact_method', $contact_method, PDO::PARAM_STR);
        $stmt->bindValue(":file_name", $file_names_string);
        $stmt->bindValue(':tags', $tags_string, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        // 6. データを更新
        if ($stmt->execute()) {
            redirect("dashboard.php");
        } else {
            sql_error($stmt);
        }
    }
    
?>
