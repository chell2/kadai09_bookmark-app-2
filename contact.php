<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    $contact_method = $_POST['contact_method'];
    
    $csv_file = "inquiries.csv";
    $id = 1; // 初期値
    if (file_exists($csv_file)) {
        $id = count(file($csv_file)) + 1;
    }

    // 画像の処理
    $file_names = []; // 添付ファイル名を格納する配列
    if (isset($_FILES['image']) && $_FILES['image']['error'][0] == 0) {
        $upload_dir = 'upload/';
        
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                die("ディレクトリの作成に失敗しました。");
            }
        }

        foreach ($_FILES['image']['name'] as $key => $filename) {
            if ($_FILES['image']['error'][$key] == 0) {
                $target_file = $upload_dir . $id . "_" . basename($filename); 

                if (move_uploaded_file($_FILES['image']['tmp_name'][$key], $target_file)) {
                    $file_names[] = $target_file;
                }
            }
        }
    }

    // ファイル名をCSVに追加（ファイルがない場合は空）
    $file_names_string = !empty($file_names) ? implode(",", array_map('basename', $file_names)) : '';

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
    
    // CSVに保存
    $tags = assignTags($message);
    $tags_string = implode(",", $tags);
    $data = [$id, $name, $email, $message, $tags_string, $contact_method, $file_names_string, date('Y-m-d H:i:s')];
    
    $file = fopen($csv_file, "a");
    fputcsv($file, $data);
    fclose($file);

    // 自動返信メール
    $reply_message = "お問い合わせありがとうございます、$name 様。\n\n";
    $reply_message .= "お問い合わせ内容:\n$message\n\n";
    if ($contact_method == "zoom") {
        $reply_message .= "Zoomリンク: https://zoom.us/j/your_zoom_meeting_id";
    } else {
        $reply_message .= "メールでの対応となります。";
    }

    $headers = "From: test@example.com";
    mail($email, "お問い合わせありがとうございます", $reply_message, $headers);

    // リダイレクト
    header("Location: index.php?status=success");
    exit;
}
?>
