<?php
session_start();
include("funcs.php");
sschk();

//1.  DB接続
$id = $_GET["id"]; //?id~**を受け取る
$pdo = db_conn();

//２．データ登録SQL作成
$stmt = $pdo->prepare("SELECT * FROM tags WHERE id=:id");
$stmt->bindValue(":id", $id, PDO::PARAM_INT);
$status = $stmt->execute();

//３．データ表示
if($status==false) {
    sql_error($stmt);
}else{
    $row = $stmt->fetch();
}

// タグの一覧取得
$stmt = $pdo->query('SELECT * FROM tags');
$tags = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>トイアワセキロク</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
  </head>
  <body>
    <?php include("inc/menu.html"); ?>
    <section class="section">
      <div class="container">
        <div class="card">
          <header class="card-header">
            <p class="card-header-title">タグの編集</p>
            <div class="card-header-icon">
              <span class="icon">
                <i class="fas fa-solid fa-truck-pickup has-text-info"></i>
              </span>
            </div>
          </header>
          <div class="card-content">
            <div class="form-container">
              <form id="tag_nameForm" action="update_tags.php" method="POST">
                <div class="columns">
                  <div class="column">
                    <div class="field">
                      <label class="label" for="tag_name">ID: <?=$row["id"]?>
                      <div class="control has-icons-left">
                        <input class="input" type="text" id="tag_name" name="tag_name" value="<?=$row["tag_name"]?>" required>
                        <span class="icon is-small is-left has-text-info">
                          <i class="fas fa-tag"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="column">
                    <div class="control">
                      <br>
                      <div class="buttons">
                          <button type="submit" class="button is-primary">更新</button>
                          <input type="hidden" name="id" value="<?=$id?>">
                          <button type="button" class="button is-info is-outlined" onclick="window.location.href='tags.php'">キャンセル</button>
                      </div>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="section">
      <div class="chart-container block mt-5">
        <h1 class="title">タグ一覧</h1>
        <table class="table is-striped is-fullwidth">
          <thead>
            <tr>
              <th>ID</th>
              <th>タグ名</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($tags)): ?>
              <tr>
                  <td colspan="8">データがありません。</td>
              </tr>
            <?php else: ?>
            <?php foreach ($tags as $tag): ?>
              <tr>
                <td><?= $tag['id'] ?></td>
                <td><?= htmlspecialchars($tag['tag_name']) ?></td>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </body>
</html>