<?php
//1.  DB接続
include("funcs.php");
$pdo=db_conn();

// タグの削除
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tags WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: tags.php');
    exit();
}

// タグの追加
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tag_name'])) {
    $tagName = $_POST['tag_name'];
    $stmt = $pdo->prepare('INSERT INTO tags (name) VALUES (?)');
    $stmt->execute([$tagName]);
    header('Location: tags.php');
    exit();
}

// タグの一覧取得
$stmt = $pdo->query('SELECT * FROM tags');
$tags = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>タグ管理</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <h1>タグ管理</h1>

    <!-- タグの追加フォーム -->
    <form method="POST" action="insert_tags.php">
        <label for="tag_name">新しいタグ名:</label>
        <input type="text" name="tag_name" id="tag_name" required>
        <button type="submit">追加</button>
    </form>

    <!-- タグ一覧表示 -->
    <h2>タグ一覧</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>タグ名</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tags as $tag): ?>
                <tr>
                    <td><?= $tag['id'] ?></td>
                    <td><?= htmlspecialchars($tag['tag_name']) ?></td>
                    <td>
                        <a href="edit_tag.php?id=<?= $tag['id'] ?>">編集</a>
                        <a href="tags.php?delete=<?= $tag['id'] ?>" onclick="return confirm('本当に削除しますか？')">削除</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
