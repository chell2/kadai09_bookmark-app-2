<?php
//1.  DB接続
include("funcs.php");
$pdo=db_conn();

// タグの取得
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare('SELECT * FROM tags WHERE id = ?');
    $stmt->execute([$id]);
    $tag = $stmt->fetch();

    if (!$tag) {
        echo "タグが見つかりません。";
        exit();
    }
}

// タグの編集
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tag_name'])) {
    $newTagName = $_POST['tag_name'];
    $stmt = $pdo->prepare('UPDATE tags SET name = ? WHERE id = ?');
    $stmt->execute([$newTagName, $id]);
    header('Location: tags.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タグ編集</title>
</head>
<body>
    <h1>タグ編集</h1>

    <!-- タグ編集フォーム -->
    <form method="POST" action="">
        <label for="tag_name">タグ名:</label>
        <input type="text" name="tag_name" id="tag_name" value="<?= htmlspecialchars($tag['name']) ?>" required>
        <button type="submit">更新</button>
    </form>

    <a href="tags.php">戻る</a>
</body>
</html>
