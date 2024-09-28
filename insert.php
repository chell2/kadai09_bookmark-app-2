<?php
//1. POSTデータ取得
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$contact_method = $_POST['contact_method'];

var_dump($name.$email.$message.$contact_method.$file_names); //OK

//2. DB接続
$dsn = 'mysql:dbname=ada02_contact_form;charset=utf8;host=localhost';
$id = 'root';
$password = ''; //Password:MAMP='root',XAMPP=''
try {
  $pdo = new PDO($dsn, $id, $password);
} catch (PDOException $e) {
  exit('DB_CONECT:'.$e->getMessage());
}

// タグの自動生成
function assignTags($message) {
    $tags = [];
    $message = trim($message);
    
    if (mb_strpos($message, "サポート") !== false) $tags[] = "サポート";
    if (mb_strpos($message, "価格") !== false || mb_strpos($message, "費用") !== false) $tags[] = "価格";
    if (mb_strpos($message, "バグ") !== false) $tags[] = "バグ";
    if (mb_strpos($message, "機能追加") !== false) $tags[] = "機能追加";
    if (mb_strpos($message, "要望") !== false || mb_strpos($message, "リクエスト") !== false) $tags[] = "要望";
    if (mb_strpos($message, "更新") !== false || mb_strpos($message, "アップデート") !== false) $tags[] = "アップデート";
    if (mb_strpos($message, "アカウント") !== false) $tags[] = "アカウント";
    return $tags;
}

// タグを生成しカンマ区切りに
$tags = assignTags($message);
$tags_string = implode(",", $tags);
var_dump($tags); //OK

//３．データ登録SQL作成（ファイル名は後で更新）
$sql = "INSERT INTO inquiries (name, email, message, tags, contact_method)
VALUES (:name, :email, :message, :tags, :contact_method);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':email', $email, PDO::PARAM_STR);
$stmt->bindValue(':message', $message, PDO::PARAM_STR);
$stmt->bindValue(':tags', $tags_string, PDO::PARAM_STR);
$stmt->bindValue(':contact_method', $contact_method, PDO::PARAM_STR);
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
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}else{
  //５．index.phpへリダイレクト
  header("Location: index.php");
  exit();

}

?>
